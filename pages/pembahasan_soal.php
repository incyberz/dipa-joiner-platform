<style>
  .wadah2 {
    padding:10px; border: solid 1px #ddd; border-radius: 5px; margin:10px 0
  }
</style>
<section>
  <div class=container>
    <div class="section-title" data-aos="fade">
      <h2>Pembahasan Soal</h2>
      <p>Berikut adalah pembahasan soal, silahkan pelajari baik-baik karena sebagian besar akan muncul kembali di ujian berikutnya.</p>
    </div>        
<!-- ============================================================== -->
<?php
$id_paket_soal = $_GET['id_paket_soal'] ?? '';

# =================================================
# GET PROPERTI PAKET SOAL
# =================================================
$s = "SELECT a.* FROM tb_paket_soal a WHERE a.id=$id_paket_soal and a.kelas='$kelas'";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0) die(div_alert('danger','Data tidak ditemukan'));

$d = mysqli_fetch_assoc($q);

// $awal_ujian = $d['awal_ujian'];
$akhir_ujian = $d['akhir_ujian'];
$tanggal_pembahasan = $d['tanggal_pembahasan'];
$akhir_ujian_show = date('d M Y H:i', strtotime($akhir_ujian));
$tanggal_pembahasan_show = date('d M Y H:i', strtotime($tanggal_pembahasan));

if((strtotime($tanggal_pembahasan)-strtotime('now')) >= 0){
  echo div_alert('danger',"Akses ditolak. <div class='kecil miring abu'>Pembahasan soal akan muncul pada tanggal $tanggal_pembahasan_show</div>");
}else{
  if((strtotime($tanggal_pembahasan)-strtotime($akhir_ujian)) < 0){
    echo div_alert('danger',"Tanggal Pembahasan invalid. <div class='kecil miring abu'>Tanggal Pembahasan lebih awal dari tanggal akhir ujian $akhir_ujian_show. Pembahasan akan tetap ditampilkan setelah tanggal ujian tersebut berakhir.</div>");
  }else{
    # =================================================
    # PRASYARAT PEMBAHASAN
    # =================================================
    $sudah_polling = 0;
    $s = "SELECT 1 FROM tb_jawaban_polling WHERE id=$id_peserta";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
    if(mysqli_num_rows($q)) $sudah_polling=1;


    if(!$sudah_polling){
      $dari = urlencode("?pembahasan_soal&id_paket_soal=$id_paket_soal");
      echo div_alert('info', "Untuk mengakses Fitur Pembahasan Soal, silahkan kamu mengisi dahulu Polling dan Kuesioner ya!! Agar web DIPA ini semakin baik. <hr><a class='btn btn-primary btn-sm btn-block' href='?polling_uts&dari=$dari'>Polling UTS</a>");
    }else{
      if($profil_ok==-1){
        echo div_alert('danger', 'Wah maaf! Silahkan ganti profil dulu dengan yang baik. | <a href="?verifikasi_profil_peserta">Status Profile</a>');
      }elseif($profil_ok=='' || $profil_ok==0){
        echo div_alert('danger', 'Sepertinya profil kamu belum diverifikasi oleh instruktur. Sabar ya! Atau silahkan japri ke beliau via Whatsapp.');
      }else{
        # =================================================
        # READY TO PEMBAHASAN
        # =================================================
        $img_check = '<img src="assets/img/icons/check.png" height=25px />';
        $img_reject = '<img src="assets/img/icons/reject.png" height=25px />';
        echo div_alert('info', 'Perhatikan dan ingat baik-baik Pembahasan Soal berikut! Sebagian besar akan muncul di ujian berikutnya.');

        $s = "SELECT * FROM tb_jawabans a 
        JOIN tb_paket_soal b ON a.id_paket_soal=b.id 
        WHERE a.id_paket_soal=$id_paket_soal 
        AND a.id_peserta=$id_peserta";
        $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
        if(mysqli_num_rows($q)==0) die(div_alert('danger','Data Jawaban tidak ditemukan'));
        $d=mysqli_fetch_assoc($q);
        $jawabans = $d['jawabans'];
        $tmp_jawabans = $d['tmp_jawabans'];

        // echo "AN:$jawabans <br> KJ:$tmp_jawabans"; ///zzz debug
        $ranswer = [];
        $rjawabans = explode('|',$jawabans);
        foreach ($rjawabans as $id_n_answer) {
          if(strlen($id_n_answer)>2){
            $rid_n_answer = explode('__',$id_n_answer);
            $ranswer[$rid_n_answer[0]] = $rid_n_answer[1];
          }
        }

        $rkj = [];
        $rtmp_jawabans = explode('|',$tmp_jawabans);
        foreach ($rtmp_jawabans as $id_n_kj) {
          if(strlen($id_n_kj)>2){
            $rid_n_kj = explode('__',$id_n_kj);
            $rkj[$rid_n_kj[0]] = $rid_n_kj[1];
          }
        }

        // echo '<pre>';
        // var_dump($rkj);
        // echo '</pre>';

        $s2 = "SELECT a.id as id_assign_soal,
        b.soal,
        b.pembahasan  
        FROM tb_assign_soal a 
        JOIN tb_soal b ON a.id_soal=b.id  
        WHERE a.id_paket_soal=$id_paket_soal";
        $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));
        $soals = '';
        $i=0;
        while($d2=mysqli_fetch_assoc($q2)){
          $i++;
          $id=$d2['id_assign_soal'];
          $kj = $rkj[$id];
          $jawaban = $ranswer[$id];
          $icon = $jawaban == $kj ? $img_check : $img_reject;
          $pembahasan = strlen($d2['pembahasan'])>5 ? $d2['pembahasan'] : '<span class="kecil miring abu">(tidak ada)</span>';

          $soals.= "
          <div class='wadah gradasi-hijau'>
            <div style='display:grid;grid-template-columns:20px auto; gap: 5px'>
              <div>$i.</div>
              <div>
                <div>$d2[soal] <span class=debug>$id</span></div>
                <div class='wadah2 bg-white'>
                  <div class=row> 
                    <div class='col-md-2 green'><span class='miring abu'>KJ:</span> $kj</div>
                    <div class=col-md-3><span class='miring abu'>Jawabanmu:</span> $jawaban $icon</div> 
                    <div class=col-md-7><span class='miring abu'>Pembahasan:</span> $pembahasan</div> 
                  </div>
                </div>
              </div>
            </div>
          </div>
          ";


        }

        echo $soals;


  
      }
    }
  }
}



?>
<!-- ============================================================== -->
  </div>
</section>