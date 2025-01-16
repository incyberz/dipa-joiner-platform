<?php
$img_manage = img_icon('manage');
$img_next = img_icon('next');

if (isset($_POST['btn_add_indikator'])) {
  $indikator_baru = $_POST['indikator_baru'];
  $title = mysqli_real_escape_string($cn, $_POST['title']);

  $s = "INSERT INTO tb_indikator (
    indikator, 
    id_room,
    title
  ) VALUES (
    '$indikator_baru', 
    $id_room,
    '$title' 
  )";
  mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
}

$indikator = '';
$s = "SELECT a.*,
(SELECT COUNT(1) FROM tb_bukti_proyek WHERE id_indikator=a.id) count_bukti ,
(SELECT COUNT(1) FROM tb_bukti_proyek WHERE id_indikator=a.id AND verif_at is not null) count_verified 
FROM tb_indikator a 
WHERE a.id_room=$id_room";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$i = 0;
if (!mysqli_num_rows($q)) {
  $indikator = div_alert('danger tengah', "Belum ada satupun [ Indikator Proyek ]");
} else {
  $indikator .= "
    <div class='bold d-none d-sm-block'>
      <div class='row gradasi-toska border-bottom p1'>
        <div class='col-sm-3'>
          Indikator Proyek
        </div>
        <div class='col-sm-4'>
          Title / Penjelasan
        </div>
        <div class='col-sm-3'>
          Poin
        </div>
        <div class='col-sm-1'>
          Verifikasi Bukti
        </div>
        <div class='col-sm-1'>
          Aksi
        </div>
      </div>
    </div>
  ";
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $light = $i % 2 == 0 ? 'abu' : 'white';
    $btn_delete = $d['count_bukti'] ? "
      <span onclick='alert(`Tidak bisa hapus indikator karena sudah ada bukti pengumpulan.`)'>$img_delete_disabled</span>
    " : "
      <span class='btn_save' id=delete__row__$d[id]>$img_delete</span>
    ";

    $lihat_bukti = $d['count_bukti'] ? "<a href='?proyek_akhir&aksi=lihat_bukti&id_indikator=$d[id]'>$img_next</a>" : '';

    $light = ($d['count_bukti'] && $d['count_bukti'] != $d['count_verified']) ? 'red' : $light;

    $indikator .= "
      <div class=''>
        <div class='row bg-$light py-4 py-sm-2'>
          <div class='col-sm-3 py-2'>
            $i. $d[indikator]
          </div>
          <div class='col-sm-4 py-2'>
            <div class='d-flex gap-2'>
              <div class='bold d-sm-none'>Title:</div>
              <div class='input_editable' id=title__$d[id]>$d[title]</div>
            </div>
          </div>
          <div class='col-sm-3 py-2'>
            <div class='d-flex gap-2'>
              <div class='bold d-sm-none'>Poin:</div>
              <div class=input_editable id=min_poin__$d[id]>$d[min_poin]</div>
              <div>
                s.d
              </div>
              <div  class=input_editable id=max_poin__$d[id]>$d[max_poin]</div>
            </div>
          </div>
          <div class='col-sm-1 py-2'>
            <div class='d-flex gap-2'>
              <div class='bold d-sm-none'>Bukti:</div>
              <div>
                $d[count_verified] of $d[count_bukti] $lihat_bukti
              </div>
            </div>
          </div>
          <div class='col-sm-1 py-2'>
            $btn_delete
          </div>
        </div>
      </div>
    ";
  }
}

$add_indikator = "
  <form method=post class='mt4'>
    <div class=row>
      <div class='col-md-4'>
        <input required minlength=5 maxlength=20 class='form-control' placeholder='Indikator baru...' id=indikator_baru name=indikator_baru>
        <div class='f12 abu mt1 mb2 ml1'>tanpa spasi (boleh pakai underscore)</div>
      </div>
      <div class='col-md-6'>
        <input required minlength=5 maxlength=100 class='form-control' placeholder='Title Indikator...' name=title>
        <div class='f12 abu mt1 mb2 ml1'>)* penjelas singkat indikator</div>
      </div>
      <div class='col-md-2'>
        <button class='btn btn-primary w-100' name=btn_add_indikator>Add</button>
      </div>
    </div>
  </form>
";

echo "
  <div class=mb2>
    <span class='btn btn-sm bordered btn_aksi' id=manage_proyek_akhir__toggle>$img_manage Manage Proyek Akhir</span>
  </div>
  <div class='wadahs mt2 gradasi-kunings mb4' id=manage_proyek_akhir>
    $indikator
    $add_indikator
  </div>
  <hr>
";

?>
<script>
  $(function() {
    $('#indikator_baru').keyup(function() {
      $(this).val(
        $(this).val()
        .trim()
        .toLowerCase()
        .replace(/ /g, '')
        .replace(/[!@#$%^&*()+\-=\[\]{}.,;:'`"\\|<>\/?~]/gim, '')
      );

    });

    $('.input_editable').click(function() {
      let isi_lama = $(this).text();
      let isi_baru = prompt('Isi baru', isi_lama);
      // if (!isi_baru) return; // tidak boleh null
      if (isi_baru.trim() == isi_lama) return;
      if (isi_baru == '') {
        let y = confirm('Ingin mengosongkan data?');
        if (!y) {
          $('#' + tid).val(isi_lama); // rollback value
          return;
        } else {
          isi_baru = 'null';
        }
      }

      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let field = rid[0];
      let id = rid[1];
      let aksi = 'ubah';

      let field_id_value = id;
      let field_target = field;
      console.log(field, id, isi_baru, isi_lama);


      // manage tags
      if (field == 'tags') {
        isi_baru = isi_baru
          .replace(/;/gim, ',')
          .replace(/[!@#$%^&*()+\-=\[\]{};:'`"\\|<>\/?~]/gim, '');
        let r = isi_baru.split(',');

        let r2 = [];
        r.forEach(el => {
          el = el.trim().toLowerCase();
          if (el) r2.push(el);
        });

        isi_baru = r2.sort().join(', ');
      }

      let link_ajax = 'ajax/ajax_crud.php?aksi=' + aksi +
        '&tb=indikator' +
        '&field_id_value=' + field_id_value +
        '&field_target=' + field_target +
        '&isi_baru=' + isi_baru;
      $.ajax({
        url: link_ajax,
        success: function(a) {
          if (a.trim() == 'sukses') {
            $('#' + tid).text(isi_baru);
          } else {
            alert(a)
          }
        }
      })

    });

  })
</script>