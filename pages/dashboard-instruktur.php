<?php
# ============================================================
# INFO TAHUN AJAR DAN PEKAN
# ============================================================
$ta_show = tahun_ajar_show($ta_aktif);
$awal = date('M-Y', strtotime($ta_awal));
$akhir = date('M-Y', strtotime($ta_akhir));
echo div_alert('info tengah', "
  Tahun Ajar Aktif $ta_show
  <div class='abu miring'>$awal - $akhir</div>
");
$border_blue = 'border: dashed 2px blue; padding: 10px; border-radius: 10px; margin: 10px 0';



# ============================================================
# INFO NOTIFICATIONS
# ============================================================
include 'dashboard-instruktur-info_pekan.php';
include 'dashboard-instruktur-info_presensi.php';
// include 'dashboard-instruktur-info_bertanya.php';
include 'dashboard-instruktur-info_latihan.php';
include 'dashboard-instruktur-info_challenge.php';
include 'dashboard-instruktur-info_image_peserta.php';
include 'dashboard-instruktur-info_war_image.php';

echo "<div class=tengah><a class='f14 text-hover-bold' href='?update_room_count'>Update Dashboard</a></div>";

# ============================================================
# PENILAIAN
# ============================================================
include 'dashboard-instruktur-penilaian.php';
