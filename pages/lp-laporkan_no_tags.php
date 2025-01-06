
<?php
# ============================================================
# BELUM ADA TAGS
# ============================================================
if ($id_role == 2) {
  $ui_tags .= "
    <div class='alert alert-danger kecil miring mb4 tengah belum_ada_tags' id=belum_ada_tags__$id_sesi>
      belum ada tags
      <hr>
      tanpa tags, di sesi ini $Peserta tidak bisa melakukan: Presensi, Tanam Soal, atau Bertanya. Tags merupakan pemandu materi agar alur belajar $Peserta tidak <i>out-of-topic</i>.
      <hr>
      <span class=blue>Silahkan klik tombol edit lalu isi beberapa tags untuk sesi ini.</span>
    </div>
  ";
} else {
  $ingin = "ingin *Request Tag Materi* untuk P-$sesi[no]";
  $href_wa = href_wa(
    $trainer['no_wa'],
    $ingin,
    'NO TAGS',
    false,
    false,
    $trainer['nama'],
    $trainer['gender'],
    $user['nama']
  );

  $ui_tags .= "
    <div class='alert alert-danger kecil miring mb4 tengah belum_ada_tags'>
      <div class='red mb2'>belum ada tags, kamu belum bisa presensi, tanam soal, atau bertanya di sesi ini.</div>
      <a target=_blank href='$href_wa' class='btn btn-primary'>$img_wa Laporkan</a>
    </div>
  ";
}
