<?php
# ============================================================
# FOR ALL ACTIVE ROOM
# ============================================================
echolog('UPDATE PUBLIC POIN');



# ============================================================
# DATA POIN | RANK ROOM
# ============================================================
$s = "SELECT 
ROW_NUMBER() OVER (ORDER BY a.nama) no,
-- e.akumulasi_poin,
UPPER(a.nama) nama,
a.id as id_peserta,
c.kelas,
(
  SELECT 
  SUM(p.poin) 
  FROM tb_presensi p 
  JOIN tb_sesi q ON p.id_sesi=q.id 
  JOIN tb_room r ON q.id_room=r.id  
  WHERE p.id_peserta=a.id 
  AND r.status = 100 -- hanya Room aktif
  AND p.is_ontime = 1 -- Ontime Only 
  AND p.tanggal >= '$week_start' 
  AND p.tanggal <= '$week_end 23:59:59'
  -- AND q.id_room = $id_room  
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
  AND s.ta = $ta_aktif 
  AND p.tanggal_upload >= '$week_start' 
  AND p.tanggal_upload <= '$week_end 23:59:59'
  ) poin_latihan,
(
  SELECT 
  SUM(p.get_point + COALESCE(p.poin_antrian,0) + COALESCE(p.poin_apresiasi,0)) 
  FROM tb_bukti_challenge p 
  JOIN tb_assign_challenge q ON p.id_assign_challenge=q.id 
  JOIN tb_room_kelas r ON q.id_room_kelas=r.id 
  JOIN tb_kelas s ON r.kelas=s.kelas  
  WHERE id_peserta=a.id
  AND p.status = 1 -- Verified Poin 
  AND s.ta = $ta_aktif 
  AND p.tanggal_upload >= '$week_start' 
  AND p.tanggal_upload <= '$week_end 23:59:59'
  ) poin_challenge,
(
  SELECT 
  SUM(p.poin_penjawab) 
  FROM tb_war p 
  JOIN tb_room q ON p.id_room=q.id 
  WHERE p.id_penjawab=a.id
  AND q.status = 100 -- Active $Room 
  -- AND q.ta=$ta_aktif 
  AND p.tanggal >= '$week_start' 
  AND p.tanggal <= '$week_end 23:59:59'
  ) poin_play_kuis,
(
  SELECT SUM(p.poin_membuat_soal) 
  FROM tb_soal_peserta p 
  JOIN tb_sesi q ON p.id_sesi=q.id 
  WHERE p.id_pembuat=a.id  
  AND 1 -- q.id_room=$id_room
  AND p.tanggal >= '$week_start' 
  AND p.tanggal <= '$week_end 23:59:59'
  ) + (
  SELECT SUM(poin_pembuat) 
  FROM tb_war p 
  WHERE p.id_pembuat=a.id 
  AND 1 -- p.id_room=$id_room
  AND p.tanggal >= '$week_start' 
  AND p.tanggal <= '$week_end 23:59:59'
  ) poin_tanam_soal

FROM tb_peserta a 
JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
JOIN tb_kelas c ON b.kelas=c.kelas 
-- JOIN tb_room_kelas d ON c.kelas=d.kelas 
-- JOIN tb_poin e ON a.id=e.id_peserta 
WHERE 1 -- d.id_room = $id_room 
AND a.id_role = 1 -- mhs only
AND a.status = 1 -- mhs aktif 
AND c.ta = $ta_aktif -- HANYA KELAS AKTIF DI TA INI
AND 1 -- e.id_room = $id_room 

ORDER BY 
a.nama 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
if (mysqli_num_rows($q)) {
  $i = 0;
  $th = '';
  $rkum_poin = [];
  $rranks = [];
  $poin_kbms = '';
  while ($d = mysqli_fetch_assoc($q)) {
    $poin_kbms .= "|$d[id_peserta]:";
    $i++;
    $td = '';
    $akumulasi_poin = 0;
    foreach ($d as $key => $value) {

      if (
        $key == 'id'
        || $key == 'id_peserta'
        || $key == 'date_created'
      ) {
        continue;
      } elseif ($key == 'nama' || $key == 'no' || $key == 'kelas') {
        // $value = "$i. $value";
      } else {
        if (substr($key, 0, 5) == 'poin_') {
          # ============================================================
          # HANYA FIELD POIN KBMS
          # ============================================================
          $poin_kbms .= "$value,";
          $akumulasi_poin += $value;
          # ============================================================
        }
      }

      if ($i == 1) {
        $kolom = key2kolom($key);
        $th .= "<th>$kolom</th>";
      }

      $td .= "<td>$value</td>";
    }
    $tr .= "
      <tr>
        $td
        <td>$akumulasi_poin</td>
      </tr>
    ";
    $rranks["$d[id_peserta]"] = $akumulasi_poin;
  }
} else {
  die("Belum ada Peserta Aktif di TA $ta_aktif.");
}

$tb = $tr ? "
  <table class=table>
    <thead>
      $th
      <th>Sum Poin</th>
    </thead>
    $tr
  </table>
" : div_alert('danger', "Belum ada $Peserta");
echo "
  <div class='wadah gradasi-toska'>
    <h3>Kalkulasi Poin KBM minggu-$week</h3>
    $tb
  </div>
";


# ============================================================
# URUTKAN RANKING
# ============================================================
arsort($rranks);
$ranks = '';
foreach ($rranks as $id_pes => $poin) {
  $ranks .= "|$id_pes:$poin";
}

# ============================================================
# UPDATE TB_POIN_WEEKLY_PUBLIC
# ============================================================
$s = "INSERT INTO tb_poin_weekly_public (
  id,
  update_at,
  poin_kbms,
  ranks
) VALUES (
  '$week',
  '$today',
  '$poin_kbms',
  '$ranks'
) ON DUPLICATE KEY UPDATE 
  update_at = '$today',
  poin_kbms = '$poin_kbms',
  ranks = '$ranks'
";
echolog($s);
$q2 = mysqli_query($cn, $s) or die(mysqli_error($cn));
