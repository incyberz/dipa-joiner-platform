<?php
$ket = '';
if ($profil_ok == 1) {
  // bentrok dg login as
  // die('Salah routing ke belum_punya_profil');
} elseif ($profil_ok == -1) {
  $ket = "Wah ternyata profil kamu direject oleh instruktur! <a href='?upload_profil'>Silahkan Reupload</a> sesuai dg petunjuk ya!";
} else { // belum upload atau belum diverifikasi
  if ($punya_profil) {
    $ket = '';
    echo div_alert('info', 'Profil status :: Sedang menunggu verifikasi profil dari instruktur');
    if ($id_role == 2) {
      // auto-self verification
      $s = "UPDATE tb_peserta SET profil_ok=1 WHERE id=$id_peserta";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      jsurl();
    }
  } else {
    $ket = "Hai! Kamu belum punya profil. Silahkan <a href='?upload_profil'>Upload Profile</a> terlebih dahulu untuk proses dokumentasi, cetak nilai KHS, dan keperluan dokumen lainnya.";
  }
}

if ($ket) {
  echo "
    <div class='alert alert-danger tengah' data-aos='fade' data-aos-delay='200' id=belum_punya_profil>
      <div class='tengah'>
        <a href='?upload_profil'>
          <img class='foto_profil' src='$path_profil_na' alt='ga punya profil'>
        </a>
      </div>
      $ket
      <hr>
      <div class='kecil'><a href='#' id=ntar_aja>ntar aja deh!</a></div>
    </div>
    <script>
      $(function(){
        $('#ntar_aja').click(function(){
          $('#belum_punya_profil').slideUp();
        })
      })
    </script>  
  ";
}
