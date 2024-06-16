<style>
  .text-zoom {
    cursor: pointer;
    transition: .2s
  }

  .text-zoom:hover {
    letter-spacing: .5px;
    font-weight: bold
  }

  .rank_number {
    display: inline-block;
    color: blue;
  }

  .rank_th {
    display: inline-block;
    vertical-align: top;
  }

  #blok_summary {
    max-width: 500px;
    margin: auto
  }

  #blok_accuracy {
    max-width: 360px;
    margin: auto
  }
</style>
<?php
# =================================================================
login_only();
$link3 = "<a href='?perang_soal'>Perang Home</a>";
$link5 = "<a href='?war_statistics'>War Statistics</a>";
echo "
  <div class='section-title' data-aos-zzz='fade-up'>
    <h2>Good Soldier</h2>
    <p>
      <div>$link3 | $link5</div>
      <div class='blue tengah'>Terimakasih bagi kamu yang sudah berpartisipasi !!</div>
    </p>
  </div>
";

# =========================================================
# INITIAL VARIABLE
# =========================================================



# =========================================================
# MAIN SELECT
# =========================================================
$sql_kelas = $id_role == 1 ? "a.kelas = '$kelas' " : '1';

$s = "SELECT 
a.id as id_peserta,
a.nama as nama_peserta,
a.username,
b.kelas,
(SELECT COUNT(1) FROM tb_war WHERE id_penjawab=a.id) play_count, 
(SELECT COUNT(1) FROM tb_soal_pg WHERE id_pembuat=a.id) soal_count 

FROM tb_peserta a 
JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
JOIN tb_kelas c ON b.kelas=c.kelas 
WHERE a.id_role = 1 
AND c.tahun_ajar=$tahun_ajar 
AND c.status = 1 
AND $sql_kelas
ORDER BY c.shift, b.kelas, a.nama";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_row = mysqli_num_rows($q);

$goods = '<div class="darkblue tengah f22">War Participant:</div>';
$i = 0;
$no = 0;
$last_kelas = '';
while ($d = mysqli_fetch_assoc($q)) {
  if ($d['play_count'] == 0 && $d['soal_count'] == 0) continue;
  $i++;
  $no++;
  $nama = ucwords(strtolower($d['nama_peserta']));
  $login_as = $id_role == 1 ? '' : "<a href='?login_as&username=$d[username]'><img src='assets/img/icons/login_as.png' height=25px></a>";

  $div_header = '';
  if ($last_kelas != $d['kelas']) {
    $no = 1;
    $margin_top = $i == 1 ? '10px' : '45px';

    $div_header = "
      <div class='flexy gradasi-kuning p2' style='margin: $margin_top -12px 0 -12px'>
        <div style='flex:1' class='tengah'>No</div>
        <div style='flex:7'>Peserta $d[kelas]</div>
        <div style='flex:3' class=kanan>War-counts</div>
      </div>
    ";
  }

  $warcounts = $d['play_count'] + $d['soal_count'];

  $img = $id_role == 1 ? '' : "<img src='$lokasi_profil/wars/peserta-$d[id_peserta].jpg' class='profil_pembuat' style='display:inline-block;margin-right:10px'> ";

  $goods .= "
    $div_header
    <div class='flexy btop pt1 pb1'>
      <div style='flex:1' class='tengah'>$no</div>
      <div style='flex:7' >$img$nama $login_as</div>
      <div style='flex:3' class=kanan>$warcounts</div>
    </div>
  ";
  $last_kelas = $d['kelas'];
}







echo "
  <div class='wadah gradasi-hijau' id=blok_summary>
    $goods  
  </div>
";

















?>
<script>
  $(function() {

  })
</script>