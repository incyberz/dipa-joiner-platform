<?php
$judul = "Ujian Pre Show";
set_title($judul);


$list_soal = '';
$form_submit = '';
$countdown = '';
$fitur_dosen = '';
$start = $_GET['start'] ?? '';
$free_access = false;
$Ujian = 'Ujian';



# =======================================================
# FITUR INSTRUKTUR
# =======================================================
if ($id_role == 2) {
  $fitur_dosen = "
  <div class='wadah gradasi-merah mt2'>
    Fitur $Trainer: <a href='?monitoring_ujian&id_paket=$id_paket'>Monitoring $Ujian</a>
  </div>
  ";
}


# =======================================================
# GET PROPERTIES PAKET UJIAN
# =======================================================
// untuk $Trainer tampilkan walaupun berbeda kelas
$sql_kelas = $id_role == 2 ? '1' : "d.kelas='$kelas'";
$s = "SELECT 
a.*,
b.nama as pembuat,
c.nama as nama_sesi,
d.awal_ujian,
(
  SELECT COUNT(1) FROM tb_jawabans p 
  JOIN tb_paket_kelas q ON p.paket_kelas=q.paket_kelas 
  WHERE q.id_paket=a.id 
  AND p.id_peserta=$id_peserta)  jumlah_attemp,  
(
  SELECT COUNT(1) FROM tb_assign_soal WHERE id_paket=a.id)  jumlah_soal  
FROM tb_paket a 
JOIN tb_peserta b ON a.id_pembuat=b.id  
JOIN tb_sesi c ON a.id_sesi=c.id 
JOIN tb_paket_kelas d ON a.id=d.id_paket   
WHERE a.id=$id_paket 
AND $sql_kelas";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) die("Data Paket Soal tidak ditemukan.");
$d = mysqli_fetch_assoc($q);
$nama_paket_soal = $d['nama'];
$awal_ujian = $d['awal_ujian'];
$akhir_ujian = date('Y-m-d H:i:s', strtotime($d['awal_ujian']) + 60 * $d['durasi_ujian']);
$tanggal_pembahasan = $d['tanggal_pembahasan'];
$is_locked = $d['is_locked'];
$tmp_jawabans = $d['tmp_jawabans'];
$tmp_jumlah_soal = $d['tmp_jumlah_soal'];
$nama_sesi = $d['nama_sesi'];
$id_sesi = $d['id_sesi'];
$sifat_ujian = $d['sifat_ujian'] ?? 'Close Book';
$pembuat = $d['pembuat'] ?? '-';
$kisi_kisi = $d['kisi_kisi'] ?? '-';
$jumlah_soal = $d['jumlah_soal'];
if ($jumlah_soal == 0) die('<section>' . div_alert('danger', "Maaf, belum ada soal untuk Paket Soal ini. | id: $id_paket"));
$max_attemp = $d['max_attemp'];
$jumlah_attemp = $d['jumlah_attemp'];

$debug .= "<br>max_attemp:<span id=max_attemp>$max_attemp</span>";
$debug .= "<br>jumlah_attemp:<span id=jumlah_attemp>$jumlah_attemp</span>";

// $awal_ujian = '2023-10-29 11:00'; //  debug
// $akhir_ujian = '2023-10-29 16:43'; //  debug




# =======================================================
# SUBMIT HANDLER
# =======================================================
if (isset($_POST['btn_submit_jawaban_ujian'])) {
  $jawabans = $_POST['jawabans'];
  $jawabans .= '___';
  $jawabans = str_replace('|___', '', $jawabans);

  $arr_jawaban = explode('|', $jawabans);
  $arr_kj = explode('|', $tmp_jawabans);

  $jumlah_benar = 0;
  foreach ($arr_jawaban as $jawaban) {
    if (in_array($jawaban, $arr_kj)) {
      $jumlah_benar++;
    }
  }

  $nilai = round($jumlah_benar / $tmp_jumlah_soal * 100, 0);

  $paket_kelas = $id_paket . "__$kelas";
  $jawabans = str_replace('\'', '`', $jawabans);
  $s = "INSERT INTO tb_jawabans 
  (
    id_room,
    id_peserta,
    paket_kelas,
    nilai,
    jawabans,
    jumlah_benar,
    tanggal_start
  ) VALUES (
    $id_room,
    $id_peserta,
    '$paket_kelas',
    $nilai,
    '$jawabans',
    $jumlah_benar,
    '$_POST[tanggal_start]'
  )";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  echo div_alert('success', 'Submit Jawaban sukses');
  echo '<script>document.cookie = "jawabans=";</script>'; //clear cookie
  if ($dm) {
    echo "<hr>location.replace skipped.<hr>$s";
    echo 'var_dump($arr_kj)<pre>';
    var_dump($arr_kj);
    echo '</pre>';
  } else {
    echo "<script>location.replace('?ujian&id_paket=$_GET[id_paket]#blok_hasil_ujian')</script>"; //redirect
  }

  exit;
}









# =======================================================
# INFO PAKET SOAL 
# =======================================================
$sub_judul = "<div>$nama_sesi untuk $kelas</div>";

$durasi = number_format((strtotime($akhir_ujian) - strtotime($awal_ujian)) / 60, 0);
$durasi_show = "<span class='kecil miring abu'>($durasi menit)</span>";

if (intval(date('Y', strtotime($awal_ujian))) > 2024) {
  $awal_ujian_show = $nama_hari[date('w', strtotime($awal_ujian))] . ', ' . date('d-M H:i', strtotime($awal_ujian));
  $akhir_ujian_show = date('H:i', strtotime($akhir_ujian));
  if ($tanggal_pembahasan) {
    $tanggal_pembahasan_show = date('d-M H:i', strtotime($tanggal_pembahasan));
    $tanggal_pembahasan_show .= "<br><span class='kecil miring abu'>Pembahasan kunci jawab akan tampil pada tanggal dan jam ini</span>";
  } else {
    $tanggal_pembahasan_show = "Tidak ada pembahasan Kunci Jawab untuk $Ujian ini.";
  }
  $tanggal_pelaksanaan = "$awal_ujian_show s.d $akhir_ujian_show $durasi_show";
} else {
  $free_access = true;
  $Ujian = 'Quiz';
  $tanggal_pelaksanaan = "<i>free-access</i> - $durasi_show";
  $tanggal_pembahasan_show = 'Tidak ada pembahasan.';
}

$kode_sesi_show = "$id_sesi | $nama_sesi";
$max_attemp_show = $max_attemp ?? '<span class="kecil miring consolas">unlimitted</span>';


$rkolom['Paket Soal'] = $nama_paket_soal;
$rkolom['Untuk Sesi'] = $kode_sesi_show;
$rkolom['Tanggal Pelaksanaan'] = $tanggal_pelaksanaan;
$rkolom["Sifat $Ujian"] = $sifat_ujian;
$rkolom['Jumlah Soal'] = $jumlah_soal;
$rkolom['Max Attemp'] = $max_attemp_show;
$rkolom['Pembuat'] = $pembuat;
$rkolom['Kisi-kisi'] = $kisi_kisi;
$rkolom['Tanggal Pembahasan'] = $tanggal_pembahasan_show;

$koloms = '';
foreach ($rkolom as $kolom => $isi) $koloms .= "
  <div class='btop pt2 mb2'>
    <div class=row>
      <div class='col-md-4 abu'>
        $kolom
      </div>
      <div class=col-md-8>
        $isi
      </div>
    </div>
  </div>
";

$info_paket_soal = $start ? '' : "
<div id=info_paket_soal class=wadah>
  <h4 class=darkblue>Info Paket Soal</h4>
  $koloms
</div>
";







# =======================================================
# TIMER HANDLER 
# =======================================================
$selisih = strtotime($awal_ujian) - strtotime('now');
$selisih_akhir = strtotime($akhir_ujian) - strtotime('now');
$selisih_hari = (strtotime(date('Y-m-d', strtotime($awal_ujian))) - strtotime('today')) / (3600 * 24);
$debug .= "<br>selisih:<span id=selisih>$selisih</span>";
$debug .= "<br>selisih_akhir:<span id=selisih_akhir>$selisih_akhir</span>";
$debug .= "<br>selisih_hari:<span id=selisih_hari>$selisih_hari</span>";
$debug .= "<br>start:<span id=start>$start</span>";

if ($selisih_hari >= 2) {
  $img = "<div class=tengah style='max-width:300px; margin:auto'><img class='img-fluid' src='assets/img/are-you-ready.png' /></div>";
  $coming_soon = "$img<h3 class=darkblue>Comming Soon !! <br>In $selisih_hari days.</h3>";
} else if ($selisih_hari == 1) {
  $img = "<div class=tengah style='max-width:300px; margin:auto'><img class='img-fluid' src='assets/img/are-you-ready.png' /></div>";
  $coming_soon = $img . '<h1 class=darkred>Ingat! Besok $Ujian</h1>';
} else {
  $rand = rand(1, 3);
  $img = "<div class='tengah' style='max-width:300px; margin:15px auto'><img class='img-fluid img-thumbnail' src='assets/img/meme/hari-ini-ujian-$rand.jpg' style='box-shadow: 0 0 20px gray' /></div>";
  $coming_soon = $img . '<h1 class=blue>Hari ini $Ujian !!</h1>';
}

$nilai_max = 0;

if ($selisih > 0 and !$free_access) { //belum mulai
  $blok_timer = "
    <h2>$coming_soon</h2>
    $Ujian akan diselenggarakan dalam:
    <div> 
      <div class='consolas darkblue' style='font-size:40px;' id=detik_lagi>00:00:00:00</div>
      <div class='darkblue hideit' style='margin-top: -10px'>detik lagi</div>
    </div>
  ";
} else { //sudah mulai | berakhir


  if ($selisih_akhir >= 0 || $free_access) { // sedang ujian
    if ($free_access) {
      $blok_timer = "
        <div class='f12 mb2 miring'>Free Access Exam</div>
        <h2 class=blue>$d[nama]</h2>
      ";
    } else {
      $blok_timer = "<h2 class=blue>$Ujian Sedang Berlangsung</h2>";
    }
  } else { // sudah berakhir
    $blok_timer = "<h2 class=darkred>$Ujian Sudah Berakhir</h2>";
  }

  if ($jumlah_attemp >= $max_attemp) {
    $blok_timer .= "<div class='darkred mb2'>Kamu sudah mencapai max_attemp ($max_attemp kali mencoba)</div>";
  } else {
    if ($start) {
      // $blok_timer = ''; // hide timer saat started
      $countdown = "
        <div class=blok-ujian-countdown>
          <span id='countdown'>00:00</span> 
          <span id=sekian_soal_lagi>
            <span id=belum_dijawab_count2>10</span>
            soal lagi
          </span>
        </div>
      ";
    }
  }


  # ==================================================
  # SHOW JAWABAN
  # ==================================================
  if ($jumlah_attemp and !$start) { // jika SUDAH ATTEMP


    $info_pembahasan = '';
    // if (!$nik) {
    //   $list_jawabans = div_alert('danger', "Sepertinya kamu belum mengisi Biodata Peserta. <a class='btn btn-primary mt2 w-100' href='?biodata'>Isi Biodata Peserta</a><hr><span class='abu kecil'>Silahkan isi dahulu agar dapat melihat hasil ujian.</span>");
    //   $info_pembahasan = '';
    // }

    if ($untuk = 'uas' && !$sudah_polling_uas and false) {
      // STOP, belum polling
      $list_jawabans = div_alert('danger', "Sepertinya kamu belum Polling.<hr><p class='biru tebal'>Silahkan isi dahulu polling agar kamu dapat melihat hasil ujian barusan.</p><hr> <a class='btn btn-primary mt2 w-100' href='?polling&u=uas'>Isi Polling</a>. <hr><span class='abu kecil'>Silahkan isi dahulu agar dapat melihat hasil ujian.</span>");
      echo "<br>untuk:<span id=untuk>$untuk</span>";
      echo "<br>sudah_polling_uas:<span id=sudah_polling_uas>$sudah_polling_uas</span>";
    } elseif (!$profil_ok and false) {
      $list_jawabans = div_alert('danger', "Sepertinya kamu belum Upload Foto Profil, atau mungkin profil kamu tidak terbaca oleh $Trainer (corrupt).<hr><p class='biru tebal'>Silahkan upload dahulu foto profilnya agar $Trainer mengetahui bahwa itu adalah kamu.</p><hr> <a class='btn btn-primary mt2 w-100' href='?upload_profil'>Upload/Reupload Profil</a>. <hr><span class='abu kecil'>Silahkan upload profil dahulu agar kamu dapat melihat hasil ujian.</span>");
    } else { // boleh lihat hasil ujian
      $s = "SELECT a.*, b.tmp_jumlah_soal,b.tanggal_pembahasan,
      (
        SELECT MAX(nilai) FROM tb_jawabans p 
        JOIN tb_paket_kelas q ON p.paket_kelas=q.paket_kelas  
        WHERE q.id_paket=$id_paket 
        AND p.id_peserta=$id_peserta ) nilai_max  
      FROM tb_jawabans a 
      JOIN tb_paket_kelas c ON a.paket_kelas=c.paket_kelas 
      JOIN tb_paket b ON c.id_paket=b.id  
      WHERE c.id_paket=$id_paket 
      AND a.id_peserta=$id_peserta 
      ORDER BY a.tanggal_submit DESC
      ";



      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      if (!mysqli_num_rows($q)) {
        $list_jawabans = div_alert('danger', 'Kamu belum sempat mengisi $Ujian dengan benar');
      } else {
        $list_jawabans = '';
        $i = 0;
        $max_sign = '';
        while ($d = mysqli_fetch_assoc($q)) {
          $i++;
          $max_sign = ($d['nilai'] == $d['nilai_max'] and $max_sign == '') ? '| MAX' : '';
          $class = $max_sign == '| MAX' ? 'biru tebal' : '';
          if ($nilai_max < $d['nilai']) $nilai_max = $d['nilai'];
          // $tanggal_submit = date('d-M H:i',strtotime($d['tanggal_submit']));
          $durasi = ceil((strtotime($d['tanggal_submit']) - strtotime($d['tanggal_start'])) / 60);
          $list_jawabans .= "<hr><span class='$class'>$i. Nilai: $d[nilai] | Benar: $d[jumlah_benar] of $d[tmp_jumlah_soal]  <span class='miring'>in $durasi minutes</span>  $max_sign</span>";
        }
      }

      $info_pembahasan = '';
      if ((strtotime($tanggal_pembahasan) - strtotime('now')) >= 0) {
        $info_pembahasan = "<hr><div class='miring darkred'>Pembahasan soal akan muncul pada tanggal $tanggal_pembahasan_show</div>";
      }
      $dari = urlencode("?ujian&id_paket=$id_paket");
      $info_pembahasan .= $free_access ? '' : "<hr><a class='btn btn-primary btn-block' href='?pembahasan_soal&id_paket=$id_paket&dari=$dari'>Lihat Pembahasan</a>";
    }

    $blok_timer .= "
      <div class='wadah kiri bg-white'>
        Nilai $Ujian kamu:
        $list_jawabans
        $info_pembahasan
      </div>
    ";
  }
}





# =======================================================
# SHOW START
# =======================================================
if (($selisih <= 0 && $selisih_akhir > 0 && $jumlah_attemp < $max_attemp && !$start) || ($id_role == 2 && !$start) || $free_access) {
  $by_pass_notif = $id_role != 2 ? '' : div_alert('danger', "By pass UI dengan Login $Trainer");
  $blok_timer .= $by_pass_notif;
  $rand = rand(1, 9);
  if (!$start) {
    $start_ujian = $jumlah_attemp == $max_attemp ? "<a href='?lp'>Back to Learning Path</a>" : "<a href='?ujian&id_paket=$id_paket&start=1' class='btn btn-success btn-block'>Start $Ujian</a>";
    if ($jumlah_attemp) {
      $blok_timer .= "
      <div class='tengah' style='max-width: 300px; margin: auto'><img  class='img-fluid img-thumbnail' src='assets/img/meme/like-$rand.jpg'></div>
      ";
      if ($nilai_max == 100) {
        $blok_timer .= "
        <div class='mb2 mt4 blue bold'>Selamat!! Kamu sudah mendapat Nilai Sempurna (100)</div>
        $start_ujian
        ";
      } else {
        $coba_lagi = $jumlah_attemp == $max_attemp ? '' : "Silahkan coba lagi! System akan mengambil nilai terbesar untuk rekap $Ujian.";
        $blok_timer .= "
        <div class='mb2 mt4'>Kamu sudah mencoba $jumlah_attemp of $max_attemp attemp. $coba_lagi</div>
        $start_ujian
        ";
      }
    } else {
      $blok_timer .= "
      <div class='tengah' style='max-width: 300px; margin: auto'><img  class='img-fluid img-thumbnail' src='assets/img/meme/funny-$rand.jpg'></div>
      <div class='mb2 mt4'>Kamu belum pernah mencoba. Coba donk!</div>
      $start_ujian
      ";
    }
  }
} else {
  $blok_timer .= "<div class='mt2'><a href='?ujian'>Back to $Ujian Home</a></div>";
}






# =======================================================
# SHOW LIST SOAL
# =======================================================
if (
  ($selisih <= 0 and $selisih_akhir >= 0 and $jumlah_attemp < $max_attemp and $start)
  || (($id_role == 2 || $free_access) && $start)
) {
  $by_pass_notif = $id_role != 2 ? '' : div_alert('danger', "By Pass UI - $Ujian Show List Soal by Login $Trainer");
  echo $by_pass_notif;
  include 'ujian_show_list_soal.php';
}
















































?>
<!-- <section> -->
<!-- <div class="container"> -->

<div class="section-title" data-aos="fade-up">
  <h2><?= $Ujian ?></h2>
  <?= $sub_judul ?>
  <?= $fitur_dosen ?>
</div>
<div class="" data-aos="fade" data-aos-delay=800>
  <?= $info_paket_soal ?>
</div>
<div id=blok_hasil_ujian class="wadah gradasi-hijau tengah" data-aos='fade-up' data-aos-delay='150'>
  <?= $blok_timer ?>
</div>
<?= $list_soal ?>
<?= $form_submit ?>
<span class="debug"><?= $debug ?></span>
<!-- </div> -->
<!-- </section> -->
<?= $countdown ?>














































<script>
  $(function() {
    let jawaban_set = new Set();
    let jumlah_soal = $('#jumlah_soal').text();
    let selisih = parseInt($('#selisih').text());
    let selisih_akhir = parseInt($('#selisih_akhir').text());
    let max_attemp = parseInt($('#max_attemp').text());
    let jumlah_attemp = parseInt($('#jumlah_attemp').text());


    // ==============================================
    // COOKIE HANDLER
    // ==============================================
    let dkue = document.cookie.split(';');

    let kues = '';
    dkue.forEach((kue) => {
      if (kue.substring(0, 9) == 'jawabans=') {
        kues = kue.substring(9, 5000);
      }
    })

    $('#jawabans').val(kues);
    let rkue = kues.split('|');
    console.log(kues);

    rkue.forEach((idkj) => {
      if (idkj.length >= 4) {
        let ridkj = idkj.split('__');
        let id = ridkj[0];
        let kj = ridkj[1];

        $('#jawaban__' + id).text(idkj);
        jawaban_set.add(id);

        $('#div_soal__' + id).removeClass('belum_dijawab');

        if (kj.toUpperCase() == 'T') {
          $('#btn_true__' + id).removeClass('btn-secondary');
          $('#btn_true__' + id).addClass('btn-success');
        } else if (kj.toUpperCase() == 'F') {
          $('#btn_false__' + id).removeClass('btn-secondary');
          $('#btn_false__' + id).addClass('btn-warning');
        } else {
          let z = document.getElementsByClassName('btn_pg__' + id);
          if (z.length == 0) {
            console.log('Undefined KJ :' + kj, id);
          } else {
            console.log('Perform Loop opsi PG id: ' + id);
            for (let i = 0; i < z.length; i++) {
              if (z[i].innerText == kj) {
                $('#btn_pg__' + id + '__' + i).addClass('btn-success');
              }
              // console.log(z[i].innerText);
            }
          }
        }
      }
    })
    // END COOKIE HANDLER

    console.log('jumlah jawaban Set(): ', jawaban_set.size);
    console.log('jumlah soal: ', jumlah_soal);
    if (jawaban_set.size == jumlah_soal) {
      $('#span_submit').hide();
      $('#check_submit').prop('disabled', 0);
    }

    let belum_dijawab_count = jumlah_soal - jawaban_set.size;
    $('#belum_dijawab_count').text(belum_dijawab_count);
    $('#belum_dijawab_count2').text(belum_dijawab_count);
    if (belum_dijawab_count == 0) {
      $('#sekian_soal_lagi').html('<span class="kecil green">All done!</span>');
    }


    // ==============================================
    // TIMER
    // ==============================================
    let eta_detik = 0;
    let eta_menit = 0;
    let eta_jam = 0;
    let eta_hari = 0;

    let eta_detik_show = '';
    let eta_menit_show = '';
    let eta_jam_show = '';
    let eta_hari_show = '';

    // selisih = 60*60*24 + 5; //debug
    // selisih = 60*60 + 5; //debug
    // selisih = 60 + 5; //debug

    let timer = setInterval(() => {
      if (selisih <= 0) {
        clearInterval(timer);
      } else {
        selisih--;
        eta_detik = selisih % 60;
        eta_menit = Math.floor(selisih / 60, 0) % 60;
        eta_jam = Math.floor(selisih / (60 * 60), 0) % 24;
        eta_hari = Math.floor(selisih / (60 * 60 * 24), 0);

        eta_detik_show = eta_detik < 10 ? `0${eta_detik}` : `${eta_detik}`;
        eta_menit_show = eta_menit < 10 ? `0${eta_menit}:` : `${eta_menit}:`;
        eta_jam_show = eta_jam < 10 ? `0${eta_jam}:` : `${eta_jam}:`;
        eta_hari_show = eta_hari == 0 ? '' : `${eta_hari}d:`;

        $('#detik_lagi').text(
          eta_hari_show +
          eta_jam_show +
          eta_menit_show +
          eta_detik_show
        );


      }
    }, 1000);


    // ==============================================
    // COUNTDOWN SESI BERLANGSUNG
    // ==============================================
    if (selisih <= 0 && selisih_akhir > 0 && jumlah_attemp < max_attemp) {
      let countdown = setInterval(() => {
        if (selisih_akhir <= 0) {
          clearInterval(countdown);
          // location.reload();
          // document.btn_submit.submit();
          $('.btn_jawab').hide();
          $('#blok_btn_submit').show();
          $('#span_submit').hide();
          alert('Waktu Habis! Silahkan Submit Jawaban Anda.');

        } else {
          selisih_akhir--;
          eta_detik = selisih_akhir % 60;
          eta_menit = Math.floor(selisih_akhir / 60, 0);

          eta_detik_show = eta_detik < 10 ? `0${eta_detik}` : `${eta_detik}`;
          eta_menit_show = eta_menit < 10 ? `0${eta_menit}:` : `${eta_menit}:`;

          $('#countdown').text(
            eta_menit_show +
            eta_detik_show
          );


        }
      }, 1000);
    }






    // ==============================================
    // CLICK HANDLER
    // ==============================================
    $('#check_submit').click(function() {
      $('#btn_submit_jawaban_ujian').prop('disabled', !$(this).prop('checked'))
    })


    $('.btn_jawab').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let btn = rid[0];
      let id_assign_soal = rid[1];
      let jawaban;

      if (btn == 'btn_true') {
        $(this).addClass('btn-success');
        $(this).removeClass('btn-secondary');
        $('#btn_false__' + id_assign_soal).removeClass('btn-warning');
        $('#btn_false__' + id_assign_soal).addClass('btn-secondary');
        jawaban = 'T';
      } else if (btn == 'btn_false') {
        jawaban = 'F';
        $(this).addClass('btn-warning');
        $(this).removeClass('btn-secondary');
        $('#btn_true__' + id_assign_soal).removeClass('btn-success');
        $('#btn_true__' + id_assign_soal).addClass('btn-secondary');
      } else if (btn == 'btn_pg') { // 
        jawaban = $(this).text();
        key = rid[2];
        console.log('jawaban-pg: ' + jawaban, 'key: ' + key);
        $('.btn_pg__' + id_assign_soal).removeClass('btn-success');
        $('.btn_pg__' + id_assign_soal).addClass('btn-secondary');
        $(this).addClass('btn-success');

        if (jawaban_set.size >= jumlah_soal - 1) {
          $('#check_submit').prop('disabled', 0);
        }
      } else {
        console.log('Undefined btn opsi name index[0]');
        return;
      }
      $('#div_soal__' + id_assign_soal).removeClass('belum_dijawab');
      $('#jawaban__' + id_assign_soal).text(id_assign_soal + '__' + jawaban);
      jawaban_set.add(id_assign_soal);

      // console.log(jawaban_set.size,jumlah_soal,'debug');

      let belum_dijawab_count = jumlah_soal - jawaban_set.size;
      $('#belum_dijawab_count').text(belum_dijawab_count);
      $('#belum_dijawab_count2').text(belum_dijawab_count);
      if (belum_dijawab_count == 0) {
        $('#sekian_soal_lagi').html('<span class="kecil green">All done!</span>');
      }



      let jawabans = '';
      let rj = document.getElementsByClassName('jawaban');
      for (let i = 0; i < rj.length; i++) {
        jawabans += rj[i].innerText + '|';
      }
      $('#jawabans').val(jawabans);
      console.log('LENGTH OF JAWABANS: ' + jawabans.length);
      document.cookie = 'jawabans=' + jawabans;
      // console.log('SET-COOKIE :\n'+jawabans);



      if (belum_dijawab_count == 0) {
        $('#span_submit').hide();
        $('#blok_btn_submit').fadeIn(2000);
      }


    })
  })
</script>