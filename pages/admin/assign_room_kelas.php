<?php
if(!$id_room) die(erid('id_room'));
instruktur_only();

# ====================================================
# PROCESSOR: ASSIGN ROOM KELAS
# ====================================================
if(isset($_POST['btn_assign_room_kelas'])){
  $kelas = $_POST['btn_assign_room_kelas'];
  $s = "SELECT 1 FROM tb_room_kelas WHERE id_room=$id_room AND kelas='$kelas'";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(!mysqli_num_rows($q)){
    $s = "INSERT INTO tb_room_kelas (kelas,id_room) VALUES ('$kelas',$id_room)";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
    echo div_alert('success',"Assign Room Kelas sukses.");
  }else{
    echo div_alert('danger',"Kelas sudah terdaftar pada room ini.");
  }
}

# ====================================================
# PROCESSOR: DROP ROOM KELAS
# ====================================================
if(isset($_POST['btn_drop_room_kelas'])){
  $s = "DELETE FROM tb_room_kelas WHERE id_room=$id_room AND kelas='$_POST[btn_drop_room_kelas]'";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  echo div_alert('success',"Drop Room Kelas sukses.");
}

$s = "SELECT a.id as id_room_kelas,a.kelas,b.fakultas FROM tb_room_kelas a JOIN tb_kelas b ON a.kelas=b.kelas WHERE a.id_room=$id_room";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$li_assigned = '';
$arr_assigned_kelas = [];
while($d=mysqli_fetch_assoc($q)){
  $btn = "<button class='mb2' name=btn_drop_room_kelas value='$d[kelas]' >Drop</button>";
  $li_assigned.= "
    <li class=''>
      $d[fakultas] ~ 
      $d[kelas] ~ 
      $btn ~ 
      <a href='?assign_peserta_kelas&kelas=$d[kelas]'>Assign Peserta Kelas</a>  ~ 
    </li>
  ";
  array_push($arr_assigned_kelas,$d['kelas']);
}

?>
<h1>Assign Room Kelas</h1>
<div class="flexy">
  <div class="wadah">
    Kelas
  </div>
  <div class="wadah">--></div>
  <div class="wadah">
    Room
  </div>
</div>

<table>
  <tr>
    <td valign=top>
      <div class="wadah">
        <div class="mb2">Available Grup Kelas pada TA <?=$tahun_ajar?> : </div>
        <form method=post>
          <ol>
            <?php

            $s = "SELECT * FROM tb_kelas WHERE tahun_ajar=$tahun_ajar ORDER BY status DESC,fakultas,semester,prodi,shift";
            $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

            while($d=mysqli_fetch_assoc($q)){
              if(in_array($d['kelas'],$arr_assigned_kelas)) continue;
              $color = $d['shift']=='P' ? 'blue' : 'darkred';
              $color = $d['status'] ? $color : 'abu miring';
              $btn = $d['status'] ? "<button class='mb2' name=btn_assign_room_kelas value='$d[kelas]' >Assign</button>" : '<span class="f10 abu miring consolas">kelas tidak aktif</span>';
              echo "<li class='$color'>$d[fakultas] ~ $d[kelas] $d[shift] ~ $btn</li>";
            }
            ?>
          </ol>   
        </form>
      </div>
    </td>
    <td valign=top>
      <div class="wadah">
        <div>Assigned Kelas pada Room <?=$room?> :</div>
        <form method=post>
          <?php
          echo $li_assigned ? "<ol>$li_assigned</ol>" : '<div class="red mt2 f12 consolas miring">Belum ada kelas pada Room ini.</div>';
          ?>
        </form>
      </div>

    </td>
  </tr>
</table>
