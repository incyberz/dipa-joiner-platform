<h1>Challenge to Ujian</h1>
<?php
$id_challenge = $_GET['id_challenge'] ?? die(erid('id_challenge'));

$s = "SELECT * FROM tb_challenge WHERE id = $id_challenge";

$s = "SELECT a.* 
FROM tb_challenge a WHERE id='$id_challenge'";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
if (mysqli_num_rows($q)) {
  $i = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    foreach ($d as $key => $value) {
      if (
        $key == 'id'
        || $key == 'date_created'
      ) continue;

      $kolom = key2kolom($key);
      $tr .= "
        <tr>
          <td>$kolom</td>
          <td>$value</td>
        </tr>
      ";
    }
  }
}

$tb = $tr ? "
  <table class='table table-striped table-hover'>
    $tr
  </table>
" : div_alert('danger', "Data challenge tidak ditemukan.");
echo "$tb";


echo "
<hr>
<h2>Dikerjakan Oleh</h2>";

$s = "SELECT 
b.kelas,
d.id as id_peserta,
d.nama as nama_peserta,
(SELECT COUNT(1) FROM tb_kelas_peserta WHERE kelas=c.kelas) count_peserta,
(SELECT ) poin_chal 
FROM tb_assign_challenge a 
JOIN tb_room_kelas b ON a.id_room_kelas=b.id 
JOIN tb_kelas_peserta c ON b.kelas=c.kelas 
JOIN tb_peserta d ON c.id_peserta=d.id
WHERE a.id_challenge = $id_challenge 
AND c.kelas != 'INSTRUKTUR'  
AND d.id_role = 1 -- peserta only 
ORDER BY b.kelas, d.nama 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
$i = 0;
$last_kelas = '';
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  if ($last_kelas != $d['kelas']) {
    $tr .= "
      <tr class='gradasi-toska'>
        <td>No</td>
        <td>Kelas</td>
        <td>Nama Peserta</td>
      </tr>
    ";
  }
  $tr .= "
    <tr>
      <td>$i</td>
      <td>$d[kelas]</td>
      <td>$d[nama_peserta]</td>
    </tr>
  ";
  $last_kelas = $d['kelas'];
}

echo "<table class='table table-striped'>$tr</table>";
