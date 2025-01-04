<style>
  .div_soal {
    border-top: solid 2px #ddd;
    padding: 10px;
  }

  .belum_dijawab {
    background: linear-gradient(#fee, #fcc)
  }

  .no_dan_soal {
    display: grid;
    grid-template-columns: 20px auto;
    grid-gap: 5px
  }
</style>
<?php
$judul = "Ujian";
set_title($judul);

$debug = '';
if (!$is_login) die('<script>location.replace("?")</script>');
$id_paket = $_GET['id_paket'] ?? '';






# =======================================================
# PAKET SOAL YANG TERSEDIA
# =======================================================
if ($id_paket == '') {
  if ($id_role == 2) {
    // tampilan untuk $Trainer, tampilkan seluruh paket soal untuk setiap kelas
    $s = "SELECT a.*,
    b.awal_ujian,
    b.kelas,
    (
      SELECT COUNT(1) FROM tb_jawabans p 
      JOIN tb_paket_kelas q ON p.paket_kelas = q.paket_kelas  
      WHERE p.id_peserta=$id_peserta 
      AND q.id_paket=a.id) jumlah_attemp  
    FROM tb_paket a 
    JOIN tb_paket_kelas b ON a.id=b.id_paket 
    JOIN tb_sesi c ON a.id_sesi=c.id 
    WHERE c.id_room='$id_room'";
  } else {
    $s = "SELECT a.*,
    b.awal_ujian,
    b.kelas,
    (
      SELECT COUNT(1) FROM tb_jawabans 
      WHERE id_peserta=$id_peserta 
      AND id_paket=a.id) jumlah_attemp  
    FROM tb_paket a 
    JOIN tb_paket_kelas b ON a.id=b.id_paket 
    WHERE b.kelas='$kelas'";
  }

  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $list_paket = '';
  if (mysqli_num_rows($q)) {
    $list_paket = "<div class='mb2 darkblue bold'>Silahkan pilih Paket Soal yang tersedia:</div> ";
    while ($d = mysqli_fetch_assoc($q)) {

      $jumlah_attemp = $d['jumlah_attemp'] ?? 0;
      $max_attemp = $d['max_attemp'] ?? 999;

      // $d['awal_ujian'] = '2023-10-29 9:00'; //debug
      // $d['akhir_ujian'] = '2023-10-29 10:00'; //debug

      $selisih = strtotime($d['awal_ujian']) - strtotime('now');
      $akhir_ujian = date('Y-m-d H:i:s', strtotime($d['awal_ujian']) + $d['durasi_ujian'] * 60);
      $selisih_akhir = strtotime($akhir_ujian) - strtotime('now');
      $selisih_hari = (strtotime(date('Y-m-d', strtotime($d['awal_ujian']))) - strtotime('today')) / (3600 * 24);


      $awal_ujian_show = $nama_hari[date('w', strtotime($d['awal_ujian']))] . ', ' . date('d-M  H:i', strtotime($d['awal_ujian']));
      // $format = $selisih_hari==0 ? 'H:i' : 'd-M  H:i';
      $format =  'd-M  H:i';
      $akhir_ujian_show = date($format, strtotime($akhir_ujian));

      $nama_paket_show = $d['kelas'] == 'INSTRUKTUR' ? "Trial Ujian $d[nama]" : "
        <div class='f20 miring consolas mb2'><u>$d[nama]</u></div>
        <div>$awal_ujian_show s.d $akhir_ujian_show</div> 
      ";

      $info_paket = '';
      $btn = '';
      if ($d['kelas'] == 'INSTRUKTUR') {
        $btn = 'primary';
        if (!$jumlah_attemp) {
          $info_paket = "$Trainer belum mencoba trial.";
        } else {
          $info_paket = "Sudah dicoba oleh $Trainer.";
        }
      } elseif ($selisih_akhir < 0) {
        // sudah berakhir
        $btn = 'secondary';
        if ($jumlah_attemp == 0) {
          $info = 'kamu tidak mengikutinya';
        } elseif (
          $jumlah_attemp >= $max_attemp
        ) {
          $info = 'kamu sudah berusaha secara maksimal';
        } else {
          $info = "kamu sudah mencoba $jumlah_attemp kali dari $max_attemp kesempatan";
        }

        $info_paket = "Ujian sudah berakhir dan $info";
      } elseif ($selisih_akhir >= 0 and $selisih <= 0) {
        // sedang berlangsung
        $btn = 'primary';
        if ($jumlah_attemp == 0) {
          $info = 'kamu belum mencobanya';
        } elseif ($jumlah_attemp >= $max_attemp) {
          $info = 'kamu sudah mencapai max_attemp';
        } else {
          $info = "kamu sudah $jumlah_attemp kali mencoba. Coba lagi!";
        }
        $info_paket = "Sedang berlangsung dan $info
        ";
      } else {
        // belum berlangsung
        $btn = 'info';
        if ($selisih_hari > 0) {
          $eta_info = "$selisih_hari hari lagi";
        } else {
          $eta_info = "$selisih detik lagi";
          if ($selisih > 60) $eta_info = number_format($selisih / 60, 0) . ' menit lagi';
          if ($selisih > 60 * 60) $eta_info = number_format($selisih / (60 * 60), 0) . ' jam lagi';
        }

        $info_paket = "
        <u class='consolas f20'> $eta_info</u>
        <br>Kamu boleh melihat info kisi-kisinya.
        ";
      } // end belum berlangsung

      $btn = $d['kelas'] == 'INSTRUKTUR' ? 'primary' : $btn;

      # ===================================================
      # FINAL OUTPUT LIST PAKET
      # ===================================================
      $list_paket .= "<a class='mb2 btn btn-$btn btn-block' href='?ujian&id_paket=$d[id]'>
        <div class='f14'>Untuk kelas $d[kelas]</div>
        $nama_paket_show
        <div>$info_paket</div>
      </a>
      ";
    } // end while
    $list_paket .= "<hr><div class='tengah' style='max-width: 300px; margin: auto'>$meme</div>";
  } else {
    // echo '<pre>';
    // var_dump($_SERVER);
    // echo '</pre>';
    $img_wa = img_icon('wa');
    $Bapak = '';
    if (strtolower($trainer['gender']) == 'l') $Bapak = 'Bapak';
    if (strtolower($trainer['gender']) == 'p') $Bapak = 'Ibu';
    $datetime = date('d F, Y, H:i:s');

    $link_encoded = urlencode(get_current_url());
    $text_wa = "Yth. $Bapak $trainer[nama], saya $user[nama] ingin meminta Paket Ujian untuk $Room $room[nama] karena sebentar lagi akan memasuki sesi ujian. Terimakasih.%0a%0aLink:%0a$link_encoded%0a%0aFrom: DIPA Joiner System, $datetime";
    $link_wa = "https://api.whatsapp.com/send?phone=$trainer[no_wa]&text=$text_wa";

    $list_paket = div_alert('danger tengah', "
      Maaf, belum ada Paket Soal untuk kelas $kelas.
      <hr>
      Mintalah ke $Trainer kamu untuk membuatnya jika sebentar lagi memasuki sesi ujian.
      <a class='btn btn-success w-100 mt4' href='$link_wa' onclick='return confirm(`Minta Paket Soal via whatsapp?`)'>$img_wa Minta Paket Soal</a>
    ");
  } // end jika ada data paket 

  # ============================================================
  # FITUR INSTRUKTUR
  # ============================================================
  $fitur_instruktur = '';
  if ($id_role == 2) {
    $fitur_instruktur = "
      <div class='wadah gradasi-kuning'>
        <div class=sub_form>Fitur $Trainer</div>
        <a class='btn btn-success' href='?manage_soal'>Manage Soal</a>
        <a class='btn btn-success' href='?manage_paket_soal'>Manage Paket Soal</a>
        <a class='btn btn-success' href='?insert_nilai_manual'>Insert Nilai Manual</a>
      </div>
    ";
  }
  $meme = meme('funny');
  echo "
  <div>
    <div class=mx-auto style=max-width:600px>
      $fitur_instruktur
      $list_paket
      <hr class=mt4>
      <div class='abu f12'>Room info:</div>
      $room[info_ujian]
    </div>
  </div>
  ";
} else {
  include 'ujian_pre_show.php';
}
