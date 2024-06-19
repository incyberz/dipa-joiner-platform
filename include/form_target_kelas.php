<div class="wadah p0 p2 gradasi-kuning tengah">
  <form method=post class=m0>
    <!-- <div class='f14 bold red consolas mb1'>Form Khusus Instruktur</div> -->
    <div class='f14 abu consolas mb2 mt2'>Saat ini saya sedang berada pada kelas:</div>
    <div class='flexy flex-center'>

      <?php
      if (isset($_POST['btn_set_target_kelas'])) {
        $_SESSION['target_kelas'] = $_POST['btn_set_target_kelas'];
        $target_kelas = $_SESSION['target_kelas'];
        jsurl();
      }


      $s9 = "SELECT * FROM tb_room_kelas WHERE id_room=$id_room AND kelas != 'INSTRUKTUR'";
      $q9 = mysqli_query($cn, $s9) or die(mysqli_error($cn));
      if (!mysqli_num_rows($q9)) {
        echo div_alert('danger', 'Belum terdapat room-kelas pada room ini.');
      } else {
        while ($d9 = mysqli_fetch_assoc($q9)) {
          $secondary = $d9['kelas'] == $target_kelas ? 'primary' : 'secondary';
          echo "<div><button class='btn btn-$secondary mb2 btn-sm' name=btn_set_target_kelas value='$d9[kelas]'>$d9[kelas]</button></div>";
        }
      }


      ?>
    </div>
  </form>
</div>