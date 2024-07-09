<?php
if (!$username) jsurl('?');
$id_sesi = $_GET['id_sesi'] ?? '';
$no_sesi = $_GET['no_sesi'] ?? '';
$nama_sesi = $_GET['nama_sesi'] ?? '';
$part = $_GET['part'] ?? '';
$nama_sesi_show = "P$no_sesi - $nama_sesi";

$arr_part = [
  'urutan_sesi' => [
    'title' => 'Urutan Sesi',
    'desc' => 'Mengurutkan sesi-sesi normal, minggu tenang, dan pekan ujian',
    'image' => 'learning_path.png',
  ],
  'deskripsi' => [
    'title' => 'Nama, dan Deskripsi',
    'desc' => 'Editing Nama Sesi dan Deskripsi Singkat tentang sesi tersebut',
    'image' => 'learning_path.png',
  ],
  'tags' => [
    'title' => 'Tag-tag Materi',
    'desc' => 'Memberikan arahan kepada Peserta Didik agar dapat Tanam Soal dan Bertanya sesuai tag-tag materi yang Anda siapkan',
    'image' => 'latihan.png',
  ],
  'bahan_ajar' => [
    'title' => 'Bahan Ajar',
    'desc' => 'Upload materi PDF (ebook) atau dokumen lainnya untuk Peserta',
    'image' => 'latihan.png',
  ],
  'file_presentasi' => [
    'title' => 'File Presentasi',
    'desc' => 'Upload File Presentasi (PPTX) atau dokumen lainnya untuk Presentasi Tatap Muka/Maya',
    'image' => 'challenge.png',
  ],
  'video_ajar' => [
    'title' => 'Video Ajar',
    'desc' => 'Tambahkan link Video Ajar (Youtube) atau link video lainnya bagi Peserta',
    'image' => 'project.png',
  ],
  'awal_presensi' => [
    'title' => 'Rule Presensi',
    'desc' => 'Editing Rule (Aturan) kapan peserta dapat Presensi Online pada sesi ini',
    'image' => 'project.png',
  ],
  'durasi' => [
    'title' => 'Durasi Tatap Muka',
    'desc' => 'Rule (aturan) untuk kegiatan Pembelajaran Tatap Muka',
    'image' => 'challenge.png',
  ],
];

if ($id_sesi) {
  # ============================================================
  # PROPS SESI
  # ============================================================
  $s = "SELECT a.*, 
  a.id as id_sesi,
  a.nama as nama_sesi
  FROM tb_sesi a WHERE a.id=$id_sesi";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $d_sesi = mysqli_fetch_assoc($q);
  # ============================================================
  # MANAGE SESI
  # ============================================================
  include 'list_sesi-manage_sesi.php';
} else {
  # ============================================================
  # SHOW LIST SESI
  # ============================================================
  include 'list_sesi-show_list_sesi.php';
}
