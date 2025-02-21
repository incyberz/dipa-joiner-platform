<?php
# ============================================================
# PROPERTI LATIHAN/CHALLENGE
# ============================================================
$s = "SELECT id_$jenis FROM tb_assign_$jenis WHERE id=$id_assign";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q)) {
  $d = mysqli_fetch_assoc($q);
  $id_jenis = $d["id_$jenis"];
} else {
  die(div_alert('danger', "Data Assign $jenis tidak ada."));
}

# ============================================================
# MAIN SELECT PESERTA KELAS
# ============================================================
$sql_image_bukti = $jenis == 'latihan' ? "a.image as image_bukti" : "('') as image_bukti";
$s = "SELECT 
a.id as id_bukti,
a.*,
$sql_image_bukti,
d.id as id_peserta,
d.nama as nama_peserta,
d.folder_uploads,
d.username,
d.image,
d.war_image,
f.kelas

FROM tb_bukti_$jenis a 
JOIN tb_assign_$jenis b ON a.id_assign_$jenis=b.id 
JOIN tb_$jenis c ON b.id_$jenis=c.id 
JOIN tb_peserta d ON a.id_peserta=d.id 
JOIN tb_kelas_peserta e ON e.id_peserta=d.id 
JOIN tb_kelas f ON e.kelas=f.kelas 
WHERE c.id=$id_jenis 
AND f.ta=$ta_aktif 
AND f.status=1 -- kelas aktif 
AND a.status = 1 -- hanya yang sudah di acc
AND f.kelas = '$kelas' -- teman kelas sendiri 

-- ======================================
-- ORDER BY NAMA KAWAN DG POIN TERBAIK
-- ======================================
ORDER BY f.kelas, a.get_point DESC, a.tanggal_upload, d.nama 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$arr_yg_sudah = [];
$img_next = img_icon('next');
$divs = null;
while ($d = mysqli_fetch_assoc($q)) {
  array_push($arr_yg_sudah, $d['id_peserta']);
  $nama_peserta = ucwords(strtolower($d['nama_peserta']));


  // ubah ribuan menjadi k
  if ($d['get_point'] >= 1000000) {
    $get_point = number_format(round($d['get_point'] / 1000000, 1)) . 'M';
  } elseif ($d['get_point'] >= 1000) {
    $get_point = number_format(round($d['get_point'] / 1000, 1)) . 'k';
  } else {
    $get_point = number_format($d['get_point']);
  }

  $href = $jenis == 'latihan' ? "uploads/$d[folder_uploads]/$d[image_bukti]" : $d['link'];

  $divs .= "
      <a class='d-block bordered p1 br10 pl2 pr2 gradasi-toska' href='$href' target=_blank>
        $d[username] - $get_point 
      </a>
  ";
}


# ============================================================
# LIHAT PUNYA TEMAN
# ============================================================
$lihat_punya_teman = !$divs ? '' : "
  <div class='border-top pt2 lower mt4 f12'>
    <h2 class='left mb2 f16 darkblue'>Lihat punya teman:</h2>
    <div class='flexy'>
      $divs
    </div>
  </div>
";
