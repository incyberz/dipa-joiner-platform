<?php
$s2 = "SELECT a.*, 
a.id as id_assign,
b.kelas,
(
  SELECT COUNT(1) FROM tb_bukti_$jenis p 
  JOIN tb_assign_$jenis q ON p.id_assign_$jenis=q.id   
  JOIN tb_peserta r ON p.id_peserta=r.id 
  JOIN tb_kelas_peserta s ON r.id=s.id_peserta 
  JOIN tb_kelas t ON s.kelas=t.kelas 
  JOIN tb_room_kelas u ON t.kelas=u.kelas 
  WHERE q.id_$jenis=a.id_$jenis
  AND r.id_role=1 
  AND u.id_room=$id_room
  AND u.kelas=b.kelas) count_submiter_kelas,
(
  SELECT jadwal_kelas 
  FROM tb_sesi_kelas 
  WHERE kelas=b.kelas 
  AND id_sesi=$id_sesi) jadwal_kelas

FROM tb_assign_$jenis a 
JOIN tb_room_kelas b ON a.id_room_kelas=b.id
WHERE a.id_sesi=$d_assign[id_sesi] 
AND a.id_$jenis=$d_assign[id_jenis]
AND b.id_room=$id_room 
AND b.kelas != 'INSTRUKTUR'
";
// die($s2);
$q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
$tr = '';
while ($d2 = mysqli_fetch_assoc($q2)) {
  $id_assign = $d2['id_assign'];
  $kelas = $d2['kelas'];


  $checked = $d2['is_wajib'] ? 'checked' : '';
  $select_is_wajib = "
    <label>
      <input type=checkbox $checked name=is_wajib[$id_assign] value=1> Wajib
    </label>
  ";

  $tgl_assign = date('Y-m-d', strtotime($d2['tanggal']));
  $jam_assign = date('H:i', strtotime($d2['tanggal']));

  $tgl_sesi = '';
  $jam_sesi = '';
  $tgl_sesi_show = '';
  $jam_sesi_show = '';
  if ($d2['jadwal_kelas']) {
    $tgl_sesi = date('d-M-y', strtotime($d2['jadwal_kelas']));
    $jam_sesi = date('H:i', strtotime($d2['jadwal_kelas']));
    $id_tgl = "tgl_sesi__$id_assign" . '__' . date('Y-m-d', strtotime($d2['jadwal_kelas']));
    $id_jam = "jam_sesi__$id_assign" . "__$jam_sesi";
    $tgl_sesi_show = "<span class='btn btn-success btn-sm btn_waktu' id=$id_tgl>$tgl_sesi</span>";
    $jam_sesi_show = "<span class='btn btn-success btn-sm btn_waktu' id=$id_jam>$jam_sesi</span>";
    if ($d2['count_submiter_kelas']) {
      $tgl_sesi_show = "<span class='btn btn-secondary btn-sm' onclick='alert(`Tidak bisa mengubah tanggal latihan karena sudah ada yang mengumpulkan (submiter).`)'>$tgl_sesi</span>";
      $jam_sesi_show = "<span class='btn btn-secondary btn-sm' onclick='alert(`Tidak bisa mengubah tanggal latihan karena sudah ada yang mengumpulkan (submiter).`)'>$jam_sesi</span>";
    }
  }

  $readonly = $d2['count_submiter_kelas'] ? 'readonly' : '';

  $tr .= "
    <tr>
      <td>$d2[kelas]</td>
      <td>$d2[count_submiter_kelas]</td>
      <td>
        $tgl_sesi_show
        $jam_sesi_show
      </td>
      <td>
        <div class=flexy>
          <div><input $readonly type=date id=tgl_assign__$id_assign value='$tgl_assign' class='form-control form-control-sm input_waktu'></div>
          <div><input $readonly type=time id=jam_assign__$id_assign value='$jam_assign' class='form-control form-control-sm input_waktu'></div>
          <div><input type=hidden name=tanggal_assign[$id_assign] id=tanggal_assign__$id_assign value='$d2[tanggal]' ></div>
        </div>
        
      </td>
      <td>$select_is_wajib</td>
    </tr>
  ";
}



$disabled_submit_assign_info = $count_submiter ? div_alert('info red mt2 tengah', "Jika sudah ada submiter maka Tanggal Mulai $Jenis tidak bisa lagi diubah") : '';

$manage_assign = "
  <h5 class='darkblue proper' id=manage_rule_$jenis>Manage Rule Assign $jenis <span class=btn_aksi id=form_assign__toggle>$img_detail</span></h5>
  <p>Manage aturan khusus latihan untuk setiap Grup Kelas pada $Room ini.</p>
  <div class='tengah p2 border-top border-bottom mb2 gradasi-toska tebal darkblue' >$Jenis Sesi-$no_sesi $nama_sesi</b></div>
  <form method=post id=form_assign class=hideita>
    <table class=table>
      <thead class=upper>
        <th>KELAS</th>
        <th>$Submiter</th>
        <th>JADWAL KELAS</th>
        <th>TANGGAL MULAI $jenis</th>
        <th>SIFAT $jenis</th>
      </thead>
      $tr
    </table>
    <div class='blue f12 miring mb1 mt1'>)* klik tombol hijau jika ingin Tanggal Mulai $Jenis sama dengan Jadwal Kelas</div>
    <div class='blue f12 miring mb2 mt1'>)* Sifat $Jenis Wajib mempunyai bobot ganda pada kalkulasi <a href='?nilai_akhir'>Nilai Akhir</a></div>
    <button class='btn btn-primary w-100 proper' name=btn_update_assign value=$id_assign>Update Rule Assign $jenis</button>
    $disabled_submit_assign_info
  </form>
";
?>
<script>
  function set_tanggal_mulai(id_assign) {
    $('#tanggal_assign__' + id_assign).val(
      $('#tgl_assign__' + id_assign).val() + ' ' +
      $('#jam_assign__' + id_assign).val()
    );
  }
  $(function() {
    $('.input_waktu').change(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id_assign = rid[1];
      // console.log(aksi, id_assign);
      set_tanggal_mulai(id_assign);
    });

    $('.btn_waktu').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id_assign = rid[1];
      let val = rid[2];
      let rsub = aksi.split('_');
      let sub = rsub[0];
      // console.log(aksi, sub, id_assign, val);
      $('#' + sub + '_assign__' + id_assign).val(val);
      set_tanggal_mulai(id_assign);
    })
  })
</script>