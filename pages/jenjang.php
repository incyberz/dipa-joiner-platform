<?php
mulai($parameter);
$jenjang = $_GET['jenjang'] ?? '';

include 'jenjang-processors.php';

if (!$jenjang) {
  set_h2('manage jenjang');

  $s = "SELECT * FROM tb_room WHERE created_by=$id_peserta ORDER BY jenjang, jenis,no,nama";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $tr = '';
  $i = 0;
  $rjenjang = [
    '' => '--null--',
    'S1' => 'Sarjana',
    'D3' => 'Diploma',
    'SA' => 'SLTA',
    'SP' => 'SLTP',
    'SD' => 'SD',
  ];
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $opts = '';
    foreach ($rjenjang as $JJ => $Jenjang) {
      $selected = $JJ == $d['jenjang'] ? 'selected' : '';
      $opts .= "<option value='$JJ' $selected>$Jenjang</option>";
    }
    $manage = $d['jenjang'] ? "<a href='?jenjang&jenjang=$d[jenjang]'>Manage Jenjang $d[jenjang]</a>" : '-';
    $tr .= "
      <tr id=tr__$d[id]>
        <td>$i</td>
        <td>$d[nama]</td>
        <td>
          <select class='form-control' name=jenjang[$d[id]]>
            $opts
          </select>
        </td>
        <td>
          $manage
        </td>
      </tr>
    ";
  }

  echo "
    <form method=post>
      <table class=table>
        $tr
      </table>
      <button class='btn btn-primary mt2' name=btn_save_jenjang>Save Jenjang</button>
    </form>
  ";
  exit;
}

set_h2("jenjang $jenjang");

$rjenis = [
  '' => [
    'title' => "Undefined Jenis $Room",
  ],
  1 => [
    'title' => 'Mapel Inti',
  ],
  2 => [
    'title' => 'Mapel Tambahan',
  ],
  3 => [
    'title' => 'Mapel Nonformal',
  ],
];

$rjenjang = [
  '' => '--null--',
  'S1' => 'Sarjana',
  'D3' => 'Diploma',
  'SA' => 'SLTA',
  'SP' => 'SLTP',
  'SD' => 'SD',
];


# ============================================================
# MAIN SELECT LOOP
# ============================================================
$count_undefined_jenis = 0;
foreach ($rjenis as $key_jenis => $arr_jenis) {
  $title = "$arr_jenis[title]";
  $and_jenis = $key_jenis ? "a.jenis='$key_jenis'" : "a.jenis is null";
  $s = "SELECT a.*,
  (SELECT nama FROM tb_status_room WHERE status=a.status) status_room 
  FROM tb_room a 
  WHERE a.created_by=$id_peserta 
  AND a.jenjang='$jenjang'
  AND $and_jenis
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (!$key_jenis) $count_undefined_jenis = mysqli_num_rows($q);
  if (!mysqli_num_rows($q)) {
    $tr = "
      <tr>
        <td colspan=100%>
          <div class='alert alert-danger'>
            Belum ada $Room berjenis [ $arr_jenis[title] ] jenjang [ $jenjang ]
          </div>
        </td>
      </tr>
    ";
  } else {
    $tr = '';
    $i = 0;
    while ($d = mysqli_fetch_assoc($q)) {
      $i++;
      $opts = '';
      $status_room = $d['status_room'] ?? 'Belum Aktivasi';
      $d['status'] = $d['status'] ?? 0;
      foreach ($rjenis as $jns => $arr_jenis) {
        $selected = $jns == $d['jenis'] ? 'selected' : '';
        $opts .= "<option value='$jns' $selected>$arr_jenis[title]</option>";
      }

      $manage = '-';
      $green = 'red';
      if ($d['jenis']) {
        if ($d['status'] == 100) {
          $manage = "<a onclick='return confirm(`Beralih dan Manage $Room ini`)' href='?manage_room&id_room=$d[id]'>Manage $Room</a>";
          $green = 'green';
        } else {
          $manage = "<a onclick='return confirm(`Aktivasi $Room ini`)' href='?pilih_room&aktivasi_room=$d[id]'>Aktivasi</a>";
        }
      }

      $td_jenis = $key_jenis ? "
        <td class='$green'>
          <b>Status-$d[status]:</b> $status_room
        </td>      
      " : "
        <td>
          <select class='form-control' name=jenis[$d[id]]>
            $opts
          </select>      
        </td>      
      ";
      $tr .= "
        <tr id=tr__$d[id]>
          <td width=50px>$i</td>
          <td width=40%>$d[nama]</td>
          $td_jenis
          <td width=20%>
            $manage
          </td>
        </tr>
      ";
    }
  }

  $btn_save = $key_jenis ? '' : "<button class='btn btn-primary' name=btn_save_jenis>Save Jenis</button>";

  # ============================================================
  # FORM ADD
  # ============================================================
  $form_add = !$key_jenis ? '' : "
    <span class='btn_aksi pointer green' id=form_add_$key_jenis" . "__toggle>$img_add Add $Room $title</span>
    <form method=post id=form_add_$key_jenis class='wadah mt3 gradasi-kuning'>
      <input placeholder='Nama $title baru...' name=nama required minlength=8 class='form-control mb2'>
      <input placeholder='Singkatan...' name=singkatan required minlength=3 maxlength=10 class='form-control mb2'>
      <input disabled class='form-control mb2' value='Tahun Ajar: $ta'>
      <input placeholder='Lembaga...' name=lembaga required minlength=5 class='hideit form-control mb2' value='AL-BAITI SUMEDANG'>
      <button class='btn btn-success' name=btn_add_room_jenis value=$key_jenis>Confirm Add</button>
    </form>
  ";

  echo (!$key_jenis and !$count_undefined_jenis) ? '' : "
    <div class='wadah gradasi-toska'>
      <h3>$title</h3>
      <form method=post >
        <table class=table>
          <thead>
            <th>No</th>
            <th>$Room</th>
            <th>Info</th>
            <th>Aksi</th>
          </thead>
          $tr
        </table>
        $btn_save
      </form>
      $form_add
    </div>
  ";
}
