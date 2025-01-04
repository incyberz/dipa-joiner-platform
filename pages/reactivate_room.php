<?php
set_h2('reactivate_room');
$session_id_room =  $_SESSION['dipa_id_room'] ?? '';
$id_room = $_GET['id_room'] ?? $session_id_room;
if (!$id_room) die(erid('id_room'));



# ============================================================
# PROCESSORS
# ============================================================
if (isset($_POST['btn_reactivate_room'])) {
  $s = "UPDATE tb_room SET status=NULL, ta = $_POST[ta]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
}

# ============================================================
# SELECT TA AVAILABLE 
# ============================================================
$s = "SELECT * FROM tb_ta WHERE ta > $room[ta]";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$count_available = mysqli_num_rows($q);
$radios = '';
if ($count_available) {
  while ($d = mysqli_fetch_assoc($q)) {
    $radios .= "
      <div>
        <label class='btn btn-primary mb2'>
          <input required type=radio name=ta value=$d[ta]> $d[ta]
        </label>
      </div>
    ";
  }
} else {
  $radios = div_alert('danger', "Belum ada Tahun Ajar baru yang tersedia.");
}


echo "
  <form method=post>
    <table class=table>
      <tr>
        <td>Room</td>
        <td>$room[nama]</td>
      </tr>
      <tr>
        <td>Status</td>
        <td>$room[status]</td>
      </tr>
      <tr>
        <td>Tahun Ajar</td>
        <td>$room[ta]</td>
      </tr>
      <tr>
        <td>Aktifkan $Room untuk TA</td>
        <td>
          $radios
        </td>
      </tr>
    </table>
    <button class='btn btn-primary w-100' name=btn_reactivate_room>Reactivate $Room</button>
  </form>
";
