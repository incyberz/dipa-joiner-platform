<?php
instruktur_only();
$judul = 'Monitoring Project Plan';
set_title($judul);

# ================================================================
# NAVIGATION VARIABLES AND VIEW RULES
# ================================================================
$blok_kelas = '';
$img_detail = img_icon('detail');
$img_refresh = img_icon('refresh');
$img_reject = img_icon('reject');
$get_kelas = $_GET['kelas'] ?? '';
$get_keyword = $_GET['keyword'] ?? '';
$jumlah_peserta = 0;











# ================================================================
# MAIN SELECT ROOM KELAS
# ================================================================
$sql_target_kelas = $target_kelas ? "b.kelas = '$target_kelas'" : '1';
$s = "SELECT a.kelas, a.id as id_room_kelas 
FROM tb_room_kelas a 
JOIN tb_kelas b ON a.kelas=b.kelas 
WHERE a.id_room=$id_room 
AND a.kelas != 'INSTRUKTUR' 
AND b.ta = $ta_aktif 
AND $sql_target_kelas
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) {
  $blok_kelas = div_alert('danger', "Belum ada Grup Kelas pada $Room ini untuk TA $ta_show | <a href='?manage_kelas'>Manage Kelas</a>");
}
while ($d = mysqli_fetch_assoc($q)) { // loop $Room kelas

  # ============================================================
  # SUB SELECT PESERTA KELAS
  # ============================================================
  $s2 = "SELECT 
  d.id as id_peserta,
  d.nama,
  d.image,
  d.war_image,
  b.kelas,
  (SELECT json_data FROM tb_project_plan WHERE id_room=$id_room AND id_peserta=d.id) json_data

  FROM tb_kelas_peserta a 
  JOIN tb_kelas b ON a.kelas=b.kelas 
  JOIN tb_room_kelas c ON b.kelas=c.kelas 
  JOIN tb_peserta d ON a.id_peserta=d.id  
  WHERE c.id=$d[id_room_kelas] 
  AND b.status=1 
  AND b.ta=$ta_aktif 
  AND d.status=1 
  ORDER BY b.shift, b.prodi,d.nama";

  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));

  if (mysqli_num_rows($q2)) {
    $list_peserta = '';
  } else {
    $list_peserta = div_alert('danger', "Tidak ada $Peserta pada kelas $d[kelas]. <hr>Untuk mahasiswa baru silahkan umumkan di Grup Whatsapp agar mhs Join ke kelas ini. <br>Jika mhs sudah ada silahkan Assign Peserta <hr> <a href='?assign_peserta_kelas&kelas=$d[kelas]'>$img_add Assign</a>");
  }

  $no = 0;
  while ($d2 = mysqli_fetch_assoc($q2)) { // LOOP PESERTA KELAS
    $nama = ucwords(strtolower($d2['nama']));
    $jumlah_peserta++;

    $app = $d2['json_data'] ? json_decode($d2['json_data'], 1) : [];

    $src = cek_src_profil($d2['image'], $d2['war_image'], $lokasi_profil);


    $no++;

    # ================================================================
    # GET ASSIGNED DATA LATIHAN UNTUK KELAS INI TIAP PESERTA
    # ================================================================
    $src = cek_src_profil($d2['image'], $d2['war_image'], $lokasi_profil);
    $judul_sistem = '';
    $infos = '';
    if ($app) {
      $judul_sistem = $app['judul_sistem'];
      // $infos = print_r($app, 1);
    }

    $list_peserta .= "
      <tr>
        <td>$no</td>
        <td class='kecil tengah abu'>
          <img src='$src' class='foto_profil'>
        </td>
        <td>
          <div>$nama <a href='?login_as&id_peserta=$d2[id_peserta]'>$img_login_as</a></div>
          <div class='f12 abu'>$d2[kelas]</div>
        </td>
        <td>
          $judul_sistem
        <td>
          $infos
        </td>
      </tr>
    ";
  }

  $blok_kelas .= "
    <div class='wadah' zzzdata-aos='fade-up' data-aos-delay='150'>
      <div class=sub_form>Detail Mode</div>
      Peserta Kelas $d[kelas]
      <table class='table mt1'>
        <thead>
          <th>No</th>
          <th>Profil Peserta</th>
          <th>Detail Info</th>
          <th>Judul Sisfo</th>
          <th>Detail Info</th>
        </thead>
        $list_peserta
      </table>      
    </div>
  ";
}


set_h2($judul, "Peserta Kelas MK $singkatan_room :: $jumlah_peserta $Peserta");
echo $blok_kelas;
?>
<script>
  $(function() {
    $('.toggle_aksi_peserta').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
      console.log(aksi, id);
      $('.aksi_peserta').slideUp();
      $('#aksi_peserta__' + id).slideDown();
    })
  })
</script>