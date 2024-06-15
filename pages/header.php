<?php
$jumlah_verif = 0;
$rjenis = ['latihan', 'challenge'];
if ($id_room) {
  foreach ($rjenis as $key => $jenis) {
    $s = "SELECT 1 FROM tb_bukti_$jenis a 
    JOIN tb_assign_$jenis b ON a.id_assign_$jenis=b.id 
    JOIN tb_sesi c ON b.id_sesi=c.id
    WHERE a.verified_by is null 
    AND c.id_room = $id_room 
    ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $jumlah_verif += mysqli_num_rows($q);
  }
}
$jumlah_ask = 0;
$s = "SELECT 1 FROM tb_pertanyaan WHERE verif_status is null";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_ask = mysqli_num_rows($q);

$red = $available_questions ? 'red' : 'green';
$available_question_show = "<span class='count_badge badge_$red' id='available_questions'>$available_questions</span>";

$target_kelas_header = $id_role == 2 ? 'all' : $kelas;
?>
<header id="header" class="fixed-top d-flex align-items-center">
  <div class="container d-flex align-items-center justify-content-between">

    <div class="logo">
      <img src="assets/img/dipa-logo.png" alt="dipa-logo" class="img-fluid">
      <!-- <h1>DIPA</h1> -->
      <!-- Uncomment below if you prefer to use an image logo -->
      <!-- <a href="index.html"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->
    </div>

    <nav id="navbar" class="navbar">
      <ul>
        <li><a class="nav-link scrollto active" href="?">Home</a></li>
        <!-- <li><a class="nav-link scrollto" href="?pengajar">Pengajar</a></li> -->
        <!-- <li><a class="nav-link scrollto" href="?teams">Teams</a></li> -->
        <!-- <li><a class="nav-link scrollto" href="?peserta">Peserta</a></li> -->
        <li><a class="nav-link scrollto" href="?grades&kelas=<?= $target_kelas_header ?>">Grades</a></li>
        <!-- <li><a class="nav-link scrollto" href="#services">Services</a></li>
        <li><a class="nav-link scrollto " href="#portfolio">Portfolio</a></li>
        <li><a class="nav-link scrollto" href="#team">Team</a></li>
        <li><a class="nav-link scrollto" href="#pricing">Pricing</a></li>
        <li class="dropdown"><a href="#"><span>Drop Down</span> <i class="bi bi-chevron-down"></i></a>
          <ul>
            <li><a href="#">Drop Down 1</a></li>
            <li class="dropdown"><a href="#"><span>Deep Drop Down</span> <i class="bi bi-chevron-right"></i></a>
              <ul>
                <li><a href="#">Deep Drop Down 1</a></li>
                <li><a href="#">Deep Drop Down 2</a></li>
                <li><a href="#">Deep Drop Down 3</a></li>
                <li><a href="#">Deep Drop Down 4</a></li>
                <li><a href="#">Deep Drop Down 5</a></li>
              </ul>
            </li>
            <li><a href="#">Drop Down 2</a></li>
            <li><a href="#">Drop Down 3</a></li>
            <li><a href="#">Drop Down 4</a></li>
          </ul>
        </li>
        <li><a class="nav-link scrollto" href="#contact">Contact</a></li> -->
        <?php
        $nickname_show = isset($_SESSION['dipa_master_username']) ? "Login As $username | $my_points LP" : "$username | $my_points LP";
        $unlog_link = isset($_SESSION['dipa_master_username']) ? "<li><a href='?login_as&unlog'>Unlog</a></li>" : '';
        if ($is_login) {

          $menu_instruktur = '';
          $li_manage_room = '';
          if ($id_role == 2) {
            $li_verif = ($id_role == 1 || !$jumlah_verif) ? '' : "<li><a href='?verif' class='proper'><span class='biru tebal'>Verif</span> <span class='count_badge badge_red' id='jumlah_verif'>$jumlah_verif</span></a></li>";

            $li_ask = !$jumlah_ask ? '' : "<li class='hideit suspend zzz'><a href='?chats' class='proper'><span class='biru tebal'>Chats</span> <span class='count_badge badge_red' id='jumlah_ask'>$jumlah_ask</span></a></li>";

            $li_verif_war = !$jumlah_verif_war ? '' : "<li><a href='?verifikasi_war_profil' class='proper'><span class='biru tebal'>WarProfil</span> <span class='count_badge badge_red' id='jumlah_verif_war'>$jumlah_verif_war</span></a></li>";

            $menu_instruktur = "
              $li_ask
            ";

            $li_manage_room = "
              <li class='gradasi-merah'><a href='?manage_room'>Manage Room</a></li>
              <li class='gradasi-merah'><a href='?assign_room_kelas'>Assign Room Kelas</a></li>
            ";
          }



          echo "
            $menu_instruktur
            <li class='dropdown'><a  href='#'><span class='tebal darkred'>Perang $available_question_show</span> <i class='bi bi-chevron-down'></i></a>
              <ul>
                <li><a href='?perang_soal'>Perang Soal</a></li>
                <li><a href='?tanam_soal'>Tanam Soal</a></li>
                <li><a href='?soal_saya'>Soal Saya</a></li>
                <li><a href='?war_history'>War History</a></li>
                <li><a href='?war_summary'>War Summary</a></li>
                <li><a href='?war_leaderboard'>War Leaderboard</a></li>
                <li><a href='?the_best_investor'>The Best Investor</a></li>
                <li><a href='?the_best_accuracy'>The Best Accuracy</a></li>
                <li><a href='?lazy_soldier'>Lazy Soldier</a></li>
                <li><a href='?good_soldier'>Good Soldier</a></li>
                <li><a href='?war_statistics'>War Statistics</a></li>
                <li><a href='?upload_profil_perang'>Reupload Profil Perang</a></li>
              </ul>
            </li>
            <li class='dropdown'><a  href='#'><span class='tebal darkblue'>$room</span> <i class='bi bi-chevron-down'></i></a>
              <ul>
                <li><a href='?pilih_room'>Pilih Room</a></li>
                <li><a href='?list_sesi'>Learning Path</a></li>
                <li><a href='?peserta_kelas'>Peserta Kelas</a></li>
                <li><a href='?activity&jenis=latihan'>Latihan</a></li>
                <li><a href='?activity&jenis=challenge'>Challenges</a></li>
                <li class='hideit'><a href='?bertanya'>Bertanya</a></li>
                <li class='hideit'><a href='?my_questions'>Pertanyaan Saya</a></li>
                <li class='hideit'><a href='?chats'>Chats</a></li>
                <li class=hideit><a href='?quiz'>Kuis PG</a></li>
                <li class=><a href='?presensi'>Presensi</a></li>
                $li_manage_room
              </ul>
            </li>

            <li><a href='?ujian'>Ujian</a></li>

            <li class='dropdown'><a class=getstarted href='#'><span>$nickname_show</span> <i class='bi bi-chevron-down'></i></a>
              <ul>
                <li><a href='?get_point'>Dapatkan Poin</a></li>
                <li><a href='?my_points'>My Points</a></li>
                <li><a href='?nilai_akhir'>Nilai Akhir</a></li>
                <li><a href='?upload_profil'>My Profile</a></li>
                <li><a href='?biodata'>My Biodata</a></li>
                <li><a href='?verifikasi_profil_peserta'>Verifikasi Profil Peserta</a></li>
                <li><a href='?polling'>Polling UTS</a></li>
                <li><a href='?polling&u=uas'>Polling UAS</a></li>
                <li class='hideit'><a href='?my_testimony'>My Testimony</a></li>
                <li class=hideit><a href='?my_biodata'>My Biodata</a></li>
                <li><a href='?ubah_password'>Ubah Password</a></li>
                <li><a href='?logout' onclick='return confirm(\"Yakin untuk Logout?\")' class=red>Logout</a></li>
              </ul>
            </li>
            <li class='darkred f10 tengah' style='margin-left:10px'>$kelas_show</li>
            $unlog_link
          ";
        } else {
          echo "<li><a class='getstarted scrollto' href='?login'>Login</a></li>";
        }
        ?>
      </ul>
      <i class="bi bi-list mobile-nav-toggle"></i>
    </nav><!-- .navbar -->

  </div>
</header>