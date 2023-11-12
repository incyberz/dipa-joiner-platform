<script src="assets/js/md5.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js" integrity="sha256-/H4YS+7aYb9kJ5OKhFYPUjSJdrtV6AeyJOtTkw6X72o=" crossorigin="anonymous"></script> -->
<script src="assets/js/aes.js" integrity="sha256-/H4YS+7aYb9kJ5OKhFYPUjSJdrtV6AeyJOtTkw6X72o=" crossorigin="anonymous"></script>
<?php
$id_soals = '';
$jawabans_md5 = '';
$durasi_default = 30;
$rows_hasil = '';

$s = "SELECT * FROM tb_alasan_reject WHERE id<9";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$alasan_reject = '';
while($d=mysqli_fetch_assoc($q)){
  $id=$d['id'];
  $alasan=$d['alasan'];
  $alasan_reject.= "
  <label style='display:block' class='opsi_reject unclicked_reject' id=opsi_reject__$id>
    <input type=radio name=id_alasan id=id_alasan__$id value=$id> $d[alasan]
  </label>
  ";
}

$path_na = "assets/img/no_profil.jpg";
$path = "assets/img/peserta/wars/peserta-$id_peserta.jpg";
$path = file_exists($path) ? $path : $path_na;
$profil_penjawab = "<img src='$path' class=profil_penjawab>";


$values_id_soals = '';
foreach ($arr_id_soal as $id_soal) {
  if(strlen($id_soal)>0){
    array_push($arr_id_soal,$id_soal);
    $values_id_soals .= " a.id=$id_soal OR";
  }
}
$values_id_soals = "($values_id_soals)";
$values_id_soals = str_replace('OR)',')',$values_id_soals);



# =============================================================
# MAIN SELECT
# =============================================================
$left_join_where = "
  LEFT JOIN tb_perang c ON a.id=c.id_soal AND c.id_penjawab=$id_peserta 
  WHERE c.id is null 
";

//resuming Quiz
if(count($arr_id_soal)) $left_join_where = "WHERE $values_id_soals ";

$s = "SELECT 
a.id as id_soal,
a.id_pembuat,
a.opsies,
a.durasi,
a.jawaban,
a.kalimat_soal,
a.id_status,
b.nama as pembuat_soal,  
b.username as username_pembuat,
(SELECT status FROM tb_status_soal WHERE id=a.id_status) status_soal   

FROM tb_soal_pg a 
JOIN tb_peserta b ON a.id_pembuat=b.id 
$left_join_where 
AND a.id_pembuat!=$id_peserta 
AND (a.id_status is null OR a.id_status >= 0) 
ORDER BY rand() 
LIMIT 10
";
// die($s);

$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_soal = mysqli_num_rows($q);

// autosave tb_perang if loaded
// zzz here
if($jumlah_soal){
  // echo div_alert('info', "Terdapat $jumlah_soal soal yang dapat kamu jawab.");
  $blok_soal = '';
  $i=0;
  $rand_opsies = [];
  $values = '';
  while($d=mysqli_fetch_assoc($q)){
    $i++;
    $id_soal=$d['id_soal'];
    $id_pembuat=$d['id_pembuat'];
    $id_status=$d['id_status'] ?? 0;
    $jawaban=$d['jawaban'];
    $username_pembuat=$d['username_pembuat'];

    $durasi=$d['durasi'] ?? $durasi_default;
    $values .= "($id_soal,$id_peserta,$id_pembuat),";

    $id_soals.="$id_soal,";

    // salting md5 jawabans
    $jawabans_md5.= md5($id_soal).',';
    $jawabans_md5.= md5($id_pembuat).',';
    $jawabans_md5.= md5("$id_soal$jawaban").',';

    $ropsies = explode('~~~',$d['opsies']);

    $randpos = $arr_randpos[rand(0,15)];
    for ($j=0; $j < 4; $j++) $rand_opsies[$j] = $ropsies[substr($randpos,$j,1)];

    $opsies = '';
    foreach ($rand_opsies as $key => $value) {
      if($value==$d['jawaban']) $kj = strtoupper($abjad[$key]);
      $id_soal__abjad = $id_soal."__$abjad[$key]";
      $opsies.= "<div class='opsi opsi__$id_soal unclicked' id=opsi__$id_soal__abjad>$abjad[$key]. $value</div>";
    }

    $status_soal = $d['status_soal']=='' ? '<span class=darkred>unverified</span>' : "<span class='hijau tebal'>$d[status_soal]</span>";

    $profil_pembuat = "<img src='assets/img/peserta/wars/peserta-$id_pembuat.jpg' class=profil_pembuat id=profil_pembuat__$id_soal>";




    # =====================================================================
    # DIV SOAL
    # =====================================================================
    $blok_soal .= "
      <div class='wadah blok_soal hideit' id=blok_soal__$id_soal>
        <div style='display:grid; grid-template-columns:auto 60px'>
          <div>
            <div>$i <span class='kecil miring abu'>of $jumlah_soal</span></div>
            <div class='kecil miring abu mb2'>by: $d[pembuat_soal] ~ $status_soal question</div>
          </div>
          <div>
            <img src='assets/img/peserta/wars/peserta-$id_pembuat.jpg' class=profil_pembuat id=profil_pembuat__$id_soal>
          </div>
        </div>
        <div class='darkblue mt2 mb2'>$d[kalimat_soal]</div>
        <div class=>$opsies</div>
        <div class=debug>
          cidnj__$id_soal: <span id=cidnj__$id_soal></span><br>
          username_pembuat__$id_soal: <span id=username_pembuat__$id_soal>$username_pembuat</span><br>
          durasi__$id_soal: <span id=durasi__$id_soal>$durasi</span><br>
          id_status__$id_soal: <span id=id_status__$id_soal>$id_status</span><br>
          id_pembuat__$id_soal: <span id=id_pembuat__$id_soal>$id_pembuat</span><br>
        </div>
      </div>
    ";




    # =====================================================================
    # ROWS HASIL KUIS
    # =====================================================================
    $r = rand(1,12);
    $rows_hasil.= "
      <tr id=row_hasil__$id_soal>
        <td class='tengah'>
          $profil_penjawab
          <br>$username
          <br>
          <span class='kecil miring abu'><span id=poin_penjawab__$id_soal>???</span> LP</span>
        </td>
        <td class='tengah' width=100px valign=middle><img src='assets/img/guns/wp$r.png' width=100px id=weapon__$id_soal></td>
        <td class='tengah'>
          $profil_pembuat
          <br>$username_pembuat
          <br>
          <span class='kecil miring abu'><span id=poin_pembuat__$id_soal>???</span> LP</span>
        </td>
      </tr>
    ";
  }
  # END WHILE 
  # =====================================================================




  # =====================================================================
  # BLOK PEMBAHASAN
  # =====================================================================
  $blok_pembahasan = "
    <div class='wadah hideit' id=blok_pembahasan>
      Checking answer...
      <div class='tengah btop mt2' id=blok_kamu_benar>
        <div class='blue f30px mt2 mb2' id=kamu_benar>Kamu ???!!</div>
        <div class='btop pt2'>
          <table width=100%>
            <tr>
              <td class='tengah'>
                $profil_penjawab
                <br><span id=username_penjawab>$username</span>
                <br><span class='miring kecil abu'><span id=poin_penjawab>100</span> LP</span>
              </td>
              <td width=100px class='tengah'><img class='senjata' src='assets/img/guns/wp10.png' id=weapon width=100px></td>
              <td class='tengah'>
                <img src='assets/img/no_profil.jpg' class=profil_pembuat id=profil_pembuat>
                <br><span id=username_pembuat>???</span>
                <br><span class='miring kecil abu'><span id=poin_pembuat>8</span> LP</span>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  ";



  # =====================================================================
  # DEBUGGING
  # =====================================================================
  $blok_debug = "
    <div class=debug>
      id_peserta:<span id=id_peserta>$id_peserta</span><br>
      durasi:<span id=durasi>$durasi</span><br>
      cid_soal:<span id=cid_soal></span><br>
      cjawaban:<span id=cjawaban></span><br>
      cusername_pembuat:<span id=cusername_pembuat></span><br>
      cindex:<span id=cindex></span> <span class='miring abu'>index of array rid_soal</span><br>
      jawabans_md5:<span id=jawabans_md5>$jawabans_md5</span><br>
      jumlah_soal:<span id=jumlah_soal>$jumlah_soal</span><br>
      timer_on:<span id=timer_on>1</span><br>
    </div>
  ";


  # =====================================================================
  # BLOK REJECT + ALASAN
  # =====================================================================
  $blok_reject = "
    <div class='wadah hideit mt2' id='blok_reject' style='border: solid 1px red'>
      Alasan:
      $alasan_reject
      <button class='btn btn-danger btn-block' id=btn_konfirmasi_reject disabled>Konfirmasi Reject</button>
    </div>
  ";


  # =====================================================================
  # TIMER + NAVIGASI SUBMIT
  # =====================================================================
  $blok_timer_nav = "
    <div id=blok_timer_nav>
      <div class='tengah consolas blok_timer' id= >
        <span id=timer_detik>30</span>:<span id=timer_milidetik>00</span>
      </div>

      <table width=100%>
        <tr>
          <td width=25%>
            <button class='btn btn-danger btn-block' id=btn_reject>Reject</button>
          </td>
          <td>
            <button class='btn btn-primary btn-block' id=btn_submit  disabled>Submit</button>
          </td>
          <td width=25%>
            <button class='btn btn-warning btn-block' id=btn_next  disabled>Next</button>
          </td>
        </tr>
      </table>

      <div id=reject_info class='hideit kecil miring abu mt1'>)* this Question is verified</div>
    </div>
  ";


  # =====================================================================
  # NAVIGASI SOAL AKHIR
  # =====================================================================
  $blok_navigasi_soal = "
    <div class=' hideit mb2' id=blok_navigasi_soal>
      <table width=100%>
        <tr>
          <td width=50%>
            <button class='btn btn-info btn-block btn_nav_soal' id=prev__id_soal>Prev</button>
          </td>
          <td width=50%>
            <button class='btn btn-info btn-block btn_nav_soal' id=next__id_soal>Next</button>
          </td>
        </tr>
      </table>
    </div>
  ";


  # =====================================================================
  # BLOK HASIL KUIS
  # =====================================================================
  $blok_hasil = "
    <div class='wadah gradasi-hijau hideit' id=blok_hasil>
      <div id=tabel_hasil>
        <div class='tengah darkblue f30px'>Hasil Kuis</div>
        <hr>
        <table class=table>
          $rows_hasil
        </table>
      </div>  
      <div class='tengah pb2'>
        <span class='btn btn-secondary btn-sm ' id=btn_lihat_kembali>Lihat Kembali Pembahasan Soal</span>
      </div>
      <div class='btop tengah pt2'>
        <div class=darkblue>Total Poin Penjawab</div>
        <div>
          <span id=total_poin_penjawab class='biru f30px'>???</span> LP
        </div>
      </div>

      <form method=post>
        <button class='btn btn-primary btn-block' name=btn_accept_points>Accept Points</button>
        <div class=debug>
          <span class=debug>cidnjpp</span>
          <textarea class='debug form-control' name=cidnjpp id=cidnjpp></textarea>
        </div>
      </form>
    </div> 
  ";


  # =====================================================================
  # SAVE TO PAKET WAR IF NONE
  # =====================================================================
  if(!count($arr_id_soal)){
    $s = "INSERT INTO tb_paket_war (id_peserta,id_soals) VALUES ($id_peserta,'$id_soals')";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));


    # =====================================================================
    # AUTO INSERT TB_PERANG
    # =====================================================================
    $s = "INSERT INTO tb_perang (id_soal,id_penjawab,id_pembuat) VALUES $values";
    $s .= '__';
    $s = str_replace(',__','',$s);
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  }




  # =====================================================================
  # FINAL OUTPUT
  # =====================================================================
  echo "
    <span class=debug>id_soals<span id=id_soals>$id_soals</span></span>
    <div id=blok_kuis>
      $blok_soal
      $blok_pembahasan
      $blok_timer_nav
      $blok_reject
      $blok_navigasi_soal
      $blok_debug
    </div>
    $blok_hasil
  ";
}else{
  echo div_alert('danger', "Maaf tidak ada soal yang tersedia untuk kamu.");
}



































?>
<script>
  $(function(){
    // ===========================================================
    // DEFAULT VARIABLES
    // ===========================================================
    let id_peserta = $('#id_peserta').text();
    let jumlah_soal = parseInt($('#jumlah_soal').text());
    let cindex = 0; // current index soal | soal ke-0
    let milidetik=0;
    let durasi_show = '';
    let milidetik_show = '';
    let jawaban = '';
    let cjawaban = '';
    let cidnj = ''; // current id_soal + id_peserta + jawaban
    let cidnjpp = 'initial value'; // current id_soal + id_peserta + jawaban + pp
    let arr_idnj = []; //id_soal + jawaban
    let arr_idnjpp = []; //id + jawaban + poin penjawab + poin pembuat
    let menjawab_benar = false;
    let abjad = ['a','b','c','d'];
    let img_check = '<img src="assets/img/icons/check.png" alt="ok" height="20px" />';
    let img_reject = '<img src="assets/img/icons/reject.png" alt="ok" height="20px" />';
    let timer_on = '1';
    let cid_status = '0'; // reject available
    let time_bonus = 0;
    let poin_penjawab = 0;
    let poin_pembuat = 0;
    let total_poin_penjawab = 0;
    let is_rejected = false;
    let id_alasan = 0;
    let cid_pembuat = '';
    let is_benar = 0;


    // get from database + salts
    let jawabans_md5 = $('#jawabans_md5').text();
    for (let i = 0; i < 100; i++) {
      jawabans_md5 += md5('x'+i) + ',';
    }
    let rjawabans_md5 = jawabans_md5.split(',');
    $('#jawabans_md5').text('cleared'); // clear it!

    // id_soal handler
    let rid_soal = $('#id_soals').text().split(',');
    let cid_soal = rid_soal[0];
    let durasi = parseInt($('#durasi__'+cid_soal).text());
    let cdurasi = durasi;

    // tampilkan soal pertama
    $('#blok_soal__'+rid_soal[0]).show();

    // id_soal current
    $('#cid_soal').text(rid_soal[0]);
    
    // index soal
    $('#cindex').text(0);






    // ===========================================================
    // FUNCTIONS
    // ===========================================================
    function resetUI(){
      $('.blok_soal').hide();
      $('#blok_soal__'+cid_soal).show();
      cid_status = $('#id_status__'+cid_soal).text();
      cid_pembuat = $('#id_pembuat__'+cid_soal).text();
      $('#cid_soal').text(cid_soal);

      // reset blok reject
      is_rejected = false;
      $('#blok_reject').hide();
      $('.opsi_reject').removeClass('clicked_reject');
      $('#btn_konfirmasi_reject').prop('disabled',true);

      //reset blok pembahasan
      $('#blok_pembahasan').removeClass('gradasi-hijau');
      $('#blok_pembahasan').removeClass('gradasi-merah');
      $('#blok_pembahasan').removeClass('gradasi-kuning');

      if(parseInt(cid_status)>0){
        // tidak dapat reject verified soal
        $('#btn_reject').prop('disabled',true);
        $('#reject_info').show();
      }else{
        $('#reject_info').hide();
        $('#btn_reject').prop('disabled',false);
      }
      

      
      
    }
    resetUI();







    // durasi = 3; //zzz debug
    // ===========================================================
    // TIMER
    // ===========================================================
    let timer = setInterval(() => {
      if(durasi>0 && $('#timer_on').text()=='1'){
        if(milidetik==0){
          durasi--;
          milidetik=20;
          durasi_show = ('0'+durasi).slice(-2)
          $('#timer_detik').text(durasi_show);
        }else{
          milidetik--;
          milidetik_show = ('0'+milidetik).slice(-2)
          $('#timer_milidetik').text(milidetik_show);
        }
        time_bonus = Math.round(durasi*15/cdurasi,0); //zzz hard code
      }else{
        // clearInterval(timer);
        if($('#timer_on').text()=='0'){
          // do nothing
        }else{
          // set paused UI
          $('#btn_submit').prop('disabled',true);
          $('#btn_next').prop('disabled',false);
          timer_on = 0; $('#timer_on').text(timer_on);
          cidnj = cid_soal + '~~' + id_peserta + '~~NULL'; 
          $('#cidnj__'+cid_soal).text(cidnj);
          cjawaban = 'NULL'; $('#cjawaban').text(cjawaban);
          $('#btn_submit').click();
        }
      }
    }, 50);





    // ===========================================================
    // CLICK HANDLER
    // ===========================================================
    $('.opsi').click(function(){
      if(durasi==0 || timer_on=='0'){
        return;
      }else{
        let tid = $(this).prop('id');
        let rid = tid.split('__');
        let id_soal = rid[1];
        jawaban = $(this).text().slice(3);
        cjawaban = jawaban;
        cidnj = id_soal+'~~'+id_peserta+'~~'+jawaban;
        arr_idnj[id_soal] = cidnj;
        let username_pembuat = $('#username_pembuat__'+id_soal).text();

        // reupdate data
        $('#cidnj__'+id_soal).text(cidnj);
        $('#cid_soal').text(id_soal);
        $('#cjawaban').text(jawaban);
        $('#cusername_pembuat').text(username_pembuat);

        // at blok_pembahasan | sudah terwakili on-submit
        // $('#username_pembuat').text(username_pembuat);

        // reupdate UI
        $('.opsi__'+id_soal).removeClass('unclicked');
        $('.opsi__'+id_soal).removeClass('clicked');
        $(this).addClass('clicked');
        $('#btn_submit').prop('disabled',false);


      }
    })







    // ===================================================
    // REJECT SYSTEM
    // ===================================================
    $('.opsi_reject').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      id_alasan = rid[1];
      
      $('.opsi_reject').removeClass('unclicked_reject');
      $('.opsi_reject').removeClass('clicked_reject');
      $(this).addClass('clicked_reject');
      $('#btn_konfirmasi_reject').prop('disabled',false);
    })

    $('#btn_reject').click(function(){ $('#blok_reject').slideToggle()})
    $('#btn_konfirmasi_reject').click(function(){ 
      is_rejected = true;
      $('#btn_reject').prop('disabled',true);
      $('#blok_reject').slideUp();


      // update answer to null
      cidnj = cid_soal + '~~' + id_peserta + '~~NULL';

      //update opsi with img-reject
      abjad.forEach((value, index) => {
        let opsi = $('#opsi__'+cid_soal+'__'+value).text();
        $('#opsi__'+cid_soal+'__'+value).html(opsi+' '+img_reject);
      })

      // hampir sama dengan submit
      // call submit
      $('#btn_submit').click();
   
      
    })















    // ===================================================
    // ON SUBMIT || REJECTED IS TRUE
    // ===================================================
    $('#btn_submit').click(function(){ 
      // stop timer
      timer_on = 0;
      $('#timer_on').text(timer_on);

      let id_soal = $('#cid_soal').text();
      cid_pembuat = $('#id_pembuat__'+id_soal).text();
      let jawaban = $('#cjawaban').text();
      $('#blok_pembahasan').slideDown();
      $('#profil_pembuat').prop('src',$('#profil_pembuat__'+id_soal).prop('src'));
      $('#username_pembuat').text($('#username_pembuat__'+id_soal).text());
      $('#btn_submit').prop('disabled',true);
      $('#btn_next').prop('disabled',false);

      if(is_rejected){
        is_benar = -1;
        $('#blok_pembahasan').addClass('gradasi-kuning');
        $('#weapon').removeClass('cermin');
        $('#kamu_benar').removeClass('blue');
        $('#kamu_benar').addClass('red');
        $('#kamu_benar').text('Kamu Mereject!!');
        poin_penjawab = 0; // 
        poin_pembuat = 0; // 

        // weapon reject red at pembahasan
        $('#weapon').prop('src','assets/img/guns/wp0.png');
        // weapon at hasil akhir
        $('#weapon__'+id_soal).prop('src','assets/img/guns/wp0.png');
        // update bg yellow
        $('#row_hasil__'+id_soal).addClass('gradasi-kuning');

      }else{

        // random weapon
        let r = parseInt(Math.random()*12)+1;
        $('#weapon').prop('src','assets/img/guns/wp'+r+'.png');

        // reset classes
        $('#kamu_benar').removeClass('blue');
        $('#kamu_benar').removeClass('red');
        $('#weapon').removeClass('cermin');

        console.log(cjawaban);

        if(cjawaban=='NULL'){
          // tidak menjawab
          is_benar = -2;
          $('#kamu_benar').text('Timed Out!!');
          $('#weapon').addClass('cermin');
          $('#kamu_benar').addClass('red');
          $('#blok_pembahasan').addClass('gradasi-merah');
          
          // update UI Hasil
          $('#weapon__'+id_soal).addClass('cermin');
          $('#row_hasil__'+id_soal).addClass('gradasi-merah');

          poin_penjawab = 0; 
          poin_pembuat = 8; // zzz hard code

        }else{

          // tidak mereject :: Jawab Benar || Salah
          if(rjawabans_md5.includes(md5(id_soal+jawaban))){
            //
            $('#blok_pembahasan').addClass('gradasi-hijau');
            $('#kamu_benar').addClass('blue');
            $('#kamu_benar').text('Kamu Benar!!');
            poin_penjawab = 100; // zzz hard code
            poin_pembuat = 8; // 
            is_benar = 1;
          }else{
            is_benar = 0;
            poin_penjawab = 20; // zzz hard code
            poin_pembuat = 12; // 
            $('#kamu_benar').text('Kamu Salah!!');
            $('#weapon').addClass('cermin');
            $('#kamu_benar').addClass('red');
            $('#blok_pembahasan').addClass('gradasi-merah');
            
            // update UI Hasil
            $('#weapon__'+id_soal).addClass('cermin');
            $('#row_hasil__'+id_soal).addClass('gradasi-merah');
          } // end if jawaban salah
        }


        // pencarian yang benar
        abjad.forEach((value, index) => {
          let opsi = $('#opsi__'+id_soal+'__'+value).text().slice(3);
          if(rjawabans_md5.includes(md5(id_soal+opsi))){
            $('#opsi__'+id_soal+'__'+value).html(value+'. '+opsi+' '+img_check);
          }
        })
      } // end if tidak me-reject


      let poin_jawab = time_bonus + poin_penjawab;
      if(is_rejected){
        is_benar = -1;
        poin_jawab = 0;
        poin_pembuat = 0;
      }

      // update poin penjawab | pembuat
      total_poin_penjawab += poin_jawab;
      $('#poin_penjawab').text(poin_jawab);
      $('#poin_pembuat').text(poin_pembuat);
      $('#poin_penjawab__'+id_soal).text(poin_jawab);
      $('#poin_pembuat__'+id_soal).text(poin_pembuat);
      $('#total_poin_penjawab').text(total_poin_penjawab);

      // update form data
      let d = new Date();
      let saat_ini = d.getFullYear()
        +'-'+(d.getMonth()+1)
        +'-'+d.getDate()
        +' '+d.getHours()
        +':'+d.getMinutes()
        +':'+d.getSeconds();
      arr_idnjpp[id_soal] = cidnj 
        + '~~' + is_benar 
        + '~~' + poin_jawab 
        + '~~' + poin_pembuat 
        + '~~' + cid_pembuat
        + '~~' + saat_ini;
      // store ids and jawabans
      cidnjpp = '';
      arr_idnjpp.forEach(e => {
        cidnjpp+=e+'~~~';
      });

      let encrypted = CryptoJS.AES.encrypt(cidnjpp, "DIPA Joiner");
      $('#cidnjpp').val(encrypted);
      

      
      // for server
      // let decrypted = CryptoJS.AES.decrypt(encrypted, "DIPA Joiner");
      // $('#cidnjpp').val(decrypted.toString(CryptoJS.enc.Utf8));


    })


    // ===================================================
    // NEXT PLAY
    // ===================================================
    $('#btn_next').click(function(){
      $('#btn_next').prop('disabled',true);
      if(jumlah_soal==1 || cindex+1 == jumlah_soal){
        // go to hasil quiz
        // console.log('go to hasil quiz');
        clearInterval(timer);
        $('#blok_reject').hide();
        $('#blok_pembahasan').slideUp();
        $('#blok_timer_nav').slideUp();
        $('#blok_navigasi_soal').show();
        $('#blok_kuis').slideUp();
        $('#blok_hasil').slideDown();


      }else{
        // next soal
        cindex++;
        cid_soal = rid_soal[cindex];

        // reset UI
        $('#blok_pembahasan').slideUp();
        durasi = $('#durasi__'+cid_soal).text();
        cdurasi = durasi;
        $('#timer_detik').text(durasi);
        timer_on = 1;
        $('#timer_on').text(timer_on);
        resetUI();
      }

    })

    $('#btn_lihat_kembali').click(function(){
      $('#blok_kuis').slideToggle();
      $('#tabel_hasil').slideToggle();
      if($(this).text()=='Lihat Kembali Pembahasan Soal'){
        $(this).text('Back to Hasil Kuis');
      }else{
        $(this).text('Lihat Kembali Pembahasan Soal');
      }
    })

    $('.btn_nav_soal').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];

      if(aksi=='prev' && cindex > 0){
        cindex--;
      }else if(aksi=='next' && cindex < (jumlah_soal-1)){
        cindex++;
      }

      if(cindex==0){
        $('#prev__id_soal').prop('disabled',true);
      }else{
        $('#prev__id_soal').prop('disabled',false);
      }

      if(cindex>=(jumlah_soal-1)){
        $('#next__id_soal').prop('disabled',true);
      }else{
        $('#next__id_soal').prop('disabled',false);
      }

      cid_soal = rid_soal[cindex];
      $('#cindex').text(cindex);
      $('#cid_soal').text(cid_soal);
      $('.blok_soal').hide();
      $('#blok_soal__'+cid_soal).fadeIn();
    })





  })
</script>