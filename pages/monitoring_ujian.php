<style>th{background: linear-gradient(#cfc,#afa)}</style>
<?php
if(!$is_login) die('<script>location.replace("?")</script>');
$id_paket_soal = $_GET['id_paket_soal'] ?? die('<script>location.replace("?ujian")</script>');
echo "<section><div class=container>
<div class='section-title' data-aos='fade-up'>
  <h2>Monitoring Ujian</h2>
  <p>Yang sudah Ujian</p>
</div>";




# =======================================================
# GET PROPERTIES PAKET UJIAN
# =======================================================
$s = "SELECT 
a.*,
b.nama as pembuat,
c.nama as nama_sesi,
(SELECT COUNT(1) FROM tb_assign_soal WHERE id_paket_soal=a.id)  jumlah_soal  
FROM tb_paket_soal a 
JOIN tb_peserta b ON a.id_pembuat=b.id  
JOIN tb_kode_sesi c ON a.kode_sesi=c.kode_sesi  
WHERE a.id=$id_paket_soal";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0) die("Data Paket Soal tidak ditemukan.");
$d_paket=mysqli_fetch_assoc($q);



# =======================================================
# GET SIMILAR PAKET BY NAMA PAKET
# =======================================================
$s = "SELECT id as id_paket_soal, kelas FROM tb_paket_soal WHERE nama='$d_paket[nama]' AND kelas!='BOCIL' ORDER BY kelas";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0) die("Similar Paket Soal tidak ditemukan.");
while ($d=mysqli_fetch_assoc($q)) {

  # =======================================================
  # LIST PESERTA
  # =======================================================
  $s2 = "SELECT a.*,
  (
    SELECT COUNT(1) FROM tb_jawabans 
    WHERE id_peserta=a.id AND id_paket_soal=$d[id_paket_soal]) jumlah_attemp, 
  (
    SELECT nilai FROM tb_jawabans 
    WHERE id_peserta=a.id AND id_paket_soal=$d[id_paket_soal] 
    ORDER BY nilai DESC 
    LIMIT 1) nilai_max 
  
  FROM tb_peserta a 
  WHERE status=1 
  AND password is not null 
  AND a.id_role=1 
  AND a.kelas='$d[kelas]'
  ORDER BY a.nama
  ";
  // echo "<pre>$s2</pre>";
  $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));
  $tr='';
  $thead = '<thead>
    <th width=5%>No</th>
    <th width=45%>Nama</th>
    <th width=20%>Kelas</th>
    <th width=15%>Attemp</th>
    <th width=15%>Nilai</th>
  </thead>';
  $no=0;
  while ($d2=mysqli_fetch_assoc($q2)) {
    $no++;
    $nama = strtoupper($d2['nama']);
    $tr.= "
    <tr>
      <td>$no</td>
      <td>$nama</td>
      <td>$d2[kelas]</td>
      <td>$d2[jumlah_attemp]</td>
      <td>$d2[nilai_max]</td>
    </tr>";
  
    $last_kelas = $d2['kelas'];
  }
  
  echo "<table class='table table-striped'>$thead$tr</table>";


}










echo "</div></section>";
