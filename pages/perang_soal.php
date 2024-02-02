<script src="assets/js/aes.js" integrity="sha256-/H4YS+7aYb9kJ5OKhFYPUjSJdrtV6AeyJOtTkw6X72o=" crossorigin="anonymous"></script>
<?php
# =================================================================
login_only();
if(!$status_room) die($div_alert_closed);
$mode = $_GET['mode'] ?? '';
$start = $_GET['start'] ?? '';



if(isset($_POST['btn_accept_points'])){
  // handle next soal | ga dijawab
  echo div_alert('success',"Memproses poin request...");
  echo "<span class=debug id=encdata>$_POST[cidnjpp]</span>";

  //get last id_paket_war
  $s = "SELECT id as id_paket_war FROM tb_paket_war WHERE id_peserta=$id_peserta ORDER BY tanggal DESC LIMIT 1";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $d = mysqli_fetch_assoc($q);
  $id_paket_war = $d['id_paket_war'];
  echo "<span class=debug>id_paket_war:<span id=id_paket_war>$id_paket_war</span></span>";

  ?><script>
    $(function(){
      let buyar = $('#encdata').text();
      let debuyar = CryptoJS.AES.decrypt(buyar, "DIPA Joiner");
      let asli = debuyar.toString(CryptoJS.enc.Utf8);
      let id_paket_war = $('#id_paket_war').text();
      let id_room = $('#id_room').text();
      let link_ajax = `ajax/kirim_hasil_kuis.php?id_paket_war=${id_paket_war}&id_room=${id_room}&data=`+asli;

      $.ajax({
        url:link_ajax,
        success:function(a){
          if(a.trim()=='sukses'){
            // redirect to meme
            // console.log(a);
            location.replace("?perang_soal_sukses");

          }else{
            alert(a);
          }
        }
      })
    })
  </script>
  <?php
}else{
  // normal view
  $durasi = 30; //default
  $abjad = ['a','b','c','d'];
  $kj = '';
  $status_soal = '<span class="darkred">unverified</span>';
  $arr_randpos = ['0123','0132','0213','0231','1230','1203','1320','1302','2301','2310','2031','2013','3012','3021','3102','3120'];
  
  $link = "<a href='?tanam_soal'>Tanam Soal</a>";
  $link2 = "<a href='?soal_saya'>Soal Saya</a>";
  $link3 = "<a href='?perang_soal'>Perang Soal</a>";
  if(!$start){
    echo "
      <div class='section-title' data-aos-zzz='fade-up'>
        <h2>Perang Soal</h2>
        <p>
          <div>$link | $link2</div>
          <div class='kecil mt2 abu'>Jawablah soal! Benar atau salah tetap dapat nilai.</div> 
          <div class='kecil mt2 mb4'><span class=blue>Answer the Question</span> <span class=red>OR die!!</span></div> 
          <img src=assets/img/answer-or-reject.png style='max-width:250px'>
        </p>
      </div>
    ";
  }
  
  
  
  
  
  
  if($mode==''){
    $meme = meme('dont-have');
    $div = "
      <div class=wadah>
        <div>Silahkan pilih mode:</div>
        <div class=row>
          <div class=col-md-6>
            <a class='btn btn-info btn-block' href='?perang_soal&mode=random'>Random Mode</a>
            <div class='kecil miring abu mb2'>
              Jawablah 10 soal random milik kawan-kawanmu
            </div>
          </div>
          <div class=col-md-6>
            <span class='btn btn-info btn-block' id=pvp_mode>PvP Mode</span>

            <div class=' wadah kecil miring darkred mb2 hideit tengah' id=pvp_mode_info>
              Maaf, fitur ini masih dalam tahap pengembangan
              <div class=tengah>$meme</div>
            </div>
            <div class='kecil miring abu mb2'>
              Jawablah semua soal milik seorang kawanmu
            </div>
          </div>
          <div class='tengah kecil'><a class='btn btn-secondary btn-sm' href='?war_history'>War History</a></div>
        </div>
  
  
      </div>
    ";
    echo $div;
  }else{
    if($mode=='random'){
      include 'perang_soal_random.php';
    }elseif($mode=='pvp'){
      // pvp
      echo div_alert('info', "Mode PvP");
  
    }else{
      echo div_alert('danger', "Invalid mode menjawab | $link3");
    }
  }
}  






















?>
<script>
  $(function(){
    $('#pvp_mode').click(function(){
      $('#pvp_mode_info').slideToggle();
    })
  })
</script>
