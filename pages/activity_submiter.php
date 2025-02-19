<style>
  .wadah-peserta {
    position: relative;
  }



  .icon-aksi {
    position: absolute;
    border-radius: 3px;
  }

  .icon-aksi-wa {
    border: solid 1px white;
    right: 15px;
    top: 110px;
  }

  .icon-aksi-absen {
    left: 15px;
    top: 110px;
    border: none;
  }

  .img-icon {
    height: 30px;
    transition: .2s;
    cursor: pointer;
  }

  .img-icon:hover {
    transform: scale(1.2);
  }
</style>
<?php
if (isset($_POST['btn_absen'])) {
  $t = explode('-', $_POST['btn_absen']);
  //sakit-$d[id_peserta]-$id_sesi-1
  // 0    1               2       3
  $s = "INSERT INTO tb_absen (
  id,
  id_peserta, 
  id_sesi, 
  absen
  ) VALUES (
  '$t[2]-$t[1]', -- id_sesi-id_peserta 
  $t[1], 
  $t[2], 
  '-$t[3]'
  ) ON DUPLICATE KEY UPDATE absen = '-$t[3]', tanggal = CURRENT_TIMESTAMP  
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
};

$icon_wa_red = "<img src='$lokasi_img/icon/wa_red.png' class=img-icon>";
$img_warning = "<img src='$lokasi_img/icon/warning.png' class=img-icon>";
$icon_wa = "<img src='$lokasi_img/icon/wa.png' class=img-icon>";
$icon_wa_disabled = "<img src='$lokasi_img/icon/wa_disabled.png' class=img-icon>";
$no_data = '<i class="abu f12">--no data--</i>';
$rabsen = [
  -1 => [
    'ket' => 'Sakit',
    'bg' => 'info',
  ],
  -2 => [
    'ket' => 'Izin',
    'bg' => 'warning',
  ],
  -9 => [
    'ket' => 'Alfa',
    'bg' => 'danger',
  ]
];


# ============================================================
# PROPERTI LATIHAN/CHALLENGE
# ============================================================
$s = "SELECT id_$jenis FROM tb_assign_$jenis WHERE id=$id_assign";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q)) {
  $d = mysqli_fetch_assoc($q);
  $id_jenis = $d["id_$jenis"];
} else {
  die(div_alert('danger', 'Data Assign $jenis tidak ada.'));
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
AND f.ta=$ta 
AND f.status=1 
AND f.kelas = '$target_kelas' 

-- ======================================
-- ORDER BY POINT DESC AND TANGGAL UPLOAD
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
  $kelas_show = str_replace("~$ta", '', $d['kelas']);

  if ($d['status'] == 1) {
    $gradasi = 'hijau';
  } elseif ($d['status'] == -1) {
    $gradasi = 'merah';
  } else {
    $gradasi = 'kuning';
  }

  $war_image = $d['war_image'] ?? $d['image'];
  $src = "$lokasi_profil/$war_image";
  $src = cek_src_profil($d['image'], $d['war_image'], $lokasi_profil);

  // ubah ribuan menjadi k
  if ($d['get_point'] >= 1000000) {
    $get_point = number_format(round($d['get_point'] / 1000000, 1)) . 'M';
  } elseif ($d['get_point'] >= 1000) {
    $get_point = number_format(round($d['get_point'] / 1000, 1)) . 'k';
  } else {
    $get_point = number_format($d['get_point']);
  }

  $divs .= "
    <div class='wadah gradasi-$gradasi tengah'>
      <img src='$src' class=foto_profil>
      <div class='f14 darkblue'>$nama_peserta</div> 
      <div class='f12 abu'>$kelas_show</div> 
      <div>
        <a href='?verif&keyword=$d[nama_peserta]&show_img=1' target=_blank>
          $get_point LP $img_next
        </a>
      </div>  
    </div>
  ";
}

$divs = $divs ?? $no_data;

# ============================================================
# SUDAH MENGERJAKAN
# ============================================================
echo "
  <div class='wadah gradasi-hijau proper'>
    <h2 class='mt2 mb4 f16 darkblue tebal'>Yang Sudah Mengerjakan</h2>
    <div class='flexy'>
      $divs
    </div>

    <button class='btn btn-primary'>Masukan ke Presensi Offline</button>
    <button class='btn btn-info'>Masukan ke Presensi Online</button>
  </div>
";

# ============================================================
# YANG BELUM NGERJAIN
# ============================================================
$s = "SELECT 
b.kelas,
c.id as id_peserta,
c.nama as nama_peserta,
c.image,
c.war_image,
(SELECT absen FROM tb_absen WHERE id=CONCAT('$id_sesi-',c.id)) absen -- -1:sakit -2:izin -9:alfa


FROM tb_kelas_peserta a 
JOIN tb_kelas b ON a.kelas=b.kelas 
JOIN tb_peserta c ON a.id_peserta=c.id 
WHERE b.ta=$ta 
AND b.status = 1 
AND b.kelas = '$target_kelas' 
ORDER BY b.kelas, c.nama 
";

$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$divs_belum = null;
while ($d = mysqli_fetch_assoc($q)) {
  if (in_array($d['id_peserta'], $arr_yg_sudah)) continue;
  $nama_peserta = ucwords(strtolower($d['nama_peserta']));
  $kelas_show = str_replace("~$ta", '', $d['kelas']);

  $war_image = $d['war_image'] ?? $d['image'];
  $src = "$lokasi_profil/$war_image";

  # ============================================================
  # DIV PESERTA YANG BELUM NGERJAIN
  # -1:sakit -2:izin -9:alfa
  # ============================================================
  $bg = $rabsen[$d['absen']]['bg'] ?? '';
  $ket = $rabsen[$d['absen']]['ket'] ?? '';
  $badge_absen = '';
  if ($bg and $ket) {
    $badge_absen = "
      <div> 
        <span class='btn btn-$bg w-100 btn-sm' onclick='alert(`$Peserta ini tercatat [$ket].`)'>$ket</span> 
      </div>
    ";
  }
  $divs_belum .= "
    <div class='wadah gradasi-hijau tengah wadah-peserta'>
      $badge_absen
      <img src='$src' class=foto_profil>
      <div class='f14 darkblue'>$nama_peserta</div> 
      <div class='f12 abu'>$kelas_show</div>
      <div class='icon-aksi icon-aksi-wa'>$icon_wa_red</div>
      <div class='icon-aksi icon-aksi-absen'>
        <span class='btn_aksi' id=form_absen$d[id_peserta]__toggle>$img_warning</span>
      </div>
      <form method=post id=form_absen$d[id_peserta] class='hideit'>
        <div class='wadah mt1'>
          <div class='f14 darkblue'>Absen Offline</div>
          <span class='btn btn-sm btn-success w-100 mb1' onclick='alert(`Dispen pada LMS ini secara default artinya $Peserta boleh Presensi Online dari mana saja.\n\nJadi biarkan saja mhs nya yang melakukan Presensi.`)'>Dispen</span>
          <button class='btn btn-sm btn-info w-100 mb1' name=btn_absen value='sakit-$d[id_peserta]-$id_sesi-1'>Sakit</button>
          <button class='btn btn-sm btn-warning w-100 mb1' name=btn_absen value='izin-$d[id_peserta]-$id_sesi-2'>Izin</button>
          <button class='btn btn-sm btn-danger w-100 mb1' name=btn_absen value='alfa-$d[id_peserta]-$id_sesi-9'>Alfa</button>
        </div>
      </form>


    </div>
  ";
}

$divs_belum = $divs_belum ?? $no_data;

# ============================================================
# BELUM NGERJAIN
# ============================================================
echo "
  <div class='wadah gradasi-merah proper'>
    <h2 class='mt2 mb4 f16 darkred tebal'>Yang Belum Ngerjain</h2>
    <div class='flexy'>
      $divs_belum
    </div>
  </div>
";
