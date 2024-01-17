<section id="about" class="about"><div class="container">
<style>
  .unclicked{background: #aaa;}
  .opsi{margin-top:4px; font-size:small; border: none;color:#555}
  .blok_opsi{display:grid; grid-template-columns: 25px auto 80px; gap:8px}
  .blok_info{background: #efe}
</style>
<?php
# =================================================================
login_only();
if(!$status_room) die($div_alert_closed);



$link = "<a href='?soal_saya'>Soal Saya</a>";
$link2 = "<a href='?perang_soal'>Perang Home</a>";
$meme = $punya_profil_perang ? meme('menanam') : '';
echo "
  <div class='section-title' data-aos-zzz='fade-up'>
    <h2>Menanam Soal</h2>
    <p>
      <div>$link2 | $link</div>
      <div class='kecil abu '>
        <div class=mb2>Tanamlah soal dengan benih yang berkualitas</div>
        $meme
      </div> 
    </p>
  </div>
";


if($punya_profil_perang){
  include 'tanam_soal_begin.php';
}else{
  $meme = meme('dont-have');
  echo div_alert('danger tengah', "$meme <br>Kamu mesti punya profil perang dulu!<hr><a href=?upload_profil_perang>Upload Profile Pose Bebas</a>");
}