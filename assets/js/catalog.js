/* ============================================================
   ArcPEAK — каталог + портфолио
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
  regionsList: "../api/regions_list.php",
  setRegion: "../api/set_project_region.php",
  addCustomSheet: "../api/add_custom_sheet.php",
  deleteCustomSheet: "../api/delete_custom_sheet.php",
};

const state = {
  projects: [],
  activeProjectId: null,
  projectItems: new Set(),
  buildings: [],
  sheets: [],
  regions: [],
  pendingItem: null,
  filterTimer: null,
};

const portfolio = {
  overlay: null,
  panel: null,
  project: null,
  items: [],
  params: null,
  price: null,
  sort: "added_at",
  page: 0,
  pageSize: 12,
};

/* ============ УТИЛИТЫ ============ */

const $ = (s, r = document) => r.querySelector(s);
const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

const fmtPrice = (n) =>
  new Intl.NumberFormat("ru-RU", { maximumFractionDigits: 0 }).format(
    Math.round(n),
  ) + " ₽";

const itemKey = (t, i) => `${t}:${i}`;

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
  } catch (err) {
    console.error("API error:", url, err);
    return { ok: false, error: "Нет связи с сервером" };
  }
}

/* ============ ТОСТ ============ */

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

/* ============ ЗАГРУЗКА ============ */

async function loadProjects() {
  const data = await api(API.listProjects);
  if (!data.ok)
    return showToast(data.error || "Ошибка загрузки проектов", true);
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
  if (!data.ok) return showToast(data.error || "Ошибка загрузки зданий", true);
  state.buildings = data.data.buildings || [];
}

async function loadSheets() {
  const data = await api(API.listSheets);
  if (!data.ok)
    return showToast(data.error || "Ошибка загрузки справочника", true);
  state.sheets = data.data.tags || [];
}

async function loadRegions() {
  if (state.regions.length) return;
  const data = await api(API.regionsList);
  if (!data.ok) return;
  state.regions = data.data.regions || [];
}

/* ============ ФИЛЬТРЫ ============ */

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

/* ============ РЕНДЕР КАТАЛОГА ============ */

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
      <button type="button" class="b-card__remove" data-remove aria-label="Убрать">×</button>
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
  const cls = s.is_custom ? "sheet-card sheet-card--custom" : "sheet-card";
  return `
    <article class="${cls}" data-type="sheet" data-id="${s.id}">
      <button type="button" class="sheet-card__remove" data-remove aria-label="Убрать">×</button>
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
    case "base_area":
      return `${v} м²`;
    case "base_type_factor":
      return `×${v}`;
    case "base_floors":
      return `${v} эт.`;
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

  // Ключевые теги уже идут первыми по sort_order
  wrap.innerHTML = state.sheets
    .map((tag) => {
      const isKey = tag.is_key === 1;
      const blockCls = isKey ? "tag-block tag-block--key" : "tag-block";
      const keyBadge = isKey
        ? '<span class="tag-block__key-badge">Обязательно</span>'
        : "";
      return `
      <section class="${blockCls}" data-tag-id="${tag.id}" data-tag-slug="${tag.slug}" data-tag-name="${escapeHtml(tag.name)}" data-radio="${tag.is_radio}">
        <header class="tag-block__head">
          <h3 class="tag-block__title">${escapeHtml(tag.name)}${keyBadge}</h3>
          <span class="tag-block__count">${tag.sheets.length} вариантов</span>
          <button type="button" class="tag-block__add-custom" data-add-custom="${tag.id}">+ Свой вариант</button>
        </header>
        <div class="sheets-grid">
          ${tag.sheets.map(sheetCardHTML).join("")}
        </div>
      </section>`;
    })
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
  const n = state.projectItems.size;
  const kindLabel = p.project_kind === "custom" ? "свой" : "готовый";
  meta.textContent = `Проект №${p.project_number} (${kindLabel}) — ${p.title} — ${n} лист.`;
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

/* ============ ПАПКА: летящий лист, бумажки, пульс ============ */

function flyToFolder(fromRect) {
  return new Promise((resolve) => {
    const folder = document.getElementById("folderFront");
    if (!folder) {
      resolve();
      return;
    }
    const toRect = folder.getBoundingClientRect();
    const fly = document.createElement("div");
    fly.className = "fly-sheet";
    fly.style.left = fromRect.left + "px";
    fly.style.top = fromRect.top + "px";
    fly.style.width = Math.min(fromRect.width, 120) + "px";
    fly.style.height = Math.min(fromRect.height, 90) + "px";
    document.body.appendChild(fly);
    const fromCX = fromRect.left + fromRect.width / 2;
    const fromCY = fromRect.top + fromRect.height / 2;
    const toCX = toRect.left + toRect.width / 2;
    const toCY = toRect.top + toRect.height / 2;
    const dx = toCX - fromCX,
      dy = toCY - fromCY;
    const anim = fly.animate(
      [
        {
          transform: "translate(0, 0) rotate(0) scale(1)",
          opacity: 1,
          offset: 0,
        },
        {
          transform: `translate(${dx * 0.5}px, ${dy * 0.35 - 100}px) rotate(-10deg) scale(0.65)`,
          opacity: 0.95,
          offset: 0.5,
        },
        {
          transform: `translate(${dx}px, ${dy}px) rotate(-22deg) scale(0.12)`,
          opacity: 0,
          offset: 1,
        },
      ],
      { duration: 850, easing: "cubic-bezier(.3,0,.4,1)", fill: "forwards" },
    );
    anim.onfinish = () => {
      fly.remove();
      resolve();
    };
  });
}

function pulseFolder() {
  const folder = document.getElementById("folderFront");
  if (!folder) return;
  folder.animate(
    [
      { transform: "translateY(-10px) scale(1)" },
      { transform: "translateY(-14px) scale(1.06)" },
      { transform: "translateY(-10px) scale(1)" },
    ],
    { duration: 420, easing: "cubic-bezier(.22,1,.36,1)" },
  );
}

function renderFolderPapers() {
  const wrap = document.getElementById("folderPapers");
  if (!wrap) return;
  const count = state.projectItems.size;
  const paperCount = count === 0 ? 0 : Math.min(3, Math.ceil(count / 3));
  wrap.innerHTML = "";
  for (let i = 0; i < paperCount; i++) {
    const p = document.createElement("div");
    p.className = "folder-stack__paper";
    p.style.setProperty("--i", i);
    wrap.appendChild(p);
  }
}

/* ============ ДОБАВЛЕНИЕ / УДАЛЕНИЕ ============ */

async function handleAdd(type, id, opts = {}) {
  if (!state.activeProjectId) {
    state.pendingItem = { type, id };
    openCreateModal(true);
    return;
  }

  const project = state.projects.find((p) => p.id === state.activeProjectId);
  if (project) {
    if (project.project_kind === "ready" && type === "sheet") {
      showToast("В готовый проект нельзя добавлять листы", true);
      return;
    }
    if (project.project_kind === "custom" && type === "building") {
      showToast("В свой проект нельзя добавлять готовые здания", true);
      return;
    }
  }

  if (state.projectItems.has(itemKey(type, id))) return;

  const selector = type === "building" ? ".b-card" : ".sheet-card";
  const cardEl = document.querySelector(
    `${selector}[data-type="${type}"][data-id="${id}"]`,
  );
  const fromRect = cardEl ? cardEl.getBoundingClientRect() : null;

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

  // После добавления в радио-группе старый лист мог быть удалён сервером
  await loadProjectItems(state.activeProjectId);
  applyAddedStates();
  bumpProjectItemsCount(+1);
  renderFolderMeta();
  renderFolderPapers();

  if (fromRect) flyToFolder(fromRect).then(pulseFolder);
  else pulseFolder();

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
  renderFolderPapers();
  showToast("Убрано из проекта");
}

function bumpProjectItemsCount(delta) {
  const p = state.projects.find((x) => x.id === state.activeProjectId);
  if (p) p.items_count = Math.max(0, (p.items_count || 0) + delta);
}

/* ============ МОДАЛКА: создание проекта ============ */

function getSelectedKind() {
  const checked = document.querySelector('input[name="project_kind"]:checked');
  return checked ? checked.value : "ready";
}

function openCreateModal(fromPending = false) {
  const modal = $("#createProjectModal");
  const input = $("#newProjectTitle");
  if (!modal) return;
  if (input && !fromPending) input.value = "Проект";
  const ready = document.querySelector(
    'input[name="project_kind"][value="ready"]',
  );
  if (ready) ready.checked = true;
  modal.removeAttribute("hidden");
  setTimeout(() => input?.focus(), 50);
}

function closeCreateModal() {
  $("#createProjectModal")?.setAttribute("hidden", "");
}

async function submitCreateModal() {
  const input = $("#newProjectTitle");
  const title = input?.value.trim() || "Проект";
  const kind = getSelectedKind();
  const btn = $("#createProjectSubmit");
  if (btn) btn.disabled = true;
  await createNewProject(title, kind);
  if (btn) btn.disabled = false;
  closeCreateModal();
}

async function createNewProject(title, kind) {
  const fd = new FormData();
  fd.append("title", title || "Проект");
  fd.append("project_kind", kind || "ready");

  const data = await api(API.createProject, { method: "POST", body: fd });
  if (!data.ok) {
    showToast(data.error || "Не удалось создать", true);
    return null;
  }

  state.projects.push({
    id: data.data.id,
    project_number: data.data.project_number,
    title: data.data.title,
    project_kind: data.data.project_kind,
    items_count: 0,
  });
  state.activeProjectId = data.data.id;
  state.projectItems.clear();
  renderFolderMeta();
  renderFolderPapers();
  showToast(`Проект №${data.data.project_number} создан`);

  if (state.pendingItem) {
    const { type, id } = state.pendingItem;
    state.pendingItem = null;
    await handleAdd(type, id);
  }
  return data.data.id;
}

/* ============ МОДАЛКА: свой вариант листа ============ */

let customSheetTag = null;

function openCustomSheetModal(tagId, tagName, isKey, slug) {
  customSheetTag = { tagId, tagName, isKey, slug };
  const modal = $("#customSheetModal");
  const title = $("#customSheetTitle");
  const lead = $("#customSheetLead");
  const nameInput = $("#customSheetName");
  const valueInput = $("#customSheetValue");
  const valueLabel = $("#customSheetValueLabel");
  const hint = $("#customSheetHint");
  if (!modal) return;

  title.textContent = `Свой вариант — ${tagName}`;
  lead.textContent = `Добавьте собственное значение в категорию «${tagName}». Оно будет видно только вам.`;
  nameInput.value = "";
  valueInput.value = "0";

  if (slug === "type") {
    valueLabel.textContent = "Коэффициент (например, 1.2)";
    hint.textContent = "Множитель к базовой цене. 1.0 — обычный дом.";
    valueInput.step = "0.01";
  } else if (slug === "area") {
    valueLabel.textContent = "Площадь, м²";
    hint.textContent = "Например, 130";
    valueInput.step = "1";
  } else if (slug === "floors") {
    valueLabel.textContent = "Количество этажей";
    hint.textContent = "Например, 2";
    valueInput.step = "1";
  } else if (isKey) {
    valueLabel.textContent = "Значение";
    hint.textContent = "";
  } else {
    valueLabel.textContent = "Значение (₽)";
    hint.textContent = "Прибавка к базовой цене в рублях.";
    valueInput.step = "0.01";
  }

  modal.removeAttribute("hidden");
  setTimeout(() => nameInput?.focus(), 50);
}

function closeCustomSheetModal() {
  $("#customSheetModal")?.setAttribute("hidden", "");
  customSheetTag = null;
}

async function submitCustomSheet() {
  if (!customSheetTag || !state.activeProjectId) return;

  const name = $("#customSheetName").value.trim();
  const value = parseFloat($("#customSheetValue").value) || 0;
  if (!name) {
    showToast("Введите название", true);
    return;
  }

  const fd = new FormData();
  fd.append("project_id", state.activeProjectId);
  fd.append("tag_id", customSheetTag.tagId);
  fd.append("name", name);
  fd.append("value", value);

  const data = await api(API.addCustomSheet, { method: "POST", body: fd });
  if (!data.ok) {
    showToast(data.error || "Не удалось создать", true);
    return;
  }

  await loadProjectItems(state.activeProjectId);
  applyAddedStates();
  renderFolderMeta();
  renderFolderPapers();
  pulseFolder();
  closeCustomSheetModal();
  showToast("Свой вариант создан");

  if (portfolio.overlay && !portfolio.overlay.hidden) {
    await loadPortfolioProject(state.activeProjectId);
  }
}

/* ============ FLIP ============ */

function ensurePortfolioElements() {
  if (portfolio.overlay) return true;
  portfolio.overlay = $("#portfolioOverlay");
  portfolio.panel = $("#portfolioPanel");
  return !!portfolio.overlay;
}

function canFlip() {
  return (
    window.matchMedia("(min-width: 601px)").matches &&
    !window.matchMedia("(prefers-reduced-motion: reduce)").matches
  );
}

function flipOpen() {
  if (!ensurePortfolioElements()) return;
  const front = $("#folderFront");
  if (!front || !canFlip()) {
    portfolio.overlay.hidden = false;
    return;
  }
  const from = front.getBoundingClientRect();
  portfolio.overlay.hidden = false;
  const to = portfolio.panel.getBoundingClientRect();
  const dx = from.left - to.left,
    dy = from.top - to.top;
  const sx = from.width / to.width,
    sy = from.height / to.height;
  portfolio.panel.animate(
    [
      {
        transformOrigin: "top left",
        transform: `translate(${dx}px,${dy}px) scale(${sx},${sy})`,
        opacity: 0.4,
      },
      {
        transformOrigin: "top left",
        transform: "translate(0,0) scale(1,1)",
        opacity: 1,
      },
    ],
    { duration: 480, easing: "cubic-bezier(.2,.8,.2,1)" },
  );
}

function flipClose(onDone) {
  if (!ensurePortfolioElements()) return;
  const front = $("#folderFront");
  if (!front || !canFlip()) {
    portfolio.overlay.hidden = true;
    onDone?.();
    return;
  }
  const to = front.getBoundingClientRect();
  const from = portfolio.panel.getBoundingClientRect();
  const dx = to.left - from.left,
    dy = to.top - from.top;
  const sx = to.width / from.width,
    sy = to.height / from.height;
  const anim = portfolio.panel.animate(
    [
      {
        transformOrigin: "top left",
        transform: "translate(0,0) scale(1,1)",
        opacity: 1,
      },
      {
        transformOrigin: "top left",
        transform: `translate(${dx}px,${dy}px) scale(${sx},${sy})`,
        opacity: 0.4,
      },
    ],
    { duration: 420, easing: "cubic-bezier(.2,.8,.2,1)" },
  );
  anim.onfinish = () => {
    portfolio.overlay.hidden = true;
    onDone?.();
  };
}

/* ============ ПОРТФОЛИО — рендер ============ */

function renderPortfolioTabs() {
  const wrap = $("#portfolioTabs");
  if (!wrap) return;
  if (!state.projects.length) {
    wrap.innerHTML = "";
    return;
  }
  wrap.innerHTML = state.projects
    .map((p) => {
      const cls = p.id === portfolio.project?.id ? "ptab is-active" : "ptab";
      const kind = p.project_kind === "custom" ? "·" : "";
      return `<button type="button" class="${cls}" data-project-tab="${p.id}">Проект №${p.project_number} ${kind}</button>`;
    })
    .join("");
}

function renderPortfolioHeader() {
  const title = $("#portfolioTitle");
  const number = $("#portfolioNumber");
  if (!portfolio.project) {
    if (title) title.textContent = "—";
    if (number) number.textContent = "";
    return;
  }
  if (title) title.textContent = portfolio.project.title;
  const kindLabel =
    portfolio.project.project_kind === "custom" ? "свой" : "готовый";
  if (number)
    number.textContent = `№${portfolio.project.project_number} · ${kindLabel}`;
}

function renderPortfolioParams() {
  const wrap = $("#portfolioParams");
  const warning = $("#portfolioParamsWarning");
  const warningText = $("#portfolioParamsWarningText");
  const select = $("#portfolioRegion");
  if (!wrap || !select) return;

  if (!portfolio.project || portfolio.project.project_kind !== "custom") {
    wrap.hidden = true;
    return;
  }
  wrap.hidden = false;

  if (select.options.length <= 1) {
    select.innerHTML = '<option value="">— выберите регион —</option>';
    for (const r of state.regions) {
      const opt = document.createElement("option");
      opt.value = r.id;
      opt.textContent = `${r.name} (×${r.factor})`;
      select.appendChild(opt);
    }
  }
  select.value = portfolio.params?.region_id || "";

  const missing = portfolio.price?.missing || [];
  if (missing.length) {
    const labels = {
      type: "тип постройки",
      area: "площадь",
      floors: "этажность",
      region: "регион",
    };
    const text = missing.map((m) => labels[m] || m).join(", ");
    warningText.textContent = `Не выбрано: ${text}. Без этого цена не считается.`;
    warning.hidden = false;
  } else {
    warning.hidden = true;
  }
}

function getSortedItems() {
  const arr = [...portfolio.items];

  if (portfolio.sort === "price") {
    return arr.sort((a, b) => {
      const pa = a.type === "building" ? a.base_price || 0 : 0;
      const pb = b.type === "building" ? b.base_price || 0 : 0;
      if (pa !== pb) return pb - pa;
      return (a.name || "").localeCompare(b.name || "", "ru");
    });
  }
  if (portfolio.sort === "name") {
    return arr.sort((a, b) => (a.name || "").localeCompare(b.name || "", "ru"));
  }
  // added_at — строка MySQL, сравнение по строкам корректно
  return arr.sort((a, b) => {
    const da = String(a.added_at || "");
    const db = String(b.added_at || "");
    if (da < db) return -1;
    if (da > db) return 1;
    return 0;
  });
}

function renderPCard(it) {
  const isBuilding = it.type === "building";
  const isCustom = it.is_custom === 1;

  let titleHTML = "";
  if (isCustom) {
    titleHTML = `<span class="pcard__cat">Свой</span><span class="pcard__sep">›</span>${escapeHtml(it.name || "—")}`;
  } else if (!isBuilding && it.tag_name) {
    titleHTML = `<span class="pcard__cat">${escapeHtml(it.tag_name)}</span><span class="pcard__sep">›</span>${escapeHtml(it.name || "—")}`;
  } else {
    titleHTML = escapeHtml(it.name || "—");
  }

  const previewInner = it.image_path
    ? `<img src="../${it.image_path}" alt="">`
    : isBuilding
      ? "ДОМ"
      : isCustom
        ? "СВОЙ"
        : "ЛИСТ";

  const badge = isBuilding ? "Здание" : isCustom ? "Свой" : "Лист";
  const price = isBuilding
    ? `<span class="pcard__price">${fmtPrice(it.base_price)}</span>`
    : "";

  return `
    <div class="pcard" data-item-type="${it.type}" data-item-id="${it.id}">
      <button type="button" class="pcard__remove" data-pcard-remove aria-label="Удалить">×</button>
      <div class="pcard__body"><h4 class="pcard__title">${titleHTML}</h4></div>
      <div class="pcard__preview">
        <span class="pcard__badge">${badge}</span>
        ${previewInner}
        ${price}
      </div>
    </div>`;
}

function renderPortfolioGrid() {
  const grid = document.getElementById("portfolioGrid");
  const pagination = document.getElementById("portfolioPagination");
  const pageInfo = document.getElementById("portfolioPageInfo");
  if (!grid) return;

  const sorted = getSortedItems();

  if (!sorted.length) {
    grid.innerHTML = `
      <div class="portfolio-empty">
        <p class="portfolio-empty__title">В проекте пока нет листов</p>
        <p class="portfolio-empty__text">${portfolio.project?.project_kind === "custom" ? "Выберите параметры и добавьте стили, материалы и т.д." : "Добавьте готовое здание из каталога."}</p>
      </div>`;
    if (pagination) pagination.hidden = true;
    return;
  }

  const totalPages = Math.max(1, Math.ceil(sorted.length / portfolio.pageSize));
  if (portfolio.page >= totalPages) portfolio.page = totalPages - 1;
  if (portfolio.page < 0) portfolio.page = 0;

  const start = portfolio.page * portfolio.pageSize;
  grid.innerHTML = sorted
    .slice(start, start + portfolio.pageSize)
    .map(renderPCard)
    .join("");

  if (pagination) pagination.hidden = totalPages <= 1;
  if (pageInfo) pageInfo.textContent = `${portfolio.page + 1} / ${totalPages}`;

  const prev = document.querySelector("[data-page-prev]");
  const next = document.querySelector("[data-page-next]");
  if (prev) prev.disabled = portfolio.page === 0;
  if (next) next.disabled = portfolio.page >= totalPages - 1;
}

function renderPortfolioTotal() {
  const el = $("#portfolioTotal");
  if (!el) return;
  if (!portfolio.price) {
    el.textContent = "—";
    return;
  }
  if (!portfolio.price.can_calculate) {
    el.textContent = "—";
    return;
  }
  el.textContent = fmtPrice(portfolio.price.total);
}

function renderPortfolioBreakdown() {
  const wrap = $("#portfolioBreakdown");
  if (!wrap) return;
  const breakdown = portfolio.price?.breakdown || [];
  if (!breakdown.length) {
    wrap.innerHTML = "";
    return;
  }

  wrap.innerHTML = breakdown
    .map((row) => {
      const cls = `breakdown-row breakdown-row--${row.kind}`;
      const val =
        typeof row.value === "number" ? fmtPrice(row.value) : String(row.value);
      return `<div class="${cls}"><span class="breakdown-row__label">${escapeHtml(row.label)}</span><span class="breakdown-row__value">${escapeHtml(val)}</span></div>`;
    })
    .join("");
}

function renderPortfolioAll() {
  renderPortfolioHeader();
  renderPortfolioParams();
  renderPortfolioTabs();
  renderPortfolioGrid();
  renderPortfolioTotal();
  renderPortfolioBreakdown();
}

/* ============ ПОРТФОЛИО — загрузка / открытие ============ */

async function loadPortfolioProject(projectId) {
  if (!projectId) {
    portfolio.project = null;
    portfolio.items = [];
    portfolio.params = null;
    portfolio.price = null;
    renderPortfolioAll();
    return;
  }
  const data = await api(`${API.getProject}?id=${projectId}`);
  if (!data.ok) {
    showToast(data.error || "Не удалось открыть", true);
    return;
  }

  portfolio.project = data.data.project;
  portfolio.items = data.data.items;
  portfolio.params = data.data.params;
  portfolio.price = data.data.price;
  portfolio.page = 0;
  renderPortfolioAll();
}

async function openPortfolio() {
  if (!state.activeProjectId) {
    openCreateModal();
    return;
  }
  if (!ensurePortfolioElements()) return;

  await loadRegions();
  await loadPortfolioProject(state.activeProjectId);
  flipOpen();
  document.body.style.overflow = "hidden";
}

function closePortfolio() {
  flipClose(() => {
    document.body.style.overflow = "";
  });
}

/* ============ ПОРТФОЛИО — детали ============ */

function openItemDetails(type, id) {
  const item = portfolio.items.find((x) => x.type === type && x.id === id);
  if (!item) return;

  const modal = document.getElementById("portfolioDetails");
  const preview = document.getElementById("pdetailsPreview");
  const content = document.getElementById("pdetailsContent");
  const removeBtn = document.getElementById("pdetailsRemove");
  if (!modal || !preview || !content) return;

  const isBuilding = type === "building";
  const isCustom = item.is_custom === 1;

  preview.innerHTML = item.image_path
    ? `<img src="../${item.image_path}" alt="">`
    : isBuilding
      ? "ЗДАНИЕ"
      : isCustom
        ? "СВОЙ ЛИСТ"
        : "ЛИСТ";

  let html = `<h3 class="pdetails__title">${escapeHtml(item.name || "—")}</h3>`;
  const meta = isBuilding
    ? "Здание"
    : isCustom
      ? `Свой вариант · ${escapeHtml(item.tag_name || "")}`
      : escapeHtml(item.tag_name || "Лист");
  html += `<p class="pdetails__meta">${meta}</p>`;

  if (isBuilding) {
    html += `<p class="pdetails__desc">Площадь ${item.area_m2} м² · ${item.floors} эт. · ${item.bedrooms} спал. · ${item.bathrooms} с/у</p>`;
    html += `<dl class="pdetails__specs">
      <div><dt>Стиль</dt><dd>${escapeHtml(item.style || "—")}</dd></div>
      <div><dt>Материал</dt><dd>${escapeHtml(item.wall_material || "—")}</dd></div>
      <div><dt>Кровля</dt><dd>${escapeHtml(item.roof_type || "—")}</dd></div>
      <div><dt>Гараж</dt><dd>${escapeHtml(item.garage || "—")}</dd></div>
      <div><dt>Цена</dt><dd>${fmtPrice(item.base_price)}</dd></div>
    </dl>`;
  } else {
    html += `<p class="pdetails__desc">${escapeHtml(item.description || "")}</p>`;
  }

  content.innerHTML = html;

  if (removeBtn) {
    removeBtn.onclick = () => {
      if (isCustom) {
        if (
          confirm(`Удалить свой лист «${item.name}»? Он удалится навсегда.`)
        ) {
          deleteCustomSheetAndRefresh(item.id);
        }
      } else {
        if (confirm(`Убрать «${item.name}» из проекта?`)) {
          removePortfolioItem(type, id);
        }
      }
    };
  }

  modal.hidden = false;
}

function closeItemDetails() {
  document.getElementById("portfolioDetails")?.setAttribute("hidden", "");
}

async function removePortfolioItem(type, id) {
  const fd = new FormData();
  fd.append("project_id", portfolio.project.id);
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

  await loadPortfolioProject(portfolio.project.id);
  renderFolderMeta();
  renderFolderPapers();
  closeItemDetails();
  showToast("Убрано из проекта");
}

async function deleteCustomSheetAndRefresh(sheetId) {
  const fd = new FormData();
  fd.append("sheet_id", sheetId);
  const data = await api(API.deleteCustomSheet, { method: "POST", body: fd });
  if (!data.ok) {
    showToast(data.error || "Не удалось удалить", true);
    return;
  }

  await loadProjectItems(state.activeProjectId);
  applyAddedStates();
  await loadPortfolioProject(portfolio.project.id);
  renderFolderMeta();
  renderFolderPapers();
  closeItemDetails();
  showToast("Свой лист удалён");
}

/* ============ ПОРТФОЛИО — rename / delete ============ */

function startRename() {
  const titleEl = $("#portfolioTitle");
  if (!titleEl || !portfolio.project) return;
  const original = portfolio.project.title;
  titleEl.contentEditable = "true";
  titleEl.classList.add("portfolio-panel__title--editing");
  titleEl.focus();
  const range = document.createRange();
  range.selectNodeContents(titleEl);
  const sel = window.getSelection();
  sel.removeAllRanges();
  sel.addRange(range);

  const finish = async (save) => {
    titleEl.contentEditable = "false";
    titleEl.classList.remove("portfolio-panel__title--editing");
    const next = titleEl.textContent.trim();
    if (!save || !next || next === original) {
      titleEl.textContent = original;
      return;
    }

    const fd = new FormData();
    fd.append("project_id", portfolio.project.id);
    fd.append("title", next);
    const data = await api(API.renameProject, { method: "POST", body: fd });
    if (!data.ok) {
      titleEl.textContent = original;
      showToast(data.error || "Ошибка", true);
      return;
    }
    portfolio.project.title = next;
    const p = state.projects.find((x) => x.id === portfolio.project.id);
    if (p) p.title = next;
    renderFolderMeta();
    showToast("Переименовано");
  };

  const onKey = (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      cleanup();
      finish(true);
    }
    if (e.key === "Escape") {
      e.preventDefault();
      cleanup();
      finish(false);
    }
  };
  const onBlur = () => {
    cleanup();
    finish(true);
  };
  const cleanup = () => {
    titleEl.removeEventListener("keydown", onKey);
    titleEl.removeEventListener("blur", onBlur);
  };
  titleEl.addEventListener("keydown", onKey);
  titleEl.addEventListener("blur", onBlur);
}

async function deleteActiveProject() {
  if (!portfolio.project) return;
  if (
    !confirm(
      `Удалить проект №${portfolio.project.project_number} «${portfolio.project.title}»?`,
    )
  )
    return;

  const fd = new FormData();
  fd.append("project_id", portfolio.project.id);
  const data = await api(API.deleteProject, { method: "POST", body: fd });
  if (!data.ok) {
    showToast(data.error || "Не удалось удалить", true);
    return;
  }

  state.projects = state.projects.filter((p) => p.id !== portfolio.project.id);
  state.projectItems.clear();
  state.activeProjectId = data.data.next_active_id;

  if (state.activeProjectId) {
    await loadProjectItems(state.activeProjectId);
    await loadPortfolioProject(state.activeProjectId);
    applyAddedStates();
    renderFolderMeta();
    renderFolderPapers();
    showToast("Проект удалён");
  } else {
    closePortfolio();
    renderFolderMeta();
    renderFolderPapers();
    applyAddedStates();
    showToast("Проект удалён");
    setTimeout(() => openCreateModal(), 400);
  }
}

async function switchPortfolioProject(projectId) {
  if (!projectId || projectId === portfolio.project?.id) return;
  await setActiveProject(projectId);
  await loadPortfolioProject(projectId);
  await loadProjectItems(projectId);
  applyAddedStates();
  renderFolderMeta();
  renderFolderPapers();
}

async function setActiveProject(projectId) {
  if (!projectId) return;
  const fd = new FormData();
  fd.append("project_id", projectId);
  const data = await api(API.setActive, { method: "POST", body: fd });
  if (!data.ok) return;
  state.activeProjectId = projectId;
}

/* ============ ПОРТФОЛИО — регион ============ */

async function onRegionChange() {
  const select = $("#portfolioRegion");
  if (!select || !portfolio.project) return;

  const regionId = parseInt(select.value) || 0;
  const fd = new FormData();
  fd.append("project_id", portfolio.project.id);
  fd.append("region_id", regionId);

  const data = await api(API.setRegion, { method: "POST", body: fd });
  if (!data.ok) {
    showToast(data.error || "Ошибка", true);
    return;
  }

  portfolio.price = data.data.price;
  portfolio.params = portfolio.params || {};
  portfolio.params.region_id = regionId;

  // найдём имя региона
  const reg = state.regions.find((r) => r.id === regionId);
  portfolio.params.region_name = reg ? reg.name : null;
  portfolio.params.region_factor = reg ? reg.factor : null;

  renderPortfolioTotal();
  renderPortfolioBreakdown();
  renderPortfolioParams();
}

/* ============ ОБРАБОТЧИКИ ============ */

function bindEvents() {
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

  $$(".catalog-tab").forEach((tab) => {
    tab.addEventListener("click", () => {
      $$(".catalog-tab").forEach((t) =>
        t.classList.toggle("is-active", t === tab),
      );
      $$(".catalog-panel").forEach((p) =>
        p.classList.toggle("is-active", p.id === `panel-${tab.dataset.tab}`),
      );
    });
  });

  // делегирование кликов по каталогу
  document.addEventListener("click", (e) => {
    // кнопка "свой вариант"
    const customBtn = e.target.closest("[data-add-custom]");
    if (customBtn) {
      e.stopPropagation();
      if (!state.activeProjectId) {
        openCreateModal();
        return;
      }
      const project = state.projects.find(
        (p) => p.id === state.activeProjectId,
      );
      if (project && project.project_kind === "ready") {
        showToast(
          "Свой вариант доступен только в пользовательском проекте",
          true,
        );
        return;
      }
      const block = customBtn.closest(".tag-block");
      const tagId = parseInt(customBtn.dataset.addCustom);
      const tagName = block?.dataset.tagName || "тег";
      const isKey = block?.classList.contains("tag-block--key") ? 1 : 0;
      const slug = block?.dataset.tagSlug || "";
      openCustomSheetModal(tagId, tagName, isKey, slug);
      return;
    }

    // удаление из каталога
    const removeBtn = e.target.closest("[data-remove]");
    if (removeBtn) {
      e.stopPropagation();
      const card = removeBtn.closest("[data-type][data-id]");
      if (card) handleRemove(card.dataset.type, +card.dataset.id);
      return;
    }

    // клик по карточке
    const card = e.target.closest(".b-card, .sheet-card");
    if (card) handleAdd(card.dataset.type, +card.dataset.id);
  });

  // модалка создания
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

  // модалка своего варианта
  $("#customSheetSubmit")?.addEventListener("click", submitCustomSheet);
  $$("[data-close-custom]").forEach((el) =>
    el.addEventListener("click", closeCustomSheetModal),
  );

  $("#folderAddBtn")?.addEventListener("click", (e) => {
    e.stopPropagation();
    openCreateModal();
  });

  document.addEventListener("keydown", (e) => {
    if (e.key !== "Escape") return;
    if (!document.getElementById("customSheetModal")?.hasAttribute("hidden")) {
      closeCustomSheetModal();
      return;
    }
    if (
      !document.getElementById("createProjectModal")?.hasAttribute("hidden")
    ) {
      closeCreateModal();
      return;
    }
    if (portfolio.overlay && !portfolio.overlay.hidden) {
      if (document.activeElement?.isContentEditable) return;
      const details = document.getElementById("portfolioDetails");
      if (details && !details.hidden) {
        closeItemDetails();
        return;
      }
      closePortfolio();
    }
  });
}

function bindPortfolioEvents() {
  if (!ensurePortfolioElements()) return;

  $("#portfolioClose")?.addEventListener("click", closePortfolio);
  $(".portfolio-overlay__backdrop")?.addEventListener("click", closePortfolio);

  $("#portfolioRename")?.addEventListener("click", startRename);
  $("#portfolioDelete")?.addEventListener("click", deleteActiveProject);

  $("#portfolioSort")?.addEventListener("change", (e) => {
    portfolio.sort = e.target.value;
    portfolio.page = 0;
    renderPortfolioGrid();
  });

  $("#portfolioRegion")?.addEventListener("change", onRegionChange);

  $("#portfolioNew")?.addEventListener("click", () => {
    closePortfolio();
    setTimeout(() => openCreateModal(), 420);
  });

  $("#portfolioTabs")?.addEventListener("click", (e) => {
    const tab = e.target.closest("[data-project-tab]");
    if (!tab) return;
    switchPortfolioProject(+tab.dataset.projectTab);
  });

  document.getElementById("portfolioGrid")?.addEventListener("click", (e) => {
    const removeBtn = e.target.closest("[data-pcard-remove]");
    if (removeBtn) {
      e.stopPropagation();
      const card = removeBtn.closest(".pcard");
      if (!card) return;
      const type = card.dataset.itemType;
      const id = +card.dataset.itemId;
      const item = portfolio.items.find((x) => x.type === type && x.id === id);
      if (item?.is_custom === 1) {
        if (confirm(`Удалить свой лист «${item.name}»?`))
          deleteCustomSheetAndRefresh(id);
      } else {
        removePortfolioItem(type, id);
      }
      return;
    }
    const card = e.target.closest(".pcard");
    if (card) openItemDetails(card.dataset.itemType, +card.dataset.itemId);
  });

  document.querySelector("[data-page-prev]")?.addEventListener("click", () => {
    if (portfolio.page > 0) {
      portfolio.page--;
      renderPortfolioGrid();
    }
  });
  document.querySelector("[data-page-next]")?.addEventListener("click", () => {
    const total = Math.ceil(getSortedItems().length / portfolio.pageSize);
    if (portfolio.page < total - 1) {
      portfolio.page++;
      renderPortfolioGrid();
    }
  });

  $$("[data-close-details]").forEach((el) =>
    el.addEventListener("click", closeItemDetails),
  );
}

/* ============ ИНИЦИАЛИЗАЦИЯ ============ */

async function init() {
  readFiltersFromURL();
  bindEvents();

  await loadProjects();
  if (state.activeProjectId) await loadProjectItems(state.activeProjectId);

  await Promise.all([loadBuildings(), loadSheets(), loadRegions()]);

  renderBuildings();
  renderSheets();
  renderFolderMeta();
  renderFolderPapers();

  if (state.projects.length === 0) {
    setTimeout(() => openCreateModal(), 400);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  init();
  bindPortfolioEvents();

  const front = $("#folderFront");
  if (front) {
    const clone = front.cloneNode(true);
    front.parentNode.replaceChild(clone, front);
    clone.addEventListener("click", openPortfolio);
  }
});
