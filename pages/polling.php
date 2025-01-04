<style>
  .gradasi-abu {
    background: linear-gradient(#ddd, #bbb);
    height: 100%;
  }

  .gradasi-abu:hover {
    background: linear-gradient(#eee, #bdb)
  }

  .btn-active {
    background: linear-gradient(#cfc, #afa);
    border: solid 2px blue;
  }

  .btn-active:hover {
    background: linear-gradient(#dfd, #bdb)
  }
</style>
<!-- ========================================================== -->
<?php
login_only();
$u = $_GET['u'] ?? 'uts';
$fitur_dosen = $id_role == 1 ? '' : "<div class='wadah mt2 gradasi-merah'>Fitur $Trainer: <a href='?polling_hasil&u=$u'>Hasil Polling</a></div>";

$judul = "Polling $u";
set_title($judul);
echo "
<div class='section-title' data-aos='fade'>
  <h2>$judul</h2>
  <p>Assalamu'alaikum! Halo semuanya! Agar DIPA Joiner semakin baik, saya <span id=nama_instruktur class=darkblue>$nama_instruktur</span> ingin meminta feedback dari kamu. Silahkan polling dengan sejujur-jujurnya. Terimakasih!</p>
  $fitur_dosen
</div>
";
$get_dari = $_GET['dari'] ?? '?';
$dari = urldecode($get_dari);


if (isset($_POST['btn_submit'])) {
  $s = "SELECT * FROM tb_polling_answer WHERE id_untuk like '$id_peserta-$u' AND id_room='$id_room'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    $s = "UPDATE tb_polling_answer SET 
    jawabans='$_POST[jawabans]', 
    nama_responden='$_POST[nama_responden]',
    tanggal=current_timestamp 
    WHERE id_untuk like '$id_peserta-$u' AND id_room='$id_room'
    ";
  } else {
    $s = "INSERT INTO tb_polling_answer (id_room,id_untuk, jawabans,nama_responden) 
    VALUES ($id_room,'$id_peserta-$u' ,'$_POST[jawabans]','$_POST[nama_responden]') 
    ";
  }
  // echo $s;
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', "Terima kasih <span class='tebal darkblue'>$_POST[nama_responden]</span> atas Polling dan Saran Anda. | <a href='$dari'>Back</a>");
  exit;
}



# =========================================================
# CEK JIKA SUDAH POLLING
# =========================================================
$s = "SELECT * FROM tb_polling_answer WHERE  id_untuk like '$id_peserta-$u' AND id_room='$id_room'";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$arr_jawaban = [];
$arr_isian = [];
$nama_responden = 'RESPONDEN-' . rand(111, 999);
if (mysqli_num_rows($q)) {
  $d = mysqli_fetch_assoc($q);
  $jawabans = $d['jawabans'];
  $nama_responden = $d['nama_responden'] ?? $nama_responden;
  $tanggal_polling = date('d M Y H:i:s', strtotime($d['tanggal']));

  $rj = explode('|||', $jawabans);
  foreach ($rj as $j) {
    if (strlen($j) > 2) {
      $rjj = explode('~~', $j);

      //jika len jawaban >1 atau isian/uraian
      if (strlen($rjj[1]) == 1) {
        $arr_jawaban[$rjj[0]] = $rjj[1];
      } else {
        $arr_isian[$rjj[0]] = $rjj[1];
      }
    }
  }
  $gradasi_merah = '';
  $hideit_saran = '';
  $submit_caption = 'Re-Submit';
  $kamu_sudah_polling = "<div class='mt2 kecil biru'>Kamu sudah pernah polling pada tanggal $tanggal_polling. Jika ingin re-Polling silahkan isi kembali pollingnya.</div>";
} else {
  $kamu_sudah_polling = '';
  $submit_caption = 'Submit';
  // $submit_disabled = 'disabled';
  $hideit_saran = 'hideit';
  $gradasi_merah = 'gradasi-merah';
  $jawabans = '';
  $saran = '';
}




# =========================================================
# GET POLLING DATA
# =========================================================
$s = "SELECT * FROM tb_polling WHERE untuk='$u' ORDER BY no,id";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$rpolling = [];
$jumlah_pilihan = 0;
$jumlah_isian = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $rpolling[$d['no']] = [$d['pertanyaan'], $d['respon']];
  if (!($d['respon'] == 'isian' || $d['respon'] == 'uraian')) {
    $jumlah_pilihan++;
  } else {
    $jumlah_isian++;
  }
}

$count_rpolling = count($rpolling);
$polls = "<span class=debug>jumlah_jawabans:<span id=jumlah_jawabans>$count_rpolling</span></span>";
$polls .= "<span class=debug>jumlah_pilihan:<span id=jumlah_pilihan>$jumlah_pilihan</span></span>";
$polls .= "<span class=debug>jumlah_isian:<span id=jumlah_isian>$jumlah_isian</span></span>";
foreach ($rpolling as $no => $rtanya) {
  $polls .= "<span class=debug><span class=jawabans id=jawabans__$no></span></span>";
  if ($rtanya[1] == 'rate') {
    //rate
    $opsi = '';
    for ($i = 1; $i <= 5; $i++) {
      $no_counter = $no . "__$i";
      $opsi .= "
      <img id=stars__$no_counter class='zoom pointer stars stars__$no aksi' src=assets/img/icon/stars.png height=40px> 
      ";
    }
    $opsi = "<div class='tengah mt2 mb4'>$opsi</div>";
  } elseif ($rtanya[1] == 'isian') {
    $opsi = "
      <input required class='form-control isian' name='isian__$no' id='isian__$no'/>
      <div class='kecil abu miring mt1 mb2'> )* jika tidak ada silahkan strip</div>
    ";
  } elseif ($rtanya[1] == 'uraian') {
    $opsi = "
      <textarea required class='form-control isian' name='uraian__$no' id='uraian__$no' rows='4'></textarea>
      <div class='kecil abu miring mt1 mb2'> )* jika tidak ada silahkan strip</div>
    ";
  } else {
    // setuju / tdk setuju
    $opsi = "
      <div class='row mt2 mb4'>
        <div class='col-md-3 mb2'><span class='btn gradasi-abu btn-block aksi proper btn__$no' id=btn__$no" . "__1>Tidak $rtanya[1]</span></div>
        <div class='col-md-3 mb2'><span class='btn gradasi-abu btn-block aksi proper btn__$no' id=btn__$no" . "__2>Sedikit $rtanya[1]</span></div>
        <div class='col-md-3 mb2'><span class='btn gradasi-abu btn-block aksi proper btn__$no' id=btn__$no" . "__3> $rtanya[1]</span></div>
        <div class='col-md-3 mb2'><span class='btn gradasi-abu btn-block aksi proper btn__$no' id=btn__$no" . "__4>Sangat $rtanya[1]</span></div>
      </div>
    ";
  }

  // show pertanyaan
  $polls .= "
    <div class='btop pt2 mb2 $gradasi_merah p1 ' id=polls__$no>
      $no
      <div class=mb2>$rtanya[0]</div>
      $opsi
    </div>
  ";
}

?>

<div class="wadahzzz">
  <h4 class='darkblue mb2 mt2'>Polling:</h4>
  <div class="form-group mt2 mb3">
    <label for="nama">Nama</label>
    <input type="text" class=form-control disabled value='<?= $nama_responden ?>'>
    <small class='ml-1 miring abu'>Identitas kamu akan kami rahasiakan.<?= $kamu_sudah_polling ?></small>
  </div>
  <?= $polls ?>
  <form method=post>

    <div class="hideit">
      <input type="hidden" name=nama_responden value='<?= $nama_responden ?>'>
      <textarea id=jawabans name=jawabans class='form-control merah' rows=5></textarea>
    </div>
    <button class="btn btn-primary btn-block" id=btn_submit name=btn_submit disabled><?= $submit_caption ?></button>
  </form>
</div>



































<script>
  $(function() {

    // auto rename $Trainer dengan nama $Trainer
    $('.nama_instruktur').text('Bapak ' + $('#nama_instruktur').text());
    $('.nama_instruktur').addClass('darkblue');


    let isian_set = new Set();
    let pilihan_set = new Set();
    let jumlah_jawabans = parseInt($('#jumlah_jawabans').text());
    let jumlah_pilihan = parseInt($('#jumlah_pilihan').text());

    function update_jawabans() {
      let z = document.getElementsByClassName('jawabans');
      console.log('DEBUG z.length:' + z.length);
      let jawabans = '';
      for (let i = 0; i < z.length; i++) {
        jawabans += z[i].innerText + '|||';
      }
      $('#jawabans').val(jawabans);

      if ((pilihan_set.size + isian_set.size) == jumlah_jawabans) {
        $('#btn_submit').prop('disabled', false);
      } else {
        $('#btn_submit').prop('disabled', true);
      }
    }

    $('.isian').keyup(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let no = rid[1];
      let val = $(this).val().trim();
      if (val.length > 0) {
        $('#jawabans__' + no).text(no + '~~' + val);

        let z = document.getElementsByClassName('jawabans');
        console.log('DEBUG z.length:' + z.length);
        let jawabans = '';
        for (let i = 0; i < z.length; i++) {
          jawabans += z[i].innerText + '|||';
        }
        $('#jawabans').val(jawabans);

        isian_set.add(no);

        console.log('DEBUG isian_set.size:' + isian_set.size);

        $('#polls__' + no).removeClass('gradasi-merah');
      } else {
        $('#polls__' + no).addClass('gradasi-merah');
        isian_set.delete(no);
      }

      update_jawabans();
    })
    $('.aksi').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let no = rid[1];
      let counter = rid[2];

      if (aksi == 'btn') {
        $('.btn__' + no).removeClass('btn-active');
        $(this).addClass('btn-active');
      } else if (aksi == 'stars') {
        $('.stars__' + no).prop('src', 'assets/img/icon/stars.png');
        for (let i = 1; i <= counter; i++) {
          $('#stars__' + no + '__' + i).prop('src', 'assets/img/icon/stars_red.png');
        }
      }

      console.log('DEBUG AKSI click : ' + aksi, 'NOMOR: ' + no, 'COUNTER: ' + counter);

      $('#jawabans__' + no).text(no + '~~' + counter);
      pilihan_set.add(no);

      update_jawabans();

      console.log('DEBUG pilihan_set.size:' + pilihan_set.size, 'JUMLAH PILIHAN:' + jumlah_jawabans);

      $('#polls__' + no).removeClass('gradasi-merah');

    })
  })
</script>