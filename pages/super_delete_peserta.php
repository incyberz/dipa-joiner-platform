<?php
instruktur_only();

$tb = 'Silahkan search!';
$keyword='';
if(isset($_POST['btn_reset_password'])){
  $id_peserta = $_POST['btn_reset_password'];
  $s = "UPDATE tb_peserta SET password=NULL where id=$id_peserta";
  // die($s);
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  echo div_alert('success','Reset Password success. | <a href=?super_delete_peserta>Super Manage Home</a>');
  exit;
}

if(isset($_POST['btn_search'])){
  unset($_POST['btn_search']);
  $keyword = $_POST['keyword'];

  $s = "SELECT a.id,
  a.nama,
  b.akumulasi_poin,
  d.kelas,
  (SELECT COUNT(1) FROM tb_war WHERE id_penjawab=a.id) wars_count  
  FROM tb_peserta a 
  JOIN tb_poin b ON a.id=b.id_peserta 
  JOIN tb_kelas_peserta c ON a.id=c.id_peserta 
  JOIN tb_kelas d ON c.kelas=d.kelas 
  JOIN tb_room_kelas e ON d.kelas=e.kelas 
  WHERE a.nama like '%$keyword%'
  AND d.status=1 
  AND d.tahun_ajar = $tahun_ajar 
  AND b.id_room = $id_room 
  AND e.id_room = $id_room 
  ";
  // echo "<hr>$s";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $tr = '';
  while($d=mysqli_fetch_assoc($q)){
    $id=$d['id'];
    $tr .= "
      <tr>
        <td>
          $d[nama] | $d[kelas] | $d[akumulasi_poin] LP | Wars: $d[wars_count] 
        </td>
        <td>
          <a href='?super_delete_peserta&id=$id' class='btn btn-danger' onclick='return confirm(\"Perform Super Delete?\")'>Super Delete</a>
        </td>
        <td>
          <form method=post>
            <button class='btn btn-danger ' name=btn_reset_password value=$id onclick='return confirm(\"Yakin untuk reset password?\")'>Reset Password</button>
          </form>
        </td>

      </tr>
    ";
  }

  $tb = "<div class=wadah><table class=table>$tr</table></div>";
}
?>
<div class="section-title" >
  <h2 class=proper>SUPER DELETE PESERTA</h2>
</div>

<?php
if(isset($_GET['id'])){
  $id = $_GET['id'];

  $s = "SELECT * FROM tb_pertanyaan WHERE id_penanya=$id";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  while($d=mysqli_fetch_assoc($q)){
    $s = "DELETE FROM tb_pertanyaan_reply WHERE id_pertanyaan=$d[id]";
    echo "<hr>$s";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  }

  $s = "DELETE FROM tb_pertanyaan WHERE id_penanya=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  


  $s = "DELETE FROM tb_war WHERE id_penjawab=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $s = "DELETE FROM tb_paket_war WHERE id_peserta=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $s = "DELETE FROM tb_jawabans WHERE id_peserta=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $s = "DELETE FROM tb_kelas_peserta WHERE id_peserta=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $s = "DELETE FROM tb_bukti_latihan WHERE id_peserta=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $s = "DELETE FROM tb_poin WHERE id_peserta=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));


  // get all war where id_soal=id of soal pg
  $s = "SELECT id as id_soal_pg FROM tb_soal_pg WHERE id_pembuat=$id";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  while($d=mysqli_fetch_assoc($q)){
    $s2 = "DELETE FROM tb_war WHERE id_soal=$d[id_soal_pg]";
    echo "<hr>-- $s2";
    $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));
  }

  $s = "DELETE FROM tb_soal_pg WHERE id_pembuat=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $s = "DELETE FROM tb_war_summary WHERE id_peserta=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $s = "DELETE FROM tb_presensi_summary WHERE id_peserta=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));













  $s = "DELETE FROM tb_peserta WHERE id=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  echo "<hr><a href='?super_delete_peserta'>Another Super Delete</a>";

  exit;
}else{ ?>

<form method=post>
  <div class="flexy wadah">
    <input type="text" class="form-control" placeholder='keyword' name=keyword value='<?=$keyword?>'>
    <button class="btn btn-primary" name=btn_search>Search</button>

  </div>
</form>

<?=$tb?>
<?php } ?>