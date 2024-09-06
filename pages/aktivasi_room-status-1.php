<?php
$thn_ajar = $room['tahun_ajar'] ? substr($room['tahun_ajar'], 0, 4) : date('Y');
$gg_year = date('m') >= 7 ? 1 : 2;
$gg = $room['tahun_ajar'] ? substr($room['tahun_ajar'], 4, 1) : $gg_year;
$selected[1] = $gg == 1 ? 'selected' : '';
$selected[2] = $gg == 2 ? 'selected' : '';

$room_tahun_ajar = $room['tahun_ajar'] > 20201 ? $room['tahun_ajar'] : "$thn_ajar$gg";

// echo '<pre>';
// echo "
//   <br>thn_ajar: $thn_ajar
//   <br>tahun_ajar: $ta
//   <br>room_tahun_ajar: $room_tahun_ajar
//   <br>gg: $gg
// ";
// echo '</pre>';


$ket_ta = '';
if ($room['tahun_ajar']) {
  $ket_ta = "<div class='green bold wadah'>Tahun Ajar Room sudah di set ke TA. $room[tahun_ajar]</div>";
}

$inputs = "
  <input class='bg-yellow' required type=hidden id=tahun_ajar name=tahun_ajar min=20231 max=20282 value='$room_tahun_ajar'>
  <div class='mb1'>Tahun Ajar</div>
  <div class='flexy'>
    <div>
      <input class='form-control tahun_ajar_trigger' required type=number id=thn_ajar min=2023 max=2028 value=$thn_ajar>
    </div>
    <div>
      <select id='gg' class='form-control tahun_ajar_trigger'>
        <option value='1' $selected[1]>Ganjil</option>
        <option value='2' $selected[2]>Genap</option>
      </select>
    </div>
    <div>
      <input class='form-control ' required name=lembaga minlength='3' maxlength='30' placeholder='Lembaga...' value='$room[lembaga]'>
    </div>

  </div>
  <div class='mt1 mb2 f12 abu'>Contoh: Tahun ajar 2023 Genap, Lembaga: Prodi MBS Fakultas FEBI Masoem University</div>  
  $ket_ta
";
?>
<script>
  $(function() {
    $('.tahun_ajar_trigger').change(function() {
      $('#tahun_ajar').val(
        $('#thn_ajar').val() + $('#gg').val()
      )
    })
  })
</script>