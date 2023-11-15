<section id="about" class="about"><div class="container">
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
// login_only(); // it's public
include 'include/date_managements.php';

$link3 = "<a href='?perang_soal'>Perang Home</a>";
$link5 = "<a href='?tanam_soal'>Tanam Soal</a>";
$links_nav = $is_login ? "<div>$link3 | $link5</div>" : '';
echo "
  <div class='section-title' data-aos-zzz='fade-up'>
    <h2>War Statistics</h2>
    $links_nav
    <div class='abu kecil'>DIPA Joiner Achievements until today.</div>
  </div>
";

# =========================================================
# INITIAL VARIABLE
# =========================================================
$active_players = 0;
$new_questions = 0;
$war_counts = 0;
$ahad_skg_show = 'Ahad, '.date('d M Y',strtotime($ahad_skg));
$ahad_depan_show = date('d M Y',strtotime($ahad_depan));
$now_show = $nama_hari[date('w')].', '. date('d M Y H:i');
$minggu_ini_show = 'Ahad, '. date('d M Y',strtotime($ahad_skg)).' s.d '.date('d M Y',strtotime($ahad_depan));

$today = date('Y-m-d');

# =========================================================
# MAIN SELECT
# =========================================================
$rstats = ['Hari ini','Minggu ini'];
foreach ($rstats as $saat) {

  $and_tanggal = $saat=='Hari ini' ? "AND tanggal >= '$today'" : "AND tanggal >= '$ahad_skg'" ;
  $now_show = $saat=='Hari ini' ? $now_show : $minggu_ini_show;

  
  $s = "SELECT 
  a.nama as nama_peserta,
  a.kelas,
  a.username,
  (SELECT COUNT(1) FROM tb_perang WHERE id_penjawab=a.id $and_tanggal) war_counts, 
  (SELECT COUNT(1) FROM tb_soal_pg WHERE id_pembuat=a.id $and_tanggal) new_questions 
  
  FROM tb_peserta a 
  JOIN tb_kelas b ON a.kelas=b.kelas  
  WHERE a.id_role = 1 
  AND a.kelas != 'BOCIL' 
  ORDER BY b.shift, a.kelas, a.nama";
  
  // echo "<pre>$s</pre>";
  
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $jumlah_row = mysqli_num_rows($q);
  $i=0;
  while($d=mysqli_fetch_assoc($q)){
    $i++;
    if($d['war_counts'] || $d['new_questions']){
      $active_players++;
      if($d['war_counts']){
        $war_counts += $d['war_counts'];
      }
      if($d['new_questions']){
        $new_questions += $d['new_questions'];
      }
    }
  }

  $active_players_show = number_format($active_players,0);
  $new_questions_show = number_format($new_questions,0);
  $war_counts_show = number_format($war_counts,0);
  
  $stats[$saat] = "
      <div class='tengah abu mb1 kecil'>$saat | $now_show</div>
      <div class='wadah bg-white' id=blok_summary>
        <div class='tengah  pt1 pb1'> $active_players_show <span class='kecil abugelap'>active players</span></div>
        <div class='tengah btop pt1 pb1'> $new_questions_show <span class='kecil abugelap'>new questions</span></div>
        <div class='tengah btop pt1 pb1'> $war_counts_show <span class='kecil abugelap'>new answers</span></div>
      </div>
  ";
}




echo "
  <div class='wadah gradasi-hijau' id=blok_summary>
    ".$stats['Hari ini']."
    <div>&nbsp;</div>
    ".$stats['Minggu ini']."
  </div>
";

















?></div></section>
<script>
  $(function(){

  })
</script>
