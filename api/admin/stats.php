<?php
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../config/helpers.php';

require_admin();

$stats = [
    'users_total'        => (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'users_curators'     => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'curator'")->fetchColumn(),
    'users_admins'       => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn(),
    'projects_total'     => (int)$pdo->query('SELECT COUNT(*) FROM portfolio_projects')->fetchColumn(),
    'calculations_total' => (int)$pdo->query('SELECT COUNT(*) FROM saved_calculations')->fetchColumn(),
];

// receipts пока может не быть — безопасная проверка
$hasReceipts = (bool)$pdo->query("SHOW TABLES LIKE 'receipts'")->fetch();
$stats['receipts_total'] = $hasReceipts
    ? (int)$pdo->query('SELECT COUNT(*) FROM receipts')->fetchColumn()
    : 0;

json_response(['ok' => true, 'data' => $stats]);