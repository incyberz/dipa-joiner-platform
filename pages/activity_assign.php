<hr>
<?php
if(isset($_POST['btn_add_activity'])){
  $s = "SELECT 1 FROM tb_$jenis WHERE nama='$_POST[nama]' and id_room=$id_room";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)){
    echo div_alert('danger',"Nama Challenge sudah ada pada room ini.");
  }else{
    $s = "INSERT INTO tb_$jenis (nama,id_room) VALUES ('$_POST[nama]',$id_room)";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
    jsurl();
  }
}
?>
<div class="wadah">
  <h2 class='abu f12 consolas'>Activity For Admin</h2>
  <h3 class='darkblue'>Assigning for Activity</h3>
  <p>Room ini diikuti oleh kelas</p>
  <?php
  $s = "SELECT id,no,nama FROM tb_sesi WHERE id_room=$id_room";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $arr_id_sesi = [];
  $arr_sesi = [];
  while($d=mysqli_fetch_assoc($q)){
    array_push($arr_id_sesi,$d['id']);
    array_push($arr_sesi,"P$d[no] $d[nama]");
  }

  $s = "SELECT a.kelas,a.id as id_room_kelas FROM tb_room_kelas a 
  JOIN tb_kelas b ON a.kelas=b.kelas  
  WHERE a.id_room=$id_room AND b.status=1
  ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $li='';
  $arr_id_room_kelas = [];
  while($d=mysqli_fetch_assoc($q)){
    array_push($arr_id_room_kelas,$d['id_room_kelas']);
    $li.="<li>$d[kelas]</li>";
  }
  echo "
    <ol>$li</ol>
    <div class='ml2 pl4'>Opsi: <a href='?assign_room_kelas'>Assign Room Kelas</a></div>
    <hr>
  ";



  # ======================================================
  # PROCESSOR :: ASSIGN
  # ======================================================
  if(isset($_POST['btn_assign_sesi'])){
    $arr = explode('__',$_POST['btn_assign_sesi']);
    $id_jenis = $arr[0];
    $id_sesi = $arr[1];

    
    
    $pesan='';
    foreach ($arr_id_room_kelas as $id_rk) {
      $s = "SELECT 1 FROM tb_assign_$jenis WHERE id_$jenis=$id_jenis AND id_room_kelas='$id_rk'";
      // die($s);
      $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
      if(mysqli_num_rows($q)){
        $s = "UPDATE tb_assign_$jenis SET id_sesi=$id_sesi  WHERE id_$jenis=$id_jenis AND id_room_kelas='$id_rk'";
      }else{
        $s = "INSERT INTO tb_assign_$jenis (id_$jenis,id_sesi,id_room_kelas) VALUES ($id_jenis,$id_sesi,'$id_rk')";
      }
      $pesan.= "<br>executing $s ...";
      $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
      $pesan.= "success";
    }
    echo "<div class='wadah gradasi-hijau'>$pesan</div>";
    jsurl();
  }

  # ======================================================
  # PROCESSOR :: DROP
  # ======================================================
  if(isset($_POST["btn_drop_$jenis"])){
    $id_jenis = $_POST["btn_drop_$jenis"];
    $s = "DELETE FROM tb_assign_$jenis WHERE id_$jenis=$id_jenis";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
    echo "<div class='wadah gradasi-hijau'>Drop $jenis from all kelas ... OK</div>";
    jsurl();
  }



  # ======================================================
  # LIST LATIHAN
  # ======================================================
  $s = "SELECT a.*,
  (
    SELECT id_sesi FROM tb_assign_$jenis 
    WHERE id_$jenis=a.id LIMIT 1) id_sesi_assigned  
  FROM tb_$jenis a 
  WHERE a.id_room=$id_room";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $tr='';
  $i=0;
  while($d=mysqli_fetch_assoc($q)){
    $i++;

    
    $td_sesi = '';
    $assigned_sesi = $unset;
    foreach ($arr_sesi as $key => $sesi) {
      if($arr_id_sesi[$key]==$d['id_sesi_assigned']){
        $assigned_sesi = $sesi;
        $primary = 'primary';
      }else{
        $primary = 'secondary';
      }
      $dual_id = $d['id']."__$arr_id_sesi[$key]";
      $td_sesi.= "<div><button class='btn btn-$primary btn-sm' name=btn_assign_sesi value='$dual_id'>$sesi</button></div>";
    }


    $img_detail = img_icon('detail');

    $td_sesi = "
      <td>
        <div class=mb2>
          $assigned_sesi 
          <span class=btn_aksi id=jenis$d[id]__toggle>$img_detail</span>
        </div>
        <div id=jenis$d[id] class=hideit>
          <div class='flexy' style='gap: 5px'>$td_sesi</div>
        </div>
      </td>
    ";

    $tr.= "
      <tr>
        <td>$i</td>
        <td>$d[nama]</td>
        $td_sesi
        <td>
          <button class='btn btn-danger btn-sm' name=btn_drop_$jenis value=$d[id] >Drop</button>
        </td>
      </tr>
    ";
  }

  $jumlah_kelas = count($arr_id_room_kelas);
  $img_add = img_icon('add');

  echo "
    <form method=post>
      <table class=table>
        <thead>
          <th>No</th>
          <th class=proper>$jenis</th>
          <th>Assigned to</th>
          <th>Drop</th>
        </thead>
        $tr
      </table>
    </form>
    <form method=post>
      <div class=flexy>
        <div>
          <span onclick='alert(\"Untuk membuat $jenis baru silahkan input nama $jenis lalu klik tombol Tambah.\")'>$img_add</span>        
        </div>
        <div>
          <input required minlength=5 maxlength=100 class='form-control form-control-sm' name=nama placeholder='nama $jenis'>
        </div>
        <div>
          <button class='btn btn-success btn-sm' name=btn_add_activity >Tambah</button>
        </div>
      </div>
      <div class='abu f12 mt2'>)* Setelah membuat $jenis baru, silahkan Anda assign! Dan untuk editing properti silahkan klik pada salah satu list assigned-$jenis di paling atas</div>
    </form>
  ";



  ?>
</div>