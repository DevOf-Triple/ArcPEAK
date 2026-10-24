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

    <div class="portfolio-grid" id="portfolioGrid"></div>

    <div class="portfolio-pagination" id="portfolioPagination" hidden>
      <button type="button" class="portfolio-page-btn" data-page-prev aria-label="Предыдущая">←</button>
      <span class="portfolio-page-info" id="portfolioPageInfo">1 / 1</span>
      <button type="button" class="portfolio-page-btn" data-page-next aria-label="Следующая">→</button>
    </div>

    <footer class="portfolio-footer">
      <div class="portfolio-total">
        <span class="portfolio-total__label">Итого</span>
        <strong class="portfolio-total__value" id="portfolioTotal">0 ₽</strong>
      </div>
      <button type="button" class="portfolio-new-btn" id="portfolioNew">+ Новый проект</button>
    </footer>
  </div>

  <!-- Модалка деталей — вложена в оверлей, поверх панели -->
  <div class="pdetails" id="portfolioDetails" hidden>
    <div class="pdetails__backdrop" data-close-details></div>
    <div class="pdetails__window" role="dialog">
      <button type="button" class="pdetails__close" data-close-details aria-label="Закрыть">×</button>
      <div class="pdetails__preview" id="pdetailsPreview"></div>
      <div class="pdetails__content" id="pdetailsContent"></div>
      <div class="pdetails__actions">
        <button type="button" class="pdetails__btn pdetails__btn--danger" id="pdetailsRemove">Удалить из проекта</button>
      </div>
    </div>
  </div>
</div>