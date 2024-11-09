<?php
# ============================================================
# URUTAN DAN DESKRIPSI
# ============================================================
$img_up = img_icon('up');
$img_down = img_icon('down');
$img_gray = img_icon('gray');
$img_gray = "<span class='ml1'>$img_gray</span>";

# ============================================================
# JENIS SESI | SELECT JENIS
# ============================================================
$s = "SELECT * FROM tb_jenis_sesi";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$arr_jenis_sesi = [];
$opt = '';
while ($d = mysqli_fetch_assoc($q)) {
  $arr_jenis_sesi[$d['jenis']] = $d['nama'];
  $opt .= "<option value='$d[jenis]'>Tambah $d[nama]</option>";
}
$select_jenis = "<select class='form-control' name=jenis>$opt</select>";
























# ============================================================
# PROCESSORS
# ============================================================
if (isset($_POST['btn_move_to'])) {

  $arr = explode('__', $_POST['btn_move_to']);
  $aksi = $arr[0];
  $id_sesi = $arr[1];
  $no = $arr[2];
  if (!$aksi || !$id_sesi || !$no) die(div_alert('danger', "Input invalid: aksi: $aksi, id_sesi: $id_sesi, no: $no "));
  if ($aksi == 'up') {
    $new_no = $no - 1;
    // update sesi target
    $s = "UPDATE tb_sesi SET no=$no WHERE no=$new_no AND id_room=$id_room";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

    // update current sesi
    $s = "UPDATE tb_sesi SET no=$new_no WHERE id=$id_sesi ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  } elseif ($aksi == 'down') {
  } else {
    die(div_alert('danger', "Belum ada handler untuk aksi $aksi"));
  }
}

if (isset($_POST['btn_add_sesi'])) {
  $nama = $arr_jenis_sesi[$_POST['jenis']];
  $s = "INSERT INTO tb_sesi (
    id_room,
    jenis,
    no,
    nama
  ) VALUES (
    $id_room,
    $_POST[jenis],
    $_POST[new_no],
    '$nama'
  )";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', 'Tambah sesi sukses.');
  jsurl('', 1000);
  exit;
}


if (isset($_POST['btn_delete_sesi'])) {

  $id = $_POST['btn_delete_sesi'];
  if ($id > 0) {

    $s = "SELECT jenis FROM tb_sesi WHERE id=$id";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $d = mysqli_fetch_assoc($q);

    if ($d['jenis'] !== '1') { // sesi tenang
      echo '<hr>SESI TENANG | UTS | UAS';
      $s = "DELETE FROM tb_sesi_kelas WHERE id_sesi=$id";
      echo "<hr>$s";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    }


    $s = "DELETE FROM tb_sesi WHERE id=$_POST[btn_delete_sesi]";
    echo "<hr>$s";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

    $s = "SELECT * FROM tb_sesi WHERE id_room=$id_room ORDER BY no";
    echo "<hr>$s";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $jumlah_sesi = mysqli_num_rows($q);
    $i = 0;
    while ($d = mysqli_fetch_assoc($q)) {
      $i++;
      $s2 = "UPDATE tb_sesi SET no=$i WHERE id=$d[id]";
      echo "<hr>$s2";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    }
  }

  echo div_alert('success', 'Hapus sesi sukses.');
  jsurl('', 1000);
  exit;
}




























# ============================================================
# LIST ALL SESI
# ============================================================
$s = "SELECT a.* 
FROM tb_sesi a WHERE id_room=$id_room 
ORDER BY a.no";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$total_sesi = mysqli_num_rows($q);
$tr = '';
$nav = '';
$no_sesi_normal = 0;
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  if ($d['no'] != $i) {
    # ============================================================
    # AUTO UPDATE URUTAN
    # ============================================================
    $s2 = "UPDATE tb_sesi SET no=$i WHERE id=$d[id]";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    $d['no'] = $i;
  }

  $btn_up = $d['no'] == 1 ? $img_gray : "<button class=btn-transparan name=btn_move_to value=up__$d[id]__$d[no]>$img_up</button>";

  if ($d['jenis'] == 1) {
    # ============================================================
    # SESI NORMAL
    # ============================================================
    $no_sesi_normal++;
    $no_sesi_normal_show = $no_sesi_normal;
    $gradasi = 'gradasi-hijau';
    $btn_edit = "<span class='btn_aksi' id=input_deskripsi_$d[id]__toggle>$img_edit</span>";


    $input_nama = "
      <input 
        class='form-control input_editable mb1' 
        name=nama 
        id=nama__$d[id] 
        value='$d[nama]'
      />
    ";
  } else { // sesi non-normal
    $no_sesi_normal_show = '&nbsp;';
    $input_deskripsi = '';
    $gradasi = 'gradasi-abu';
    $btn_edit = '&nbsp;';
    if ($d['jenis'] == 0) {
      $input_nama = 'minggu tenang';
    } elseif ($d['jenis'] == 2) {
      $input_nama = 'pekan UTS';
    } elseif ($d['jenis'] == 3) {
      $input_nama = 'pekan UAS';
    } else {
      die(div_alert('danger', "Jenis sesi invalid: $d[jenis]"));
    }
    $input_nama = "<div class='tengah p2 abu miring'>$input_nama</div>";
  }

  $info_id_sesi = $username == 'abi' ? "<span class='f10 abu miring'>id: $d[id]</span>" : '';

  $tr .= "
    <tr class='$gradasi'>
      <td>$no_sesi_normal_show</td>
      <td>
        <div>$input_nama</div>
      </td>
      <td>
        $btn_up 
        <span class='f14 abu'>$d[no]</span>
        <form method=post style='display:inline-block'>
          <button class='btn-transparan' onclick='return confirm(`Yakin hapus sesi ini?`)' name=btn_delete_sesi value=$d[id]>$img_delete</button>
        </form> 
        $info_id_sesi
      </td>
    </tr>
  ";
}

$total_sesi++;

echo "
  <div>
    $nav
  </div>
  <form method=post>
    <input type=hidden name=new_no value=$total_sesi>
    <table class=table>
      <thead>
        <th>P</th>
        <th>Sesi</th>
        <th>Manage</th>
      </thead>
      $tr
      <tr>
        <td>$img_add</td>
        <td colspan=100%>
          <div class=flexy>
            <div>
              $select_jenis
            </div>
            <div>
              <button name=btn_add_sesi class='btn btn-success btn-sm' onclick='return confirm(`Tambah sesi baru?`)'>Add</button>
            </div>
          </div>
        </td>
      </tr>
    </table>
  </form>
";
