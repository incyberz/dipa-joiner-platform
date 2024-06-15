<?php
instruktur_only();
$jeda_sesi = $d_room['jeda_sesi'] ?? 7;
$Minggu = $jeda_sesi == 7 ? 'minggu' : 'hari';
$Minggu = $jeda_sesi == 30 ? 'bulan' : $Minggu;


# ============================================================
# PROCESSORS
# ============================================================
include 'aktivasi_room_processors.php';

# ============================================================
# ARRAY STATUS ROOM 
# ============================================================
include 'include/arr_status_room.php';

# ============================================================
# SET HEADER
# ============================================================
$status_room = $status_room == '' ? 0 : $status_room;
set_h2('Aktivasi Room', "
  Aktivasi Room bertujuan agar Room siap dipakai oleh peserta.
  <div class='wadah mt1 gradasi-toska f20 darkblue'>
    Status Room : Selesai $arr_status_room[$status_room] <span class=consolas>(Tahap $status_room)</span>
  </div>
");

# ============================================================
# NEXT STATUS
# ============================================================
$next_status = $status_room + 1;
$inputs = div_alert('danger', "Belum ada komponen input untuk Next Status : $next_status");

# ============================================================
# REPLACE $inputs with next status form
# ============================================================
$pre_form = '';
$src = "$lokasi_pages/aktivasi_room-status-$next_status.php";
if (file_exists($src)) include $src;

# ============================================================
# FINAL ECHO
# ============================================================
$h3 = $arr_status_room[$next_status] ?? '';
if ($h3) {
  echo "
  <div class='tebal abu miring'>Verifikasi Tahap $next_status</div>
  <h3>$h3</h3>
  <p>$arr_status_room_desc[$next_status]</p>
  $pre_form
  <form method='post'>
    <div class='wadah gradasi-hijau'>
      $inputs
      <button class='btn btn-primary w-100' name=btn_aktivasi id=btn_aktivasi value=$next_status>Aktivasi Berikutnya</button>
    </div>
    <div class=tengah>
      <button class='btn btn-sm btn-secondary' onclick='return confirm(`Batalkan aktivasi room?`)' name=btn_batalkan_aktivasi>Batalkan Aktivasi</button>
    </div>
  
  </form>
";
} else {
  $s = "UPDATE tb_room SET status=100 WHERE id=$id_room";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  echo div_alert('success tengah', '<h2>Selamat Room Anda sudah aktif</h2><hr><a class="btn btn-primary" href="?">Dashboard</a>');
  jsurl('?', 5000);
}
