<?php
$div_range = '';
$min_range = 0;
$max_range = 0;
$i = 0;
foreach ($v['range'] as $key2 => $range_value) {
  $i++;
  if ($i == 1) $min_range = $range_value;
  $div_range .= "<div>$range_value</div>";
  $max_range = $range_value;
}
$value = $v['value'];
$val_range = $value ? $value : intval(($max_range - $min_range) / 2) + $min_range;
$step = $v['step'] ?? 1;
$placeholder = $v['placeholder'] ?? '...';
$type = $v['type'] ?? 'text';
$min = $v['min'] ?? '';
$max = $v['max'] ?? '';
$minlength = $v['minlength'] ?? '';
$maxlength = $v['maxlength'] ?? '';
$class = $v['class'] ?? '';
$satuan = $v['satuan'] ?? '';
$required = 'required'; // zzz default

$range_durasi = "
  <div class='flexy flex-center'>
    <div class='f14 darkblue miring pt1'>$v[label]</div>
    <div>
      <input 
        id='durasi_ujian' 
        name='durasi_ujian' 
        value='$value' 
        step='$step' 
        placeholder='$placeholder' 
        type='$type' 
        $required
        class='form-control mb2 $class' 
        min='$min' 
        max='$max' 
        minlength='$minlength' 
        maxlength='$maxlength' 
        style='max-width:100px'
      >          
    </div>
    <div class='f14 abu miring pt1'>$satuan</div>
  </div>
  <input type='range' class='form-range range' min='$min_range' max='$max_range' id='range__durasi_ujian' value='$val_range' step='$step' name=durasi_ujian>
  <div class='flexy flex-between f12 consolas abu'>
    $div_range
  </div>
";
