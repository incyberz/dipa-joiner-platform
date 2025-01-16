<style>
  .nav_verifikasi {
    cursor: pointer;
    transition: .2s;
  }

  .nav_verifikasi:hover {
    color: #f0f;
    font-weight: bold;
    letter-spacing: 1px;
  }
</style>
<?php

$tb = '';
$mode = $_GET['mode'] ?? 'all';
$arr_nav = [
  'questions' => ['param' => 'questions', 'title' => 'List Bertanya'],
  'my_questions' => ['param' => 'questions&mode=me', 'title' => 'Pertanyaan Saya'],
  'bertanya' => ['param' => 'bertanya', 'title' => 'Bertanya'],
];
if ($mode == 'all') {
  $sql_id_peserta =  "1";
  $current_nav = 'questions';
  $judul_my =  '';
} else {
  $sql_id_peserta = "a.id_penanya=$id_peserta";
  $judul_my = 'My-';
  $current_nav = 'my_questions';
}
$sub = '';
foreach ($arr_nav as $k => $v) {
  if ($k == $current_nav) {
    // $sub .= " $v[title] ";
  } else {
    $slash = $sub ? ' | ' : '';
    $sub .= "$slash<a href='?$v[param]'>$v[title]</a> ";
  }
}
set_h2($judul_my . 'Questions', $sub);






























# ============================================================
# PROCESSORS
# ============================================================
if (isset($_POST['btn_submit_jawaban'])) {

  $id = $_POST['btn_submit_jawaban'];
  $s = "SELECT a.*,b.nama FROM tb_bertanya a 
  JOIN tb_peserta b ON a.id_penjawab=b.id 
  WHERE a.jawaban is not null and a.id=$id";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    $d = mysqli_fetch_assoc($q);
    $nama = $d['nama'];
    echo div_alert('info', "Pertanyaan ini sudah duluan dijawab oleh $nama.");
  } else {
    $if_gm = '';
    if ($id_role == 2) {
      $poin = $_POST['range_poin'] ?? die(erid('range_poin'));
      $if_gm = "
          ,poin = $poin
          ,verif_by = $id_peserta
          ,verif_date = CURRENT_TIMESTAMP
        ";
    }

    $jawaban = addslashes($_POST['jawaban']);

    $s = "UPDATE tb_bertanya SET 
        jawaban='$jawaban',
        id_penjawab=$id_peserta
        $if_gm 
      WHERE id=$id";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    jsurl();
  }
} elseif (isset($_POST['btn_verifikasi'])) {
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';

  $t = explode('__', $_POST['btn_verifikasi']);
  $kondisi = $t[0];
  $id_bertanya = $t[1];

  $s = "SELECT jawaban,id_penjawab FROM tb_bertanya WHERE id=$id_bertanya";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (!mysqli_num_rows($q)) {
    div_alert('danger', 'Data jawaban tidak ditemukan');
  } else {
    $d = mysqli_fetch_assoc($q);
    $jawaban = $d['jawaban'];
    $id_penjawab = $d['id_penjawab'];
  }


  $jawaban_tambahan = $_POST['jawaban_tambahan'] ?? '';
  $jawaban = $jawaban_tambahan ? "$jawaban<hr>tambahan: $jawaban_tambahan" : $jawaban;

  $replace_jawaban = $_POST['replace_jawaban'] ?? '';
  $jawaban = $replace_jawaban ? $replace_jawaban : $jawaban;
  $poin_penjawab = $_POST['poin_penjawab'] ?? 'NULL';

  if ($kondisi == 'sudah_benar') {
  } elseif ($kondisi == 'add_jawaban') {
  } elseif ($kondisi == 'replace_jawaban') {
    $id_penjawab = $id_peserta;
    $poin_penjawab = 'NULL';
    $jawaban = $_POST['jawaban'];
  } elseif ($kondisi == 'reset_jawaban') {
    $s = "UPDATE tb_bertanya SET 
    jawaban = NULL,
    id_penjawab = NULL,
    poin_penjawab = NULL,
    verif_by = NULL,
    verif_date = NULL
    WHERE id=$id_bertanya";

    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    jsurl();
  } else {
    die(div_alert('danger', "Belum ada handler untuk kondisi [$kondisi]"));
  }
  $s = "UPDATE tb_bertanya SET 
    jawaban = '$jawaban',
    id_penjawab = '$id_penjawab',
    poin_penjawab = $poin_penjawab,
    verif_by = $id_peserta,
    verif_date = CURRENT_TIMESTAMP
  WHERE id=$id_bertanya";

  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
} elseif (isset($_POST['btn_delete_bertanya'])) {
  $id = $_POST['btn_delete_bertanya'];
  $s = "DELETE FROM tb_bertanya WHERE id='$id'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
}






























# ============================================================
# MAIN SELECT
# ============================================================
$s = "SELECT a.*,
b.nama as nama_sesi,  
c.nama as penanya,
c.image,
c.war_image,
(SELECT nama FROM tb_peserta WHERE id=a.id_penjawab) penjawab,
(SELECT nama FROM tb_peserta WHERE id=a.verif_by) verifikator,
(SELECT id_role FROM tb_peserta WHERE id=a.id_penjawab) id_role_penjawab 

FROM tb_bertanya a 
JOIN tb_sesi b ON a.id_sesi=b.id 
JOIN tb_peserta c ON a.id_penanya=c.id
WHERE $sql_id_peserta 
AND b.id_room = $id_room 
ORDER BY a.tanggal desc";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$i = 0;
if (!mysqli_num_rows($q)) echo div_alert('info', "Belum ada $Peserta yang bertanya pada $Room ini.");
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $id = $d['id'];
  $is_mine = $d['id_penanya'] == $id_peserta ? true : false;
  $d['penanya'] = $is_mine ? 'saya' : $d['penanya'];
  $d['war_image'] = $d['war_image'] ? $d['war_image'] : $d['image'];




  $by = $d['id_role_penjawab'] ? "<div class='f14 abu miring'>by: $d[penjawab]</div>" : '';

  $form_verifikasi = ($id_role != 2 || $d['id_role_penjawab'] == 2 || $d['verif_by']) ? '' : "
    <div class='flexy flex-center f12 border-top border-bottom p1 mt2 gradasi-toska'>
      <div class=nav_verifikasi id=sudah_benar>Sudah Benar</div>
      <div class=nav_verifikasi id=add_jawaban>Add</div>
      <div class=nav_verifikasi id=replace_jawaban>Replace</div>
    </div>

    <form method=post class='form_verifikasi hideit' id=form_sudah_benar>
        <div class='wadah mt2 m0'>
          <div>Poin penjawab: <span id=span_poin_penjawab__$id>200</span></div>
          <input type='range' class='form-range range poin_penjawab' min='-200' max='2000' id='poin_penjawab__$id' value='200' name=poin_penjawab>
        </div>
      <button class='btn btn-sm btn-success mt2' value=sudah_benar__$id name=btn_verifikasi>Sudah Benar</button>
    </form>

    <form method=post class='form_verifikasi hideit' id=form_add_jawaban>
      <textarea required minlength=10 maxlength=300 class='form-control mt1' name=jawaban_tambahan placeholder='Jawaban tambahan...'></textarea>
      <div class='wadah mt2 m0'>
        <div>Poin penjawab: <span id=span_poin_penjawab__$id>100</span></div>
        <input type='range' class='form-range range poin_penjawab' min='-200' max='2000' id='poin_penjawab__$id' value='100' name=poin_penjawab>
      </div>
      <button class='btn btn-sm btn-success mt2' value=add_jawaban__$id name=btn_verifikasi>Add Jawaban</button>
    </form>

    <form method=post class='form_verifikasi hideit' id=form_replace_jawaban>
      <textarea required minlength=10 maxlength=300 class='form-control mt1' name=jawaban placeholder='Replace jawaban...'></textarea>
      <div class='f12 abu mt1'>Poin penjawab: 0</div>
      <button class='btn btn-sm btn-secondary mt2' value=replace_jawaban__$id name=btn_verifikasi>Replace Jawaban</button>
    </form>
  ";

  $form_dev = $is_live ? '' : "
    <form method=post >
      <button class='btn btn-sm btn-danger mt2' value=reset_jawaban__$id name=btn_verifikasi>Reset Jawaban</button>
    </form>  

  ";

  if ($d['verif_by']) {
    $hari = hari_tanggal($d['verif_date'], 0);
    $eta = eta2($d['verif_date'], 1);
    $verified_by =  "<div class='border-top mt1 pt1 f12 abu miring'>verified by: $d[verifikator] at $hari | $eta $img_check</div>";
    $gradasi = 'hijau';
    if ($d['verifikator'] == $d['penjawab']) $by = '';
  } else {
    $gradasi = 'kuning';
    $verified_by = '';
  }

  # ============================================================
  # DELETE BERTANYA
  # ============================================================
  if ($id_role == 1 and $d['jawaban']) {
    $form_delete_bertanya = ''; // _peserta tidak bisa menghapus pertanyaan yg sudah dijawab
  } else {
    $form_delete_bertanya = "
      <form method=post style=display:inline>
        <button class='btn-transparan' onclick='return confirm(`Delete pertanyaan ini?`)' name=btn_delete_bertanya value=$id>
          $img_delete
        </button>
      </form>
    ";
  }


  if ($d['jawaban']) {
    $form_jawab = "
      <div style='display: grid; grid-template-columns: 35px auto; grid-gap: 5px;'>
        <div class='green'>Jwb:</div>
        <div class='green'>
          <div>$d[jawaban]</div>
          $by
          $form_verifikasi
          $verified_by
          $form_dev
        </div>
      </div>
    ";
  } else {

    if ($is_mine) {
      $form_jawab = "<div class='f14 miring abu mt1'>menunggu dijawab... </div>";
    } else {
      $range_poin = $id_role != 2 ? '' : "
        <div class='wadah mt2'>
          <div>Poin bertanya: <span id=span_poin__$id>500</span></div>
          <input type='range' class='form-range range range_poin' min='-200' max='5000' id='range_poin__$id' value='500' name=range_poin>
        </div>
      ";

      $dan_verifikasi = $id_role == 2 ? ' dan Verifikasi' : '';
      $w_100 = $id_role == 2 ? ' w-100' : '';
      $kamu = $id_role == 2 ? $Trainer : 'kamu';

      $form_jawab = "
        <form method=post>
          $range_poin
          <textarea required minlength=30 maxlength=300 class='form-control mt1' name=jawaban placeholder='Jawaban $kamu...'></textarea>
          <button class='btn btn-sm btn-primary mt2 $w_100' value=$id name=btn_submit_jawaban onclick='return confirm(`Submit Jawaban?`)'>Submit Jawaban$dan_verifikasi</button>
        </form>
      ";
    }
  }



  $wadah_active = $is_mine ? 'wadah_active' : 'wadah';
  $eta = eta2($d['tanggal']);
  $profil_penanya = $id_role != 2 ? '' : "<img src='$lokasi_profil/$d[war_image]' class='profil_pembuat'>";

  $tb .= "
    <div class='blok_tanya_jawab gradasi-$gradasi $wadah_active'>
      <div class='flexy flex-between'>
        <div class='kecil miring abu' style='padding-left: 25px'>
          <div>dari $d[penanya]</div>
          <div>sesi $d[nama_sesi]</div>
          <div>$eta</div>
        </div>
        <div>
          $profil_penanya
        </div>

      </div>
      <div class='blok_pertanyaan blok_grid darkblue' style='display: grid; grid-template-columns: 20px auto; grid-gap: 5px;'>
        <div class='smallsd kanan'>$i.</div>
        <div>
          $d[pertanyaan] $form_delete_bertanya 
          $form_jawab
        </div>
      </div>
    </div>
  ";
}

echo "
  <div data-aos='fade-up'>
    $tb
    <a href='?bertanya' class='btn btn-success w-100'>Ajukan Pertanyaan Baru</a>
  </div>
";
?>















<script>
  $(function() {
    let tags = $('#tags').text().split(', ');

    // console.log(tags);
    let my_tags = [];

    $('#pertanyaan').keyup(function() {
      let val = $(this).val();
      my_tags = [];
      // alert(val);
      tags.forEach(i => {
        if (val.search(i) >= 0) {
          my_tags.push(i)
        }
      });
      // console.log(my_tags);
      if (my_tags.length > 0) {

        $('#my_tags').text(my_tags.join(', '));
        $('#blok_info_tags').hide();
        $('#blok_my_tags').fadeIn();
        if (val.length >= 50) {
          $('#saya_menyatakan').slideDown();
          $('#length_info').text('');
          $('#length_info').fadeOut();
        } else {
          $('#saya_menyatakan').slideUp();
          $('#length_info').text('minimal 50 karakter, kamu mengetik ' + val.length + ' karakter.');
          $('#length_info').fadeIn();
        }

      } else {
        $('#my_tags').text('-');
        $('#blok_my_tags').hide();
        $('#blok_info_tags').fadeIn();
        $('#saya_menyatakan').slideUp();

      }

    })

    $('.cek_syarat').click(function() {
      let cek1 = $('#cek1').prop('checked');
      let cek2 = $('#cek2').prop('checked');

      let dis = cek1 && cek2 ? true : false;

      $('#btn_submit').prop('disabled', !(cek1 && cek2));
      console.log(cek1, cek2, dis);
    });

    $('.range_poin').dblclick(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
      console.log(aksi, id);

      let poin = prompt('Poin bertanya:', $(this).val());
      if (poin === null || poin === '') return;
      $(this).val(poin);
      $('#span_poin__' + id).text(poin);
    });

    $('.poin_penjawab').dblclick(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
      console.log(aksi, id);

      let poin = prompt('Poin menjawab:', $(this).val());
      if (poin === null || poin === '') return;
      $(this).val(poin);
      $('#span_poin_penjawab__' + id).text(poin);
    });

    $('.range_poin').change(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
      console.log(id);

      $('#span_poin__' + id).text($(this).val());
    })
    $('.poin_penjawab').change(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
      console.log(id);
      $('#span_poin_penjawab__' + id).text($(this).val());
    });

    $('.nav_verifikasi').click(function() {
      let tid = $(this).prop('id');
      $('.form_verifikasi').hide();
      $('#form_' + tid).fadeIn();
    })

  })
</script>