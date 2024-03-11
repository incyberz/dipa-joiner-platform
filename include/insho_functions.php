<?php
// v.1.3.2 baca_csv update
// v.1.3.1 echolog update
// v.1.3.0 revision with echolog
// v.1.2.0 revision with function baca_csv
function echolog($pesan, $break = true)
{
  $br = ($break and $pesan != 'sukses') ? '<br>' : '';
  $dots = ($break and $pesan != 'sukses') ? '... ' : '';
  echo "$br<span class='log'>system-log: $pesan$dots</span>";
}

function baca_csv($file, $separator = ',')
{

  if (file_exists($file)) {
    $file = fopen($file, 'r');
    $data = array();

    while (!feof($file)) {
      $data[] = fgetcsv($file, null, $separator);
    }

    fclose($file);
    return $data;
  } else {
    die("File <b class='consolas'>$file</b> tidak ditemukan.");
  }
}

function th($rank)
{
  if ($rank % 10 == 1) {
    return 'st';
  } elseif ($rank % 10 == 2) {
    return 'nd';
  } elseif ($rank % 10 == 3) {
    return 'rd';
  } else {
    return 'th';
  }
}

function hm($nilai)
{
  if ($nilai >= 85) {
    return 'A';
  } elseif ($nilai >= 70) {
    return 'B';
  } elseif ($nilai >= 60) {
    return 'C';
  } elseif ($nilai >= 40) {
    return 'D';
  } elseif ($nilai >= 1) {
    return 'E';
  } elseif ($nilai == 0) {
    return 'TL';
  } else {
    return false;
  }
}

function eta($eta, $indo = 1)
{
  $menit = '';
  $jam = '';
  $hari = '';
  $minggu = '';
  $bulan = '';

  if ($eta >= 0) {
    if ($eta < 60) {
      return $indo ? "$eta detik lagi" : "$eta seconds left";
    } elseif ($eta < 60 * 60) {
      $menit = ceil($eta / 60);
      return $indo ? "$menit menit lagi" : "$menit minutes left";
    } elseif ($eta < 60 * 60 * 24) {
      $jam = ceil($eta / (60 * 60));
      return $indo ? "$jam jam lagi" : "$jam hours left";
    } elseif ($eta < 60 * 60 * 24 * 7) {
      $hari = ceil($eta / (60 * 60 * 24));
      return $indo ? "$hari hari lagi" : "$hari days left";
    } elseif ($eta < 60 * 60 * 24 * 7 * 4) {
      $minggu = ceil($eta / (60 * 60 * 24 * 7));
      return $indo ? "$minggu minggu lagi" : "$minggu weeks left";
    } elseif ($eta < 60 * 60 * 24 * 365) {
      $bulan = ceil($eta / (60 * 60 * 24 * 7 * 4));
      return $indo ? "$bulan bulan lagi" : "$bulan monts left";
    } else {
      $tahun = ceil($eta / (60 * 60 * 24 * 365));
      return $indo ? "$tahun tahun lagi" : "$tahun years left";
    }
  } else {
    if ($eta > -60) {
      $eta = -$eta;
      return $indo ? "$eta detik yang lalu" : "$eta seconds ago";
    } elseif ($eta > -60 * 60) {
      $menit = ceil($eta / 60);
      $menit = -$menit;
      return $indo ? "$menit menit yang lalu" : "$menit minutes ago";
    } elseif ($eta > -60 * 60 * 24) {
      $jam = ceil($eta / (60 * 60));
      $jam = -$jam;
      return $indo ? "$jam jam yang lalu" : "$jam hours ago";
    } elseif ($eta > -60 * 60 * 24 * 7) {
      $hari = ceil($eta / (60 * 60 * 24));
      $hari = -$hari;
      return $indo ? "$hari hari yang lalu" : "$hari days ago";
    } elseif ($eta > -60 * 60 * 24 * 7 * 4) {
      $minggu = ceil($eta / (60 * 60 * 24 * 7));
      $minggu = -$minggu;
      return $indo ? "$minggu minggu yang lalu" : "$minggu weeks ago";
    } elseif ($eta > -60 * 60 * 24 * 365) {
      $bulan = ceil($eta / (60 * 60 * 24 * 7 * 4));
      $bulan = -$bulan;
      return $indo ? "$bulan bulan yang lalu" : "$bulan monts ago";
    } else {
      $tahun = ceil($eta / (60 * 60 * 24 * 365));
      $tahun = -$tahun;
      return $indo ? "$tahun tahun yang lalu" : "$tahun years ago";
    }
  }
}

function jsurl($a = '', $milidetik = 0)
{ // v1.1 revision with duration milidetik
  if ($a == '') {
    $arr = explode('?', $_SERVER['REQUEST_URI']);
    jsurl("?$arr[1]", $milidetik);
    exit;
  }
  echo "
    <div class='consolas f12 abu'>Please wait, redirecting in $milidetik mili seconds...</div>
    <script>
      setTimeout(()=>{
        location.replace('$a');
      },$milidetik);
    </script>
  ";
  exit;
}

function jsreload()
{
  echo "<script>location.reload()</script>";
  exit;
}


// function penyebut($nilai) {
//   $nilai = abs($nilai);
//   $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
//   $temp = '';

//   if ($nilai < 12) {
//     $temp = " ". $huruf[$nilai];
//   } else if ($nilai <20) {
//     $temp = penyebut($nilai - 10). " belas";
//   } else if ($nilai < 100) {
//     $temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10);
//   } else if ($nilai < 200) {
//     $temp = " seratus" . penyebut($nilai - 100);
//   } else if ($nilai < 1000) {
//     $temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
//   } else if ($nilai < 2000) {
//     $temp = " seribu" . penyebut($nilai - 1000);
//   } else if ($nilai < 1000000) {
//     $temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
//   } else if ($nilai < 1000000000) {
//     $temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000);
//   } else if ($nilai < 1000000000000) {
//     $temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000));
//   } else if ($nilai < 1000000000000000) {
//     $temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000));
//   }     
//   return $temp;
// }

// function terbilang($nilai) {
//   if($nilai<0) {
//     $hasil = "minus ". trim(penyebut($nilai));
//   } else {
//     $hasil = trim(penyebut($nilai));
//   }         
//   return $hasil;
// }

function set_title($a)
{
  echo '<script>$(function(){$("title").text("' . $a . '");})</script>';
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