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
  <title>Калькулятор — ПИК</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;500;600;700&family=Onest:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/calculator.css">
</head>
<body>
  <div class="bg" aria-hidden="true"></div>

  <a href="index.php" class="page__back">← На главную</a>

  <main class="page__main">
    <section class="calc-hero">
      <span class="calc-hero__label">Инструмент</span>
      <h1 class="page__title">Калькулятор <em>проекта</em></h1>
      <p class="calc-hero__lead">Привет, <?= htmlspecialchars($userName) ?>. Соберите проект по этажам и сразу увидите смету и условия ипотеки.</p>
    </section>

    <div class="calc">
      <div class="calc__form">

        <fieldset class="calc__group">
          <legend class="calc__legend">Тип постройки</legend>
          <div class="calc__radios">
            <label class="calc__radio">
              <input type="radio" name="type" value="flat" checked>
              <span class="calc__radio-body"><strong>Квартира</strong><small>×1.0</small></span>
            </label>
            <label class="calc__radio">
              <input type="radio" name="type" value="house">
              <span class="calc__radio-body"><strong>Частный дом</strong><small>×1.2</small></span>
            </label>
            <label class="calc__radio">
              <input type="radio" name="type" value="penthouse">
              <span class="calc__radio-body"><strong>Пентхаус</strong><small>×1.8</small></span>
            </label>
            <label class="calc__radio">
              <input type="radio" name="type" value="townhouse">
              <span class="calc__radio-body"><strong>Таунхаус</strong><small>×1.1</small></span>
            </label>
            <label class="calc__radio">
              <input type="radio" name="type" value="apartments">
              <span class="calc__radio-body"><strong>Апартаменты</strong><small>×0.9</small></span>
            </label>
            <label class="calc__radio">
              <input type="radio" name="type" value="villa">
              <span class="calc__radio-body"><strong>Вилла</strong><small>×1.5</small></span>
            </label>
          </div>
        </fieldset>

        <fieldset class="calc__group">
          <legend class="calc__legend">Регион</legend>
          <select id="region" class="calc__select">
            <option value="">Выберите регион</option>
          </select>
        </fieldset>

        <fieldset class="calc__group">
          <legend class="calc__legend">Этажность</legend>
          <div class="calc__inline">
            <input type="number" id="floors" class="calc__input" value="1" min="1" max="20">
            <span class="calc__hint">этажей (каждый после 1-го +10% к цене м²)</span>
          </div>
        </fieldset>

        <fieldset class="calc__group">
          <legend class="calc__legend">Помещения по этажам</legend>
          <div id="rooms" class="calc__rooms"></div>
        </fieldset>

        <fieldset class="calc__group">
          <legend class="calc__legend">Ипотека</legend>
          <div id="loanPresets" class="calc__loan-presets"></div>
        </fieldset>

      </div>

      <aside class="calc__result">
        <h2 class="calc__result-title">Смета</h2>

        <div class="calc__row"><span>Общая площадь</span><strong id="totalArea">0 м²</strong></div>
        <div class="calc__row"><span>Цена за м²</span><strong id="pricePerSqm">0 ₽</strong></div>
        <div class="calc__row calc__row--total"><span>Итого</span><strong id="totalPrice">0 ₽</strong></div>

        <h3 class="calc__result-sub">Ипотека</h3>
        <div class="calc__row"><span>Программа</span><strong id="loanName">—</strong></div>
        <div class="calc__row"><span>Первый взнос</span><strong id="initialFee">0 ₽</strong></div>
        <div class="calc__row"><span>Сумма кредита</span><strong id="loanAmount">0 ₽</strong></div>
        <div class="calc__row calc__row--hl"><span>Ежемесячный платёж</span><strong id="monthlyPayment">0 ₽</strong></div>
        <div class="calc__row"><span>Выплаты банку</span><strong id="totalPayout">0 ₽</strong></div>
        <div class="calc__row"><span>Переплата банку</span><strong id="overpayment">0 ₽</strong></div>
        <div class="calc__row calc__row--total"><span>Всего потрачено</span><strong id="grandTotal">0 ₽</strong></div>
      </aside>
    </div>
  </main>

  <script>
    const TYPES = {
      flat:      { pricePerSqm: 80000, factor: 1.0 },
      house:     { pricePerSqm: 80000, factor: 1.2 },
      penthouse: { pricePerSqm: 80000, factor: 1.8 },
      townhouse: { pricePerSqm: 80000, factor: 1.1 },
      apartments:{ pricePerSqm: 80000, factor: 0.9 },
      villa:     { pricePerSqm: 80000, factor: 1.5 },
    };

    // полный перечень субъектов РФ, отсортирован по алфавиту
    // коэффициент — условный, для демонстрации
    const REGIONS = [
      { name: 'Алтайский край', factor: 1.0 },
      { name: 'Амурская область', factor: 1.0 },
      { name: 'Архангельская область', factor: 1.0 },
      { name: 'Астраханская область', factor: 1.0 },
      { name: 'Белгородская область', factor: 1.0 },
      { name: 'Брянская область', factor: 1.0 },
      { name: 'Владимирская область', factor: 1.0 },
      { name: 'Волгоградская область', factor: 1.0 },
      { name: 'Вологодская область', factor: 1.0 },
      { name: 'Воронежская область', factor: 1.0 },
      { name: 'Еврейская автономная область', factor: 0.9 },
      { name: 'Забайкальский край', factor: 0.9 },
      { name: 'Ивановская область', factor: 1.0 },
      { name: 'Иркутская область', factor: 1.0 },
      { name: 'Кабардино-Балкарская Республика', factor: 0.9 },
      { name: 'Калининградская область', factor: 1.1 },
      { name: 'Калужская область', factor: 1.0 },
      { name: 'Камчатский край', factor: 1.0 },
      { name: 'Карачаево-Черкесская Республика', factor: 0.9 },
      { name: 'Кемеровская область — Кузбасс', factor: 0.9 },
      { name: 'Кировская область', factor: 0.9 },
      { name: 'Костромская область', factor: 1.0 },
      { name: 'Краснодарский край', factor: 1.3 },
      { name: 'Красноярский край', factor: 1.0 },
      { name: 'Курганская область', factor: 0.9 },
      { name: 'Курская область', factor: 1.0 },
      { name: 'Ленинградская область', factor: 1.2 },
      { name: 'Липецкая область', factor: 1.0 },
      { name: 'Магаданская область', factor: 1.0 },
      { name: 'Москва', factor: 1.5 },
      { name: 'Московская область', factor: 1.4 },
      { name: 'Мурманская область', factor: 1.0 },
      { name: 'Ненецкий автономный округ', factor: 1.0 },
      { name: 'Нижегородская область', factor: 1.1 },
      { name: 'Новгородская область', factor: 1.0 },
      { name: 'Новосибирская область', factor: 1.0 },
      { name: 'Омская область', factor: 1.0 },
      { name: 'Оренбургская область', factor: 1.0 },
      { name: 'Орловская область', factor: 1.0 },
      { name: 'Пензенская область', factor: 1.0 },
      { name: 'Пермский край', factor: 1.0 },
      { name: 'Приморский край', factor: 1.1 },
      { name: 'Псковская область', factor: 1.0 },
      { name: 'Республика Адыгея', factor: 1.0 },
      { name: 'Республика Алтай', factor: 0.9 },
      { name: 'Республика Башкортостан', factor: 1.1 },
      { name: 'Республика Бурятия', factor: 0.9 },
      { name: 'Республика Дагестан', factor: 0.9 },
      { name: 'Республика Ингушетия', factor: 0.9 },
      { name: 'Республика Калмыкия', factor: 0.9 },
      { name: 'Республика Карелия', factor: 1.0 },
      { name: 'Республика Коми', factor: 1.0 },
      { name: 'Республика Крым', factor: 1.2 },
      { name: 'Республика Марий Эл', factor: 0.9 },
      { name: 'Республика Мордовия', factor: 0.9 },
      { name: 'Республика Саха (Якутия)', factor: 1.0 },
      { name: 'Республика Северная Осетия — Алания', factor: 0.9 },
      { name: 'Республика Татарстан', factor: 1.1 },
      { name: 'Республика Тыва', factor: 0.9 },
      { name: 'Республика Хакасия', factor: 0.9 },
      { name: 'Ростовская область', factor: 1.1 },
      { name: 'Рязанская область', factor: 1.0 },
      { name: 'Самарская область', factor: 1.1 },
      { name: 'Санкт-Петербург', factor: 1.3 },
      { name: 'Саратовская область', factor: 1.0 },
      { name: 'Сахалинская область', factor: 1.0 },
      { name: 'Свердловская область', factor: 1.1 },
      { name: 'Севастополь', factor: 1.2 },
      { name: 'Смоленская область', factor: 1.0 },
      { name: 'Ставропольский край', factor: 1.0 },
      { name: 'Тамбовская область', factor: 1.0 },
      { name: 'Тверская область', factor: 1.0 },
      { name: 'Томская область', factor: 1.0 },
      { name: 'Тульская область', factor: 1.0 },
      { name: 'Тюменская область', factor: 1.0 },
      { name: 'Удмуртская Республика', factor: 1.0 },
      { name: 'Ульяновская область', factor: 1.0 },
      { name: 'Хабаровский край', factor: 1.1 },
      { name: 'Ханты-Мансийский автономный округ — Югра', factor: 1.1 },
      { name: 'Челябинская область', factor: 1.1 },
      { name: 'Чеченская Республика', factor: 0.9 },
      { name: 'Чувашская Республика', factor: 0.9 },
      { name: 'Чукотский автономный округ', factor: 1.0 },
      { name: 'Ямало-Ненецкий автономный округ', factor: 1.1 },
      { name: 'Ярославская область', factor: 1.0 },
    ].sort((a, b) => a.name.localeCompare(b.name, 'ru'));

    const LOAN_PRESETS = [
      { id: 'family',  name: 'Семейная',      rate: 6,  downPct: 20, years: 30 },
      { id: 'it',      name: 'IT-специалисты',rate: 5,  downPct: 20, years: 30 },
      { id: 'state',   name: 'Господдержка',  rate: 8,  downPct: 15, years: 20 },
      { id: 'market',  name: 'Рыночная',      rate: 15, downPct: 20, years: 20 },
      { id: 'military',name: 'Военная',       rate: 12, downPct: 20, years: 25 },
      { id: 'far',     name: 'Дальневосточная',rate: 3.5, downPct: 30, years: 55 },
    ];

    const fmt = new Intl.NumberFormat('ru-RU', { maximumFractionDigits: 0 });
    const rub = (v) => fmt.format(Math.round(v)) + ' ₽';
    const sqm = (v) => (Math.round(v * 10) / 10).toString().replace('.', ',') + ' м²';

    let roomsByFloor = {};   // { 1: [...], 2: [...] }
    let activePresetId = 'market';

    // заполняем регионы
    const regionSelect = document.getElementById('region');
    REGIONS.forEach((r) => {
      const opt = document.createElement('option');
      opt.value = r.name;
      opt.textContent = r.name + (r.factor !== 1 ? ' (×' + r.factor + ')' : '');
      regionSelect.appendChild(opt);
    });
    regionSelect.value = 'Московская область';

    // заполняем пресеты ипотеки
    const presetsWrap = document.getElementById('loanPresets');
    LOAN_PRESETS.forEach((p) => {
      const label = document.createElement('label');
      label.className = 'calc__loan-preset';
      label.innerHTML = `
        <input type="radio" name="loan" value="${p.id}" ${p.id === activePresetId ? 'checked' : ''}>
        <span class="calc__loan-body">
          <strong>${p.name}</strong>
          <small>${p.rate}% · взнос ${p.downPct}% · ${p.years} лет</small>
        </span>
      `;
      presetsWrap.appendChild(label);
    });

    presetsWrap.addEventListener('change', (e) => {
      if (e.target.name === 'loan') {
        activePresetId = e.target.value;
        recalc();
      }
    });

    /* ===== ПОМЕЩЕНИЯ ===== */

    function ensureFloors(n) {
      for (let f = 1; f <= n; f++) {
        if (!roomsByFloor[f]) roomsByFloor[f] = [];
      }
      Object.keys(roomsByFloor).forEach((f) => {
        if (parseInt(f) > n) delete roomsByFloor[f];
      });
    }

    function addRoom(floor, data) {
      if (!roomsByFloor[floor]) roomsByFloor[floor] = [];
      const room = {
        id: 'r_' + Date.now() + '_' + Math.random().toString(36).slice(2, 5),
        name: data?.name || 'Помещение ' + (roomsByFloor[floor].length + 1),
        length: data?.length ?? 5,
        width: data?.width ?? 4,
      };
      roomsByFloor[floor].push(room);
      renderRooms();
      recalc();
    }

    function removeRoom(floor, id) {
      roomsByFloor[floor] = roomsByFloor[floor].filter((r) => r.id !== id);
      renderRooms();
      recalc();
    }

    function updateRoom(floor, id, field, value) {
      const room = roomsByFloor[floor]?.find((r) => r.id === id);
      if (!room) return;
      if (field === 'name') room.name = value;
      else room[field] = parseFloat(value) || 0;
      recalc();
    }

    function renderRooms() {
      const wrap = document.getElementById('rooms');
      wrap.innerHTML = '';
      const floors = Math.max(1, parseInt(document.getElementById('floors').value) || 1);

      for (let f = 1; f <= floors; f++) {
        if (!roomsByFloor[f]) roomsByFloor[f] = [];

        const floorBlock = document.createElement('div');
        floorBlock.className = 'calc__floor';

        const head = document.createElement('div');
        head.className = 'calc__floor-head';
        head.innerHTML = `<span class="calc__floor-title">Этаж ${f}</span>`;
        floorBlock.appendChild(head);

        const list = document.createElement('div');
        list.className = 'calc__rooms-list';

        roomsByFloor[f].forEach((room, i) => {
          const el = document.createElement('div');
          el.className = 'calc__room';
          el.innerHTML = `
            <div class="calc__room-head">
              <span class="calc__room-num">${String(i + 1).padStart(2, '0')}</span>
              <input type="text" class="calc__input calc__input--name" value="${escapeAttr(room.name)}" placeholder="Название">
              <button type="button" class="calc__remove" data-floor="${f}" data-id="${room.id}" aria-label="Удалить">×</button>
            </div>
            <div class="calc__room-grid">
              <label class="calc__field">
                <span class="calc__label">Длина, м</span>
                <input type="number" class="calc__input" min="0" step="0.1" value="${room.length}" data-floor="${f}" data-id="${room.id}" data-field="length">
              </label>
              <label class="calc__field">
                <span class="calc__label">Ширина, м</span>
                <input type="number" class="calc__input" min="0" step="0.1" value="${room.width}" data-floor="${f}" data-id="${room.id}" data-field="width">
              </label>
              <div class="calc__room-area">
                <span class="calc__label">Площадь</span>
                <strong>${sqm(room.length * room.width)}</strong>
              </div>
            </div>
          `;
          list.appendChild(el);
        });

        const addBtn = document.createElement('button');
        addBtn.type = 'button';
        addBtn.className = 'calc__add';
        addBtn.textContent = '+ Добавить помещение на этаж ' + f;
        addBtn.addEventListener('click', () => addRoom(f));

        floorBlock.appendChild(list);
        floorBlock.appendChild(addBtn);
        wrap.appendChild(floorBlock);
      }

      // обработчики
      wrap.querySelectorAll('.calc__remove').forEach((btn) => {
        btn.addEventListener('click', () => removeRoom(parseInt(btn.dataset.floor), btn.dataset.id));
      });

      wrap.querySelectorAll('input[data-field]').forEach((inp) => {
        inp.addEventListener('input', (e) => {
          const floor = parseInt(e.target.dataset.floor);
          const id = e.target.dataset.id;
          updateRoom(floor, id, e.target.dataset.field, e.target.value);
          const room = roomsByFloor[floor]?.find((r) => r.id === id);
          if (room) {
            e.target.closest('.calc__room').querySelector('.calc__room-area strong').textContent = sqm(room.length * room.width);
          }
        });
      });

      wrap.querySelectorAll('.calc__input--name').forEach((inp, idx) => {
        // idx не глобальный — ищем по floor/id
        inp.addEventListener('input', (e) => {
          const roomEl = e.target.closest('.calc__room');
          const id = roomEl.querySelector('.calc__remove').dataset.id;
          const floor = parseInt(roomEl.querySelector('.calc__remove').dataset.floor);
          updateRoom(floor, id, 'name', e.target.value);
        });
      });
    }

    function escapeAttr(s) {
      return String(s).replace(/"/g, '&quot;');
    }

    /* ===== РАСЧЁТ ===== */

    function getTypeKey() {
      return document.querySelector('input[name="type"]:checked').value;
    }

    function getRegionFactor() {
      const name = regionSelect.value;
      const r = REGIONS.find((x) => x.name === name);
      return r ? r.factor : 1.0;
    }

    function calc() {
      const type = TYPES[getTypeKey()];
      const regionFactor = getRegionFactor();
      const floors = Math.max(1, parseInt(document.getElementById('floors').value) || 1);

      let totalArea = 0;
      for (let f = 1; f <= floors; f++) {
        (roomsByFloor[f] || []).forEach((room) => {
          totalArea += room.length * room.width;
        });
      }

      const floorFactor = 1 + (floors - 1) * 0.1;
      const pricePerSqm = type.pricePerSqm * type.factor * regionFactor * floorFactor;
      const totalPrice = totalArea * pricePerSqm;

      const preset = LOAN_PRESETS.find((p) => p.id === activePresetId) || LOAN_PRESETS[3];
      const downPct = preset.downPct;
      const rate = preset.rate;
      const years = preset.years;

      const initialFee = totalPrice * downPct / 100;
      const loanAmount = totalPrice - initialFee;
      const months = years * 12;
      const monthlyRate = rate / 12 / 100;

      let monthlyPayment = 0;
      if (loanAmount > 0 && months > 0) {
        if (monthlyRate === 0) {
          monthlyPayment = loanAmount / months;
        } else {
          const pow = Math.pow(1 + monthlyRate, months);
          monthlyPayment = loanAmount * monthlyRate * pow / (pow - 1);
        }
      }

      const totalPayout = monthlyPayment * months;
      const overpayment = totalPayout - loanAmount;

      return {
        totalArea, pricePerSqm, totalPrice,
        presetName: preset.name,
        initialFee, loanAmount, monthlyPayment, overpayment, totalPayout,
      };
    }

    function recalc() {
      const r = calc();
      document.getElementById('totalArea').textContent = sqm(r.totalArea);
      document.getElementById('pricePerSqm').textContent = rub(r.pricePerSqm);
      document.getElementById('totalPrice').textContent = rub(r.totalPrice);
      document.getElementById('loanName').textContent = r.presetName;
      document.getElementById('initialFee').textContent = rub(r.initialFee);
      document.getElementById('loanAmount').textContent = rub(r.loanAmount);
      document.getElementById('monthlyPayment').textContent = rub(r.monthlyPayment);
      document.getElementById('overpayment').textContent = rub(r.overpayment);
      document.getElementById('totalPayout').textContent = rub(r.totalPayout);
      document.getElementById('grandTotal').textContent = rub(r.initialFee + r.totalPayout);
    }

    /* ===== СОБЫТИЯ ===== */

    document.querySelectorAll('input[name="type"]').forEach((inp) => inp.addEventListener('change', recalc));
    regionSelect.addEventListener('change', recalc);

    document.getElementById('floors').addEventListener('input', () => {
      const n = Math.max(1, parseInt(document.getElementById('floors').value) || 1);
      ensureFloors(n);
      renderRooms();
      recalc();
    });

    // начальные помещения
    ensureFloors(1);
    addRoom(1, { name: 'Гостиная', length: 6, width: 4 });
    addRoom(1, { name: 'Спальня', length: 4, width: 4 });
  </script>
</body>
</html>