<?php
require_once __DIR__ . '/../config/database.php';
$q = $_GET['q'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$pageSize = max(1, min(20, intval($_GET['pageSize'] ?? 10)));
$offset = ($page - 1) * $pageSize;
$params = [];
$where = "WHERE role='teacher'";
if (strlen($q) > 0) {
    $where .= " AND (name LIKE ? OR username LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
}
// 查询总数
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users $where");
$stmt->execute($params);
$total = $stmt->fetchColumn();
// 查询当前页
$stmt = $pdo->prepare("SELECT id, name, username FROM users $where LIMIT $pageSize OFFSET $offset");
$stmt->execute($params);
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode([
    'data' => $teachers,
    'total' => intval($total),
    'page' => $page,
    'pageSize' => $pageSize
]); 