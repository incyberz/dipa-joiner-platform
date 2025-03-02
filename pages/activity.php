<?php
if (!$username) jsurl('?');
if (!$status_room) die("<section><div class=container>$div_alert_closed</div></section>");
$jenis = $_GET['jenis'] ?? '';
$id_assign = $_GET['id_assign'] ?? '';


if ($jenis == '') {
  $rjenis = ['latihan', 'challenge'];
  $j = '';
  foreach ($rjenis as $key => $value) $j .= "<a href='?activity&jenis=$value data-aos='fade-up'' class='proper btn btn-info mb2'>$value</a> ";
  echo "<section><div class=container><div data-aos='fade-up'><p>Silahkan pilih jenis aktivitas:</p>$j</div></div></section>";
  exit;
}

$ryaitu = [
  'latihan' => "Yaitu praktikum yang persis dicontohkan oleh $Trainer atau materi yang sudah disampaikan. Kamu wajib mengerjakannya.",
  'challenge' => 'Yaitu pembuktian bahwa kamu sudah siap terjun ke Dunia Usaha dan Industri (DUDI). Kamu wajib membangun salah satu portfolio system yang berhasil kamu buat.'
];
$yaitu = $id_role == 2 ? "<span class=proper>$jenis</span> untuk kelas <b>$target_kelas_show</b>" : $ryaitu[$jenis];
$pesan_upload = null;
$closed = 0;
$Jenis = ucwords($jenis);
$cara_pengumpulan_default = $jenis == 'latihan'
  ? "Kerjakan latihan di Buku Catatan kalian atau di komputer/HP, kemudian foto/screenshot, lalu upload di latihan ini"
  : "Kerjakan Challenge sesuai dengan Sub Level Challenge yang kalian pilih, khusus untuk MK Pemrograman Web maka wajib dihostingkan, untuk Challenge Video maka wajib diupload ke Youtube, dan untuk $Room lainnya upload ke GDrive semua hasil pekerjaan. Dapatkan link-nya dan paste-kan link-nya di Challenge ini agar $Trainer dapat memeriksanya via online";

set_h2(
  $Jenis,
  "
    $yaitu
    <div class=mt2>
      <a href='?activity&jenis=$jenis'>$img_prev</a>
    </div>
  "
);

include 'activity_manage-processor.php';



# ============================================
# NORMAL FLOW :: LIST ALL LATIHAN/CHALLENGE
# ============================================
if (!$id_assign) {
  /*
  $s = "SELECT a.nama,
  (
    SELECT 1 FROM tb_assign_$jenis
    WHERE id_$jenis=a.id 
    AND id_room_kelas=$id_room_kelas) assigned 
  FROM tb_$jenis a 
  WHERE a.id_room=$id_room";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $count_jenis = mysqli_num_rows($q);

  $list = '';
  while ($d = mysqli_fetch_assoc($q)) {
    if ($d['assigned']) continue;
    $list .= "<li>$d[nama]</li>";
  }
  echo $list ? "<div class='wadah gradasi-kuning'>List $jenis yang belum bisa dikerjakan: <ol>$list</ol><div class='f12 biru miring'>Hubungi $Trainer agar $jenis ini di-assign ke kelas kamu.</div></div>" : '';
  */

  $sql_target_kelas = $id_role == 2 ?
    "AND d.kelas = '$target_kelas' -- dosen fokus ke target kelas" :
    "AND id_room_kelas='$id_room_kelas' -- kelas sendiri, mhs ponly";

  $s = "SELECT a.id as id_assign, 
  a.is_wajib,
  b.nama,
  b.status as status_jenis,
  -- (b.basic_point + b.ontime_point) as sum_point,
  c.no, 
  (
    SELECT 1 FROM tb_bukti_$jenis 
    WHERE id_assign_$jenis=a.id 
    AND id_peserta=$id_peserta) sudah_mengerjakan,   
  (
    SELECT status FROM tb_bukti_$jenis 
    WHERE id_assign_$jenis=a.id 
    AND id_peserta=$id_peserta) status_mengerjakan   
  FROM tb_assign_$jenis a 
  JOIN tb_$jenis b ON a.id_$jenis=b.id 
  JOIN tb_sesi c ON a.id_sesi=c.id 
  JOIN tb_room_kelas d ON a.id_room_kelas=d.id 
  -- WHERE no is not null 
  WHERE 1  
  $sql_target_kelas
  order by c.no";
  // echo "<pre>$s</pre>";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (!mysqli_num_rows($q)) {
    if ($id_role == 2 and !$target_kelas) {
      echo div_alert('danger', "Anda belum memilih Target kelas. Klik pada tombol kelas di kiri-atas.");
    } else {
      echo div_alert('danger', "Maaf, belum ada satupun $jenis pada $Room ini. Beritahukan hal ini kepada $Trainer!");
    }
  } else {
    $list_jenis = '';
    $rlist_jenis = [];
    while ($d = mysqli_fetch_assoc($q)) {
      // $sum_point = number_format($d['sum_point'], 0);

      if ($d['status_jenis'] == -1) {
        $closed = 1;
        $primary = 'secondary';
        $nama_jenis = "$d[nama] ( <b class=red>CLOSED</b> )";
      } else {
        $primary = $d['sudah_mengerjakan'] ? 'warning' : 'primary';
        $primary = $d['status_mengerjakan'] ? 'success' : $primary;
        $nama_jenis = $d['is_wajib'] ? "<b style=color:#ff0>$d[nama] (WAJIB)</b>" : "$d[nama]";
      }

      $div = "
        <div>
          <a class='btn btn-$primary btn-sm mb2 w-100' href='?activity&jenis=$jenis&id_assign=$d[id_assign]'>
            $nama_jenis
          </a> 
        </div>
      ";
      if (isset($rlist_jenis[$d['no']])) {
        $rlist_jenis[$d['no']] .= $div;
      } else {
        $rlist_jenis[$d['no']] = $div;
      }
    }

    $divs_col = '';
    foreach ($rlist_jenis as $no_sesi => $divs) {
      $divs_col .= "
        <div class='col-lg-4 col-xl-3 col-md-6'>
          <div class=wadah>
            <div class='tengah f24 mb2'>P$no_sesi</div>
            $divs
          </div>
        </div>
      ";
    }

    $info = $id_role == 1 ? "<div class='tengah mb2'><span class=proper>$jenis</span> yang dapat kamu kerjakan:</span></div>" : '';

    echo "
      $info
      <div class=row>$divs_col</div>
      <div class='kecil miring'>
        <span class=hijau>hijau: sudah dikerjakan</span>; <span class=kuning>kuning: belum diverifikasi</span>; <span class=biru>biru: belum kamu kerjakan</span>
      </div>
    ";
  }
} else {
  include 'activity_detail.php';
}


if ($id_role == 2) {
  if (!$id_assign) {
    include 'activity_manage-assign_latihan_or_challenge.php';
  }
}

echo "<div class=hideit>
  jenis: <span id=jenis>$jenis</span>
  id_assign: <span id=id_assign>$id_assign</span>
</div>";
