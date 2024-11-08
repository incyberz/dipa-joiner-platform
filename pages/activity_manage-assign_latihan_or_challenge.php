<hr>
<?php
# ============================================================
# GET ARRAY DATA FROM DB
# ============================================================
$s = "SELECT id,no,nama FROM tb_sesi WHERE id_room=$id_room";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$arr_id_sesi = [];
$arr_sesi = [];
while ($d = mysqli_fetch_assoc($q)) {
  array_push($arr_id_sesi, $d['id']);
  array_push($arr_sesi, "P$d[no] $d[nama]");
}

$s = "SELECT a.kelas,a.id as id_room_kelas FROM tb_room_kelas a 
JOIN tb_kelas b ON a.kelas=b.kelas  
WHERE a.id_room=$id_room AND b.status=1
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$li = '';
$arr_id_room_kelas = [];
while ($d = mysqli_fetch_assoc($q)) {
  array_push($arr_id_room_kelas, $d['id_room_kelas']);
  $li .= "<li>$d[kelas]</li>";
}

$grup_kelas_pengakses = "
  <h3 class='darkblue'>Grup Kelas Pengakses $Jenis</h3>
  <p>$Jenis yang Anda buat akan dapat diakses oleh kelas:</p>
  <ol>$li</ol>
  <div class='ml2 pl1 f14'>Opsi: <a href='?assign_room_kelas'>Assign Room Kelas</a></div>
  <hr>
";





























# ======================================================
# PROCESSOR :: ASSIGN
# ======================================================
if (isset($_POST['btn_assign_sesi'])) {
  $arr = explode('__', $_POST['btn_assign_sesi']);
  $id_jenis = $arr[0];
  $id_sesi = $arr[1];

  $pesan = '';
  foreach ($arr_id_room_kelas as $id_rk) {
    $s = "SELECT 1 FROM tb_assign_$jenis WHERE id_$jenis=$id_jenis AND id_room_kelas='$id_rk'";
    // die($s);
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    if (mysqli_num_rows($q)) {
      $s = "UPDATE tb_assign_$jenis SET id_sesi=$id_sesi  WHERE id_$jenis=$id_jenis AND id_room_kelas='$id_rk'";
    } else {
      $s = "INSERT INTO tb_assign_$jenis (id_$jenis,id_sesi,id_room_kelas) VALUES ($id_jenis,$id_sesi,'$id_rk')";
    }
    $pesan .= "<br>executing $s ...";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $pesan .= "success";
  }
  echo "<div class='wadah gradasi-hijau'>$pesan</div>";
  jsurl();
  exit;
}

# ======================================================
# PROCESSOR :: DROP
# ======================================================
if (isset($_POST["btn_drop_$jenis"])) {
  $id_jenis = $_POST["btn_drop_$jenis"];
  $s = "DELETE FROM tb_assign_$jenis WHERE id_$jenis=$id_jenis";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo "<div class='wadah gradasi-hijau'>Drop $jenis from all kelas ... OK</div>";
  jsurl();
}
# ======================================================
# PROCESSOR :: DELETE
# ======================================================
if (isset($_POST["btn_delete_$jenis"])) {
  $id_jenis = $_POST["btn_delete_$jenis"];
  $s = "DELETE FROM tb_$jenis WHERE id=$id_jenis";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo "<div class='wadah gradasi-hijau'>delete $jenis from all kelas ... OK</div>";
  jsurl();
}


# ======================================================
# PROCESSOR :: CLOSE / OPEN
# ======================================================
if (isset($_POST["btn_close_$jenis"]) || isset($_POST["btn_open_$jenis"])) {
  $status = isset($_POST["btn_close_$jenis"]) ? -1 : 1;
  $id_jenis = $_POST["btn_close_$jenis"] ??  $_POST["btn_open_$jenis"];
  $s = "UPDATE tb_$jenis SET status=$status WHERE id=$id_jenis";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo "<div class='wadah gradasi-hijau'>close $jenis from all kelas ... OK</div>";
  jsurl();
}


# ======================================================
# PROCESSOR :: CLOSE / OPEN ALL
# ======================================================
if (isset($_POST['btn_set_all'])) {
  $status = $_POST['btn_set_all'];
  $s = "UPDATE tb_$jenis SET status=$status WHERE id_room=$id_room";
  echo '<pre>';
  var_dump($s);
  echo '</pre>';
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo "<div class='wadah gradasi-hijau'>Set All Status of $jenis ... OK</div>";
  jsurl();
}


if (isset($_POST['btn_add_activity'])) {
  $s = "SELECT 1 FROM tb_$jenis WHERE nama='$_POST[nama]' and id_room=$id_room";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    echo div_alert('danger', "Nama Challenge sudah ada pada room ini.");
  } else {
    $s = "INSERT INTO tb_$jenis (nama,id_room) VALUES ('$_POST[nama]',$id_room)";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    jsurl();
  }
}




































































# ======================================================
# LIST LATIHAN
# ======================================================
$s = "SELECT a.*,
a.status as status_jenis,
(
  SELECT id_sesi FROM tb_assign_$jenis 
  WHERE id_$jenis=a.id LIMIT 1) id_sesi_assigned,  
(
  SELECT q.no FROM tb_assign_$jenis p 
  JOIN tb_sesi q ON p.id_sesi=q.id 
  WHERE p.id_$jenis=a.id LIMIT 1) no_sesi,  
(
  SELECT COUNT(1) FROM tb_bukti_$jenis p 
  JOIN tb_assign_$jenis q ON p.id_assign_$jenis=q.id  
  WHERE q.id_$jenis=a.id ) count_bukti  
FROM tb_$jenis a 
WHERE a.id_room=$id_room 
ORDER BY no_sesi, a.nama 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$tr = '';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;


  $assigned_to = '';
  $assigned_sesi = $unset;
  foreach ($arr_sesi as $key => $sesi) {
    if ($arr_id_sesi[$key] == $d['id_sesi_assigned']) {
      $assigned_sesi = $sesi;
      $primary = 'primary';
    } else {
      $primary = 'secondary';
    }
    $dual_id = $d['id'] . "__$arr_id_sesi[$key]";
    $assigned_to .= "<div class=mb1><button class='btn btn-$primary btn-sm' name=btn_assign_sesi value='$dual_id'>$sesi</button></div>";
  }
  $is_set = $assigned_sesi == $unset ? false : true;


  $img_detail = img_icon('detail');

  // wrapping td_sesi
  $assigned_to = "
    <td>
      <div class=mb2>
        $assigned_sesi 
        <span class=btn_aksi id=jenis$d[id]__toggle>$img_detail</span>
      </div>
      <div id=jenis$d[id] class=hideit>
        <div class='wadah'>
          <div class='tebal biru mb2'>Assign $jenis ini ke sesi:</div>
          $assigned_to
        </div>
      </div>
    </td>
  ";

  if ($is_set) {
    if ($d['status_jenis'] == -1) {
      $btn_close = "<button class='btn btn-success btn-sm' name=btn_open_$jenis value=$d[id] >Open</button>";
    } else {
      $btn_close = "<button class='btn btn-danger btn-sm' name=btn_close_$jenis value=$d[id] >Close</button>";
    }
    $btn_drop = "<button class='btn btn-danger btn-sm' name=btn_drop_$jenis value=$d[id] >Drop</button>";
    $btn_delete = '';
  } else {
    $btn_delete = "<button class='btn btn-danger btn-sm' name=btn_delete_$jenis value=$d[id] onclick='return confirm(`Yakin hapus $jenis ini?`)'>Delete</button>";
    $btn_drop = '';
    $btn_close = '';
  }


  # ============================================================
  # FINAL TR OUTPUT
  # ============================================================
  $tr .= "
    <tr>
      <td>$i</td>
      <td>$d[nama]</td>
      $assigned_to
      <td>
        $d[count_bukti]
      </td>
      <td>
        $btn_drop 
        $btn_close 
        $btn_delete 
      </td>
    </tr>
  ";
}

$jumlah_kelas = count($arr_id_room_kelas);
$img_add = img_icon('add');

# ============================================================
# FINAL ECHO
# ============================================================
echo "
<div class='wadah gradasi-kuning'>
  <div class='f14 consolas tebal red mb2'>Form Khusus Instruktur</div>
  
  <form method=post>
    <h3 class=darkblue>Manage Assign $Jenis</h3>
    <table class=table>
      <thead>
        <th>No</th>
        <th class=proper>$jenis</th>
        <th>Assigned to</th>
        <th>Count Bukti</th>
        <th width=200px>Aksi</th>
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
  <form method=post class='wadah mt4'>
    <div class='proper mb2'>Close/Open All $jenis</div>
    <button class='btn btn-danger btn-sm' name=btn_set_all value=-1>Close All</button>
    <button class='btn btn-warning btn-sm' name=btn_set_all value=1>Open All</button>
  </form>

  $grup_kelas_pengakses
</div>
";
