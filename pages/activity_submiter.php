<?php
$judul = "Yang Sudah Mengerjakan $jenis";
$judul2 = "Yang Belum Ngerjain";



$s = "SELECT id_latihan FROM tb_assign_latihan WHERE id=$id_assign";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)){
  $d = mysqli_fetch_assoc($q);
  $id_latihan = $d['id_latihan'];
}else{
  die(div_alert('danger','Data Assign latihan tidak ada.'));
}

$s = "SELECT 
a.id as id_bukti,
a.*,
d.id as id_peserta,
d.nama as nama_peserta,
d.folder_uploads,
f.kelas

FROM tb_bukti_latihan a 
JOIN tb_assign_latihan b ON a.id_assign_latihan=b.id 
JOIN tb_latihan c ON b.id_latihan=c.id 
JOIN tb_peserta d ON a.id_peserta=d.id 
JOIN tb_kelas_peserta e ON e.id_peserta=d.id 
JOIN tb_kelas f ON e.kelas=f.kelas 
WHERE c.id=$id_latihan 
AND f.tahun_ajar=$tahun_ajar 
AND f.status=1 
AND f.kelas = '$target_kelas'
ORDER BY f.kelas, d.nama 
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$arr_yg_sudah = [];
$divs = '';
while($d=mysqli_fetch_assoc($q)){
  array_push($arr_yg_sudah,$d['id_peserta']);
  $nama_peserta = ucwords(strtolower($d['nama_peserta']));
  $kelas_show = str_replace("~$tahun_ajar",'',$d['kelas']);

  if($d['status']==1){
    $gradasi = 'hijau';
  }elseif($d['status']==-1){
    $gradasi = 'merah';
  }else{
    $gradasi = 'kuning';
  }

  $divs.= "
    <div class='wadah gradasi-$gradasi tengah'>
      <img src='assets/img/peserta/wars/peserta-$d[id_peserta].jpg' class=foto_profil>
      <div class='f14 darkblue'>$nama_peserta</div> 
      <div class='f12 abu'>$kelas_show</div> 
    </div>
  ";
}

echo "
  <div class='wadah gradasi-hijau proper'>
    <h2 class='mt2 mb4 f16 darkblue tebal'>$judul</h2>
    <div class='flexy'>
      $divs
    </div>
  </div>
";

$s = "SELECT 
b.kelas,
c.id as id_peserta,
c.nama as nama_peserta  
FROM tb_kelas_peserta a 
JOIN tb_kelas b ON a.kelas=b.kelas 
JOIN tb_peserta c ON a.id_peserta=c.id 
WHERE b.tahun_ajar=$tahun_ajar 
AND b.status = 1 
AND b.kelas = '$target_kelas' 
ORDER BY b.kelas, c.nama 
";

$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$divs_belum = '';
while($d=mysqli_fetch_assoc($q)){
  if(in_array($d['id_peserta'],$arr_yg_sudah)) continue;
  $nama_peserta = ucwords(strtolower($d['nama_peserta']));
  $kelas_show = str_replace("~$tahun_ajar",'',$d['kelas']);

  $divs_belum.= "
    <div class='wadah gradasi-hijau tengah'>
      <img src='assets/img/peserta/wars/peserta-$d[id_peserta].jpg' class=foto_profil>
      <div class='f14 darkblue'>$nama_peserta</div> 
      <div class='f12 abu'>$kelas_show</div> 
    </div>
  ";
}

echo "
  <div class='wadah gradasi-merah proper'>
    <h2 class='mt2 mb4 f16 darkred tebal'>$judul2</h2>
    <div class='flexy'>
      $divs_belum
    </div>
  </div>
";
