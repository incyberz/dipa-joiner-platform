<?php
$img_next = img_icon('next');
// $img_prev = img_icon('prev');

echo $room['info_ujian'];

$id_challenge = $_GET['id_challenge'] ?? '';
if (!$id_challenge) {
  echo "
    <h2>Silahkan Pilih dari salah satu Challenge ! </h2>
    <a href='?activity&jenis=challenge' class='btn btn-primary'>$img_prev List Challenge</a>
  ";
  exit;
}

# ============================================================
# GET PEKAN UJIAN
# ============================================================
$get_pekan = $_GET['pekan'] ?? '';
$arr_pekan = [
  'uts' => 'UTS',
  'uas' => 'UAS',
  'remed_uts' => 'Remed UTS',
  'remed_uas' => 'Remed UAS'
]; // diambil dari tb_poin
if (!$get_pekan) {
  echo "
    <h2>Untuk Pekan Ujian: </h2>
    <div class='f12 mb4'>
      <a href='?activity&jenis=challenge' >$img_prev List Challenge</a>
    </div>
  ";

  $select_sesi = '';
  foreach ($arr_pekan as $pekan => $title) {
    echo "<a class='btn btn-primary' href='?challenge_to_ujian&id_challenge=$id_challenge&pekan=$pekan'>$title</a> ";
  }
  exit;
}



# ============================================================
# GET KELAS
# ============================================================
$get_kelas = $_GET['kelas'] ?? '';
if (!$get_kelas) {
  echo "
    <h2>$arr_pekan[$get_pekan] untuk Kelas : </h2>
    <div class='f12 mb4'>
      <a href='?activity&jenis=challenge' >$img_prev List Challenge</a> | 
      <a href='?challenge_to_ujian&id_challenge=$id_challenge' >Ubah Pekan</a> 
    </div>
  ";
  $s = $select_room_kelas;
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  while ($d = mysqli_fetch_assoc($q)) {
    echo "<a class='btn btn-primary' href='?challenge_to_ujian&id_challenge=$id_challenge&pekan=$get_pekan&kelas=$d[kelas]'>$d[kelas]</a> ";
  }
  exit;
}


# ============================================================
# MAIN PROCESS
# ============================================================
set_title("Challenge to Ujian | $arr_pekan[$get_pekan] | $get_kelas");
echo "
  <h1>$arr_pekan[$get_pekan] untuk Kelas $get_kelas </h1>
  <div class='f12 mb4'>
    <a href='?activity&jenis=challenge' >$img_prev List Challenge</a> | 
    <a href='?challenge_to_ujian&id_challenge=$id_challenge' >Ubah Pekan</a> |
    <a href='?challenge_to_ujian&id_challenge=$id_challenge&pekan=$get_pekan' >Ubah Kelas</a> 
  </div>
";
include 'challenge_to_ujian_process.php';


# ============================================================
# CHALLENGE PROPERTIES
# ============================================================
$s = "SELECT a.* 
FROM tb_challenge a WHERE id='$id_challenge'";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
if (mysqli_num_rows($q)) {
  $i = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    foreach ($d as $key => $value) {
      if (
        $key == 'id'
        || $key == 'date_created'
      ) continue;

      $kolom = key2kolom($key);
      $tr .= "
        <tr>
          <td>$kolom</td>
          <td>$value</td>
        </tr>
      ";
    }
  }
}

$tb = $tr ? "
  <table class='table table-striped table-hover'>
    $tr
  </table>
" : div_alert('danger', "Data challenge tidak ditemukan.");
// echo "$tb";



# ============================================================
# DIKERJAKAN OLEH
# ============================================================
$s = "SELECT 
b.kelas,
d.id as id_peserta,
d.nama as nama_peserta,
(SELECT COUNT(1) FROM tb_kelas_peserta WHERE kelas=c.kelas) count_peserta,
(
  SELECT get_point FROM tb_bukti_challenge p
  JOIN tb_assign_challenge q ON p.id_assign_challenge=q.id 
  WHERE q.id_challenge = $id_challenge 
  AND p.id_peserta=d.id 
  AND p.status != -1
) poin_chal,
-- nilai sebelumnya pada tb_poin
(
  SELECT $get_pekan FROM tb_poin WHERE id_peserta=d.id AND id_room=$id_room 
) nilai_sebelumnya

FROM tb_assign_challenge a 
JOIN tb_room_kelas b ON a.id_room_kelas=b.id 
JOIN tb_kelas_peserta c ON b.kelas=c.kelas 
JOIN tb_peserta d ON c.id_peserta=d.id
WHERE a.id_challenge = $id_challenge 
AND b.kelas = '$get_kelas'
AND d.id_role = 1 -- _peserta only 
ORDER BY b.kelas, poin_chal desc, d.nama  
";

$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$count_peserta = mysqli_num_rows($q);
$tr = '';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $d2 = [];
  $info = '';
  if ($d['poin_chal']) {
    $s2 = "SELECT a.* FROM tb_bukti_challenge a 
    JOIN tb_assign_challenge b ON a.id_assign_challenge=b.id 
    WHERE b.id_challenge = $id_challenge 
    AND a.id_peserta = $d[id_peserta]";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    $d2 = mysqli_fetch_assoc($q2);

    $status_show = $d2['tanggal_verifikasi'] ? $img_check : $img_warning;
    $info = "
        <div>$status_show</div>
        <div><a target=_blank href='$d2[link]'>$img_next</a></div>
    ";
  }


  $i++;
  $poin_chal = $d['poin_chal'] ? number_format($d['poin_chal'], 0) : '-';
  $cid_peserta = $d['id_peserta'];
  $nilai_sebelumnya = $d['nilai_sebelumnya'] ?? '-';

  $tr .= "
    <tr>
      <td>$i</td>
      <td>$d[nama_peserta] <a href='?login_as&id_peserta=$d[id_peserta]'>$img_login_as</a></td>
      <td>
        <div class='flexy'>
          <div id=poin_chal__$i>$poin_chal</div>
          $info
        </div>
      </td>
      <td>
        <input type=number min=0 max=100 step=0.1 class='form-control konversi' id=konversi__$d[kelas]__$i name=konversi[$cid_peserta] />
      </td>
      <td class=tengah>$nilai_sebelumnya</td>
    </tr>
  ";
  $last_kelas = $d['kelas'];
}

echo "
  <form method=post>
    <h2>Batasan Konversi Nilai</h2>
    <table class='table table-bordered'>
      <tr class='gradasi-toska'>
        <td>
          <div>$get_kelas</div>
          <span id=total_peserta__$get_kelas>$count_peserta</span> $Peserta
        </td>
        <td>
          <div class='f12 mb1'>Batas Nilai Terendah</div>
          <input type=number min=1 max=80 class='form-control batas batas__$get_kelas' id='batas__$get_kelas" . "__awal' value=70 />
        </td>
        <td>
          <div class='f12 mb1'>Batas Nilai Tertinggi</div>
          <input type=number min=70 max=105 class='form-control batas batas__$get_kelas' id='batas__$get_kelas" . "__akhir' value=100 />
        </td>
      </tr>
    </table>
    <hr>
    <h2>Dikerjakan Oleh</h2>
    <table class='table table-striped'>
      <tr class='gradasi-toska '>
        <td>No</td>
        <td>Nama Peserta</td>
        <td>Challenge Points</td>
        <td>Konversi Nilai</td>
        <td class=tengah>Nilai $arr_pekan[$get_pekan] Sebelumnya</td>
      </tr>
      $tr
      <tr class='gradasi-toska'>
        <td colspan=100%>
          <button class='btn btn-primary w-100' onclick='return confirm(`Simpan Nilai Ujian untuk kelas $get_kelas`)' name=btn_simpan>Simpan Nilai Ujian Kelas $get_kelas</button>
        </td>
      </tr>
    </table>
  </form>
";

























?>
<script>
  $(function() {
    $('.batas').keyup(function() {
      let val = parseInt($(this).val());
      if (val && val > 0 && val <= 105) {
        let tid = $(this).prop('id');
        let rid = tid.split('__');
        let aksi = rid[0];
        let kelas = rid[1];
        let bagian = rid[2];
        let total_peserta = parseInt($('#total_peserta__' + kelas).text());
        if (total_peserta) {
          let batas_awal = parseInt($('#batas__' + kelas + '__awal').val());
          let batas_akhir = parseInt($('#batas__' + kelas + '__akhir').val());
          let interval = (batas_akhir - batas_awal) / total_peserta;
          if (interval > 0) {
            let konversi = 0;
            // console.log(aksi, kelas, bagian, total_peserta);

            for (let i = 1; i <= total_peserta; i++) {

              if ($("#poin_chal__" + i).text() == '-') {
                konversi = 0;
              } else {
                konversi = batas_awal + interval * (total_peserta - i + 1);
                konversi = konversi > 100 ? 100 : konversi;
              }

              $('#konversi__' + kelas + '__' + i).val(Math.round(konversi));

            }

          } else {
            console.log('invalid interval: ', interval);
          }
        } else {
          console.log('invalid total_peserta: ', total_peserta, 'batas_awal: ', batas_awal);
        }
      } else {
        console.log('invalid value: ', val);
      }
    });
    $('.batas').keyup();
  })
</script>