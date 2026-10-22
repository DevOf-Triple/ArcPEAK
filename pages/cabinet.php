<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$userName = $_SESSION['user_name'] ?? '';
$userLogin = $_SESSION['user_login'] ?? '';
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

  <main class="cabinet">
    <div class="cabinet__card">
      <span class="cabinet__label">Личный кабинет</span>
      <h1 class="cabinet__title">Привет, <?= htmlspecialchars($userName ?: $userLogin) ?></h1>
      <p class="cabinet__subtitle">@<?= htmlspecialchars($userLogin) ?></p>

      <form action="../api/logout.php" method="post" class="cabinet__form">
        <button type="submit" class="cabinet__logout">Выйти из аккаунта</button>
      </form>

      <a href="index.php" class="cabinet__back">← На главную</a>
    </div>
  </main>

</body>
</html>