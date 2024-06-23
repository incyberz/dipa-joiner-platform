<?php
if (!$id_peserta) jsurl('?login');
if (!$id_room) jsurl('?pilih_room');

$welcome_kelas = $id_role == 1 ? "kelas <span class='tebal darkblue'>$kelas </span> pada Room <span class='tebal darkblue'>$nama_room</span>" : '';
$welcome = "Welcome
    <span class='tebal darkblue'>$nama_peserta</span>
    ! Anda login sebagai
    <span class='tebal darkblue'>$Sebagai</span> 
    $welcome_kelas
";
set_h2("Dashboard $Sebagai", $welcome);
if ($status_room == -1) echo div_alert('info', meme('closed', 6) . '<hr>Room ini sudah ditutup.');

if ($id_role == 1) {
  echo "
    <div class='row '>
      <div class='col-xl-4' data-zzz-aos=fade-up data-zzz-aos-delay=150>
        <?php include 'dashboard-rank.php'; ?>
      </div>

      <div class='col-xl-8' data-zzz-aos=fade-up data-zzz-aos-delay=300>
        <?php include 'dashboard-my-points.php'; ?>
      </div>
    </div>
  ";
} elseif ($id_role == 4) {
  if ($status < 2) {
    echo "Anda harus aktivasi sebagai Mitra. Status: $status";
  } else {
    echo div_alert('danger', "Maaf, belum ada dashboard untuk role: $sebagai");
  }
} else {
  echo div_alert('danger', "Maaf, belum ada dashboard untuk role: $sebagai");
}

// include 'dashboard_room_stats.php'; 