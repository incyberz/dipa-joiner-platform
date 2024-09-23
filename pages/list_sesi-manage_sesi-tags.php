<?php
# ============================================================
# URUTAN DAN DESKRIPSI
# ============================================================

$s = "SELECT 
a.*
FROM tb_sesi a WHERE id_room=$id_room and jenis=1";
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

  $divs .= "
    <tr>
      <td>$i</td>
      <td>
        <div><input class='form-control input_editable mb1' name=nama id=nama__$d[id] value='$d[nama]' /></div>
        <div class='mb1 f12 mt4 miring'>Tags:</div>
        <div><input class='form-control input_editable mb1' name=tags id=tags__$d[id] value='$d[tags]' /></div>
        <div class='mb1 f12 mt4 miring'>Deskripsi:</div>
        <div class=''>
          <textarea 
            class='form-control input_editable mb1' 
            rows=5 
            name=deskripsi 
            id=deskripsi__$d[id] 
            placeholder='deskripsi sesi...'
          >$d[deskripsi]</textarea>          
        </div>
      </td>
    </tr>
  ";
}

echo "
  <div>
    $nav
  </div>
  <div class='alert alert-info biru bold tengah'><span class=biru>Tags digunakan agar peserta tidak \"Out of Topic\" dalam membuat Soal PG sebagai syarat presensi.</span></div>
  <div class='gradasi-hijau br5 p1 pt0 br10'>
    <table class='table td_trans td_toska '>
      $divs
    </table>
  </div>
";
