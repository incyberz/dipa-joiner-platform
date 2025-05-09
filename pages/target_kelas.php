<div class="section-title">
  <h2 class=proper>Set Target Kelas</h2>
  <p>Jika Anda sedang masuk ke kelas tertentu, Anda dapat <u>Set Target Kelas</u> agar default Rekap Presensi, Grades, dan hal lainnya mengacu ke target kelas yang Anda pilih.</p>
</div>

<div>
  <form method=post>
    <?php
    if (isset($_POST['btn_set_target_kelas'])) {
      $_SESSION['target_kelas'] = $_POST['btn_set_target_kelas'];
      $target_kelas = $_SESSION['target_kelas'];
      jsurl();
    }

    $info_target = $target_kelas ? "<div>Target kelas saat ini: <b class=darkblue>$target_kelas</b> | <button name=btn_set_target_kelas >Refresh Page</button></div>" : '<div class="consolas darkred">Saat ini target kelas belum terpilih.</div>';

    echo "
      $info_target
      <hr>
      <div class=mb2>Set target kelas ke:</div>
    ";

    $s = $select_room_kelas;
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    if (!mysqli_num_rows($q)) {
      echo div_alert('danger', "Belum terdapat $Room-kelas pada $Room ini. | <a href=?manage_kelas>Manage Kelas</a>");
    } else {
      while ($d = mysqli_fetch_assoc($q)) {
        $secondary = $d['kelas'] == $target_kelas ? 'primary' : 'secondary';
        echo "<div><button class='btn btn-$secondary mb2' name=btn_set_target_kelas value='$d[kelas]'>$d[kelas]</button></div>";
      }
    }


    ?>
  </form>

</div>