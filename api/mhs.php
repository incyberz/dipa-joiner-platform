<?php
// Konfigurasi koneksi database
include '../conn.php';

$id_room = $_GET['id_room'] ?? 27; // id 27 React Native
$prodi = $_GET['prodi'] ?? 'SI';


$host = "localhost";
$username = "root";
$password = "";
$database = "db_universitas";

$host = $db_server;
$username = $db_user;
$password = $db_pass;
$database = $db_name;

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// Mengatur header response
header("Content-Type: application/json");

// Query untuk mengambil data mahasiswa dari prodi SI dan kelas SI-A atau SI-B dengan limit 10 baris secara random
$sql = "SELECT CONCAT(1,a.id) as id, '-' as nim, a.nama, c.kelas,
(SELECT akumulasi_poin FROM tb_poin WHERE id_peserta=a.id AND id_room=$id_room) points 
FROM tb_peserta a 
JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
JOIN tb_kelas c ON b.kelas=c.kelas 
JOIN tb_room_kelas d ON c.kelas=d.kelas 
WHERE id_room = $id_room 
AND a.id_role = 1 
AND a.status = 1 
AND c.kelas LIKE '%$prodi%'
ORDER BY RAND() LIMIT 10";
$result = $conn->query($sql);

// Inisialisasi array untuk menyimpan hasil
$data = [];

if ($result->num_rows > 0) {
  // Mengambil setiap baris data
  while ($row = $result->fetch_assoc()) {
    $data[] = $row;
  }

  // Mengembalikan data dalam format JSON
  echo json_encode([
    "status" => "success",
    "data" => $data
  ], JSON_PRETTY_PRINT);
} else {
  // Jika tidak ada data
  echo json_encode([
    "status" => "error",
    "message" => "Tidak ada data mahasiswa yang ditemukan"
  ], JSON_PRETTY_PRINT);
}

// Menutup koneksi database
$conn->close();
