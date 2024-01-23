<h1>Assign Peserta</h1>
<?php
$id_room_kelas = $_GET['id_room_kelas'] ?? die(erid('id_room_kelas'));

$s = "SELECT 
c.id_peserta,
d.nama as nama_peserta 

FROM tb_room_kelas a 
JOIN tb_kelas b ON a.kelas=b.kelas 
JOIN tb_kelas_peserta c ON b.kelas=c.kelas  
JOIN tb_peserta d ON c.id_peserta=d.id   
WHERE a.id=$id_room_kelas 
AND b.tahun_ajar=$tahun_ajar
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0){
  echo div_alert('danger', "Belum ada data room_kelas.");
}else{
  // $div = '';
  $i=0;
  while($d=mysqli_fetch_assoc($q)){
    $i++;

    echo "<hr>$d[nama_peserta]";
  }
}

echo "
  <div class='row'>
    <div class='col-6'>
      <h2>Peserta Kelas $kelas </h2>
      <table class='table'>
        <thead>
          <th>No</th>
          <th>Nama Peserta</th>
          <th>Aksi</th>
        </thead>
        $tr_peserta_kelas
      </table>
    </div>

    <div class='col-6'>
      <h2>Peserta Kelas $kelas pada Room $room </h2>
      <table class='table'>
        <thead>
          <th>No</th>
          <th>Nama Peserta</th>
          <th>Aksi</th>
        </thead>
        $tr_peserta_room
      </table>
    </div>
  </div>
";
?>

