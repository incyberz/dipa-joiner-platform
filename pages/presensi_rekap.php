
  <style>
    .border_blue{border: solid 3px blue;}
    .border_red{border: solid 1px #f55;}
    .border_green{border: solid 1px #5f5;}
  </style>
<?php
# =================================================================
instruktur_only();
include 'include/date_managements.php';

$show_img = $_GET['show_img'] ?? 0;
$menu1 = $show_img ? '<a href="?presensi_rekap">Hide Profile</a>' : '<a href="?presensi_rekap&show_img=1">Show Profile Peserta</a>';
echo "
  <div class='section-title' data-aos-zzz='fade-up'>
    <h2>Rekap Presensi</h2>
    <div>$menu1</div>
  </div>
";


# ====================================================
# INITIAL VALUE
# ====================================================
$rid_sesi = [];

# ====================================================
# GET DATA ARRAY SESI
# ====================================================
$s = "SELECT a.*, 
a.id as id_sesi, 
a.nama as nama_sesi 
FROM tb_sesi a WHERE a.id_room=$id_room";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0){
  echo div_alert('danger', "Belum ada sesi pada room $room.");
}else{
  // $div = '';
  while($d=mysqli_fetch_assoc($q)){
    // $id=$d['id'];
    $rid_sesi[$d['no']]=$d['id_sesi'];

    // $div .= "<div>$d[nama_sesi]</div>";
  }
}

// echo '<pre>';
// var_dump($rid_sesi);
// echo '</pre>';

// echo $div;

# ====================================================
# GET LIST PESERTA
# ====================================================
$s = "SELECT 
a.id as id_rp, 
b.id as id_peserta, 
b.nama as nama_peserta ,
b.kelas  
FROM tb_room_player a 
JOIN tb_peserta b ON a.id_peserta=b.id 
JOIN tb_kelas c ON b.kelas=c.kelas  
WHERE a.id_room=$id_room 
AND b.status=1 
AND b.kelas != 'BOCIL' 
ORDER BY c.shift, b.kelas, b.nama 
";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$div = '';
if(mysqli_num_rows($q)==0){
  echo div_alert('danger', "Belum ada data peserta pada room ini.");
}else{
  $div = '';
  $i = 0;
  while($d=mysqli_fetch_assoc($q)){
    $i++;
    $id_rp=$d['id_rp'];
    $nama = ucwords(strtolower($d['nama_peserta']));

    $sesies = '<div class="flexy tengah" style="gap:0; height: 100%">';
    foreach ($rid_sesi as $no => $id_sesi) {
      $sesies .= "<div class='bordered' style='flex:1; '>P$no</div>";
    }
    $sesies .= '</div>';

    # ==============================================================
    # FINAL OUTPUT :: SHOW IMAGE OR COMPACT
    # ==============================================================
    if($show_img){
      $div .= "
          <div class=mb1 style='display:grid; grid-template-columns: 300px auto'>
            <div>
              <div class='flexy p1 bordered'>
                <div>
                  <a href='assets/img/peserta/wars/peserta-$d[id_peserta]-hi.jpg' target=_blank><img src='assets/img/peserta/wars/peserta-$d[id_peserta].jpg' class=profil_penjawab ></a>
                </div>
                <div>
                  <div class='kecil miring abu'>$i.</div>
                  <div>$nama</div>
                  <div class='miring abu kecil'>$d[kelas]</div>
                </div>
              </div>        
            </div>
            <div>
              $sesies
            </div>
          </div>
      ";
    }else{
      $div .= "
        <div class=row>
          <div class=col-lg-8>
            <div>$i. $nama</div>
          </div>
          <div class=col-lg-4>
            <div class='miring abu kecil'>$d[kelas]</div>
          </div>
        </div>
      ";
    }
  }
}

echo $div;









?>
