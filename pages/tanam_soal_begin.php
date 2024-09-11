<style>
  .unclicked {
    background: #aaa;
  }

  .opsi {
    margin-top: 4px;
    font-size: small;
    border: none;
    color: #555
  }

  .blok_opsi {
    display: grid;
    grid-template-columns: 25px auto 80px;
    gap: 8px
  }

  .blok_info {
    background: #efe
  }
</style>
<?php
$kalimat_soal = '';
$opsi['a'] = '';
$opsi['b'] = '';
$opsi['c'] = '';
$opsi['d'] = '';
$pembahasan = '';
# =================================================================
# HANDLE SUBMIT
# =================================================================
if (isset($_POST['btn_simpan'])) {
  $kalimat_soal = $_POST['kalimat_soal2'] ?? die(erid('kalimat_soal2::null'));
  $opsi['a'] = $_POST['opsi__a2'] ?? die(erid('opsi__a2::null'));
  $opsi['b'] = $_POST['opsi__b2'] ?? die(erid('opsi__b2::null'));
  $opsi['c'] = $_POST['opsi__c2'] ?? die(erid('opsi__c2::null'));
  $opsi['d'] = $_POST['opsi__d2'] ?? die(erid('opsi__d2::null'));
  $pembahasan = $_POST['pembahasan'];

  if ($kalimat_soal == '') die(erid('kalimat_soal::null'));
  if ($opsi['a'] == '') die(erid('opsi__a::null'));
  if ($opsi['b'] == '') die(erid('opsi__b::null'));
  if ($opsi['c'] == '') die(erid('opsi__c::null'));
  if ($opsi['d'] == '') die(erid('opsi__d::null'));

  $kalimat_soal = clean_sql($kalimat_soal);
  $opsi['a'] = clean_sql($opsi['a']);
  $opsi['b'] = clean_sql($opsi['b']);
  $opsi['c'] = clean_sql($opsi['c']);
  $opsi['d'] = clean_sql($opsi['d']);
  $pembahasan = clean_sql($pembahasan);





  $opsies = "$opsi[a]~~~$opsi[b]~~~$opsi[c]~~~$opsi[d]";
  $jawaban = $opsi['a'];
  if ($_POST['kj'] == 'b') $jawaban = $opsi['b'];
  if ($_POST['kj'] == 'c') $jawaban = $opsi['c'];
  if ($_POST['kj'] == 'd') $jawaban = $opsi['d'];

  $status = $id_role == 1 ? 'NULL' : 2;

  function tambah_spasi($a)
  {
    $a = str_replace('<', ' < ', $a);
    $a = str_replace('>', ' > ', $a);
    $a = str_replace('< =', '<=', $a);
    $a = str_replace('> =', '>=', $a);
    $a = str_replace('  ', ' ', $a);
    $a = str_replace('&', 'n', $a);
    $a = str_replace('#', '[hashtag]', $a);
    return $a;
  }


  $kalimat_soal = tambah_spasi($kalimat_soal);
  $jawaban = tambah_spasi($jawaban);
  $opsies = tambah_spasi($opsies);

  $_POST['my_tags'] = trim($_POST['my_tags']);

  $s = "INSERT INTO tb_soal_peserta 
  (id_sesi,id_pembuat,kalimat_soal,tags,opsies,jawaban,id_status) values 
  ($_POST[id_sesi],$id_peserta,'$kalimat_soal','$_POST[my_tags]','$opsies','$jawaban',$status)
  ";
  // echo "<pre>$s</pre>";
  // zzz poin membuat soal belum disimpan
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', 'Simpan Soal sukses. | <a href="?soal_saya">Soal Saya</a> |  <a href="?tanam_soal">Tanam Lagi</a>');
  // echo '<script>location.replace("?soal_saya")</script>';
  exit;
}




# =================================================================
# START
# =================================================================
// max 10 kalimat_soal per sesi

$id_sesi = $_GET['id_sesi'] ?? '';
$info_sesi = '';
$pilih_sesi = '';
if ($id_sesi == '') {
  // include 'include/arr_sesi.php';
  $s = "SELECT a.*, a.id as id_sesi,
  (
    SELECT count(1) FROM tb_soal_peserta p 
    WHERE id_pembuat=$id_peserta 
    AND id_sesi=a.id) my_soal_count 
  FROM tb_sesi a 
  WHERE a.id_room='$id_room' 
  AND a.jenis=1 -- sesi normal
  ORDER BY a.no";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $i = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $id_sesi = $d['id_sesi'];
    $tmp_jumlah_soal = $d['tmp_jumlah_soal_pg'] ?? 0;
    $my_soal_count = $d['my_soal_count'];

    if ($id_role != 1) {
      // instruktur only
      $s2 = "SELECT 1 FROM tb_soal_peserta WHERE id_sesi=$id_sesi AND (id_status is null OR id_status >= 0)";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
      $jumlah_soal = mysqli_num_rows($q2);

      if ($jumlah_soal != $tmp_jumlah_soal) {
        // update
        $s2 = "UPDATE tb_sesi SET tmp_jumlah_soal_pg=$jumlah_soal WHERE id=$id_sesi";
        $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
      }
    }

    $badge_green = $my_soal_count ? 'badge_green' : 'badge_red';

    $tags = $d['tags'];
    $r = explode(';', $tags);
    sort($r);
    $imp = $tags == '' ? '<span class=red>belum bisa mengajukan kalimat_soal karena belum ada tags sesi.</span>' : implode(', ', $r);
    $tags_show = "<div class='kecil miring abu'>Tags: $imp</div>";
    $danger = $tags == '' ? 'danger' : 'success';
    $href = $tags == ''
      ? "'#' onclick='alert(\"Maaf, belum bisa mengajukan kalimat_soal pada sesi ini karena instuktur belum setting tags untuk sesi ini.\")'"
      : "'?tanam_soal&id_sesi=$id_sesi'";
    $pilih_sesi .= "
      <div class='col-md-4 mb2'>
        <a class='btn btn-$danger btn-sm mb1 btn-block' href=$href>
          P$i $d[nama] <span class='count_badge $badge_green'>$my_soal_count</span>
        </a>
        $tags_show
      </div>
    ";
  }


  $info_sesi = "
    <div class='darkblue tebal'>Silahkan pilih di sesi manakah kamu ingin membuat soal:</div><hr>
    <div class=row>$pilih_sesi</div>
  ";
  $form = '';
} else {
  $s = "SELECT * FROM tb_sesi WHERE id=$id_sesi ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (!mysqli_num_rows($q)) die(erid('id_sesi (not found)'));
  $d = mysqli_fetch_assoc($q);
  $tags = $d['tags'];
  $nama_sesi = $d['nama'];
  $no_sesi = $d['no'];
  $info_sesi = "
    <div>Sesi: <code>$nama_sesi</code> | <a href='?tanam_soal'>pilih sesi lain</a></div>
  ";



  $rabjad = ['a', 'b', 'c', 'd'];
  $opsies = '';
  foreach ($rabjad as $abjad) {
    $opsies .= "
      <div class='blok_opsi'>
        <div class=tengah>$abjad.</div>
        <div>
          <input class='form-control opsies user_input' id=opsi__$abjad name=opsi__$abjad minlength=3 maxlength=30 required value='$opsi[$abjad]'>
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
      <input class=debug name=kalimat_soal2 id=kalimat_soal2 placeholder=kalimat_soal2>
      <input class=debug name=opsi__a2 id=opsi__a2 placeholder=opsi__a2>
      <input class=debug name=opsi__b2 id=opsi__b2 placeholder=opsi__b2>
      <input class=debug name=opsi__c2 id=opsi__c2 placeholder=opsi__c2>
      <input class=debug name=opsi__d2 id=opsi__d2 placeholder=opsi__d2>

      <div class='form-group mb2'>
        <label for=kalimat_soal>Kalimat soal:</label>
        <textarea name=kalimat_soal id=kalimat_soal class='form-control user_input' rows=6 minlength=30 required>$kalimat_soal</textarea required>
        <div class='kecil miring abu mt1' id=minimal_30_huruf>minimal 30 huruf s.d 300 huruf</div>
      </div>

      $opsies

      <div class='wadah mt2 blok_info tengah'>
        <div class='kecil miring red bold mt2' id=opsi_error></div>
        <div class='kecil miring red bold mt2 hideit' id=info_kj>Kunci Jawaban belum di Set $img_warning</div>
        <div class='kecil miring red bold mt2 hideit' id=info_tags>Tags belum ada di kalimat atau opsi soal  $img_warning</div>
        <div class='kecil miring abu mt1 mb2' id=info_tags_awal>
          <div class='mt1'>Pilihan Tags:</div> 
          <span id=tags class=green>$tags</span>.
        </div>
        <div class='kecil miring darkred mt2 hideit' id=blok_similaritas>
          <span class='btn btn-primary btn-sm w-100' id=btn_cek_similaritas>Cek Similaritas</span>
          <span id=similaritas>0% </span>
          <span id=similaritas_info>???</span>
        </div>
        <div id=blok_reset_similaritas class='hideit mt2'>
          <span class='btn btn-primary btn-sm btn-block mb2' id=btn_reset_similaritas>Perbaiki Kalimat Soal</span>
          <div class='kecil darkblue miring tengah'>Ubahlah kalimat soal atau opsi agar agar angka similaritas tidak terlalu tinggi</div>
        </div>
      </div>

      <div id=info_poin class=mb2></div>

      <div id=blok_ubah_kalimat class='hideit mt2'>
        <span class='btn btn-secondary btn-sm btn-block mb2' id=btn_ubah_kalimat>Ubah Kalimat Soal</span>
        <div class='kecil darkblue miring tengah'>Saat ini kamu boleh mengubah kalimat soal, membuat pembahasan, atau langsung simpan soal</div>
      </div>

      <div id=blok_toggle_pembahasanZZZ class='hideit mt2'>
        <span class='hideit btn btn-secondary btn-sm kecil' id=toggle_pembahasan>Tambah Pembahasan (+50 LP):</span>
      </div>
      <div id=blok_pembahasan class='hideit mt2'>
        <textarea name=pembahasan id=pembahasan class=form-control rows=6 minlength=30>$pembahasan</textarea>
        <div class='kecil miring abu'>Sifatnya opsional, minimal 30 huruf</div>
      </div>

      <div class=form-group>
        <button name=btn_simpan id=btn_simpan class='btn btn-primary btn-block hideit' disabled>Simpan Soal Saya</button>
      </div>

    </form>
  ";
}



?>


<div class="wadah gradasi-hijau" data-aos-zzz='fade-up'>
  <?= $info_sesi ?>
  <?= $form ?>

</div>

</div>
</section>














<script>
  $(function() {
    let tags = $('#tags').text().split(',');
    let my_tags = [];
    let minimal_30_huruf = $('#minimal_30_huruf').text();
    let img_check = '<img src="assets/img/icon/check.png" alt="ok" height="20px" />';
    let img_reject = '<img src="assets/img/icon/reject.png" alt="ok" height="20px" />';
    let kalimat_soal = '';
    let opsi__a = '';
    let opsi__b = '';
    let opsi__c = '';
    let opsi__d = '';

    let kalimat_soal_full = '';
    let kj = '';
    let similaritas = 0;
    let similaritas_show = '';

    function reset_form() {
      $('#info_poin').text('');
      $('#btn_simpan').prop('disabled', true);
      $('#btn_simpan').hide();
      $('#blok_similaritas').hide();
      $('#blok_toggle_pembahasan').hide();
      $('#blok_reset_similaritas').hide();
      $('#blok_ubah_kalimat').hide();
      $('#btn_cek_similaritas').show();
      similaritas = 0;
      similaritas_show = '';
      $('#similaritas').text(similaritas + '%');
      $('#similaritas_info').text(similaritas_show);
      $('.user_input').prop('disabled', false);
      $('.set_kj').show();
    }

    // ==============================================
    // FORM LOAD
    // ==============================================
    reset_form();

    function ready_simpan() {
      $('#blok_toggle_pembahasan').fadeIn();
      $('#info_poin').html('<span class="btn btn-secondary btn-sm">Poin membuat soal +100 LP</span> ' + img_check);
      $('#btn_simpan').prop('disabled', false);
      $('#btn_simpan').show();
      $('#kalimat_soal2').val(kalimat_soal);
      $('#opsi__a2').val(opsi__a);
      $('#opsi__b2').val(opsi__b);
      $('#opsi__c2').val(opsi__c);
      $('#opsi__d2').val(opsi__d);
      console.log(kalimat_soal, opsi__a, opsi__b, opsi__c, opsi__d);
    }

    $('#kalimat_soal').keyup(function() {
      let val = $(this).val();
      if (val.length >= 30) {
        $('#minimal_30_huruf').html(val.length + ' of max 300 huruf ' + img_check);
        kalimat_soal = val.trim();

      } else {
        $('#minimal_30_huruf').text(val.length + ' | ' + minimal_30_huruf);
      }
      // recall
      $('.opsies').keyup();
    })

    $('.opsies').keyup(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let abjad = rid[1];

      // replace hashtag dan ampersand
      let v = $(this).val();
      v = v.replace('#', 'hashtag');
      v = v.replace('&', 'ampersand');
      $(this).val(v.substring(0, 30));

      opsi__a = $('#opsi__a').val().trim();
      opsi__b = $('#opsi__b').val().trim();
      opsi__c = $('#opsi__c').val().trim();
      opsi__d = $('#opsi__d').val().trim();


      kalimat_soal = $('#kalimat_soal').val().trim();
      reset_form();

      if (kalimat_soal.length < 30) {
        $('#opsi_error').text('Kalimat soal minimal 30 s.d 300 huruf');
      } else if (0 ||
        opsi__a.length < 3 ||
        opsi__b.length < 3 ||
        opsi__c.length < 3 ||
        opsi__d.length < 3
      ) {
        $('#opsi_error').text('Opsi minimal 3 s.d 30 huruf');
      } else {
        if (0 ||
          opsi__a.toUpperCase() == opsi__b.toUpperCase() ||
          opsi__a.toUpperCase() == opsi__c.toUpperCase() ||
          opsi__a.toUpperCase() == opsi__d.toUpperCase() ||
          opsi__b.toUpperCase() == opsi__c.toUpperCase() ||
          opsi__b.toUpperCase() == opsi__d.toUpperCase() ||
          opsi__c.toUpperCase() == opsi__d.toUpperCase()
        ) {
          $('#opsi_error').text('Dilarang ada opsi yang sama!');
        } else {
          $('#opsi_error').text('');
          $('#info_tags').show();

          // =================================================
          // OPSIES LENGTH ARE OK :: MY TAGS
          // =================================================
          opsies = `${opsi__a} ${opsi__b} ${opsi__c} ${opsi__d}`;
          kalimat_soal_full = `${kalimat_soal} ${opsies}`;

          my_tags = []; // re-empty tags
          tags.forEach(tag => {
            if (kalimat_soal_full.toLocaleLowerCase().search(tag.toLowerCase().trim()) >= 0) {
              my_tags.push(tag);

            }
          });

          // console.log(my_tags);
          if (my_tags.length == 0) {
            $('#info_tags').text('Tags belum ada di kalimat atau opsi soal');
            $('#info_tags_awal').fadeIn();
            // $('#info_tags').addClass('red');
            $('#tags').val('');
          } else {
            // =================================================
            // MY TAGS OK
            // =================================================
            $('#info_kj').show();
            $('#info_tags_awal').fadeOut();
            $('#info_tags').html('<div class="mb2"><span class=green>Tags yang dipakai:</span> <span class=blue>' +
              my_tags.join(', ') + '</span> ' + img_check + '</div>');
            $('#info_tags').removeClass('darkred');
            $('#my_tags').val(my_tags.join(','));
            if (kj != '') {
              // =================================================
              // MY TAGS OK + KJ OK
              // =================================================
              $('#blok_similaritas').show();

              if (similaritas > 0 && similaritas <= 75) {
                ready_simpan();
              }
            }
          }
        }
      }


    })

    $('.set_kj').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let abjad = rid[1];
      // console.log(abjad);

      $('.set_kj').addClass('unclicked');
      $('.set_kj').text('Set KJ');
      $(this).removeClass('unclicked');
      $(this).text('KJ : ' + abjad.toUpperCase());
      $('#info_kj').html('<span class=green>Kunci Jawaban : ' + abjad.toUpperCase() + '</span> ' + img_check);
      // $('#info_kj').removeClass('darkred');
      $('#info_kj').addClass('biru tebal');
      $('#kj').val(abjad);
      kj = abjad;
      // recall
      $('.opsies').keyup();
    })

    $('#btn_cek_similaritas').click(function() {
      let link_ajax = `ajax/cek_similaritas_soal.php?my_tags=${my_tags}&kalimat_soal=${kalimat_soal_full}`;
      console.log('link_ajax:', link_ajax);

      $.ajax({
        url: link_ajax,
        success: function(a) {
          // console.log(a);
          let ra = a.split('__');
          $('#btn_cek_similaritas').hide();
          $('.set_kj').fadeOut();
          if (ra[0] == 'sukses') {
            similaritas = parseInt(ra[1]);
            $('.user_input').prop('disabled', true);
            similaritas_show = 'Similaritas ' + similaritas + '% ';
            if (similaritas > 75) {
              similaritas_show += img_reject;
              $('#blok_reset_similaritas').fadeIn();
            } else {
              similaritas_show += img_check;
              ready_simpan();
              $('#blok_ubah_kalimat').fadeIn();
            }
            $('#similaritas').html(similaritas_show);
            $('#similaritas_info').html(ra[2]);
            // $('.opsies').keyup();
          } else {
            alert(a);
          }
        }
      })

    })

    $('#btn_reset_similaritas').click(function() {
      reset_form();
    })

    $('#btn_ubah_kalimat').click(function() {
      reset_form();
    })


    $('#toggle_pembahasan').click(function() {
      $('#blok_pembahasan').fadeToggle();
    })

  })
</script>