<?php

function frp($x)
{
  return "Rp " . fnum($x) . ",-";
}

function fnum($x)
{
  switch (strlen($x)) {
    case 1:
    case 2:
    case 3:
      $y = $x;
      break;

    case 4:
      $y = substr($x, 0, 1) . "." . substr($x, 1, 3);
      break;
    case 5:
      $y = substr($x, 0, 2) . "." . substr($x, 2, 3);
      break;
    case 6:
      $y = substr($x, 0, 3) . "." . substr($x, 3, 3);
      break;

    case 7:
      $y = substr($x, 0, 1) . "." . substr($x, 1, 3) . "." . substr($x, 4, 3);
      break;
    case 8:
      $y = substr($x, 0, 2) . "." . substr($x, 2, 3) . "." . substr($x, 5, 3);
      break;
    case 9:
      $y = substr($x, 0, 3) . "." . substr($x, 3, 3) . "." . substr($x, 6, 3);
      break;

    default:
      $y = "Out of length digit.";
      break;
  }

  if ($y == 0) {
    return "-";
  } else {
    return "$y";
  }
}




function penyebut($nilai)
{
  $nilai = abs($nilai);
  $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
  $temp = '';

  if ($nilai < 12) {
    $temp = " " . $huruf[$nilai];
  } else if ($nilai < 20) {
    $temp = penyebut($nilai - 10) . " belas";
  } else if ($nilai < 100) {
    $temp = penyebut($nilai / 10) . " puluh" . penyebut($nilai % 10);
  } else if ($nilai < 200) {
    $temp = " seratus" . penyebut($nilai - 100);
  } else if ($nilai < 1000) {
    $temp = penyebut($nilai / 100) . " ratus" . penyebut($nilai % 100);
  } else if ($nilai < 2000) {
    $temp = " seribu" . penyebut($nilai - 1000);
  } else if ($nilai < 1000000) {
    $temp = penyebut($nilai / 1000) . " ribu" . penyebut($nilai % 1000);
  } else if ($nilai < 1000000000) {
    $temp = penyebut($nilai / 1000000) . " juta" . penyebut($nilai % 1000000);
  } else if ($nilai < 1000000000000) {
    $temp = penyebut($nilai / 1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
  } else if ($nilai < 1000000000000000) {
    $temp = penyebut($nilai / 1000000000000) . " trilyun" . penyebut(fmod($nilai, 1000000000000));
  }
  return $temp;
}

function terbilang($nilai)
{
  if ($nilai < 0) {
    $hasil = "minus " . trim(penyebut($nilai));
  } else {
    $hasil = trim(penyebut($nilai));
  }
  return $hasil;
}


?>
<script>
  const rupiah = (number) => {
    return new Intl.NumberFormat("id-ID", {
      style: "currency",
      currency: "IDR"
    }).format(number);
  }
</script>