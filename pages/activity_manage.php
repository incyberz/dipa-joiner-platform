<?php
# ============================================================
# MANAGE ACTIVITY UI
# ============================================================
$img_detail = img_icon('detail');

$s = "SELECT * FROM tb_assign_$jenis WHERE id=$id_assign";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q)) {
  $d = mysqli_fetch_assoc($q);

  # ============================================================
  # GET ALL ID_ASSIGN 
  # ============================================================
  $s2 = "SELECT a.*, 
  a.id as id_assign,
  b.kelas 
  FROM tb_assign_$jenis a 
  JOIN tb_room_kelas b ON a.id_room_kelas=b.id
  WHERE a.id_sesi=$d[id_sesi] 
  AND a.id_latihan=$d[id_latihan]
  AND b.id_room=$id_room
  ";
  // die($s2);
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  $tr = '';
  while ($d2 = mysqli_fetch_assoc($q2)) {
    $id_assign = $d2['id_assign'];
    $kelas = $d2['kelas'];

    $select_is_wajib = "
      <select class='form-control' name=is_wajib[$id_assign]>
        <option value=''>Tidak wajib</option>
        <option value='1'>Wajib</option>
      </select>
    ";

    $tr .= "
      <tr>
        <td>$d2[kelas]</td>
        <td>
          <input required name=tanggal_assign[$id_assign] value='$d2[tanggal]' class='form-control'>
        </td>
        <td>$select_is_wajib</td>
      </tr>
    ";
  }





  $manage_assign = "
  <h5 class='darkblue proper'>Manage Rule Assign $jenis <span class=btn_aksi id=form_assign__toggle>$img_detail</span></h5>
  <p>Manage aturan khusus latihan untuk setiap Grup Kelas pada room ini.</p>
  <form method=post id=form_assign class=hideita>
    <table class=table>
      <thead class=upper>
        <th>KELAS</th>
        <th>TANGGAL MULAI $jenis</th>
        <th>IS WAJIB</th>
      </thead>
      $tr
    </table>
    <button class='btn btn-primary w-100 proper' name=btn_update_assign value=$id_assign>Update Rule Assign $jenis</button>
  </form>
  ";




















  # ====================================
  # MANAGE LATIHAN/CHALLENGE
  # ====================================
  $id = $d['id_' . $jenis];
  $s = "SELECT * FROM tb_$jenis WHERE id=$id";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $d = mysqli_fetch_assoc($q);

  $rdesc = [
    'nama' => "Nama $Jenis yang tampil ke peserta",
    'ket' => "Berisi perintah dan petunjuk bagaimana cara mengerjakan $jenis",
    'link_panduan' => "Masukan Link Youtube atau File di Google Drive sebagai Panduan tambahan untuk $jenis",
    'basic_point' => "Sebelum $jenis di closing oleh instruktur, peserta akan tetap mendapat Basic Point secara penuh",
    'ontime_point' => "Ontime Point adalah poin tambahan bagi peserta yang mengerjakan $jenis secara Ontime",
    'ontime_dalam' => "Ontime Point akan terus berkurang jika melebihi batas Ontime Point ini",
    'ontime_deadline' => "Ontime Point akan habis saat melebihi Ontime Deadline",
    'cara_pengumpulan' =>  "Cara peserta mengumpulkan $jenis dari instruktur",
  ];
  $tr = '';

  foreach ($d as $key => $value) {
    if (
      $key == 'id'
      || $key == 'id_room'
      || $key == 'date_created'
      || $key == 'status'
    ) continue;
    $kolom = key2kolom($key);
    $desc = $rdesc[$key] ?? '';
    $rows = 2 + intval(strlen($value) / 30);

    if (strlen($value) > 30 || $key == 'ket' || $key == 'cara_pengumpulan') {
      if (!$value and $key == 'cara_pengumpulan') $value = $cara_pengumpulan_default;
      $input = "<textarea required minlength=50 class='form-control' name=$key rows=$rows>$value</textarea>";
    } elseif ($key == 'ontime_deadline' || $key == 'ontime_dalam') {

      $opt_hari = '';
      $opt_jam = '';
      $opt_menit = '';
      for ($i = 0; $i <= 30; $i++) $opt_hari .= "<option value=$i>$i hari</option>";
      for ($i = 0; $i <= 23; $i++) $opt_jam .= "<option value=$i>$i jam</option>";
      for ($i = 0; $i <= 59; $i++) $opt_menit .= "<option value=$i>$i menit</option>";
      $select_hari = "
      <select 
        class='form-control select_durasi select__$key' 
        id=select_hari__$key
      >
        $opt_hari
      </select>
      ";
      $select_jam = "
      <select 
        class='form-control select_durasi select__$key' 
        id=select_jam__$key
      >
        $opt_jam
      </select>
      ";
      $select_menit = "
      <select 
        class='form-control select_durasi select__$key' 
        id=select_menit__$key
      >
        $opt_menit
      </select>
      ";

      $input = "
        <div class=flexy>
          <div>$select_hari</div>
          <div>$select_jam</div>
          <div>$select_menit</div>
          <div>
            <input required class='form-control hideita' name=$key id=$key value='$value' />
          </div>
        </div>
      ";
    } else {
      if (!$value and $key == 'link_panduan') $value = '-';
      $type_number = ($key == 'basic_point' || $key == 'ontime_point') ? 'type=number min=1000 max=1000000' : '';
      $input = "<input required $type_number class='form-control' name=$key value='$value' />";
    }

    $tr .= "
      <tr>
        <td class=kanan width=20%>
          $kolom
          <div class='f12 abu miring'>$desc</div>
        </td>
        <td>
          $input
        </td>
      </tr>
    ";
  }
  $form_manage_jenis = "
  <h5 class='darkblue proper'>Manage $jenis <span class=btn_aksi id=form_properties__toggle>$img_detail</span></h5>
  <p>Manage prosedur latihan, timing, dan cara pengumpulannya</p>
  <form method=post id=form_properties class=hideita>
    <table class=table>
      $tr
    </table>
    <button class='btn btn-primary w-100' name=btn_update_jenis value=$id>Update $jenis</button>
  </form>
  ";
} else {
  $form_manage_jenis = div_alert('danger', "Data Assign $jenis tidak ditemukan.");
}

echo "
  <div class='wadah gradasi-kuning'>
    $form_manage_jenis
  </div>
  <div class='wadah gradasi-kuning'>
    $manage_assign
  </div>
";




































?>
<script>
  $(function() {
    // default durasi Ontime Dalam
    let ontime_dalam = parseInt($('#ontime_dalam').val());
    let ontime_deadline = parseInt($('#ontime_deadline').val());

    if (ontime_dalam) {
      let menit = ontime_dalam % 60;
      let jam = parseInt(ontime_dalam / 60) % 24;
      let hari = parseInt(ontime_dalam / (60 * 24));
      $('#select_menit__ontime_dalam').val(menit);
      $('#select_jam__ontime_dalam').val(jam);
      $('#select_hari__ontime_dalam').val(hari);
      if (menit) $('#select_menit__ontime_dalam').addClass('gradasi-hijau');
      if (jam) $('#select_jam__ontime_dalam').addClass('gradasi-hijau');
      if (hari) $('#select_hari__ontime_dalam').addClass('gradasi-hijau');
    } else {
      $('#select_jam__ontime_dalam').val(4);
      $('#select_jam__ontime_dalam').addClass('gradasi-hijau');
    }


    if (ontime_deadline) {
      let menit = ontime_deadline % 60;
      let jam = parseInt(ontime_deadline / 60) % 24;
      let hari = parseInt(ontime_deadline / (60 * 24));
      $('#select_menit__ontime_deadline').val(menit);
      $('#select_jam__ontime_deadline').val(jam);
      $('#select_hari__ontime_deadline').val(hari);
      if (menit) $('#select_menit__ontime_deadline').addClass('gradasi-hijau');
      if (jam) $('#select_jam__ontime_deadline').addClass('gradasi-hijau');
      if (hari) $('#select_hari__ontime_deadline').addClass('gradasi-hijau');
    } else {
      $('#select_jam__ontime_deadline').val(4);
      $('#select_jam__ontime_deadline').addClass('gradasi-hijau');

    }

    $('.select_durasi ').change(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let field = rid[1];
      console.log(aksi, field);

      let hari = parseInt($('#select_hari__' + field).val());
      let jam = parseInt($('#select_jam__' + field).val());
      let menit = parseInt($('#select_menit__' + field).val());
      $('#' + field).val(hari * 24 * 60 + jam * 60 + menit);

      if (hari) {
        $('#select_hari__' + field).addClass('gradasi-hijau');
      } else {
        $('#select_hari__' + field).removeClass('gradasi-hijau');
      }
      if (jam) {
        $('#select_jam__' + field).addClass('gradasi-hijau');
      } else {
        $('#select_jam__' + field).removeClass('gradasi-hijau');
      }
      if (menit) {
        $('#select_menit__' + field).addClass('gradasi-hijau');
      } else {
        $('#select_menit__' + field).removeClass('gradasi-hijau');
      }
    })

  })
</script>