<?php

const BASE_PRICE_PER_M2 = 75000;
const FLOOR_FACTOR_STEP = 0.1;

function json_response($data, int $code = 200): void
{
    if (ob_get_length()) ob_clean();
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function require_auth(): int
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['user_id'])) {
        json_response(['ok' => false, 'error' => 'Требуется авторизация'], 401);
    }
    return (int)$_SESSION['user_id'];
}

function get_free_project_number(PDO $pdo, int $userId): int
{
    $stmt = $pdo->prepare('SELECT project_number FROM portfolio_projects WHERE user_id = ? ORDER BY project_number ASC');
    $stmt->execute([$userId]);
    $used = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));

    $n = 1;
    foreach ($used as $u) {
        if ($u === $n) $n++;
        elseif ($u > $n) break;
    }
    return $n;
}

/**
 * Возвращает актуальное значение листа с учётом override'а от куратора.
 */
function get_sheet_effective_value(PDO $pdo, int $projectId, array $sheet): float
{
    $stmt = $pdo->prepare('SELECT override_value FROM project_sheet_overrides WHERE project_id = ? AND sheet_id = ?');
    $stmt->execute([$projectId, (int)$sheet['id']]);
    $override = $stmt->fetchColumn();
    return $override !== false ? (float)$override : (float)$sheet['modifier_value'];
}

/**
 * Главная точка расчёта. Возвращает:
 * total, can_calculate, missing[], kind, breakdown[]
 */
function calculate_project_price(PDO $pdo, int $projectId): array
{
    $stmt = $pdo->prepare('SELECT project_kind FROM portfolio_projects WHERE id = ?');
    $stmt->execute([$projectId]);
    $kind = $stmt->fetchColumn();

    if (!$kind) {
        return ['total' => 0, 'can_calculate' => false, 'missing' => [], 'kind' => null, 'breakdown' => []];
    }

    return $kind === 'ready'
        ? calculate_ready_project($pdo, $projectId)
        : calculate_custom_project($pdo, $projectId);
}

function calculate_ready_project(PDO $pdo, int $projectId): array
{
    $stmt = $pdo->prepare("
        SELECT b.id, b.name, b.base_price
        FROM portfolio_items pi
        JOIN buildings b ON b.id = pi.item_id
        WHERE pi.project_id = ? AND pi.item_type = 'building'
        ORDER BY pi.added_at ASC, pi.id ASC
    ");
    $stmt->execute([$projectId]);
    $buildings = $stmt->fetchAll();

    $total = 0.0;
    $breakdown = [];

    foreach ($buildings as $b) {
        $price = (float)$b['base_price'];
        $total += $price;
        $breakdown[] = [
            'label' => $b['name'],
            'value' => round($price),
            'kind' => 'building',
        ];
    }

    return [
        'total' => (int)round($total),
        'can_calculate' => true,
        'missing' => [],
        'kind' => 'ready',
        'breakdown' => $breakdown,
    ];
}

function calculate_custom_project(PDO $pdo, int $projectId): array
{
    // Все листы проекта с инфой о теге
    $stmt = $pdo->prepare("
        SELECT s.id, s.name, s.tag_id, s.modifier_type, s.modifier_value, s.is_custom,
               t.slug AS tag_slug, t.name AS tag_name, t.is_key, t.is_radio,
               pi.added_at
        FROM portfolio_items pi
        JOIN sheets s ON s.id = pi.item_id AND pi.item_type = 'sheet'
        JOIN catalog_tags t ON t.id = s.tag_id
        WHERE pi.project_id = ?
        ORDER BY pi.added_at ASC, pi.id ASC
    ");
    $stmt->execute([$projectId]);
    $sheets = $stmt->fetchAll();

    $keyType = $keyArea = $keyFloors = null;
    $modifiers = [];
    foreach ($sheets as $s) {
        if ((int)$s['is_key'] === 1) {
            if ($s['tag_slug'] === 'type')   $keyType   = $s;
            if ($s['tag_slug'] === 'area')   $keyArea   = $s;
            if ($s['tag_slug'] === 'floors') $keyFloors = $s;
        } else {
            $modifiers[] = $s;
        }
    }

    // Регион
    $stmt = $pdo->prepare('
        SELECT r.id, r.name, r.factor
        FROM project_params pp
        LEFT JOIN regions r ON r.id = pp.region_id
        WHERE pp.project_id = ?
    ');
    $stmt->execute([$projectId]);
    $regionRow = $stmt->fetch();
    $region = ($regionRow && $regionRow['id']) ? $regionRow : null;

    // Что не выбрано
    $missing = [];
    if (!$keyType)   $missing[] = 'type';
    if (!$keyArea)   $missing[] = 'area';
    if (!$keyFloors) $missing[] = 'floors';
    if (!$region)    $missing[] = 'region';

    if ($missing) {
        return [
            'total' => 0,
            'can_calculate' => false,
            'missing' => $missing,
            'kind' => 'custom',
            'breakdown' => [],
        ];
    }

    $area       = get_sheet_effective_value($pdo, $projectId, $keyArea);
    $typeFactor = get_sheet_effective_value($pdo, $projectId, $keyType);
    $floors     = get_sheet_effective_value($pdo, $projectId, $keyFloors);
    $regionFactor = (float)$region['factor'];

    $floorsCoef = 1 + max(0, ($floors - 1)) * FLOOR_FACTOR_STEP;
    $basePrice = BASE_PRICE_PER_M2 * $area * $typeFactor * $floorsCoef * $regionFactor;

    $breakdown = [
        ['label' => 'Тип: ' . $keyType['name'],   'value' => '×' . number_format($typeFactor, 2, ',', ''), 'kind' => 'param'],
        ['label' => 'Площадь: ' . $keyArea['name'], 'value' => round($area) . ' м²', 'kind' => 'param'],
        ['label' => 'Этажность: ' . $keyFloors['name'], 'value' => '×' . number_format($floorsCoef, 2, ',', ''), 'kind' => 'param'],
        ['label' => 'Регион: ' . $region['name'], 'value' => '×' . number_format($regionFactor, 2, ',', ''), 'kind' => 'param'],
        ['label' => 'Базовая цена', 'value' => round($basePrice), 'kind' => 'base'],
    ];

    $price = $basePrice;
    foreach ($modifiers as $s) {
        $val = get_sheet_effective_value($pdo, $projectId, $s);
        $before = $price;
        switch ($s['modifier_type']) {
            case 'per_m2_add':       $price += $val * $area; break;
            case 'flat_add':         $price += $val; break;
            case 'percent_add':      $price *= (1 + $val / 100); break;
            case 'percent_discount': $price *= (1 - $val / 100); break;
            case 'multiplier':       $price *= $val; break;
        }
        $delta = $price - $before;
        $prefix = $delta >= 0 ? '+' : '−';
        $breakdown[] = [
            'label' => $s['tag_name'] . ': ' . $s['name'] . ($s['is_custom'] ? ' (свой)' : ''),
            'value' => $prefix . number_format(abs($delta), 0, ',', ' '),
            'kind' => 'modifier',
        ];
    }

    return [
        'total' => (int)round($price),
        'can_calculate' => true,
        'missing' => [],
        'kind' => 'custom',
        'breakdown' => $breakdown,
    ];
}