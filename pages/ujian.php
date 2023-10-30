<style>
  .div_soal{border-top: solid 2px #ddd; padding: 10px;}
  .belum_dijawab{background: linear-gradient(#fee,#fcc)}
  .no_dan_soal{display: grid; grid-template-columns: 20px auto; grid-gap:5px}
</style>
<?php
$debug = '';
if(!$is_login) die('<script>location.replace("?")</script>');
$id_paket_soal=$_GET['id_paket_soal'] ?? '';







# =======================================================
# PAKET SOAL YANG TERSEDIA
# =======================================================
if($id_paket_soal==''){
  $s = "SELECT a.*,
  (
    SELECT COUNT(1) FROM tb_jawabans WHERE id_peserta=$id_peserta AND id_paket_soal=a.id) jumlah_attemp  
  FROM tb_paket_soal a 
  WHERE a.kelas='$kelas'";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $list_paket = div_alert('danger', "Maaf, belum ada Paket Soal untuk kelas $kelas.");
  if(mysqli_num_rows($q)){
    $list_paket = '';
    while ($d=mysqli_fetch_assoc($q)) {

      $jumlah_attemp = $d['jumlah_attemp'] ?? 0;
      $max_attemp = $d['max_attemp'] ?? 999;

      // $d['awal_ujian'] = '2023-10-29 9:00'; //debug
      // $d['akhir_ujian'] = '2023-10-29 10:00'; //debug

      $selisih = strtotime($d['awal_ujian']) - strtotime('now');
      $selisih_akhir = strtotime($d['akhir_ujian']) - strtotime('now');
      $selisih_hari = (strtotime(date('Y-m-d',strtotime($d['awal_ujian']))) - strtotime('today')) / (3600*24);


      $awal_ujian_show = $nama_hari[date('w',strtotime($d['awal_ujian']))].', '.date('d M Y H:i', strtotime($d['awal_ujian']));
      $akhir_ujian_show = date('H:i', strtotime($d['akhir_ujian']));

      $nama_paket_show = "
      $d[nama]
      <br>$awal_ujian_show s.d $akhir_ujian_show 
      ";

      if($selisih_akhir<0){
        // sudah berakhir
        if($jumlah_attemp==0){
          $list_paket .= "<div><span class='btn btn-secondary btn-block' onclick='alert(\"Maaf, Paket Soal ini sudah berakhir.\")'>$nama_paket_show<br>Sudah berakhir dan kamu tidak bisa mengikutinya kembali</span></div>";
        }else{
          $list_paket .= "<div><a href='?ujian&id_paket_soal=$d[id]' class='btn btn-secondary btn-block' >$nama_paket_show<br>Sudah berakhir dan kamu boleh melihat hasilnya</a></div>";
        }


      }elseif($selisih_akhir >= 0 AND $selisih <= 0){
        // sedang berlangsung
        if($jumlah_attemp==0){
          $info = 'Kamu belum mencobanya';
        }elseif($jumlah_attemp>=$max_attemp){
          $info = 'Kamu sudah mencapai max_attemp';
        }else{
          $info = "Kamu sudah $jumlah_attemp kali mencoba. Coba lagi!";
        }
        $list_paket .= "<div><a class='btn btn-primary btn-block' href='?ujian&id_paket_soal=$d[id]'>$nama_paket_show<br>Sedang berlangsung<br>$info</a></div>";

      }else{
        // belum berlangsung
        if($selisih_hari>0){
          $eta_info = "$selisih_hari hari lagi";
        }else{
          $eta_info = "$selisih detik lagi";
          if($selisih>60) $eta_info = number_format($selisih/60,0) . ' menit lagi';
          if($selisih>60*60) $eta_info = number_format($selisih/(60*60),0). ' jam lagi';
        }


        if($jumlah_attemp==0){
          $attemp_info = 'Kamu belum pernah mencoba Ujian ini' ;
        }else{
          if($jumlah_attemp<$max_attemp){
            $attemp_info = "Kamu sudah $jumlah_attemp kali mencoba dan boleh mencobanya kembali";
          }else{
            $attemp_info = "Kamu telah mencapai max_attemp (batasan mencoba ujian)";
          }
        }

        # ===================================================
        # FINAL OUTPUT LIST PAKET
        # ===================================================
        $list_paket .= "
        <div class=mb2>
          <a class='btn btn-info btn-block ' href='?ujian&id_paket_soal=$d[id]'>
            $nama_paket_show <span class='kecil miring'> ~ $eta_info</span>
            <br>$attemp_info
          </a>
        </div>";

      }

    }

    $rand = rand(1,9);
  
    echo "
    <section>
      <div class=container>
        <div class='mb2 darkblue bold'>Silahkan pilih Paket Soal yang tersedia:</div> 
        $list_paket
        <hr>
        <div class='tengah' style='max-width: 300px; margin: auto'><img  class='img-fluid img-thumbnail' src='assets/img/meme/funny-$rand.jpg'></div>
      </div>
    </section>";
    
  }
}else{
  include 'ujian_pre_show.php';
}