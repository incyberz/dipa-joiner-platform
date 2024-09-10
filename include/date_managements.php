<?php
# ============================================================
# DATE MANAGEMENTS v.2.0.1
# ============================================================
$now = date('Y-m-d H:i:s');
$today = date('Y-m-d');
$w = date('w', strtotime($today));
$week = intval(strtotime('now') / (7 * 24 * 60 * 60));

$ahad_skg = date('Y-m-d', strtotime("-$w day", strtotime($today)));
$besok = date('Y-m-d H:i', strtotime('+1 day', strtotime('today')));
$lusa = date('Y-m-d H:i', strtotime('+2 day', strtotime('today')));

$senin_skg = date('Y-m-d', strtotime("+1 day", strtotime($ahad_skg)));
$selasa_skg = date('Y-m-d', strtotime("+2 day", strtotime($ahad_skg)));
$rabu_skg = date('Y-m-d', strtotime("+3 day", strtotime($ahad_skg)));
$kamis_skg = date('Y-m-d', strtotime("+4 day", strtotime($ahad_skg)));
$jumat_skg = date('Y-m-d', strtotime("+5 day", strtotime($ahad_skg)));
$sabtu_skg = date('Y-m-d', strtotime("+6 day", strtotime($ahad_skg)));
$ahad_depan = date('Y-m-d', strtotime("+7 day", strtotime($ahad_skg)));

$senin_skg_show = 'Senin, ' . date('d M Y', strtotime($senin_skg));
$sabtu_skg_show = 'Sabtu, ' . date('d M Y', strtotime($sabtu_skg));

# ============================================================
# SELAMAT PAGI
# ============================================================
$waktu = "Pagi";
if (date("H") >= 9) $waktu = "Siang";
if (date("H") >= 15) $waktu = "Sore";
if (date("H") >= 18) $waktu = "Malam";

# ============================================================
# HARI INI
# ============================================================
$nama_hari = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$nama_bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$hari_ini = $nama_hari[date('w')] . ', ' . date('d') . ' ' . $nama_bulan[intval(date('m')) - 1] . ' ' . date('Y');



function durasi_hari($a, $b)
{
  if (intval($a) == 0 || intval($b) == 0) {
    return "-";
  }
  $dStart = new DateTime($a);
  $dEnd  = new DateTime($b);
  $dDiff = $dStart->diff($dEnd);
  return $dDiff->format('%r%a');
}

function eta2($datetime, $indo = 1)
{
  return eta(strtotime($datetime) - strtotime('now'), $indo);
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
