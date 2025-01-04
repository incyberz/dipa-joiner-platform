<?php
$id = $d['id_' . $jenis];
$s = "SELECT * FROM tb_$jenis WHERE id=$id";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);

$rdesc = [
  'nama' => "Nama $Jenis yang tampil ke $Peserta",
  'ket' => "Berisi prosedur, perintah, dan petunjuk bagaimana cara mengerjakan $jenis. Jelaskan spesifikasi $jenis secara terstruktur, singkat, dan jelas.",
  'link_panduan' => "Masukan Link Youtube atau File di Google Drive sebagai Panduan tambahan untuk $jenis ini",
  'basic_point' => "Sebelum $jenis di closing oleh $Trainer, $Peserta akan tetap mendapat Basic Point secara penuh",
  'ontime_point' => "Ontime Point adalah poin tambahan bagi $Peserta yang mengerjakan $jenis secara Ontime",
  'ontime_dalam' => "Ontime Point akan terus berkurang jika melebihi batas Ontime Point ini",
  'ontime_deadline' => "Ontime Point akan habis saat melebihi Ontime Deadline",
  'cara_pengumpulan' =>  "Cara $Peserta mengumpulkan $jenis dari $Trainer",
  'link_includes' =>  "Pada link $jenis harus terdapat kata-kata berikut (pisahkan dengan koma)",
  'link_excludes' =>  "Tidak boleh ada kata-kata berikut pada $jenis (pisahkan dengan koma)",
  'kuota' =>  "Stop Submit jika melebihi kuota $jenis",
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
  $rows = 3 + intval(strlen($value) / 80);

  if (strlen($value) > 30 || $key == 'ket' || $key == 'cara_pengumpulan') {
    if (!$value and $key == 'cara_pengumpulan') $value = $cara_pengumpulan_default;
    $input = "<textarea required minlength=20 class='form-control' name=$key rows=$rows>$value</textarea>";
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
          <input type=hidden class='form-control' name=$key id=$key value='$value' />
        </div>
      </div>
    ";
  } else {
    if (
      !$value and (
        $key == 'link_panduan'
        || $key == 'link_includes'
        || $key == 'link_excludes'
        || $key == 'kuota'
      )
    ) $value = '-';
    $min = $jenis == 'latihan' ? 1000 : 10000;
    $max = $jenis == 'latihan' ? 1000000 : 10000000;
    $type_number = ($key == 'basic_point' || $key == 'ontime_point') ? "type=number min=$min max=$max" : '';
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
<h5 class='darkblue proper' id=manage_$jenis>Manage $jenis <span class=btn_aksi id=form_properties__toggle>$img_detail</span></h5>
<p>Manage prosedur latihan, timing, dan cara pengumpulannya</p>
<form method=post id=form_properties class=hideita>
  <table class=table>
    $tr
  </table>
  <button class='btn btn-primary w-100' name=btn_update_jenis value=$id $disabled_submit>Update $jenis</button>
  $disabled_submit_info
</form>
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