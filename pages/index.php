<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Архитектурная корпорация ПИК</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;500;600;700&family=Onest:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

  <div class="blobs" aria-hidden="true">
    <span class="blob blob--1"></span>
    <span class="blob blob--2"></span>
    <span class="blob blob--3"></span>
  </div>
  <div class="particles" id="particles" aria-hidden="true"></div>

  <header class="header" id="header">
    <div class="header__taglines">
      <span class="header__tagline is-active">Проектируем пространство, в котором хочется жить</span>
      <span class="header__tagline">От эскиза до приёмки — под одним договором</span>
      <span class="header__tagline">340 проектов. 12 регионов. 14 лет</span>
      <span class="header__tagline">Архитектура, которая работает на вас</span>
      <span class="header__tagline">Каждый проект — под конкретного человека</span>
    </div>
<nav class="header__menu">
  <div class="header__menu-group header__menu-group--auth">
    <a href="cabinet.php" class="header__menu-link">Личный кабинет</a>
    <a href="#" class="header__menu-link">Калькулятор</a>
    <a href="#" class="header__menu-link">Каталог</a>
    <a href="#" class="header__menu-link">Корзина</a>
    <a href="#" class="header__menu-link">Партнёрам</a>
    <a href="#" class="header__menu-link" data-auth-logout>Выйти</a>
  </div>

  <div class="header__menu-group">
    <a href="#" class="header__menu-link">Отзывы</a>
    <a href="#" class="header__menu-link">Вакансии</a>
    <a href="#" class="header__menu-link">Блог</a>
    <a href="#" class="header__menu-link">FAQ</a>
    <a href="#" class="header__menu-link">Контакты</a>
  </div>
</nav>
  </header>

  <button class="burger" id="burger" type="button" aria-label="Меню">
    <span></span>
    <span></span>
    <span></span>
  </button>

  <div class="building__container">

    <div class="building">
      <img src="../assets/img/peak_main_building_ext.png" alt="" class="building__img">
    </div>

    <div class="logo__container">
      <div class="logo" id="logo">
        <img src="../assets/img/logo.png" alt="" class="logo__img">
      </div>
    </div>

  </div>

  <main class="main">

    <!-- ===== УСЛУГИ ===== -->
    <section class="section">
      <div class="wrap">
        <div class="sec-head" data-reveal>
          <span class="sec-label">01 — Услуги</span>
          <h2 class="sec-title">Что мы <em>проектируем</em></h2>
          <p class="sec-lead">Полный цикл — от первого эскиза до сдачи объекта. Работаем с частными заказчиками, девелоперами и инвестиционными фондами по всей России.</p>
        </div>

        <div class="services">
          <article class="service" data-reveal style="--d:.05s">
            <span class="service__num">01</span>
            <h3 class="service__title">Частные дома</h3>
            <p class="service__text">Загородные резиденции, виллы и шале. Проект под участок, семью и образ жизни заказчика.</p>
            <span class="service__arrow">↗</span>
          </article>
          <article class="service" data-reveal style="--d:.15s">
            <span class="service__num">02</span>
            <h3 class="service__title">Квартиры и апартаменты</h3>
            <p class="service__text">Дизайн и перепланировка. От компактных студий до пентхаусов с авторской мебелью.</p>
            <span class="service__arrow">↗</span>
          </article>
          <article class="service" data-reveal style="--d:.25s">
            <span class="service__num">03</span>
            <h3 class="service__title">Многоквартирные здания</h3>
            <p class="service__text">Жилые комплексы, клубные дома, апарт-отели. От концепции до рабочей документации.</p>
            <span class="service__arrow">↗</span>
          </article>
          <article class="service" data-reveal style="--d:.35s">
            <span class="service__num">04</span>
            <h3 class="service__title">Дизайн интерьера</h3>
            <p class="service__text">Авторский интерьер с полным ведением: от планировочного решения до декора и текстиля.</p>
            <span class="service__arrow">↗</span>
          </article>
        </div>
      </div>
    </section>

    <!-- ===== НАШИ РАБОТЫ ===== -->
    <section class="section">
      <div class="wrap">
        <div class="sec-head" data-reveal>
          <span class="sec-label">02 — Портфолио</span>
          <h2 class="sec-title">Наши <em>работы</em></h2>
          <p class="sec-lead">Более 340 реализованных проектов в 12 регионах страны. Каждый — под конкретного заказчика и конкретное место.</p>
        </div>

        <div class="portfolio">
          <article class="work" data-reveal style="--d:.05s">
            <div class="work__img" style="background-image:url('../assets/img/sosnovy-bor.jpg'), linear-gradient(135deg,#c79b78,#99523a)"></div>
            <div class="work__info">
              <h3 class="work__title">Резиденция «Сосновый бор»</h3>
              <span class="work__meta">Частный дом · 420 м² · 2023</span>
            </div>
          </article>

          <article class="work" data-reveal style="--d:.1s">
            <div class="work__img" style="background-image:url('../assets/img/severnaya-gavan.jpg'), linear-gradient(135deg,#ae7f6c,#7f786d)"></div>
            <div class="work__info">
              <h3 class="work__title">ЖК «Северная гавань»</h3>
              <span class="work__meta">Многоквартирный · 12 этажей · 2024</span>
            </div>
          </article>

          <article class="work" data-reveal style="--d:.15s">
            <div class="work__img" style="background-image:url('../assets/img/penthaus-bereg.jpg'), linear-gradient(135deg,#ddbd84,#c79b78)"></div>
            <div class="work__info">
              <h3 class="work__title">Пентхаус на берегу</h3>
              <span class="work__meta">Квартира · 180 м² · 2023</span>
            </div>
          </article>

          <article class="work" data-reveal style="--d:.2s">
            <div class="work__img" style="background-image:url('../assets/img/alpiyskiy-veter.jpg'), linear-gradient(135deg,#99523a,#7f786d)"></div>
            <div class="work__info">
              <h3 class="work__title">Шале «Альпийский ветер»</h3>
              <span class="work__meta">Частный дом · 380 м² · 2022</span>
            </div>
          </article>

          <article class="work" data-reveal style="--d:.25s">
            <div class="work__img" style="background-image:url('../assets/img/krasnyy-oktyabr.jpg'), linear-gradient(135deg,#beb3a4,#ae7f6c)"></div>
            <div class="work__info">
              <h3 class="work__title">Лофт «Красный Октябрь»</h3>
              <span class="work__meta">Интерьер · 96 м² · 2026</span>
            </div>
          </article>

          <article class="work" data-reveal style="--d:.3s">
            <div class="work__img" style="background-image:url('../assets/img/tihiy.jpg'), linear-gradient(135deg,#cfc9c5,#beb3a4)"></div>
            <div class="work__info">
              <h3 class="work__title">Клубный дом «Тихий»</h3>
              <span class="work__meta">Многоквартирный · 8 этажей · 2023</span>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- ===== ПОЧЕМУ МЫ ===== -->
    <section class="section">
      <div class="wrap">
        <div class="sec-head" data-reveal>
          <span class="sec-label">03 — Почему мы</span>
          <h2 class="sec-title">Нас <em>выбирают</em></h2>
          <p class="sec-lead">Четырнадцать лет на рынке, сотни сданных объектов и репутация, которую не купишь рекламой.</p>
        </div>

        <div class="stats" data-reveal>
          <div class="stat">
            <span class="stat__num"><span data-count="14">0</span></span>
            <span class="stat__label">лет на рынке</span>
          </div>
          <div class="stat">
            <span class="stat__num"><span data-count="340">0</span>+</span>
            <span class="stat__label">проектов сдано</span>
          </div>
          <div class="stat">
            <span class="stat__num"><span data-count="52">0</span></span>
            <span class="stat__label">архитекторов в штате</span>
          </div>
          <div class="stat">
            <span class="stat__num"><span data-count="98">0</span>%</span>
            <span class="stat__label">клиентов рекомендуют</span>
          </div>
        </div>

        <div class="reasons">
          <div class="reason" data-reveal style="--d:.05s">
            <span class="reason__num">01</span>
            <div class="reason__body">
              <h3>Авторский надзор</h3>
              <p>Ведём проект до самого конца — от котлована до последнего светильника.</p>
            </div>
          </div>
          <div class="reason" data-reveal style="--d:.1s">
            <span class="reason__num">02</span>
            <div class="reason__body">
              <h3>Собственная BIM-лаборатория</h3>
              <p>Цифровая модель здания до начала стройки — никаких сюрпризов на площадке.</p>
            </div>
          </div>
          <div class="reason" data-reveal style="--d:.15s">
            <span class="reason__num">03</span>
            <div class="reason__body">
              <h3>Фиксированная смета</h3>
              <p>Стоимость закрепляем договором. Без «внезапных» дополнительных расходов.</p>
            </div>
          </div>
          <div class="reason" data-reveal style="--d:.2s">
            <span class="reason__num">04</span>
            <div class="reason__body">
              <h3>Гарантия 5 лет</h3>
              <p>Отвечаем за конструктив, инженерию и отделку всё это время.</p>
            </div>
          </div>
          <div class="reason" data-reveal style="--d:.25s">
            <span class="reason__num">05</span>
            <div class="reason__body">
              <h3>Один договор — весь цикл</h3>
              <p>Проект, стройка, надзор, приёмка. Всё внутри одной компании.</p>
            </div>
          </div>
          <div class="reason" data-reveal style="--d:.3s">
            <span class="reason__num">06</span>
            <div class="reason__body">
              <h3>Прозрачные этапы</h3>
              <p>Личный кабинет с фотографиями стройки каждую неделю.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== ШТАТ ===== -->
    <section class="section">
      <div class="wrap">
        <div class="sec-head" data-reveal>
          <span class="sec-label">04 — Команда</span>
          <h2 class="sec-title">Штат <em>сотрудников</em></h2>
          <p class="sec-lead">52 специалиста в штате, ещё около сотни подрядчиков и партнёров по всей стране.</p>
        </div>

        <div class="team">
          <article class="member" data-reveal style="--d:.05s">
            <div class="member__photo">АВ</div>
            <h3 class="member__name">Александр Волков</h3>
            <span class="member__role">Главный архитектор</span>
            <p class="member__bio">18 лет практики, лауреат премии «Золотое сечение» 2022.</p>
          </article>
          <article class="member" data-reveal style="--d:.12s">
            <div class="member__photo">МС</div>
            <h3 class="member__name">Марина Соколова</h3>
            <span class="member__role">Руководитель отдела дизайна</span>
            <p class="member__bio">Специалист по авторским интерьерам. 12 лет в профессии.</p>
          </article>
          <article class="member" data-reveal style="--d:.19s">
            <div class="member__photo">ДО</div>
            <h3 class="member__name">Дмитрий Орлов</h3>
            <span class="member__role">Главный инженер-конструктор</span>
            <p class="member__bio">Отвечает за несущий каркас и BIM-моделирование.</p>
          </article>
          <article class="member" data-reveal style="--d:.26s">
            <div class="member__photo">ЕК</div>
            <h3 class="member__name">Елена Кузнецова</h3>
            <span class="member__role">Директор по работе с клиентами</span>
            <p class="member__bio">Ведёт ключевых заказчиков от первой встречи до приёмки.</p>
          </article>
        </div>
      </div>
    </section>

    <!-- ===== УСЛОВИЯ ===== -->
    <section class="section">
      <div class="wrap">
        <div class="sec-head" data-reveal>
          <span class="sec-label">05 — Как мы работаем</span>
          <h2 class="sec-title">Условия <em>работы</em></h2>
          <p class="sec-lead">Прозрачный процесс из четырёх этапов. На каждом — отчёт и точка согласования с заказчиком.</p>
        </div>

        <div class="steps">
          <div class="step" data-reveal style="--d:.05s">
            <span class="step__num">01</span>
            <h3 class="step__title">Бриф</h3>
            <p class="step__text">Знакомимся, изучаем участок, обсуждаем бюджет и желания. 2–3 дня.</p>
          </div>
          <div class="step" data-reveal style="--d:.15s">
            <span class="step__num">02</span>
            <h3 class="step__title">Концепция</h3>
            <p class="step__text">Объёмно-планировочное решение, эскизы, 3D-визуализации. 2 недели.</p>
          </div>
          <div class="step" data-reveal style="--d:.25s">
            <span class="step__num">03</span>
            <h3 class="step__title">Рабочий проект</h3>
            <p class="step__text">Полный комплект документации для стройки. 6–8 недель.</p>
          </div>
          <div class="step" data-reveal style="--d:.35s">
            <span class="step__num">04</span>
            <h3 class="step__title">Надзор</h3>
            <p class="step__text">Авторский надзор и приёмка — весь период строительства.</p>
          </div>
        </div>

        <div class="cta" data-reveal>
          <div class="cta__text">
            <h3>Готовы обсудить ваш проект?</h3>
            <p>Первая встреча и оценка — бесплатно.</p>
          </div>
          <a href="#" class="cta__btn">Оставить заявку</a>
        </div>
      </div>
    </section>

  </main>

  <footer class="footer">
    <div class="footer__grid">
      <div class="footer__col">
        <span class="footer__logo">ПИК</span>
        <p class="footer__about">Архитектурная корпорация. Проектируем, строим, ведём объекты по всей России с 2010 года.</p>
      </div>
      <div class="footer__col">
        <h4 class="footer__title">Навигация</h4>
        <ul class="footer__list">
          <li><a href="#">Услуги</a></li>
          <li><a href="#">Проекты</a></li>
          <li><a href="#">О нас</a></li>
          <li><a href="#">Контакты</a></li>
        </ul>
      </div>
      <div class="footer__col">
        <h4 class="footer__title">Контакты</h4>
        <ul class="footer__list footer__list--plain">
          <li>+7 (495) 123-45-67</li>
          <li>hello@pik-arch.ru</li>
          <li>Москва, Пресненская наб., 12</li>
        </ul>
      </div>
      <div class="footer__col">
        <h4 class="footer__title">Соцсети</h4>
        <ul class="footer__list">
          <li><a href="#">Telegram</a></li>
          <li><a href="#">VK</a></li>
          <li><a href="#">YouTube</a></li>
          <li><a href="#">Behance</a></li>
        </ul>
      </div>
    </div>
    <div class="footer__bottom">
      <span>© 2026 Архитектурная корпорация ПИК</span>
      <span>Все права защищены</span>
    </div>
  </footer>

  <div class="auth-overlay" id="authOverlay"></div>

  <aside class="auth" id="auth" aria-label="Авторизация">
    <button class="auth__tab" id="authTab" type="button" aria-label="Открыть вход">
      <span class="auth__tab-text">ВХОД</span>
    </button>

    <div class="auth__panel">
      <button class="auth__close" id="authClose" type="button" aria-label="Закрыть">×</button>

      <form class="auth__form auth__form--login is-active" id="loginForm" novalidate>
        <h3 class="auth__title">Вход</h3>
        <p class="auth__subtitle">Введите свои данные</p>

        <label class="auth__field">
          <span class="auth__label">Логин или почта</span>
          <input type="text" name="login" class="auth__input" placeholder="alex_volkov или mail@example.com" autocomplete="username">
        </label>

        <label class="auth__field">
          <span class="auth__label">Пароль</span>
          <input type="password" name="password" class="auth__input" placeholder="Минимум 6 символов" autocomplete="current-password">
        </label>

        <div class="auth__error" data-auth-error></div>

        <button type="submit" class="auth__submit">Войти</button>

        <p class="auth__switch">
          Нет аккаунта?
          <a href="#" data-auth-switch="register">Зарегистрируйтесь</a>
        </p>
      </form>

      <form class="auth__form auth__form--register" id="registerForm" novalidate>
        <h3 class="auth__title">Регистрация</h3>
        <p class="auth__subtitle">Создайте новый аккаунт</p>

        <label class="auth__field">
          <span class="auth__label">Логин</span>
          <input type="text" name="login" class="auth__input" placeholder="alex_volkov" autocomplete="username">
        </label>
        
        <label class="auth__field">
          <span class="auth__label">ФИО</span>
          <input type="text" name="full_name" class="auth__input" placeholder="Волков Александр Сергеевич" autocomplete="name">
        </label>
        
        <label class="auth__field">
          <span class="auth__label">Почта</span>
          <input type="email" name="email" class="auth__input" placeholder="alex@example.com" autocomplete="email">
        </label>
        
        <label class="auth__field">
          <span class="auth__label">Пароль</span>
          <input type="password" name="password" class="auth__input" placeholder="Минимум 6 символов" autocomplete="new-password">
        </label>
        
        <label class="auth__field">
          <span class="auth__label">Пароль повторно</span>
          <input type="password" name="password2" class="auth__input" placeholder="Повторите пароль" autocomplete="new-password">
        </label>
        
        <div class="auth__error" data-auth-error></div>

        <button type="submit" class="auth__submit">Создать аккаунт</button>

        <p class="auth__switch">
          Уже есть аккаунт?
          <a href="#" data-auth-switch="login">Войти</a>
        </p>
      </form>

      <div class="auth__user" id="authUser">
        <h3 class="auth__title">Привет!</h3>
        <p class="auth__user-name" data-user-name>—</p>
        <p class="auth__user-login" data-user-login>—</p>
        <button type="button" class="auth__btn-logout" data-auth-logout>Выйти</button>
      </div>
    </div>
  </aside>

  <!-- тост: показывается только при свежем логине/регистрации -->
  <div class="auth-toast" id="authToast" role="status" aria-live="polite">
    <span class="auth-toast__label">Успешный вход</span>
    <strong class="auth-toast__name" data-toast-name></strong>
  </div>

  <script src="../assets/js/main.js"></script>
</body>
</html>