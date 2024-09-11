<?php
$dm_db = 1;
include 'conn.php';

// $s = "SELECT a.*,
// b.nama as nama_paket  
// FROM tb_paket_kelas a 
// JOIN tb_paket b ON a.id_paket = b.id  
// ORDER BY b.nama";
// $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

// $last_nama = '';
// $last_id_paket = '';
// while ($d = mysqli_fetch_assoc($q)) {
//   // $id=$d['id'];
//   $beda =  $last_nama == $d['nama_paket'] ? '' : 'BEDA';

//   if ($last_nama == $d['nama_paket']) {
//     // delete assign soal
//     $s2 = "DELETE FROM tb_assign_soal WHERE id_paket=$d[id_paket]";
//     echo "<br>$s2";
//     $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));

//     // delete paket
//     $s2 = "DELETE FROM tb_paket_kelas WHERE id_paket=$d[id_paket] AND kelas='$d[kelas]'";
//     echo "<br>$s2";
//     $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));

//     // delete paket
//     $s2 = "UPDATE tb_jawabans SET id_paket=$last_id_paket WHERE id_paket=$d[id_paket]";
//     echo "<br>$s2";
//     $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));

//     // delete paket
//     $s2 = "DELETE FROM tb_paket WHERE id=$d[id_paket]";
//     echo "<br>$s2";
//     $q2 = mysqli_query($cn, $s2); // or die(mysqli_error($cn));

//     // insert PAKET KELAS
//     $paket_kelas = $last_id_paket . "__$d[kelas]";
//     $s2 = "INSERT INTO tb_paket_kelas (
//       paket_kelas,
//       id_paket,
//       kelas
//     ) VALUES (
//       '$paket_kelas',
//       '$last_id_paket',
//       '$d[kelas]'
//     )";
//     echo "<br>$s2";
//     $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
//     
//   } else {
//     // beda
//     $last_id_paket = $d['id_paket'];
//   }
//   echo "<br>nama_paket:$d[nama_paket] || id_paket:$d[id_paket] | kelas:$d[kelas] | $beda";

//   $last_nama = $d['nama_paket'];
// }


$s = "SELECT * FROM tb_jawabans";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));


$s = "SELECT 
a.id as id_jawabans,
a.id_room,
a.id_peserta,
a.paket_kelas,
a.id_paket_old,
(
  SELECT p.kelas FROM tb_kelas_peserta p 
  JOIN tb_kelas q ON p.kelas=q.kelas 
  WHERE p.id_peserta=a.id_peserta 
  AND q.ta = 20232) kelas


FROM tb_jawabans a WHERE 1";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
if (mysqli_num_rows($q)) {
  $i = 0;
  $th = '';
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $td = '';
    foreach ($d as $key => $value) {
      if (
        $key == 'id'
        || $key == 'date_created'
      ) continue;
      if ($i == 1) {
        // $kolom = key2kolom($key);
        $kolom = $key;
        $th .= "<th>$kolom</th>";
      }
      $td .= "<td>$value</td>";
    }
    $tr .= "
      <tr>
        $td
      </tr>
    ";

    $s2 = "SELECT awal_ujian_old FROM tb_paket WHERE id_room=$d[id_room] AND kelas_old='$d[kelas]'";
    echo "<br>$s2";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    $d2 = mysqli_fetch_assoc($q2);

    if ($d2) {

      $awal_ujian_old = $d2['awal_ujian_old'];


      $paket_kelas = $d['id_paket_old'] . "__$d[kelas]";
      $s2 = "INSERT INTO tb_paket_kelas (
        paket_kelas,
        id_paket,
        kelas,
        awal_ujian
      ) VALUES (
        '$paket_kelas',
        '$d[id_paket_old]',
        '$d[kelas]',
        '$awal_ujian_old'
      ) ON DUPLICATE KEY UPDATE 
        id_paket = '$d[id_paket_old]',
        kelas = '$d[kelas]',
        awal_ujian='$awal_ujian_old'
      ";
      echo "<br>$s2";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));


      $s2 = "UPDATE tb_jawabans SET paket_kelas = '$paket_kelas' 
      WHERE id=$d[id_jawabans]
      ";
      echo "<hr>$s2";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    }
  }
}

$tb = $tr ? "
  <table class=table border=1 cellpadding=5>
    <thead>$th</thead>
    $tr
  </table>
" : div_alert('danger', "Data jawabans tidak ditemukan.");
echo "$tb";
