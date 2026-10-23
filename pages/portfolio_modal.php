<div class="portfolio-overlay" id="portfolioOverlay" hidden>
  <div class="portfolio-overlay__backdrop" data-close-portfolio></div>

  <div class="portfolio-panel" id="portfolioPanel" role="dialog" aria-labelledby="portfolioTitle">
    <header class="portfolio-panel__header">
      <div class="portfolio-panel__title-block">
        <span class="portfolio-panel__label">Проект</span>
        <h2 class="portfolio-panel__title" id="portfolioTitle" data-rename-target>—</h2>
        <span class="portfolio-panel__number" id="portfolioNumber"></span>
      </div>
      <div class="portfolio-panel__actions">
        <button type="button" class="pp-btn" id="portfolioRename">Переименовать</button>
        <button type="button" class="pp-btn pp-btn--danger" id="portfolioDelete">Удалить</button>
        <button type="button" class="pp-btn pp-btn--close" id="portfolioClose" aria-label="Закрыть">×</button>
      </div>
    </header>

    <nav class="portfolio-tabs" id="portfolioTabs" aria-label="Проекты"></nav>

    <div class="portfolio-toolbar">
      <span class="portfolio-toolbar__label">Сортировка</span>
      <select class="portfolio-toolbar__select" id="portfolioSort">
        <option value="added_at">По дате добавления</option>
        <option value="price">По цене</option>
        <option value="name">По названию</option>
      </select>
    </div>

    <div class="portfolio-items" id="portfolioItems"></div>

    <footer class="portfolio-footer">
      <div class="portfolio-total">
        <span class="portfolio-total__label">Итого</span>
        <strong class="portfolio-total__value" id="portfolioTotal">0 ₽</strong>
      </div>
      <button type="button" class="portfolio-new-btn" id="portfolioNew">+ Новый проект</button>
    </footer>
  </div>
</div>