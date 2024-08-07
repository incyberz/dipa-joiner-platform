<?php
# ============================================================
# INFO TAHUN AJAR DAN PEKAN
# ============================================================
$ta = tahun_ajar($tahun_ajar);
echo div_alert('info tengah', "
  Tahun Ajar Aktif $ta
  <div class='abu miring'>Februari - Juli 2024</div>
");

# ============================================================
# JADWAL SESI
# ============================================================
echo div_alert('info tengah', "
  <div class='f12 abu'>Pekan-id #$week</div>
    <div>
      <a href='?list_sesi'>
        P13 Proyek Tugas Web
      </a>
    </div>
  <div class='f12 abu mb1'>Tatap Muka pada 5 Agustus 2024 | 3 hari lagi</div>
");

# ============================================================
# PRESENSI
# ============================================================
echo div_alert('info tengah', "
  <div class='mb2'>Presensi P13</div>
  <div class='mt2 mb1 f12 abu'>KA-REG-2024 : 34 of 76 (67%)</div>
  <div class=progress>
    <div class=progress-bar style='width:67%'></div>
  </div>
  <div class='mt2 mb1 f12 abu'>MI-REG-2024 : 5 of 12 (43%)</div>
  <div class=progress>
    <div class=progress-bar style='width:43%'></div>
  </div>
  <div class='mt2 mb1 f12 abu'>RPL-REG-2024 : 22 of 26 (88%)</div>
  <div class=progress>
    <div class=progress-bar style='width:88%'></div>
  </div>
  <div class=mt3>
    <a href=?presensi_rekap>$img_next</a>
  </div>
");

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
