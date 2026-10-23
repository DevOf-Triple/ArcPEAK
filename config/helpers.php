<?php

function json_response($data, int $code = 200): void
{
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
        if ($u === $n) {
            $n++;
        } elseif ($u > $n) {
            break;
        }
    }
    return $n;
}

function calculate_project_price(PDO $pdo, int $projectId): array
{
    $stmt = $pdo->prepare('SELECT item_type, item_id, added_at FROM portfolio_items WHERE project_id = ? ORDER BY added_at ASC, id ASC');
    $stmt->execute([$projectId]);
    $items = $stmt->fetchAll();

    $buildings = [];
    $sheets = [];

    foreach ($items as $it) {
        if ($it['item_type'] === 'building') {
            $buildings[(int)$it['item_id']] = true;
        } else {
            $sheets[] = (int)$it['item_id'];
        }
    }

    $buildingRows = [];
    if ($buildings) {
        $ids = array_keys($buildings);
        $in = implode(',', array_fill(0, count($ids), '?'));
        $st = $pdo->prepare("SELECT id, name, base_price, area_m2 FROM buildings WHERE id IN ($in)");
        $st->execute($ids);
        foreach ($st->fetchAll() as $row) {
            $buildingRows[(int)$row['id']] = $row;
        }
    }

    $sheetRows = [];
    if ($sheets) {
        $in = implode(',', array_fill(0, count($sheets), '?'));
        $st = $pdo->prepare("SELECT id, name, modifier_type, modifier_value FROM sheets WHERE id IN ($in)");
        $st->execute($sheets);
        foreach ($st->fetchAll() as $row) {
            $sheetRows[(int)$row['id']] = $row;
        }
    }

    $orderedSheets = [];
    foreach ($sheets as $sid) {
        if (isset($sheetRows[$sid])) {
            $orderedSheets[] = $sheetRows[$sid];
        }
    }

    $details = [];
    $total = 0.0;

    foreach ($buildingRows as $b) {
        $price = (float)$b['base_price'];
        $area = (float)$b['area_m2'];
        $applied = [];

        foreach ($orderedSheets as $s) {
            $val = (float)$s['modifier_value'];
            $before = $price;
            switch ($s['modifier_type']) {
                case 'per_m2_add':
                    $price += $val * $area;
                    break;
                case 'flat_add':
                    $price += $val;
                    break;
                case 'percent_add':
                    $price *= (1 + $val / 100);
                    break;
                case 'percent_discount':
                    $price *= (1 - $val / 100);
                    break;
                case 'multiplier':
                    $price *= $val;
                    break;
            }
            $applied[] = [
                'sheet_id' => (int)$s['id'],
                'sheet_name' => $s['name'],
                'delta' => round($price - $before, 2),
            ];
        }

        $price = round($price, 0);
        $details[] = [
            'building_id' => (int)$b['id'],
            'building_name' => $b['name'],
            'base_price' => (float)$b['base_price'],
            'final_price' => $price,
            'applied_sheets' => $applied,
        ];
        $total += $price;
    }

    return [
        'total' => (int)round($total),
        'buildings' => $details,
        'sheets_applied' => array_map(fn($s) => ['id' => (int)$s['id'], 'name' => $s['name']], $orderedSheets),
    ];
}