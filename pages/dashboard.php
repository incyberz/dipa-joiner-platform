<?php
if (!$id_peserta) jsurl('?login');
if (!$id_room) jsurl('?pilih_room');
$img_check = img_icon('check');
$img_next = img_icon('next');
$null_red = '<span class="red consolas miring">null</span>';


$welcome_kelas = $id_role == 1 ? "kelas <span class='tebal darkblue'>$kelas </span> pada Room <span class='tebal darkblue'>$nama_room</span>" : '';
$login_as_info = $is_login_as ? '<span class="darkred bold">[login_as]</span>' : '';
$welcome = "
  <div>
    Welcome
    <span class='tebal darkblue'>$nama_peserta!</span>
  </div>
  Anda login $login_as_info sebagai
  <span class='tebal darkblue'>$Sebagai</span> 
  $welcome_kelas
";
set_h2("Dashboard", $welcome);
if ($status_room == -1) echo div_alert('info', meme('closed', 6) . '<hr>Room ini sudah ditutup.');

if ($id_role == 1 || $is_login_as) {
?>
  <div class='row '>
    <div class='col-xl-4' data-zzz-aos=fade-up data-zzz-aos-delay=150>
      <?php include 'dashboard-rank.php'; ?>
    </div>

    <div class='col-xl-8' data-zzz-aos=fade-up data-zzz-aos-delay=300>
      <?php include 'dashboard-my-points.php'; ?>
    </div>
  </div>
<?php
} elseif ($id_role == 2) {
  include 'dashboard-instruktur.php';
} elseif ($id_role == 4) {
  if ($status < 2) {
    echo "Anda harus aktivasi sebagai Mitra. Status: $status
    <ul>
      <li>melengkapi biodata</li>
      <li>melengkapi data perusahaan</li>
      <li>membuat request produk</li>
    </ul>
    <ul>
      <li>PRAKERIN</li>
      <li>BURSA KERJA</li>
      <li>ZZZ</li>
    </ul>
    
    ";
  } else {
    echo div_alert('info', "Status Mitra: $status");
  }
} else {
  echo div_alert('info', "Maaf, dashboard khusus untuk role: $sebagai sedang dalam tahap pengembangan. Silahkan klik menu lainnya.");
}

// include 'dashboard_room_stats.php'; 