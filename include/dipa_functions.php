<?php
function akhir_ujian($awal_ujian, $durasi_ujian = 60)
{
  return date('Y-m-d H:i:s', strtotime($awal_ujian) + 60 * $durasi_ujian);
}

function tahun_ajar_show($ta)
{
  if (strlen($ta) != 5) return false;
  if ($ta < 20201 || $ta > 20252) return "[tahun_ajar: $ta out of range.]";
  $smt = substr($ta, 4, 1);
  if ($smt < 1 || $smt > 2) return "[smt: $smt out of range]";
  $gg = $smt == 2 ? 'Genap' : 'Ganjil';
  $thn = substr($ta, 0, 4);
  return   "$thn/" . ($thn + 1) . " $gg";
}
