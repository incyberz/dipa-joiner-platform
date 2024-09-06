<?php
# ============================================================
# URUTAN DAN DESKRIPSI
# ============================================================
$belum_ada = '<span class="red miring f12">belum ada</span>';

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
  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';
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

























# ============================================================
# KOLOM UNTUK RPS
# ============================================================
$koloms_rps = ['sub_cpmk', 'indikator', 'bentuk_penilaian', 'durasi_rps', 'materi_pembelajaran', 'bobot_persentase'];
$list_rps = [];

# ============================================================
# LIST ALL SESI
# ============================================================
$s = "SELECT * FROM tb_bahan_ajar WHERE id_room=$id_room";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$total_bahan_ajar = mysqli_num_rows($q);
$tr = '';
$nav = '';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $id_sesi = $d['id'];
  $tr .= "
    <tr class='border-top'>
      <td>$i</td>
      <td colspan=100%>
        $d[zzz] <span class=btn_aksi id=tr_editing$id_sesi" . "__toggle>$img_edit ZZZ</span>
      </td>
    </tr>
  ";
}

$tr = $tr ? $tr : div_alert('danger', "Belum ada Bahan Ajar untuk Room ini.");

$ondev = div_alert('danger', "Untuk fitur ini masih dalam tahap pengembangan. terimakasih.");
echo "
  <div>
    $nav
  </div>
  <table class=table>$tr</table>
  $ondev
  ";
