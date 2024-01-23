<?php
$pertanyaan = '';
if(isset($_POST['btn_submit'])){
  $pertanyaan = $_POST['pertanyaan'] ?? '';
  $s = "INSERT INTO tb_pertanyaan (id_sesi,id_penanya,pertanyaan,tags) values ('$_POST[id_sesi]','$_POST[id_penanya]','$_POST[pertanyaan]','$_POST[input_tags]')";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  echo div_alert('success','Submit pertanyaan sukses.');
  echo '<script>location.replace("?my_questions")</script>';
  exit;
}


// max 3 pertanyaan per hari
$today = date('Y-m-d');
$today_end = date('Y-m-d').' 23:59:59';
$s = "SELECT 1 FROM tb_pertanyaan WHERE id_penanya=$id_peserta AND tanggal >= '$today' AND tanggal < '$today_end' ";
$qp = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_pertanyaan_hari_ini = mysqli_num_rows($qp);

$max_reached = $jumlah_pertanyaan_hari_ini>=3 
? ' <span class="darkred miring">Kamu dapat mengajukan kembali pertanyaan esok hari.</span>' 
: ' Kamu masih boleh mengajukan '.(3-$jumlah_pertanyaan_hari_ini).' pertanyaan lagi.';

$jp_info = $jumlah_pertanyaan_hari_ini 
? "Kamu sudah mengajukan $jumlah_pertanyaan_hari_ini pertanyaan hari ini.$max_reached" 
: 'Hari ini kamu belum bertanya. Yuk bertanya agar mendapat poin, ilmu, dan wawasan!';


$id_sesi = $_GET['id_sesi'] ?? '';
$info_sesi = '';
if($id_sesi==''){
  include 'include/arr_sesi.php';
  $pilih_sesi = '';
  foreach ($arr_sesi as $key => $sesi){
    $tags = $arr_tags[$key];
    $r = explode(';',$tags);
    sort($r);
    $imp = $tags=='' ? '<span class=red>belum bisa mengajukan pertanyaan karena belum ada tags sesi.</span>' : implode(', ',$r);
    $tags_show = "<div class='kecil miring abu'>Tags: $imp</div>";
    $danger = $tags=='' ? 'danger' : 'success'; 
    $href = $tags=='' 
    ? "'#' onclick='alert(\"Maaf, belum bisa mengajukan pertanyaan pada sesi ini karena instuktur belum setting tags untuk sesi ini.\")'" 
    : "'?bertanya&id_sesi=$key'";
    $pilih_sesi .= "<a class='btn btn-$danger btn-sm mb1' href=$href>$sesi</a>$tags_show<br>";
  } 

  $info_sesi = "
    <div class=mb2>Saya ingin bertanya tentang:</div>
    $pilih_sesi
  ";
  $form = '';
}else{
  $s = "SELECT * FROM tb_sesi WHERE id=$id_sesi ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==0) die(erid('id_sesi (not found)'));
  $d = mysqli_fetch_assoc($q);
  $tags = $d['tags'];
  $nama_sesi = $d['nama'];
  $no_sesi = $d['no'];
  $info_sesi = "
    <div>Saya ingin bertanya tentang: <code>$nama_sesi</code> | <a href='?bertanya'>pilih topik lain</a></div>
  ";

  $form = "
    <form method=post>
      <input class=debug name=id_penanya value=$id_peserta>
      <input class=debug name=id_sesi value=$id_sesi>
      <input class=debug name=input_tags id=input_tags value=input_tags>
      <div class=form-group>
        <label for=pertanyaan>Pertanyaan saya:</label>
        <textarea name=pertanyaan id=pertanyaan class=form-control rows=6>$pertanyaan</textarea>
        <div class='small miring abu mt1' id=blok_info_tags>Pertanyaan kamu harus mengandung salah satu tags berikut: <span id=tags class=darkred>$tags</span>.</div>
        <div class='small miring abu mt1 hideit' id=blok_my_tags>tags: <span id=my_tags class='tebal biru kecil'>my_tags</span></div>
        <div class='red kecil miring' id=length_info></div>
      </div>

      <div id=saya_menyatakan class='hideit mt2'>
        <div class='kecil abu miring wadah'>
          estimasi point: 
          <ul>
            <li>basic point: 100 LP</li>
            <li>session point: 200 LP (jika diajukan saat sesi berlangsung)</li>
            <li>bobot soal point: -300 s.d 500 LP (saat diverifikasi oleh instruktur)</li>
          </ul>
        </div>

        <div class=form-group>
          <label>Saya menyatakan bahwa:</label>
          <div class=wadah>
            <label><input class='cek_syarat' type=checkbox id=cek1> Pertanyaan saya tidak asal-asalan</label>
            <br><label><input class='cek_syarat' type=checkbox id=cek2> Pertanyaan saya sesuai dg topik sesi yang saya pilih</label>
          </div>
        </div>

        <div class=form-group>
          <button name=btn_submit id=btn_submit class='btn btn-primary btn-block' disabled>Submit</button>
        </div>
      </div>

    </form>
  ";
}


$jp_info = ''; //zzz suspend fitur
$info_sesi = ''; //zzz suspend fitur
$form = ''; //zzz suspend fitur
?>

<div class="section-title" data-aos="fade-up">
  <h2>Bertanya</h2>
  <p>Kamu boleh bertanya kapan saja, setiap pertanyaan berbobot akan mendapatkan poin. Double poin jika kamu bertanya pada saat sesi berlangsung atau instruktur sedang menerangkan!</p>
</div>

<div class="wadah gradasi-hijau" data-aos='fade-up'>
  <div style='padding-bottom: 10px; margin-bottom: 10px; border-bottom: solid 1px #ccc; color: darkblue'>
    <?=$jp_info?>
    <div class="alert alert-danger tengah">
      Maaf, fitur ini di suspend dikarenakan sulitnya dalam proses verifikasi. | Fitur dialihkan ke <a href="?tanam_soal">Tanam Soal</a>
      <hr>
      <?=meme('funny')?>

    </div>
  </div>

  <?=$info_sesi?>
  <?=$form?>

</div>













<script>
  $(function(){
    let tags = $('#tags').text().split(', ');

    // console.log(tags);
    let my_tags = [];

    $('#pertanyaan').keyup(function(){
      let val = $(this).val();
      my_tags = [];
      // alert(val);
      tags.forEach(i => {
        if(val.search(i)>=0){
          my_tags.push(i)
        }
      });
      // console.log(my_tags);
      if(my_tags.length>0){

        $('#input_tags').val(my_tags.join(', '));
        $('#my_tags').text(my_tags.join(', '));
        $('#blok_info_tags').hide();
        $('#blok_my_tags').fadeIn();
        if(val.length>=50){
          $('#saya_menyatakan').slideDown();
          $('#length_info').text('');
          $('#length_info').fadeOut();
        }else{
          $('#saya_menyatakan').slideUp();
          $('#length_info').text('minimal 50 karakter, kamu mengetik '+val.length+' karakter.');
          $('#length_info').fadeIn();
        }
        
      }else{
        $('#my_tags').text('-');
        $('#blok_my_tags').hide();
        $('#blok_info_tags').fadeIn();
        $('#saya_menyatakan').slideUp();

      }

    })

    $('.cek_syarat').click(function(){
      let cek1 = $('#cek1').prop('checked');
      let cek2 = $('#cek2').prop('checked');

      let dis = cek1 && cek2 ? true : false;

      $('#btn_submit').prop('disabled', !(cek1 && cek2));
      console.log(cek1,cek2,dis);
    })
  })
</script>