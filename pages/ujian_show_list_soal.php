<?php
$list_soal = '';
$s = "SELECT 
a.id as id_assign_soal,
b.soal,
b.opsies,
b.kjs,
b.id as id_soal,
b.tipe_soal,
b.gambar_soal,
c.nama as paket_soal 
FROM tb_assign_soal a 
JOIN tb_soal b ON a.id_soal=b.id 
JOIN tb_paket_soal c ON a.id_paket_soal=c.id 
JOIN tb_tipe_soal d ON b.tipe_soal=d.tipe_soal 
WHERE c.kelas = '$kelas' 
AND c.id=$id_paket_soal 
ORDER BY rand()
";


$div = '';
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_soal = mysqli_num_rows($q);
$debug .= "<br>jumlah_soal:<span id=jumlah_soal>$jumlah_soal</span>";

$i = 0;
$new_tmp_jawabans = '';
while ($d = mysqli_fetch_assoc($q)) {
  $id_assign_soal = $d['id_assign_soal'];
  $new_tmp_jawabans .= $id_assign_soal . "__$d[kjs]|";

  $tipe_soal = $d['tipe_soal'];
  if (strtoupper($tipe_soal) == 'PG') {
    echo '<style>.btn-success{border: solid 3px blue; box-shadow: 0 0 9px yellow; font-weight: bold}</style>';

    $arr = explode('~~~', $d['opsies']);
    $div_opsi = '';
    foreach ($arr as $key => $value) {
      $dual_id = "btn_pg__$id_assign_soal" . "__$key";
      $div_opsi .= "<div class='mb1 col-lg-3 col-md-6'><span class='btn btn-secondary btn-sm btn-block btn_jawab btn_pg__$id_assign_soal' id=$dual_id>$value</span></div>";
    }

    $div_opsies = "
      <div class=col-md-12 style='padding-left:35px'>
        <div class=row>
          $div_opsi
        </div>
      </div>
    ";
  } elseif ($tipe_soal == 'TF') {
    $div_opsies = "
      <div class=col-md-4>
        <div class='row'>
          <div class='col-sm-6 mb1'><span class='btn btn-secondary btn-sm btn-block btn_jawab' id=btn_true__$id_assign_soal>True</span></div>
          <div class='col-sm-6 mb1'><span class='btn btn-secondary btn-sm btn-block btn_jawab' id=btn_false__$id_assign_soal>False</span></div>
        </div>
      </div>
    ";
  } else {
    die(div_alert('danger', "Belum ada handler untuk tipe soal: $tipe_soal"));
  }

  // gambar soal (jika ada)
  $gambar_soal = '';
  if ($d['gambar_soal']) {
    $src = "assets/img/gambar_soal/$d[gambar_soal].jpg";
    if (!file_exists($src)) {
      die("Terdapat gambar soal yang hilang. Segera hubungi Instruktur!<hr>id_soal: $id_soal");
    } else {
      $gambar_soal = "<img src='$src' class='img-fluid'>";
    }
  }


  $i++;
  $div .= "
  <div class='div_soal belum_dijawab' id=div_soal__$id_assign_soal>
    <div class=row>
      <div class='col-md-8 mb2'>
        <div class=no_dan_soal>
          <div>$i.</div>
          <div>
            <div class=mb1>$d[soal]</div> 
            $gambar_soal
            <span class=debug>
              id_assign_soal:<span id=id_assign_soal__$id_assign_soal>$id_assign_soal</span> | 
              jawaban:<span id=jawaban__$id_assign_soal class=jawaban></span> | 
              index_jawaban:<span id=index_jawaban__$id_assign_soal class=index_jawaban></span> | 
            </span> 
          </div>
        </div>
      </div>
      $div_opsies
    </div>
  </div>
  ";
}

// autosave tmp_jawabans if null
$debug .= "<br>tmp_jawabans1: $tmp_jawabans <br>tmp_jawabans2: $new_tmp_jawabans";
if (strlen($new_tmp_jawabans) != strlen($tmp_jawabans)) {
  $s = "UPDATE tb_paket_soal SET tmp_jawabans='$new_tmp_jawabans',tmp_jumlah_soal=$jumlah_soal WHERE id=$id_paket_soal";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
}

$list_soal = "<div data-aos='fade-up' data-aos-delay=300>$div</div>";






$tanggal_start = date('Y-m-d H:i:s');

$form_submit = "
  <form method=post>
    <input id=tanggal_start name=tanggal_start class='debug' value='$tanggal_start'>
    <input id=jawabans name=jawabans class='debug'>
    <div class='mt2' data-aos='fade-up' data-aos-delay='450'>
      <span id=span_submit class='btn btn-secondary btn-block' disabled>Belum bisa Submit Jawaban 
        <br>
        <span class=yellow>
          <span id=belum_dijawab_count style='font-size:40px'>
            $jumlah_soal
          </span>
          soal belum dijawab
        </span>
      </span>
      <div id='blok_btn_submit' class=hideita>
        <div class='mb2 mt2'>
          <table>
            <tr>
              <td valign=top class='pt1'>
                <input type='checkbox' id='check_submit' disabled> 
              </td>
              <td class='pl2 kecil'>
                <label for='check_submit'>Saya sudah menjawab semua soal dan yakin untuk Submit</label>
              </td>
            </tr>
          </table>
        </div>
        <button class='btn btn-primary btn-block ' name=btn_submit_jawaban_ujian id=btn_submit_jawaban_ujian disabled>Submit Jawaban Ujian</button>
        <div class='small miring tengah mt1'><b>Perhatian!</b><br> Silahkan re-check kembali selama masih ada waktu!</div>

      </div>
    </div>
  </form>
";
