<?php
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../config/helpers.php';

require_admin();

$role = $_GET['role'] ?? '';
$q    = trim($_GET['q'] ?? '');

$where = [];
$params = [];

if (in_array($role, ['user', 'curator', 'admin'], true)) {
    $where[] = 'role = ?';
    $params[] = $role;
}
if ($q !== '') {
    $where[] = '(login LIKE ? OR full_name LIKE ? OR email LIKE ?)';
    $like = '%' . $q . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

$sql = 'SELECT id, login, full_name, email, role, is_root_admin, created_at FROM users';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY id ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

foreach ($rows as &$r) {
    $r['id'] = (int)$r['id'];
    $r['is_root_admin'] = (int)$r['is_root_admin'];
}
unset($r);

json_response(['ok' => true, 'data' => ['users' => $rows]]);