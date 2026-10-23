<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$userName = $_SESSION['user_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Каталог — ПИК</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;500;600;700&family=Onest:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/catalog.css">
</head>
<body data-user="<?= htmlspecialchars($userName) ?>">
  <div class="bg" aria-hidden="true"></div>

  <a href="index.php" class="page__back">← На главную</a>

  <main class="page__main">
    <section class="catalog-hero">
      <span class="catalog-hero__label">Проекты</span>
      <h1 class="page__title">Каталог <em>решений</em></h1>
      <p class="catalog-hero__lead">Соберите свой проект из готовых решений или настройте всё под себя. Всё, что добавите, — попадёт в вашу папку проектов в правом нижнем углу.</p>
    </section>

    <!-- Фильтры -->
    <section class="catalog-filters" aria-label="Фильтры">
      <div class="catalog-filters__row">
        <label class="catalog-field">
          <span class="catalog-field__label">Стиль</span>
          <select id="fStyle" class="catalog-select">
            <option value="">Любой</option>
            <option value="loft">Лофт</option>
            <option value="minimal">Минимализм</option>
            <option value="hitek">Хай-тек</option>
            <option value="barnhouse">Барнхаус</option>
            <option value="scandi">Скандинавский</option>
            <option value="classic">Классический</option>
            <option value="modern">Современный</option>
            <option value="shale">Шале</option>
            <option value="fahverk">Фахверк</option>
          </select>
        </label>

        <label class="catalog-field">
          <span class="catalog-field__label">Площадь</span>
          <select id="fArea" class="catalog-select">
            <option value="">Любая</option>
            <option value="0-50">до 50 м²</option>
            <option value="50-100">50–100 м²</option>
            <option value="100-150">100–150 м²</option>
            <option value="150-200">150–200 м²</option>
            <option value="200-300">200–300 м²</option>
            <option value="300-">300+ м²</option>
          </select>
        </label>

        <label class="catalog-field">
          <span class="catalog-field__label">Этажность</span>
          <select id="fFloors" class="catalog-select">
            <option value="">Любая</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3plus">3+</option>
          </select>
        </label>

        <label class="catalog-field">
          <span class="catalog-field__label">Спальни</span>
          <select id="fBedrooms" class="catalog-select">
            <option value="">Любое</option>
            <option value="0">0</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5plus">5+</option>
          </select>
        </label>

        <label class="catalog-field">
          <span class="catalog-field__label">Санузлы</span>
          <select id="fBathrooms" class="catalog-select">
            <option value="">Любое</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3plus">3+</option>
          </select>
        </label>

        <label class="catalog-field">
          <span class="catalog-field__label">Гараж</span>
          <select id="fGarage" class="catalog-select">
            <option value="">Любой</option>
            <option value="none">Нет</option>
            <option value="canopy">Навес</option>
            <option value="1">1</option>
            <option value="2plus">2+</option>
          </select>
        </label>

        <label class="catalog-field">
          <span class="catalog-field__label">Материал</span>
          <select id="fMaterial" class="catalog-select">
            <option value="">Любой</option>
            <option value="gazobeton">Газобетон</option>
            <option value="kirpich">Кирпич</option>
            <option value="tree">Дерево / каркас</option>
          </select>
        </label>

        <label class="catalog-field">
          <span class="catalog-field__label">Кровля</span>
          <select id="fRoof" class="catalog-select">
            <option value="">Любая</option>
            <option value="flat">Плоская</option>
            <option value="2skat">Двускатная</option>
            <option value="4skat">Четырёхскатная</option>
          </select>
        </label>

        <label class="catalog-field catalog-field--price">
          <span class="catalog-field__label">Цена, ₽</span>
          <div class="catalog-price">
            <input type="number" id="fPriceMin" class="catalog-input" placeholder="от">
            <span>—</span>
            <input type="number" id="fPriceMax" class="catalog-input" placeholder="до">
          </div>
        </label>

        <label class="catalog-field catalog-field--sort">
          <span class="catalog-field__label">Сортировка</span>
          <select id="fSort" class="catalog-select">
            <option value="newest">Новые</option>
            <option value="price_asc">Цена ↑</option>
            <option value="price_desc">Цена ↓</option>
            <option value="area_asc">Площадь ↑</option>
            <option value="area_desc">Площадь ↓</option>
          </select>
        </label>
      </div>

      <div class="catalog-filters__features" id="fFeatures">
        <span class="catalog-field__label">Дополнительно:</span>
        <label class="feature-chip"><input type="checkbox" value="office"><span>Кабинет</span></label>
        <label class="feature-chip"><input type="checkbox" value="wardrobe"><span>Гардеробная</span></label>
        <label class="feature-chip"><input type="checkbox" value="sauna"><span>Сауна</span></label>
        <label class="feature-chip"><input type="checkbox" value="pool"><span>Бассейн</span></label>
        <label class="feature-chip"><input type="checkbox" value="terrace"><span>Терраса</span></label>
      </div>

      <div class="catalog-filters__actions">
        <button type="button" id="resetFilters" class="catalog-reset">Сбросить фильтры</button>
      </div>
    </section>

    <!-- Табы -->
    <div class="catalog-tabs" role="tablist">
      <button class="catalog-tab is-active" data-tab="ready" role="tab">Готовые решения</button>
      <button class="catalog-tab" data-tab="custom" role="tab">Пользовательское</button>
    </div>

    <!-- Панель: готовые решения -->
    <section class="catalog-panel is-active" id="panel-ready">
      <div class="catalog-grid" id="buildingsGrid"></div>
      <div class="catalog-empty" id="buildingsEmpty" hidden>
        <p class="catalog-empty__title">Ничего не найдено</p>
        <p class="catalog-empty__text">Попробуйте сбросить фильтры или выбрать другие параметры.</p>
        <button type="button" class="catalog-empty__btn" id="emptyReset">Сбросить фильтры</button>
      </div>
    </section>

    <!-- Панель: пользовательское -->
    <section class="catalog-panel" id="panel-custom">
      <div id="tagsWrap" class="tags-wrap"></div>
    </section>

  </main>

  <!-- Стопка папок -->
  <div class="folder-stack" id="folderStack" aria-hidden="true">
    <div class="folder-stack__layer folder-stack__layer--3"></div>
    <div class="folder-stack__layer folder-stack__layer--2"></div>
    <div class="folder-stack__layer folder-stack__layer--1"></div>
    <div class="folder-stack__papers" id="folderPapers"></div>
    <button type="button" class="folder-stack__front" id="folderFront">
      <span class="folder-stack__title">Мои проекты</span>
      <span class="folder-stack__meta" id="folderMeta">Нет проектов</span>
    </button>
    <button type="button" class="folder-stack__add" id="folderAddBtn" aria-label="Новый проект">+</button>
  </div>

  <!-- Модалка создания проекта -->
  <div class="modal" id="createProjectModal" hidden>
    <div class="modal__backdrop" data-close-modal></div>
    <div class="modal__window" role="dialog" aria-labelledby="createProjectTitle">
      <h3 class="modal__title" id="createProjectTitle">Создайте первый проект</h3>
      <p class="modal__lead">Проект — это папка, в которую вы собираете здания и листы.</p>
      <label class="modal__field">
        <span class="modal__label">Название</span>
        <input type="text" id="newProjectTitle" class="modal__input" value="Проект" placeholder="Например, «Дом для семьи»">
      </label>
      <div class="modal__actions">
        <button type="button" class="modal__btn modal__btn--ghost" data-close-modal>Отмена</button>
        <button type="button" class="modal__btn" id="createProjectSubmit">Создать</button>
      </div>
    </div>
  </div>

  <?php include __DIR__ . '/portfolio_modal.php'; ?>

  <!-- Тост -->
  <div class="toast" id="toast" role="status" aria-live="polite"></div>

  <script src="../assets/js/catalog.js"></script>
</body>
</html>