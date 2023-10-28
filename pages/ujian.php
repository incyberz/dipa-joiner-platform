<style>
  .div_soal{border-top: solid 2px #ddd; padding: 10px;}
  .belum_dijawab{background: linear-gradient(#fee,#fcc)}
  .no_dan_soal{display: grid; grid-template-columns: 20px auto; grid-gap:5px}
</style>
<?php
if(!$is_login) die('<script>location.replace("?")</script>');
$id_paket_soal=$_GET['id_paket_soal'] ?? '';


# =======================================================
# PAKET SOAL YANG TERSEDIA
# =======================================================
if($id_paket_soal==''){
  $s = "SELECT * FROM tb_paket_soal WHERE kelas='$kelas'";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $list_paket = div_alert('danger', "Maaf, belum ada Paket Soal untuk kelas $kelas.");
  if(mysqli_num_rows($q)){
    $list_paket = '';
    while ($d=mysqli_fetch_assoc($q)) {
      $list_paket = "
      <div>
        <a class='btn btn-info btn-sm' href='?ujian&id_paket_soal=$d[id]'>$d[nama]</a>
      </div>";
    }
  
    die("<section><div class=container>Silahkan pilih Paket Soal: $list_paket</div></section>");
  }
}


# =======================================================
# GET PROPERTIES PAKET UJIAN
# =======================================================
$s = "SELECT * FROM tb_paket_soal WHERE id=$id_paket_soal AND kelas='$kelas'";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0) die("Data Paket Soal tidak ditemukan.");
$d=mysqli_fetch_assoc($q);
$mode = $d['mode'];
$nama_paket_soal = $d['nama'];
$awal_ujian = $d['awal_ujian'];
$akhir_ujian = $d['akhir_ujian'];
$is_locked = $d['is_locked'];
$tmp_jawabans = $d['tmp_jawabans'];






if(isset($_POST['btn_submit'])){

  echo '<pre style=margin-top:200px>'; var_dump($_POST); echo '</pre>';
  $jawabans = $_POST['jawabans'];
  $jawabans .= '___';
  $jawabans = str_replace('|___','',$jawabans);

  echo '<pre>'; var_dump($jawabans); echo '</pre>';
  
  $arr_jawaban = explode('|',$jawabans);
  echo '<pre>'; var_dump($arr_jawaban); echo '</pre>';

  $arr_kj = explode('|',$tmp_jawabans);
  echo '<pre>'; var_dump($arr_kj); echo '</pre>';

  $jumlah_benar=0;
  foreach ($arr_jawaban as $jawaban) {
    if(in_array($jawaban,$arr_kj)){
      $jumlah_benar++;
    }
  }

  echo "jb $jumlah_benar ";




  exit;
}





# =======================================================
# MODE UJIAN
# =======================================================
$rmode['uh'] = 'Ujian Harian';
$rmode['uts'] = 'Ujian Tengah Semester';
$rmode['uas'] = 'Ujian Akhir Semester';
$rmode['upk'] = 'Ujian Praktikum';
$rmode['tas'] = 'Tugas Akhir Semester';
$rmode['up'] = 'Ujian Perbaikan';


# =======================================================
# JENIS SOAL
# =======================================================
# 1. PG
# 2. TF
# 3. MC : Multiple Check
# 4. ISIAN
$jenis_soal[1] = ['PG','Pilihan Ganda'];
$jenis_soal[2] = ['TF','True False'];
$jenis_soal[3] = ['MC','Multi Check'];
$jenis_soal[4] = ['IS','Isian Singkat'];
$jenis_soal[5] = ['UR','Uraian'];


# =======================================================
# PAKET DAN LIST SOAL
# =======================================================
$list_soal = '';
$s= "SELECT 
a.id as id_assign_soal,
b.soal,
b.kjs,
b.id as id_soal,
c.nama as paket_soal,
d.nama as tipe_soal 
FROM tb_assign_soal a 
JOIN tb_soal b ON a.id_soal=b.id 
JOIN tb_paket_soal c ON a.id_paket_soal=c.id 
JOIN tb_tipe_soal d ON b.tipe_soal=d.tipe_soal 
WHERE c.kelas = '$kelas' 
limit 4
";

// ORDER BY rand() 
// LIMIT 3

$div = '';
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_soal = mysqli_num_rows($q);
$i=0;
$new_tmp_jawabans = '';
while ($d=mysqli_fetch_assoc($q)) {
  $id_assign_soal = $d['id_assign_soal'];
  $new_tmp_jawabans.= $id_assign_soal."__$d[kjs]|";


  $i++;
  $div .= "
  <div class='div_soal belum_dijawab' id=div_soal__$id_assign_soal>
    <div class=row>
      <div class='col-md-8 mb2'>
        <div class=no_dan_soal>
          <div>$i.</div>
          <div>
            $d[soal] 
            <span class=debug>
              id_assign_soal:<span id=id_assign_soal__$id_assign_soal>$id_assign_soal</span> | 
              jawaban:<span id=jawaban__$id_assign_soal class=jawaban></span>
            </span> 
          </div>
        </div>
      </div>
      <div class=col-md-4>
        <div class='row'>
          <div class='col-sm-6 mb1'><span class='btn btn-secondary btn-sm btn-block btn_tf' id=btn_true__$id_assign_soal>True</span></div>
          <div class='col-sm-6 mb1'><span class='btn btn-secondary btn-sm btn-block btn_tf' id=btn_false__$id_assign_soal>False</span></div>
        </div>
      </div>
    </div>
  </div>
  ";
}

// autosave tmp_jawabans if null
echo "$tmp_jawabans zzz $new_tmp_jawabans";
if(strlen($new_tmp_jawabans) != strlen($tmp_jawabans)){
  $s = "UPDATE tb_paket_soal SET tmp_jawabans='$new_tmp_jawabans' WHERE id=$id_paket_soal";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

}

$list_soal = "<div data-aos='fade-up' data-aos-delay=300>$div</div>";







?>
<section>
  <div class="container">

    <div class="section-title" data-aos="fade-up">
      <h2>Ujian</h2>
      <p><?=$rmode[$mode]?> untuk <?=$kelas?></p>
      <span class="debug">jumlah_soal: <span id=jumlah_soal><?=$jumlah_soal?></span></span>

    </div>

    <div class="wadah gradasi-hijau tengah" data-aos='fade-up' data-aos-delay='150'>
      <!-- <h1>Comming soon !!</h1>
      Ujian akan diselenggarakan dalam:
      <div> 
        <div class=consolas style="font-size:50px">00:00:00</div>
        detik lagi
      </div> -->
    </div>
    <?=$list_soal?>
    <form method=post>
      <input id=jawabans name=jawabans class="debug">
      <div class="mt2" data-aos='fade-up' data-aos-delay='450'>
        <span id=span_submit class='btn btn-secondary btn-block' disabled>Belum bisa Submit Jawaban 
          <br>
          <span class=yellow>
            <span id=belum_dijawab_count style='font-size:40px'>
              <?=$jumlah_soal?>
            </span>
            soal belum dijawab
          </span>
        </span>
        <div id="blok_btn_submit" class=hideit>
          <div class="mb2 mt2">
            <input type="checkbox" id="check_submit"> 
            <label for="check_submit">Saya sudah menjawab semua soal dan yakin untuk Submit</label>
          </div>
          <button class='btn btn-primary btn-block ' name=btn_submit id=btn_submit disabled>Submit Jawaban</button>
          <div class="small miring"><b>Perhatian!</b> Silahkan re-check kembali selama masih ada waktu!</div>

        </div>
      </div>
    </form>
  </div>
</section>


<script>
  $(function(){
    let jawaban_set = new Set();
    let jumlah_soal = $('#jumlah_soal').text();


    // ==============================================
    // COOKIE HANDLER
    // ==============================================
    let dkue = document.cookie.split(';');

    let kues='';
    dkue.forEach((kue) => {
      if(kue.substring(0,9)=='jawabans='){
        kues = kue.substring(9,500);
      }
    })
 
    $('#jawabans').val(kues);
    let rkue = kues.split('|');
    console.log(rkue);

    rkue.forEach((idkj) => {
      if(idkj.length>=4){

        
        
        let ridkj = idkj.split('__');
        let id = ridkj[0];
        let kj = ridkj[1];

        $('#jawaban__'+id).text(idkj);
        jawaban_set.add(id);

        if(kj.toUpperCase()=='T'){
          $('#btn_true__'+id).removeClass('btn-secondary');
          $('#btn_true__'+id).addClass('btn-success');
        }else if(kj.toUpperCase()=='F'){
          $('#btn_false__'+id).removeClass('btn-secondary');
          $('#btn_false__'+id).addClass('btn-warning');
        }else{
          console.log('Undefined KJ :'+kj);
        } 
      }
    })


    
    
    // console.log(rkue);



    $('#check_submit').click(function(){
      $('#btn_submit').prop('disabled',!$(this).prop('checked'))

    })


    $('.btn_tf').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let btn = rid[0];
      let id_assign_soal = rid[1];
      let jawaban;

      if(btn=='btn_true'){
        $(this).addClass('btn-success');
        $(this).removeClass('btn-secondary');
        $('#btn_false__'+id_assign_soal).removeClass('btn-warning');
        $('#btn_false__'+id_assign_soal).addClass('btn-secondary');
        jawaban = 'T';
      }else{
        jawaban = 'F';
        $(this).addClass('btn-warning');
        $(this).removeClass('btn-secondary');
        $('#btn_true__'+id_assign_soal).removeClass('btn-success');
        $('#btn_true__'+id_assign_soal).addClass('btn-secondary');
      }
      $('#div_soal__'+id_assign_soal).removeClass('belum_dijawab');
      $('#jawaban__'+id_assign_soal).text(id_assign_soal+'__'+jawaban);
      jawaban_set.add(id_assign_soal);
      // console.log(jawaban_set.size)
      let belum_dijawab_count = jumlah_soal - jawaban_set.size;
      $('#belum_dijawab_count').text(belum_dijawab_count);

      
      let jawabans='';
      let rj = document.getElementsByClassName('jawaban');
      for (let i = 0; i < rj.length; i++) {
        jawabans += rj[i].innerText + '|';
      }
      $('#jawabans').val(jawabans);
      document.cookie = 'jawabans=' + jawabans;

      
      
      if(belum_dijawab_count==0){
        $('#span_submit').hide();
        $('#blok_btn_submit').fadeIn(2000);
      }
      

    })
  })
</script>