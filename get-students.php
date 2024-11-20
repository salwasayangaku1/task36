<?php
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=task36', 'root', ''); // Pastikan username dan password sesuai
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$offset = ($page - 1) * $limit;

try {
    $totalQuery = $pdo->query("SELECT COUNT(*) as total FROM students");
    $total = $totalQuery->fetch(PDO::FETCH_ASSOC)['total'];

    $query = $pdo->prepare("SELECT * FROM students LIMIT :limit OFFSET :offset");
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->execute();
    $students = $query->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'data' => $students,
        'total' => $total,
        'page' => $page,
        'limit' => $limit,
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'error' => $e->getMessage(),
    ]);
}
?>
