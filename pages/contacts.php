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
  <div class="bg" aria-hidden="true"></div>

  <a href="index.php" class="page__back">← На главную</a>

  <main class="page__main">

    <section class="contacts-hero">
      <span class="contacts-hero__label">Связаться</span>
      <h1 class="page__title">Давайте <em>поговорим</em></h1>
      <p class="contacts-hero__lead">Первая встреча и оценка бюджета — бесплатно. Отвечаем в течение рабочего дня, без ботов и шаблонов.</p>
    </section>

    <div class="contacts">

      <div class="contacts__info" data-reveal>
        <h2 class="contacts__heading">Реквизиты и связь</h2>

        <div class="contacts__block">
          <span class="contacts__label">Телефон</span>
          <a href="tel:+74951234567" class="contacts__value">+7 (495) 123-45-67</a>
          <span class="contacts__hint">Пн–Пт, 09:00 — 19:00</span>
        </div>

        <div class="contacts__block">
          <span class="contacts__label">Почта</span>
          <a href="mailto:hello@pik-arch.ru" class="contacts__value">hello@pik-arch.ru</a>
          <span class="contacts__hint">Ответ в течение рабочего дня</span>
        </div>

        <div class="contacts__block">
          <span class="contacts__label">Офис</span>
          <p class="contacts__value">Москва, Пресненская набережная, 12</p>
          <span class="contacts__hint">БЦ «Башня на набережной», 45 этаж</span>
        </div>

        <div class="contacts__block">
          <span class="contacts__label">Соцсети</span>
          <div class="contacts__socials">
            <a href="#" class="contacts__social">Telegram</a>
            <a href="#" class="contacts__social">VK</a>
            <a href="#" class="contacts__social">YouTube</a>
            <a href="#" class="contacts__social">Behance</a>
          </div>
        </div>
      </div>

      <form class="contacts__form" id="contactForm" data-reveal style="--d:.1s">
        <h2 class="contacts__form-title">Оставить заявку</h2>
        <p class="contacts__form-sub">Расскажите о проекте — вернёмся с вопросами и оценкой</p>

        <label class="contacts__field">
          <span class="contacts__input-label">Имя</span>
          <input type="text" name="name" class="contacts__input" placeholder="Как к вам обращаться" required>
        </label>

        <label class="contacts__field">
          <span class="contacts__input-label">Почта или телефон</span>
          <input type="text" name="contact" class="contacts__input" placeholder="mail@example.com или +7..." required>
        </label>

        <label class="contacts__field">
          <span class="contacts__input-label">Тип проекта</span>
          <select name="type" class="contacts__input">
            <option value="">Выберите</option>
            <option value="house">Частный дом</option>
            <option value="flat">Квартира / апартаменты</option>
            <option value="complex">Многоквартирное здание</option>
            <option value="interior">Дизайн интерьера</option>
            <option value="other">Другое</option>
          </select>
        </label>

        <label class="contacts__field">
          <span class="contacts__input-label">Сообщение</span>
          <textarea name="message" rows="4" class="contacts__input" placeholder="Пара слов о задаче, площади, сроках..."></textarea>
        </label>

        <button type="submit" class="contacts__btn">Отправить заявку</button>
      </form>

    </div>

    <section class="contacts-map" data-reveal>
      <div class="contacts-map__visual">
        <span class="contacts-map__pin">◆</span>
        <span class="contacts-map__label">Пресненская набережная, 12</span>
      </div>
      <div class="contacts-map__info">
        <h2 class="contacts__heading">Как добраться</h2>
        <ul class="contacts-map__list">
          <li><strong>Метро:</strong> «Выставочная» или «Деловой центр», 5 минут пешком</li>
          <li><strong>На машине:</strong> съезд с Краснопресненской набережной, парковка в БЦ</li>
          <li><strong>Ориентир:</strong> башня с золотым шпилем, вход со стороны набережной</li>
        </ul>
      </div>
    </section>

  </main>

  <!-- тост для формы -->
  <div class="toast" id="toast" role="status" aria-live="polite">
    <span class="toast__label">Заявка отправлена</span>
    <strong class="toast__text">Мы свяжемся с вами в течение рабочего дня</strong>
  </div>

  <script>
    // reveal
    const io = new IntersectionObserver((entries) => {
      entries.forEach((e) => {
        if (e.isIntersecting) {
          e.target.classList.add("is-visible");
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.1, rootMargin: "0px 0px -40px 0px" });

    document.querySelectorAll("[data-reveal]").forEach((el) => io.observe(el));

    // форма — имитация отправки
    const form = document.getElementById("contactForm");
    const toast = document.getElementById("toast");
    let toastTimer = null;

    form.addEventListener("submit", (e) => {
      e.preventDefault();
      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }
      toast.classList.add("is-visible");
      clearTimeout(toastTimer);
      toastTimer = setTimeout(() => {
        toast.classList.remove("is-visible");
      }, 4000);
      form.reset();
    });
  </script>
</body>
</html>