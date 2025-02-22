<?php
set_h2('Kalkulasi Leaderboard', "Week $week");
include "includes/form_target_kelas.php";

# ============================================================
# CEK JIKA ADA DATA POIN WEEKLY HARI INI
# ============================================================
$s = "SELECT * FROM tb_poin_weekly WHERE id='$id_room-$week' AND update_at = '$today'";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q)) {
  # ============================================================
  # GUNAKAN DATA POIN WEEKLY
  # ============================================================
  // echolog('GUNAKAN DATA POIN WEEKLY');
  $row = [];
  $d = mysqli_fetch_assoc($q);
  $t = explode('|', $d['poin_kbms']);
  foreach ($t as $k => $v) {
    if ($v) {
      $t2 = explode(':', $v);
      $id_pes = $t2[0];

      $t3 = explode(',', $t2[1]);

      $row[$id_pes] = [
        'presensi' => $t3[0],
        'latihan' => $t3[1],
        'challenge' => $t3[2],
        'play_kuis' => $t3[3],
        'tanam_soal' => $t3[4],
      ];
    }
  }

  $t = explode('|', $d['rank_kelass']);
  $rank_kelas = [];
  foreach ($t as $k => $v) {
    if ($v) {
      $t2 = explode(':', $v);
      $ckelas = $t2[0];
      $rank_kelas[$ckelas] = [];

      $t3 = explode(',', $t2[1]);

      foreach ($t3 as $key_rank => $id_pes) {
        if ($id_pes) {
          $row[$id_pes]['rank_kelas'] = $key_rank + 1;
          array_push($rank_kelas[$ckelas], $id_pes);
        }
      }
    }
  }

  $t = explode('|', $d['rank_rooms']);
  foreach ($t as $k => $v) {
    if ($v) {
      $t2 = explode(',', $v);
      foreach ($t2 as $key_rank => $id_pes) {
        if ($id_pes) {
          $row[$id_pes]['rank_room'] = $key_rank + 1;
        }
      }
    }
  }


  # ============================================================
  # MAIN SELECT PESERTA
  # ============================================================
  $s = "SELECT 
  a.id,
  c.kelas,
  a.nama
  FROM tb_peserta a 
  JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
  JOIN tb_kelas c ON b.kelas=c.kelas 
  JOIN tb_room_kelas d ON c.kelas=d.kelas 
  WHERE d.id_room = $id_room 
  AND a.id_role = 1 -- mhs only
  AND a.status = 1 -- mhs aktif 
  AND d.ta = $ta_aktif 
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $rpes = [];
  while ($d = mysqli_fetch_assoc($q)) {
    $rpes[$d['id']] = $d;
  }

  /*

  # ============================================================
  # USER ROWS
  # ============================================================
  $tr = '';
  $i = 0;
  foreach ($row as $id_pes => $v) {
    $crank_kelas = $v['rank_kelas'] ?? '-'; // exception for all KBM null
    $crank_room = $v['rank_room'] ?? '-';
    $nama = strtoupper($rpes[$id_pes]['nama']);
    $kelas = $rpes[$id_pes]['kelas'];
    $i++;
    $tr .= "
      <tr>
        <td>$i</td>
        <td>$nama</td>
        <td>$kelas</td>
        <td>$v[presensi]</td>
        <td>$v[latihan]</td>
        <td>$v[challenge]</td>
        <td>$v[play_kuis]</td>
        <td>$v[tanam_soal]</td>
        <td>$crank_kelas</td>
        <td>$crank_room</td>
      </tr>
    ";
  }

  echo "
    <table class='table table-striped table-hover'>
      <thead>
        <th>No</th>
        <th>Nama</th>
        <th>kelas</th>
        <th>presensi</th>
        <th>latihan</th>
        <th>challenge</th>
        <th>play_kuis</th>
        <th>tanam_soal</th>
        <th>rank_kelas</th>
        <th>rank_room</th>
      </thead>
      $tr
    </table>
  ";
  */

  # ============================================================
  # USE RANK KELAS
  # ============================================================
  $div_rank = '';
  $col = intval(12 / count($rank_kelas)); // bootstrap col
  if ($col < 4) $col = 4;

  foreach ($rank_kelas as $ckelas => $ids) {

    $tr = '';
    $i = 0;
    foreach ($ids as $key => $id_pes) {
      $i++;
      $nama = strtoupper($rpes[$id_pes]['nama']);
      $ckelas = str_replace('--', '-', str_replace("-$ta_aktif", '', $rpes[$id_pes]['kelas']));
      $data = $row[$id_pes];
      $poin = 0;
      foreach ($data as $k2 => $v2) {
        if ($k2 == 'rank_kelas' || $k2 == 'rank_room') continue;
        $poin += intval($v2);
      }
      $rank = $key + 1;
      $poin = number_format($poin);
      $hideit = $i > 10 ? 'hideit' : '';
      $tr .= "
        <tr class='$hideit tr__$ckelas'>
          <td>$rank</td>
          <td>$nama</td>
          <td>$poin</td>
        </tr>
      ";
    }

    $show_all = count($ids) > 10  ? "<div class='tengah abu mb4'><i class='show_all pointer' id=show_all__$ckelas>Show All</i></div>" : '';



    $div_rank .= "
      <div class='col-lg-$col f12'>
        <h3 class='f16 bold darkblue tengah gradasi-toska border-top p2'>$ckelas</h3>
        <table class='table table-striped table-hover mb1'>
          <thead>
            <th>Rank</th>
            <th>Nama</th>
            <th>Poin</th>
          </thead>
          $tr
        </table>
        $show_all
      </div>
    ";
  }

  echo "<div class=row>$div_rank</div>";
?>
  <script>
    $(function() {
      $(".show_all").click(function() {
        let tid = $(this).prop('id');
        let rid = tid.split('__');
        let aksi = rid[0];
        let id = rid[1];
        $('.tr__' + id).show();
        $(this).hide();
      })
    })
  </script>
<?php
} else {
  # ============================================================
  # KALKULASI DAN UPDATE REALTIME POIN
  # ============================================================
  echolog('KALKULASI DAN UPDATE REALTIME POIN');
  # ============================================================
  # MAIN SELECT PESERTA
  # ============================================================
  $s = "SELECT 
  a.id,
  c.kelas,
  a.nama,
  (
    SELECT 
    SUM(p.poin) 
    FROM tb_presensi p 
    JOIN tb_sesi q ON p.id_sesi=q.id 
    WHERE id_peserta=a.id
    -- AND p.is_ontime = 1 -- Ontime Only 
    AND q.id_room = $id_room  
    -- AND s.ta = $ta_aktif 
    ) poin_presensi,
  (
    SELECT 
    SUM(p.get_point + COALESCE(p.poin_antrian,0) + COALESCE(p.poin_apresiasi,0)) 
    FROM tb_bukti_latihan p 
    JOIN tb_assign_latihan q ON p.id_assign_latihan=q.id 
    JOIN tb_room_kelas r ON q.id_room_kelas=r.id 
    JOIN tb_kelas s ON r.kelas=s.kelas  
    WHERE id_peserta=a.id
    AND p.status = 1 -- Verified Poin 
    AND s.ta = $ta_aktif ) poin_latihan,
  (
    SELECT 
    SUM(p.get_point + COALESCE(p.poin_antrian,0) + COALESCE(p.poin_apresiasi,0)) 
    FROM tb_bukti_challenge p 
    JOIN tb_assign_challenge q ON p.id_assign_challenge=q.id 
    JOIN tb_room_kelas r ON q.id_room_kelas=r.id 
    JOIN tb_kelas s ON r.kelas=s.kelas  
    WHERE id_peserta=a.id
    AND p.status = 1 -- Verified Poin 
    AND s.ta = $ta_aktif ) poin_challenge,
  (
    SELECT 
    SUM(p.poin_penjawab) 
    FROM tb_war p 
    JOIN tb_room q ON p.id_room=q.id 
    WHERE p.id_penjawab=a.id
    AND q.status = 100 -- Active $Room 
    -- AND q.ta=$ta_aktif 
    ) poin_play_kuis,
  (
    SELECT SUM(p.poin_membuat_soal) 
    FROM tb_soal_peserta p 
    JOIN tb_sesi q ON p.id_sesi=q.id 
    WHERE p.id_pembuat=a.id  
    AND q.id_room=$id_room
    ) + (
    SELECT SUM(poin_pembuat) 
    FROM tb_war p 
    WHERE p.id_pembuat=a.id 
    AND p.id_room=$id_room
    ) as poin_tanam_soal
  
  FROM tb_peserta a 
  JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
  JOIN tb_kelas c ON b.kelas=c.kelas 
  JOIN tb_room_kelas d ON c.kelas=d.kelas 
  
  WHERE d.id_room = $id_room 
  AND a.id_role = 1 -- mhs only
  AND a.status = 1 -- mhs aktif 
  AND d.ta = $ta_aktif 
  
  ORDER BY kelas, nama 
  ";

  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $tr = '';
  $poin_kbms = ''; // for tb_poin_weekly | id:lat=poin,
  if (mysqli_num_rows($q)) {
    $i = 0;
    $th = '';
    $last_kelas = '';
    $rpairs = [];
    $rkum_poin = [];
    while ($d = mysqli_fetch_assoc($q)) {
      $separator = '';
      $i++;
      if ($last_kelas != $d['kelas'] and $i > 1) $separator = "border-top:solid 5px #faf";
      $td = '';
      $pairs = '';
      $akumulasi_poin = 0;
      // $poin_kbms = ''; // for tb_poin_weekly | id:lat=poin,
      $poin_kbms .= "$d[id]:"; // for tb_poin_weekly | id:lat=poin,
      foreach ($d as $key => $value) {

        # ============================================================
        # KOLOMS DAN VALUES
        # ============================================================
        if (!($key == 'nama' || $key == 'kelas' || $key == 'id')) {
          $akumulasi_poin += $value;
          $koma = $pairs ? ',' : '';
          $pairs .= "$koma$key = '$value'";
          // $poin_kbms = ''; // for tb_poin_weekly | id:lat=poin,
          $poin_kbms .= "$value,"; // for tb_poin_weekly | id:poin,
        }

        # ============================================================
        # KOLOM HANDLER UI
        # ============================================================
        if (
          $key == 'id'
          || $key == 'date_created'
        ) {
          continue;
        } elseif ($key == 'nama') {
          $value = strtoupper("$i. $value");
        }
        if ($i == 1) {
          $kolom = key2kolom($key);
          $th .= "<th>$kolom</th>";
        }
        $td .= "<td>$value</td>";
      } // end foreach
      $poin_kbms .= "|"; // end separator

      # ============================================================
      # AUTOSAVE TB_POIN 
      # ============================================================
      $rpairs["$id_room-$d[id]"] = $pairs; // tampung pairs kolom poin
      $rkum_poin["$id_room-$d[id]"] = $akumulasi_poin; // tampung pairs kolom poin

      $tr .= "
        <tr style='$separator'>
          $td
        </tr>
      ";
      $last_kelas = $d['kelas'];
    }
  }

  $tb = $tr ? "
    <table class=table>
      <thead>$th</thead>
      $tr
    </table>
  " : div_alert('danger', "Data XXX tidak ditemukan.");
  echo "
    <div class='wadah gradasi-toska'>
      <h3>POIN KBM</h3>
      $tb
    </div>
  ";


  # ============================================================
  # DATA POIN | RANK KELAS
  # ============================================================
  $s = "SELECT 
  e.akumulasi_poin,
  a.nama,
  a.id,
  c.kelas 
  
  FROM tb_peserta a 
  JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
  JOIN tb_kelas c ON b.kelas=c.kelas 
  JOIN tb_room_kelas d ON c.kelas=d.kelas 
  JOIN tb_poin e ON a.id=e.id_peserta 
  WHERE d.id_room = $id_room 
  AND a.id_role = 1 -- mhs only
  AND a.status = 1 -- mhs aktif 
  AND d.ta = $ta_aktif 
  AND e.id_room = $id_room 
  
  ORDER BY c.kelas, e.akumulasi_poin DESC
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $tr = '';
  $i = 0;
  $rank = 0;
  $last_kelas = '';
  $rank_kelass = ''; // untuk tb_poin_weekly | kelas=id_pesertas,
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $rank++;
    $separator = '';
    if ($last_kelas != $d['kelas']) {
      $rank = 1;
      $separator = "border-top:solid 5px #faf";
      $rank_kelass .= "|$d[kelas]:"; // untuk tb_poin_weekly | kelas=id_pesertas,
    }
    $nama = strtoupper($d['nama']);

    # ============================================================
    # TAMPUNG RANK KELAS KE PAIRS 
    # ============================================================
    $k = "$id_room-$d[id]";
    $rpairs[$k] .= ", rank_kelas = $rank";
    $rank_kelass .= "$d[id],"; // untuk tb_poin_weekly | kelas=id_pesertas,


    # ============================================================
    # FINAL TR RANK KELAS
    # ============================================================
    $tr .= "
      <tr id=tr__$d[id] style='$separator'>
        <td>$i</td>
        <td>$nama</td>
        <td>$d[akumulasi_poin]</td>
        <td>$rank</td>
      </tr>
    ";
    $last_kelas = $d['kelas'];
  }


  echo "
    <div class='wadah gradasi-toska'>
      <h3>RANK KELAS</h3>
      <table class=table>
        <thead>
          <th>No</th>
          <th>Nama</th>
          <th>Poin</th>
          <th>Rank Kelas</th>
        </thead>
        $tr
      </table>
    </div>
  ";


  # ============================================================
  # DATA POIN | RANK ROOM
  # ============================================================
  $s = "SELECT 
  e.akumulasi_poin,
  a.nama,
  a.id,
  c.kelas 
  
  FROM tb_peserta a 
  JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
  JOIN tb_kelas c ON b.kelas=c.kelas 
  JOIN tb_room_kelas d ON c.kelas=d.kelas 
  JOIN tb_poin e ON a.id=e.id_peserta 
  WHERE d.id_room = $id_room 
  AND a.id_role = 1 -- mhs only
  AND a.status = 1 -- mhs aktif 
  AND d.ta = $ta_aktif 
  AND e.id_room = $id_room 
  
  ORDER BY e.akumulasi_poin DESC
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $tr = '';
  $rank = 0;
  $rank_rooms = '';
  while ($d = mysqli_fetch_assoc($q)) {
    $rank++;
    $rank_rooms .= "$d[id],";
    $nama = strtoupper($d['nama']);

    # ============================================================
    # TAMPUNG RANK ROOM KE PAIRS 
    # ============================================================
    $k = "$id_room-$d[id]";
    $rpairs[$k] .= ", rank_room = $rank";

    # ============================================================
    # FINAL TR RANK ROOM
    # ============================================================
    $tr .= "
      <tr id=tr__$d[id] >
        <td>$rank</td>
        <td>$nama</td>
        <td>$d[akumulasi_poin]</td>
      </tr>
    ";
  }

  echo "
    <div class='wadah gradasi-toska'>
      <h3>RANK</h3>
      <table class=table>
        <thead>
          <th>Rank</th>
          <th>Nama</th>
          <th>Poin</th>
        </thead>
        $tr
      </table>
    </div>
  ";

  foreach ($rpairs as $key => $pairs) {
    $t = explode('-', $key);

    $s2 = "UPDATE tb_poin SET 
    $pairs,
    akumulasi_poin = '$rkum_poin[$key]'
    WHERE id_peserta = $t[1] 
    AND id_room = $id_room 
  
    ";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  }


  # ============================================================
  # UPDATE TB_POIN_WEEKLY
  # ============================================================
  $s = "INSERT INTO tb_poin_weekly (
    id,
    update_at,
    poin_kbms,
    rank_rooms,
    rank_kelass
  ) VALUES (
    '$id_room-$week',
    '$today',
    '$poin_kbms',
    '$rank_rooms',
    '$rank_kelass'
  ) ON DUPLICATE KEY UPDATE 
    update_at = '$today',
    poin_kbms = '$poin_kbms',
    rank_rooms = '$rank_rooms',
    rank_kelass = '$rank_kelass'
  ";
  $q2 = mysqli_query($cn, $s) or die(mysqli_error($cn));
} // end update realtime
