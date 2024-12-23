<?php
$img_warning = img_icon('warning');
$img_wa = img_icon('wa');

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


$laporkan_error = "
  <div data-aos=fade data-aos-delay=300>
    <span class='pointer f12 abu btn_aksi' id=laporkan_error__toggle>Laporkan Error $img_warning</span>
    <div class='hideit wadah gradasi-kuning mt2' id=laporkan_error>
      <div class='f12 abu mb1'>Error tentang:</div>
      <select class='form-control' id=select_error>
        <option value=0>--Pilih--</option>
        <option>Deskripsi Sesi</option>
        <option>Tags Materi</option>
        <option>Link Bahan Ajar</option>
        <option>Link File PPT</option>
        <option>Link Video Ajar</option>
        <option>Link File Lainnya</option>
        <option>Aktivitas - Play Kuis</option>
        <option>Aktivitas - Tanam Soal</option>
        <option>Aktivitas - Bertanya</option>
        <option>Aktivitas - Tugas Latihan</option>
        <option>Aktivitas - Tugas Challenge</option>
        <option>Jadwal Kelas</option>
        <option>Presensi Saya</option>
      </select>
      <div class='f12 abu mb1 mt3'>Keterangan Error:</div>
      <textarea class='form-control' id=keterangan_error></textarea>
      <div id=div_laporkan_error  class=hideit>
        <span id=href_wa class=hideit>$href_wa</span>
        <a target=_blank id=link_laporkan_error href='$href_wa' class='btn btn-sm btn-warning w-100 mt2'>$img_wa Kirim</a>
      </div>
    </div>
  </div>
";
?>
<script>
  $(function() {
    $('#select_error').change(function() {
      var select_error = $(this).val();
      if (select_error == '0') {
        $('#div_laporkan_error').slideUp();
        return;
      } else {
        $('#div_laporkan_error').slideDown();
      }
      $('#keterangan_error').keyup();
    });

    // keterangan_error keyup
    $('#keterangan_error').keyup(function() {
      let keterangan_error = $(this).val().trim();
      if (keterangan_error.length) {
        $('#link_laporkan_error').prop('href',
          $('#href_wa').text() +
          `%0a%0aError pada: [ *${$('#select_error').val()}* ] %0a*Keterangan:* ` + keterangan_error
        );
      }
    });

  })
</script>