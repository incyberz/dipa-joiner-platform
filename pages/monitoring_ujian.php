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


$img_check = '<img src=assets/img/icon/check.png height=25px />';


























# ============================================================
# PROCESSORS
# ============================================================
if (isset($_POST['btn_hapus_jawaban_instruktur'])) {
  $paket_kelas = $id_paket . '__INSTRUKTUR';
  $s = "DELETE FROM tb_jawabans WHERE paket_kelas='$paket_kelas' AND id_peserta=$_POST[btn_hapus_jawaban_instruktur]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echolog('Hapus jawaban instruktur sukses.');
  jsurl();


  exit;
}
















# =======================================================
# GET PROPERTIES PAKET UJIAN
# =======================================================
$s = "SELECT 
a.*,
a.nama as nama_paket_soal,
b.nama as pengawas_ujian,
-- c.nama as nama_sesi,
(SELECT COUNT(1) FROM tb_assign_soal WHERE id_paket=a.id)  jumlah_soal  
FROM tb_paket a 
JOIN tb_peserta b ON a.id_pembuat=b.id  
-- JOIN tb_ kode sesi aborted  
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

  // if ($d['kelas'] == 'INSTRUKTUR') continue;

  # =======================================================
  # LIST PESERTA
  # =======================================================
  $s2 = "SELECT 
  a.id as id_peserta,
  a.nama,
  a.id_role,
  a.image,
  a.war_image,
  b.kelas,

  (
    SELECT COUNT(1) FROM tb_jawabans p 
    JOIN tb_paket_kelas q ON p.paket_kelas=q.paket_kelas  
    WHERE p.id_peserta=a.id 
    AND q.id_paket=$d[id_paket]) jumlah_attemp, 
  (
    SELECT MAX(nilai) FROM tb_jawabans p 
    JOIN tb_paket_kelas q ON p.paket_kelas=q.paket_kelas  
    WHERE p.id_peserta=a.id 
    AND q.id_paket=$d[id_paket] 
    ORDER BY p.nilai DESC 
    LIMIT 1) nilai_max,
  (
    SELECT tanggal_submit FROM tb_jawabans p
    JOIN tb_paket_kelas q ON p.paket_kelas=q.paket_kelas  
    WHERE p.id_peserta=a.id 
    AND q.id_paket=$d[id_paket] 
    ORDER BY p.tanggal_submit DESC 
    LIMIT 1) tanggal_submit 
  
  FROM tb_peserta a 
  JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
  JOIN tb_kelas c ON b.kelas=c.kelas 
  JOIN tb_room_kelas d ON c.kelas=d.kelas 
  WHERE 1 -- a.status=1  -- _peserta aktif
  AND a.password is not null 
  -- AND a.id_role=1 
  AND b.kelas='$d[kelas]' 
  AND c.status = 1 -- kelas aktif
  AND d.id_room=$id_room  
  AND d.ta = $ta 
  ORDER BY a.nama
  ";

  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  if (mysqli_num_rows($q2)) {

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

    $no = 0;
    $jumlah_hadir = 0;
    $jumlah_peserta = mysqli_num_rows($q2);
    $last_kelas = '';
    $download_hasil_ujian = $jumlah_peserta ? "<a href='$src_csv' class='btn btn-$btn btn-sm' target=_blank $onclick>Download Hasil Ujian</a> " : "<span class='btn btn-secondary btn-sm' onclick='alert(`Belum ada pesertanya.`)'>Download Hasil Ujian</span>";
    while ($d2 = mysqli_fetch_assoc($q2)) {

      // jika INSTRUKTUR (trial)
      if ($d2['id_role'] == 2 and !$d2['jumlah_attemp']) continue; // skip jika instruktur dan belum submit
      $delete_jawaban = '';
      if ($d2['id_role'] == 2) {
        // jawaban trial dari INSTRUKTUR boleh dihapus
        $delete_jawaban = "
          <form method=post class='mt2'>
            <button class='btn btn-danger btn-sm' name=btn_hapus_jawaban_instruktur value=$d2[id_peserta]>Hapus Jawaban INSTRUKTUR</button>
          </form>
        ";
      }


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

      $src2 = "$lokasi_profil/$d2[image]";
      $src = "$lokasi_profil/$d2[war_image]";
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
          $nama 
          $super_delete 
          <a target=_blank22 href='?login_as&id_peserta=$d2[id_peserta]'>$img_login_as</a>
          <div>$img_profil</div>
          $delete_jawaban
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
    } // end while list _peserta

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

    if ($d['kelas'] == 'INSTRUKTUR' and !$jumlah_hadir) {
      // skip UI jika tidak ada instruktur yang menjawab
    } else {
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
  } else { // tanpa _peserta yang menjawab, mungkin submitter lama
    $s2 = "SELECT *,
    c.nama as penjawab  
    FROM tb_jawabans a 
    JOIN tb_paket_kelas b ON a.paket_kelas=b.paket_kelas 
    JOIN tb_peserta c ON a.id_peserta=c.id  
    WHERE b.id_paket=$id_paket";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    $i = 0;
    $penjawabs = '';
    while ($d2 = mysqli_fetch_assoc($q2)) {
      $i++;
      $penjawabs .= "<br>$i. $d2[penjawab]";
    }
    echo $penjawabs ? "Penjawab di system lama:$penjawabs" : "<hr>--no data-- untuk kelas [$d[kelas]]";
  }
} // end while paket kelas
