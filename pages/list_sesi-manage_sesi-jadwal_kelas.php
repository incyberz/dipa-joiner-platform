<?php
# ============================================================
# JADWAL KELAS
# ============================================================
$s = "SELECT kelas FROM tb_room_kelas WHERE id_room=$id_room AND kelas!='INSTRUKTUR' AND ta=$ta";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$th_kelas = '';
$arr_kelas = [];
$arr_sesi_kelas = [];
while ($d = mysqli_fetch_assoc($q)) {
  array_push($arr_kelas, $d['kelas']);
  $th_kelas .= "<th>$d[kelas]</th>";
  $s2 = "SELECT * FROM tb_sesi_kelas a 
  JOIN tb_sesi b ON a.id_sesi=b.id 
  WHERE b.id_room=$id_room
  ";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  while ($d2 = mysqli_fetch_assoc($q2)) {
    // $id2=$d2['id'];
    $arr_sesi_kelas[$d['kelas']][$d2['id_sesi']] = $d2['jadwal_kelas'];
  }
}


















# ============================================================
# PROCESSORS
# ============================================================




























# ============================================================
# LIST ALL SESI
# ============================================================
$s = "SELECT a.* 
FROM tb_sesi a 
WHERE id_room=$id_room 
-- AND jenis = 1  
ORDER BY a.no";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$total_sesi = mysqli_num_rows($q);
$tr = '';
$no_sesi_normal = 0;
$i = 0;
$p_ke = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $info_id_sesi = $username == 'abi' ? "<span class='f10 abu miring'>id: $d[id]</span>" : '';
  $gradasi = $d['jenis'] == 1 ? 'gradasi-hijau' : 'gradasi-pink';
  $gradasi = $d['jenis'] == 0 ? 'gradasi-kuning' : $gradasi;
  if ($d['jenis'] == 1) $p_ke++;

  $td_jadwal_kelas = '';
  foreach ($arr_kelas as $kelas) {
    $jadwal = date('Y-m-d H:i', strtotime($arr_sesi_kelas[$kelas][$d['id']]));
    $td_jadwal_kelas .= "<td>$jadwal</td>";
  }

  $p_ke_show = $d['jenis'] == 1 ? "P$p_ke" : '';

  $tr .= "
    <tr class='$gradasi'>
      <td>$i</td>
      <td>
        <div>$p_ke_show $d[nama]</div>
      </td>
      $td_jadwal_kelas
    </tr>
  ";
}

$total_sesi++;

echo "
  <form method=post>
    <input type=hidden name=new_no value=$total_sesi>
    <table class='table th-toska td-trans'>
      <thead>
        <th>No</th>
        <th>Sesi</th>
        $th_kelas
      </thead>
      $tr
    </table>
  </form>
";
