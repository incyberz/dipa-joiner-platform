<?php
instruktur_only();
?>
<form method=post id=form-target-kelas class="hideit wadah p0 p2 gradasi-kuning tengah form-ontop">
  <div class='f14 abu consolas mb2 mt2'>Saat ini saya sedang berada pada kelas:</div>
  <div class='flexy flex-center tengah'>

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


    $s9 = $select_room_kelas;
    $q9 = mysqli_query($cn, $s9) or die(mysqli_error($cn));
    if (!mysqli_num_rows($q9)) {
      echo div_alert('danger', "Belum terdapat kelas pada $Room ini di TA. $ta_aktif.");
    } else {
      while ($d9 = mysqli_fetch_assoc($q9)) {
        $secondary = $d9['kelas'] == $target_kelas ? 'primary' : 'secondary';
        $ckelas = str_replace("-$ta_aktif", '', $d9['kelas']);
        echo "
          <div>
            <button class='btn btn-$secondary mb2 btn-sm' name=btn_set_target_kelas value='$d9[kelas]'>
              $d9[kelas_show]
            </button>
          </div>
        ";
      }
    }

    $btn_exit = $target_kelas ? "<div><button class=' btn-transparan' name=btn_exit_target_kelas onclick='return confirm(`Keluar dari kelas $target_kelas?`)'>$img_exit</button></div>" : '';

    echo $btn_exit;
    ?>

  </div>
  <a href="?manage_kelas">Manage Kelas</a>
</form>