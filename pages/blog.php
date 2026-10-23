<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Блог — ПИК</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;500;600;700&family=Onest:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/blog.css">
</head>
<body>
  <div class="bg" aria-hidden="true"></div>

  <a href="index.php" class="page__back">← На главную</a>

  <main class="page__main">

    <section class="blog-hero">
      <span class="blog-hero__label">Журнал корпорации</span>
      <h1 class="page__title">Блог <em>о проектировании</em></h1>
      <p class="blog-hero__lead">Разбираем реальные кейсы, ошибки и решения. Без маркетинга и воды — только то, что видели сами на своих объектах.</p>
    </section>

    <div class="posts">

      <article class="post post--featured" data-reveal style="--d:.05s">
        <div class="post__cover">
          <span class="post__num">01</span>
          <span class="post__cat">Гайды</span>
        </div>
        <div class="post__body">
          <div class="post__meta">
            <span class="post__date">15 октября 2026</span>
            <span class="post__dot">·</span>
            <span class="post__time">8 мин чтения</span>
          </div>
          <h2 class="post__title">Как выбрать участок под частный дом</h2>
          <p class="post__excerpt">Грунт, подъездные пути, окружение, коммуникации — разбираем чек-лист из 12 пунктов, который реально сэкономит вам миллионы. На реальных примерах наших проектов.</p>
          <a href="#" class="post__more">Читать статью →</a>
        </div>
      </article>

      <article class="post" data-reveal style="--d:.1s">
        <div class="post__cover">
          <span class="post__num">02</span>
          <span class="post__cat">Аналитика</span>
        </div>
        <div class="post__body">
          <div class="post__meta">
            <span class="post__date">8 октября 2026</span>
            <span class="post__dot">·</span>
            <span class="post__time">6 мин</span>
          </div>
          <h3 class="post__title">Сколько реально стоит проект дома в 2026</h3>
          <p class="post__excerpt">Разложили по этапам: изыскания, эскиз, рабочка, надзор. Без маркетинговых округлений.</p>
          <a href="#" class="post__more">Читать →</a>
        </div>
      </article>

      <article class="post" data-reveal style="--d:.15s">
        <div class="post__cover">
          <span class="post__num">03</span>
          <span class="post__cat">Мнение</span>
        </div>
        <div class="post__body">
          <div class="post__meta">
            <span class="post__date">30 сентября 2026</span>
            <span class="post__dot">·</span>
            <span class="post__time">5 мин</span>
          </div>
          <h3 class="post__title">Зачем нужен авторский надзор</h3>
          <p class="post__excerpt">Что именно делает архитектор на площадке и почему без него часто выходит «как-то не так».</p>
          <a href="#" class="post__more">Читать →</a>
        </div>
      </article>

      <article class="post" data-reveal style="--d:.2s">
        <div class="post__cover">
          <span class="post__num">04</span>
          <span class="post__cat">BIM</span>
        </div>
        <div class="post__body">
          <div class="post__meta">
            <span class="post__date">22 сентября 2026</span>
            <span class="post__dot">·</span>
            <span class="post__time">7 мин</span>
          </div>
          <h3 class="post__title">BIM в частном домостроении: мифы и реальность</h3>
          <p class="post__excerpt">Кому цифровая модель реально экономит деньги, а кому — просто модный ярлык.</p>
          <a href="#" class="post__more">Читать →</a>
        </div>
      </article>

      <article class="post" data-reveal style="--d:.25s">
        <div class="post__cover">
          <span class="post__num">05</span>
          <span class="post__cat">Ошибки</span>
        </div>
        <div class="post__body">
          <div class="post__meta">
            <span class="post__date">14 сентября 2026</span>
            <span class="post__dot">·</span>
            <span class="post__time">6 мин</span>
          </div>
          <h3 class="post__title">7 ошибок в планировке, которые дорого стоят</h3>
          <p class="post__excerpt">Собрали то, что чаще всего переделывают в проектах «с улицы» — на реальных примерах.</p>
          <a href="#" class="post__more">Читать →</a>
        </div>
      </article>

      <article class="post" data-reveal style="--d:.3s">
        <div class="post__cover">
          <span class="post__num">06</span>
          <span class="post__cat">Гайды</span>
        </div>
        <div class="post__body">
          <div class="post__meta">
            <span class="post__date">5 сентября 2026</span>
            <span class="post__dot">·</span>
            <span class="post__time">4 мин</span>
          </div>
          <h3 class="post__title">Что спросить у архитектора до подписания договора</h3>
          <p class="post__excerpt">Семь вопросов, которые сразу отсеют сомнительных подрядчиков и покажут уровень студии.</p>
          <a href="#" class="post__more">Читать →</a>
        </div>
      </article>

    </div>

    <div class="blog-cta" data-reveal>
      <div class="blog-cta__text">
        <h3>Хотите получать новые статьи?</h3>
        <p>Раз в месяц присылаем самое полезное — без спама и рекламы.</p>
      </div>
      <a href="mailto:hello@pik-arch.ru?subject=Подписка на блог" class="blog-cta__btn">Подписаться</a>
    </div>

  </main>

  <script>
    const io = new IntersectionObserver((entries) => {
      entries.forEach((e) => {
        if (e.isIntersecting) {
          e.target.classList.add("is-visible");
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.1, rootMargin: "0px 0px -40px 0px" });

    document.querySelectorAll("[data-reveal]").forEach((el) => io.observe(el));
  </script>
</body>
</html>