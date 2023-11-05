<section id="about" class="about">
  <div class="container">

    <div class="section-title" data-aos-zzz="fade-up">
      <h2>Menanam Soal</h2>
      <p>Tanamlah soal dengan benih yang berkualitas</p>
    </div>
<style>
  .unclicked{background: #aaa;}
  .opsi{margin-top:4px; font-size:small; border: none;color:#555}
  .blok_opsi{display:grid; grid-template-columns: 25px auto 80px; gap:8px}
  .blok_info{background: #efe}
</style>
<?php
# =================================================================
login_only();
$kalimat_soal = '';
$opsi['a'] = '';
$opsi['b'] = '';
$opsi['c'] = '';
$opsi['d'] = '';
$pembahasan = '';

$kalimat_soal = 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Quaerat, tenetur ab consectetur obcaecati laborum debitis sequi modi ipsum illum provident consequatur assumenda facilis inventore voluptatem accusantium placeat alias tempore enim? zzz  informatika, lore';
$opsi['a'] = 'opsi a';
$opsi['b'] = 'opsi b';
$opsi['c'] = 'opsi c';
$opsi['d'] = 'implikas';

# =================================================================
# HANDLE SUBMIT
# =================================================================
if(isset($_POST['btn_simpan'])){
  $kalimat_soal = $_POST['kalimat_soal'] ?? die(erid('kalimat_soal::null'));
  $opsi['a'] = $_POST['opsi__a'] ?? die(erid('opsi__a::null'));
  $opsi['b'] = $_POST['opsi__b'] ?? die(erid('opsi__b::null'));
  $opsi['c'] = $_POST['opsi__c'] ?? die(erid('opsi__c::null'));
  $opsi['d'] = $_POST['opsi__d'] ?? die(erid('opsi__d::null'));
  $pembahasan = $_POST['pembahasan'];

  $kalimat_soal = clean_sql($kalimat_soal);
  $opsi['a'] = clean_sql($opsi['a']);
  $opsi['b'] = clean_sql($opsi['b']);
  $opsi['c'] = clean_sql($opsi['c']);
  $opsi['d'] = clean_sql($opsi['d']);
  $pembahasan = clean_sql($pembahasan);

  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';

  // $s = "INSERT INTO tb_soal_mhs 
  // (id_sesi,id_peserta,kalimat_soal,tags,opsies,kj) values 
  // ('$_POST[id_sesi]','$_POST[id_peserta]','$_POST[kalimat_soal]','$_POST[input_tags]')";
  // $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  // echo div_alert('success','Submit kalimat_soal sukses.');
  // echo '<script>location.replace("?my_questions")</script>';
  exit;
}




# =================================================================
# START
# =================================================================
// max 10 kalimat_soal per sesi

$id_sesi = $_GET['id_sesi'] ?? '';
$info_sesi = '';
if($id_sesi==''){
  include 'include/arr_sesi.php';
  $pilih_sesi = '';
  foreach ($arr_sesi as $key => $sesi){
    $tags = $arr_tags[$key];
    $r = explode(';',$tags);
    sort($r);
    $imp = $tags=='' ? '<span class=red>belum bisa mengajukan kalimat_soal karena belum ada tags sesi.</span>' : implode(', ',$r);
    $tags_show = "<div class='kecil miring abu'>Tags: $imp</div>";
    $danger = $tags=='' ? 'danger' : 'success'; 
    $href = $tags=='' 
    ? "'#' onclick='alert(\"Maaf, belum bisa mengajukan kalimat_soal pada sesi ini karena instuktur belum setting tags untuk sesi ini.\")'" 
    : "'?tanam_soal&id_sesi=$key'";
    $pilih_sesi .= "<div class='col-md-4 mb2'><a class='btn btn-$danger btn-sm mb1 btn-block' href=$href>P$arr_no_sesi[$key] $sesi</a>$tags_show</div>";
  } 

  $info_sesi = "
    <div class='darkblue tebal'>Silahkan pilih di sesi manakah kamu ingin membuat soal:</div><hr>
    <div class=row>$pilih_sesi</div>
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
    <div>Sesi: <code>$nama_sesi</code> | <a href='?tanam_soal'>pilih sesi lain</a></div>
  ";











  $rabjad = ['a','b','c','d'];
  $opsies = '';
  foreach ($rabjad as $abjad) {
    $opsies.= "
      <div class='blok_opsi'>
        <div class=tengah>$abjad.</div>
        <div>
          <input class='form-control opsies' id=opsi__$abjad name=opsi__$abjad minlength=3 maxlength=30 required value='$opsi[$abjad]'>
        </div>
        <div>
          <span class='btn btn-sm btn-info btn-block opsi unclicked set_kj' id=set_kj__$abjad name=set_kj__$abjad>Set KJ</span>
        </div>
      </div>
    ";
  }









  $form = "
    <form method=post>
      <input class=debug name=id_peserta value=$id_peserta>
      <input class=debug name=id_sesi value=$id_sesi>
      <input class=debug name=my_tags id=my_tags placeholder=my_tags>
      <input class=debug name=kj id=kj placeholder=kj>

      <div class='form-group mb2'>
        <label for=kalimat_soal>Kalimat soal:</label>
        <textarea name=kalimat_soal id=kalimat_soal class='form-control' rows=6 minlength=30 required>$kalimat_soal</textarea required>
        <div class='kecil miring abu mt1' id=minimal_30_huruf>minimal 30 huruf s.d 300 huruf</div>
      </div>

      $opsies

      <div class='wadah mt2 blok_info'>
        <div class='miring abu' style='font-size:10px'>Syarat simpan soal:</div>
        <div class='kecil miring darkred mt2' id=opsi_error></div>
        <div class='kecil miring darkred mt2' id=info_kj>Kunci Jawaban belum di Set</div>
        <div class='kecil miring darkred mt2' id=info_tags>Tags belum ada di kalimat atau opsi soal</div>
        <div class='kecil miring abu mt1 mb2' id=info_tags_awal>
          Pilihan Tags: 
          <span id=tags class=green>$tags</span>.
        </div>
        <div class='kecil miring darkred mt2 hideit' id=blok_similaritas>
          <span class='btn btn-secondary btn-sm' id=btn_cek_similaritas>Cek Similaritas</span> <span id=similaritas>0</span>%
        </div>
      </div>

      <div id=info_poin class=mb2></div>
      <div id=blok_toggle_pembahasan class='hideit mt2'>
        <span class='btn btn-secondary btn-sm kecil' id=toggle_pembahasan>Tambah Pembahasan (+50 LP):</span>
      </div>
      <div id=blok_pembahasan class='hideit mt2'>
        <textarea name=pembahasan id=pembahasan class=form-control rows=6 minlength=30>$pembahasan</textarea>
        <div class='kecil miring abu'>Sifatnya opsional, minimal 30 huruf</div>
      </div>

      <div class=form-group>
        <button name=btn_simpan id=btn_simpan class='btn btn-primary btn-block' disabled>Simpan Soal Saya</button>
      </div>

    </form>
  ";
}



?>


    <div class="wadah gradasi-hijau" data-aos-zzz='fade-up'>
      <?=$info_sesi?>
      <?=$form?>

    </div>

  </div>
</section>














<script>
  $(function(){
    let tags = $('#tags').text().split(', ');
    let my_tags = [];
    let minimal_30_huruf = $('#minimal_30_huruf').text();
    let img_check = '<img src="assets/img/icons/check.png" alt="ok" height="20px" />';
    let kalimat_soal = '';
    let kalimat_soal_full = '';
    let kj = '';
    let similaritas = 0;

    $('#kalimat_soal').keyup(function(){
      let val = $(this).val();
      if(val.length>=30){
        $('#minimal_30_huruf').html(val.length + ' of max 300 huruf '+img_check);
        kalimat_soal = val.trim();

        // recall
        $('.opsies').keyup();
      }else{
        $('#minimal_30_huruf').text(val.length + ' | '+minimal_30_huruf);
      }
    })

    $('.opsies').keyup(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let abjad = rid[1];
      
      let opsi__a = $('#opsi__a').val().trim();
      let opsi__b = $('#opsi__b').val().trim();
      let opsi__c = $('#opsi__c').val().trim();
      let opsi__d = $('#opsi__d').val().trim();

      let kalimat_soal = $('#kalimat_soal').val().trim();
      $('#info_poin').text('');
      $('#btn_simpan').prop('disabled',true);
      $('#blok_similaritas').hide();
      $('#blok_toggle_pembahasan').hide();

      if(0
        || opsi__a.length<3
        || opsi__b.length<3
        || opsi__c.length<3
        || opsi__d.length<3
        ){
        $('#opsi_error').text('Opsi minimal 3 s.d 30 huruf');
      }else{
        if(0 
          || opsi__a.toUpperCase()==opsi__b.toUpperCase()
          || opsi__a.toUpperCase()==opsi__c.toUpperCase()
          || opsi__a.toUpperCase()==opsi__d.toUpperCase()
          || opsi__b.toUpperCase()==opsi__c.toUpperCase()
          || opsi__b.toUpperCase()==opsi__d.toUpperCase()
          || opsi__c.toUpperCase()==opsi__d.toUpperCase()
          ){
          $('#opsi_error').text('Dilarang ada opsi yang sama!');
        }else{
          $('#opsi_error').text('');
          // =================================================
          // OPSIES LENGTH ARE OK :: MY TAGS
          // =================================================
          kalimat_soal_full = `${kalimat_soal} ${opsi__a} ${opsi__b} ${opsi__c} ${opsi__d}`;

          my_tags = []; // re-empty tags
          tags.forEach(tag => {
            if(kalimat_soal_full.toLocaleLowerCase().search(tag.toLowerCase())>=0){
              my_tags.push(tag)
            }
          });

          // console.log(my_tags);
          if(my_tags.length==0){
            $('#info_tags').text('Tags belum ada di kalimat atau opsi soal');
            $('#info_tags_awal').fadeIn();
            $('#info_tags').addClass('darkred');
            $('#tags').val('');
          }else{
            // =================================================
            // MY TAGS OK
            // =================================================
            $('#info_tags_awal').fadeOut();
            $('#info_tags').html('<div class=mb2>Tags yang dipakai: <span class=blue>'
            +my_tags.join(', ')+'</span> '+img_check+'</div>');
            $('#info_tags').removeClass('darkred');
            $('#my_tags').val(my_tags.join(','));
            if(kj!=''){
              // =================================================
              // MY TAGS OK + KJ OK
              // =================================================
              $('#blok_similaritas').fadeIn();
              
              if(similaritas>0 && similaritas<80){
                $('#blok_toggle_pembahasan').fadeIn();
                $('#info_poin').html('<span class="btn btn-secondary btn-sm">Poin membuat soal +100 LP</span> ' + img_check);
                $('#btn_simpan').prop('disabled',false);
              }
            }
          }
        }
      }


    })

    $('.set_kj').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let abjad = rid[1];
      // console.log(abjad);

      $('.set_kj').addClass('unclicked');
      $('.set_kj').text('Set KJ');
      $(this).removeClass('unclicked');
      $(this).text('KJ : '+abjad.toUpperCase());
      $('#info_kj').html('Kunci Jawaban : '+abjad.toUpperCase()+' '+img_check);
      $('#info_kj').removeClass('darkred');
      $('#info_kj').addClass('biru tebal');
      $('#kj').val(abjad);
      kj = abjad;
      // recall
      $('.opsies').keyup();
    })

    $('#btn_cek_similaritas').click(function(){
      let link_ajax = `ajax/cek_similaritas_soal.php?my_tags=${my_tags}&kalimat_soal=${kalimat_soal}`;

      $.ajax({
        url:link_ajax,
        success:function(a){
          let ra = a.split('__');
          if(ra[0]=='sukses'){
            $('#similaritas').text(ra[1]);
          }else{
            alert(a);
          }
        }
      })

    })

    $('#toggle_pembahasan').click(function(){
      $('#blok_pembahasan').fadeToggle();
    })

  })
</script>