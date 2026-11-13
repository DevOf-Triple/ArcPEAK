/* ============================================================
   ArcPEAK — Админ-панель
   ============================================================ */
const API = {
  stats: "../api/admin/stats.php",
  users: "../api/admin/users.php",
  setRole: "../api/admin/set_role.php",
  deleteUser: "../api/admin/delete_user.php",
  resetPassword: "../api/admin/reset_password.php",
  browseTable: "../api/admin/browse_table.php",
};

const state = {
  users: [],
  roleFilter: "",
  search: "",
  currentTable: "users",
  tablePage: 1,
  isRootAdmin: false,
};

const $ = (s, r = document) => r.querySelector(s);
const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

function escapeHtml(s) {
  return String(s ?? "").replace(
    /[&<>"']/g,
    (c) =>
      ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;",
      })[c],
  );
}

async function api(url, options = {}) {
  try {
    const res = await fetch(url, options);
    return await res.json();
  } catch (e) {
    return { ok: false, error: "Нет связи с сервером" };
  }
}

let toastTimer = null;
function toast(msg, isError = false) {
  const el = $("#adminToast");
  if (!el) return;
  el.textContent = msg;
  el.classList.toggle("is-error", !!isError);
  el.classList.add("is-visible");
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => el.classList.remove("is-visible"), 3200);
}

/* ============ STATS ============ */
async function loadStats() {
  const data = await api(API.stats);
  if (!data.ok) {
    toast(data.error || "Ошибка загрузки статистики", true);
    return;
  }
  const s = data.data;
  $("#statUsersTotal").textContent = s.users_total;
  $("#statUsersCurators").textContent = s.users_curators;
  $("#statUsersAdmins").textContent = s.users_admins;
  $("#statProjects").textContent = s.projects_total;
  $("#statCalculations").textContent = s.calculations_total;
  $("#statReceipts").textContent = s.receipts_total;
}

/* ============ USERS ============ */
async function loadUsers() {
  const params = new URLSearchParams();
  if (state.roleFilter) params.set("role", state.roleFilter);
  if (state.search) params.set("q", state.search);

  const data = await api(`${API.users}?${params}`);
  if (!data.ok) {
    toast(data.error || "Ошибка загрузки пользователей", true);
    return;
  }
  state.users = data.data.users;
  renderUsers();
}

function roleBadge(u) {
  if (u.is_root_admin === 1) {
    return `<span class="role-badge role-badge--root">ROOT ADMIN</span>`;
  }
  const map = { user: "Пользователь", curator: "Куратор", admin: "Админ" };
  return `<span class="role-badge role-badge--${u.role}">${map[u.role] || u.role}</span>`;
}

function renderUsers() {
  const wrap = $("#usersTableWrap");
  if (!state.users.length) {
    wrap.innerHTML = `<div class="admin-empty">Пользователей не найдено</div>`;
    return;
  }

  const rows = state.users
    .map((u) => {
      const isRoot = u.is_root_admin === 1;
      const canAdmin = state.isRootAdmin && !isRoot;

      let actions = "";
      if (!isRoot) {
        if (u.role === "curator") {
          actions += `<button class="admin-btn" data-act="role" data-id="${u.id}" data-role="user">Снять куратора</button>`;
        } else if (u.role === "user") {
          actions += `<button class="admin-btn admin-btn--accent" data-act="role" data-id="${u.id}" data-role="curator">Сделать куратором</button>`;
        }

        if (u.role === "admin") {
          if (canAdmin) {
            actions += `<button class="admin-btn" data-act="role" data-id="${u.id}" data-role="user">Снять админа</button>`;
          }
        } else if (canAdmin) {
          actions += `<button class="admin-btn admin-btn--accent" data-act="role" data-id="${u.id}" data-role="admin">Сделать админом</button>`;
        }

        actions += `<button class="admin-btn" data-act="resetpwd" data-id="${u.id}" data-login="${escapeHtml(u.login)}">Сбросить пароль</button>`;
        actions += `<button class="admin-btn admin-btn--danger" data-act="delete" data-id="${u.id}" data-login="${escapeHtml(u.login)}">Удалить</button>`;
      }

      return `
      <tr>
        <td class="mono">${u.id}</td>
        <td><strong>${escapeHtml(u.login)}</strong></td>
        <td>${escapeHtml(u.full_name)}</td>
        <td class="mono">${escapeHtml(u.email)}</td>
        <td>${roleBadge(u)}</td>
        <td class="mono">${u.created_at || "—"}</td>
        <td><div class="admin-actions">${actions || '<span style="color:#7f786d;font-size:12px;">—</span>'}</div></td>
      </tr>`;
    })
    .join("");

  wrap.innerHTML = `
    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th><th>Логин</th><th>ФИО</th><th>Email</th>
          <th>Роль</th><th>Создан</th><th>Действия</th>
        </tr>
      </thead>
      <tbody>${rows}</tbody>
    </table>`;
}

async function changeRole(userId, role) {
  const fd = new FormData();
  fd.append("user_id", userId);
  fd.append("role", role);
  const data = await api(API.setRole, { method: "POST", body: fd });
  if (!data.ok) {
    toast(data.error, true);
    return;
  }
  toast("Роль обновлена");
  await loadUsers();
  await loadStats();
}

async function deleteUser(userId, login) {
  if (
    !confirm(
      `Удалить пользователя «${login}»?\nВсё связанное (проекты, комментарии, чеки) будет удалено безвозвратно.`,
    )
  )
    return;
  const fd = new FormData();
  fd.append("user_id", userId);
  const data = await api(API.deleteUser, { method: "POST", body: fd });
  if (!data.ok) {
    toast(data.error, true);
    return;
  }
  toast("Пользователь удалён");
  await loadUsers();
  await loadStats();
}

async function resetPassword(userId) {
  if (!confirm("Сбросить пароль и сгенерировать новый?")) return;
  const fd = new FormData();
  fd.append("user_id", userId);
  const data = await api(API.resetPassword, { method: "POST", body: fd });
  if (!data.ok) {
    toast(data.error, true);
    return;
  }

  $("#pwdLogin").textContent = data.data.login;
  $("#pwdValue").textContent = data.data.new_password;
  $("#adminModal").hidden = false;
}

/* ============ TABLES ============ */
async function loadTable(table, page = 1) {
  state.currentTable = table;
  state.tablePage = page;

  const data = await api(`${API.browseTable}?table=${table}&page=${page}`);
  if (!data.ok) {
    toast(data.error || "Ошибка загрузки таблицы", true);
    return;
  }

  const { columns, rows, total, per_page } = data.data;
  const wrap = $("#tableContent");

  if (!rows.length) {
    wrap.innerHTML = `<div class="admin-empty">Таблица пуста</div>`;
  } else {
    const cols = columns.map((c) => `<th>${escapeHtml(c)}</th>`).join("");
    const body = rows
      .map((r) => {
        const tds = columns
          .map((c) => {
            let v = r[c];
            if (v === null) v = '<span style="color:#b0a89c;">NULL</span>';
            else if (typeof v === "object") v = escapeHtml(JSON.stringify(v));
            else v = escapeHtml(String(v));
            return `<td>${v}</td>`;
          })
          .join("");
        return `<tr>${tds}</tr>`;
      })
      .join("");
    wrap.innerHTML = `<div class="admin-table-wrap"><table class="admin-table"><thead><tr>${cols}</tr></thead><tbody>${body}</tbody></table></div>`;
  }

  const totalPages = Math.max(1, Math.ceil(total / per_page));
  $("#tablePagination").innerHTML =
    totalPages > 1
      ? `<div class="admin-pagination">
         <button class="admin-btn" data-page="${page - 1}" ${page <= 1 ? "disabled" : ""}>← Назад</button>
         <span class="admin-pagination__info">${page} / ${totalPages} · всего ${total}</span>
         <button class="admin-btn" data-page="${page + 1}" ${page >= totalPages ? "disabled" : ""}>Вперёд →</button>
       </div>`
      : `<div class="admin-pagination"><span class="admin-pagination__info">всего ${total} строк</span></div>`;

  $$(".admin-tables-nav button").forEach((b) => {
    b.classList.toggle("is-active", b.dataset.table === table);
  });
}

/* ============ INIT ============ */
document.addEventListener("DOMContentLoaded", () => {
  state.isRootAdmin = document.body.dataset.isRootAdmin === "1";

  loadStats();
  loadUsers();

  // фильтры
  $("#roleFilter")?.addEventListener("change", (e) => {
    state.roleFilter = e.target.value;
    loadUsers();
  });
  let searchTimer = null;
  $("#userSearch")?.addEventListener("input", (e) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
      state.search = e.target.value.trim();
      loadUsers();
    }, 250);
  });

  // табы
  $$(".admin-tab").forEach((tab) => {
    tab.addEventListener("click", () => {
      $$(".admin-tab").forEach((t) =>
        t.classList.toggle("is-active", t === tab),
      );
      $$(".admin-panel").forEach((p) =>
        p.classList.toggle("is-active", p.id === `panel-${tab.dataset.tab}`),
      );
      if (
        tab.dataset.tab === "tables" &&
        !$("#tableContent").innerHTML.trim()
      ) {
        loadTable("users");
      }
    });
  });

  // таблицы
  document.addEventListener("click", (e) => {
    const tbtn = e.target.closest(".admin-tables-nav button");
    if (tbtn) {
      loadTable(tbtn.dataset.table, 1);
      return;
    }

    const pageBtn = e.target.closest("[data-page]");
    if (pageBtn && !pageBtn.disabled) {
      loadTable(state.currentTable, +pageBtn.dataset.page);
      return;
    }

    const act = e.target.closest("[data-act]");
    if (act) {
      const id = +act.dataset.id;
      const kind = act.dataset.act;
      if (kind === "role") changeRole(id, act.dataset.role);
      if (kind === "delete") deleteUser(id, act.dataset.login);
      if (kind === "resetpwd") resetPassword(id);
    }
  });

  // модалка
  $("#pwdClose")?.addEventListener("click", () => {
    $("#adminModal").hidden = true;
  });
  $("#pwdOk")?.addEventListener("click", () => {
    $("#adminModal").hidden = true;
  });
  $("#adminModal .admin-modal__backdrop")?.addEventListener("click", () => {
    $("#adminModal").hidden = true;
  });
});
