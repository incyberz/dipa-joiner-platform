<?php
$tr = '';
$s = "SELECT * FROM tb_peserta WHERE status=1 and id_role=1";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$i=0;
while ($d=mysqli_fetch_assoc($q)) {
  $i++;
  $id_peserta = $d['id'];
  
  $s2 = "SELECT 
  a.id as id_sesi, 
  a.no as no_sesi, 
  a.nama as nama_sesi 
  FROM tb_sesi a 
  ORDER BY no";
  $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));
  $td_sesi = '';
  $th_sesi = '';
  while ($d2=mysqli_fetch_assoc($q2)) {
    $th_sesi .= "<th>P$d2[no_sesi] <div class='kecil miring abu'>$d2[nama_sesi]</div> </th>";
    
    $id_sesi = $d2['id_sesi'];

    $s3 = "SELECT a.no,
    (SELECT get_point FROM tb_bukti_latihan WHERE status=1 AND id_latihan=a.id AND id_peserta=$id_peserta) poin_latihan 
    FROM tb_assign_latihan a 
    WHERE a.id_sesi='$id_sesi' 
    
    ";
    $q3 = mysqli_query($cn,$s3) or die(mysqli_error($cn));
    $div_latihan = '';
    while ($d3=mysqli_fetch_assoc($q3)) {
      $div_latihan .= "<div class='u150px'>Lat-$d3[no]: $d3[poin_latihan]</div>";
    }

    $s3 = "SELECT a.no,
    (SELECT get_point FROM tb_bukti_challenge WHERE status=1 AND id_challenge=a.id AND id_peserta=$id_peserta) poin_challenge 
    FROM tb_assign_challenge a 
    WHERE a.id_sesi='$id_sesi' 
    AND a.no <=2
    ";
    $q3 = mysqli_query($cn,$s3) or die(mysqli_error($cn));
    $div_challenge = '';
    while ($d3=mysqli_fetch_assoc($q3)) {
      $div_challenge .= "<div class='u150px'>Proyek-$d3[no]: $d3[poin_challenge]</div>";
    }

    $pre_test = $d2['no_sesi']==1 ? "<div class='u150px'>Pre-test: 100</div>" : '';

    $td_sesi .= "
    <td>
      $pre_test
      $div_latihan
      $div_challenge
    </td>";
  }

  $td_sesi .= "
  <td>
    $d[akumulasi_poin]
  </td>
  <td>
    zzz
  </td>
  ";

  $thead = "
  <thead>
    <th>No</th>
    <th>Nama</th>
    $th_sesi
    <th>TOTAL POIN</th>
    <th>NILAI AKHIR</th>
  </thead>";


  $tr .= "
  <tr>
    <td>$i</td>
    <td>$d[nama]</td>
    $td_sesi
  </tr>
  ";
}

$rekap = "<div style='overflow:scroll;height:80vh'><table class='table table-striped table-bordered'>$thead$tr</table></div>";


?>
<style>td{padding:2px}.u150px{width:150px}</style>
<section id="services" class="services">
  <div class="container">
    <h3>Rekap Nilai</h3>

    <?=$rekap?>

  </div>
</section>

