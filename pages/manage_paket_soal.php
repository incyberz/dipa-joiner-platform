<?php
instruktur_only();
$null = '<span class="abu f12 miring consolas">null</span>';

$judul = 'Manage Paket Soal';
set_title($judul);
// $abjad = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'];
// $path_gambar_soal = 'assets/img/gambar_soal';
echo "<h1>$judul</h1>";

















if (isset($_POST['btn_delete_paket_soal '])) {
  $id = $_POST['btn_delete_paket_soal '];
  if ($id) {
    $s = "DELETE FROM tb_paket_soal WHERE id=$id";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo div_alert('success', 'Delete soal berhasil.');
    jsurl('', 1000);
  }
}


if (isset($_POST['btn_simpan_soal'])) {

  // clean SQL
  foreach ($_POST as $key => $value) $_POST[$key] = clean_sql($value);

  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';




  # =============================================
  # SAVE TO DB
  # =============================================
  $soal = $_POST['kalimat_soal'];
  $opsies = "$_POST[opsi_a]~~~$_POST[opsi_b]~~~$_POST[opsi_c]~~~$_POST[opsi_d]";
  $kj = $_POST['kj'];
  $pembahasan = $_POST['pembahasan'];
  $kjs = $_POST[$kj];
  $id_pembuat = $id_peserta;
  $tipe_paket_soal = 'PG';
  $pembahasan_or_null = $pembahasan ? "'$pembahasan'" : 'NULL';

  // SQL Update or Insert
  if ($id_soal_for_update) {
    $s = "UPDATE tb_paket_soal SET 
      soal='$soal',
      opsies='$opsies',
      kjs='$kjs',
      pembahasan=$pembahasan_or_null,
      $sql_update_gambar_soal
    WHERE id=$id_soal_for_update
    ";
  } else {
    $s = "INSERT INTO tb_paket_soal (
      id_room,
      soal,
      opsies,
      kjs,
      id_pembuat,
      tipe_soal,
      pembahasan,
      gambar_soal
    ) VALUES (
      $id_room,
      '$soal',
      '$opsies',
      '$kjs',
      $id_pembuat,
      '$tipe_soal',
      $pembahasan_or_null,
      $gambar_soal_or_null
    )";
  }
  // echo '<pre>';
  // var_dump($s);
  // echo '</pre>';

  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', 'Simpan data paket soal berhasil.');
  jsurl('', 2000);
  exit;
}






































# =============================================
# MAIN SELECT
# =============================================
$s = "SELECT a.*, 
a.id as id_soal,
(
  SELECT COUNT(1) FROM tb_assign_soal 
  WHERE id_paket_soal=a.id) count_assign 
FROM tb_paket_soal a 
WHERE a.id_room=$id_room 
ORDER BY date_created";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q) == 0) {
  $tr = div_alert('danger', "Belum ada data paket soal untuk room ini.");
} else {
  $tr = '';
  $no = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $no++;
    $id_paket_soal = $d['id_soal'];
    $arr = explode('~~~', $d['opsies']);
    $opsies = '';
    foreach ($arr as $key => $value) {
      $biru_tebal = $value == $d['kjs'] ? 'biru tebal' : '';
      $opsies .= "<div class='opsi_paket_soal $biru_tebal'>$abjad[$key]. <span id=opsi_$abjad[$key]__$id_soal>$value</span></div>";
    }

    $pembahasan_show = $d['pembahasan'] ? "<div class='miring abu f14' id=pembahasan__$id_soal>$d[pembahasan]</div>" : $null;

    $count_assign = $d['count_assign'];
    $list_paket = $null;
    if ($count_assign) {
      $s2 = "SELECT b.nama as nama_paket_paket_soal 
      FROM tb_assign_paket_soal a 
      JOIN tb_paket_paket_soal b ON a.id_paket_soal=b.id 
      WHERE a.id_soal=$id_soal";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
      $li = '';
      while ($d2 = mysqli_fetch_assoc($q2)) {
        $li .= "<li>$d2[nama_paket_soal]</li>";
      }
      $list_paket = "<ol>$li</ol>";
    }


    $tr .= "
      <tr class=tr_paket_soal id=tr_soal__$id_soal>
        <td>$no</td>
        <td><span id=kalimat_soal__$id_soal>$d[soal]</span>$gambar_soal_show</td>
        <td>$opsies</td>
        <td>$pembahasan_show</td>
        <td>$list_paket</td>
        <td width=50px class=tengah>
          <span class=edit_paket_soal id=edit_soal__$id_soal>$img_edit</span> 
          <form method=post style='display:inline'>
            <button class='p0 m0' name=btn_delete_paket_soal style='display:inline; background:none; border:none' onclick='return confirm(\"Yakin untuk hapus soal ini?\")' value=$id_soal>$img_delete</button>
          </form>
        </td>
      </tr>
    ";
  }
}

















































# ================================================ -->
# BLOK TAMBAH SOAL
# ================================================ -->
$tr_tambah = "<tr><td colspan=100%>
  <div class='mb2 flexy flex-between'>
    <div>
      <span class='btn_aksi pointer green f14' id=form_tambah_soal__toggle>
        $img_add 
        <span class=Tambah>Tambah</span> Paket Soal
      </span>
    </div>
    <div id=cancel_update class=hideit>
      <a class='btn btn-danger btn-sm' href='?manage_soal&mode=pg'>Cancel Update</a>
    </div>
  </div>
  <form method=post class='m0 hideit' id=form_tambah_paket_soal enctype='multipart/form-data'>
    <input type=hidden name=id_soal_for_update id=id_soal_for_update>
    <div class='wadah gradasi-hijau'>
      <div class=sub_form>Form <span class=Tambah>Tambah</span> Paket Soal</div>
      <textarea required minlength=30 maxlength=500 class='form-control mt1 mb1' placeholder='Kalimat soal...' rows=5 name=kalimat_paket_soal id=kalimat_soal></textarea>
      <div class='f12 abu miring mb4'>Silahkan ketik kalimat soal 30 s.d 500 karakter.</div>

      <!-- ================================================ -->
      <!-- TAMBAH GAMBAR SOAL -->
      <!-- ================================================ -->
      <div class=mb2><span class='btn_aksi f14 abu consolas pointer' id=blok_tambah_gambar__toggle>$img_add <span class=Tambah>Tambah</span> Gambar</span></div>
      <div class='hideit wadah' id=blok_tambah_gambar>
        <div class=row>
          <div class=col-lg-6>
            <div class=wadah>
              <div class=sub_form id=upload_gambar_baru>Upload Gambar Baru</div>
              <input type=file class='form-control mb1' name=gambar_paket_soal id=gambar_paket_soal accept=.jpg>
              <div class='f12 abu miring mb4'>Gambar soal (opsional), format JPG, ukuran 30 s.d 200kB</div>
            </div>
          </div>
          <div class=col-lg-6>
            <div class=wadah>
              <div class=sub_form>Atau silahkan pilih gambar yang sudah ada:</div>
                $ list_gambar
              </div>
            </div>
        </div>
      </div>

      <button class='btn btn-primary w-100' name=btn_simpan_paket_soal id=btn_simpan_soal>Simpan Paket Soal</button>

    </div>
  </form>
</td></tr>";

# ================================================ -->
# FINAL ECHO
# ================================================ -->
echo "
  <table class=table>
    <thead class=gradasi-toska>
      <th class=proper>no</th>
      <th class=proper>Untuk Event</th>
      <th class=proper>Nama Paket Soal</th>
      <th class=proper>Untuk Kelas</th>
      <th class=proper>Awal Ujian</th>
      <th class=proper>Akhir Ujian</th>
      <th class=proper>Tanggal Tampil Pembahasan</th>
      <th class=proper>Sifat Ujian</th>
      <th class=proper>Kisi-kisi</th>
      <th class=proper>Jumlah Maksimum Mencoba</th>
      <th class=proper>aksi</th>
    </thead>
    $tr
    $tr_tambah
  </table>
";





































?>
<script type="text/javascript">
  $(function() {
    $('.edit_soal').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id_paket_soal = rid[1];

      // hide all tr and show this
      $('.tr_soal').hide();
      $('#tr_soal__' + id_soal).show();

      // fill id_soal_for_update
      $('#id_soal_for_update').val(id_soal);
      // fill values
      $('#kalimat_soal').val($('#kalimat_soal__' + id_soal).text());
      $('#opsi_a').val($('#opsi_a__' + id_soal).text());
      $('#opsi_b').val($('#opsi_b__' + id_soal).text());
      $('#opsi_c').val($('#opsi_c__' + id_soal).text());
      $('#opsi_d').val($('#opsi_d__' + id_soal).text());

      // change caption
      $('.Tambah').text('Update');
      $('#btn_simpan_soal').text('Update Soal');
      $('#upload_gambar_baru').text('Replace Gambar dengan:');

      // show cancel and form
      $('#cancel_update').show();
      $('#form_tambah_soal').slideDown();

    });
    $('.show_soal_bergambar').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id_paket_soal = rid[1];

      console.log(aksi, id_soal);

      $('#blok_gambar__' + id_soal).html(`<img src='assets/img/gambar_soal/${aksi}.jpg' class='img-fluid'>`);
      $('#form_rename_gambar__' + id_soal).fadeIn();
      console.log("$('#form_rename_gambar__' + id_soal).fadeIn()");;
      $(this).fadeOut();
    });
    $('.radio_gambar').click(function() {
      let val = $(this).val();
      if (val) {
        $('#gambar_soal').prop('disabled', 1);
      } else {
        $('#gambar_soal').prop('disabled', 0);
      }
    })
  })
</script>