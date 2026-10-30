<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$userId = require_auth();

$title = trim($_POST['title'] ?? 'Расчёт от ' . date('d.m.Y'));
$typeName = trim($_POST['type_name'] ?? '');
$regionName = trim($_POST['region_name'] ?? '');
$floors = (int)($_POST['floors'] ?? 1);
$totalArea = (float)($_POST['total_area'] ?? 0);
$totalPrice = (float)($_POST['total_price'] ?? 0);
$loanName = trim($_POST['loan_name'] ?? '');
$monthlyPayment = (float)($_POST['monthly_payment'] ?? 0);

if (!$typeName || !$regionName) {
    json_response(['ok' => false, 'error' => 'Недостаточно данных'], 400);
}

$stmt = $pdo->prepare('
    INSERT INTO saved_calculations 
    (user_id, title, type_name, region_name, floors, total_area, total_price, loan_name, monthly_payment) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
');
$stmt->execute([$userId, $title, $typeName, $regionName, $floors, $totalArea, $totalPrice, $loanName, $monthlyPayment]);

json_response(['ok' => true, 'id' => $pdo->lastInsertId()]);