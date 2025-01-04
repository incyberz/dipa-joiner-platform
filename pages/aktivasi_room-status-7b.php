<link rel="stylesheet" href="assets/css/radio-toolbar.css">
<?php
function input_radio($name, $value = '', $caption = '', $id = '', $checked = '', $classess = '')

{
  $id = $id ? $id : $name;
  $caption = $caption ? $caption : $name;
  $value = $value ? $value : $name;
  return "
    <div class='radio-toolbar abu mb2 mt2'>
      <input type='radio' name='$name' id='$id' class='opsi_radio $classess' required value='$value' $checked >
      <label class='proper' for='$id'>$caption</label>
    </div>
  ";
}

# ============================================================
# ROOM KELAS
# ============================================================
$s = $select_all_from_tb_room_kelas;
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$div = '';
$arr = [];
for ($i = 0; $i < 6; $i++) {
  $date = date('Y-m-d H:i', strtotime("+$i day", strtotime($room['awal_sesi'])));
  array_push($arr, $date);
}

while ($d = mysqli_fetch_assoc($q)) {
  $blok_radios = '';
  $no_hari = 0;
  foreach ($arr as $tanggal) {
    $no_hari++;
    $hari = $nama_hari[date('w', strtotime($tanggal))];
    $tgl = date('Y-m-d', strtotime($tanggal));
    $jam = date('H:i', strtotime($tanggal));
    $kelas = $d['kelas'];
    $radio_tanggal = input_radio(
      "radio__$d[kelas]",
      $tanggal,
      $hari,
      "opsi_radio__$d[kelas]__$no_hari",
    );

    # ============================================================
    # BLOK RADIOS PER KELAS
    # ============================================================
    $blok_radios .= "
    <div class='col-lg-2'>
      <div class='wadah blok_radio__$d[kelas]' id=blok_radio__$d[kelas]__$no_hari>
        <div>$radio_tanggal</div>
        <div>
          <input 
            class='form-control mb2 tengah input__$d[kelas]' 
            type=date 
            id=tgl_sesi__$d[kelas]__$no_hari 
            value='$tgl' 
            disabled
          >
        </div>
        <div>
          <input 
            class='form-control tengah jam_sesi jam_sesi__$d[kelas]' 
            type=time 
            id=jam_sesi__$d[kelas]__$no_hari 
            value='$jam' 
            disabled
          >
        </div>
      </div>
    </div>
    ";
  }

  $div .= "
    <div class='wadah bg-white' style='margin-bottom:60px'>
      <div class='abu tebal f14 tengah mt2 miring'>
        Awal Pembelajaran (Sesi Pertama)
      </div>
      <div class='darkblue f26 tengah mt2 mb4'>
        $d[kelas]
      </div>
      <div class=row>
        $blok_radios
      </div>
      <input 
        type=hidden
        name=jadwal_kelas[$kelas] 
        id=tanggal_sesi__$kelas 
        value='$tanggal' 
      >

      
    </div>
  ";
}

$inputs = "
  $div
  <div class=wadah>
    <a href='?aktivasi_room&aksi=reset_room_kelas' class='btn btn-danger btn-sm mb2' onclick='return confirm(`Ingin memilih ulang anggota kelas untuk $Room ini?\n\nPerhatian! Setingan kelas-kelas diatas akan hilang.`)'>Reset $Room Kelas</a>
    <div class='mb4 abu f14 miring'>Reset $Room Kelas digunakan untuk memilih ulang anggota kelas pada $Room ini</div>
  </div>
";
?>
<script>
  $(function() {

    function update_sesi_kelas(kelas, no_hari) {
      $('#tanggal_sesi__' + kelas).val(
        $('#tgl_sesi__' + kelas + '__' + no_hari).val() +
        ' ' +
        $('#jam_sesi__' + kelas + '__' + no_hari).val()
      );
    }

    $('.jam_sesi').keyup(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let kelas = rid[1];
      let no_hari = rid[2];
      let val = $(this).val();
      console.log(aksi, kelas, no_hari, val);
      update_sesi_kelas(kelas, no_hari);

    });
    $('.opsi_radio').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let kelas = rid[1];
      let no_hari = rid[2];
      let kelas_no_hari = kelas + '__' + no_hari;
      console.log(aksi, kelas, no_hari);

      $('.blok_radio__' + kelas).removeClass('gradasi-hijau');
      $('#blok_radio__' + kelas_no_hari).addClass('gradasi-hijau');

      $('.jam_sesi__' + kelas).prop('disabled', 1);
      $('#jam_sesi__' + kelas_no_hari).prop('disabled', 0);

      update_sesi_kelas(kelas, no_hari);
    })
  })
</script>