<?php
$tb = '';

$s = "SELECT a.*,b.nama as nama_sesi  
FROM tb_bertanya a 
JOIN tb_sesi b ON a.id_sesi=b.id 
WHERE a.id_penanya=$id_peserta 
ORDER BY a.tanggal desc";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $id = $d['id'];
  $tb .= "
    <div class='blok_tanya_jawab gradasi-kuning'>
      <div class='blok_pertanyaan blok_grid darkblue'>
        <div class='smallsd kanan'>$i.</div>
        <div>$d[pertanyaan]</div>
      </div>
      <div class='kecil miring abu blok_sesi upper mt1'>
        $d[nama_sesi]
      </div>
      <div class='blok_jawaban kecil miring abu'>
        -- no answer -- <span class=darkred>unverified</span> -- <span class='pointer darkblue more_info_toggle' id=more_info_toggle__$id>more info</span>
      </div>
      <div class='blok_info kecil miring abu hideit' id=blok_info__$id>
        <div>tags: <code>$d[tags]</code></div>
        <div>tanggal: <code>$d[tanggal]</code></div>
        <div>poin: <code>$d[poin]</code></div>
        <div>status: <code>$d[verif_status]</code></div>
      </div>
    </div>
  ";
}

?>
<style>
  .blok_sesi {
    margin-left: 30px
  }

  .blok_tanya_jawab {
    border: solid 1px #ddd;
    padding: 5px;
    border-radius: 5px;
    margin-bottom: 15px;
  }

  .blok_grid {
    display: grid;
    grid-template-columns: 25px auto;
    grid-gap: 5px;
  }

  .blok_jawaban {
    margin: 5px 0 5px 30px;
  }

  .more_info {
    padding: 10px;
    border: solid 1px #ddd;
    border-radius: 5px;
    margin: 0 5px 5px 30px;
  }
</style>

<div class="section-title" data-aos="fade-up">
  <h2>My-Question</h2>
  <p>Berikut adalah pertanyaan yang pernah kamu ajukan!</p>
</div>

<div class="wadah gradasi-hijau" data-aos='fade-up'>
  <?= $tb ?>
</div>

<div class="kanan" data-aos='fade-up'><a href="?bertanya" class='btn btn-success btn-sm'>Ajukan Pertanyaan Baru</a></div>














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
    })

    $('.more_info_toggle').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let id = rid[1];
      $('#blok_info__' + id).fadeToggle();
    })
  })
</script>