<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id, login, full_name, email, role, is_root_admin FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$me = $stmt->fetch();

if (!$me || $me['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$userName      = $me['full_name'] ?: $me['login'];
$isRootAdmin   = (int)$me['is_root_admin'] === 1;

$tables = [
    'users'                   => 'users',
    'portfolio_projects'      => 'portfolio_projects',
    'portfolio_items'         => 'portfolio_items',
    'buildings'               => 'buildings',
    'building_sheets'         => 'building_sheets',
    'catalog_tags'            => 'catalog_tags',
    'sheets'                  => 'sheets',
    'regions'                 => 'regions',
    'project_params'          => 'project_params',
    'project_sheet_overrides' => 'project_sheet_overrides',
    'saved_calculations'      => 'saved_calculations',
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Админ-панель — ПИК</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;500;600;700&family=Onest:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body data-is-root-admin="<?= $isRootAdmin ? '1' : '0' ?>">
  <div class="bg" aria-hidden="true"></div>

  <a href="index.php" class="page__back">← На главную</a>

  <main class="page__main">
    <section class="admin-hero">
      <div>
        <span class="admin-hero__label">Служебный доступ</span>
        <h1 class="admin-hero__title">Админ<em>-панель</em></h1>
      </div>
      <div class="admin-hero__user">
        Вы вошли как
        <strong><?= htmlspecialchars($userName) ?><?= $isRootAdmin ? ' · ROOT ADMIN' : '' ?></strong>
      </div>
    </section>

    <section class="admin-stats">
      <div class="admin-stat"><span class="admin-stat__num" id="statUsersTotal">—</span><span class="admin-stat__label">Всего пользователей</span></div>
      <div class="admin-stat"><span class="admin-stat__num" id="statUsersCurators">—</span><span class="admin-stat__label">Кураторов</span></div>
      <div class="admin-stat"><span class="admin-stat__num" id="statUsersAdmins">—</span><span class="admin-stat__label">Администраторов</span></div>
      <div class="admin-stat"><span class="admin-stat__num" id="statProjects">—</span><span class="admin-stat__label">Проектов</span></div>
      <div class="admin-stat"><span class="admin-stat__num" id="statCalculations">—</span><span class="admin-stat__label">Расчётов</span></div>
      <div class="admin-stat"><span class="admin-stat__num" id="statReceipts">—</span><span class="admin-stat__label">Чеков</span></div>
    </section>

    <div class="admin-tabs" role="tablist">
      <button class="admin-tab is-active" data-tab="users" role="tab">Пользователи</button>
      <button class="admin-tab" data-tab="tables" role="tab">Таблицы БД</button>
    </div>

    <!-- Панель: Пользователи -->
    <section class="admin-panel is-active" id="panel-users">
      <div class="admin-filters">
        <label class="admin-field">
          <span class="admin-field__label">Поиск</span>
          <input type="text" id="userSearch" class="admin-input" placeholder="Логин, ФИО или email">
        </label>
        <label class="admin-field">
          <span class="admin-field__label">Роль</span>
          <select id="roleFilter" class="admin-select">
            <option value="">Все роли</option>
            <option value="user">Пользователи</option>
            <option value="curator">Кураторы</option>
            <option value="admin">Администраторы</option>
          </select>
        </label>
      </div>

      <div id="usersTableWrap"></div>
    </section>

    <!-- Панель: Таблицы БД -->
    <section class="admin-panel" id="panel-tables">
      <div class="admin-tables-nav">
        <?php foreach ($tables as $key => $label): ?>
          <button data-table="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></button>
        <?php endforeach; ?>
      </div>
      <div id="tableContent"></div>
      <div id="tablePagination"></div>
    </section>
  </main>

  <!-- Модалка с новым паролем -->
  <div class="admin-modal" id="adminModal" hidden>
    <div class="admin-modal__backdrop"></div>
    <div class="admin-modal__window">
      <h3 class="admin-modal__title">Новый пароль</h3>
      <p class="admin-modal__lead">Пароль для пользователя <strong id="pwdLogin">—</strong>. Скопируйте и передайте лично.</p>
      <div class="admin-modal__pwd" id="pwdValue">—</div>
      <p class="admin-modal__pwd-hint">Кликните на пароль, чтобы выделить. Сохраните — повторно посмотреть нельзя.</p>
      <button type="button" class="admin-modal__btn" id="pwdOk">Готово</button>
      <button type="button" class="admin-modal__btn" id="pwdClose" style="margin-top:8px;background:transparent;color:#7f786d;border:1px solid rgba(42,36,28,.2);">Закрыть</button>
    </div>
  </div>

  <div class="admin-toast" id="adminToast" role="status" aria-live="polite"></div>

  <script src="../assets/js/admin.js"></script>
</body>
</html>