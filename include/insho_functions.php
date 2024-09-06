<?php
// v.1.3.12 update fungsi hari_tanggal
// v.1.3.11 add data AOS to set_h2
// v.1.3.10 add hari_tanggal
// v.1.3.9 update eta function
// v.1.3.8 autoset title when set_judul
// v.1.3.7 set_h2 id, set_judul, set_sub_judul
// v.1.3.6 function gender
// v.1.3.5 eta2 updated
// v.1.3.4 seth2 dan key2kolom
// v.1.3.3 tr_col colspan=100%
// v.1.3.2 baca_csv update
// v.1.3.1 echolog update
// v.1.3.0 revision with echolog
// v.1.2.0 revision with function baca_csv

function hari_tanggal($datetime = '', $long_mode = 1, $with_day = 1, $with_hour = 1, $with_second = false, $separator = ' ')
{
  $time = $datetime ? strtotime($datetime) : strtotime('now');
  $nama_hari = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
  $nama_bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  if (!$long_mode) {
    $nama_hari = ['Ah', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'];
    $nama_bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
  }
  $hari_show = $with_day ? $nama_hari[date('w', $time)] . ', ' : '';
  $year_format = $with_hour ? 'Y, H:i' : 'Y';
  $year_format = $with_second ? 'Y, H:i:s' : $year_format;
  $tanggal_show =  date('d', $time) . $separator . $nama_bulan[intval(date('m', $time)) - 1] . $separator . date($year_format, $time);


  return $hari_show . $tanggal_show;
}

function set_h2($judul, $sub_judul = '', $href_back = '')
{
  set_title($judul);
  $link = !$href_back ? '' : "<div class='mt2'><a href='$href_back'><img src='assets/img/icon/prev.png' class=img_icon></a></div>";
  echo "
    <div class='section-title' data-aos='fade'>
      <h2 id=judul>$judul</h2>
      <p id=sub_judul>$sub_judul$link</p>
    </div>
  ";
}

function key2kolom($key)
{
  return ucwords(str_replace('_', ' ', $key));
}


function tr_col($pesan, $td_class = '', $tr_class = '', $jumlah_col = 100)
{
  $colspan = $jumlah_col < 10 ? $jumlah_col : "$jumlah_col%";
  return "<tr class='$tr_class'><td class='$td_class' colspan='$colspan'>$pesan</td></tr>";
}

function echolog($pesan, $break = true)
{
  $br = ($break and $pesan != 'sukses') ? '<br>' : '';
  $dots = ($break and $pesan != 'sukses') ? '... ' : '';
  $dots = '... ';
  echo "<span class='log'>$pesan$dots</span>$br";
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

function eta2($datetime, $indo = 1)
{
  return eta(strtotime($datetime) - strtotime('now'));
}

function eta($detik, $indo = 1)
{
  $menit = '';
  $jam = '';
  $hari = '';
  $minggu = '';
  $bulan = '';

  if ($detik >= 0) {
    if ($detik < 60) {
      return $indo ? "$detik detik lagi" : "$detik seconds left";
    } elseif ($detik < 60 * 60) {
      $menit = ceil($detik / 60);
      return $indo ? "$menit menit lagi" : "$menit minutes left";
    } elseif ($detik < 60 * 60 * 24) {
      $jam = ceil($detik / (60 * 60));
      return $indo ? "$jam jam lagi" : "$jam hours left";
    } elseif ($detik < 60 * 60 * 24 * 7) {
      $hari = ceil($detik / (60 * 60 * 24));
      return $indo ? "$hari hari lagi" : "$hari days left";
    } elseif ($detik < 60 * 60 * 24 * 7 * 4) {
      $minggu = ceil($detik / (60 * 60 * 24 * 7));
      return $indo ? "$minggu minggu lagi" : "$minggu weeks left";
    } elseif ($detik < 60 * 60 * 24 * 365) {
      $bulan = ceil($detik / (60 * 60 * 24 * 7 * 4));
      return $indo ? "$bulan bulan lagi" : "$bulan monts left";
    } else {
      $tahun = ceil($detik / (60 * 60 * 24 * 365));
      return $indo ? "$tahun tahun lagi" : "$tahun years left";
    }
  } else {
    if ($detik > -60) {
      $detik = -$detik;
      return $indo ? "$detik detik yang lalu" : "$detik seconds ago";
    } elseif ($detik > -60 * 60) {
      $menit = ceil($detik / 60);
      $menit = -$menit;
      return $indo ? "$menit menit yang lalu" : "$menit minutes ago";
    } elseif ($detik > -60 * 60 * 24) {
      $jam = ceil($detik / (60 * 60));
      $jam = -$jam;
      return $indo ? "$jam jam yang lalu" : "$jam hours ago";
    } elseif ($detik > -60 * 60 * 24 * 7) {
      $hari = ceil($detik / (60 * 60 * 24));
      $hari = -$hari;
      return $indo ? "$hari hari yang lalu" : "$hari days ago";
    } elseif ($detik > -60 * 60 * 24 * 7 * 4) {
      $minggu = ceil($detik / (60 * 60 * 24 * 7));
      $minggu = -$minggu;
      return $indo ? "$minggu minggu yang lalu" : "$minggu weeks ago";
    } elseif ($detik > -60 * 60 * 24 * 365) {
      $bulan = ceil($detik / (60 * 60 * 24 * 7 * 4));
      $bulan = -$bulan;
      return $indo ? "$bulan bulan yang lalu" : "$bulan monts ago";
    } else {
      $tahun = ceil($detik / (60 * 60 * 24 * 365));
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



function set_title($text)
{
  echo '<script>$(function(){$("title").text("' . $text . '");})</script>';
}

function set_judul($text, $sub_judul = '')
{
  $set_sub_judul = !$sub_judul ? '' : "$('#sub_judul').text('$sub_judul');";
  echo "
    <script>
      $(function(){
        $('#judul').text('$text');
        $set_sub_judul
      })
    </script>
  ";
}


function gender($lp)
{
  if ((strtolower($lp) == 'l')) {
    return 'laki-laki';
  } elseif (strtolower($lp) == 'p') {
    return 'perempuan';
  } elseif (strtolower($lp) == '') {
    return '<i>null</i>';
  } else {
    return "<i style=color:red>gender $lp undefined</i>";
  }
}

function tanggal($date, $format = 'd-M-Y')
{
  if (strtotime($date) > 0) {
    return date($format, strtotime($date));
  } else {
    if ($date == '') {
      return '<i>null</i>';
    } else {
      return '<i style=color:red>tanggal invalid</i>';
    }
  }
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