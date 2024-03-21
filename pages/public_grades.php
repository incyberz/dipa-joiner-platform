<?php
$judul = 'The Best Top 10';

$s = "SELECT 
a.id as id_peserta,
a.nama as nama_peserta,
c.kelas,
d.akumulasi_poin 

FROM tb_peserta a 
JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
JOIN tb_kelas c ON b.kelas=c.kelas 
JOIN tb_poin d ON a.id=d.id_peserta  
WHERE c.tahun_ajar = $tahun_ajar  
AND c.status = 1 -- kelas aktif 
AND a.status = 1 -- peserta aktif
ORDER BY d.akumulasi_poin DESC LIMIT 10";

$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tb = div_alert('danger', 'Belum ada data peserta.');
if (mysqli_num_rows($q)) {
  $tr = '';
  $i = 0;
  $my_rank = 0;
  $jumlah_rows = mysqli_num_rows($q);
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $nama_show = ucwords(strtolower($d['nama_peserta']));
    $poin_show = number_format($d['akumulasi_poin'], 0);

    # ==================================
    # MY RANK
    # ==================================
    $link_nama_show = "$nama_show";

    $td_profil = ($id_role == 2 and $show_img) ? "<td><img src='assets/img/peserta/wars/peserta-$d[id_peserta].jpg' class=foto_profil></td>" : '';

    $tr .= "
      <tr>
        <td>$i</td>
        $td_profil
        <td>$link_nama_show <div class='kecil darkred'>$d[kelas]</div></td>
        <td>$poin_show LP</td>
      </tr>
    ";
  }

  $tb = "
    <table class='table table-striped table-hover'>
      $tr
    </table>
  ";
}

?>


<h4 class='darkblue bold text-center consolas ' data-aos="fade-up" data-aos-delay="150"><?= $judul ?></h4>
<div class="grades" data-aos="fade-up" data-aos-delay="150">
  <p>Berikut adalah 10 Peserta Terbaik DIPA Joiner</p>
  <?= $tb ?>
  <div class="kecil miring abu">
    Poin didapatkan dari pengerjaan latihan, challenge, perangsoal, tanam soal, atau aktifitas belajar lainnya.
  </div>
</div>