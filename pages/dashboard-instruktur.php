<?php
# ============================================================
# INFO TAHUN AJAR DAN PEKAN
# ============================================================
$ta_show = tahun_ajar_show($ta);
echo div_alert('info tengah', "
  Tahun Ajar Aktif $ta_show
  <div class='abu miring'>Februari - Juli 2024</div>
");


# ============================================================
# INFO NOTIFICATIONS
# ============================================================
include 'dashboard-instruktur-info_pekan.php';
include 'dashboard-instruktur-info_presensi.php';


# ============================================================
# JADWAL SESI
# ============================================================
echo div_alert('success tengah', "
  <div>23 dari 23 pertanyaan terjawab</div>
  <div class='f12 abu mb1'>Belum ada lagi Peserta Yang Bertanya</div>
  <div>$img_check</div>
");

# ============================================================
# VERIFICATION - LATIHAN/CHALLENGE
# ============================================================
echo div_alert('warning tengah', "
  <div class=mb1>Ada <span class='darkred f20'>12</span> Latihan/Challenge yang harus Anda verifikasi</div>
  <div>$img_next</div>

");

# ============================================================
# VERIFICATION - IMAGE PROFILE
# ============================================================
echo div_alert('success tengah', "
  <div>78 profil dari 93 peserta</div>
  <div class='f12 abu mb1'>Tidak ada Profil Image Peserta yang harus Anda verifikasi</div>
  <div>$img_check</div>
");

# ============================================================
# VERIFICATION - IMAGE PROFILE
# ============================================================
echo div_alert('success tengah', "
  <div class='f12 abu mb1'>Tidak ada Request Reset Password dari peserta</div>
  <div>$img_check</div>
");

# ============================================================
# PENILAIAN
# ============================================================
include 'dashboard-instruktur-penilaian.php';
