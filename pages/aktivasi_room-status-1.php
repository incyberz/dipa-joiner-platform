<?php
$thn_ajar = $d_room['tahun_ajar'] ? substr($d_room['tahun_ajar'], 0, 4) : date('Y');
$gg_year = date('m') >= 7 ? 1 : 2;
$gg = $d_room['tahun_ajar'] ? substr($d_room['tahun_ajar'], 4, 1) : $gg_year;
$selected[1] = $gg == 1 ? 'selected' : '';
$selected[2] = $gg == 2 ? 'selected' : '';

$inputs = "
  <input class='bg-yellow' required type=hidden name=tahun_ajar min=20231 max=20282 value='$thn_ajar$gg'>
  <div class='mb1'>Tahun Ajar</div>
  <div class='flexy'>
    <div>
      <input class='form-control' required type=number id=thn_ajar min=2023 max=2028 value=$thn_ajar>
    </div>
    <div>
      <select id='gg' class='form-control'>
        <option value='1' $selected[1]>Ganjil</option>
        <option value='2' $selected[2]>Genap</option>
      </select>
    </div>
    <div>
      <input class='form-control' required name=lembaga minlength='3' maxlength='30' placeholder='Lembaga...' value='$d_room[lembaga]'>
    </div>

  </div>
  <div class='mt1 mb2 f12 abu'>Contoh: Tahun ajar 2023 Genap, Lembaga: Prodi MBS Fakultas FEBI Masoem University</div>  
";
