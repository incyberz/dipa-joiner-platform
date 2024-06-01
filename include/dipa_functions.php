<?php
function akhir_ujian($awal_ujian, $durasi_ujian = 60)
{
  return date('Y-m-d H:i:s', strtotime($awal_ujian) + 60 * $durasi_ujian);
}
