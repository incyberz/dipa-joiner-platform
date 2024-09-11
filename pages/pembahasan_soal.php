<style>
  .wadah2 {
    padding: 10px;
    border: solid 1px #ddd;
    border-radius: 5px;
    margin: 10px 0
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
    $id_paket = $_GET['id_paket'] ?? '';

    # =================================================
    # GET PROPERTI PAKET SOAL
    # =================================================
    $s = "SELECT a.*,
    b.awal_ujian  
    FROM tb_paket a 
    JOIN tb_paket_kelas b ON a.id=b.id_paket  
    WHERE a.id=$id_paket and b.kelas='$kelas'";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    if (!mysqli_num_rows($q)) die(div_alert('danger', 'Data tidak ditemukan'));

    $d = mysqli_fetch_assoc($q);

    $akhir_ujian = akhir_ujian($d['awal_ujian'], $d['durasi_ujian']);
    $tanggal_pembahasan = $d['tanggal_pembahasan'];
    if (strtotime($tanggal_pembahasan) < strtotime('2020-1-1')) {
      die("Tanggal pembahasan invalid ($tanggal_pembahasan) untuk id: $id_paket");
    } else {
      $time = strtotime($tanggal_pembahasan);
    }
    $akhir_ujian_show = date('d M Y H:i', strtotime($akhir_ujian));
    $tanggal_pembahasan_show = date('d M Y H:i', strtotime($tanggal_pembahasan));

    $selisih = strtotime($tanggal_pembahasan) - strtotime('now');
    $selisih_debug = "|| $selisih || $tanggal_pembahasan";
    $selisih_debug = '';

    if ($selisih < 60) {
      $eta = "$selisih detik lagi $selisih_debug";
    } elseif ($selisih < 60 * 60) {
      $eta = ceil($selisih / 60) . " menit lagi $selisih_debug";
    } elseif ($selisih < 60 * 60 * 24) {
      $eta = round($selisih / (60 * 60), 0) . " jam lagi $selisih_debug";
    } else {
      $eta = round($selisih / (60 * 60 * 24), 0) . " hari lagi $selisih_debug";
    }
    $eta = "<span class='miring kecil biru tebal'>($eta)</span>";

    if ((strtotime($tanggal_pembahasan) - strtotime('now')) >= 0) {
      echo div_alert('danger', "Sabar masbro/sisbro!! <div class='kecil miring abu'>Pembahasan soal akan muncul pada tanggal $tanggal_pembahasan_show $eta<hr>Syarat melihat pembahasan adalah:
        <ol>
          <li>Upload foto profil yang baik | <a href='?upload_profil'>Upload</a></li>
          <li>Foto profil sudah terverifikasi oleh intruktur | <a href='?verifikasi_profil_peserta'>Lihat Status Profil</a></li>
          <li>Kamu sudah mengisi polling pasca ujian | <a href='?polling'>Isi Polling</a></li>
        </ol>
      </div>");
    } else {
      if ((strtotime($tanggal_pembahasan) - strtotime($akhir_ujian)) < 0) {
        echo div_alert('danger', "Tanggal Pembahasan invalid ($tanggal_pembahasan). <div class='kecil miring abu'>Tanggal Pembahasan lebih awal dari tanggal akhir ujian $akhir_ujian_show. Pembahasan akan tetap ditampilkan setelah tanggal ujian tersebut berakhir.</div>");
      }

      if ((strtotime($tanggal_pembahasan) - strtotime($akhir_ujian)) < 0 and false) {
        echo div_alert('danger', "Tanggal Pembahasan invalid. <div class='kecil miring abu'>Tanggal Pembahasan lebih awal dari tanggal akhir ujian $akhir_ujian_show. Pembahasan akan tetap ditampilkan setelah tanggal ujian tersebut berakhir.</div>");
      } else {
        # =================================================
        # PRASYARAT PEMBAHASAN
        # =================================================
        $sudah_polling = 0;
        $s = "SELECT 1 FROM tb_polling_answer WHERE id=$id_peserta";
        $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
        if (mysqli_num_rows($q)) $sudah_polling = 1;


        if (!$sudah_polling and false) {
          $dari = urlencode("?pembahasan_soal&id_paket=$id_paket");
          echo div_alert('info', "Untuk mengakses Fitur Pembahasan Soal, silahkan kamu mengisi dahulu Polling dan Kuesioner ya!! Agar web DIPA ini semakin baik. <hr><a class='btn btn-primary btn-sm btn-block' href='?polling&dari=$dari'>Polling UTS</a>");
        } else {
          if ($profil_ok == -1 and false) {
            echo div_alert('danger', 'Wah maaf! Silahkan ganti profil dulu dengan yang baik. | <a href="?verifikasi_profil_peserta">Status Profile</a>');
          } elseif ($profil_ok == '' || $profil_ok == 0 and false) {
            echo div_alert('danger', 'Sepertinya profil kamu belum diverifikasi oleh instruktur. Sabar ya! Atau silahkan japri ke beliau via Whatsapp.');
          } else {
            # =================================================
            # READY TO PEMBAHASAN
            # =================================================
            $img_check = '<img src="assets/img/icon/check.png" height=25px />';
            $img_reject = '<img src="assets/img/icon/reject.png" height=25px />';
            echo div_alert('info', 'Perhatikan dan ingat baik-baik Pembahasan Soal berikut! Sebagian besar akan muncul di ujian berikutnya.');

            $s = "SELECT * FROM tb_jawabans a 
            JOIN tb_paket_kelas b ON a.paket_kelas=b.paket_kelas 
            JOIN tb_paket c ON b.id_paket=c.id  
            WHERE b.id_paket=$id_paket 
            AND a.id_peserta=$id_peserta";
            // echo $s;
            $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
            if (!mysqli_num_rows($q)) die(div_alert('danger', 'Data Jawaban tidak ditemukan'));
            // if(mysqli_num_rows($q)>1) die(div_alert('danger','zzz Data Jawaban tidak ditemukan'));

            $nilai_max = 0;
            while ($d = mysqli_fetch_assoc($q)) {
              if ($d['nilai'] < $nilai_max) continue;
              $nilai_max = $d['nilai'];
              $jawabans = $d['jawabans'];
              $tmp_jawabans = $d['tmp_jawabans'];
            }

            // echo "<hr>$jawabans<hr>" . strlen($jawabans);


            $ranswer = [];
            $rjawabans = explode('|', $jawabans);
            foreach ($rjawabans as $id_n_answer) {
              if (strlen($id_n_answer) > 2) {
                $rid_n_answer = explode('__', $id_n_answer);
                // echo "<br>$rid_n_answer[0]";
                $ranswer[$rid_n_answer[0]] = $rid_n_answer[1];
              }
            }

            $rkj = [];
            $rtmp_jawabans = explode('|', $tmp_jawabans);
            foreach ($rtmp_jawabans as $id_n_kj) {
              if (strlen($id_n_kj) > 2) {
                $rid_n_kj = explode('__', $id_n_kj);
                $rkj[$rid_n_kj[0]] = $rid_n_kj[1];
              }
            }

            $s2 = "SELECT a.id as id_assign_soal,
            b.soal,
            b.pembahasan  
            FROM tb_assign_soal a 
            JOIN tb_soal b ON a.id_soal=b.id  
            WHERE a.id_paket=$id_paket";
            $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
            $soals = '';
            $i = 0;
            while ($d2 = mysqli_fetch_assoc($q2)) {
              $i++;
              $id = $d2['id_assign_soal'];
              $kj = $rkj[$id];
              $jawaban = $ranswer[$id];
              $icon = $jawaban == $kj ? $img_check : $img_reject;
              $pembahasan = strlen($d2['pembahasan']) > 5 ? $d2['pembahasan'] : '<span class="kecil miring abu">(tidak ada)</span>';

              $soals .= "
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