/* ============================================================
   ArcPEAK — каталог + портфолио (фронт)
   ============================================================ */

const API = {
  filterBuildings: "../api/filter_buildings.php",
  listSheets: "../api/list_sheets.php",
  listProjects: "../api/list_projects.php",
  getProject: "../api/get_project.php",
  createProject: "../api/create_project.php",
  renameProject: "../api/rename_project.php",
  deleteProject: "../api/delete_project.php",
  addItem: "../api/add_item.php",
  removeItem: "../api/remove_item.php",
  setActive: "../api/set_active_project.php",
};

const state = {
  projects: [],
  activeProjectId: null,
  projectItems: new Set(), // "building:12", "sheet:7"
  buildings: [],
  sheets: [],
  pendingItem: null, // { type, id } — что добавить после создания проекта
  filterTimer: null,
};

/* ============================================================
   УТИЛИТЫ
   ============================================================ */

const $ = (sel, root = document) => root.querySelector(sel);
const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

const fmtPrice = (n) =>
  new Intl.NumberFormat("ru-RU", { maximumFractionDigits: 0 }).format(
    Math.round(n),
  ) + " ₽";

const itemKey = (type, id) => `${type}:${id}`;

async function api(url, options = {}) {
  try {
    const res = await fetch(url, options);
    const data = await res.json();
    return data;
  } catch (err) {
    console.error("API error:", url, err);
    return { ok: false, error: "Нет связи с сервером" };
  }
}

/* ============================================================
   ТОСТЫ
   ============================================================ */

let toastTimer = null;
function showToast(text, isError = false) {
  const el = $("#toast");
  if (!el) return;
  el.textContent = text;
  el.classList.toggle("is-error", !!isError);
  el.classList.add("is-visible");
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => el.classList.remove("is-visible"), 3200);
}

/* ============================================================
   ЗАГРУЗКА ДАННЫХ
   ============================================================ */

async function loadProjects() {
  const data = await api(API.listProjects);
  if (!data.ok) {
    showToast(data.error || "Не удалось загрузить проекты", true);
    return;
  }
  state.projects = data.data.projects || [];
  state.activeProjectId = data.data.active_project_id || null;
}

async function loadProjectItems(projectId) {
  if (!projectId) {
    state.projectItems.clear();
    return;
  }
  const data = await api(`${API.getProject}?id=${projectId}`);
  if (!data.ok) return;
  state.projectItems.clear();
  for (const item of data.data.items) {
    state.projectItems.add(itemKey(item.type, item.id));
  }
}

async function loadBuildings() {
  const qs = buildFilterQuery();
  const data = await api(`${API.filterBuildings}?${qs}`);
  if (!data.ok) {
    showToast(data.error || "Не удалось загрузить здания", true);
    return;
  }
  state.buildings = data.data.buildings || [];
}

async function loadSheets() {
  const data = await api(API.listSheets);
  if (!data.ok) {
    showToast(data.error || "Не удалось загрузить справочник", true);
    return;
  }
  state.sheets = data.data.tags || [];
}

/* ============================================================
   ФИЛЬТРЫ
   ============================================================ */

function readFiltersFromURL() {
  const p = new URLSearchParams(location.search);
  const set = (id, v) => {
    const el = document.getElementById(id);
    if (el) el.value = v || "";
  };

  set("fStyle", p.get("style"));
  set("fArea", p.get("area"));
  set("fFloors", p.get("floors"));
  set("fBedrooms", p.get("bedrooms"));
  set("fBathrooms", p.get("bathrooms"));
  set("fGarage", p.get("garage"));
  set("fMaterial", p.get("material"));
  set("fRoof", p.get("roof"));
  set("fPriceMin", p.get("price_min"));
  set("fPriceMax", p.get("price_max"));
  set("fSort", p.get("sort") || "newest");

  const features = (p.get("features") || "").split(",").filter(Boolean);
  $$('#fFeatures input[type="checkbox"]').forEach((cb) => {
    cb.checked = features.includes(cb.value);
  });
}

function collectFilters() {
  const features = $$('#fFeatures input[type="checkbox"]:checked').map(
    (cb) => cb.value,
  );
  return {
    style: $("#fStyle")?.value || "",
    area: $("#fArea")?.value || "",
    floors: $("#fFloors")?.value || "",
    bedrooms: $("#fBedrooms")?.value || "",
    bathrooms: $("#fBathrooms")?.value || "",
    garage: $("#fGarage")?.value || "",
    material: $("#fMaterial")?.value || "",
    roof: $("#fRoof")?.value || "",
    price_min: $("#fPriceMin")?.value.trim() || "",
    price_max: $("#fPriceMax")?.value.trim() || "",
    sort: $("#fSort")?.value || "newest",
    features: features.join(","),
  };
}

function buildFilterQuery() {
  const f = collectFilters();
  const p = new URLSearchParams();
  Object.entries(f).forEach(([k, v]) => {
    if (v) p.set(k, v);
  });
  return p.toString();
}

function syncFiltersToURL() {
  const qs = buildFilterQuery();
  const url = qs ? `${location.pathname}?${qs}` : location.pathname;
  history.replaceState(null, "", url);
}

function onFiltersChanged() {
  syncFiltersToURL();
  clearTimeout(state.filterTimer);
  state.filterTimer = setTimeout(async () => {
    await loadBuildings();
    renderBuildings();
  }, 200);
}

function resetFilters() {
  [
    "fStyle",
    "fArea",
    "fFloors",
    "fBedrooms",
    "fBathrooms",
    "fGarage",
    "fMaterial",
    "fRoof",
    "fPriceMin",
    "fPriceMax",
  ].forEach((id) => {
    const el = document.getElementById(id);
    if (el) el.value = "";
  });
  const sort = document.getElementById("fSort");
  if (sort) sort.value = "newest";
  $$('#fFeatures input[type="checkbox"]').forEach((cb) => {
    cb.checked = false;
  });
  onFiltersChanged();
}

/* ============================================================
   РЕНДЕР
   ============================================================ */

function stampSVG() {
  return `
    <svg viewBox="0 0 76 76" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
      <defs>
        <path id="stampCircle" d="M 38,38 m -26,0 a 26,26 0 1,1 52,0 a 26,26 0 1,1 -52,0"/>
      </defs>
      <circle cx="38" cy="38" r="35" fill="rgba(46,204,113,0.1)" stroke="#2ecc71" stroke-width="2"/>
      <circle cx="38" cy="38" r="26" fill="none" stroke="#2ecc71" stroke-width="0.8" opacity="0.4"/>
      <text font-family="Onest, sans-serif" font-size="7.5" font-weight="700" fill="#2ecc71" letter-spacing="1.4">
        <textPath href="#stampCircle" startOffset="0">ДОБАВЛЕНО · ДОБАВЛЕНО ·</textPath>
      </text>
      <text x="38" y="43" text-anchor="middle" font-family="Onest, sans-serif" font-size="18" font-weight="700" fill="#2ecc71">✓</text>
    </svg>`;
}

function buildingCardHTML(b) {
  const coverInner = b.image_path
    ? `<img src="../${b.image_path}" alt="${escapeHtml(b.name)}" loading="lazy">`
    : "";
  const floorsLabel =
    b.floors === 1 ? "этаж" : b.floors < 5 ? "этажа" : "этажей";
  return `
    <article class="b-card" data-type="building" data-id="${b.id}">
      <div class="b-card__stamp">${stampSVG()}</div>
      <button type="button" class="b-card__remove" data-remove aria-label="Убрать из проекта">×</button>
      <div class="b-card__cover">
        ${coverInner}
        <span class="b-card__price">${fmtPrice(b.base_price)}</span>
      </div>
      <div class="b-card__body">
        <h3 class="b-card__title">${escapeHtml(b.name)}</h3>
        <p class="b-card__meta">${escapeHtml(b.style || "")}</p>
        <div class="b-card__specs">
          <span><b>${b.area_m2}</b> м²</span>
          <span><b>${b.floors}</b> ${floorsLabel}</span>
          <span><b>${b.bedrooms}</b> спал.</span>
          <span><b>${b.bathrooms}</b> с/у</span>
        </div>
      </div>
    </article>`;
}

function sheetCardHTML(s) {
  const modifierLabel = sheetModifierLabel(s);
  return `
    <article class="sheet-card" data-type="sheet" data-id="${s.id}">
      <button type="button" class="sheet-card__remove" data-remove aria-label="Убрать из проекта">×</button>
      <h4 class="sheet-card__title">${escapeHtml(s.name)}</h4>
      <p class="sheet-card__desc">${escapeHtml(s.description || "")}</p>
      ${modifierLabel ? `<span class="sheet-card__modifier">${modifierLabel}</span>` : ""}
    </article>`;
}

function sheetModifierLabel(s) {
  const v = Number(s.modifier_value) || 0;
  switch (s.modifier_type) {
    case "percent_add":
      return `+${v}%`;
    case "percent_discount":
      return `−${v}%`;
    case "per_m2_add":
      return `+${v.toLocaleString("ru-RU")} ₽/м²`;
    case "flat_add":
      return `+${v.toLocaleString("ru-RU")} ₽`;
    case "multiplier":
      return `× ${v}`;
    default:
      return "";
  }
}

function renderBuildings() {
  const grid = $("#buildingsGrid");
  const empty = $("#buildingsEmpty");
  if (!grid) return;

  if (state.buildings.length === 0) {
    grid.innerHTML = "";
    empty?.removeAttribute("hidden");
    return;
  }
  empty?.setAttribute("hidden", "");
  grid.innerHTML = state.buildings.map(buildingCardHTML).join("");
  applyAddedStates();
}

function renderSheets() {
  const wrap = $("#tagsWrap");
  if (!wrap) return;

  if (!state.sheets.length) {
    wrap.innerHTML = "";
    return;
  }

  wrap.innerHTML = state.sheets
    .map(
      (tag) => `
    <section class="tag-block">
      <header class="tag-block__head">
        <h3 class="tag-block__title">${escapeHtml(tag.name)}</h3>
        <span class="tag-block__count">${tag.sheets.length} листов</span>
      </header>
      <div class="sheets-grid">
        ${tag.sheets.map(sheetCardHTML).join("")}
      </div>
    </section>`,
    )
    .join("");

  applyAddedStates();
}

function renderFolderMeta() {
  const meta = $("#folderMeta");
  if (!meta) return;

  if (!state.activeProjectId) {
    meta.textContent = "Нет проектов";
    return;
  }
  const p = state.projects.find((x) => x.id === state.activeProjectId);
  if (!p) {
    meta.textContent = "Нет проектов";
    return;
  }
  meta.textContent = `Проект №${p.project_number} — ${p.title} — ${state.projectItems.size} лист.`;
}

function applyAddedStates() {
  $$(".b-card, .sheet-card").forEach((card) => {
    const type = card.dataset.type;
    const id = +card.dataset.id;
    card.classList.toggle(
      "is-added",
      state.projectItems.has(itemKey(type, id)),
    );
  });
}

function updateCardState(type, id, added) {
  const sel = type === "building" ? ".b-card" : ".sheet-card";
  const card = $(`${sel}[data-type="${type}"][data-id="${id}"]`);
  if (!card) return;
  card.classList.toggle("is-added", added);
}

/* ============================================================
   ЭКШЕНЫ — добавить / удалить
   ============================================================ */

async function handleAdd(type, id) {
  if (!state.activeProjectId) {
    state.pendingItem = { type, id };
    openCreateModal(true);
    return;
  }
  if (state.projectItems.has(itemKey(type, id))) return;

  const fd = new FormData();
  fd.append("project_id", state.activeProjectId);
  fd.append("item_type", type);
  fd.append("item_id", id);

  const data = await api(API.addItem, { method: "POST", body: fd });
  if (!data.ok) {
    showToast(data.error || "Не удалось добавить", true);
    return;
  }
  if (!data.data.added) {
    showToast("Уже в проекте", true);
    return;
  }

  state.projectItems.add(itemKey(type, id));
  updateCardState(type, id, true);
  bumpProjectItemsCount(+1);
  renderFolderMeta();
  showToast("Добавлено в проект");
}

async function handleRemove(type, id) {
  if (!state.activeProjectId) return;
  if (!state.projectItems.has(itemKey(type, id))) return;

  const fd = new FormData();
  fd.append("project_id", state.activeProjectId);
  fd.append("item_type", type);
  fd.append("item_id", id);

  const data = await api(API.removeItem, { method: "POST", body: fd });
  if (!data.ok) {
    showToast(data.error || "Не удалось убрать", true);
    return;
  }

  state.projectItems.delete(itemKey(type, id));
  updateCardState(type, id, false);
  bumpProjectItemsCount(-1);
  renderFolderMeta();
  showToast("Убрано из проекта");
}

function bumpProjectItemsCount(delta) {
  const p = state.projects.find((x) => x.id === state.activeProjectId);
  if (p) p.items_count = Math.max(0, (p.items_count || 0) + delta);
}

/* ============================================================
   ПРОЕКТЫ — создание, активация
   ============================================================ */

async function createNewProject(title) {
  const fd = new FormData();
  fd.append("title", title || "Проект");

  const data = await api(API.createProject, { method: "POST", body: fd });
  if (!data.ok) {
    showToast(data.error || "Не удалось создать проект", true);
    return null;
  }

  state.projects.push({
    id: data.data.id,
    project_number: data.data.project_number,
    title: data.data.title,
    items_count: 0,
  });
  state.activeProjectId = data.data.id;
  state.projectItems.clear();
  renderFolderMeta();
  showToast(`Проект №${data.data.project_number} создан`);

  if (state.pendingItem) {
    const { type, id } = state.pendingItem;
    state.pendingItem = null;
    await handleAdd(type, id);
  }
  return data.data.id;
}

async function setActiveProject(projectId) {
  if (!projectId || projectId === state.activeProjectId) return;
  const fd = new FormData();
  fd.append("project_id", projectId);
  const data = await api(API.setActive, { method: "POST", body: fd });
  if (!data.ok) return;

  state.activeProjectId = projectId;
  await loadProjectItems(projectId);
  applyAddedStates();
  renderFolderMeta();
}

/* ============================================================
   МОДАЛКА СОЗДАНИЯ
   ============================================================ */

function openCreateModal(fromPending = false) {
  const modal = $("#createProjectModal");
  const input = $("#newProjectTitle");
  if (!modal) return;
  if (input && !fromPending) input.value = "Проект";
  modal.removeAttribute("hidden");
  setTimeout(() => input?.focus(), 50);
}

function closeCreateModal() {
  const modal = $("#createProjectModal");
  modal?.setAttribute("hidden", "");
}

async function submitCreateModal() {
  const input = $("#newProjectTitle");
  const title = input?.value.trim() || "Проект";
  const btn = $("#createProjectSubmit");
  if (btn) btn.disabled = true;
  await createNewProject(title);
  if (btn) btn.disabled = false;
  closeCreateModal();
}

/* ============================================================
   СОБЫТИЯ
   ============================================================ */

function bindEvents() {
  /* Фильтры */
  [
    "fStyle",
    "fArea",
    "fFloors",
    "fBedrooms",
    "fBathrooms",
    "fGarage",
    "fMaterial",
    "fRoof",
    "fPriceMin",
    "fPriceMax",
    "fSort",
  ].forEach((id) => {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener("input", onFiltersChanged);
    el.addEventListener("change", onFiltersChanged);
  });
  $$('#fFeatures input[type="checkbox"]').forEach((cb) =>
    cb.addEventListener("change", onFiltersChanged),
  );

  $("#resetFilters")?.addEventListener("click", resetFilters);
  $("#emptyReset")?.addEventListener("click", resetFilters);

  /* Табы */
  $$(".catalog-tab").forEach((tab) => {
    tab.addEventListener("click", () => {
      $$(".catalog-tab").forEach((t) =>
        t.classList.toggle("is-active", t === tab),
      );
      $$(".catalog-panel").forEach((p) => {
        p.classList.toggle("is-active", p.id === `panel-${tab.dataset.tab}`);
      });
    });
  });

  /* Клик по карточкам (делегирование) */
  document.addEventListener("click", (e) => {
    const removeBtn = e.target.closest("[data-remove]");
    if (removeBtn) {
      e.stopPropagation();
      const card = removeBtn.closest("[data-type][data-id]");
      if (!card) return;
      handleRemove(card.dataset.type, +card.dataset.id);
      return;
    }

    const card = e.target.closest(".b-card, .sheet-card");
    if (card) {
      handleAdd(card.dataset.type, +card.dataset.id);
    }
  });

  /* Модалка создания */
  $("#createProjectSubmit")?.addEventListener("click", submitCreateModal);
  $("#newProjectTitle")?.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      submitCreateModal();
    }
  });
  $$("[data-close-modal]").forEach((el) =>
    el.addEventListener("click", closeCreateModal),
  );

  /* Стопка папок — клик по кнопке «+» и по «front» */
  $("#folderAddBtn")?.addEventListener("click", (e) => {
    e.stopPropagation();
    openCreateModal();
  });
  $("#folderFront")?.addEventListener("click", () => {
    // TODO шаг 6: здесь будет открытие полноэкранного оверлея
    if (!state.activeProjectId) {
      openCreateModal();
      return;
    }
    const p = state.projects.find((x) => x.id === state.activeProjectId);
    showToast(
      `Проект №${p?.project_number || "?"} — ${state.projectItems.size} листов`,
    );
  });

  /* Esc — закрыть модалку */
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      const modal = $("#createProjectModal");
      if (modal && !modal.hasAttribute("hidden")) closeCreateModal();
    }
  });
}

/* ============================================================
   ИНИЦИАЛИЗАЦИЯ
   ============================================================ */

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

async function init() {
  readFiltersFromURL();
  bindEvents();

  await loadProjects();

  if (state.activeProjectId) {
    await loadProjectItems(state.activeProjectId);
  }

  await Promise.all([loadBuildings(), loadSheets()]);

  renderBuildings();
  renderSheets();
  renderFolderMeta();

  // если у пользователя нет ни одного проекта — показать модалку
  if (state.projects.length === 0) {
    setTimeout(() => openCreateModal(), 400);
  }
}

document.addEventListener("DOMContentLoaded", init);
