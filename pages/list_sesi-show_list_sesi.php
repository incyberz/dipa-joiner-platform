<style>
  .icon_bahan_ajar_disabled {
    opacity: 20%;
    -webkit-filter: grayscale();
  }

  .icon_bahan_ajar {
    height: 50px;
    width: 50px;
    object-fit: cover;
    transition: .2s;
  }

  .icon_bahan_ajar:hover {
    transform: scale(1.1)
  }
</style>
<?php
$img_ask = img_icon('ask');
$img_play_kuis = img_icon('gray');
$img_tanam_soal = img_icon('gray');
$img_gray = img_icon('gray');


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
# ACTIVITIES
# ============================================================
$arr_act = ['latihan', 'challenge'];
$arr_data_act = [];
foreach ($arr_act as $act) {
  $s = "SELECT a.id, a.id_sesi,b.nama as nama_act,b.ket 
  FROM tb_assign_$act a
  JOIN tb_$act b ON a.id_$act=b.id
  WHERE id_room_kelas='$id_room_kelas' 
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  while ($d = mysqli_fetch_assoc($q)) {
    if (isset($arr_data_act[$act][$d['id_sesi']])) {
      array_push($arr_data_act[$act][$d['id_sesi']], $d);
    } else {
      $arr_data_act[$act][$d['id_sesi']][0] = $d;
    }
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
  ) jadwal_kelas,
(SELECT COUNT(1) FROM tb_link_file WHERE jenis_file='ba' AND id_sesi=a.id) count_ba, 
(SELECT COUNT(1) FROM tb_link_file WHERE jenis_file='fp' AND id_sesi=a.id) count_fp, 
(SELECT COUNT(1) FROM tb_link_file WHERE jenis_file='va' AND id_sesi=a.id) count_va, 
(SELECT COUNT(1) FROM tb_link_file WHERE jenis_file='fl' AND id_sesi=a.id) count_fl 
FROM tb_sesi a 
WHERE a.id_room=$id_room 
ORDER BY a.no
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$total_count_sesi = mysqli_num_rows($q);
$div_lp = '';
$nav_lp = '';
$no_sesi = 0;
$count_file = [];
$warna = [
  0 => 'kuning',
  1 => 'hijau',
  2 => 'pink',
  3 => 'pink',
];
while ($sesi = mysqli_fetch_assoc($q)) {
  $kode_jenis = $sesi['jenis'];
  $count_sesi[$kode_jenis]++;

  $hide_lp = $sesi['id'] == $sesi_aktif['id'] ? '' : 'hideit';

  if ($kode_jenis === '0') {
    $div_lp .= "<div id=div_lp__$sesi[id] class='div_lp $hide_lp alert alert-info tengah' data-aos='fade'>Minggu tenang</div>";
  } elseif ($kode_jenis == 2) {
    $div_lp .= "<div id=div_lp__$sesi[id] class='div_lp $hide_lp alert alert-danger tengah' data-aos='fade'>Pekan UTS</div>";
  } elseif ($kode_jenis == 3) {
    $div_lp .= "<div id=div_lp__$sesi[id] class='div_lp $hide_lp alert alert-danger tengah' data-aos='fade'>Pekan UAS</div>";
  } elseif ($kode_jenis == 1) {

    $no_sesi++;
    $id_sesi = $sesi['id_sesi'];
    $nama_sesi = $sesi['nama_sesi'];
    // $count_sesi[$kode_jenis]++;

    $count_file['bahan_ajar'] = $sesi['count_ba'];
    $count_file['file_ppt'] = $sesi['count_fp'];
    $count_file['video_ajar'] = $sesi['count_va'];
    $count_file['file_lain'] = $sesi['count_fl'];


    # ============================================================
    # FITUR SESI HANDLER AT LOOP SESI NORMAL
    # ============================================================
    $str_fiturs = '';
    $is_icon = 0;
    foreach ($arr_fitur_sesi as $k => $arr) {
      if (($k == 'bertanya' || $k == 'tanam_soal') and !$sesi['tags']) {
        $str_fiturs .= "<div class='abu miring f12 mb1 bordered br5 p1'>belum bisa $k</div>";
      } elseif ($k == 'challenge' || $k == 'latihan') {
        $title = '';
        $tambah = $id_role == 2 ? "<a href='?tambah_activity&p=$k&id_sesi=$id_sesi'>$img_add</a>" : '';
        $sub_fitur = "<div class='abu miring f12'>belum ada $k</div>";

        if (isset($arr_data_act[$k][$id_sesi])) {
          $title = "<div class='mb1 green bold f12 proper'>$k</div>";
          $sub_fitur = '';
          $j = 0;
          foreach ($arr_data_act[$k][$id_sesi] as $k2 => $v2) {
            $j++;
            $btn_info = $v2['ket'] ? 'btn-info' : 'btn-secondary';
            $sub_fitur .= "<a href='?activity&jenis=$k&id_assign=$v2[id]' class='btn $btn_info btn-sm mb1 w-100'>$j. $v2[nama_act]</a> ";
          }
        }
        $str_fiturs .= "<div class='bordered br5 p1 mb1'>$title $sub_fitur $tambah</div>";
      } else {
        # ============================================================
        # BAHAN AJAR, PPT, VIDEO, FILE LAIN
        # ============================================================
        if ($is_icon) {
          if ($count_file[$k]) {
            $link = "
              <a href='?akses_link&f=$arr[param]&id_sesi=$id_sesi' onclick='return confirm(`$arr[title]?`)'>
                <img src='assets/img/ilustrasi/$k.png' class='icon_bahan_ajar' >
              </a>
            ";
          } else {
            $link = "
              <span onclick='return confirm(`$arr[title] pada sesi ini belum tersedia.`)'>
                <img src='assets/img/ilustrasi/$k.png' class='icon_bahan_ajar icon_bahan_ajar_disabled' >
              </span>
            ";
          }
          $str_fiturs .= "
            <div class='col-3'>
              <div class=' mt4 mb4 br5'>
                $link
              </div>
            </div>
          ";
        } else {
          $str_fiturs .= "
            <div>
              <a href='?$arr[param]&id_sesi=$id_sesi' class='btn btn-primary btn-sm mb1 w-100' onclick='return confirm(`$arr[title]?`)'>$arr[title]</a>
            </div>
          ";
        }
      }
      // if ($k == 'challenge') $str_fiturs .= "</div></div><div class='wadah'><div class='flexy flex-center'>";
      if ($k == 'challenge') {
        $str_fiturs .= "</div><div class='row'>";
        $is_icon = 1;
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

    # ============================================================
    # DIV LP LOOP
    # ============================================================
    $div_lp .= "
      <div class='$hide_lp div_lp' id=div_lp__$sesi[id]>
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

            <div class=wadah>
              <div class='kecil miring abu mb1 f10 tengah'>Opening: $opening</div>
              <div class='kecil miring abu tengah'><span class=darkblue>$awal_presensi_show <br>s.d <br>$akhir_presensi_show</span></div>
              <div class='kecil miring abu f10 tengah'>Closing: $closing</div>
            </div>

            <div class='mt1 tengah wadah'>
              <div class='kecil miring abu mb1 f10 tengah'>Di sesi ini kamu dapat:</div>
              <div class='flexy flex-center str_fiturs'>
                $str_fiturs
              </div>
            </div>
            <div class='mt1 tengah f10 abu'>Status: $pelaksanaan</div>
          </div>
        </div>
      </div>
    ";
  } else {
    die(div_alert('danger', "Jenis sesi: $sesi[jenis], belum terdefinisi."));
  }

  # ============================================================
  # NAV LP LOOP
  # ============================================================
  $caption = $kode_jenis == 1 ? $count_sesi[1] : 'U';
  $caption = $kode_jenis ? $caption : 'T';
  $nav_lp_selected = $hide_lp ? '' : 'nav_lp_selected';
  $nav_lp_active = $hide_lp ? '' : 'nav_lp_active';
  $nav_lp .= "<div class='gradasi-$warna[$kode_jenis] p1 pl2 pr2 br5 pointer nav_lp $nav_lp_selected $nav_lp_active' id=nav_lp__$sesi[id]>$caption</div>";
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
  Room 
  <span class=darkblue>$nama_room</span> 
  <div class='f10 flexy flex-center mt2' style=gap:1px>
    $nav_lp
  </div>
");

# ============================================================
# FINAL ECHO
# ============================================================
echo "<div class='flexy flex-center'><div style=max-width:700px>$div_lp</div></div>";

































/*
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

      });

    })
  </script>
<?php } */
?>
<script>
  $(function() {
    $('.nav_lp').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
      console.log(aksi, id);

      $('.div_lp').hide();
      $('#div_lp__' + id).fadeIn();
      $('.nav_lp').removeClass("nav_lp_selected");
      $('#nav_lp__' + id).addClass("nav_lp_selected");
    })
  });
</script>