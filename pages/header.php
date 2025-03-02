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
    // die($s);
  }
}
// $jumlah_ask = 0;
// $s = "SELECT 1 FROM tb_bertanya WHERE verif_status is null";
// $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
// $jumlah_ask = mysqli_num_rows($q);

$red = $available_questions ? 'red' : 'green';
$available_question_show = "<span class='count_badge badge_$red' id='available_questions'>$available_questions</span>";

$target_kelas_header = $id_role == 2 ? 'all' : $kelas;
$header_logo = $is_custom ? "$path_custom/custom-header-logo.png" : 'assets/img/dipa-logo.png';
?>
<header id="header" class="fixed-top d-flex align-items-center">
  <div class="container d-flex align-items-center justify-content-between">

    <div style="position: absolute; z-index: -100; color:white; background: white; height: 1px; width: 1px; overflow: hidden">
      <h1><?= $meta_title ?></h1>
      <p><?= $meta_description ?></p>
    </div>

    <div class="logo">
      <img src="<?= $header_logo ?>" alt="lms-logo" class="img-fluid">
    </div>

    <nav id="navbar" class="navbar">
      <ul>
        <?php

        $nickname_show = isset($_SESSION['dipa_master_username']) ? "Login As $username" : $username;
        $unlog_link = isset($_SESSION['dipa_master_username']) ? "<li><a href='?login_as&unlog'>Unlog</a></li>" : '';

        if ($id_role != 4) {
          echo "
            <li><a class='nav-link scrollto active' href='?'>Home</a></li>
            <li><a class='nav-link scrollto' href='?leaderboard'>$Leaderboard</a></li>
          ";
        }

        if ($is_login) {

          # ============================================================
          # MENU INSTRUKTUR
          # ============================================================
          $menu_instruktur = '';
          $li_manage_room = '';
          if ($id_role == 2) {
            $li_verif = !$jumlah_verif ? '' : "<li><a href='?verif' class='proper'><span class='biru tebal'>Verif</span> <span class='count_badge badge_red' id='jumlah_verif'>$jumlah_verif</span></a></li>";

            // $li_ask = !$jumlah_ask ? '' : "<li class='hideit suspend zzz'><a href='?chats' class='proper'><span class='biru tebal'>Chats</span> <span class='count_badge badge_red' id='jumlah_ask'>$jumlah_ask</span></a></li>";
            $li_ask = '';

            $li_verif_war = !$jumlah_verif_war ? '' : "<li><a href='?verifikasi_war_profil' class='proper'><span class='biru tebal'>WarProfil</span> <span class='count_badge badge_red' id='jumlah_verif_war'>$jumlah_verif_war</span></a></li>";

            $menu_instruktur = "
              $li_verif
              $li_ask
            ";

            $li_manage_room = "
              <li class='gradasi-merah'><a href='?manage_room'>Manage $Room</a></li>
              <li class='gradasi-merah'><a href='?manage_kelas'>Manage Kelas</a></li>
            ";
          }

          $Peserta_Kelas = $id_role == 1 ? 'Teman Sekelas' : "Peserta $Room ini";

          if ($id_role == 1 || $id_role == 2) {
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
                  <li class=hideit><a href='?lazy_soldier'>Lazy Soldier</a></li>
                  <li class=hideit><a href='?good_soldier'>Good Soldier</a></li>
                  <li><a href='?war_statistics'>War Statistics</a></li>
                  <li><a href='?upload_profil_perang'>Reupload Profil Perang</a></li>
                </ul>
              </li>
              <li class='dropdown'><a  href='#'><span class='tebal darkblue'>Belajar</span> <i class='bi bi-chevron-down'></i></a>
                <ul>
                  <li><a href='?lp'>Learning Path</a></li>
                  <li class=><a href='?presensi'>Presensi</a></li>
                  <li><a href='?activity&jenis=latihan'>Latihan</a></li>
                  <li><a href='?activity&jenis=challenge'>Challenges</a></li>
                  <li class='hideit' ><a href='?proyek_akhir'>Proyek Akhir</a></li>
                  <li class='hideit'><a href='?bertanya'>Fitur Bertanya</a></li>
                  <li class='hideit'><a href='?questions'>List Bertanya</a></li>
                  <li class=''><a href='?ujian'>Quiz | Ujian</a></li>
                  <li class=''><a href='?nilai_akhir'>Nilai Akhir</a></li>
                  <li><a href='?pilih_room'>Ganti $Room</a></li>
                  $li_manage_room
                </ul>
              </li>


              <li class='dropdown'><a class=getstarted href='#'><span>$nickname_show</span> <i class='bi bi-chevron-down'></i></a>
                <ul>
                  <li><a href='?get_point'>Dapatkan Poin</a></li>
                  <li><a href='?peserta_kelas'>$Peserta_Kelas</a></li>
                  <li><a href='?upload_profil'>My Profile</a></li>
                  <li><a href='?biodata'>My Biodata</a></li>
                  <li><a href='?verifikasi_profil_peserta'>Verifikasi Profil Peserta</a></li>
                  <li><a href='?polling'>Polling UTS</a></li>
                  <li><a href='?polling&u=uas'>Polling UAS</a></li>
                  <li><a href='?room_info'>Room Info</a></li>
                  <li class='hideit'><a href='?my_testimony'>My Testimony</a></li>
                  <li class=hideit><a href='?my_biodata'>My Biodata</a></li>
                  <li><a href='?ubah_password'>Ubah Password</a></li>
                  <li><a href='?logout' onclick='return confirm(`Yakin untuk Logout?`)' class=red>Logout</a></li>
                </ul>
              </li>
              $unlog_link
            ";
          } elseif ($id_role == 4) {
            # ============================================================
            # MENU MITRA
            # ============================================================
            echo "
            <li><a href='?dashboard'>Dashboard</a></li>
            <li><a href='?challenge_mitra'>Challenge Mitra</a></li>
            <li><a href='?logout' onclick='return confirm(`Logout?`)'>Logout</a></li>
            ";
          }
        } elseif (!$is_login) {
          echo "<li><a class='getstarted scrollto' href='?login'>Login</a></li>";
        }
        ?>
      </ul>
      <i class="bi bi-list mobile-nav-toggle"></i>
    </nav>

  </div>
</header>