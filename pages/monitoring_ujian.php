<style>
  th {
    background: linear-gradient(#cfc, #afa)
  }
</style>
<?php
instruktur_only();


$id_paket = $_GET['id_paket'] ?? die('<script>location.replace("?ujian")</script>');
$show_nilai = $_GET['show_nilai'] ?? '';
$show_profil = $_GET['show_profil'] ?? '';

$Show = $show_nilai ? 'Hide' : 'Show';
$not_show_nilai = $show_nilai ? '' : 1;
$link_show_nilai = "<a href='?monitoring_ujian&id_paket=$id_paket&show_nilai=$not_show_nilai&show_profil=$show_profil'>$Show Nilai</a>";

$Show = $show_profil ? 'Hide' : 'Show';
$not_show_profil = $show_profil ? '' : 1;
$link_show_profil = "<a href='?monitoring_ujian&id_paket=$id_paket&show_nilai=$show_nilai&show_profil=$not_show_profil'>$Show Profil</a>";

$judul = "Monitoring Ujian";
set_title($judul);
echo "
<div class='section-title' data-aos='fade'>
  <h2>$judul</h2>
  <p>Yang sudah Ujian | $link_show_nilai | $link_show_profil</p>
</div>";


$img_check = '<img src=assets/img/icons/check.png height=25px />';




# =======================================================
# GET PROPERTIES PAKET UJIAN
# =======================================================
$s = "SELECT 
a.*,
a.nama as nama_paket_soal,
b.nama as pengawas_ujian,
c.nama as nama_sesi,
(SELECT COUNT(1) FROM tb_assign_soal WHERE id_paket=a.id)  jumlah_soal  
FROM tb_paket a 
JOIN tb_peserta b ON a.id_pembuat=b.id  
JOIN tb_kode_sesi c ON a.kode_sesi=c.kode_sesi  
WHERE a.id=$id_paket";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) die("Data Paket Soal tidak ditemukan.");
$d_paket = mysqli_fetch_assoc($q);



# =======================================================
# GET PAKET KELAS
# =======================================================
$s = "SELECT 
a.id as id_paket,
b.kelas,
b.awal_ujian 

FROM tb_paket a 
JOIN tb_paket_kelas b ON a.id=b.id_paket 
WHERE a.id = $id_paket 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) die("Paket Soal tidak ditemukan atau tidak ada kelas yang diassign.");
while ($d = mysqli_fetch_assoc($q)) {

  # =======================================================
  # LIST PESERTA
  # =======================================================
  $s2 = "SELECT 
  a.id as id_peserta,
  a.nama,
  b.kelas,

  (
    SELECT COUNT(1) FROM tb_jawabans p 
    JOIN tb_paket_kelas q ON p.id_paket_kelas=q.paket_kelas  
    WHERE p.id_peserta=a.id 
    AND q.id_paket=$d[id_paket]) jumlah_attemp, 
  (
    SELECT COUNT(1) FROM tb_jawabans p 
    JOIN tb_paket_kelas q ON p.id_paket_kelas=q.paket_kelas  
    WHERE p.id_peserta=a.id 
    AND q.id_paket=$d[id_paket] 
    ORDER BY p.nilai DESC 
    LIMIT 1) nilai_max,
  (
    SELECT tanggal_submit FROM tb_jawabans p
    JOIN tb_paket_kelas q ON p.id_paket_kelas=q.paket_kelas  
    WHERE p.id_peserta=a.id 
    AND q.id_paket=$d[id_paket] 
    ORDER BY p.tanggal_submit DESC 
    LIMIT 1) tanggal_submit 
  
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
    <th width=40%>Nama</th>
    <th width=15%>Kelas</th>
    <th width=15%>Attemp</th>
    <th width=10%>Last Submit</th>
    <th width=10% class=$hideit>Nilai</th>
  </thead>";

  # =======================================================
  # CSV HANDLER
  # =======================================================
  $sudah_berakhir = (strtotime($d['awal_ujian']) + strtotime($d_paket['durasi_ujian'])) > strtotime('now') ? 0 : 1;
  $arr_header = ['NO', 'PESERTA UJIAN', 'KELAS', 'ATTEMP',  'NILAI', 'LAST SUBMIT'];
  $src_csv = "csv/hasil_ujian-$d_paket[nama_paket_soal]-$d[kelas].csv";
  $file = fopen($src_csv, "w+");
  fputcsv($file, ['HASIL UJIAN ' . strtoupper($d_paket['nama_paket_soal'])]);
  fputcsv($file, ['KELAS ' . strtoupper($d['kelas'])]);
  fputcsv($file, [' ']);
  fputcsv($file, $arr_header);
  if ($sudah_berakhir) {
    $btn = 'primary';
    $onclick = '';
  } else {
    $onclick = 'onclick="return confirm(\'Ujian belum berakhir. Yakin untuk Download Hasil Ujian?\')"';
    $btn = 'warning';
  }
  $download_hasil_ujian = "<a href='$src_csv' class='btn btn-$btn btn-sm' target=_blank $onclick>Download Hasil Ujian</a> ";

  $no = 0;
  $jumlah_hadir = 0;
  $jumlah_peserta = mysqli_num_rows($q2);
  while ($d2 = mysqli_fetch_assoc($q2)) {
    $no++;
    $nama = strtoupper($d2['nama']);
    $check = '-';
    if ($d2['jumlah_attemp']) {
      $jumlah_hadir++;
      $check = '';
      for ($i = 0; $i < $d2['jumlah_attemp']; $i++) {
        $check .= "$img_check ";
      }
    }
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

    $eta = !$d2['tanggal_submit'] ? '-' : eta(strtotime($d2['tanggal_submit']) - strtotime('now'));

    $super_delete = '';
    // if ujian telah berakhir dan tidak ada nilai maka bisa hapus
    $super_delete = $d2['nilai_max'] ? '' : "
      <form method='post' action='?super_delete_peserta' style='display:inline'>
        <input type=hidden name=keyword value='$nama'>
        <button name=btn_search  style='border:none; padding:0'>$img_delete</button>
      </form>
    ";


    $tr .= "
    <tr class='gradasi-$merah'>
      <td>$no</td>
      <td>
        $nama $super_delete <a target=_blank href='?login_as&id_peserta=$d2[id_peserta]'>$img_login_as</a>
        <div>$img_profil</div>
      </td>
      <td class=f14>$d2[kelas]</td>
      <td>$check</td>
      <td class='f12'>$eta</td>
      <td class=$hideit>$d2[nilai_max]</td>
    </tr>";

    $d2['id_peserta'] = $no; // untuk numbering csv
    fputcsv($file, $d2);

    // for sub judul kelas
    $last_kelas = $d2['kelas'];
  }

  fputcsv($file, [' ']);
  fputcsv($file, ['', '', 'JUMLAH HADIR: ', $jumlah_hadir]);
  fputcsv($file, ['', '', 'TIDAK HADIR: ', $jumlah_peserta - $jumlah_hadir]);
  fputcsv($file, ['', '', 'TOTAL PESERTA: ', $jumlah_peserta]);
  fputcsv($file, [' ']);
  fputcsv($file, ['', '', 'PENGAWAS UJIAN: ', $d_paket['pengawas_ujian']]);
  fputcsv($file, ['', '', 'DATA FROM:', 'Gamified Learning DIPA Joiner ']);
  fputcsv($file, ['', '', '', 'http://iotikaindonesia.com/dipa']);
  fputcsv($file, ['', '', 'PRINTED AT:', date('F d, Y, H:i:s')]);
  fclose($file);

  echo "
    <h2 class='f20 darkblue mt4'>Kelas $last_kelas</h2>
    <table class='table '>
      $thead
      $tr
    </table>
    $download_hasil_ujian

    <hr>
  ";
}
