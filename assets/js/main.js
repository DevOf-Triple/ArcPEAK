const header = document.getElementById("header");
const logo = document.getElementById("logo");
const burger = document.getElementById("burger");
const particlesWrap = document.getElementById("particles");

let menuOpen = false;
const HIDE_THRESHOLD = 60;

function syncScroll() {
  const scrolled = window.scrollY > HIDE_THRESHOLD;
  logo.classList.toggle("logo--hidden", scrolled);
  header.classList.toggle("header--hidden", scrolled && !menuOpen);
}

function setMenu(state) {
  menuOpen = state;
  header.classList.toggle("header--open", menuOpen);
  burger.classList.toggle("burger--active", menuOpen);
  syncScroll();
}

window.addEventListener("scroll", syncScroll);

burger.addEventListener("click", (e) => {
  e.stopPropagation();
  setMenu(!menuOpen);
});

document.addEventListener("click", (e) => {
  if (!menuOpen) return;
  if (header.contains(e.target)) return;
  setMenu(false);
});

/* ===== Reveal on scroll ===== */
const revealIO = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add("is-visible");
      revealIO.unobserve(entry.target);
    });
  },
  { threshold: 0.12, rootMargin: "0px 0px -60px 0px" },
);

document
  .querySelectorAll("[data-reveal]")
  .forEach((el) => revealIO.observe(el));

/* ===== Счётчики ===== */
const counterIO = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      const el = entry.target;
      const target = +el.dataset.count;
      const duration = 1600;
      const start = performance.now();

      function tick(now) {
        const p = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - p, 3);
        el.textContent = Math.round(target * eased).toLocaleString("ru-RU");
        if (p < 1) requestAnimationFrame(tick);
      }
      requestAnimationFrame(tick);
      counterIO.unobserve(el);
    });
  },
  { threshold: 0.6 },
);

document
  .querySelectorAll("[data-count]")
  .forEach((el) => counterIO.observe(el));

/* ===== Idle particles ===== */
const PARTICLE_COUNT = 28;
for (let i = 0; i < PARTICLE_COUNT; i++) {
  const p = document.createElement("span");
  p.className = "particle";
  const size = 2 + Math.random() * 4;
  p.style.left = Math.random() * 100 + "%";
  p.style.width = size + "px";
  p.style.height = size + "px";
  p.style.animationDelay = Math.random() * 25 + "s";
  p.style.animationDuration = 20 + Math.random() * 18 + "s";
  p.style.opacity = 0.3 + Math.random() * 0.5;
  particlesWrap.appendChild(p);
}

syncScroll();

/* ===== Ротация фраз в хедере ===== */
const taglines = document.querySelectorAll(".header__tagline");
let taglineIndex = 0;

if (taglines.length > 1) {
  setInterval(() => {
    const current = taglines[taglineIndex];
    current.classList.remove("is-active");
    current.classList.add("is-out");

    setTimeout(() => current.classList.remove("is-out"), 800);

    taglineIndex = (taglineIndex + 1) % taglines.length;
    taglines[taglineIndex].classList.add("is-active");
  }, 4500);
}

/* ===== АВТОРИЗАЦИЯ ===== */
const authEl = document.getElementById("auth");
const authTab = document.getElementById("authTab");
const authOverlay = document.getElementById("authOverlay");
const authClose = document.getElementById("authClose");
const loginForm = document.getElementById("loginForm");
const registerForm = document.getElementById("registerForm");
const authUser = document.getElementById("authUser");

function setAuthMode(mode) {
  loginForm.classList.toggle("is-active", mode === "login");
  registerForm.classList.toggle("is-active", mode === "register");
  authUser.classList.toggle("is-active", mode === "user");
}

function openAuth(mode) {
  document.body.classList.add("auth-open");
  setAuthMode(mode || "login");
}

function closeAuth() {
  document.body.classList.remove("auth-open");
}

authTab.addEventListener("click", () => {
  if (document.body.classList.contains("auth-open")) closeAuth();
  else openAuth("login");
});

authClose.addEventListener("click", closeAuth);
authOverlay.addEventListener("click", closeAuth);

document.addEventListener("click", (e) => {
  const openBtn = e.target.closest("[data-auth-open]");
  if (openBtn) {
    e.preventDefault();
    openAuth(openBtn.dataset.authOpen || "login");
    return;
  }
  const switchLink = e.target.closest("[data-auth-switch]");
  if (switchLink) {
    e.preventDefault();
    setAuthMode(switchLink.dataset.authSwitch);
    return;
  }
  const logoutBtn = e.target.closest("[data-auth-logout]");
  if (logoutBtn) {
    e.preventDefault();
    doLogout();
  }
});

function applyUser(user) {
  document.body.classList.toggle("is-auth", !!user);

  document.querySelectorAll("[data-user-name]").forEach((el) => {
    el.textContent = user ? user.full_name : "—";
  });

  document.querySelectorAll("[data-user-login]").forEach((el) => {
    el.textContent = user ? "@" + user.login : "—";
  });

  setAuthMode(user ? "user" : "login");

  if (user) closeAuth();
}

async function submitAuth(url, form) {
  const errorEl = form.querySelector("[data-auth-error]");
  errorEl.textContent = "";
  try {
    const res = await fetch(url, { method: "POST", body: new FormData(form) });
    const data = await res.json();
    if (!data.ok) {
      errorEl.textContent = data.error || "Ошибка";
      return;
    }
    applyUser(data.user);
    closeAuth();
    showAuthToast(data.user); // <-- единственная точка вызова тоста
    form.reset();
  } catch (err) {
    errorEl.textContent = "Нет связи с сервером";
  }
}

loginForm.addEventListener("submit", (e) => {
  e.preventDefault();
  submitAuth("../api/login.php", loginForm);
});

registerForm.addEventListener("submit", (e) => {
  e.preventDefault();
  submitAuth("../api/register.php", registerForm);
});

async function doLogout() {
  try {
    await fetch("../api/logout.php", {
      method: "POST",
      headers: { "X-Requested-With": "fetch" },
    });
  } catch (err) {}
  window.location.href = "index.php";
}

(async () => {
  try {
    const res = await fetch("../api/me.php");
    const data = await res.json();
    applyUser(data.user || null);
  } catch (err) {
    applyUser(null);
  }
})();

// Тост только по факту входа/регистрации. Из me.php НЕ вызываем —
// иначе будет всплывать при каждом F5.
const authToast = document.getElementById("authToast");
let toastTimer = null;

function showAuthToast(user) {
  if (!authToast || !user) return;
  const nameEl = authToast.querySelector("[data-toast-name]");
  if (nameEl) nameEl.textContent = user.full_name || user.login;

  authToast.classList.add("is-visible");
  clearTimeout(toastTimer); // если быстро залогинился дважды — не плодим таймеры
  toastTimer = setTimeout(() => {
    authToast.classList.remove("is-visible");
  }, 4000);
}
