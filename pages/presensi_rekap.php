<?php
# =================================================================
instruktur_only();
include 'include/date_managements.php';
$img_ontime = img_icon('check');
$img_late = img_icon('check_brown');
$img_reject = img_icon('reject');
$icon_mhs = img_icon('mhs');
$icon_gray = '<span class="br50 pointer" style="display:inline-block;width:20px;height:20px;background:#ccc;">&nbsp;</span>';

$target_kelas_info = $target_kelas ? "Target kelas : $target_kelas" : 'All kelas pada room ini.';

echo "
  <div class='section-title' data-aos-zzz='fade-up'>
    <h2>Rekap Presensi</h2>
    <div>$target_kelas_info</div>
  </div>
";

$get_kelas = $_GET['kelas'] ?? '';
$param_awal = 'presensi_rekap';
include 'navigasi_kelas.php';


# ====================================================
# INITIAL VALUE
# ====================================================
$rid_sesi = [];

# ====================================================
# GET DATA ARRAY SESI
# ====================================================
$s = "SELECT a.*, 
a.id as id_sesi, 
a.nama as nama_sesi,
(
  SELECT 1 FROM tb_sesi WHERE id=a.id 
  AND '$today' > awal_presensi) is_presensi_aktif 
FROM tb_sesi a 
WHERE a.id_room=$id_room";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0){
  echo div_alert('danger', "Belum ada sesi pada room $room.");
}else{
  while($d=mysqli_fetch_assoc($q)){
    $rid_sesi[$d['no']]=$d['id_sesi'];
    $ris_presensi_aktif[$d['id_sesi']]=$d['is_presensi_aktif'];
  }
}


# ====================================================
# GET LIST PESERTA
# ====================================================
$sql_kelas = $target_kelas ? "c.kelas='$target_kelas'" : '1';
if($get_kelas) $sql_kelas = "c.kelas = '$get_kelas'";

$s = "SELECT 
a.id as id_kelas_peserta, 
b.id as id_peserta, 
b.nama as nama_peserta ,
c.kelas,
(SELECT id FROM tb_sesi WHERE id_room=$id_room AND awal_presensi<='$today' AND akhir_presensi>'$today') id_sesi 
FROM tb_kelas_peserta a 
JOIN tb_peserta b ON a.id_peserta=b.id 
JOIN tb_kelas c ON a.kelas=c.kelas  
JOIN tb_room_kelas d ON c.kelas=d.kelas 
WHERE d.id_room=$id_room 
AND b.status=1 
AND $sql_kelas 
AND b.nama NOT LIKE '%DUMMY%' 
ORDER BY c.shift, c.kelas, b.nama 
";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$tr = '';
if(mysqli_num_rows($q)==0){
  echo div_alert('danger', "Belum ada data peserta pada room ini.");
}else{

  $table_tag = "<table class='table mt4'>";
  
  $th_sesi = '';
  foreach ($rid_sesi as $no => $id_sesi) $th_sesi .= "<th>P$no</th>";
  $thead = "
    <thead class='gradasi-toska'>
      <th>No</th>
      <th>Peserta / Kelas</th>
      $th_sesi
    </thead>
  ";

  $tr = '';
  $i = 0;
  $last_kelas = '';
  while($d=mysqli_fetch_assoc($q)){
    $id_kelas_peserta=$d['id_kelas_peserta'];
    $nama = ucwords(strtolower($d['nama_peserta']));

    $s2 = "SELECT id as id_sesi,
    (
      SELECT is_ontime FROM tb_presensi 
      WHERE id_peserta=$d[id_peserta] 
      AND id_sesi=a.id) is_ontime   
    FROM tb_sesi a WHERE a.id_room=$id_room";
    $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));
    $td_presensi = '';
    while($d2=mysqli_fetch_assoc($q2)){
      $icon_hadir = '';
      if($d2['is_ontime']==='1'){
        $icon_hadir = $img_ontime;
      }elseif($d2['is_ontime']==='0'){
        $icon_hadir = $img_late;
      }else{
        if($ris_presensi_aktif[$d2['id_sesi']]){
          $icon_hadir = $img_reject;
        }else{
          $icon_hadir = '-';
        }
      }
      $td_presensi .= "<td>$icon_hadir</td>";
    }


    # ==============================================================
    # FINAL OUTPUT :: SHOW IMAGE OR COMPACT
    # ==============================================================
    if($last_kelas!=$d['kelas']){
      $tr.= "</table>$table_tag$thead";
      $i=0;
    }

    $path = "assets/img/peserta/wars/peserta-$d[id_peserta].jpg";
    $path_na = 'assets/img/no_profil.jpg';
    if(file_exists($path)){
      $src_profile = $path;
      $icon = $icon_mhs;
      $poin = 1000;
    }else{
      $src_profile = $path_na;
      $icon = $icon_gray;
      $poin = 200;
    }

    $id[-2] = "set_absen__$d[id_peserta]__$d[id_sesi]__0__-2";
    $id[-1] = "set_absen__$d[id_peserta]__$d[id_sesi]__0__-1";
    $id[0] = "set_absen__$d[id_peserta]__$d[id_sesi]__0__0";
    $id[1] = "set_absen__$d[id_peserta]__$d[id_sesi]__1000__1";
    $id[2] = "set_absen__$d[id_peserta]__$d[id_sesi]__1000__2";

    $blok_sia = "
      <div class=flexy style=gap:2px>
        <button class='consolas btn btn-warning btn-sm set_absen' id=$id[-2]>I</button>
        <button class='consolas btn btn-warning btn-sm set_absen' id=$id[-1]>S</button>
        <button class='consolas btn btn-danger btn-sm set_absen' id=$id[0]>A</button>
        <button class='consolas btn btn-success btn-sm set_absen' id=$id[1]>H</button>
        <button class='consolas btn btn-info btn-sm set_absen' id=$id[2]>D</button>
      </div>
    ";

    $i++;
    $tr.= "
      <tr>
        <td>$i</td>
        <td>
          $nama <span class=btn_aksi id=profil_peserta$d[id_peserta]__toggle>$icon</span>
          <div class='f12 abu'>$d[kelas]</div>
          <div class='hideit ' id=profil_peserta$d[id_peserta]>
            <img src='$src_profile' class=foto_profil>
            $blok_sia
          </div>
        </td>
        $td_presensi
      </tr>
    ";


    $last_kelas = $d['kelas'];

  } // end while list peserta

  echo "
      $tr
    </table>
  ";
}









?>
<script>
  $(function(){
    $('.set_absen').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id_peserta = rid[1];
      let id_sesi = rid[2];
      let poin = rid[3];
      let kode_absen = rid[4];
      
      let link_ajax = `ajax/ajax_set_presensi_offline.php?id_peserta=${id_peserta}&id_sesi=${id_sesi}&poin=${poin}&kode_absen=${kode_absen}`;
      console.log(link_ajax);
      $.ajax({
        url:link_ajax,
        success:function(a){
          if(a.trim()=='sukses'){
            alert('sukses');
          }else{
            alert(a);
          }
        }
      })
    })
  })
</script>