<?php
instruktur_only();
$null = '<span class="abu f12 miring consolas">null</span>';

$judul = 'Manage Soal';
set_title($judul);
$abjad = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'];
$path_gambar_soal = 'assets/img/gambar_soal';
echo "<h1>$judul</h1>";

if (isset($_POST['btn_simpan_soal'])) {
  // clean SQL
  foreach ($_POST as $key => $value) $_POST[$key] = clean_sql($value);

  // id for proses update
  $id_soal_for_update = $_POST['id_soal_for_update'];

  // unlink gambar for proses update
  $old_gambar_soal = $_POST['old_gambar_soal'];
  if ($id_soal_for_update && $old_gambar_soal) {
    $path = "$path_gambar_soal/$old_gambar_soal.jpg";
    if (file_exists($path)) {
      if (unlink($path)) {
        echo div_alert('success', 'File lama berhasil dihapus.');
      } else {
        echo div_alert('danger', 'Tidak bisa menghapus file lama. Proses dibatalkan.');
        exit;
      }
    } else {
      echo div_alert('danger', 'Perhatian! File Gambar sudah hilang. Proses dibatalkan.');
      exit;
    }
  }

  // cek jika plus gambar
  $new_gambar_soal = '';
  if ($_FILES) {
    $sub_kalimat_soal = substr($_POST['kalimat_soal'], 0, 10);
    $sub_kalimat_soal = strtolower(str_replace(' ', '', $sub_kalimat_soal));
    $new_gambar_soal = "$sub_kalimat_soal" . '_' . date('ymdHis');
    $new_path_gambar_soal = "$path_gambar_soal/$new_gambar_soal.jpg";

    echo '<pre>';
    var_dump($new_gambar_soal);
    echo '</pre>';

    // cek size
    $size = $_FILES['gambar_soal']['size'];
    if ($size < 30000 || $size > 204800) {
      echo div_alert('danger', 'Ukuran gambar yang diperbolehkan antara 30 s.d 200kB.');
    } else {
      // cek ekstensi
      if ($_FILES['gambar_soal']['type'] != 'image/jpeg') {
        echo div_alert('danger', 'Ekstensi harus JPG.');
      } else {
        //move upload file
        if (!move_uploaded_file($_FILES['gambar_soal']['tmp_name'], $new_path_gambar_soal)) {
          echo div_alert('danger', 'Gagal move upload file.');
        } else {
          echo div_alert('success', 'Upload gambar soal berhasil.');
        }
      }
    }
  } elseif ($_POST['radio_gambar_soal_yang_ada']) {
    $new_gambar_soal = $_POST['radio_gambar_soal_yang_ada'];
  }


  # =============================================
  # SAVE TO DB
  # =============================================
  $soal = $_POST['kalimat_soal'];
  $opsies = "$_POST[opsi_a]~~~$_POST[opsi_b]~~~$_POST[opsi_c]~~~$_POST[opsi_d]";
  $kj = $_POST['kj'];
  $pembahasan = $_POST['pembahasan'];
  $kjs = $_POST[$kj];
  $id_pembuat = $id_peserta;
  $tipe_soal = 'PG';
  $pembahasan_or_null = $pembahasan ? "'$pembahasan'" : 'NULL';
  $gambar_soal_or_null = $new_gambar_soal ? "'$new_gambar_soal'" : 'NULL';

  if ($id_soal_for_update) {
    $s = "UPDATE tb_soal SET 
    id_room,
    soal,
    opsies,
    kjs,
    id_pembuat,
    tipe_soal,
    pembahasan,
    gambar_soal
    
    WHERE id=$id_soal_for_update
    ";
  } else {
    $s = "INSERT INTO tb_soal (
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
  echo $s;

  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', 'Simpan data soal berhasil.');
  jsurl('', 1000);
  exit;
}

$s = "SELECT a.*, a.id as id_soal   
FROM tb_soal a 
WHERE a.id_room=$id_room 
AND tipe_soal='PG'";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q) == 0) {
  $tr = div_alert('danger', "Belum ada data soal untuk room ini.");
} else {
  $tr = '';
  $no = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $no++;
    $id_soal = $d['id_soal'];
    $arr = explode('~~~', $d['opsies']);
    $opsies = '';
    foreach ($arr as $key => $value) {
      $biru_tebal = $value == $d['kjs'] ? 'biru tebal' : '';
      $opsies .= "<div class='opsi_soal $biru_tebal'>$abjad[$key]. <span id=opsi_$abjad[$key]__$id_soal>$value</span></div>";
    }

    $pembahasan_show = $d['pembahasan'] ? "<div class='miring abu f14' id=pembahasan__$id_soal>$d[pembahasan]</div>" : $null;

    // gambar_soal_show
    $gambar_soal_show = '';
    if ($d['gambar_soal']) {
      $gambar_soal_show = "
        <div class='consolas miring abu mt1 mb2'>
        <span class=bg-yellow>Soal Bergambar</span> 
        <span class=show_soal_bergambar id=$d[gambar_soal]__$id_soal>$img_detail</span>
        </div>
        <div id=blok_gambar__$id_soal></div>
      ";
    }


    $tr .= "
      <tr class=tr_soal id=tr_soal__$id_soal>
        <td>$no</td>
        <td><span id=kalimat_soal__$id_soal>$d[soal]</span>$gambar_soal_show</td>
        <td>$opsies</td>
        <td>$pembahasan_show</td>
        <td width=50px class=tengah><span class=edit_soal id=edit_soal__$id_soal>$img_edit $img_delete</td>
      </tr>
    ";
  }
}

$list_gambar = div_alert('info', 'Belum ada media gambar pada room ini.');
$s = "SELECT gambar_soal FROM tb_soal WHERE gambar_soal IS NOT NULL AND id_room=$id_room";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q)) {
  $arr = [];
  while ($d = mysqli_fetch_assoc($q)) {
    if (!in_array($d['gambar_soal'], $arr)) {
      array_push($arr, $d['gambar_soal']);
    }
  }

  $li = '';
  foreach ($arr as $key => $value) {
    $li .= "
      <div class=mb2>
        <label class='pointer'>
          <input class=radio_gambar type=radio name=radio_gambar_soal_yang_ada value='$value'> 
          $value
        </label>
      </div>
    ";
  }

  $list_gambar = "
    <div class=mb2>
      <label class='pointer'>
        <input class=radio_gambar type=radio name=radio_gambar_soal_yang_ada checked value=''> 
        <span class='abu miring consolas'>none (tanpa gambar)</span>
      </label>
    </div>
    $li
  ";
}












































# ================================================ -->
# OPSI A,B,C,D SHOWS -->
# ================================================ -->
$tr_opsies = '';
foreach ($abjad as $huruf) {
  $HURUF = strtoupper($huruf);
  $tr_opsies .= "
    <tr>
      <td width=30px class='pb2 pr2 kanan'>$huruf.</td>
      <td>
        <input required class='form-control mb2' placeholder='opsi $huruf...' name=opsi_$huruf id=opsi_$huruf>
      </td>
      <td width=80px class='tengah pb2'><label class='btn btn-secondary btn-sm'><input required type=radio name=kj value=opsi_$huruf> KJ: $HURUF</label></td>
    </tr>
  ";
  if ($huruf == 'd') break;
}


# ================================================ -->
# BLOK TAMBAH SOAL
# ================================================ -->
$tr_tambah = "<tr><td colspan=100%>
  <div class=mb2><span class='btn_aksi pointer green f14' id=form_tambah_soal__toggle>$img_add <span class=Tambah>Tambah</span> Soal</span></div>
  <form method=post class='m0 hideita' id=form_tambah_soal enctype='multipart/form-data'>
    <input type=hiddena name=id_soal_for_update id=id_soal_for_update>
    <div class='wadah gradasi-hijau'>
      <div class=sub_form>Form <span class=Tambah>Tambah</span> Soal</div>
      <textarea required minlength=30 maxlength=500 class='form-control mt1 mb1' placeholder='Kalimat soal...' rows=5 name=kalimat_soal id=kalimat_soal></textarea>
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
              <input type=file class='form-control mb1' name=gambar_soal id=gambar_soal accept=.jpg>
              <div class='f12 abu miring mb4'>Gambar soal (opsional), format JPG, ukuran 30 s.d 200kB</div>
            </div>
          </div>
          <div class=col-lg-6>
            <div class=wadah>
              <div class=sub_form>Atau silahkan pilih gambar yang sudah ada:</div>
                $list_gambar
              </div>
            </div>
        </div>
      </div>


      <!-- ================================================ -->
      <!-- OPSI A,B,C,D SHOWS -->
      <!-- ================================================ -->
      <table width=100%>$tr_opsies</table>

      <!-- ================================================ -->
      <!-- PEMBAHASAN OPSIONAL -->
      <!-- ================================================ -->
      <div class=mb2><span class='btn_aksi f14 abu consolas pointer' id=blok_pembahasan__toggle>$img_add <span class=Tambah>Tambah</span> Pembahasan</span></div>
      <div class='hideit' id=blok_pembahasan>
        <textarea minlength=30 maxlength=500 class='form-control mt1 mb1' placeholder='Pembahasan soal...' rows=5 name=pembahasan></textarea>
        <div class='f12 abu miring mb4'>Silahkan ketik pembahasan soal 30 s.d 500 karakter. Jika tidak ada maka boleh dikosongkan.</div>
      </div>

      <button class='btn btn-primary w-100' name=btn_simpan_soal id=btn_simpan_soal>Simpan Soal</button>

    </div>
  </form>
</td></tr>";

# ================================================ -->
# FINAL ECHO
# ================================================ -->
echo "
  <table class=table>
    <thead>
      <td class=proper>no</td>
      <td class=proper>soal</td>
      <td class=proper>opsies</td>
      <td class=proper>pembahasan</td>
      <td class=proper>aksi</td>
    </thead>
    $tr
    $tr_tambah
  </table>
";





































?>
<script type="text/javascript">
  var uploadField = document.getElementById("gambar_soal");
  uploadField.onchange = function() {
    if (this.files[0].size < 30000) {
      alert("Ukuran File terlalu kecil, minimal 30KB agar resolusi gambar tidak pecah.");
      this.value = '';
    } else
    if (this.files[0].size > 204800) {
      alert("Ukuran File terlalu besar, maksimal 200KB agar kestabilan server terjaga. Silahkan Anda kecilkan ukurannya, atau Anda cari file lain yang lebih sesuai.");
      this.value = '';
    }
  };

  $(function() {
    $('.edit_soal').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id_soal = rid[1];

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
      $('#form_tambah_soal').slideDown();



    });
    $('.show_soal_bergambar').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id_soal = rid[1];
      $('#blok_gambar__' + id_soal).html(`<img src='assets/img/gambar_soal/${aksi}.jpg' class='img-fluid'>`);
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