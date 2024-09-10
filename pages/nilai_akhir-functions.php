<?php
function gradasi_nilai($nilai)
{
  if ($nilai) {
    if ($nilai >= 85) {
      return  'hijau';
    } elseif ($nilai >= 70) {
      return  'toska';
    } elseif ($nilai >= 50) {
      return  'kuning';
    } else {
      return  'merah';
    }
  } else {
    if ($nilai == 0 || !$nilai) {
      return 'merah';
    } else {
      return '';
    }
  }
}
function konversikan($count, $total)
{
  if ($total == 0) {
    return 100;
  } elseif ($count == 0) {
    return 0;
  } elseif ($count == 1 and $total == 1) {
    return 100;
  } elseif ($count == $total) {
    return 100;
  } else {
    $hasil = round(50 + ($count - 1) * ((round($total * 8 / 10, 0) / $total) * (100 / $total)), 0);
    return $hasil > 100 ? 100 : $hasil;
  }
}
