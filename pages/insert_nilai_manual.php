<?php
instruktur_only();
$pekan = $_GET['pekan'] ?? 'uts';
$not_pekan = $pekan == 'uts' ? 'uas' : 'uts';
$NOT_PEKAN = strtoupper($not_pekan);
$PEKAN = strtoupper($pekan);
$arr_field = ['presensi', 'tugas1', 'uts', 'tugas2', 'uas', 'nilai_akhir'];
$arr_bobot = [
  'presensi' => 10,
  'tugas1' => 15,
  'uts' => 25,
  'tugas2' => 15,
  'uas' => 35,
  'nilai_akhir' => 100,
];

include 'insert_nilai_manual_styles.php';

# ============================================================
# SET HEADER
# ============================================================
set_h2('Insert Nilai Manual', "
  Form ini digunakan untuk memasukan Nilai $PEKAN secara manual. | 
  <a href='?insert_nilai_manual&pekan=$not_pekan'>Insert Nilai $NOT_PEKAN</a>
  <span class='hideit' id=pekan>$pekan</span>
");


# ============================================================
# PROCESSORS
# ============================================================
if (isset($_POST['btn_simpan'])) {




  # ============================================================
  # SAVE DATA BOBOT MANUAL
  # ============================================================
  foreach ($_POST['bobot_presensi'] as $key => $value) $kelas = $key;
  $id_room_kelas = $id_room . "__$kelas";
  $fields = '__';
  $values = '__';
  $pairs = '__';
  foreach ($arr_field as $field) {
    $value = $_POST['bobot_' . $field][$kelas] ?? 100;
    $value = $value ? $value : 'NULL';
    $values .= ",$value";
    $fields .= ",$field";
    $pairs .= ",$field=$value";
  }
  $fields = str_replace('__,', '', $fields);
  $values = str_replace('__,', '', $values);
  $pairs = str_replace('__,', '', $pairs);

  $s = "INSERT INTO tb_bobot_manual (kode, $fields) VALUES('$id_room_kelas',$values)
      ON DUPLICATE KEY UPDATE $pairs";
  // echo $s;
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', 'Update bobot nilai sukses.');






  # ============================================================
  # SAVE DATA NILAI
  # ============================================================
  $arr = $_POST['presensi'];
  foreach ($arr as $kelas_id_peserta => $value) {
    $kode = $id_room . "__$kelas_id_peserta";
    if ($_POST['nilai_akhir'][$kelas_id_peserta]) {
      $fields = '__';
      $values = '__';
      $pairs = '__';
      foreach ($arr_field as $field) {
        $value = $_POST[$field][$kelas_id_peserta];
        $value = $value ? $value : 'NULL';
        $values .= ",$value";
        $fields .= ",$field";
        $pairs .= ",$field=$value";
      }
      $fields = str_replace('__,', '', $fields);
      $values = str_replace('__,', '', $values);
      $pairs = str_replace('__,', '', $pairs);

      $s = "INSERT INTO tb_nilai_manual (kode, $fields) VALUES('$kode',$values)
      ON DUPLICATE KEY UPDATE $pairs";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    }
  }
  echo div_alert('success', 'Update data nilai sukses.');
  jsurl();
  exit;
}

# ============================================================
# FUNCTIONS
# ============================================================
function create_input_array(
  $arr_name,
  $index_id,
  $placeholder = '',
  $readonly = '',
  $type = 'number',
  $classes = '',
  $value = '',
  $arr_id = '',
  $required = '',
  $step = '0.01',
  $min = 0,
  $max = 100,
) {
  $arr_id = $arr_id ? $arr_id : $arr_name . '__' . $index_id;
  $placeholder = $placeholder ? $placeholder : "$arr_name...";
  $name = $arr_name . '[' . $index_id . ']';
  return "
    <input 
      $required 
      $readonly 
      type=$type 
      step=$step 
      min=$min 
      max=$max 
      name=$name 
      id=$arr_id 
      value='$value' 
      placeholder='$placeholder' 
      class='input_array form-control tengah $classes'
    />  
  ";
}















# ============================================================
# MAIN SELECT KELAS
# ============================================================
$s = "SELECT a.*,
(
  SELECT COUNT(1) 
  FROM tb_kelas_peserta 
  WHERE kelas=a.kelas) count_peserta
FROM tb_room_kelas a 
WHERE a.id_room=$id_room 
AND a.kelas != 'INSTRUKTUR' 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
while ($d = mysqli_fetch_assoc($q)) {

  // replace tilde with dash
  $kelas_oldname = $d['kelas'];
  $d['kelas'] = str_replace('~', '-', $d['kelas']);

  # ============================================================
  # BOBOT KELAS MANUAL
  # ============================================================
  $id_room_kelas = $id_room . "__$d[kelas]";
  $s2 = "SELECT * FROM tb_bobot_manual WHERE kode='$id_room_kelas'";
  // echo "<br>$s2";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  if (mysqli_num_rows($q2)) {
    $z = mysqli_fetch_assoc($q2);

    $arr_bobot = [
      'presensi' => $z['presensi'],
      'tugas1' => $z['tugas1'],
      'uts' => $z['uts'],
      'tugas2' => $z['tugas2'],
      'uas' => $z['uas'],
      'nilai_akhir' => $z['nilai_akhir'],
    ];
  }


  # ============================================================
  # SELECT PESERTA DI TIAP KELAS
  # ============================================================
  $s2 = "SELECT a.*,
  b.nama as nama_peserta,
  (
    SELECT 1 FROM tb_nilai_manual 
    WHERE kode=CONCAT($id_room,'__','$d[kelas]','__',a.id_peserta) ) punya_nilai  
  FROM tb_kelas_peserta a 
  JOIN tb_peserta b ON a.id_peserta=b.id 
  WHERE a.kelas='$kelas_oldname' 
  ORDER BY b.nama
  ";
  // die($s2);
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  $tr = '';
  $i = 0;
  while ($d2 = mysqli_fetch_assoc($q2)) {
    $i++;

    # ============================================================
    # DEFAULT VALUE OF INPUTS
    # ============================================================
    $value = [];
    $gradasi = [];
    foreach ($arr_field as $field) {
      $value[$field] = '';
      $gradasi[$field] = '';
    }
    if ($d2['punya_nilai']) {
      $kode = $id_room . "__$d[kelas]__$d2[id_peserta]";
      $s3 = "SELECT * FROM tb_nilai_manual WHERE kode = '$kode'";
      $q3 = mysqli_query($cn, $s3) or die(mysqli_error($cn));
      $d3 = mysqli_fetch_assoc($q3);
      foreach ($arr_field as $field) {
        $value[$field] = $d3[$field];
        $gradasi_HM = '';
        if ($value[$field] >= 85) {
          $gradasi_HM = 'gradasi_A';
        } elseif ($value[$field] >= 70) {
          $gradasi_HM = 'gradasi_B';
        } elseif ($value[$field] >= 60) {
          $gradasi_HM = 'gradasi_C';
        } elseif ($value[$field] >= 40) {
          $gradasi_HM = 'gradasi_D';
        } elseif ($value[$field] > 0) {
          $gradasi_HM = 'gradasi_E';
        }
        $gradasi[$field] = $gradasi_HM;
      }
      $value['nilai_akhir'] = $d3['nilai_akhir'];
    }


    $input_presensi = create_input_array('presensi', "$d[kelas]__$d2[id_peserta]", 'Presensi', '', 'number', "$gradasi[presensi] presensi__$d[kelas]", $value['presensi']);
    $input_tugas1 = create_input_array('tugas1', "$d[kelas]__$d2[id_peserta]", 'Tugas 1', '', 'number', "$gradasi[tugas1] tugas1__$d[kelas]", $value['tugas1']);
    $input_uts = create_input_array('uts', "$d[kelas]__$d2[id_peserta]", 'UTS', '', 'number', "$gradasi[uts] uts__$d[kelas]", $value['uts']);
    $input_tugas2 = create_input_array('tugas2', "$d[kelas]__$d2[id_peserta]", 'Tugas 2', '', 'number', "$gradasi[tugas2] tugas2__$d[kelas]", $value['tugas2']);
    $input_uas = create_input_array('uas', "$d[kelas]__$d2[id_peserta]", 'UAS', '', 'number', "$gradasi[uas] uas__$d[kelas]", $value['uas']);
    $nilai_akhir = create_input_array('nilai_akhir', "$d[kelas]__$d2[id_peserta]", 'N.Akhir', 'readonly', 'text', "$gradasi[nilai_akhir] input-disabled nilai_akhir__$d[kelas]", $value['nilai_akhir']);

    $tr .= "
      <tr>
        <td>$i</td>
        <td>$d2[nama_peserta]</td>
        <td colspan=100%>
          <div class=row>
            <div class='col-lg-2'>$input_presensi</div>
            <div class='col-lg-2'>$input_tugas1</div>
            <div class='col-lg-2'>$input_uts</div>
            <div class='col-lg-2'>$input_tugas2</div>
            <div class='col-lg-2'>$input_uas</div>
            <div class='col-lg-2'>$nilai_akhir</div>
          </div>
        </td>
      </tr>
    ";
  }

  $bobot_presensi = create_input_array('bobot_presensi', $d['kelas'], '', '', 'number', 'input_bobot', $arr_bobot['presensi'], '', 'required', 1);
  $bobot_tugas1 = create_input_array('bobot_tugas1', $d['kelas'], '', '', 'number', 'input_bobot', $arr_bobot['tugas1'], '', 'required', 1);
  $bobot_tugas2 = create_input_array('bobot_tugas2', $d['kelas'], '', '', 'number', 'input_bobot', $arr_bobot['tugas2'], '', 'required', 1);
  $bobot_uts = create_input_array('bobot_uts', $d['kelas'], '', '', 'number', 'input_bobot', $arr_bobot['uts'], '', 'required', 1);
  $bobot_uas = create_input_array('bobot_uas', $d['kelas'], '', '', 'number', 'input_bobot', $arr_bobot['uas'], '', 'required', 1);
  $bobot_total = create_input_array('bobot_total', $d['kelas'], '', 'disabled', '', 'input_bobot input-disabled', $arr_bobot['nilai_akhir'], '', 'required', 1);

  $link_assign = "<a href='?assign_peserta_kelas&kelas=$d[kelas]'>Assign Peserta Kelas</a>";
  echo !$d['count_peserta'] ? div_alert('danger', "Belum ada $Peserta pada Grup Kelas $d[kelas] | $link_assign") : "
    <h3 class='h3-kelas'>$d[kelas]</h3>
    <form method=post>
    <table class='table table-striped table-hover table-bordered'>
      <thead class='gradasi-toska'>
        <th>No</th>
        <th>NAMA PESERTA</th>
        <th colspan=100%>
          <div class='tengah mb2 pb1' style='border-bottom:solid 1px #ddd;'>PERSEN BOBOT PENILAIAN</div>
          <div class=row>
            <div class='col-lg-2'>
              <div class='tengah f12 abu mb1'>Presensi</div>
              $bobot_presensi
            </div>
            <div class='col-lg-2'>
              <div class='tengah f12 abu mb1'>Tugas 1</div>
              $bobot_tugas1
            </div>
            <div class='col-lg-2'>
              <div class='tengah f12 abu mb1'>UTS</div>
              $bobot_uts
            </div>
            <div class='col-lg-2'>
              <div class='tengah f12 abu mb1'>Tugas 2</div>
              $bobot_tugas2
            </div>
            <div class='col-lg-2'>
              <div class='tengah f12 abu mb1'>UAS</div>
              $bobot_uas
            </div>
            <div class='col-lg-2'>
              <div class='tengah f12 abu mb1'>Nilai Akhir</div>
              $bobot_total
            </div>
          </div>
        </th>
      </thead>
      $tr
    </table>
    <div class='abu miring f12 mb1'>$d[count_peserta] $Peserta</div>
    <button class='btn btn-primary w-100 mb4 btn-simpan' name=btn_simpan>SIMPAN</button>
    </form>
  ";
}

?>
<script>
  let komponen = [
    'presensi',
    'tugas1',
    'tugas2',
    'uts',
    'uas'
  ];

  function hitung_nilai(field, kelas, id_peserta = '') {
    let kelas_id_peserta = kelas + '__' + id_peserta;

    let nilai_akhir = 0;
    komponen.forEach((k) => {
      let nilai = parseFloat($('#' + k + '__' + kelas_id_peserta).val());
      let bobot = parseInt($('#bobot_' + k + '__' + kelas).val());
      if (nilai < 0 || nilai > 100) {
        $('#' + k + '__' + kelas_id_peserta).val('');
        return;
      }
      nilai_akhir += (isNaN(nilai) || isNaN(bobot)) ? 0 : nilai * bobot;
    })
    $('#nilai_akhir__' + kelas_id_peserta).val(Math.round(nilai_akhir) / 100);
  }
  $(function() {
    $('.input_array').keyup(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let field = rid[0];
      let kelas = rid[1];
      let id_peserta = rid[2] ?? '';
      hitung_nilai(field, kelas, id_peserta);
    })
    $('.input_bobot').change(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let field = rid[0];
      let kelas = rid[1];

      let bobot_total = 0;
      komponen.forEach((k) => {
        bobot_total += parseInt($('#bobot_' + k + '__' + kelas).val());
      })
      $('#bobot_total__' + kelas).val(bobot_total);

      // recalculate by calling presensi keyup
      $('.presensi__' + kelas).each(function() {
        let tid = $(this).prop('id');
        let rid = tid.split('__');
        let field = rid[0];
        let kelas = rid[1];
        let id_peserta = rid[2];
        hitung_nilai(field, kelas, id_peserta);
      })
    })
  })
</script>