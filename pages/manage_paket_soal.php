<?php
instruktur_only();
$null = '<span class="abu f12 miring consolas">null</span>';

$judul = 'Manage Paket Soal';
set_title($judul);
// $abjad = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'];
// $path_gambar_paket_soal = 'assets/img/gambar_paket_soal';
echo "<h1>$judul</h1>
  <div class=mb2><a href='?ujian'>Ujian Home</a> | <a href='?manage_soal'>Manage Soal</a></div>
";

















if (isset($_POST['btn_delete_paket_soal'])) {
  $id = $_POST['btn_delete_paket_soal'];
  if ($id) {
    $s = "DELETE FROM tb_paket_soal WHERE id=$id";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo div_alert('success', 'Delete paket berhasil.');
    jsurl('', 1000);
  }
}


if (isset($_POST['btn_simpan_paket_soal'])) {
  // clean SQL
  foreach ($_POST as $key => $value) $_POST[$key] = clean_sql($value);
  # =============================================
  # SAVE TO DB
  # =============================================
  $id_paket_for_update = $_POST['id_paket_soal_for_update'];
  $nama = $_POST['nama_paket'];
  $max_attemp = $_POST['max_attemp'];
  $untuk_kelas = $_POST['untuk_kelas'];
  $awal_ujian = $_POST['awal_ujian'];
  $akhir_ujian = $_POST['akhir_ujian'];
  $tanggal_ujian = $_POST['tanggal_ujian'];
  $tanggal_pembahasan = $_POST['tanggal_pembahasan'];
  $awal_pembahasan = $_POST['awal_pembahasan'];
  $kode_sesi = $_POST['kode_sesi'];
  $sifat_ujian = $_POST['sifat_ujian'];
  $kisi_kisi = $_POST['kisi_kisi'];
  $id_pembuat = $id_peserta;
  $kisi_kisi_or_null = $kisi_kisi ? "'$kisi_kisi'" : 'NULL';

  // SQL Update or Insert
  if ($id_paket_for_update) {
    $s = "UPDATE tb_paket_soal SET 
      soal='$soal',
      opsies='$opsies',
      kjs='$kjs',
      pembahasan=$pembahasan_or_null,
      $sql_update_gambar_paket_soal
    WHERE id=$id_paket_for_update
    ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo div_alert('success', 'Update paket soal berhasil.');
    jsurl('', 1000);
    exit;
  } else {
    if ($untuk_kelas == 'all') {
      $s = "SELECT a.kelas FROM tb_room_kelas a 
      JOIN tb_kelas b ON a.kelas=b.kelas 
      WHERE a.id_room=$id_room AND b.status=1 ";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      $arr_untuk_kelas = [];
      while ($d = mysqli_fetch_assoc($q)) {
        array_push($arr_untuk_kelas, $d['kelas']);
      }
    } else {
      $arr_untuk_kelas = [$untuk_kelas];
    }

    foreach ($arr_untuk_kelas as $key => $untuk_kelas) {
      $s = "INSERT INTO tb_paket_soal (
        id_room,
        nama,
        kelas,
        id_pembuat,
        awal_ujian,
        akhir_ujian,
        tanggal_pembahasan,
        kode_sesi,
        sifat_ujian,
        kisi_kisi,
        max_attemp
      ) VALUES (
        $id_room,
        '$nama',
        '$untuk_kelas',
        $id_pembuat,
        '$tanggal_ujian $awal_ujian',
        '$tanggal_ujian $akhir_ujian',
        '$tanggal_pembahasan $awal_pembahasan',
        '$kode_sesi',
        '$sifat_ujian',
        $kisi_kisi_or_null,
        $max_attemp
      )";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      echo div_alert('success', "Simpan paket untuk kelas $untuk_kelas berhasil.");
    }
    jsurl('', 2000);
    exit;
  }
}






































# =============================================
# MAIN SELECT
# =============================================
$s = "SELECT  
a.id as id_paket,
a.nama as nama_paket,
a.kelas as untuk_kelas,
a.awal_ujian,
a.akhir_ujian,
a.tanggal_pembahasan,
a.max_attemp,
b.nama as untuk_event,
(
  SELECT COUNT(1) FROM tb_assign_soal 
  WHERE id_paket_soal=a.id) count_soal, 
(
  SELECT COUNT(1) FROM tb_kelas_peserta p 
  JOIN tb_peserta q ON p.id_peserta=q.id  
  WHERE p.kelas=a.kelas 
  AND q.status=1 
  AND q.nama not like '%dummy%') count_peserta

FROM tb_paket_soal a 
JOIN tb_kode_sesi b ON a.kode_sesi=b.kode_sesi
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
    $id_paket = $d['id_paket'];
    $count_soal = $d['count_soal'];
    $untuk_event = $d['untuk_event'];
    $nama_paket = $d['nama_paket'];
    $max_attemp = $d['max_attemp'];
    $awal_ujian = $d['awal_ujian'];
    $akhir_ujian = $d['akhir_ujian'];
    $count_peserta = $d['count_peserta'];
    $tanggal_pembahasan = $d['tanggal_pembahasan'];
    $tanggal_ujian = date('Y-m-d', strtotime($d['awal_ujian']));
    $tanggal_ujian_show = date('d M Y', strtotime($tanggal_ujian));
    $tanggal_pembahasan_show = date('d-M H:i', strtotime($tanggal_pembahasan));
    $awal_ujian_show = date('H:i', strtotime($awal_ujian));
    $akhir_ujian_show = date('H:i', strtotime($akhir_ujian));




    # =============================================
    # FINAL TR
    # =============================================
    $tr .= "
      <tr class=tr_paket_soal id=tr_paket_soal__$id_paket>
        <td>$no</td>
        <td>
          $untuk_event
          <div class='f12 abu'>Tanggal: $tanggal_ujian_show</div>
          <div class='f12 abu'>Pukul: $awal_ujian_show s.d $akhir_ujian_show</div>
        </td>
        <td>
          $d[untuk_kelas]
          <div class='f12 abu'>$count_peserta peserta</div>
        </td>
        <td>
          $nama_paket
          <div class='f12 abu'>Tampil Pembahasan: $tanggal_pembahasan_show</div>
          <div class='f12 abu'>Max Attemp: $max_attemp kali</div>
        </td>
        <td>
          $d[count_soal] soal | <a href='?assign_soal&id_paket=$id_paket'>Assign</a>
        </td>
        <td width=50px class=tengah>
          <span class=edit_paket_soal id=edit_paket_soal__$id_paket>$img_edit</span> 
          <form method=post style='display:inline'>
            <button class='p0 m0' name=btn_delete_paket_soal style='display:inline; background:none; border:none' onclick='return confirm(\"Yakin untuk hapus paket ini?\")' value=$id_paket>$img_delete</button>
          </form>
        </td>
      </tr>
    ";
  }
}

















































# ================================================ -->
# SELECT KODE SESI
# ================================================ -->
$s = "SELECT * FROM tb_kode_sesi ";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$opt = '';
while ($d = mysqli_fetch_assoc($q)) {
  $selected = $d['kode_sesi'] == 'uts' ? 'selected' : '';
  $opt .= "<option value='$d[kode_sesi]' $selected>Untuk event $d[nama]</option>";
}
$select_kode_sesi = "<select name=kode_sesi id=kode_sesi class='form-control mb2'>$opt</select>";

# ================================================ -->
# SELECT KELAS DAN MISAL NAMA PAKET
# ================================================ -->
$s = "SELECT a.kelas,b.semester,b.prodi FROM tb_room_kelas a 
JOIN tb_kelas b ON a.kelas=b.kelas
WHERE id_room=$id_room ";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$opt = '<option value=all>Untuk Semua Kelas pada Room ini</option>';
$info_prodi = '';
$info_semester = '';
while ($d = mysqli_fetch_assoc($q)) {
  $info_prodi = $d['prodi'];
  $info_semester = $d['semester'];
  $opt .= "<option value='$d[kelas]' >Untuk Kelas $d[kelas]</option>";
}
$select_kelas = "<select name=untuk_kelas id=untuk_kelas class='form-control mb2'>$opt</select>";
$gg = $tahun_ajar % 2 == 0 ? 'Genap' : 'Ganjil';
$ta_gg = substr($tahun_ajar, 0, 4) . ' ' . $gg;
$misal_nama_paket = "UTS $room $info_prodi Semester $info_semester TA. $ta_gg";


# ================================================ -->
# BLOK TAMBAH PAKET SOAL
# ================================================ -->
$tr_tambah = "<tr><td colspan=100%>
  <div class='mb2 flexy flex-between'>
    <div>
      <span class='btn_aksi pointer green f14' id=form_tambah_paket_soal__toggle>
        $img_add 
        <span class=Tambah>Tambah</span> Paket Soal
      </span>
    </div>
    <div id=cancel_update class=hideita>
      <a class='btn btn-danger btn-sm' href='?manage_paket_soal&mode=pg'>Cancel Update</a>
    </div>
  </div>


  <!-- ================================================ -->
  <!-- FORM TAMBAH PAKET SOAL -->
  <!-- ================================================ -->
  <form method=post class='m0 hideita' id=form_tambah_paket_soal enctype='multipart/form-data'>
    <input type=hiddena name=id_paket_soal_for_update id=id_paket_soal_for_update>
    <div class='wadah gradasi-hijau'>
      <div class='sub_form mb4'>Form <span class=Tambah>Tambah</span> Paket Soal</div>

      <input required minlength=10 maxlength=50 class='form-control mb2' placeholder='Nama Paket Soal...' name=nama_paket id=nama_paket>
      <div class='f12 abu mb4'>Misal: <span class='darkblue miring pointer' id=misal_nama_paket>$misal_nama_paket</span></div>
      $select_kode_sesi
      $select_kelas
      <div class=wadah>
        <div class=sub_form>Waktu Ujian</div>
        <div class='flexy' style='flex-wrap:wrap'>
          <div>
            <input required type=date value='$today' class='form-control mb2' name=tanggal_ujian>
          </div>
          <div>
            <input required type=time value='07:30' class='form-control mb2' name=awal_ujian>
          </div>
          <div class=pt2>s.d</div>
          <div>
            <input required type=time value='09:00' class='form-control mb2' name=akhir_ujian>
          </div>
        </div>
      </div>
      <div class=wadah>
        <div class=sub_form>Waktu Tampil Pembahasan Soal</div>
        <div class=flexy>
          <div>
            <input required type=date value='$today' class='form-control mb2' name=tanggal_pembahasan id=tanggal_pembahasan>
          </div>
          <div>
            <input required type=time value='09:10' class='form-control mb2' name=awal_pembahasan id=awal_pembahasan>
          </div>
        </div>
        <div class='mb4 f12 abu'>Pada tanggal ini peserta akan dapat melihat Kunci Jawaban dan Pembahasan soal (jika ada)</div>
        <label class=f14>
          <input type=checkbox id=jangan_tampilkan_kj> Jangan tampilkan Kunci Jawaban dan pembahasan soal setelah ujian
        </label>
      </div>
      <select class='form-control mb2' name=sifat_ujian>
        <option>Sifat Ujian Close Book</option>
        <option>Sifat Ujian Open Book</option>
        <option>Sifat Ujian Close Book, Open Kalkulator</option>
        <option>Sifat Ujian Open Book, Open Kalkulator</option>
        <option>Sifat Ujian Open Book, Open Internet</option>
      </select>
      <textarea class='form-control mb2' placeholder='Kisi-kisi ujian (opsional)... akan bisa dilihat oleh peserta sebelum ujian berlangsung.' rows=4 name=kisi_kisi></textarea>

      <div class=wadah>
        <div class=sub_form>Jumlah Maksimum Mencoba (Attemp) :: Selama waktu ujian masih ada peserta boleh mengerjakan sebanyak</div>
        <select class='form-control' name=max_attemp>
          <option value=1>1 kali (tidak bisa mengulang)</option>
          <option value=2 selected>2 kali mencoba</option>
          <option value=3>3 kali mencoba</option>
          <option value=4>4 kali mencoba</option>
          <option value=5>5 kali mencoba</option>
        </select>
      </div>

      <div class=wadah>
        <div class=sub_form>Polling System</div>
        <select class='form-control' name=wajib_polling>
          <option value=0>Tidak wajib polling untuk melihat Hasil Ujian</option>
          <option value=uts>Wajib Poling UTS</option>
          <option value=uas>Wajib Poling UAS</option>
        </select>
      </div>

      <button class='btn btn-primary w-100' name=btn_simpan_paket_soal id=btn_simpan_paket_soal>Simpan Paket Soal</button>

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
      <th class=proper>Untuk Kelas</th>
      <th class=proper>Paket Soal</th>
      <th class=proper>Assign</th>
      <th class=proper>aksi</th>
    </thead>
    $tr
    $tr_tambah
  </table>
";





































?>
<script type="text/javascript">
  $(function() {
    $('#jangan_tampilkan_kj').click(function() {
      let val = $(this).prop('checked');
      console.log(val);
      $('#tanggal_pembahasan').prop('disabled', val);
      $('#awal_pembahasan').prop('disabled', val);
    });

    $('#misal_nama_paket').click(function() {
      $('#nama_paket').val($(this).text());
    });

    $('.edit_paket_soal').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id_paket_soal = rid[1];

      // hide all tr and show this
      $('.tr_paket_soal').hide();
      $('#tr_paket_soal__' + id_paket_soal).show();

      // fill id_paket_soal_for_update
      $('#id_paket_soal_for_update').val(id_paket_soal);
      // fill values
      $('#kalimat_paket_soal').val($('#kalimat_paket_soal__' + id_paket_soal).text());
      $('#opsi_a').val($('#opsi_a__' + id_paket_soal).text());
      $('#opsi_b').val($('#opsi_b__' + id_paket_soal).text());
      $('#opsi_c').val($('#opsi_c__' + id_paket_soal).text());
      $('#opsi_d').val($('#opsi_d__' + id_paket_soal).text());

      // change caption
      $('.Tambah').text('Update');
      $('#btn_simpan_paket_soal').text('Update Soal');
      $('#upload_gambar_baru').text('Replace Gambar dengan:');

      // show cancel and form
      $('#cancel_update').show();
      $('#form_tambah_paket_soal').slideDown();

    });
    $('.show_paket_soal_bergambar').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id_paket_soal = rid[1];

      console.log(aksi, id_paket_soal);

      $('#blok_gambar__' + id_paket_soal).html(`<img src='assets/img/gambar_paket_soal/${aksi}.jpg' class='img-fluid'>`);
      $('#form_rename_gambar__' + id_paket_soal).fadeIn();
      console.log("$('#form_rename_gambar__' + id_paket_soal).fadeIn()");;
      $(this).fadeOut();
    });
    $('.radio_gambar').click(function() {
      let val = $(this).val();
      if (val) {
        $('#gambar_paket_soal').prop('disabled', 1);
      } else {
        $('#gambar_paket_soal').prop('disabled', 0);
      }
    })
  })
</script>