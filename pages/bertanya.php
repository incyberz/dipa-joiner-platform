<?php
$pertanyaan = '';
if (isset($_POST['btn_posting_pertanyaan'])) {
  $pertanyaan = $_POST['pertanyaan'];
  if (strlen($pertanyaan) < 50) {

    echo 'Panjang pertanyaan kurang dari 50 huruf. Silahkan coba lagi!';
    jsurl('', 5000);
  }
  $pertanyaan = addslashes($pertanyaan);
  $s = "INSERT INTO tb_bertanya (
    id_sesi,
    id_room_kelas,
    id_penanya,
    pertanyaan,
    tags
  ) values (
    '$_POST[id_sesi]',
    $id_room_kelas,
    '$_POST[id_penanya]',
    '$pertanyaan',
    '$_POST[input_tags]'
  )";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', 'Submit pertanyaan sukses.');
  echo '<script>location.replace("?my_questions")</script>';
  exit;
}

# ============================================================
# IS SEDANG BERTANYA
# ============================================================
$s = "SELECT 1 FROM tb_bertanya WHERE id_penanya=$id_peserta AND verif_date is null";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$sedang_bertanya = mysqli_num_rows($q);
if ($sedang_bertanya) {
  $info_sesi = '';
  $form = div_alert('info', "Maaf, pertanyaan kamu belum dijawab atau belum diverifikasi oleh instruktur. Mohon bersabar hingga pertanyaan kamu dibahas!<hr><a class='btn btn-sm btn-primary' href='?questions'>List Bertanya</a>");
} else {
  $id_sesi = $_GET['id_sesi'] ?? '';
  $info_sesi = '';
  if ($id_sesi == '') {
    $pilih_sesi = '';
    $s = "SELECT id,tags,nama,no FROM tb_sesi WHERE jenis=1 AND id_room=$id_room order by no";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $i = 0;
    while ($d = mysqli_fetch_assoc($q)) {
      $i++;
      $r = explode(';', $d['tags']);
      sort($r);
      $imp = $d['tags'] == '' ? '<span class=red>belum bisa mengajukan pertanyaan karena belum ada tags sesi.</span>' : implode(', ', $r);
      $tags_show = "<div class='kecil miring abu'>$imp</div>";
      $danger = $d['tags'] == '' ? 'danger' : 'success';
      $href = $d['tags'] == ''
        ? "'#' onclick='alert(\"Maaf, belum bisa mengajukan pertanyaan pada sesi ini karena instuktur belum setting tags untuk sesi ini.\")'"
        : "'?bertanya&id_sesi=$d[id]'";
      $pilih_sesi .= "<div class=wadah><a class='btn btn-$danger btn-sm mb1' href=$href>P$i $d[nama]</a>$tags_show</div>";
    }

    $info_sesi = "
      <div class=mb2>Saya ingin bertanya tentang:</div>
      $pilih_sesi
    ";
    $form = '';
  } else {
    $s = "SELECT * FROM tb_sesi WHERE id=$id_sesi ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    if (mysqli_num_rows($q) == 0) die(erid('id_sesi (not found)'));
    $d = mysqli_fetch_assoc($q);
    $tags = $d['tags'];
    $nama_sesi = $d['nama'];
    $no_sesi = $d['no'];
    $img_refresh = img_icon('refresh');
    $info_sesi = "
      <div class='border-bottom mb2 pb2'>
        <div class='abu f12 mb1'>Topik:</div> 
        <div class='mb2'>$nama_sesi</div> 
        <a href='?bertanya'>$img_refresh</a>
      </div>
    ";

    $form = "
      <form method=post>
        <input class=debug name=id_penanya value=$id_peserta>
        <input class=debug name=id_sesi value=$id_sesi>
        <input class=debug name=input_tags id=input_tags value=input_tags>
        <div class=form-group>
          <label for=pertanyaan>Pertanyaan saya:</label>
          <textarea name=pertanyaan id=pertanyaan class='form-control mt2' rows=6>$pertanyaan</textarea>
          <div class='small miring abu mt2 mb1' id=blok_info_tags>
            Referensi tags: 
            <div id=tags class=biru>$tags</div>
            <div class='red bold mt2'>Pertanyaan kamu wajib mengandung salah satu tag diatas.</div>
          </div>
          <div class='small miring abu mt1 hideit' id=blok_my_tags>tags: <span id=my_tags class='tebal biru kecil'>my_tags</span></div>
          <div class='red bold miring' id=length_info></div>
        </div>
  
        <div id=saya_menyatakan class='hideit mt2'>
          <div class='kecil abu miring wadah'>
            estimasi point: 
            <div class='darkblue bold f18'>-200 s.d 5.000 LP</div>
          </div>
  
          <div class='form-group f14 left mb4'>
            <label>Saya menyatakan bahwa:</label>
            <div class=''>
              <label><input class='cek_syarat' type=checkbox id=cek1> Pertanyaan saya tidak asal-asalan</label>
              <br><label><input class='cek_syarat' type=checkbox id=cek2> Pertanyaan sudah sesuai dg topik</label>
            </div>
          </div>
  
          <div class=form-group>
            <button name=btn_posting_pertanyaan id=btn_posting_pertanyaan class='btn btn-primary btn-block' disabled>Posting Pertanyaan</button>
          </div>
        </div>
  
      </form>
    ";
  }
}


set_h2('Bertanya', '<span class="green f12">Malu bertanya, sesat IPK :)</span>');

echo "
  <div class='wadah gradasi-hijau tengah' data-aos='fade'>
    $info_sesi
    $form
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

        $('#input_tags').val(my_tags.join(', '));
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

      $('#btn_posting_pertanyaan').prop('disabled', !(cek1 && cek2));
      console.log(cek1, cek2, dis);
    })
  })
</script>