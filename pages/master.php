<?php
$p = $_GET['p'] ?? die(erid('p (page)'));
echo "
  <div class='section-title' data-aos='fade'>
    <h2 class=proper>Master $p</h2>
    <p>Berikut adalah list $p pada Room $room</p>
  </div>
";

$Field = [];
$Type = [];
$Null = [];
$Key = [];
$Default = [];
$s = "DESCRIBE tb_$p ";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
while($d=mysqli_fetch_assoc($q)){
  array_push($Field, $d['Field']);
  array_push($Type, $d['Type']);
  array_push($Null, $d['Null']);
  array_push($Key, $d['Key']);
  array_push($Default, $d['Default']);
}

$s = "SELECT * FROM tb_$p WHERE id_room=$id_room";
if($p=='kelas') $s = "SELECT * FROM tb_kelas ORDER BY tahun_ajar, fakultas,prodi,shift  ";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$tr = '';
$i=0;
while($d=mysqli_fetch_assoc($q)){
  $i++;
  $sty = $i%2==0 ? 'style=background:#eee' : '';
  $tr .= "<tr $sty><td>$i</td>";
  $j=0;
  foreach ($Field as $nama_kolom) {
    $j++;
    $class = $j%2==0 ? 'darkblue' : '';
    $sty = $j==1 ? 'style=background:#ffe' : '';
    $class = $j==1 ? 'darkred f12 miring tengah' : $class;
    $tr .= "<td class='$class' $sty>$d[$nama_kolom]</td>";
  }
  $tr .= '</tr>';
}

// echo '<pre>';
// var_dump($Field);
// echo '</pre>';
$ths = '';
foreach ($Field as $nama_kolom) {
  $ths.= "<th>$nama_kolom</th>";
}

echo "
<table class='table'>
  <thead><th>No</th>$ths</thead>
  $tr
</table>
";
?>
