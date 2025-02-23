<?php
$tr = '';
$i = 0;
foreach ($row as $id_pes => $v) {
  $crank_kelas = $v['rank_kelas'] ?? '-'; // exception for all KBM null
  $crank_room = $v['rank_room'] ?? '-';
  $nama = strtoupper($rpeserta[$id_pes]['nama']);
  $kelas = $rpeserta[$id_pes]['kelas'];
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
