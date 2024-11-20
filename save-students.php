<?php
// Mengambil data JSON dari request
$body = file_get_contents('php://input');
$request = json_decode($body, true);

// Koneksi ke database
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=task36', 'root', ''); // Pastikan username dan password sesuai
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Validasi jika data yang diterima tidak null
if (!isset($request['nik']) || !isset($request['name'])) {
    echo json_encode([
        'status' => false,
        'error' => 'NIK dan Nama harus diisi.'
    ]);
    exit();
}

$query = $pdo->prepare("INSERT INTO students (nik, nama) VALUES (:nik, :name)");
$query->bindValue(':nik', $request['nik'], PDO::PARAM_STR);
$query->bindValue(':name', $request['name'], PDO::PARAM_STR);

try {
    // Eksekusi query
    $result = $query->execute();
    $id = $pdo->lastInsertId(); // Mendapatkan ID terakhir yang diinsert
    
    if ($result) {
        // Mengambil data yang baru dimasukkan untuk dikirim kembali sebagai respons
        $query = $pdo->prepare("SELECT * FROM students WHERE id = :id");
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $student = $query->fetch(PDO::FETCH_ASSOC);
        
        // Mengirimkan respons JSON sukses
        echo json_encode([
            'status' => true,
            'student' => $student
        ]);
    }
} catch (Exception $e) {
    // Menangani kesalahan dan mengirimkan pesan kesalahan
    echo json_encode([
        'status' => false,
        'error' => $e->getMessage()
    ]);
}
?>
