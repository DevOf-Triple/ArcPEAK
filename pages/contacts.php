<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Контакты — ПИК</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;500;600;700&family=Onest:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/contacts.css">
</head>
<body>
  <a href="index.php" class="page__back">← На главную</a>

  <main class="page__main">
    <h1 class="page__title">Связаться <em>с нами</em></h1>

    <div class="contacts">
      <div class="contacts__info">
        <div class="contacts__block">
          <span class="contacts__label">Телефон</span>
          <a href="tel:+74951234567" class="contacts__value">+7 (495) 123-45-67</a>
        </div>
        <div class="contacts__block">
          <span class="contacts__label">Почта</span>
          <a href="mailto:hello@pik-arch.ru" class="contacts__value">hello@pik-arch.ru</a>
        </div>
        <div class="contacts__block">
          <span class="contacts__label">Офис</span>
          <p class="contacts__value">Москва, Пресненская набережная, 12</p>
        </div>
        <div class="contacts__block">
          <span class="contacts__label">Часы работы</span>
          <p class="contacts__value">Пн–Пт, 09:00 — 19:00</p>
        </div>
      </div>

      <form class="contacts__form" onsubmit="event.preventDefault(); this.reset(); alert('Заявка отправлена');">
        <h2 class="contacts__form-title">Оставить заявку</h2>
        <p class="contacts__form-sub">Свяжемся в течение рабочего дня</p>

        <label class="contacts__field">
          <span class="contacts__label">Имя</span>
          <input type="text" name="name" class="contacts__input" placeholder="Как к вам обращаться">
        </label>

        <label class="contacts__field">
          <span class="contacts__label">Почта или телефон</span>
          <input type="text" name="contact" class="contacts__input" placeholder="mail@example.com">
        </label>

        <label class="contacts__field">
          <span class="contacts__label">Сообщение</span>
          <textarea name="message" rows="4" class="contacts__input" placeholder="Пара слов о проекте"></textarea>
        </label>

        <button type="submit" class="contacts__btn">Отправить</button>
      </form>
    </div>
  </main>
</body>
</html>