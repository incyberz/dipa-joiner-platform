<div class="wadah p0 p2 gradasi-kuning tengah">
  <form method=post class=m0>
    <!-- <div class='f14 bold red consolas mb1'>Form Khusus $Trainer</div> -->
    <div class='f14 abu consolas mb2 mt2'>Saat ini saya sedang berada pada kelas:</div>
    <div class='flexy flex-center'>

      <?php
      $img_exit = img_icon('exit');
      if (isset($_POST['btn_set_target_kelas'])) {
        $_SESSION['target_kelas'] = $_POST['btn_set_target_kelas'];
        // $target_kelas = $_SESSION['target_kelas'];
        jsurl();
      } elseif (isset($_POST['btn_exit_target_kelas'])) {
        unset($_SESSION['target_kelas']);
        jsurl();
      }


      $s9 = $select_all_from_tb_room_kelas;
      $q9 = mysqli_query($cn, $s9) or die(mysqli_error($cn));
      if (!mysqli_num_rows($q9)) {
        echo div_alert('danger', "Belum terdapat-- active $room-kelas pada $Room ini.");
      } else {
        while ($d9 = mysqli_fetch_assoc($q9)) {
          $secondary = $d9['kelas'] == $target_kelas ? 'primary' : 'secondary';
          $ckelas = str_replace("-$ta_aktif", '', $d9['kelas']);
          echo "<div><button class='btn btn-$secondary mb2 btn-sm' name=btn_set_target_kelas value='$d9[kelas]'>$ckelas</button></div>";
        }
      }

      echo "<div><button class=' btn-transparan' name=btn_exit_target_kelas>$img_exit</button></div>";
      ?>

    </div>
  </form>
</div>