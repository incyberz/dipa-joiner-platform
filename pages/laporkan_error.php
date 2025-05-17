<?php
$img_warning = img_icon('warning');
$img_wa = img_icon('wa');

$get_topic = $_GET['topic'] ?? '';
$get_keterangan_error = $_GET['keterangan_error'] ?? '';

$ingin = "ingin *MELAPORKAN ADA ERROR*";
$href_wa = href_wa(
  $trainer['no_wa'],
  $ingin,
  'LAPORAN ERROR',
  false,
  false,
  $trainer['nama'],
  $trainer['gender'],
  $user['nama']
);

$topics = [
  'deskripsi' => 'Deskripsi Pertemuan',
  'tags' => 'Tags Materi',
  'link_ba' => 'Link Bahan Ajar',
  'link_ppt' => 'Link File Presntasi',
  'link_fl' => 'Link File Lainnya',
  'play_kuis' => 'Aktivitas - Play Kuis',
  'tanam_soal' => 'Aktivitas - Tanam Soal',
  'bertanya' => 'Aktivitas - Bertanya',
  'latihan' => 'Aktivitas - Tugas Latihan',
  'challenge' => 'Aktivitas - Tugas Challenge',
  'jadwal' => 'Jadwal Kelas',
  'presensi' => 'Presensi Saya',
];
if ($get_topic and !key_exists($get_topic, $topics)) die(div_alert('danger', "Get topic [$get_topic] is out of topics. | <a href='?laporkan_error'>Laporkan Topic lain</a>"));

$opts = '';
foreach ($topics as $topic => $value) {
  $selected = $topic == $get_topic ? 'selected' : '';
  if ($topic) $opts .= "<option value='$topic' $selected>$value</option>";
}

if ($param == 'laporkan_error') {
  $hideit = '';
} else {
  $hideit = 'hideit';
}

$laporkan_error = "
  <div data-aos=fade data-aos-delay=300>
    <span class='pointer f12 abu btn_aksi' id=laporkan_error__toggle>Laporkan Error $img_warning</span>
    <div class='$hideit wadah gradasi-kuning mt2' id=laporkan_error>
      <div class='f12 abu mb1'>Error tentang:</div>
      <select class='form-control' id=select_error>
        <option value=0>--Pilih--</option>
        $opts
      </select>
      <div id=blok_keterangan_error class=$hideit>
        <div class='f12 abu mb1 mt3'>Keterangan Error:</div>
        <textarea class='form-control' id=keterangan_error rows=6>$get_keterangan_error</textarea>
        <div class='mt1 mb2 f12 darkred' id=keterangan_error_info>wajib diisi minimal 50 karakter</div>
      </div>
      <div id=div_btn_laporkan  class=hideit>
        <span id=href_wa class=hideit>$href_wa</span>
        <a target=_blank id=link_laporkan_error href='$href_wa' class='btn btn-sm btn-warning w-100 mt2'>$img_wa Kirim</a>
        <div class='mt1 mb2 f12 abu tengah' id=link_laporkan_error_info>akan diteruskan ke <b>LMS Operator:</b> $ops[nama]</div>

      </div>
    </div>
  </div>
";

if ($param == 'laporkan_error') echo $laporkan_error;
?>
<script>
  $(function() {
    $('#select_error').change(function() {
      var select_error = $(this).val();
      if (select_error == '0') {
        $('#blok_keterangan_error').slideUp();
        return;
      } else {
        $('#blok_keterangan_error').slideDown();
      }
      $('#keterangan_error').keyup();
    });

    // keterangan_error keyup
    $('#keterangan_error').keyup(function() {
      let keterangan_error = $(this).val().trim();
      let length = keterangan_error.length;
      if (length >= 50) {
        $('#link_laporkan_error').prop('href',
          $('#href_wa').text() +
          `%0a%0aError pada: [ *${$('#select_error').val()}* ] %0a*Keterangan:* ` + keterangan_error
        );
        $('#div_btn_laporkan').slideDown();
        $('#keterangan_error_info').slideUp();
      } else {
        $('#keterangan_error_info').slideDown();
        $('#div_btn_laporkan').slideUp();
        if (length) $('#keterangan_error_info').html(`Anda mengetik <span class='f30 darkblue'>${length}</span> dari 50 karakter.`);
      }
    });

  })
</script>