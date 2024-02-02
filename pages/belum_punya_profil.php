<div class="alert alert-danger tengah" data-aos='fade' data-aos-delay='200'>
  <div class="tengah">
    <a href="?upload_profil">
      <img class='foto_profil' src="<?=$path_profil_na?>" alt="ga punya profil">
    </a>
  </div>
  Hai! Kamu belum punya profil. Silahkan <a href="?upload_profil">Upload Profile</a> terlebih dahulu untuk proses dokumentasi, cetak nilai KHS, dan keperluan dokumen lainnya.
  <hr>
  <div class="kecil"><a href="#" id=ntar_aja>ntar aja deh!</a></div>
</div>
<script>
  $(function(){
    $('#ntar_aja').click(function(){
      $('#belum_punya_profil').slideUp()
    })
  })
</script>