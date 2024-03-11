<?php
instruktur_only();
$null = '<span class="abu f12 miring consolas">null</span>';

$judul = 'Tambah Soal PG';
set_title($judul);
$abjad = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'];

echo "<h1>$judul</h1>";

$s = "SELECT a.*  
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
    $arr = explode('~~~', $d['opsies']);
    $opsies = '';
    foreach ($arr as $key => $value) {
      $biru_tebal = $value == $d['kjs'] ? 'biru tebal' : '';
      $opsies .= "<div class='opsi_soal $biru_tebal'>$abjad[$key]. $value $value $value $value</div>";
    }

    $pembahasan_show = $d['pembahasan'] ? "<div class='miring abu f14'>$d[pembahasan]</div>" : $null;

    $tr .= "
      <tr>
        <td>$no</td>
        <td>$d[soal]</td>
        <td>$opsies</td>
        <td>$pembahasan_show</td>
        <td width=50px class=tengah>$img_edit $img_delete</td>
      </tr>
      <tr class='hideit'>
        <td colspan=100%>
          BLOK EDIT SOAL
        </td>
      </tr>

    ";
  }
}

$tr_tambah = "<tr><td colspan=100%>
  <div class=mb2><span class='btn_aksi pointer green f14' id=form_tambah_soal__toggle>$img_add Tambah Soal</span></div>
  <form method=post class='m0 hideita' id=form_tambah_soal >
    <div class='wadah gradasi-hijau'>
      <div class=sub_form>Form Tambah Soal</div>
      <textarea required minlength=30 maxlength=500 class='form-control mt1 mb1' placeholder='Kalimat soal...' rows=5></textarea>
      <div class='f12 abu miring mb4'>Silahkan ketik kalimat soal 30 s.d 500 karakter.</div>

      <div class=mb2><span class='btn_aksi f14 abu consolas pointer' id=blok_tambah_gambar__toggle>$img_add Tambah gambar</span></div>
      <div class='hideit' id=blok_tambah_gambar>
        <input type=file class='form-control mb1' name=link_media >
        <div class='f12 abu miring mb4'>Gambar soal (opsional), format JPG</div>
      </div>
      <table width=100%>
        <tr>
          <td width=30px class='pb2 pr2 kanan'>a.</td>
          <td>
            <input required class='form-control mb2' placeholder='opsi a...' name=opsi_a>
          </td>
          <td width=80px class='tengah pb2'><label class='btn btn-secondary btn-sm'><input required type=radio name=kj> KJ: A</label></td>
        </tr>
        <tr>
          <td width=30px class='pb2 pr2 kanan'>b.</td>
          <td>
            <input required class='form-control mb2' placeholder='opsi b...' name=opsi_b>
          </td>
          <td width=80px class='tengah pb2'><label class='btn btn-secondary btn-sm'><input required type=radio name=kj> KJ: B</label></td>
        </tr>
        <tr>
          <td width=30px class='pb2 pr2 kanan'>c.</td>
          <td>
            <input required class='form-control mb2' placeholder='opsi c...' name=opsi_c>
          </td>
          <td width=80px class='tengah pb2'><label class='btn btn-secondary btn-sm'><input required type=radio name=kj> KJ: C</label></td>
        </tr>
        <tr>
          <td width=30px class='pb2 pr2 kanan'>d.</td>
          <td>
            <input required class='form-control mb2' placeholder='opsi d...' name=opsi_d>
          </td>
          <td width=80px class='tengah pb2'><label class='btn btn-secondary btn-sm'><input required type=radio name=kj> KJ: D</label></td>
        </tr>
      </table>

      <button class='btn btn-primary w-100'>Simpan Soal</button>

    </div>
  </form>
</td></tr>";

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
