<?php
if (!$id_room) die(erid('id_room'));
instruktur_only();

$get_ta = $_GET['ta'] ?? $ta;

$s = "SELECT ta FROM tb_ta";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$nav_ta = '';
while ($d = mysqli_fetch_assoc($q)) {
  $current = $d['ta'] == $get_ta ? 'blue bold' : '';
  $slash = $nav_ta ? ' | ' : '';
  $nav_ta .= "$slash<a href='?assign_room_kelas&ta=$d[ta]'><span class='$current'>$d[ta]</span></a>";
}


set_h2("Assign $Room Kelas", "$nav_ta");
// $room['status'] = 5;
// $status_room = 5;
// include "$lokasi_pages/aktivasi_room.php";


# ====================================================
# PROCESSOR: ASSIGN ROOM KELAS
# ====================================================
if (isset($_POST['btn_assign_room_kelas'])) {
  $kelas = $_POST['btn_assign_room_kelas'];
  $s = "SELECT 1 FROM tb_room_kelas WHERE id_room=$id_room AND kelas='$kelas'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (!mysqli_num_rows($q)) {
    $s = "INSERT INTO tb_room_kelas (kelas,id_room,ta) VALUES ('$kelas',$id_room,$ta)";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo div_alert('success', "Assign $Room Kelas sukses.");
  } else {
    echo div_alert('danger', "Kelas sudah terdaftar pada $Room ini.");
  }
}

# ====================================================
# PROCESSOR: DROP ROOM KELAS
# ====================================================
if (isset($_POST['btn_drop_room_kelas'])) {

  echo div_alert('danger', "DROPPING ROOM KELAS dapat berhasil jika dan hanya jika tidak ada $Peserta yang terdaftar pada kelas ini.<hr>Hubungi Master $Trainer (Developer) Jika ingin menghapus kelas aktif (yang sudah berjalan) dari $Room ini.");

  $s = "DELETE FROM tb_room_kelas WHERE id_room=$id_room AND kelas='$_POST[btn_drop_room_kelas]'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', "Drop $Room Kelas sukses.");
}

$s = "SELECT a.id as id_room_kelas,a.kelas,b.fakultas 
FROM tb_room_kelas a JOIN tb_kelas b ON a.kelas=b.kelas 
WHERE a.id_room=$id_room 
AND a.kelas != 'INSTRUKTUR'
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$li_assigned = '';
$arr_assigned_kelas = [];
while ($d = mysqli_fetch_assoc($q)) {
  $btn = "<button class='btn btn-danger btn-sm mb2' name=btn_drop_room_kelas value='$d[kelas]' >Drop</button>";
  $li_assigned .= "
    <li class=''>
      $d[fakultas] ~ 
      $d[kelas] | 
      $btn 
      <a class='btn btn-info btn-sm' href='?assign_peserta_kelas&kelas=$d[kelas]'>+Peserta</a> 
    </li>
  ";
  array_push($arr_assigned_kelas, $d['kelas']);
}

?>

<table>
  <tr>
    <td valign=top>
      <div class="wadah">
        <div class="mb2">Available Grup Kelas pada TA <?= $get_ta ?> : </div>
        <form method=post>
          <ol>
            <?php

            $s = "SELECT * FROM tb_kelas 
            WHERE ta=$get_ta 
            AND kelas != 'INSTRUKTUR'
            ORDER BY status DESC,fakultas,semester,prodi,shift";
            $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

            while ($d = mysqli_fetch_assoc($q)) {
              if (in_array($d['kelas'], $arr_assigned_kelas)) continue;
              $color = $d['shift'] == 'P' ? 'blue' : 'darkred';
              $color = $d['status'] ? $color : 'abu miring';
              $btn = $d['status'] ? "<button class='btn btn-primary btn-sm mb2' name=btn_assign_room_kelas value='$d[kelas]' >Assign</button>" : '<span class="f10 abu miring consolas">kelas tidak aktif</span>';
              echo "<li class='$color'>$d[fakultas] ~ $d[kelas] $d[shift] ~ $btn</li>";
            }
            ?>
          </ol>
        </form>
      </div>
    </td>
    <td valign=top>
      <div class="wadah ml2">
        <div>Assigned Kelas pada <?= $Room ?> <?= $singkatan_room ?> :</div>
        <form method=post>
          <?php
          echo $li_assigned ? "<ol>$li_assigned</ol>" : "<div class='red mt2 f12 consolas miring'>Belum ada kelas pada $Room ini.</div>";
          ?>
        </form>
      </div>

    </td>
  </tr>
</table>