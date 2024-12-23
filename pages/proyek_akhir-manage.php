<?php
$img_manage = img_icon('manage');

$sub_proyek = '';
$s = "SELECT * FROM tb_sub_proyek WHERE id_room=$id_room";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$i = 0;
if (!mysqli_num_rows($q)) {
  $sub_proyek = div_alert('danger', "Belum ada satupun [ Sub Proyek ]");
}
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $sub_proyek .= "
    <div>$i. $d[sub_proyek]</div>
  ";
}

$add_sub_proyek = "
  <form method=post>
    <input class='form-control '>
    <button class='btn btn-primary'>Add</button>
  </form>
";

echo "
  <div class=mb2>
    <span class='btn btn-sm bordered btn_aksi' id=manage_proyek_akhir__toggle>$img_manage Manage Proyek Akhir</span>
  </div>
  <div class='wadah mt2 gradasi-kuning' id=manage_proyek_akhir>
    $sub_proyek
    $add_sub_proyek
  </div>
";
