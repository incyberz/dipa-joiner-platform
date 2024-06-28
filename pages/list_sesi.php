<?php
if (!$username) jsurl('?');
$id_sesi = $_GET['id_sesi'] ?? '';
$no_sesi = $_GET['no_sesi'] ?? '';
$nama_sesi = $_GET['nama_sesi'] ?? '';
$part = $_GET['part'] ?? '';

if ($id_sesi and !$part) {
  echo div_alert('info tengah', "
    <h4 class=mb2>P$no_sesi - $nama_sesi</h4>
    <a href=?list_sesi>$img_prev</a>
    <hr>
    Mana yang ingin Anda atur ?
  ");
  $arr_part = [
    'deskripsi' => [
      'title' => 'Nama dan Deskripsi',
      'desc' => 'Editing Nama Sesi dan Deskripsi Singkat tentang sesi tersebut',
    ],
    'tags' => [
      'title' => 'Tag-tag Materi',
      'desc' => 'Memberikan arahan kepada Peserta Didik agar dapat Tanam Soal dan Bertanya sesuai tag-tag materi yang Anda siapkan',
    ],
    'awal_presensi' => [
      'title' => 'Rule Presensi',
      'desc' => 'Editing Rule (Aturan) kapan peserta dapat Presensi Online pada sesi ini',
    ],
    'durasi' => [
      'title' => 'Durasi Tatap Muka',
      'desc' => 'Rule (aturan) untuk kegiatan Pembelajaran Tatap Muka',
    ],
    'kelengkapan' => [
      'title' => 'Kelengkapan Sesi',
      'desc' => 'Upload materi PDF (ebook), PPT, Video Ajar, dan kelengkapan lainnya',
    ],
  ];

  $col = '';
  foreach ($arr_part as $part => $arr_value) {
    $col .= "
      <div class='col-md-4 col-lg-3 col-xl-2'>
        <a class='btn btn-success w-100 mb1' href='?list_sesi&id_sesi=$id_sesi&part=$part'>
          $arr_value[title]
        </a>
        <div class='f12 abu tengah mb4'>$arr_value[desc]</div>
      </div>
    ";
  }

  echo "<div class=row>$col</div>";
} else {

  # ============================================================
  # SHOW LIST SESI
  # ============================================================
  include 'list_sesi-show_list_sesi.php';
}
