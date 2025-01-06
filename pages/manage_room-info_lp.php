<?php
$s = "SELECT * FROM tb_sesi a WHERE a.id_room=$id_room AND jenis=1 ORDER BY no";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $tags_ok = $d['tags'] ? "Tags $img_check" : "<span class=red>Tag materi belum ada</span> $img_warning";
  $deskripsi_ok = $d['deskripsi'] ? "Deskripsi $img_check" : "<span class=red>Deskripsi belum ada</span> $img_warning";
  $tr .= "
    <tr id=tr__$d[id]>
      <td>P$i</td>
      <td>$d[nama]</td>
      <td><a target=_blank href='?lp&id_sesi=$d[id]'>$deskripsi_ok</a></td>
      <td><a target=_blank href='?lp&id_sesi=$d[id]'>$tags_ok</a></td>
    </tr>
  ";
}

$info_lp = "
  <div class='wadah gradasi-toska'>
    <h3>Info Learning Path</h3>
    <table class='table'>$tr</table>
  </div>
";
