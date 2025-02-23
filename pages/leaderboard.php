<?php
$bulan = date('M Y');
$public = $_GET['public'] ?? null;
$link_public = $public ? '' : ' | <a href=?leaderboard&public=1>Public</a>';
if (!$username || $public) {
  # ============================================================
  # PUBLIC LEADERBOARD
  # ============================================================
  $global = 1; // set as global leaderboard
  $public = 1; // set as public
  include 'leaderboard-public.php';
  # ============================================================
} else {
  # ============================================================
  # LOGGED USER LEADERBOARD
  # ============================================================
  $global = $_GET['global'] ?? null;
  if ($id_role == 2 and !$global) {
    set_h2('Leaderboard', "Minggu ke-$week -  $bulan $link_public");
    include "includes/form_target_kelas.php"; // dosen memilih masuk di kelas mana?
  }

  # ============================================================
  # CEK JIKA ADA DATA POIN WEEKLY HARI INI
  # ============================================================
  $s = "SELECT * FROM tb_poin_weekly WHERE id='$id_room-$week' AND update_at = '$today'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    # ============================================================
    # GUNAKAN DATA POIN WEEKLY ROW MANAGER
    # ============================================================
    $row = []; // data poin KBM
    $rrank_kelas = []; // rank per kelas
    $rrank_room = []; // rank per room
    include 'leaderboard-poin_weekly-row_manager.php';
    # ============================================================

    # ============================================================
    # MAIN SELECT PESERTA ROOM
    # ============================================================
    include 'rpeserta_room.php';

    # ============================================================
    # TAMPIL DETAIL ROWS POIN
    # ============================================================
    // include 'leaderboard-poin_weekly-show.php';

    # ============================================================
    # USE RANK KELAS
    # ============================================================
    if ($target_kelas || $id_role == 1) { // jika ada target kelas atau untuk individu mhs
      # ============================================================
      # THE BEST TIAP KELAS
      # ============================================================
      include 'leaderboard-the_best_kelas.php';
      # ============================================================
    } else { // untuk dosen, tanpa target kelas, show all kelas
      # ============================================================
      # SHOW ALL RANKING KELAS
      # ============================================================
      include 'leaderboard-show_all_ranking_kelas.php';
      # ============================================================
    } // end if tidak ada target_kelas
  } else {
    instruktur_only();
    # ============================================================
    # UPDATE REALTIME POIN
    # ============================================================
    echolog('UPDATE REALTIME KUMULATIF POIN');
    include 'leaderboard-update_realtime_kumulatif_poin.php';
    # ============================================================
  } // end update realtime kumulatif poin
} // end logged user
