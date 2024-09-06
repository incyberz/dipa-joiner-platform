<?php
$img_ask = img_icon('ask');
$img_play_kuis = img_icon('gray');
$img_tanam_soal = img_icon('gray');

$arr_fitur_sesi = [
  'play_kuis' => [
    'title' => 'Play Kuis',
    'icon' => img_icon('play_kuis_gray'),
    'param' => 'play_kuis',
  ],
  'bertanya' => [
    'title' => 'Bertanya',
    'icon' => img_icon('ask'),
    'param' => 'bertanya',
  ],
  'tanam_soal' => [
    'title' => 'Tanam Soal',
    'icon' => img_icon('tanam_soal_gray'),
    'param' => 'tanam_soal',
  ],
  'latihan' => [
    'title' => 'Kerjakan Latihan',
    'icon' => img_icon('latihan_gray'),
    'param' => 'latihan',
  ],
  'challenge' => [
    'title' => 'Beat Challenge',
    'icon' => img_icon('challenge_gray'),
    'param' => 'challenge',
  ],
  'bahan_ajar' => [
    'title' => 'Akses Bahan Ajar',
    'icon' => img_icon('bahan_ajar_gray'),
    'param' => 'bahan_ajar',
  ],
  'file_ppt' => [
    'title' => 'Akses PPT',
    'icon' => img_icon('file_ppt_gray'),
    'param' => 'file_ppt',
  ],
  'video_ajar' => [
    'title' => 'Akses Video',
    'icon' => img_icon('video_ajar_gray'),
    'param' => 'video_ajar',
  ],
  'file_lain' => [
    'title' => 'Akses Files',
    'icon' => img_icon('file_lain_gray'),
    'param' => 'file_lain',
  ],
];


# ============================================================
# get list latihan
# ============================================================
$s = "SELECT id as id_assign, id_sesi 
FROM tb_assign_latihan 
WHERE id_room_kelas='$id_room_kelas' 
ORDER BY id_latihan 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$rlats = [];
while ($d = mysqli_fetch_assoc($q)) {
  if (isset($rlats[$d['id_sesi']])) {
    array_push($rlats[$d['id_sesi']], $d['id_assign']);
  } else {
    $rlats[$d['id_sesi']][0] = $d['id_assign'];
  }
}

# ============================================================
# get list challenge
# ============================================================
$s = "SELECT id as id_assign, id_sesi 
FROM tb_assign_challenge 
WHERE id_room_kelas='$id_room_kelas' 
ORDER BY id_challenge 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$rchals = [];
while ($d = mysqli_fetch_assoc($q)) {
  if (isset($rchals[$d['id_sesi']])) {
    array_push($rchals[$d['id_sesi']], $d['id_assign']);
  } else {
    $rchals[$d['id_sesi']][0] = $d['id_assign'];
  }
}


$count_sesi = [];
$count_sesi[0] = 0;
$count_sesi[1] = 0;
$count_sesi[2] = 0;
$count_sesi[3] = 0;


# ============================================================
# MAIN SELECT SESI
# ============================================================
$s = "SELECT 
a.id as id_sesi,
a.nama as nama_sesi,
a.*,
(
  SELECT p.jadwal_kelas FROM tb_sesi_kelas p 
  WHERE p.id_sesi=a.id 
  AND p.kelas='$kelas' 
  AND p.is_terlaksana=1 
  ) jadwal_kelas 
FROM tb_sesi a 
WHERE a.id_room=$id_room 
ORDER BY a.no
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$total_count_sesi = mysqli_num_rows($q);
$div_list = '';
$no_sesi = 0;
while ($sesi = mysqli_fetch_assoc($q)) {
  if ($sesi['jenis'] === '0') {
    $div_list .= "<div class='alert alert-info tengah' data-aos='fade'>Minggu tenang</div>";
  } elseif ($sesi['jenis'] == 2) {
    $div_list .= "<div class='alert alert-danger tengah' data-aos='fade'>Pekan UTS</div>";
  } elseif ($sesi['jenis'] == 3) {
    $div_list .= "<div class='alert alert-danger tengah' data-aos='fade'>Pekan UAS</div>";
  } elseif ($sesi['jenis'] == 1) {

    $no_sesi++;
    $id_sesi = $sesi['id_sesi'];
    $nama_sesi = $sesi['nama_sesi'];
    $jenis = $sesi['jenis'];
    $count_sesi[$jenis]++;


    # ============================================================
    # FITUR SESI HANDLER AT LOOP SESI NORMAL
    # ============================================================
    $str_fiturs = '';
    foreach ($arr_fitur_sesi as $k => $arr) {
      if (($k == 'bertanya' || $k == 'tanam_soal') and !$sesi['tags']) {
        $str_fiturs .= "<div class='abu miring f12 mb1 bordered br5 p1'>belum bisa $k</div>";
      } elseif ($k == 'challenge' || $k == 'latihan') {
        $sub_fitur = "<div class='abu miring f12 mb1 bordered br5 p1'>belum ada $k</div>";

        if ($k == 'challenge' and isset($rchals[$id_sesi])) {
          $sub_fitur = '';
          $j = 0;
          foreach ($rchals[$id_sesi] as $k2 => $v2) {
            $j++;
            $sub_fitur .= "<a href='?activity&jenis=challenge&id_assign=$v2' class='btn btn-danger btn-sm mb1' onclick='return confirm(\"Menuju laman Challenge?\")'>C$j</a> ";
          }
        } elseif ($k == 'latihan' and isset($rlats[$id_sesi])) {
          $sub_fitur = '';
          $j = 0;
          foreach ($rlats[$id_sesi] as $k2 => $v2) {
            $j++;
            $sub_fitur .= "<a href='?activity&jenis=challenge&id_assign=$v2' class='btn btn-success btn-sm mb1' onclick='return confirm(\"Menuju laman Challenge?\")'>L$j</a> ";
          }
        }
        $str_fiturs .= "<div>$sub_fitur</div>";
      } else {
        $str_fiturs .= "
          <div>
            <a href='?$arr[param]&id_sesi=$id_sesi' class='btn btn-primary btn-sm mb1 w-100' onclick='return confirm(`$arr[title]?`)'>$arr[title]</a>
          </div>
        ";
      }
    }



    if ($sesi['tags'] != '') {
      $r = explode(', ', $sesi['tags']);
      sort($r);
      $tags_show = '<span class="darkblue kecil miring">' . implode(', ', $r) . '</span>';
    } else {
      $tags_show = '<span class="red kecil miring">belum ada tags</span>';
    }


    $nama_show = $sesi['nama_sesi'];
    $ket_show = $sesi['deskripsi'];
    $tags_show = $tags_show;

    $pelaksanaan = $sesi['jadwal_kelas'] ? $sesi['jadwal_kelas'] : '<span class="f12 miring abu">belum dilaksanakan</span>';
    $awal_presensi_show = hari_tanggal($sesi['awal_presensi']);
    $akhir_presensi_show = hari_tanggal($sesi['akhir_presensi']);
    $opening = eta2($sesi['awal_presensi']);
    $closing = eta2($sesi['akhir_presensi']);

    $div_list .= "
      <div class='wadah gradasi-hijau blok_list_sesi' data-aos='fade'>
        <div class='text-center wadah bg-white'>
          sesi 
          <div class='no_sesi f40 darkred'>$no_sesi</div>
        </div>
        <div>
          <div class='flexy flex-between desktop_only mb2'>
            <div class='f10 abu'>id. $id_sesi</div>
            <div class='f10 abu'>
              <a href='?list_sesi&mode=edit'>$img_edit</a>
            </div>
          </div>

          <div class='nama_sesi f14 bold darkblue tengah'>$nama_show</div>
          <div class='kecil miring abu tengah'>$ket_show</div>

          <div class='kecil miring abu mt3 mb1 tengah'>Tag-tag materi</div>
          <div class='mb3 tengah'>$tags_show</div>

          <div class='kecil miring abu mb1 mt4 f10 tengah'>Opening: $opening</div>
          <div class='kecil miring abu tengah'><span class=darkblue>$awal_presensi_show <br>s.d <br>$akhir_presensi_show</span></div>
          <div class='kecil miring abu mb4 f10 tengah'>Closing: $closing</div>

          <div class='mt1 tengah wadah'>
            <div class='kecil miring abu mb1 f10 tengah'>Di sesi ini kamu dapat:</div>
            <div class='flexy flex-center str_fiturs'>
              $str_fiturs
            </div>
          </div>
          <div class='mt1 tengah f10 abu'>Status: $pelaksanaan</div>
        </div>
      </div>
    ";
  } else {
    die(div_alert('danger', "Jenis sesi: $sesi[jenis], belum terdefinisi."));
  }
}











$form_tambah_sesi = $id_role != 2 ? '' : "
<form method=post>
  <div class='kanan'>
    <a class='btn btn-success' href='?manage_sesi&id_room=$id_room'>Manage Sesi</a>
    <button class='btn btn-success' onclick='return confirm('Tambah Sesi untuk Room ini?')' name=btn_tambah_sesi>Tambah Sesi ZZZ</button>
  </div>
</form>
";




set_h2('Learning Path', "
  List Sesi Pembelajaran (Learning Path) 
  <span class=darkblue>$nama_room</span> 
  <div class='desktop_only f12 abu'>
    $count_sesi[1] sesi normal | 
    $count_sesi[0] minggu tenang | 
    $count_sesi[2] pekan UTS | 
    $count_sesi[3] pekan UAS
  </div>
");

# ============================================================
# FINAL ECHO
# ============================================================
echo $div_list;


































if ($manage) { ?>
  <script>
    $(function() {
      $('.input_editable').focusout(function() {
        // alert($(this).prop('id'))
        let tid = $(this).prop('id');
        let rid = tid.split('__');
        let kolom = rid[0];
        let id = rid[1];

        let isi_lama = $('#' + kolom + '2__' + id).text();
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

        // manage tags
        if (kolom == 'tags') {
          isi_baru = isi_baru
            .replace(/;/gim, ',')
            .replace(/[!@#$%^&*()+\-=\[\]{};:'`"\\|<>\/?~]/gim, '');
          let r = isi_baru.split(',');

          let r2 = [];
          r.forEach(el => {
            r2.push(el.trim().toLowerCase());
          });

          isi_baru = r2.sort().join(', ');
        }

        let aksi = 'ubah';
        let link_ajax = `ajax/ajax_crud_sesi.php?aksi=${aksi}&id=${id}&kolom=${kolom}&isi_baru=${isi_baru}`
        // alert(link_ajax);
        $.ajax({
          url: link_ajax,
          success: function(a) {
            if (a.trim() == 'sukses') {
              $('#' + tid).addClass('gradasi-hijau biru');
              $('#' + tid).val(isi_baru);
              $('#' + kolom + '2__' + id).text(isi_baru);
            } else {
              alert(a)
            }
          }
        })

      })
    })
  </script>
<?php } ?>