<?php
# ============================================================
# URUTAN DAN DESKRIPSI
# ============================================================

$r = explode(', ', $d_sesi['tags']);
sort($r);
$tags_sort = implode(', ', $r);
$tags_show = "
  <textarea 
    class='form-control input_editable mb1' 
    rows=5 
    name=tags 
    id=tags__$id_sesi 
    placeholder='tag-tag materi...'
  >$d_sesi[tags]</textarea>
";

$s = "SELECT 
a.id, 
a.nama, 
a.jenis, 
a.deskripsi 
FROM tb_sesi a WHERE id_room=$id_room";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$divs = '';
$nav = '';
$no_sesi_normal = 0;
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  # ============================================================
  # NAVIGASI
  # ============================================================
  if ($d['jenis'] == 1) {
    $no_sesi_normal++;
    // $nav .= "<span class='btn btn-success btn-sm'>P$no_sesi_normal</span> ";
  } else {
    // $nav .= "<span class='btn btn-secondary btn-sm'>P ZZZ</span> ";
  }

  $input_nama = "
    <input 
      class='form-control input_editable mb1' 
      name=nama 
      id=nama__$d[id] 
      value='$d[nama]'
    />
  ";
  $input_deskripsi = "
    <textarea 
      class='form-control input_editable mb1' 
      rows=5 
      name=deskripsi 
      id=deskripsi__$d[id] 
      placeholder='deskripsi sesi...'
    >$d[deskripsi]</textarea>
  ";

  $divs .= "
    <tr>
      <td>$i $img_up $img_down</td>
      <td>
        <div>$input_nama</div>
        <div class='hideit'>$input_deskripsi</div>
      </td>
      <td>$img_edit</td>
    </tr>
  ";
}

echo "
  <div>
    $nav
  </div>
  <table class=table>
    $divs
  </table>
";
