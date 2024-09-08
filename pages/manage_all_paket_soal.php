<?php
$dp = 1;
instruktur_only();
if ($username != 'abi') die('Hanya developer yang bisa akses.');
$null = '<span class="abu f12 miring consolas">null</span>';

$judul = 'Manage All Paket Soal';
set_h2($judul, "Paket Soal adalah wadah untuk soal-soal yang akan diujikan ke tiap Grup Kelas<div class=mt2><a href='?ujian' >$img_prev</a></div>");

















if (isset($_POST['btn_delete_paket_soal'])) {
  $id = $_POST['btn_delete_paket_soal'];
  if ($id) {
    $s = "DELETE FROM tb_paket_kelas WHERE id_paket=$id AND kelas='INSTRUKTUR'";
    $s = "DELETE FROM tb_paket_kelas WHERE id_paket=$id ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $s = "DELETE FROM tb_paket WHERE id=$id";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo div_alert('success', 'Delete paket berhasil.');
    jsurl('', 1000);
  }
}
if (isset($_POST['btn_set_id_sesi'])) {
  $id_sesi = $_POST['btn_set_id_sesi'];
  $s = "UPDATE tb_paket SET id_sesi = $id_sesi WHERE id=$_POST[id_paket]";
  echo '<pre>';
  var_dump($s);
  echo '</pre>';
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
}








































# =============================================
# MAIN SELECT
# =============================================
$s = "SELECT  
a.*,
a.id as id_paket,
a.nama as nama_paket,
a.durasi_ujian,
a.tanggal_pembahasan,
a.max_attemp,
(
  SELECT COUNT(1) FROM tb_assign_soal 
  WHERE id_paket=a.id) count_soal,
(
  SELECT COUNT(1) FROM tb_paket_kelas 
  WHERE id_paket=a.id
  AND kelas != 'INSTRUKTUR') count_kelas,
(
  SELECT COUNT(1) FROM tb_jawabans p 
  JOIN tb_paket_kelas q ON p.paket_kelas=q.paket_kelas  
  WHERE q.id_paket=a.id) count_submit

FROM tb_paket a 
WHERE 1 -- a.id_room=$id_room 
AND id_sesi is null
ORDER BY date_created 
";

// echo '<pre>';
// var_dump($s);
// echo '</pre>';
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) {
  $tr = div_alert('danger', "Belum ada data paket soal untuk room ini.");
} else {
  $tr = '';
  $no = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $no++;
    $id_paket = $d['id_paket'];
    $count_soal = $d['count_soal'];
    $count_kelas = $d['count_kelas'];
    $count_submit = $d['count_submit'] ?? 0;
    // $untuk_event = $d['untuk_event'];
    $untuk_event = 'ZZZ';
    $nama_paket = $d['nama_paket'];
    $max_attemp = $d['max_attemp'];
    $durasi_ujian = $d['durasi_ujian'];
    $tanggal_pembahasan = $d['tanggal_pembahasan'];
    $tanggal_pembahasan_show = date('d-M-Y H:i', strtotime($tanggal_pembahasan));

    $btn_delete = ($count_soal  || $count_submit) ? "<span onclick='alert(`Tidak bisa menghapus Paket Soal jika pada paket tersebut sudah ada soal atau jumlah submit.`)'>$img_delete_disabled</span>" : "
      <form method=post style='display:inline'>
        <button class='p0 m0' name=btn_delete_paket_soal style='display:inline; background:none; border:none' onclick='return confirm(\"Yakin untuk hapus paket ini?\")' value=$id_paket>$img_delete</button>
      </form>
    ";


    # ============================================================
    # SELECT SESI
    # ============================================================
    $s2 = "SELECT * FROM tb_sesi WHERE id_room=$d[id_room] ORDER BY no";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    $btns = '';
    while ($d2 = mysqli_fetch_assoc($q2)) {
      $btns .= "<div><button name=btn_set_id_sesi value=$d2[id]>$d2[id] - $d2[nama]</button></div>";
    }



    $list_kelas = '';
    if (!$count_kelas) {
      $list_kelas = "<a class='btn btn-sm btn-danger' href='?add_paket_soal&id_paket=$id_paket'>Set Kelas</a>";
    } else {
      $s2 = "SELECT * FROM tb_paket_kelas WHERE id_paket=$id_paket";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
      while ($d2 = mysqli_fetch_assoc($q2)) {
        // $id = $d2['id'];
        $awal_ujian = $d2['awal_ujian'];
        $awal_ujian_show = date('d M Y', strtotime($awal_ujian)) . ' ' . date('H:i', strtotime($awal_ujian));
        $akhir_ujian = date('Y-m-d H:i:s', strtotime($awal_ujian) + $durasi_ujian * 60);
        $eta = eta2($akhir_ujian);
        $list_kelas .= "
          <li>
            $d2[kelas]
            <div class='abu f12 miring'>$awal_ujian_show</div>
            <div class='abu f12 miring'>$eta</div>
          </li>
        ";
      }
      $list_kelas = "<ol>$list_kelas</ol>";
    }

    $id_paket_show = $dp ? "<div class='f12 abu'>id: $id_paket</div>" : '';

    $eta = eta2($tanggal_pembahasan);
    $pembahasan_show = !$tanggal_pembahasan ? "
      Tidak ada pembahasan kunci jawaban.
    " : "
      Tampil Pembahasan: $tanggal_pembahasan_show | $eta
    ";

    $form_destroy_paket = $username != 'abi' ? '' : "
      <div>
        <a target=_blank href='?destroy_paket&id_paket=$id_paket' class='btn btn-sm btn-danger' >DESTROY</a>
      </div>
    ";

    # =============================================
    # FINAL TR
    # =============================================
    $tr .= "
      <tr class=tr_paket_soal id=tr_paket_soal__$id_paket>
        <td>$no</td>
        <td>
          <div class='f14 miring'>$untuk_event</div>
          <form method=post>
            <input type=hiddena name=id_paket value=$id_paket>
            $btns
          </form>
          <div class='f16 darkblue tebal'>
            $nama_paket 
            <a href='?add_paket_soal&id_paket=$id_paket'>$img_edit</a>
          </div>
          <div class='f12 abu'>Durasi: $durasi_ujian menit</div>
          <div class='f12 abu'>$pembahasan_show</div>
          <div class='f12 abu'>Max Attemp: $max_attemp kali</div>
          $id_paket_show
          $form_destroy_paket
        </td>
        <td>
          $list_kelas
        </td>
        <td>
          $d[count_soal] soal <a href='?assign_soal&id_paket=$id_paket'>$img_detail</a>
        </td>
        <td>
          $d[count_submit] kali <a href='?monitoring_ujian&id_paket=$id_paket'>$img_detail</a>
        </td>
        <td width=50px class=tengah>
          $btn_delete
        </td>
      </tr>
    ";
  }
}
















































# ================================================ -->
# BLOK TAMBAH PAKET SOAL
# ================================================ -->
$tr_tambah = "
  <tr><td colspan=100%>
    <a class='btn btn-sm btn-success' href='?add_paket_soal'>Add Paket Soal</a>
  </td></tr>
";

# ================================================ -->
# FINAL ECHO
# ================================================ -->
echo "
  <table class=table>
    <thead class=gradasi-toska>
      <th class=proper>no</th>
      <th class=proper>Paket Soal</th>
      <th class=proper>Count Kelas</th>
      <th class=proper>Count Soal</th>
      <th class=proper>Count Submit</th>
      <th class=proper>Aksi</th>
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


  })
</script>