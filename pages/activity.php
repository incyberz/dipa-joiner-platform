<?php
if (!$username) jsurl('?');
if (!$status_room) die("<section><div class=container>$div_alert_closed</div></section>");
$jenis = $_GET['jenis'] ?? '';
$id_assign = $_GET['id_assign'] ?? '';

// bukti latihan
$target_bukti = "uploads/$folder_uploads/$jenis-$id_assign.jpg";

if ($jenis == '') {
  $rjenis = ['latihan', 'challenge'];
  $j = '';
  foreach ($rjenis as $key => $value) $j .= "<a href='?activity&jenis=$value data-aos='fade-up'' class='proper btn btn-info mb2'>$value</a> ";
  echo "<section><div class=container><div data-aos='fade-up'><p>Silahkan pilih jenis aktivitas:</p>$j</div></div></section>";
  exit;
}

$ryaitu = [
  'latihan' => 'Yaitu praktikum yang persis dicontohkan oleh instruktur atau materi yang sudah disampaikan. Kamu wajib mengerjakannya.',
  'challenge' => 'Yaitu pembuktian bahwa kamu sudah siap terjun ke Dunia Usaha dan Industri (DUDI). Kamu wajib membangun salah satu portfolio system yang berhasil kamu buat.'
];
$yaitu = $ryaitu[$jenis];
$pesan_upload = null;
$closed = 0;
$Jenis = ucwords($jenis);

set_h2(
  $Jenis,
  "
    $yaitu
    <div class=mt2>
      <a href='?activity&jenis=$jenis'>$img_prev</a>
    </div>
  "
);

include 'activity_manage_processor.php';

$cara_pengumpulan_default = $jenis == 'latihan'
  ? "Kerjakan latihan di Buku Catatan kalian atau di komputer/HP, kemudian foto/screenshot, lalu upload di latihan ini"
  : "Kerjakan Challenge sesuai dengan Sub Level Challenge yang kalian pilih, khusus untuk MK Pemrograman Web maka wajib dihostingkan, untuk Challenge Video maka wajib diupload ke Youtube, dan untuk Room lainnya upload ke GDrive semua hasil pekerjaan. Dapatkan link-nya dan paste-kan link-nya di Challenge ini agar instruktur dapat memeriksanya via online";


# ============================================
# NORMAL FLOW
# ============================================
if (!$id_assign) {

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
  echo $list ? "<div class='wadah gradasi-kuning'>List $jenis yang belum bisa dikerjakan: <ol>$list</ol><div class='f12 biru miring'>Hubungi instruktur agar $jenis ini di-assign ke kelas kamu.</div></div>" : '';

  $s = "SELECT a.id as id_assign, 
  a.is_wajib,
  b.nama,
  b.status as status_jenis,
  (b.basic_point + b.ontime_point) as sum_point,
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
  -- WHERE no is not null 
  WHERE 1  
  AND id_room_kelas='$id_room_kelas'
  order by c.no, sum_point";
  // echo "<pre>$s</pre>";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (!mysqli_num_rows($q)) {
    if ($count_jenis) {
      echo div_alert('danger', "Terdapat $count_jenis $jenis yang belum di-assign oleh instruktur untuk kelas $kelas");
    } else {
      echo div_alert('danger', "Maaf, belum ada satupun $jenis pada room $room. Beritahukan hal ini kepada instruktur!");
    }
  } else {
    $list_jenis = '';
    while ($d = mysqli_fetch_assoc($q)) {
      $sum_point = number_format($d['sum_point'], 0);
      if ($d['status_jenis'] == -1) {
        $closed = 1;
        $primary = 'secondary';
        $is_wajib_icon = "<span class='f12 red'>Closed</span>";
      } else {
        $primary = $d['sudah_mengerjakan'] ? 'warning' : 'primary';
        $primary = $d['status_mengerjakan'] ? 'success' : $primary;
        $is_wajib_icon = $d['is_wajib'] ? '<b class="consolas f12 gradasi-kuning red bordered br10" style="padding: 5px 10px">Wajib</b>' : '';
      }

      $list_jenis .= "
        <div>
          <a class='btn btn-$primary btn-sm mb2' href='?activity&jenis=$jenis&id_assign=$d[id_assign]'>
            P$d[no] 
            ~ 
            $d[nama]
            ~ 
            $sum_point
          </a> $is_wajib_icon
        </div>
      ";
    }
    echo "
      Silahkan pilih $jenis yang dapat kamu kerjakan:
      <div class=wadah>
        $list_jenis
      </div>
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
    include 'activity_assign.php';
  }
}

echo "<div class=debug>
  jenis: <span id=jenis>$jenis</span>
  id_assign: <span id=id_assign>$id_assign</span>
</div>";
