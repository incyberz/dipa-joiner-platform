<?php
# ============================================================
# WEEKLY PUBLIC LEADERBOARD
# ============================================================
$w = date('w', $week * 7 * 24 * 60 * 60);
$week_start = date('Y-m-d', strtotime("-$w day", $week * 7 * 24 * 60 * 60));
$week_end = date('Y-m-d', strtotime("+6 day", strtotime($week_start)));

$start = date('d-M-Y', strtotime($week_start));
$end = date('d-M-Y', strtotime($week_end));
$info = "<div class='f12 abu consolas'>$start s.d $end</div>";
$link = $username ? "<a href=?leaderboard>Best Class</a>" : '';
set_h2('The Best Top 10', "Global Rank Poin Terbaik minggu ke-$week$info $link");


# ============================================================
# CEK JIKA ADA DATA POIN PUBLIC HARI INI
# ============================================================
$s = "SELECT * FROM tb_poin_weekly_public WHERE id='$week'"; // data poin publik minggu ini
// echolog($s);
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q)) {
  # ============================================================
  # GUNAKAN DATA POIN WEEKLY ROW MANAGER PUBLIC
  # ============================================================
  // echolog('GUNAKAN DATA POIN WEEKLY ROW MANAGER PUBLIC');
  $d = mysqli_fetch_assoc($q);
  $t = explode('|', $d['ranks']);

  # ============================================================
  # MAIN SELECT PESERTA ALL ROOM AKTIF
  # ============================================================
  include 'rpeserta.php';

  $rranks = [];
  $i = 0;
  foreach ($t as $k => $v) {
    if ($v) {
      $i++; // index rank from 0, menyesuaikan ke ldb-public-show.php
      $t2 = explode(':', $v);
      $id_pes = $t2[0];

      $peserta = $rpeserta[$id_pes] ?? "Tidak ada data pada array peserta dg id: $id_pes";

      if ($id_role != 2) {
        if ($id_pes != $id_peserta) {
          if ($i > 10) continue;
        }
      }
      $rranks[$i - 1] = [
        'id' => $id_pes,
        'rank' => $i,
        'poin' => $t2[1],
        'nama' => $peserta['nama'],
        'kelas' => $peserta['kelas'],
        'image' => $peserta['image'],
        'war_image' => $peserta['war_image'],
      ];
    }
  }


  # ============================================================
  # LEADERBOARD PUBLIC SHOW
  # ============================================================
  include 'leaderboard-public-show.php';
  # ============================================================
} else {
  // instruktur_only(); // public can auto-update
  # ============================================================
  # UPDATE REALTIME POIN FOR PUBLIC
  # ============================================================
  include 'leaderboard-update_public_poin.php';
  # ============================================================
} // end update public poin
