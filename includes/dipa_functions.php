<?php
function akhir_ujian($awal_ujian, $durasi_ujian = 60)
{
  return date('Y-m-d H:i:s', strtotime($awal_ujian) + 60 * $durasi_ujian);
}

function tahun_ajar_show($ta_aktif)
{
  if (strlen($ta_aktif) != 5) return false;
  if ($ta_aktif < 20201 || $ta_aktif > 20252) return "[ta_aktif: $ta_aktif out of range.]";
  $smt = substr($ta_aktif, 4, 1);
  if ($smt < 1 || $smt > 2) return "[smt: $smt out of range]";
  $gg = $smt == 2 ? 'Genap' : 'Ganjil';
  $thn = substr($ta_aktif, 0, 4);
  return   "$thn/" . ($thn + 1) . " $gg";
}

function ondev()
{
  echo div_alert('danger', 'Page ini in development. Terimakasih sudah mencoba!');
}

function cek_src_profil($src_image, $src_war, $lokasi_profil = '.')
{
  $src =  "$lokasi_profil/$src_image";
  $src = (file_exists($src) and $src_image) ? $src : 'assets/img/no_profil.jpg';
  $src2 = "$lokasi_profil/$src_war";
  $src = (file_exists($src2) and $src_war) ? $src2 : $src;
  return $src;
}
