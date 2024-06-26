<?php
$s = "SELECT * FROM tb_poin WHERE id_room=$id_room";
echo $s;
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$s = "SELECT a.* 
FROM tb_poin a WHERE id_room='$id_room'";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
if (mysqli_num_rows($q)) {
  $i = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    foreach ($d as $key => $value) {
      if (
        $key == 'id'
        || $key == 'date_created'
      ) continue;

      $kolom = key2kolom($key);
      $tr .= "
        <tr>
          <td>$kolom</td>
          <td>$value</td>
        </tr>
      ";
    }
  }
}

$tb = $tr ? "
  <table class=table>
    $tr
  </table>
" : div_alert('danger', "Data poin tidak ditemukan.");
echo "$tb";
