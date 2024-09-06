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
$s = "SELECT 
a.*
FROM tb_sesi a WHERE id_room=$id_room 
ORDER BY a.no";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$total_sesi = mysqli_num_rows($q);
$tr = '';
$nav = '';
$no_sesi_normal = 0;
$i = 0;
$count_poin_rps = 0;
$total_count_poin_rps = mysqli_num_rows($q) * count($koloms_rps);
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $id_sesi = $d['id'];
  if ($d['no'] != $i) {
    # ============================================================
    # AUTO UPDATE URUTAN
    # ============================================================
    $s2 = "UPDATE tb_sesi SET no=$i WHERE id=$d[id]";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    $d['no'] = $i;
  }

  // $d['']


  if ($d['jenis'] == 1) { // if sesi normal
    # ============================================================
    # SESI NORMAL
    # ============================================================
    $no_sesi_normal++;

    $td_rps = '';
    foreach ($koloms_rps as $kolom) {
      if ($d[$kolom]) {
        $count_poin_rps++;
        $tmp = explode(';', $d[$kolom]);
        if (count($tmp) >= 2) {
          $li = '';
          foreach ($tmp as $item) {
            if (trim($item)) {
              $li .= "<li>$item</li>";
            }
          }
          $list_rps[$kolom] = "<ul class=list_rps>$li</ul>";
        } else {
          $list_rps[$kolom] = $d[$kolom];
        }
      } else {
        $list_rps[$kolom] = $belum_ada;
      }
      $td_rps .= "<td class='td_rps f12' valign=top>$list_rps[$kolom]</td>";
    }


    $tr .= "
      <tr class='border-top'>
        <td>$no_sesi_normal</td>
        <td colspan=100%>
          <div class='mt4 mb2'>$d[nama] <span class=btn_aksi id=tr_editing$id_sesi" . "__toggle>$img_edit</span></div>
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        $td_rps
      </tr>
      <tr class=hideit id=tr_editing$id_sesi>
        <td>&nbsp;</td>
        <td>
          <textarea 
            class='form-control input_editable mb1' 
            rows=7 
            name=sub_cpmk 
            id=sub_cpmk__$d[id] 
            placeholder='sub_cpmk sesi...'
          >$d[sub_cpmk]</textarea>

        </td>
        <td>
          <textarea 
            class='form-control input_editable mb1' 
            rows=7 
            name=indikator 
            id=indikator__$d[id] 
            placeholder='indikator sesi...'
          >$d[indikator]</textarea>
        </td>
        <td>
          <textarea 
            class='form-control input_editable mb1' 
            rows=7 
            name=bentuk_penilaian 
            id=bentuk_penilaian__$d[id] 
            placeholder='bentuk_penilaian sesi...'
          >$d[bentuk_penilaian]</textarea>
        </td>
        <td>
          <textarea 
            class='form-control input_editable mb1' 
            rows=7 
            name=durasi_rps 
            id=durasi_rps__$d[id] 
            placeholder='durasi_rps sesi...'
          >$d[durasi_rps]</textarea>
        </td>
        <td>
          <textarea 
            class='form-control input_editable mb1' 
            rows=7 
            name=materi_pembelajaran 
            id=materi_pembelajaran__$d[id] 
            placeholder='materi_pembelajaran sesi...'
          >$d[materi_pembelajaran]</textarea>
        </td>
        <td>
          <textarea 
            class='form-control input_editable mb1' 
            rows=7 
            name=bobot_persentase 
            id=bobot_persentase__$d[id] 
            placeholder='bobot_persentase sesi...'
          >$d[bobot_persentase]</textarea>
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td colspan=100%>
          <div class='abu f12 miring abu mb4 mt1'>)* pisahkan list item dengan titik koma</div>
        </td>
      </tr>
    ";
  }
}

$total_sesi++;
$btn_download_rps = $count_poin_rps == $total_count_poin_rps ? "<button onclick='return confirm(`Fitur Download RPS masih dalam tahap pengembangan. Silahkan hubungi developer untuk keterangan lebih lanjut.`)' class=' ml4 btn btn-sm btn-primary'>Download RPS</button>" : "<button onclick='return confirm(`Silahkan lengkapi dahulu yang belum ada.`)' class=' ml4 btn btn-sm btn-secondary'>Download RPS</button>";

echo "
  <style>
    .th_sticky{position: sticky; top:-15px; background: #cff;}
    .th_sticky th {padding: 10px; }
    .list_rps {font-size: 12px; padding:0; padding-left: 10px; }
    .tb_rps{width:100%}
    .td_rps{padding: 10px; border: solid 1px #ccc}
  </style>
  <div>
    $nav
  </div>
  <input type=hidden name=new_no value=$total_sesi>
  <div class='wadah gradasi-hijau' style='height: 60vh; overflow-y:scroll; position: relative'>
    <table class=tb_rps>
      <thead class='th_sticky f12' >
        <th>P</th>
        <th>SUB-CP-MK</th>
        <th>INDIKATOR</th>
        <th>BENTUK PENILAIAN</th>
        <th>WAKTU</th>
        <th>MATERI PEMBELAJARAN</th>
        <th>BOBOT</th>
      </thead>
      $tr
    </table>
  </div>
  <div class='tengah abu'>Kelengkapan RPS $count_poin_rps of $total_count_poin_rps $btn_download_rps
  </div>
";
