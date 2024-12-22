<?php
$str_fiturs = '';
foreach ($arr_fitur_sesi as $fitur => $arr_fitur) {
  if (($fitur == 'bertanya' || $fitur == 'tanam_soal') and !$sesi['tags']) {
    $str_fiturs = "<div class='abu miring f12 mb1 bordered br5 p1'>belum bisa $fitur</div>";
  } elseif ($fitur == 'challenge' || $fitur == 'latihan') {
    $title = '';
    $tambah = $id_role == 2 ? "<a href='?tambah_activity&p=$fitur&id_sesi=$sesi[id]'>$img_add</a>" : '';
    $sub_fitur = "<div class='abu miring f12'>belum ada $fitur</div>";

    if (isset($arr_data_act[$fitur][$id_sesi])) {
      $title = "<div class='mb1 green bold f12 proper'>$arr_fitur[title]</div>";
      $sub_fitur = '';
      $j = 0;
      foreach ($arr_data_act[$fitur][$id_sesi] as $k2 => $v2) {
        $j++;
        $btn_info = $v2['ket'] ? 'btn-info' : 'btn-secondary';
        $sub_fitur .= "<a href='?activity&jenis=$fitur&id_assign=$v2[id]' class='btn $btn_info btn-sm mb1 w-100'>$j. $v2[nama_act]</a> ";
      }
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
      $str_fiturs = "
        <span onclick='return confirm(`$arr_fitur[title] pada sesi ini belum tersedia.`)'>
          <img src='assets/img/ilustrasi/$fitur.png' class='icon_bahan_ajar icon_bahan_ajar_disabled' >
          <div class='f12 abu mt1'>$arr_fitur[title]</div>
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
    <div class='mt2 col-6 col-md-3'>
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
  <div class='mt1 tengah pt1' style='border-top:solid 3px #cdc'>
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
    </div>
  </div>
";
