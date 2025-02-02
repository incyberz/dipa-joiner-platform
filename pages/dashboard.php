<?php
if (!$id_peserta) jsurl('?login');
if (!$id_room) jsurl('?pilih_room');
$img_check = img_icon('check');
$img_next = img_icon('next');
$null_red = '<span class="red consolas miring">null</span>';


$welcome_kelas = $id_role == 1 ? "<li>Kelas <span class='tebal darkblue'>$kelas </span></li>" : '';
$login_as_info = $is_login_as ? '<span class="darkred bold">[login_as]</span>' : '';
$welcome = "
  <div>
    <ul class='left mx-auto' style='max-width: 400px'>
      <li>
        Welcome
        <span class='tebal darkblue'>$nama_peserta!</span>
      </li>
      <li>
        Anda login $login_as_info sebagai
        <span class='tebal darkblue'>$Sebagai</span>
      </li> 
      $welcome_kelas 
      <li> 
        $Room <span class='tebal darkblue'>$nama_room</span>
      </li> 
    </ul> 
    <div class='tengah border-top blue f14 miring pt1 d-md-none'>
      Silahkan klik Menu di pojok-kanan-atas untuk menuju ke halaman lain.
    </div> 

  </div> 
";
set_h2("Dashboard", $welcome);
$link_reactivate = $id_role != 2 ? '' : "<a href='?reactivate_room&id_room=$id_room'>Reactivate</a>";
if ($status_room == -1) echo div_alert('info', meme('closed', 6) . "<hr>Room ini sudah ditutup. $link_reactivate");

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
  echo div_alert('info', "Maaf, dashboard khusus untuk role: $sebagai in development. Silahkan klik menu lainnya.");
}

// include 'dashboard_room_stats.php'; 