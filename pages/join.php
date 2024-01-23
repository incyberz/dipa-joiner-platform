<?php
$as = $_GET['as'] ?? '';
if($as!=''){
  if(file_exists("pages/join_as_$as.php")){
    include "join_as_mhs.php";
    
  }else{
    echo div_alert('danger','Maaf, fitur ini masih dalam tahap pengembangan.');
  }
}else{ 
  $arr_as = ['mhs','dosen','praktisi','industri'];
  $arr_gradasi = ['hijau','hijau','biru','kuning'];
  $arr_ket = [
    'Saya ingin belajar dengan target di dunia nyata',
    'Koordinator mahasiswa, praktisi, dan industri',
    'Saya bersedia mentoring dengan senang hati',
    'Saya membutuhkan jasa dari mahasiswa'
  ];

  $blok_joins = '';
  foreach ($arr_as as $key => $value) {
    $time_anim = ($key+1)*150;
    $blok_joins.="
      <div class='col-lg-3' data-aos='fade-up' data-aos-delay='$time_anim'>
        <div class='wadah gradasi-$arr_gradasi[$key]'>
          <div class='text-center p-4'>
            <img src='assets/img/icons/$value.png' alt='as $value' class='foto-ilustrasi'>
          </div>
          <a href='?join&as=$value' class='btn btn-primary btn-block proper'>Sebagai $value</a>
          <div class='tengah kecil abu mt1'>$arr_ket[$key]</div>
        </div>
      </div>
    ";
  }

  ?>

<div class="section-title" data-aos="fade-up">
  <h2>Join</h2>
</div>

<div class="row content">
  <?=$blok_joins?>
</div>
<?php } ?>

<div class="tengah mt3" data-aos="fade-up" data-aos-delay="800">Sudah punya akun? Silahkan <a href="?login"><b>Login</b></a></div>

