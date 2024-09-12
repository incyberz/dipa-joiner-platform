<?php
$thn_ajar = $room['ta'] ? substr($room['ta'], 0, 4) : date('Y');
$gg_year = date('m') >= 7 ? 1 : 2;
$gg = $room['ta'] ? substr($room['ta'], 4, 1) : $gg_year;
$selected[1] = $gg == 1 ? 'selected' : '';
$selected[2] = $gg == 2 ? 'selected' : '';

$room_tahun_ajar = $room['ta'] > 20201 ? $room['ta'] : "$thn_ajar$gg";


// echo "
//   <br>thn_ajar: $thn_ajar
//   <br>ta: $ta
//   <br>room_tahun_ajar: $room_tahun_ajar
//   <br>gg: $gg
// ";



$ket_ta = '';
if ($room['ta']) {
  $ket_ta = "<div class='green bold wadah'>Tahun Ajar Room sudah di set ke TA. $room[ta]</div>";
}

$inputs = "
  <input class='bg-yellow' required type=hidden id=ta name=ta min=20231 max=20282 value='$room_tahun_ajar'>
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
      $('#ta').val(
        $('#thn_ajar').val() + $('#gg').val()
      )
    })
  })
</script>