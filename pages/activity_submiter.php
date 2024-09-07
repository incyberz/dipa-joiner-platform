<style>
  .wadah-peserta {
    position: relative;
  }

  .icon-aksi {
    position: absolute;
    right: 15px;
    top: 110px;
    /* box-shadow: 0 0 5px white; */
    background: white;
    border: solid 1px white;
    border-radius: 3px;
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
$judul = "Yang Sudah Mengerjakan $jenis";
$judul2 = "Yang Belum Ngerjain";
$icon_wa_red = "<img src='$lokasi_img/icon/wa_red.png' class=img-icon>";
$icon_wa = "<img src='$lokasi_img/icon/wa.png' class=img-icon>";
$icon_wa_disabled = "<img src='$lokasi_img/icon/wa_disabled.png' class=img-icon>";
$no_data = '<i class="abu f12">--no data--</i>';



$s = "SELECT id_$jenis FROM tb_assign_$jenis WHERE id=$id_assign";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q)) {
  $d = mysqli_fetch_assoc($q);
  $id_jenis = $d["id_$jenis"];
} else {
  die(div_alert('danger', 'Data Assign $jenis tidak ada.'));
}

$s = "SELECT 
a.id as id_bukti,
a.*,
d.id as id_peserta,
d.nama as nama_peserta,
d.folder_uploads,
f.kelas

FROM tb_bukti_$jenis a 
JOIN tb_assign_$jenis b ON a.id_assign_$jenis=b.id 
JOIN tb_$jenis c ON b.id_$jenis=c.id 
JOIN tb_peserta d ON a.id_peserta=d.id 
JOIN tb_kelas_peserta e ON e.id_peserta=d.id 
JOIN tb_kelas f ON e.kelas=f.kelas 
WHERE c.id=$id_jenis 
AND f.tahun_ajar=$ta 
AND f.status=1 
AND f.kelas = '$target_kelas'
ORDER BY f.kelas, d.nama 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$arr_yg_sudah = [];
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

  $divs .= "
    <div class='wadah gradasi-$gradasi tengah'>
      <img src='$lokasi_profil/wars/peserta-$d[id_peserta].jpg' class=foto_profil>
      <div class='f14 darkblue'>$nama_peserta</div> 
      <div class='f12 abu'>$kelas_show</div> 
    </div>
  ";
}

$divs = $divs ?? $no_data;

echo "
  <div class='wadah gradasi-hijau proper'>
    <h2 class='mt2 mb4 f16 darkblue tebal'>$judul</h2>
    <div class='flexy'>
      $divs
    </div>
  </div>
";

# ============================================================
# YANG BELUM NGERJAIN
# ============================================================
$s = "SELECT 
b.kelas,
c.id as id_peserta,
c.nama as nama_peserta  
FROM tb_kelas_peserta a 
JOIN tb_kelas b ON a.kelas=b.kelas 
JOIN tb_peserta c ON a.id_peserta=c.id 
WHERE b.tahun_ajar=$ta 
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

  $divs_belum .= "
    <div class='wadah gradasi-hijau tengah wadah-peserta'>
      <img src='$lokasi_profil/wars/peserta-$d[id_peserta].jpg' class=foto_profil>
      <div class='f14 darkblue'>$nama_peserta</div> 
      <div class='f12 abu'>$kelas_show</div>
      <div class='icon-aksi'>$icon_wa_red</div>
    </div>
  ";
}

$divs_belum = $divs_belum ?? $no_data;

echo "
  <div class='wadah gradasi-merah proper'>
    <h2 class='mt2 mb4 f16 darkred tebal'>$judul2</h2>
    <div class='flexy'>
      $divs_belum
    </div>
  </div>
";
