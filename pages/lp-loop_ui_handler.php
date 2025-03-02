<?php
$str_fiturs = '';
foreach ($arr_fitur_sesi as $fitur => $arr_fitur) {
  if (($fitur == 'bertanya' || $fitur == 'tanam_soal') and !$sesi['tags']) {
    $str_fiturs = "<div class='abu miring f12 mb1 bordered br5 p1'>belum bisa $fitur</div>";
  } elseif ($fitur == 'challenge' || $fitur == 'latihan' || $fitur == 'ujian') {
    $title = '';
    $tambah = '';
    if ($id_role == 2) {
      $href = $fitur == 'ujian' ? '?add_paket_soal' : "?tambah_activity&p=$fitur";
      $tambah = "<a href='$href&id_sesi=$sesi[id]'>$img_add</a>";
    }

    $belum_ada = $fitur == 'ujian' ? 'quiz harian' : $fitur;
    $sub_fitur = "<div class='abu miring f12'>belum ada $belum_ada</div>";

    if (isset($arr_data_act[$fitur][$id_sesi])) { // jika ada datanya
      $title = "<div class='mb1 green bold f12 proper'>$arr_fitur[title]</div>";
      $sub_fitur = '';
      $j = 0;
      foreach ($arr_data_act[$fitur][$id_sesi] as $k2 => $v2) {
        $j++;
        $btn_info = $v2['ket'] ? 'btn-info' : 'btn-secondary';
        $sub_fitur .= "<a href='?activity&jenis=$fitur&id_assign=$v2[id]' class='btn $btn_info btn-sm mb1 w-100'>$j. $v2[nama_act]</a> ";
      }
    } else {
      // if ($fitur == 'ujian') {
      //   echo '<pre>';
      //   var_dump($arr_fitur);
      //   echo '<b style=color:red>Developer SEDANG DEBUGING: exit(true)</b></pre>';
      //   // exit;
      // }
    }
    $str_fiturs = "<div class='bordered br5 p1 mb1'>$title $sub_fitur $tambah</div>";
  } elseif (
    $fitur == 'bahan_ajar'
    || $fitur == 'file_ppt'
    || $fitur == 'video_ajar'
    || $fitur == 'file_lain'
  ) {
    # ============================================================
    # BAHAN AJAR, PPT, VIDEO, FILE LAIN
    # ============================================================
    $href = $sesi[$fitur];
    if ($href) {
      $str_fiturs = "
        <a target=_blank onclick='return confirm(`Akses link?\n\n$href`)' href='$href' >
          <img src='assets/img/ilustrasi/$fitur.png' class='icon_bahan_ajar' >
          <div class='f12 abu mt1'>$arr_fitur[title]</div>
        </a>
      ";
    } else {
      $ingin = "ingin *Request $arr_fitur[title]* untuk P-$sesi[no]";
      $href_wa = href_wa(
        $trainer['no_wa'],
        $ingin,
        'REQUEST LMS',
        false,
        false,
        $trainer['nama'],
        $trainer['gender'],
        $user['nama']
      );
      $str_fiturs = "
        <span>
          <img src='assets/img/ilustrasi/$fitur.png' class='icon_bahan_ajar icon_bahan_ajar_disabled' onclick='return confirm(`$arr_fitur[title] pada sesi ini belum tersedia.\n\nSilahkan request!`)'>
          <div class='f12 abu mt1 btn_aksi pointer' id=request$fitur$id_sesi" . "__toggle>Request</div>
          <div class='mt2 hideit border-top pt2' id=request$fitur$id_sesi>
            <a target=_blank class='btn btn-sm btn-success w-100 ' href='$href_wa' onclick='return confirm(`Request $arr_fitur[title]?`)'>$img_wa Request</a>
          </div>
        </span>
      ";
    }
  } else { // button only
    $str_fiturs = "
      <div>
        <a href='?$arr_fitur[param]&id_sesi=$id_sesi' class='btn btn-primary btn-sm mb1 w-100' onclick='return confirm(`$arr_fitur[title]?`)'>$arr_fitur[title]</a>
      </div>
    ";
  }

  $fiturs[$fitur] = $str_fiturs;
}

$ui_edit = [];
$cols_ba = '';
foreach ($arr_bahan_ajar as $ba) {
  $ui_edit[$ba] = create_ui($ba, $sesi[$ba], $id_sesi, '');
  $cols_ba .= "
    <div class='mt2 mb3 col-6 col-md-3'>
      $fiturs[$ba]
      $ui_edit[$ba]
    </div>
  ";
}

$ui_bahan_ajar = "
  <div class='row tengah mb2'>
    $cols_ba
  </div>
";

$ui_acts = "
  <div class='mt1 tengah pt1 aktifitas-pembelajaran'>
    <div class='kecil miring abu mb2 f10 tengah'>Aktivitas Pembelajaran:</div>
    <div class='row'>
      <div class='col-md-4 mb2'>
        $fiturs[play_kuis]
      </div>
      <div class='col-md-4 mb2'>
        $fiturs[tanam_soal]
      </div>
      <div class='col-md-4 mb2'>
        $fiturs[bertanya]
      </div>
      <div class='col-md-6'>
        $fiturs[latihan]
      </div>
      <div class='col-md-6'>
        $fiturs[challenge]
      </div>
      <div class='col-md-12 mt2'>
        $fiturs[ujian]
      </div>
    </div>
  </div>
";
