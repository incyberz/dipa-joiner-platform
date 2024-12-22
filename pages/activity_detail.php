<?php
# =====================================================================
# GET DATA ASSIGN
# =====================================================================
$s = "SELECT 
a.tanggal as tanggal_assign,
a.no as no_lat,
a.id as id_assign_jenis,
c.id as id_jenis,
c.*,
c.status as status_jenis,
b.id as id_sesi,
b.no as no_sesi,
b.nama as nama_sesi,
b.wag, 
b.no as no_sesi, 
(SELECT id FROM tb_bukti_$jenis WHERE id_peserta=$id_peserta AND id_assign_$jenis=a.id) as id_bukti,
(
  SELECT SUM(poin) FROM tb_sublevel_challenge  
  WHERE id_challenge=c.id) sub_level_point, 
(
  SELECT COUNT(1) FROM tb_sublevel_challenge  
  WHERE id_challenge=c.id) count_sublevel, 
(
  SELECT COUNT(1) FROM tb_bukti_$jenis p 
  JOIN tb_assign_$jenis q ON p.id_assign_$jenis=q.id   
  JOIN tb_peserta r ON p.id_peserta=r.id 
  JOIN tb_room_kelas s ON q.id_room_kelas=s.id 
  WHERE q.id_$jenis=a.id_$jenis
  AND r.id_role=1 
  AND s.ta = $ta 

  ) count_submiter 

FROM tb_assign_$jenis a 
JOIN tb_sesi b ON a.id_sesi=b.id 
JOIN tb_$jenis c ON a.id_$jenis=c.id 
WHERE a.id=$id_assign  
";

$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) die(div_alert('danger', "Maaf, data $jenis tidak ditemukan.<hr><a class=proper href='?activity&jenis=$jenis'>Pilih $jenis</a>"));
$d_assign = mysqli_fetch_assoc($q);
# ============================================================
# EXTRACTED D_ASSIGN
# ============================================================
$closed = $d_assign['status_jenis'] == -1 ? 1 : 0;
$id_assign_jenis = $d_assign['id_assign_jenis'];
$id_jenis = $d_assign['id_jenis'];
$id_sesi = $d_assign['id_sesi'];
$nama_sesi = $d_assign['nama_sesi'];
$no_sesi = $d_assign['no_sesi'];
$id_bukti = $d_assign['id_bukti'];
$tanggal_assign = $d_assign['tanggal_assign'];
$basic_point = $d_assign['basic_point'];
$ontime_point = $d_assign['ontime_point'];
$ontime_dalam = $d_assign['ontime_dalam'];
$ontime_deadline = $d_assign['ontime_deadline'];
$ket = $d_assign['ket'];
$count_submiter = $d_assign['count_submiter'];
$link_includes = $d_assign['link_includes'] ?? ''; //untuk latihan tidak ada link_includes
$link_excludes = $d_assign['link_excludes'] ?? ''; //untuk latihan tidak ada link_excludes
// $apresiasi_poin = $d_assign['apresiasi_poin'];
$sub_level_point = $d_assign['sub_level_point'];

$max_apresiasi_poin = $d_assign['basic_point'];
$max_apresiasi_poin += $jenis == 'latihan' ? 0 : $d_assign['ontime_point'];

# ============================================================
# TANGGAL ASSIGN TARGET KELAS
# ============================================================
$s = "SELECT p.tanggal as tanggal_assign_target_kelas FROM tb_assign_$jenis p 
  JOIN tb_room_kelas q ON p.id_room_kelas=q.id
  WHERE q.kelas = '$target_kelas' 
  AND q.id_room=$id_room 
  AND p.id_$jenis = $id_jenis 
   ";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q) > 1) {
  die('Subquery return more than one.');
} elseif (mysqli_num_rows($q)) {
  $d = mysqli_fetch_assoc($q);
  $tanggal_assign_target_kelas = $d['tanggal_assign_target_kelas'];
} else {
  $tanggal_assign_target_kelas = null;
}





# ============================================================
# POIN ANTRIAN
# ============================================================
$ten_percent = intval($total_peserta / 10) + 1;
$poin_antrian = 0;
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
  $multiplier = $basic_point;
  $multiplier += $jenis == 'latihan' ? 0 : $ontime_point;
  $poin_antrian = $arr_persen_poin_antrian[$count_submiter] * $multiplier / 100;
  $poin_antrian_show = number_format($poin_antrian, 0) . ' LP';

  $max_poin = $basic_point + $poin_antrian + $max_apresiasi_poin + $ontime_point + $sub_level_point;
  $max_poin_show = number_format($max_poin, 0);
  $max_poin_show = "<div>Kamu bisa mendapatkan poin hingga $max_poin_show LP !!</div>";

  # ============================================================
  # BE THE FIRST SUGGESTION
  # ============================================================
  if ($id_bukti) {
    $be_the_first = "Kamu sudah submit $jenis.";
  } else {
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
    $be_the_first = $arr_be_the_first[$count_submiter] . $max_poin_show;
  }
}







































# =====================================================================
# PROCESSORS FOLLOW TO D_ASSIGN
# =====================================================================
include 'activity_detail-processors.php';
































include 'activity_detail-hasil_submit.php';

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

$form_add_sublevel = '';

if ($id_bukti) {
  $form_bukti = div_alert('success', 'Kamu sudah mengerjakan.');
} else { // belum mengerjakan
  if ($jenis == 'latihan') {
    if ($ket) {

      if ($closed) {
        $form_bukti = "<div class='wadah red bold miring p4 tengah gradasi-merah'>Latihan ini sudah ditutup.</div>";
      } else {
        $date = date('ymdHis');
        $new_file = "$jenis-$d_assign[id_jenis]-$id_peserta-$date.jpg";

        $form_bukti = "
          <form method=post enctype=multipart/form-data>
            <div>Bukti kamu mengerjakan:</div>
            <div class='mb2 mt1'>
              <input class=form-control type=$input_type[$jenis] name=bukti accept='$accept_ekstensi[$jenis]' required>
              <div class='kecil miring abu mt1 pl1'>)* $info_ekstensi[$jenis].</div>
            </div>
            <button class='btn btn-primary btn-block' name=btn_upload value='$new_file'>Submit</button>
          </form>
        ";
      }
    } else {

      if ($trainer['no_wa']) {
        $link_akses = urlencode(get_current_url());
        $text_wa = "Yth. Instruktur ($trainer[nama]),%0a%0aPak/Bu $jenis nya belum disetting. Segera ya Pak/Bu, mau saya kerjakan :) %0a%0aLink akses:%0a$link_akses";
        $href = "https://api.whatsapp.com/?send&phone=$trainer[no_wa]&text=$text_wa";
        $img_wa = img_icon('wa');
        $link_wa = "<a href='$href' target=_blank>Hubungi Instruktur $img_wa</a>";
      } else {
        $link_wa = div_alert('danger', "Instruktur belum mempunyai nomor whatsapp, silahkan hubungi via manual.");
      }

      $form_bukti = div_alert('danger tengah mt2', "
        Belum bisa upload bukti latihan. Instruktur belum mengisi keterangan untuk latihan ini. 
        Silahkan hubungi beliau! <hr>
        $link_wa 
      ");
    }
  } elseif ($jenis == 'challenge') {

    if ($d_assign['count_sublevel']) {
      $id_sublevel = $_GET['id_sublevel'] ?? '';
      if ($id_sublevel) {
        include 'activity_sublevel_submit.php';
      } else {
        include 'activity_sublevel_show.php';
      }
    } else {
      $form_bukti = div_alert('danger', "Maaf, challenge ini belum mempunyai Sub-Level. Segera hubungi instruktur!");
      $hasil_submit = '';
    }

    if ($id_role == 2) {

      $contoh_nama_sublevel = $d_assign['count_sublevel'] > 2 ? '' : "
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

$link_panduan_show = $d_assign['link_panduan'] ? "<a target=_blank href='$d_assign[link_panduan]'>$d_assign[link_panduan]</a>" : '<span class="consolas f12 miring">belum ada</span>';
$tanggal_assign_show = $nama_hari[date('w', strtotime($tanggal_assign))] . ', ' . date('d-M-Y, H:i', strtotime($tanggal_assign));
if ($tanggal_assign_target_kelas) {
  $tanggal_assign = $tanggal_assign_target_kelas;
  $tanggal_assign_show = "<div class='abu mb1'>Target kelas <b>$target_kelas</b>:</div>" . $nama_hari[date('w', strtotime($tanggal_assign))] . ', ' . date('d-M-Y, H:i', strtotime($tanggal_assign));
}
$apresiasi_poin_show = number_format($max_apresiasi_poin, 0);
$basic_point_show = number_format($d_assign['basic_point'], 0);
$ontime_point_show = number_format($d_assign['ontime_point'], 0);
$ontime_dalam_show = eta(strtotime($tanggal_assign) - strtotime('now') + $d_assign['ontime_dalam'] * 60);
$ontime_deadline_show = eta(strtotime($tanggal_assign) - strtotime('now') + $d_assign['ontime_deadline'] * 60);

# ============================================================
# PERSEN SUBMITER
# ============================================================
$persen_peserta = !$total_peserta ? 0 : round($count_submiter * 100 / $total_peserta, 1);


$admin_hint = $id_role == 2 ? " | <a href='#manage_$jenis'><span class=' darkblue'>Update $jenis Properties</span>.</a>" : '';


if (!$d_assign['ket']) {
  $cara_pengumpulan = '';
  $list_info = '';
  $ket_kosong = true;
} else { // keterangan latihan sudah diupdate
  $ket_kosong = false;

  # ============================================================
  # 3 BEST SUBMITER
  # ============================================================
  include 'activity_detail-best_submiter.php';

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
        <td class='tebal abu'>Basic Point</td>
        <td class='darkblue'>$basic_point_show LP</td>
      </tr>
      <tr>
        <td class='tebal abu'>Bonus First Submit</td>
        <td class='darkblue'>$poin_antrian_show</td>
      </tr>
      <tr>
        <td class='tebal abu'>Apresiasi Point</td>
        <td class='darkblue'>0 s.d $apresiasi_poin_show LP</td>
      </tr>
      <tr>
        <td class='tebal abu'>Ontime Point</td>
        <td class='darkblue'>$ontime_point_show LP</td>
      </tr>
      <tr>
        <td class='tebal abu'>Tanggal mulai</td>
        <td class='darkblue'>$tanggal_assign_show</td>
      </tr>
      <tr>
        <td class='tebal abu'>Ontime Dalam</td>
        <td class='tebal darkred'>$ontime_dalam_show</td>
      </tr>
      <tr>
        <td class='tebal abu'>Bonus Deadline</td>
        <td class='tebal darkred'>$ontime_deadline_show</td>
      </tr>
      <tr>
        <td class='tebal abu'>Closing $Jenis</td>
        <td class='tebal darkred'>hingga UTS/UAS (atau sesuai info dari instruktur)</td>
      </tr>
    </table>
  
    <div class='wadah darkblue tengah f12 bg-white'>
      Dikerjakan oleh $count_submiter of $total_peserta $peserta_title ($persen_peserta%)
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

  if (!$d_assign['cara_pengumpulan']) {
    $cara_pengumpulan = '';
  } else {
    $cara_pengumpulan = $d_assign['cara_pengumpulan'] ?? $cara_pengumpulan_default;
    $cara_pengumpulan = "<div class='darkblue mt4 mb1 tengah '>Cara Pengumpulan:</div>
    <div class='tengah mb4'>$cara_pengumpulan</div>
    ";
  }
}

$ket_show = $d_assign['ket'] ? $d_assign['ket'] : "<span class='red f12'>Keterangan $jenis belum ditentukan. $admin_hint</span>";
$hide_manage_rule = $d_assign['ket'] ? '' : 'hideit';

# ============================================================
# SET TITLE
# ============================================================
set_title("$jenis - $d_assign[nama]");


# ============================================================
# FINAL ECHO
# ============================================================
echo "
<div class='wadah gradasi-hijau' data-zzz-aos=fade-up>
  $pesan_upload
  
  <p class='mb4 f14 blue tengah'>Bacalah detail $jenis dengan seksama!</p>

  <div class='f20 tebal darkblue tengah proper mb2' style='border-bottom:solid 1px #ddd;border-top:solid 1px #ddd; padding:15px 0; background: linear-gradient(#eff,#ffe)'>$d_assign[nama]</div>

  <div class='darkred mt4 mb1 tengah '>Prosedur Pengerjaan:</div>
  <div class='tengah bold darkblue'>$ket_show</div>
  
  $cara_pengumpulan
  
  $list_info 
  $form_bukti 
  $form_add_sublevel
  $hasil_submit
</div>
";

# =========================================================
# ADMIN ONLY
# =========================================================
if ($id_role == 2) {
  echo '<hr class="mt4 mb4"><h3 class="tebal darkred tengah mb4">Fitur Khusus Instruktur</h3>';
  if (!$ket_kosong) {
    include 'includes/form_target_kelas.php';
    if ($target_kelas) {
      include 'activity_submiter.php';
    }
  }
  include 'activity_manage.php';

  if ($jenis == 'challenge') {
    # ============================================================
    # CHALLENGE TO UTS
    # ============================================================
    echo "
      <div class='wadah gradasi-kuning'>
        <a class='btn btn-primary mb2' href='?challenge_to_ujian&id_challenge=$id_jenis'>Challenge to Ujian</a>
        <p>Jika ingin mengkonversi Learning Point pada challenge ini menjadi nilai UH, UTS, atau UAS</p>
      </div>
    ";
  }
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