<?php
# ============================================================
# JADWAL KELAS
# ============================================================
$img_cancel = img_icon('cancel');

$s = "SELECT kelas FROM tb_room_kelas WHERE id_room=$id_room AND kelas!='INSTRUKTUR' AND ta=$ta";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$th_kelas = '';
$arr_kelas = [];
$arr_sesi_kelas = [];
$nav_kelas = '';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  array_push($arr_kelas, $d['kelas']);
  if ($i == 1) {
    echo "<span class='hideit bg-red' id=kelas_selected>$d[kelas]</span>";
    $primary = 'primary';
    $hideit = '';
  } else {
    $primary = 'secondary';
    $hideit = 'hideit';
  }
  $nav_kelas .= "<div class='btn btn-$primary btn-sm btn_nav_kelas' id=btn_nav_kelas__$d[kelas]>$d[kelas]</div> ";
  $th_kelas .= "<th  class='td_kelas td__$d[kelas] $hideit'>$d[kelas]</th>";
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
if (isset($_POST['btn_check'])) {
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';

  // zzz here
}



























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
  foreach ($arr_kelas as $key => $kelas) {
    $jadwal = date('Y-m-d H:i', strtotime($arr_sesi_kelas[$kelas][$d['id']]));
    $hari = hari_tanggal($jadwal, 0);
    $triple_id = $kelas . "__$d[id]__$d[jenis]";
    $hideit = $key == 0 ? '' : 'hideit';
    $td_jadwal_kelas .= $d['jenis'] == 0 ? "<td width=30% class='td_kelas td__$kelas $hideit'><div class='miring pt2 pb2 f12 abu'>sesi tenang</div></td>" : "
      <td width=30% class='td_kelas td__$kelas $hideit'>
        <div class='flexy'>
          <div id=div_span__$triple_id class='div_span'>
            <span id=span__$triple_id>$hari</span> 
          </div>
          <div id=div_input__$triple_id class='div_input hideit'>
            <input class='form-control jadwal_kelas' value='$jadwal'  id=jadwal_kelas__$triple_id />
            <div class='mt1 f10 miring abu'>masukan waktu pada rentang Durasi Presensi</div>
          </div>
          <div id=div_img_check__$triple_id class='div_img_check hideit'>
            <button class='btn_check btn-transparan' id=btn_check__$triple_id name=btn_check value='$triple_id'>$img_check</button>
            <span class='btn_cancel' id=btn_cancel__$triple_id name=btn_cancel__$triple_id>$img_cancel</span>
          </div>
          <div class=div_img_edit id=div_img_edit__$triple_id>
            <span class=img_edit id=img_edit__$triple_id>$img_edit</span>
          </div>
        </div>
      </td>
    ";
  }

  $p_ke_show = $d['jenis'] == 1 ? "P$p_ke" : '';

  $tr .= "
    <tr class='$gradasi'>
      <td width=30px>$i</td>
      <td>
        <div>$p_ke_show $d[nama]</div>
      </td>
      <td class=f10>
        <div><b>Awal</b>: " . hari_tanggal($d['awal_presensi'], 0) . "</div>
        <div><b>Akhir</b>: " . hari_tanggal($d['akhir_presensi'], 0) . "</div>
        <div id=awal_presensi__$d[id] class='hideit bg-red'>$d[awal_presensi]</div>
        <div id=akhir_presensi__$d[id] class='hideit bg-red'>$d[akhir_presensi]</div>
      </td>
      $td_jadwal_kelas
    </tr>
  ";
}

$total_sesi++;

echo "
  <form method=post>
    <div class='alert alert-info'>
      <input type=checkbox checked name=check_auto_jeda_hari> 
      Otomatis Tambahkan <span id=jeda_sesi>$room[jeda_sesi]</span> hari untuk sesi selanjutnya yang sejenis
      <div class=mt2>$nav_kelas</div>
    </div>
    <table class='table th-toska td-trans'>
      <thead>
        <th>No</th>
        <th>Sesi</th>
        <th>Durasi Presensi</th>
        $th_kelas
      </thead>
      $tr
    </table>
    <input type=hiddena name=new_jadwal id=new_jadwal>
  </form>
";























?>
<script>
  $(function() {
    $('.img_edit').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let kelas = rid[1];
      let id_sesi = rid[2];
      let jenis_sesi = rid[3];
      let triple_id = rid[1] + '__' + rid[2] + '__' + rid[3];
      $('#div_span__' + triple_id).toggle();
      $('#div_input__' + triple_id).toggle();

      $('.div_img_edit').hide();
      // $('#div_img_edit__' + triple_id).show();
      $('#div_img_check__' + triple_id).show();
    })

    $('.btn_cancel').click(function() {
      $('.div_span').show();
      $('.div_input').hide();
      $('.div_img_edit').show();
      $('.div_img_check').hide();
    })

    // $('.img_check').click(function() {
    //   let tid = $(this).prop('id');
    //   let rid = tid.split('__');
    //   let aksi = rid[0];
    //   let kelas = rid[1];
    //   let id_sesi = rid[2];
    //   let jenis_sesi = rid[3];
    //   let triple_id = rid[1] + '__' + rid[2] + '__' + rid[3];
    //   $('#div_span__' + triple_id).toggle();
    //   $('#div_input__' + triple_id).toggle();
    //   $('#div_img_edit__' + triple_id).toggle();
    //   $('#div_img_check__' + triple_id).toggle();
    // })

    let kelas_selected = $('#kelas_selected').text();
    $('.btn_nav_kelas').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      kelas_selected = rid[1];
      $('.btn_nav_kelas').removeClass('btn-primary');
      $('.btn_nav_kelas').addClass('btn-secondary');
      $(this).removeClass('btn-secondary');
      $(this).addClass('btn-primary');
      $('#kelas_selected').text(kelas_selected);
      $('.td_kelas').hide();
      $('.td__' + kelas_selected).show();
    })

    $('.jadwal_kelas').keyup(function() {
      $('#new_jadwal').val($(this).val());
    })
  })
</script>