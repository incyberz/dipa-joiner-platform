<?php
instruktur_only();
set_h2(
  'SUPER DELETE PESERTA',
  "<span class='tebal red'>Perhatian! Super Delete digunakan untuk menghapus peserta dan seluruh kegiatan aktifitas belajarnya. <br>Aktifitas Presensi, Latihan, Challenge, Create Soal, Play Kuis, dan aktifitas lainnya juga akan terhapus.</span>"
);

$tb = 'Silahkan search!';
$keyword = $_GET['keyword'] ?? '';
if (isset($_POST['btn_reset_password'])) {
  $id_peserta = $_POST['btn_reset_password'];
  $s = "UPDATE tb_peserta SET password=NULL where id=$id_peserta";
  // die($s);
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  echo div_alert('success', 'Reset Password success. | <a href=?super_delete_peserta>Super Manage Home</a>');
  exit;
}

if (isset($_POST['btn_search'])) {
  jsurl("?super_delete_peserta&keyword=$_POST[keyword]");
}
if ($keyword) {

  # ============================================================
  # SELECT PESERTA BY KEYWORD
  # ============================================================
  $s = "SELECT a.id,
  a.nama,
  -- b.akumulasi_poin,
  d.kelas,
  (SELECT akumulasi_poin FROM tb_poin WHERE id_peserta=a.id and id_room=$id_room) akumulasi_poin,  
  (SELECT COUNT(1) FROM tb_kelas_peserta WHERE id_peserta=a.id) count_kelas_peserta,  
  (SELECT COUNT(1) FROM tb_war WHERE id_penjawab=a.id) count_wars,  
  (SELECT COUNT(1) FROM tb_jawabans WHERE id_peserta=a.id) count_ujian  
  FROM tb_peserta a 
  JOIN tb_kelas_peserta c ON a.id=c.id_peserta 
  JOIN tb_kelas d ON c.kelas=d.kelas 
  JOIN tb_room_kelas e ON d.kelas=e.kelas 
  WHERE a.nama like '%$keyword%'
  AND a.id_role = 1 
  AND d.status=1 
  AND d.tahun_ajar = $ta 
  -- AND b.id_room = $id_room 
  AND e.id_room = $id_room 
  LIMIT 50
  ";
  // echo "<hr>$s";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  $tr = '';
  if (!mysqli_num_rows($q)) {
    $tr = '<tr><td>' . div_alert('danger', "Data peserta tidak ditemukan pada room ini  | keyword: <b>$keyword</b>") . '</td></tr>';
  }

  while ($d = mysqli_fetch_assoc($q)) {
    $id = $d['id'];
    $super_delete = $d['count_ujian'] ? '' : "<a href='?super_delete_peserta&id=$id' class='btn btn-danger' onclick='return confirm(`Perform Super Delete?\n\nPerhatian! Seluruh data aktifitas dari peserta ini juga akan terhapus.`)'>Super Delete</a>";
    $tr .= "
      <tr>
        <td>
          $d[nama] | $d[kelas] | $d[akumulasi_poin] LP | Wars: $d[count_wars] | Ujian: $d[count_ujian] | count_kelas_peserta: $d[count_kelas_peserta]
        </td>
        <td>
          $super_delete
        </td>
        <td>
          <form method=post>
            <button class='btn btn-danger ' name=btn_reset_password value=$id onclick='return confirm(`Yakin untuk reset password?\n\nPassword akan kembali NULL, sehingga untuk login peserta, password  adalah sama dengan username-nya.`)'>Reset Password</button>
          </form>
        </td>

      </tr>
    ";
  }

  $tb = "<div class='wadah gradasi-kuning'><table class=table>$tr</table></div>";
}

if (isset($_GET['id'])) {
  $id = $_GET['id'];

  // $s = "SELECT * FROM tb_bertanya WHERE id_penanya=$id";
  // $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  // while ($d = mysqli_fetch_assoc($q)) {
  //   $s = "DELETE FROM tb_bertanya_reply WHERE id_pertanyaan=$d[id]";
  //   echo "<hr>$s";
  //   $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  // }

  $s = "DELETE FROM tb_bertanya WHERE id_penanya=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));



  $s = "DELETE FROM tb_war WHERE id_penjawab=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  $s = "DELETE FROM tb_paket_war WHERE id_peserta=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  $s = "DELETE FROM tb_jawabans WHERE id_peserta=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  $s = "DELETE FROM tb_kelas_peserta WHERE id_peserta=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  $s = "DELETE FROM tb_bukti_latihan WHERE id_peserta=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  $s = "DELETE FROM tb_bukti_challenge WHERE id_peserta=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  $s = "DELETE FROM tb_poin WHERE id_peserta=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));


  // get all war where id_soal=id of soal pg
  $s = "SELECT id as id_soal_pg FROM tb_soal_peserta WHERE id_pembuat=$id";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  while ($d = mysqli_fetch_assoc($q)) {
    $s2 = "DELETE FROM tb_war WHERE id_soal=$d[id_soal_pg]";
    echo "<hr>-- $s2";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  }

  $s = "DELETE FROM tb_soal_peserta WHERE id_pembuat=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  $s = "DELETE FROM tb_war_summary WHERE id_peserta=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  $s = "DELETE FROM tb_presensi WHERE id_peserta=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  $s = "DELETE FROM tb_presensi_summary WHERE id_peserta=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));













  $s = "DELETE FROM tb_peserta WHERE id=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  echo "<hr><a href='?super_delete_peserta'>Another Super Delete</a>";

  exit;
} else { ?>

  <form method=post>
    <div class="flexy wadah gradasi-kuning">
      <input type="text" class="form-control" placeholder='keyword' name=keyword value='<?= $keyword ?>'>
      <button class="btn btn-primary" name=btn_search>Search</button>

    </div>
  </form>

  <?= $tb ?>
<?php } ?>