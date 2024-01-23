<?php
instruktur_only();

$tb = 'Silahkan search!';
if(isset($_POST['btn_search'])){
  unset($_POST['btn_search']);

  $s = "SELECT * FROM tb_peserta WHERE nama like '%$_POST[keyword]%'";
  // echo "<hr>$s";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $tr = '';
  while($d=mysqli_fetch_assoc($q)){
    $id=$d['id'];
    $tr .= "
      <tr>
        <td>
          $d[nama] | $d[kelas] | $d[akumulasi_poin] LP | Rank: $d[rank_global] | UTS: $d[uts] | UAS: $d[uas]
        </td>
        <td>
          <a href='?super_delete_peserta&id=$id' class='btn btn-danger' onclick='return confirm(\"Perform Super Delete?\")'>Super Delete</a>
        </td>

      </tr>
    ";
  }

  $tb = "<div class=wadah><table class=table>$tr</table></div>";
}
?>
<div class="section-title" data-aos="fade-up">
  <h2 class=proper>SUPER DELETE PESERTA</h2>
</div>

<?php
if(isset($_GET['id'])){
  $id = $_GET['id'];

  $s = "SELECT * FROM tb_pertanyaan WHERE id_penanya=$id";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  while($d=mysqli_fetch_assoc($q)){
    $s = "DELETE FROM tb_jawaban_chat WHERE id_pertanyaan=$d[id]";
    echo "<hr>$s";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  }

  $s = "DELETE FROM tb_pertanyaan WHERE id_penanya=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  


  $s = "DELETE FROM tb_perang WHERE id_penjawab=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $s = "DELETE FROM tb_paket_war WHERE id_peserta=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $s = "DELETE FROM tb_jawabans WHERE id_peserta=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $s = "DELETE FROM tb_room_player WHERE id_peserta=$id";
  echo "<hr>$s";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $s = "DELETE FROM tb_bukti_latihan WHERE id_peserta=$id";
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
    <input type="text" class="form-control" placeholder='keyword' name=keyword>
    <button class="btn btn-primary" name=btn_search>Search</button>

  </div>
</form>

<?=$tb?>
<?php } ?>