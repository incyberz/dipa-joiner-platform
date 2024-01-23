
  <style>
  .text-zoom{cursor: pointer; transition: .2s}
  .text-zoom:hover{letter-spacing: .5px; font-weight: bold}
  .rank_number{display:inline-block; color:blue;}
  .rank_th{display:inline-block; vertical-align:top;}
  
  #blok_summary {max-width: 500px; margin:auto}
  #blok_accuracy {max-width: 360px; margin:auto}
  </style>
<?php
# =================================================================
login_only();
$link3 = "<a href='?perang_soal'>Perang Home</a>";
$link5 = "<a href='?good_soldier'>Good Soldier</a>";
echo "
  <div class='section-title' data-aos-zzz='fade-up'>
    <h2>Lazy Soldier</h2>
    <p>
      <div>$link3 | $link5</div>
      <div class=abu>List yg ga war minggu ini! <span class=blue>Play Quiz</span> <span class=red>or DIE!!</span></div>
      <div class='darkred tengah'>Kamu dilarang ada di list ini !!</div>
    </p>
  </div>
";

# =========================================================
# INITIAL VARIABLE
# =========================================================



# =========================================================
# MAIN SELECT
# =========================================================
$sql_kelas = $id_role ==1 ? "a.kelas = '$kelas' " : '1';

$s = "SELECT 
a.id as id_peserta,
a.nama as nama_peserta,
a.kelas,
a.username,
(SELECT COUNT(1) FROM tb_perang WHERE id_penjawab=a.id) play_count, 
(SELECT COUNT(1) FROM tb_soal_pg WHERE id_pembuat=a.id) soal_count 

FROM tb_peserta a 
JOIN tb_kelas b ON a.kelas=b.kelas  
WHERE a.id_role = 1 
AND a.kelas != 'BOCIL' 
AND $sql_kelas
ORDER BY b.shift, a.kelas, a.nama";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_row = mysqli_num_rows($q);

$lazys = '<div class="red tengah f22">Yang belum ikut perang:</div>';
$i=0;
$no=0;
$last_kelas = '';
while($d=mysqli_fetch_assoc($q)){
  if($d['play_count']>0 || $d['soal_count']>0) continue;
  $i++;
  $no++;
  $nama = ucwords(strtolower($d['nama_peserta']));
  $login_as = $id_role == 1 ? '' : "<a href='?login_as&username=$d[username]'><img src='assets/img/icons/login_as.png' height=25px></a>";

  $div_header = '';
  if($last_kelas!=$d['kelas']){
    $no = 1;
    $margin_top = $i==1 ? '10px' : '45px';

    $div_header = "
      <div class='flexy gradasi-kuning p2' style='margin: $margin_top -12px 0 -12px'>
        <div style='flex:1' class='tengah'>No</div>
        <div style='flex:5'>PESERTA $d[kelas]</div>
        <div style='flex:3' class=kanan>WAR-COUNTS</div>
      </div>
    ";
  }

  $img = $id_role==1 ? '' : "<img src='assets/img/peserta/peserta-$d[id_peserta].jpg' class='profil_pembuat' > <span>&nbsp;</span> ";

  $lazys.= "
    $div_header
    <div class='flexy btop pt1 pb1'>
      <div style='flex:1' class='tengah'>$no</div>
      <div style='flex:5'>$img $nama $login_as</div>
      <div style='flex:3' class=kanan>$d[play_count]</div>
    </div>
  ";
  $last_kelas = $d['kelas'];
}







echo "
  <div class='wadah gradasi-merah' id=blok_summary>
    $lazys  
  </div>
";

















?>
<script>
  $(function(){

  })
</script>
