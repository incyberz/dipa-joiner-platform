<style>
  th {
    background: linear-gradient(#cfc, #afa)
  }
</style>
<?php
instruktur_only();

$id_paket_soal = $_GET['id_paket_soal'] ?? die('<script>location.replace("?ujian")</script>');
$show_nilai = $_GET['show_nilai'] ?? '';
$show_profil = $_GET['show_profil'] ?? '';

$Show = $show_nilai ? 'Hide' : 'Show';
$not_show_nilai = $show_nilai ? '' : 1;
$link_show_nilai = "<a href='?monitoring_ujian&id_paket_soal=$id_paket_soal&show_nilai=$not_show_nilai&show_profil=$show_profil'>$Show Nilai</a>";

$Show = $show_profil ? 'Hide' : 'Show';
$not_show_profil = $show_profil ? '' : 1;
$link_show_profil = "<a href='?monitoring_ujian&id_paket_soal=$id_paket_soal&show_nilai=$show_nilai&show_profil=$not_show_profil'>$Show Profil</a>";

echo "
<div class='section-title' data-aos='fade'>
  <h2>Monitoring Ujian</h2>
  <p>Yang sudah Ujian | $link_show_nilai | $link_show_profil</p>
</div>";


$img_check = '<img src=assets/img/icons/check.png height=25px />';




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
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q) == 0) die("Data Paket Soal tidak ditemukan.");
$d_paket = mysqli_fetch_assoc($q);



# =======================================================
# GET SIMILAR PAKET BY NAMA PAKET
# =======================================================
$s = "SELECT a.id as id_paket_soal, a.kelas 
FROM tb_paket_soal a 
JOIN tb_kelas b ON a.kelas=b.kelas 
WHERE a.nama='$d_paket[nama]' 
AND a.kelas!='INSTRUKTUR' 
ORDER BY b.shift, a.kelas";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q) == 0) die("Similar Paket Soal tidak ditemukan.");
while ($d = mysqli_fetch_assoc($q)) {

  # =======================================================
  # LIST PESERTA
  # =======================================================
  $s2 = "SELECT 
  a.id as id_peserta,
  a.nama,
  b.kelas,

  (
    SELECT COUNT(1) FROM tb_jawabans 
    WHERE id_peserta=a.id AND id_paket_soal=$d[id_paket_soal]) jumlah_attemp, 
  (
    SELECT nilai FROM tb_jawabans 
    WHERE id_peserta=a.id AND id_paket_soal=$d[id_paket_soal] 
    ORDER BY nilai DESC 
    LIMIT 1) nilai_max 
  
  FROM tb_peserta a 
  JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
  JOIN tb_kelas c ON b.kelas=c.kelas 
  JOIN tb_room_kelas d ON c.kelas=d.kelas 
  WHERE a.status=1  -- peserta aktif
  AND a.password is not null 
  AND a.nama not like '%dummy%' 
  AND a.id_role=1 
  AND b.kelas='$d[kelas]' 
  AND c.status = 1 -- kelas aktif
  AND d.id_room=$id_room  
  ORDER BY a.nama
  ";
  // echo "<pre>$s2</pre>";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));

  $hideit = $show_nilai ? '' : 'hideit';
  $tr = '';
  $thead = "<thead>
    <th width=5%>No</th>
    <th width=45%>Nama</th>
    <th width=20%>Kelas</th>
    <th width=15%>Attemp</th>
    <th width=15% class=$hideit>Nilai</th>
  </thead>";
  $no = 0;
  while ($d2 = mysqli_fetch_assoc($q2)) {
    $no++;
    $nama = strtoupper($d2['nama']);
    $check = $d2['jumlah_attemp'] == 1 ? $img_check : '-';
    $check = $d2['jumlah_attemp'] >= 2 ? "$img_check $img_check" : $check;
    $merah = $d2['jumlah_attemp'] ? '' : 'merah';

    $src2 = "assets/img/peserta/peserta-$d2[id_peserta].jpg";
    $src = "assets/img/peserta/wars/peserta-$d2[id_peserta].jpg";
    $sty = '';
    if (file_exists($src)) {
      // do nothing
    } elseif (file_exists($src2) and !file_exists($src)) {
      // profil ada tapi belum jadi profil wars
      $sty = 'border:solid 3px blue';
      $src = $src2;
    } else {
      $src = 'assets/img/img_na.jpg';
    }

    $img_profil = !$show_profil ? '' : "<img src='$src' class='foto_profil' style='$sty'>";

    $tr .= "
    <tr class='gradasi-$merah'>
      <td>$no</td>
      <td>
        $nama
        <div>$img_profil</div>
      </td>
      <td>$d2[kelas]</td>
      <td>$check</td>
      <td class=$hideit>$d2[nilai_max]</td>
    </tr>";

    $last_kelas = $d2['kelas'];
  }

  echo "<h2 class='f20 darkblue mt4'>Kelas $last_kelas</h2><table class='table '>$thead$tr</table>";
}
