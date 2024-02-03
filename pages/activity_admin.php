<div class="wadah">
  <h5 class=darkblue>Activity For Admin</h5>
  <?php
  $s = "SELECT * FROM tb_kelas WHERE tahun_ajar=$tahun_ajar AND status=1";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $arr_kelas = [];
  while($d=mysqli_fetch_assoc($q)){
    array_push($arr_kelas,$d['kelas']);
  }

  $s = "SELECT * FROM tb_$jenis WHERE id_room=$id_room";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $tr='';
  $i=0;
  while($d=mysqli_fetch_assoc($q)){
    $i++;

    $td_kelas = '';
    foreach ($arr_kelas as $kelas) {
      $td_kelas.= "<div>$kelas</div>";
    }

    $td_kelas = "<td>$td_kelas</td>";


    $tr.= "
      <tr>
        <td>$i</td>
        <td>$d[nama]</td>
        $td_kelas
      </tr>
    ";
  }

  $jumlah_kelas = count($arr_kelas);

  echo "
    <table class=table>
      <thead>
        <th>No</th>
        <th class=proper>$jenis</th>
        <th>Assigned to</th>
        <th>Drop</th>
      </thead>
      $tr
    </table>
  ";



  ?>
</div>