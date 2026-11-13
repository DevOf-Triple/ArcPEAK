<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

$userId = (int)$_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? '';
$userLogin = $_SESSION['user_login'] ?? '';

// Инфо о пользователе
$stmt = $pdo->prepare('SELECT email, created_at, role, is_root_admin FROM users WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();

$isCurator   = $user && $user['role'] === 'curator';
$isAdmin     = $user && $user['role'] === 'admin';
$isRootAdmin = $user && (int)$user['is_root_admin'] === 1;

// 3 последних проекта
$stmt = $pdo->prepare('
    SELECT id, project_number, title, project_kind, updated_at,
           (SELECT COUNT(*) FROM portfolio_items WHERE project_id = portfolio_projects.id) as items_count
    FROM portfolio_projects 
    WHERE user_id = ? 
    ORDER BY updated_at DESC 
    LIMIT 3
');
$stmt->execute([$userId]);
$recentProjects = $stmt->fetchAll();

foreach ($recentProjects as &$p) {
    $priceData = calculate_project_price($pdo, $p['id']);
    $p['price_total'] = $priceData['can_calculate'] ? $priceData['total'] : null;
}
unset($p);

// Сохранённые расчёты
$stmt = $pdo->prepare('SELECT * FROM saved_calculations WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$userId]);
$calculations = $stmt->fetchAll();

$fmtPrice = fn($n) => number_format($n, 0, ',', ' ') . ' ₽';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Личный кабинет — ПИК</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;500;600;700&family=Onest:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/cabinet.css">
</head>
<body class="cabinet-page">
  <!-- Фоновые блобы -->
  <div class="blobs" aria-hidden="true">
    <span class="blob blob--1"></span>
    <span class="blob blob--2"></span>
    <span class="blob blob--3"></span>
  </div>

  <a href="index.php" class="page__back">← На главную</a>

  <main class="cabinet-wrapper">
    
    <div class="cabinet-grid">

      <!-- 1. ЛИЧНОЕ ДЕЛО (Профиль) -->
      <section class="cab-card cab-card--profile" data-reveal>
        <div class="cab-stamp">ЛИЧНОЕ ДЕЛО</div>
        
        <div class="cab-profile__header">
          <div class="cab-profile__avatar">
            <?= mb_strtoupper(mb_substr($userName ?: $userLogin, 0, 1)) ?>
          </div>
          <div class="cab-profile__info">
            <h1 class="cab-profile__name">
              <?= htmlspecialchars($userName ?: $userLogin) ?>
              <?php if ($isAdmin): ?>
                <span style="display:inline-block;padding:2px 8px;background:#2a241c;color:#ddbd84;font-size:10px;font-weight:700;letter-spacing:1.5px;border-radius:3px;vertical-align:middle;margin-left:8px;"><?= $isRootAdmin ? 'ROOT ADMIN' : 'АДМИН' ?></span>
              <?php elseif ($isCurator): ?>
                <span style="display:inline-block;padding:2px 8px;background:rgba(52,152,219,.15);color:#2471a3;border:1px solid rgba(52,152,219,.4);font-size:10px;font-weight:700;letter-spacing:1.5px;border-radius:3px;vertical-align:middle;margin-left:8px;">КУРАТОР</span>
              <?php endif; ?>
            </h1>
            <p class="cab-profile__login">@<?= htmlspecialchars($userLogin) ?></p>
          </div>
        </div>

        <div class="cab-profile__details">
          <div class="cab-detail-row">
            <span>Почта</span>
            <strong><?= htmlspecialchars($user['email']) ?></strong>
          </div>
          <div class="cab-detail-row">
            <span>В системе с</span>
            <strong><?= date('d.m.Y', strtotime($user['created_at'])) ?></strong>
          </div>
        </div>

        <div class="cab-profile__actions">
          <?php if ($isAdmin): ?>
            <a href="admin.php" class="cab-btn" style="display:block;text-align:center;margin-bottom:12px;background:#ddbd84;color:#2a241c;">Перейти в админ-панель</a>
          <?php endif; ?>
          <form action="../api/logout.php" method="post">
            <button type="submit" class="cab-btn cab-btn--outline">Выйти из аккаунта</button>
          </form>
        </div>
      </section>

      <!-- 2. ПОСЛЕДНИЕ ПРОЕКТЫ -->
      <section class="cab-card cab-card--projects" data-reveal style="--d:.1s">
        <div class="cab-section-head">
          <h2 class="cab-section-title">Последние проекты</h2>
          <a href="catalog.php" class="cab-link">Все проекты →</a>
        </div>

        <?php if (empty($recentProjects)): ?>
          <div class="cab-empty">
            <p>Вы ещё не создали ни одного проекта.</p>
            <a href="catalog.php" class="cab-btn">Перейти в каталог</a>
          </div>
        <?php else: ?>
          <div class="cab-projects-list">
            <?php foreach ($recentProjects as $p): ?>
              <div class="cab-project-item">
                <div class="cab-project-item__icon">
                  <?php if ($p['project_kind'] === 'custom'): ?>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <line x1="4" y1="21" x2="4" y2="14"></line>
                      <line x1="4" y1="10" x2="4" y2="3"></line>
                      <line x1="12" y1="21" x2="12" y2="12"></line>
                      <line x1="12" y1="8" x2="12" y2="3"></line>
                      <line x1="20" y1="21" x2="20" y2="16"></line>
                      <line x1="20" y1="12" x2="20" y2="3"></line>
                      <line x1="1" y1="14" x2="7" y2="14"></line>
                      <line x1="9" y1="8" x2="15" y2="8"></line>
                      <line x1="17" y1="16" x2="23" y2="16"></line>
                    </svg>
                  <?php else: ?>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="square" stroke-linejoin="miter">
                      <path d="M3 10L12 3L21 10V21H3V10Z"/>
                      <path d="M9 21V15H15V21"/>
                    </svg>
                  <?php endif; ?>
                </div>
                <div class="cab-project-item__info">
                  <strong>Проект №<?= $p['project_number'] ?> · <?= htmlspecialchars($p['title']) ?></strong>
                  <span><?= $p['items_count'] ?> листов · обновлён <?= date('d.m', strtotime($p['updated_at'])) ?></span>
                </div>
                <div class="cab-project-item__price">
                  <?= $p['price_total'] ? $fmtPrice($p['price_total']) : '—' ?>
                </div>
                <a href="catalog.php?open_project=<?= $p['id'] ?>" class="cab-project-item__link">Открыть</a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>

      <!-- 3. СОХРАНЁННЫЕ РАСЧЁТЫ -->
      <section class="cab-card cab-card--calcs" data-reveal style="--d:.2s">
        <div class="cab-section-head">
          <h2 class="cab-section-title">История расчётов</h2>
          <a href="calculator.php" class="cab-link">Новый расчёт →</a>
        </div>

        <?php if (empty($calculations)): ?>
          <div class="cab-empty">
            <p>У вас пока нет сохранённых смет.</p>
            <a href="calculator.php" class="cab-btn">Открыть калькулятор</a>
          </div>
        <?php else: ?>
          <div class="cab-calcs-table-wrap">
            <table class="cab-table">
              <thead>
                <tr>
                  <th>Дата / Название</th>
                  <th>Параметры</th>
                  <th>Итого</th>
                  <th>Платёж</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($calculations as $c): ?>
                  <tr>
                    <td>
                      <strong><?= htmlspecialchars($c['title']) ?></strong>
                      <small><?= date('d.m.Y', strtotime($c['created_at'])) ?></small>
                    </td>
                    <td>
                      <div class="cab-table__specs">
                        <span><?= htmlspecialchars($c['type_name']) ?></span>
                        <span><?= htmlspecialchars($c['region_name']) ?></span>
                        <span><?= $c['floors'] ?> эт.</span>
                        <span><?= round($c['total_area']) ?> м²</span>
                      </div>
                    </td>
                    <td>
                      <strong class="cab-price"><?= $fmtPrice($c['total_price']) ?></strong>
                    </td>
                    <td>
                      <span class="cab-monthly"><?= $fmtPrice($c['monthly_payment']) ?>/мес</span>
                      <small><?= htmlspecialchars($c['loan_name']) ?></small>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </section>

      <!-- 4. УВЕДОМЛЕНИЯ -->
      <section class="cab-card cab-card--settings" data-reveal style="--d:.3s">
        <h2 class="cab-section-title">Уведомления</h2>
        <p class="cab-settings__desc">Настройте, какие письма вы хотите получать на почту.</p>
        
        <div class="cab-toggle-list">
          <label class="cab-toggle">
            <input type="checkbox" checked>
            <span class="cab-toggle__slider"></span>
            <span class="cab-toggle__label">Новости архитектурной корпорации</span>
          </label>
          <label class="cab-toggle">
            <input type="checkbox" checked>
            <span class="cab-toggle__slider"></span>
            <span class="cab-toggle__label">Статус моих проектов и новые листы</span>
          </label>
          <label class="cab-toggle">
            <input type="checkbox">
            <span class="cab-toggle__slider"></span>
            <span class="cab-toggle__label">Рекламные рассылки и акции</span>
          </label>
        </div>
      </section>

      <!-- 5. СМЕНА ПАРОЛЯ -->
      <section class="cab-card cab-card--security" data-reveal style="--d:.4s">
        <h2 class="cab-section-title">Смена пароля</h2>
        <p class="cab-settings__desc">Пароль должен быть не короче 6 символов. Сохраните новый пароль — восстановить старый нельзя.</p>

        <form class="cab-form" id="changePasswordForm" autocomplete="off">
          <label class="cab-field">
            <span class="cab-field__label">Текущий пароль</span>
            <input type="password" name="current_password" class="cab-field__input" required autocomplete="current-password">
          </label>
          <label class="cab-field">
            <span class="cab-field__label">Новый пароль</span>
            <input type="password" name="new_password" class="cab-field__input" required minlength="6" autocomplete="new-password">
          </label>
          <label class="cab-field">
            <span class="cab-field__label">Повторите новый пароль</span>
            <input type="password" name="new_password2" class="cab-field__input" required minlength="6" autocomplete="new-password">
          </label>

          <div class="cab-form__error" id="cabFormError"></div>

          <button type="submit" class="cab-btn">Изменить пароль</button>
        </form>
      </section>

    </div>
  </main>

  <script>
    // Reveal on scroll
    const io = new IntersectionObserver((entries) => {
      entries.forEach((e) => {
        if (e.isIntersecting) {
          e.target.classList.add("is-visible");
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.1 });
    document.querySelectorAll("[data-reveal]").forEach((el) => io.observe(el));

    // Смена пароля
    const cpForm  = document.getElementById('changePasswordForm');
    const cpError = document.getElementById('cabFormError');
    if (cpForm) {
      cpForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        cpError.textContent = '';
        cpError.style.color = '';

        const btn = cpForm.querySelector('button[type="submit"]');
        const original = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Сохранение…';

        try {
          const res = await fetch('../api/change_password.php', {
            method: 'POST',
            body: new FormData(cpForm),
          });
          const data = await res.json();
          if (!data.ok) {
            cpError.textContent = data.error || 'Ошибка';
            cpError.style.color = '#c0392b';
            return;
          }
          cpForm.reset();
          cpError.textContent = 'Пароль успешно изменён';
          cpError.style.color = '#2ecc71';
          setTimeout(() => { cpError.textContent = ''; cpError.style.color = ''; }, 4000);
        } catch (err) {
          cpError.textContent = 'Нет связи с сервером';
          cpError.style.color = '#c0392b';
        } finally {
          btn.disabled = false;
          btn.textContent = original;
        }
      });
    }
  </script>
</body>
</html>