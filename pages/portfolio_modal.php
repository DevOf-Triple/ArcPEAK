<div class="portfolio-overlay" id="portfolioOverlay" hidden>
  <div class="portfolio-overlay__backdrop" data-close-portfolio></div>

  <div class="portfolio-panel" id="portfolioPanel" role="dialog" aria-labelledby="portfolioTitle">
    
    <!-- ================= ЛЕВАЯ КОЛОНКА (ИНФО) ================= -->
    <aside class="portfolio-sidebar">
      
      <header class="portfolio-panel__header">
        <div class="portfolio-panel__title-block">
          <span class="portfolio-panel__label">Проект</span>
          <h2 class="portfolio-panel__title" id="portfolioTitle" data-rename-target>—</h2>
          <span class="portfolio-panel__number" id="portfolioNumber"></span>
        </div>
        
      </header>

      <!-- Блок заметок (описание) -->
      <div class="portfolio-notes">
        <span class="portfolio-params__label">Заметки к проекту</span>
        <textarea id="portfolioNotes" class="portfolio-notes__input" placeholder="Напишите пожелания, бюджет или комментарии к проекту..."></textarea>
      </div>

      <!-- Параметры (Регион) -->
      <div class="portfolio-params" id="portfolioParams" hidden>
        <div class="portfolio-params__row">
          <label class="portfolio-params__field">
            <span class="portfolio-params__label">Регион</span>
            <select id="portfolioRegion" class="portfolio-params__select">
              <option value="">— выберите регион —</option>
            </select>
          </label>
        </div>
        <div class="portfolio-params__warning" id="portfolioParamsWarning" hidden>
          <span class="portfolio-params__warning-icon">⚠</span>
          <span class="portfolio-params__warning-text" id="portfolioParamsWarningText"></span>
        </div>
      </div>

      <!-- Табы проектов -->
      <nav class="portfolio-tabs" id="portfolioTabs" aria-label="Проекты"></nav>

      <!-- Действия с проектом -->
      <div class="portfolio-actions">
        <button type="button" class="pp-btn" id="portfolioRename">Переименовать</button>
        <button type="button" class="pp-btn pp-btn--danger" id="portfolioDelete">Удалить</button>
      </div>

      <!-- Футер (Смета) -->
      <footer class="portfolio-footer">
        <div class="portfolio-total">
          <span class="portfolio-total__label">Итого</span>
          <strong class="portfolio-total__value" id="portfolioTotal">—</strong>
          <div class="portfolio-breakdown" id="portfolioBreakdown"></div>
        </div>
        <button type="button" class="portfolio-new-btn" id="portfolioNew">+ Новый проект</button>
      </footer>
    </aside>

    <!-- ================= ПРАВАЯ КОЛОНКА (ЛИСТЫ) ================= -->

    <main class="portfolio-main">
      <div class="portfolio-toolbar">
        <span class="portfolio-toolbar__label">Сортировка</span>
        <select class="portfolio-toolbar__select" id="portfolioSort">
          <option value="added_at">По дате добавления</option>
          <option value="price">По цене</option>
          <option value="name">По названию</option>
        </select>
        <button type="button" class="pp-btn pp-btn--close" id="portfolioClose" aria-label="Закрыть">×</button>
      </div>

      <div class="portfolio-grid" id="portfolioGrid"></div>

      <!-- Компактная пагинация -->
      <div class="portfolio-pagination" id="portfolioPagination" hidden>
        <button type="button" class="portfolio-page-btn" data-page-prev aria-label="Назад">←</button>
        <!-- Текст скрыт, но используется JS -->
        <span class="portfolio-page-info" id="portfolioPageInfo" style="display:none;">1 / 1</span>
        <button type="button" class="portfolio-page-btn" data-page-next aria-label="Вперед">→</button>
      </div>
    </main>

  </div>

  <!-- Модалка деталей -->
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