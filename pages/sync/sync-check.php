<?php
$table = $_GET['table'] ?? die(erid('table'));
set_h2(basename(__FILE__, '.php'), $link_home);
set_title(basename(__FILE__, '.php') . ' ' . $table);


$s = "DESCRIBE $table";
$q1 = mysqli_query($cn1, $s) or die(mysqli_error($cn1));
$q2 = mysqli_query($cn2, $s) or die(mysqli_error($cn2));
$count_field1 = mysqli_num_rows($q1);
$count_field2 = mysqli_num_rows($q2);

$count_check = $count_field1 == $count_field2 ? $img_check : $img_warning;
echo "
  <div class='wadah tengah'>
    <div class='darkblue f20 consolas'>$table</div>
    <span class='f12 abu'>Count Check:</span> $count_field1 :: $count_field2 $count_check
  </div>
";

$arr = [1, 2];
foreach ($arr as $key => $index) {
  $colField[$index] = [];
  $colType[$index] = [];
  $colLength[$index] = [];
  $colNull[$index] = [];
  $colKey[$index] = [];
  $colDefault[$index] = [];
  $q = $index == 1 ? $q1 : $q2;
  while ($d = mysqli_fetch_assoc($q)) {
    array_push($colField[$index], $d['Field']);
    array_push($colNull[$index], $d['Null']);
    array_push($colKey[$index], $d['Key']);
    array_push($colDefault[$index], $d['Default']);

    if ($d['Type'] == 'date') {
      $Type = 'date';
      $Length = 10;
    } else if ($d['Type'] == 'timestamp') {
      $Type = 'timestamp';
      $Length = 19;
    } else {
      $pos = strpos($d['Type'], '(');
      $pos2 = strpos($d['Type'], ')');
      $len = strlen($d['Type']);
      $len_type = $len - ($len - $pos);
      $len_length = $len - ($len - $pos2) - $len_type - 1;

      $Type = substr($d['Type'], 0, $len_type);
      $Length = intval(substr($d['Type'], $pos + 1, $len_length));
    }

    array_push($colType[$index], $Type);
    array_push($colLength[$index], $Length);
  }
}

// echo '<pre>';
// var_dump($colType[1]);
// echo '</pre>';
// echo '<pre>';
// var_dump($colType[2]);
// echo '</pre>';
// exit;

$tr = '';
$i = 0;
$img_reject = "<span class=img_reject>$img_reject</span>";

foreach ($colField[1] as $key => $field) {
  $i++;
  $Field1 = $field;
  $Type1 = $colType[1][$key];
  $Length1 = $colLength[1][$key];
  $Null1 = $colNull[1][$key];
  $Key1 = $colKey[1][$key];
  $Default1 = $colDefault[1][$key];

  $Field2 = $colField[2][$key] ?? $null;
  $Type2 = $colType[2][$key] ?? $img_warning;
  $Length2 = $colLength[2][$key] ?? $img_warning;
  $Null2 = $colNull[2][$key] ?? $img_warning;
  $Key2 = $colKey[2][$key] ?? $img_warning;
  $Default2 = $colDefault[2][$key] ?? '';

  $checkField = $Field1 == $Field2 ? $img_check : $img_reject;
  $checkType = $Type1 == $Type2 ? $img_check : $img_reject;
  $checkLength = $Length1 == $Length2 ? $img_check : $img_reject;
  $checkNull = $Null1 == $Null2 ? $img_check : $img_reject;
  $checkKey = $Key1 == $Key2 ? $img_check : $img_reject;
  $checkDefault = $Default1 == $Default2 ? $img_check : $img_reject;


  $tr .= "
    <tr>
      <td>$i</td>
      <td>$Field1<br>$Field2 $checkField</td>
      <td>$Type1<br>$Type2 $checkType</td>
      <td>$Length1<br>$Length2 $checkLength</td>
      <td>$Null1<br>$Null2 $checkNull</td>
      <td>$Key1<br>$Key2 $checkKey</td>
      <td>$Default1<br>$Default2 $checkDefault</td>
    </tr>
  ";
}
echo "
  <table class=table>
    <thead>
      <th>No</th>
      <th>Field</th>
      <th>Type</th>
      <th>Length</th>
      <th>Null</th>
      <th>Key</th>
      <th>Default</th>
    </thead>
    $tr
  </table>
";
?>
<script>
  $(function() {
    let count_reject = $('.img_reject').length;
    console.log(count_reject);
  })
</script>