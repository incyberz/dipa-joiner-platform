<?php
# ============================================================
# ROOM KELAS
# ============================================================

// get list from tb_kelas
$s = "SELECT 
a.kelas, 
a.fakultas, 
a.prodi,
(
  SELECT COUNT(1) FROM tb_kelas_peserta WHERE kelas=a.kelas) jumlah_peserta,
(
  SELECT 1 FROM tb_room_kelas 
  WHERE id_room=$id_room 
  AND kelas=a.kelas) my_room_kelas

FROM tb_kelas a 
WHERE a.status=1 
AND a.tahun_ajar = $ta  
AND kelas != 'INSTRUKTUR'
ORDER BY a.fakultas,a.prodi,a.kelas";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
while ($d = mysqli_fetch_assoc($q)) {
  $my_room_kelas = $d['my_room_kelas'] ? 'biru bg-yellow bold' : '';
  $checked = $d['my_room_kelas'] ? 'checked' : '';

  # ============================================================
  # FINAL TR
  # ============================================================
  $tr .= "
    <tr class='tr $my_room_kelas' id=tr__$d[kelas]>
      <td>$d[fakultas]</td>
      <td>$d[prodi]</td>
      <td class='bold'>
        <label class='label pointer' id='label__$d[kelas]'>
          <input 
            class='cek_kelas' 
            id='cek_kelas__$d[kelas]' 
            type=checkbox 
            name=room_kelas[] 
            value='$d[kelas]' 
            $checked
          > 
          $d[kelas]
        </label>
      </td>
      <td>$d[jumlah_peserta]</td>
    </tr>
  ";
}
















# ============================================================
# ARRAY DATA FOR INPUTS ADD KELAS
# ============================================================
$arr = [
  'fakultas' => [
    'caption' => 'Fakultas / Lembaga',
    'minlength' => 4,
    'maxlength' => 30,
    'placeholder' => 'Fakultas/Lembaga...',
    'info' => 'Contoh: FTEK MU, FTI UNISBA, FISIP ITS, FIKOM UNPAD, dll',
  ],
  'nama_prodi' => [
    'caption' => 'Nama Lengkap Prodi / Jurusan',
    'minlength' => 4,
    'maxlength' => 30,
    'placeholder' => 'Nama Lengkap Prodi...',
    'info' => 'Contoh: Teknik Informatika, Komputerisasi Akuntansi, dll',
  ],
  'jenjang' => [
    'caption' => 'Jenjang',
    'minlength' => 2,
    'maxlength' => 2,
    'placeholder' => 'Jenjang...',
    'info' => 'Isi dengan pilihan jenjang: SD, SP, SA, MI, MT, MA, D3, D4, S1, S2, atau S3',
  ],
  'prodi' => [
    'caption' => 'Singkatan Prodi',
    'minlength' => 2,
    'maxlength' => 5,
    'placeholder' => 'Prodi...',
    'info' => 'Contoh: TI, RPL, MI, KA',
  ],
  'sub_kelas' => [
    'caption' => 'Sub Grup Kelas',
    'minlength' => 1,
    'maxlength' => 3,
    'placeholder' => 'Sub Kelas...',
    'info' => 'Contoh: A, B, C, IV, XII, dll, masukan strip (-) jika tidak ada.',
  ],
  'shift' => [
    'caption' => 'Shift Kelas',
    'minlength' => 1,
    'maxlength' => 2,
    'placeholder' => 'Shift Kelas...',
    'info' => 'Contoh: P = Kelas Pagi, S = Kelas Sore, R = Reguler, NR = Non-Reguler, strip jika tidak ada.',
  ],
  'semester' => [
    'caption' => 'Semester',
    'minlength' => 1,
    'maxlength' => 1,
    'placeholder' => 'Semester...',
    'info' => 'Contoh: 1, 2, 3, 4, dll',
  ],
  'caption' => [
    'caption' => 'Nama Grup Kelas',
    'minlength' => 5,
    'maxlength' => 30,
    'placeholder' => 'Nama Grup Kelas...',
    'info' => 'Contoh: TI-Reg-SM2, artinya Kelas TI Reguler Semester 2. <br>Buatlah nama grup kelas yang unik dan jelas karena mungkin dapat sama dengan yang dibuat instruktur lainnya!',
  ],
];

function create_tr_input($key, $value)
{
  $input_kelas = $key == 'caption' ? '' : 'input_kelas';
  return "
    <tr>
      <td class='kanan'>
        <div class='pt2 miring'>$value[caption]</div>
      </td>
      <td>
        <input 
          required
          minlength=$value[minlength] 
          maxlength=$value[maxlength] 
          class='form-control upper $input_kelas input_add_kelas' 
          name='kelas[$key]' 
          id=$key
          placeholder='$value[placeholder]' 
          value='' 
          disabled
        />
        <div class='f12 abu mt1 mb2'>$value[info]</div>
      </td>
    </tr>
  ";
}

$tr_add_input = '';
foreach ($arr as $key => $value) {
  $tr_add_input .= create_tr_input($key, $value);
}

$tr_tambah = "
    <tr>
      <td colspan=100%>
        <div class='btn_aksi tebal green pointer mt4 mb4' id=form_add_kelas__toggle>$img_add Add Kelas</div>
        <div class='abu mt2 mb2' id=form_add_kelas__note>Kosongkan ceklis pada tabel diatas jika ingin menambah kelas</div>
        <div class='wadah gradasi-toska mb4 hideit' id=form_add_kelas>
          <table class='table'>
            $tr_add_input
            <tr>
              <td class='kanan'>
                <div class='pt2 miring consolas darkblue'>Kode Grup Kelas Baru</div>
                <input type=hidden id=kelas name=kelas[kelas] class=input_add_kelas disabled>
              </td>
              <td>
                <div class='f30 biru tebal consolas ' id=kode_kelas>...</div>
              </td>
            </tr>            
          </table>
          <div class='mt2 mb2'>
            <button class='btn btn-primary w-100' name=btn_add_kelas>Add Kelas</button>
          </div>
          <div class='mt2 mb2'>
            <span class='btn btn-secondary w-100' id=btn_cancel_add_kelas>Cancel</span>
          </div>
        </div>

      </td>
    </tr>
  ";

$inputs = "
  <h3 class=tr>Select Room Kelas</h3>
  <p class=tr>Silahkan Anda pilih Grup <b class=darkblue>Kelas aktif TA. $ta</b> yang boleh mengakses Room Anda (max: 5 kelas).</p>
  <table class='table' id=tb_kelas>
    <thead class='tr'>
      <th>Fakultas / Lembaga</th>
      <th>Prodi / Jurusan</th>
      <th>Grup Kelas</th>
      <th>Jumlah Peserta</th>
    </thead>

    $tr
    $tr_tambah
  </table>
";
?>
<script>
  $(function() {
    let modeAdd = 0;
    let tahun_ajar = $('#tahun_ajar').text();
    let kode_kelas = '';

    // auto disabled on formload
    $('#btn_aktivasi').prop('disabled', 1);

    $('.input_kelas').keyup(function() {
      kode_kelas = $('#jenjang').val() + '-' +
        $('#prodi').val() + '-' +
        $('#sub_kelas').val() + '-' +
        $('#shift').val() + '-SM' +
        $('#semester').val() + '-' + tahun_ajar;

      kode_kelas = kode_kelas.toUpperCase().replace(/-+/gim, '-');

      $('#kode_kelas').text(kode_kelas);
      $('#kelas').val(kode_kelas);
      $('#caption').val(kode_kelas);
    });

    $('#form_add_kelas__toggle').click(function() {
      modeAdd = 1 - modeAdd;
      if (modeAdd) {
        $('.input_add_kelas').prop('disabled', 0);
        // $('.cek_kelas').prop('disabled', !modeAdd);

      } else {
        $('.input_add_kelas').prop('disabled', 1);

      }
      console.log('modeAdd', modeAdd);
      $('#btn_aktivasi').fadeToggle();
      $('#form_add_kelas__note').toggle();
      $('.tr').toggle();
    });
    $('.cek_kelas').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let kelas = rid[1];

      let checked = $(this).prop('checked');
      if (checked) {
        $('#tr__' + kelas).addClass('biru bold bg-yellow');
      } else {
        $('#tr__' + kelas).removeClass('biru bold bg-yellow');
      }
      if ($('.cek_kelas:checked').length) {
        // zzz here
        console.log($('.cek_kelas:checked').length);
        $('#btn_aktivasi').prop('disabled', 0);
        $('#form_add_kelas__toggle').hide();
        console.log('disabled');
      } else {
        console.log('enabled');
        $('#btn_aktivasi').prop('disabled', 1);
        $('#form_add_kelas__toggle').show();
      }
    });
    $('#btn_cancel_add_kelas').click(function() {
      $('#form_add_kelas__toggle').click();
      // modeAdd = 0;
      // $('input').prop('disabled', !modeAdd);
      // $('#btn_aktivasi').fadeToggle();
    });
  })
</script>