<?php
set_h2('SKS Mengajar');
instruktur_only();

if (isset($_POST['periode'])) jsurl("?sks_mengajar&periode=$_POST[periode]");
$get_periode = $_GET['periode'] ?? null;
$bulan_ini = intval(date('m'));
$arr_hari = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

$rperiode = [
  0 => ['Tahun ini', '2024-12-21', '2025-12-20'],
  1 => ['Jan', '2024-12-21', '2025-01-20'],
  2 => ['Feb', '2025-01-21', '2025-02-20'],
  3 => ['Mar', '2025-02-21', '2025-03-20'],
  4 => ['Apr', '2025-03-21', '2025-04-20'],
  5 => ['Mei', '2025-04-21', '2025-05-20'],
  6 => ['Jun', '2025-05-21', '2025-06-20'],
  7 => ['Jul', '2025-06-21', '2025-07-20'],
  8 => ['Agu', '2025-07-21', '2025-08-20'],
  9 => ['Sep', '2025-08-21', '2025-09-20'],
  10 => ['Okt', '2025-09-21', '2025-10-20'],
  11 => ['Nov', '2025-10-21', '2025-11-20'],
  12 => ['Des', '2025-11-21', '2025-12-20'],
];

if (!$get_periode) {


  $opt = '';
  foreach ($rperiode as $key => $rvalue) {
    $opt .= "
      <option value='$key' " . ($key == $bulan_ini ? 'selected' : '') . ">
        Periode $rvalue[0]-2025 -- $rvalue[1] -- $rvalue[2]
      </option>
    ";
  }

  $select_periode = "<select class='form-control' id='periode' name='periode'>$opt</select>";

  echo "
    <form method=post class='mx-auto' style=max-width:400px>
      $select_periode
      <button class='btn btn-primary w-100 mt2'>Tampil SKS Mengajar</button>
    </form>
  ";
} else {
  $d = $rperiode[$get_periode];
  $awal =  $d[1];
  $akhir = $d[2];
  # ============================================================
  # SELECT MY JADWAL KELAS FROM ALL MY ROOMS
  # ============================================================
  $s = "SELECT a.*,
  b.nama as materi,
  c.nama as room
    
  FROM tb_sesi_kelas a 
  JOIN tb_sesi b ON a.id_sesi=b.id 
  JOIN tb_room c ON b.id_room=c.id 
  JOIN tb_kelas d ON a.kelas=d.kelas
  WHERE a.jadwal_kelas BETWEEN '$awal' AND '$akhir' 
  -- AND b.id_room = $id_room  -- dari semua mapel 
  AND (d.jenjang = 'S1' OR d.jenjang = 'D3') -- tidak jenjang SD 
  AND c.created_by = $id_peserta 
  ORDER BY d.shift DESC, c.nama, a.kelas, a.jadwal_kelas ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $tr = [];
  $i = 0;
  $last_kelas = null;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    if ($last_kelas != $d['kelas']) $i = 1;
    $last_kelas = $d['kelas'];

    $hari = $arr_hari[date('w', strtotime($d['jadwal_kelas']))];
    $tgl =  date('d-M-Y', strtotime($d['jadwal_kelas']));
    $jam = date('H:i', strtotime($d['jadwal_kelas']));
    $materi = $d['materi'];
    $row = "
      <tr id=tr__$d[id]>
        <td>$i</td>
        <td>$hari</td>
        <td>$tgl</td>
        <td>$jam</td>
        <td>$materi</td>
      </tr>
    ";

    $room_kelas = "$d[room] / $d[kelas]";
    if (!isset($tr[$room_kelas])) {
      $tr[$room_kelas] = [$row];
    } else {
      array_push($tr[$room_kelas], $row);
    }
  }

  foreach ($tr as $room_kelas => $arr) {
    $rows = '';
    foreach ($arr as $row) {
      $rows .= $row;
    }
    echo "
      <h4>$room_kelas</h4>
      <table class='table table-bordered'>
        <thead>
          <th width=5%>No</th>
          <th width=10%>Hari</th>
          <th>Tgl</th>
          <th>Jam</th>
          <th width=50%>Materi</th>
        </thead>
        $rows
      </table>
    ";
  }
}
