<?php
# =====================================================================
# PROCESSOR HAPUS BUKTI
# =====================================================================
include 'activity_processor_upload_bukti.php';
# =====================================================================
# PROCESSOR UPLOAD BUKTI
# =====================================================================
include 'activity_processor_hapus_bukti.php';

# =====================================================================
# PROCESSOR ADD SUBLEVEL 
# =====================================================================
include 'activity_processor_add_sublevel.php';

# =====================================================================
# NORMAL FLOW :: ID ASSIGN IS SET
# =====================================================================
$s = "SELECT 
a.tanggal as tanggal_assign,
a.no as no_lat,
a.id as id_assign_jenis,
c.id as id_jenis,
c.*,
c.status as status_jenis,
b.id as id_sesi,
b.wag, 
b.no as no_sesi, 
(SELECT id FROM tb_bukti_$jenis WHERE id_peserta=$id_peserta AND id_assign_$jenis=a.id) as id_bukti,
(
  SELECT COUNT(1) FROM tb_sublevel_challenge  
  WHERE id_challenge=c.id) count_sublevel, 
(
  SELECT COUNT(1) FROM tb_bukti_$jenis p 
  JOIN tb_assign_$jenis q ON p.id_assign_$jenis=q.id   
  JOIN tb_peserta r ON p.id_peserta=r.id
  WHERE q.id_$jenis=a.id_$jenis
  AND r.id_role=1) count_submiter 
FROM tb_assign_$jenis a 
JOIN tb_sesi b ON a.id_sesi=b.id 
JOIN tb_$jenis c ON a.id_$jenis=c.id 
WHERE a.id=$id_assign  
";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) die(section(div_alert('danger', "Maaf, data $jenis tidak ditemukan.<hr><a class=proper href='?activity&jenis=$jenis'>Pilih $jenis</a>")));
$d = mysqli_fetch_assoc($q);

$closed = $d['status_jenis'] == -1 ? 1 : 0;

$id_assign_jenis = $d['id_assign_jenis'];
$id_jenis = $d['id_jenis'];
$id_sesi = $d['id_sesi'];
$id_bukti = $d['id_bukti'];
$tanggal_assign = $d['tanggal_assign'];
$basic_point = $d['basic_point'];
$ontime_point = $d['ontime_point'];
$ontime_dalam = $d['ontime_dalam'];
$ontime_deadline = $d['ontime_deadline'];
$ket = $d['ket'];
$count_submiter = $d['count_submiter'];
$link_includes = $d['link_includes'] ?? ''; //untuk latihan tidak ada link_includes
$link_excludes = $d['link_excludes'] ?? ''; //untuk latihan tidak ada link_excludes

$pada_wag = "<a href='$d[wag]' target=_blank>Lihat Pada Whatsapp Group P$d[no_sesi]</a>";

$hasil = '<div class="kecil miring merah">kamu belum mengerjakan.</div>';
if ($id_bukti) {
  $s2 = "SELECT a.*,b.no as no_lat, 
  (
    SELECT nama FROM tb_peserta 
    WHERE id=a.verified_by) as verifikator

  FROM tb_bukti_$jenis a 
  JOIN tb_assign_$jenis b ON a.id_assign_$jenis=b.id 
  JOIN tb_$jenis c ON b.id_$jenis=c.id  
  WHERE a.id=$id_bukti";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  $d2 = mysqli_fetch_assoc($q2);
  $tanggal_upload = $d2['tanggal_upload'];
  $get_point = $d2['get_point'];
  $tanggal_verifikasi = $d2['tanggal_verifikasi'];
  $verifikator = $d2['verifikator'];
  $status = $d2['status'];
  $alasan_reject = $d2['alasan_reject'];

  $id_sublevel = '';
  $nama_sublevel = '';
  $no_sublevel = '';
  if ($jenis == 'challenge') {
    $id_sublevel = $d2['id_sublevel'] ?? die('id_sublevel is null at activity show.');
    $s3 = "SELECT nama as nama_sublevel,no as no_sublevel FROM tb_sublevel_challenge WHERE id=$id_sublevel";
    $q3 = mysqli_query($cn, $s3) or die(mysqli_error($cn));
    $d3 = mysqli_fetch_assoc($q3);
    $nama_sublevel = $d3['nama_sublevel'];
    $no_sublevel = $d3['no_sublevel'];
  }

  $form_hapus = "
    <form method=post>
      <button class='btn btn-danger btn-block proper' name=btn_hapus_bukti onclick='return confirm(\"Yakin untuk menghapus dan upload kembali bukti $jenis?\")' value='$id_bukti'>Hapus bukti $jenis</button>
    </form>
  ";

  if ($tanggal_verifikasi != '' and $status == 1) {
    $verif_icon = img_icon('check');
    $verif_opsi = div_alert('success', "Selamat! Bukti kamu sudah terverifikasi oleh $verifikator pada $tanggal_verifikasi");
  } elseif ($status == -1) {
    $verif_icon = "<span class='red kecil miring'>(rejected :: $alasan_reject)</span>";
    $verif_opsi = div_alert('danger', "Maaf, bukti kamu ditolak dengan alasan $alasan_reject.$form_hapus");
  } else {
    $verif_icon = '<span class="red kecil miring">(belum diverifikasi)</span>';
    $verif_opsi = "Bukti kamu belum diverifikasi, kamu masih boleh menghapusnya.$form_hapus";
  }


  if ($jenis == 'latihan') {
    $path_file = "uploads/$folder_uploads/$jenis-$id_assign.jpg";
    $scr = "
      <div class=mb2 style=margin-left:-30px>
        <a href='$path_file' target=_blank onclick='return confirm(\"Buka gambar di Tab baru?\")'>
          <img src='$path_file' class='img-fluid'>
        </a>
        <div class=mt2>$verif_opsi</div>
      </div>
    ";
  } else if ($jenis == 'challenge') {
    $scr = "<a href='$d2[link]' target=_blank>$d2[link]</a><div class=mt2>$verif_opsi</div>";
  } else {
    die("Jenis activity: $jenis unhandled action.");
  }

  $menit = round((strtotime($tanggal_upload) - strtotime($tanggal_assign)) / 60, 0);
  $jam = intval($menit / 60);
  $sisa_menit = $menit % 60;

  $menit_show = $jam ? "$jam jam $sisa_menit menit" : "$sisa_menit menit";

  $tanggal_upload_show = date('d/m/y H:i', strtotime($tanggal_upload));

  $screenshoot = $jenis == 'latihan' ? 'Screenshoot' : 'Link bukti ' . $jenis;

  $sublevel_info = $jenis == 'challenge' ? "<li class=kecil>Sublevel: <span class='f20 darkblue'>Level $no_sublevel # $nama_sublevel</span></li>" : '';

  $hasil = "
  <ul>
    <li><b class=darkblue>Get Point: $get_point LP</b> $verif_icon</li>
    $sublevel_info
    <li class=kecil>Tanggal Upload: $tanggal_upload_show</li>
    <li class=kecil>Dikerjakan dalam $menit_show</li>
    <li class=kecil>$screenshoot: $scr</li>
  </ul>
  ";
}

$btn_hapus_bukti = '';
if ($status == -1 and $jenis == 'challenge') {
  $btn_hapus_bukti = "
  <form method=post>
    <button class='btn btn-danger btn-sm' name=btn_hapus_bukti  id=challenge__$id_assign_jenis onclick='return confirm(\"Yakin untuk hapus Challenge dan Reupload kembali?\")' value='$id_bukti'>Hapus dan Reupload</button>
  </form>
  ";
}

$hasil = "<div class='wadah'><div>Hasil $jenis:</div>$hasil$btn_hapus_bukti</div>";

$info_ekstensi = [
  'latihan' => 'ekstensi harus JPG, jika latihan coding posisikan bukti screenshoot: kiri code, kanan hasil',
  'challenge' => 'harus berupa link-online diawali dg http atau https, misal: http://iin-sholihin.github.io, https://insho.rf.gd',
];

$accept_ekstensi = [
  'latihan' => '.jpg,.jpeg',
  'challenge' => '',
];

$input_type = [
  'latihan' => 'file',
  'challenge' => 'text minlength=15 maxlength=100',
];

$btn_upload = $id_role != 3 ? "<button class='btn btn-primary btn-block' name=btn_upload value='$id_assign_jenis'>Submit</button>" : "<span class='btn btn-primary btn-block' onclick='alert(\"Anda login sebagai Supervisor! Terima kasih sudah mencoba upload.\")'>Upload</span>";
$form_add_sublevel = '';

if ($id_bukti) {
  $form_bukti = div_alert('success', 'Kamu sudah mengerjakan.');
} else { // belum mengerjakan
  if ($jenis == 'latihan') {
    if ($ket) {

      if ($closed) {
        $form_bukti = "<div class='wadah red bold miring p4 tengah gradasi-merah'>Latihan ini sudah ditutup.</div>";
      } else {
        $form_bukti = "
          <form method=post enctype=multipart/form-data>
            Bukti kamu mengerjakan:
            <div class='mb2 mt1'>
              <input class=form-control type=$input_type[$jenis] name=bukti accept='$accept_ekstensi[$jenis]' required>
              <div class='kecil miring abu mt1 pl1'>)* $info_ekstensi[$jenis].</div>
            </div>
            $btn_upload
          </form>
        ";
      }
    } else {
      $form_bukti = div_alert('danger', "Belum bisa upload bukti latihan. Instruktur belum mengisi keterangan untuk latihan ini. Silahkan hubungi beliau!");
    }
  } elseif ($jenis == 'challenge') {

    if ($d['count_sublevel']) {
      $id_sublevel = $_GET['id_sublevel'] ?? '';
      if ($id_sublevel) {
        include 'activity_sublevel_submit.php';
      } else {
        include 'activity_sublevel_show.php';
      }
    } else {
      $form_bukti = div_alert('danger', "Maaf, challenge ini belum mempunyai Sub-Level. Segera hubungi instruktur!");
      $hasil = '';
    }

    if ($id_role == 2) {

      $contoh_nama_sublevel = $d['count_sublevel'] > 2 ? '' : "
        <div class='abu f12 mt2'>
          Contoh nama sublevel:
          <ul>
            <li>Novice</li>
            <li>Beginner</li>
            <li>Advance</li>
            <li>Master</li>
            <li>Expert</li>
          </ul>
        </div>
      ";

      $form_add_sublevel = "
        <form method=post>
          <div class=flexy>
            <div>+</div>
            <div>
              <input type=text minlength=3 maxlength=100 required class='form-control form-control-sm' placeholder='Nama sublevel' name=nama_sublevel>
              $contoh_nama_sublevel
            </div>
            <div><button class='btn btn-success btn-sm' value=$id_jenis name=btn_add_sublevel>Add Sublevel</button></div>
          </div>
        </form>
      ";
    }
  } else {
    echo div_alert('danger', 'Undefined jenis activity.');
  }
}

$link_panduan_show = $d['link_panduan'] ? "<a target=_blank href='$d[link_panduan]'>$d[link_panduan]</a>" : '<span class="consolas f12 miring">belum ada</span>';
$tanggal_jenis_show = $nama_hari[date('w', strtotime($d['tanggal_assign']))] . ', ' . date('d-M-Y, H:i', strtotime($d['tanggal_assign']));
$basic_point_show = number_format($d['basic_point'], 0);
$ontime_point_show = number_format($d['ontime_point'], 0);
$ontime_dalam_show = eta(strtotime($tanggal_assign) - strtotime('now') + $d['ontime_dalam'] * 60);
$ontime_deadline_show = eta(strtotime($tanggal_assign) - strtotime('now') + $d['ontime_deadline'] * 60);

# ============================================================
# PERSEN SUBMITER
# ============================================================
$persen_peserta = round($count_submiter * 100 / $total_peserta, 1);
# ============================================================
# POIN ANTRIAN
# ============================================================
$ten_percent = intval($total_peserta / 10);
$poin_antrian_show = '-';
$be_the_first = '';
$Submiter = $jenis == 'latihan' ? 'Submiter' : 'Challenger';
if ($count_submiter < $ten_percent and $count_submiter <= 10) {
  $arr_persen_poin_antrian = [
    0 => 40,
    1 => 32,
    2 => 26,
    3 => 20,
    4 => 16,
    5 => 13,
    6 => 10,
    7 => 8,
    8 => 7,
    9 => 5,
  ];
  $poin_antrian = $arr_persen_poin_antrian[$count_submiter] * $basic_point / 100;
  $poin_antrian_show = number_format($poin_antrian, 0) . ' LP';

  $arr_be_the_first = [
    0 => "Be the First $Submiter !",
    1 => "Be the Second $Submiter !",
    2 => "Be the Third $Submiter !",
    3 => "Be the Top 5 $Submiter !",
    4 => "Be the Top 5 $Submiter !",
    5 => "Be the Top 10 $Submiter !",
    6 => "Be the Top 10 $Submiter !",
    7 => "Be the Top 10 $Submiter !",
    8 => "Be the Top 10 $Submiter !",
    9 => "Be the Top 10 $Submiter !",

  ];
  $be_the_first = $arr_be_the_first[$count_submiter];
}

# ============================================================
# 3 BEST SUBMITER
# ============================================================
$best_submiter = '';
if ($count_submiter) {
  $s2 = "SELECT 
  (a.get_point + a.poin_antrian + a.poin_apresiasi) total_poin, 
  c.nama as nama_submiter, 
  c.image as image_submiter, 
  c.war_image as war_image_submiter 
  FROM tb_bukti_$jenis a 
  JOIN tb_assign_$jenis b ON a.id_assign_$jenis=b.id   
  JOIN tb_peserta c ON a.id_peserta=c.id
  WHERE b.id_$jenis=$id_jenis
  AND c.id_role=1 
  ORDER BY total_poin DESC, tanggal_upload  
  LIMIT 3 
  ";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  while ($d2 = mysqli_fetch_assoc($q2)) {
    $nama_submiter = $d2['nama_submiter'];
    // echo "<br>$nama_submiter";
    $best_submiter .= "
      <div>
        <img src='$lokasi_profil/$d2[war_image_submiter]' class=foto_profil>
        <div class='f12 darkblue'>$d2[nama_submiter]</div>
      </div>
    ";
  }
  $best_submiter = "
    <h4>Best $Submiter</h4>
    <div class='flexy flex-center center'>
      $best_submiter
    </div>
  ";
}

# ============================================================
# LATIHAN/CHALLENGE INFO
# ============================================================
$list_info = "
  <table class='table kecil mt2 table-striped'>
    <tr>
      <td class='tebal abu'>Link Panduan</td>
      <td class='darkblue'>$link_panduan_show</td>
    </tr>
    <tr>
      <td class='tebal abu'>Tanggal mulai</td>
      <td class='darkblue'>$tanggal_jenis_show</td>
    </tr>
    <tr>
      <td class='tebal abu'>Basic Point</td>
      <td class='darkblue'>$basic_point_show LP</td>
    </tr>
    <tr>
      <td class='tebal abu'>Bonus First Submit</td>
      <td class='darkblue'>$poin_antrian_show</td>
    </tr>
    <tr>
      <td class='tebal abu'>Apresiasi Point</td>
      <td class='darkblue'>0 s.d $basic_point_show LP</td>
    </tr>
    <tr>
      <td class='tebal abu'>Ontime Point</td>
      <td class='darkblue'>$ontime_point_show LP</td>
    </tr>
    <tr>
      <td class='tebal abu'>Ontime Dalam</td>
      <td class='tebal darkred'>$ontime_dalam_show</td>
    </tr>
    <tr>
      <td class='tebal abu'>Ontime Deadline</td>
      <td class='tebal darkred'>$ontime_deadline_show</td>
    </tr>
    <tr>
      <td class='tebal abu'>Closing $Jenis</td>
      <td class='tebal darkred'>hingga UTS/UAS (atau sesuai info dari instruktur)</td>
    </tr>
  </table>

  <div class='wadah darkblue tengah f14 bg-white'>
    Dikerjakan oleh $count_submiter of $total_peserta peserta ($persen_peserta%)
    <div class='progress mt1'>
      <div class='progress-bar' style='width:$persen_peserta%'>
      </div>
    </div>
    <div class='f20 blue pt2'>
      $be_the_first
      $best_submiter
    </div>
  </div>
";

$admin_hint = $id_role == 2 ? "<span class=abu>Silahkan ubah via <b class='consolas darkblue'>Update $jenis Properties</b>.</span>" : '';

$cara_pengumpulan_show = $d['cara_pengumpulan'] ?? $cara_pengumpulan_default;
$cara_pengumpulan_show = "<div class='abu tebal mt4 mb2 consolas'>Cara Pengumpulan:</div>$cara_pengumpulan_show";

$ket_show = $d['ket'] ? $d['ket'] : "<span class='red f12'>Keterangan $jenis belum ditentukan. $admin_hint</span>";



echo "
<div class='wadah gradasi-hijau' data-zzz-aos=fade-up>
  $pesan_upload
  
  <p class='mb4 tebal f18 darkblue'>Bacalah detail $jenis dengan seksama!</p>

  <div class=proper>
    Nama $jenis: 
    <input disabled class='form-control input_editable ' id=nama__$id_assign_jenis value='$d[nama]'>
  </div>
  <div class='mt1 mb2 wadah biru'>
    $ket_show
    $cara_pengumpulan_show
    $list_info 
  </div>
  $form_bukti 
  $form_add_sublevel
  $hasil
</div>
<div class=debug>

  <div>id_assign_jenis:<span id=id_assign_jenis>$d[id_assign_jenis]</span></div>
  <div>id_sesi:<span id=id_sesi>$d[id_sesi]</span></div>
  <div>nama2:<span id=nama2__$id_assign_jenis>$d[nama]</span></div>
  <div>ket2:<span id=ket2__$id_assign_jenis>$d[ket]</span></div>
  <div>tanggal_" . $jenis . "2:<span id=tanggal_$jenis" . "2__$id_assign_jenis>$d[tanggal_assign]</span></div>
  <div>basic_point2:<span id=basic_point2__$id_assign_jenis>$d[basic_point]</span></div>
  <div>ontime_point2:<span id=ontime_point2__$id_assign_jenis>$d[ontime_point]</span></div>
  <div>ontime_dalam2:<span id=ontime_dalam2__$id_assign_jenis>$d[ontime_dalam]</span></div>
  <div>ontime_deadline2:<span id=ontime_deadline2__$id_assign_jenis>$d[ontime_deadline]</span></div>
</div>
";

# =========================================================
# ADMIN ONLY
# =========================================================
if ($id_role == 2) {
  echo '<hr class="mt4 mb4"><h3 class="tebal darkred tengah mb4">Fitur Khusus Instruktur</h3>';
  include 'include/form_target_kelas.php';
  include 'activity_submiter.php';
  include 'activity_manage.php';
}
?>















<script>
  $(function() {
    let jenis = $('#jenis').text();
    let id_sesi = $('#id_sesi').text();
    let id_assign_jenis = $('#id_assign_jenis').text();
    // alert(id_assign_jenis);

    $('#set_now').click(function() {
      // alert(1)
      let nd = new Date();
      let y = nd.getFullYear();
      let m = nd.getMonth() + 1;
      let d = nd.getDate();
      let h = nd.getHours();
      let i = nd.getMinutes();
      // console.log(y,m,d,h,i)

      let z = confirm('Isi tanggal mulai ke saat ini?');
      if (!z) return;

      $('#tanggal_' + jenis + '__' + id_assign_jenis).val(`${y}-${m}-${d} ${h}:${i}`);
    })

    $('.input_editable').focusout(function() {
      // alert($(this).prop('id'))
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let kolom = rid[0];
      let id_assign_jenis = rid[1];

      let isi_lama = $('#' + kolom + '2__' + id_assign_jenis).text();
      let isi_baru = $(this).val().trim();
      if (isi_lama == isi_baru) return;
      if (isi_baru == '') {
        let y = confirm('Ingin mengosongkan data?');
        if (!y) {
          // console.log(isi_lama);
          $('#' + tid).val(isi_lama);
          return;
        }
        // $('#'+tid).val(isi_lama);
      }
      let aksi = 'ubah';
      let link_ajax = `ajax/ajax_crud_jenis.php?aksi=${aksi}&id=${id_assign_jenis}&kolom=${kolom}&isi_baru=${isi_baru}&id_sesi=${id_sesi}&jenis=${jenis}`
      // alert(link_ajax);
      $.ajax({
        url: link_ajax,
        success: function(a) {
          if (a.trim() == 'sukses') {
            $('#' + tid).addClass('gradasi-hijau biru');
            $('#' + kolom + '2__' + id_assign_jenis).text(isi_baru);
          } else {
            alert(a)
          }
        }
      })

    })

    $('.btn_aksi_old').click(function() {
      // alert($(this).prop('id'))
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];

      let alasan_reject = '';
      if (aksi == 'reject') {
        alasan_reject = prompt('Alasan Reject (min 20 char):', 'Tidak sesuai request. Silahkan baca keterangan ' + jenis + ' dengan baik.').replace(/['"]/gim, '');
        if (alasan_reject.length < 20) {
          alert('Silahkan masukan alasan reject minimal 20 karakter.');
          return;
        }
      } else if (aksi == 'accept') {
        let y = confirm('Ingin verifikasi (accept) aktifitas ini?');
        if (!y) return;
      } else {
        alert('Unhandle aksi: ' + aksi);
        return;
      }

      let link_ajax = `ajax/ajax_verif_bukti_jenis.php?aksi=${aksi}&id=${id}&jenis=${jenis}&alasan_reject=${alasan_reject}`
      // alert(link_ajax);
      $.ajax({
        url: link_ajax,
        success: function(a) {
          if (a.trim() == 'sukses') {
            let h = aksi == 'accept' ? '<span class="hijau tebal">Anda telah accept bukti ini.</span>' : '<span class="red">Anda telah reject bukti ini dengan alasan: <quote class=miring>' + alasan_reject + '</quote>.</span>';
            $('#blok_bukti__' + id).html(h);
          } else {
            alert(a)
          }
        }
      })

    })
  })
</script>