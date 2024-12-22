<?php
include 'list_sesi-processors.php';
include 'list_sesi-styles.php';
include 'list_sesi-functions.php';

$img_ask = img_icon('ask');
$img_play_kuis = img_icon('gray');
$img_tanam_soal = img_icon('gray');
$img_gray = img_icon('gray');
$img_up = img_icon('up');

# ============================================================
# ARR FITUR SESI
# ============================================================
include 'list_sesi-arr_fitur_sesi.php';

# ============================================================
# ARR LATIHAN DAN CHALLENGE
# ============================================================
include 'list_sesi-arr_latihan.php';

$count_sesi = [];
$count_sesi[0] = 0; // sesi tenang
$count_sesi[1] = 0; // normal
$count_sesi[2] = 0; // uts
$count_sesi[3] = 0; // uas

# ============================================================
# MAIN SELECT SESI
# ============================================================
$s = "SELECT 
a.*,
(
  SELECT p.jadwal_kelas FROM tb_sesi_kelas p 
  WHERE p.id_sesi=a.id 
  AND p.kelas='$kelas' 
  -- AND p.is_terlaksana=1 
  ) jadwal_kelas 
  
FROM tb_sesi a 
WHERE a.id_room=$id_room 
ORDER BY a.no
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$total_count_sesi = mysqli_num_rows($q);
$div_lp = ''; // div learning path
$nav_lp = ''; // navigasi antar sesi
$no_sesi = 0;
$warna = [ // warna jenis sesi
  0 => 'kuning',
  1 => 'hijau',
  2 => 'pink',
  3 => 'pink',
];

$sesi_aktif_id = $sesi_aktif['id'] ?? null;

while ($sesi = mysqli_fetch_assoc($q)) {
  $kode_jenis = $sesi['jenis'];
  $count_sesi[$kode_jenis]++;

  $hide_lp = $sesi['id'] == $sesi_aktif_id ? '' : 'hideit';

  if ($kode_jenis === '0') {
    $div_lp .= "<div id=div_lp__$sesi[id] class='div_lp $hide_lp alert alert-info tengah' data-aos='fade'>Minggu tenang</div>";
  } elseif ($kode_jenis == 2) {
    $div_lp .= "<div id=div_lp__$sesi[id] class='div_lp $hide_lp alert alert-danger tengah' data-aos='fade'>Pekan UTS</div>";
  } elseif ($kode_jenis == 3) {
    $div_lp .= "<div id=div_lp__$sesi[id] class='div_lp $hide_lp alert alert-danger tengah' data-aos='fade'>Pekan UAS</div>";
  } elseif ($kode_jenis == 1) {

    $no_sesi++;
    $id_sesi = $sesi['id'];

    # ============================================================
    # FITUR SESI HANDLER AT LOOP SESI NORMAL
    # ============================================================
    $acts = []; // activities
    $str_fiturs = '';
    foreach ($arr_fitur_sesi as $k => $arr) {
      if (($k == 'bertanya' || $k == 'tanam_soal') and !$sesi['tags']) {
        $str_fiturs = "<div class='abu miring f12 mb1 bordered br5 p1'>belum bisa $k</div>";
      } elseif ($k == 'challenge' || $k == 'latihan') {
        $title = '';
        $tambah = $id_role == 2 ? "<a href='?tambah_activity&p=$k&id_sesi=$sesi[id]'>$img_add</a>" : '';
        $sub_fitur = "<div class='abu miring f12'>belum ada $k</div>";

        if (isset($arr_data_act[$k][$id_sesi])) {
          $title = "<div class='mb1 green bold f12 proper'>$arr[title]</div>";
          $sub_fitur = '';
          $j = 0;
          foreach ($arr_data_act[$k][$id_sesi] as $k2 => $v2) {
            $j++;
            $btn_info = $v2['ket'] ? 'btn-info' : 'btn-secondary';
            $sub_fitur .= "<a href='?activity&jenis=$k&id_assign=$v2[id]' class='btn $btn_info btn-sm mb1 w-100'>$j. $v2[nama_act]</a> ";
          }
        }
        $str_fiturs = "<div class='bordered br5 p1 mb1'>$title $sub_fitur $tambah</div>";
      } elseif ($k == 'bahan_ajar' || $k == 'file_ppt' || $k == 'video_ajar' || $k == 'file_lain') {
        # ============================================================
        # BAHAN AJAR, PPT, VIDEO, FILE LAIN
        # ============================================================
        if ($sesi[$k]) {
          $str_fiturs = "
            <a href='?akses_link&f=$arr[param]&id_sesi=$id_sesi' onclick='return confirm(`$arr[title]?`)'>
              <img src='assets/img/ilustrasi/$k.png' class='icon_bahan_ajar' >
              <div class='f12 abu mt1'>$arr[title]</div>
            </a>
          ";
        } else {
          $str_fiturs = "
            <span onclick='return confirm(`$arr[title] pada sesi ini belum tersedia.`)'>
              <img src='assets/img/ilustrasi/$k.png' class='icon_bahan_ajar icon_bahan_ajar_disabled' >
              <div class='f12 abu mt1'>$arr[title]</div>
            </span>
          ";
        }
      } else { // button only
        $str_fiturs = "
          <div>
            <a href='?$arr[param]&id_sesi=$id_sesi' class='btn btn-primary btn-sm mb1 w-100' onclick='return confirm(`$arr[title]?`)'>$arr[title]</a>
          </div>
        ";
      }

      $acts[$k] = $str_fiturs;
    }



    if ($sesi['tags'] != '') {
      $r = explode(', ', $sesi['tags']);
      sort($r);
      $tags_show = '<span class="darkblue kecil miring">' . implode(', ', $r) . '</span>';
    } else {
      $tags_show = '<span class="red kecil miring">belum ada tags</span>';
    }

    $tags_show = $tags_show;

    if ($sesi['jadwal_kelas']) {
      $status_pelaksanaan = eta2($sesi['jadwal_kelas']);
      $jadwal_kelas_show = hari_tanggal($sesi['jadwal_kelas']);
    } else {
      $jadwal_kelas_show = $null;
      if ($id_role == 1) {
        $link_encoded = urlencode(get_current_url());
        $text_wa = "Yth. $Bapak $trainer[nama], saya $user[nama] ingin melaporkan bahwa Jadwal Kuliah di LMS untuk sesi $sesi[no] belum ditentukan. Terimakasih.%0a%0aLink:%0a$link_encoded%0a%0aFrom: DIPA Joiner System, $datetime";
        $href_wa = href_wa($trainer['no_wa'], $text_wa);
        $set_presensi = "<a class='btn btn-success w-100 mt4' href='$link_wa' onclick='return confirm(`Laporkan?`)'>$img_wa Laporkan</a>";
      } else {
        $set_presensi = "<a href='?presensi' >Set</a>";
      }
      $status_pelaksanaan = div_alert('danger', "Jadwal Kelas untuk sesi ini belum ditentukan. $set_presensi");
      $sesi['jadwal_kelas'] ? '' : '<span class="f12 miring abu">belum dilaksanakan</span>';
    }

    // $awal_presensi_show = hari_tanggal($sesi['awal_presensi']);
    // $akhir_presensi_show = hari_tanggal($sesi['akhir_presensi']);
    // $opening = eta2($sesi['awal_presensi']);
    $closing = eta2($sesi['akhir_presensi']);

    $edit_sesi = $id_role == 1 ? '' : "
      <div class='flexy flex-between desktop_only mb2'>
        <div class='f10 abu'>id. $id_sesi</div>
        <div class='f10 abu'>
          <a class=hideit href='?list_sesi&mode=edit'>$img_edit</a>
          <span class=mode_edit id=mode_edit__$id_sesi >$img_edit</span>
        </div>
      </div>
    ";

    # ============================================================
    # UI FIELDS
    # ============================================================
    $ui_nama = create_ui('nama', $sesi['nama'], $id_sesi, '', 'f18 bold darkblue');
    $ui_deskripsi = create_ui('deskripsi', $sesi['deskripsi'], $id_sesi, '', null, true);
    $ui_tags = create_ui('tags', $sesi['tags'], $id_sesi, 'Tags Materi', 'darkblue f14', true);

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
            $edit_sesi
            $ui_nama            
            $ui_deskripsi            
            $ui_tags            

            <div class='row tengah mb2'>
              <div class='mt2 col-6 col-md-3'>
                $acts[bahan_ajar]
              </div>
              <div class='mt2 col-6 col-md-3'>
                $acts[file_ppt]
              </div>
              <div class='mt2 col-6 col-md-3'>
                $acts[video_ajar]
              </div>
              <div class='mt2 col-6 col-md-3'>
                $acts[file_lain]
              </div>
            </div>


            <div class='mt1 tengah pt1' style='border-top:solid 3px #cdc'>
              <div class='kecil miring abu mb2 f10 tengah'>Aktivitas Pembelajaran:</div>
              <div class='row'>
                <div class='col-md-4 mb2'>
                  $acts[play_kuis]
                </div>
                <div class='col-md-4 mb2'>
                  $acts[tanam_soal]
                </div>
                <div class='col-md-4 mb2'>
                  $acts[bertanya]
                </div>
                <div class='col-md-6'>
                  $acts[latihan]
                </div>
                <div class='col-md-6'>
                  $acts[challenge]
                </div>
              </div>
            </div>

            <div  class='mt1 pt1 mb4' style='border-top:solid 3px #cdc'>
              <div class='f10 abu'><div>Jadwal Kuliah  [$kelas]</div>$jadwal_kelas_show ($status_pelaksanaan)</div>
              <div class='kecil miring abu f10'>Closing Presensi: $closing</div>
            </div>

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
# TIDAK ADA SESI AKTIF
# ============================================================
if (!$sesi_aktif_id) {
  $div_lp .= "
    <div class='alert alert-warning tengah' id=perhatian>
      Perhatian! Hari ini tidak ada sesi perkuliahan yang aktif.<hr>Kamu tidak bisa presensi, tapi masih dapat melakukan aktivitas belajar lainnya.<hr>
      <button class='btn btn-sm btn-info btn_aksi' id=perhatian__toggle>OK</button>
    </div>
  ";
}


# ============================================================
# FINAL ECHO
# ============================================================
echo "<div class='flexy flex-center'><div style=max-width:700px>$div_lp</div></div>";

































if ($id_role == 2) { ?>
  <script>
    $(function() {
      $('.mode_edit').click(function() {
        let tid = $(this).prop('id');
        let rid = tid.split('__');
        let aksi = rid[0];
        let id_sesi = rid[1];
        console.log(aksi, id_sesi);
        $('.ui_view').slideToggle();
        $('.ui_edit').slideToggle();
      });

      $('.input_editable').keyup(function() {
        let tid = $(this).prop('id');
        let rid = tid.split('__');
        let field = rid[0];
        let id_sesi = rid[1];
        let isi_lama = $('#isi_lama__' + field + '__' + id_sesi).text();
        let isi_baru = $(this).val().trim();
        if (isi_lama == isi_baru) {
          $('#btn_save__' + field + '__' + id_sesi).slideUp();
        } else {
          $('#btn_save__' + field + '__' + id_sesi).slideDown();
        }
      });

      $('.btn_save').click(function() {
        // alert($(this).prop('id'))
        let tid = $(this).prop('id');
        let rid = tid.split('__');
        let field = rid[1];
        let id_sesi = rid[2];

        let aksi = 'ubah';
        let field_id_value = id_sesi;
        let field_target = field;
        let isi_baru = $('#' + field + '__' + id_sesi).val();

        let isi_lama = $('#isi_lama__' + field + '__' + id_sesi).text();
        console.log(field, id_sesi, isi_baru, isi_lama);

        if (isi_lama == isi_baru) return;
        if (isi_baru == '') {
          let y = confirm('Ingin mengosongkan data?');
          if (!y) {
            $('#' + tid).val(isi_lama); // rollback value
            return;
          } else {
            isi_baru = 'null';
          }
        }

        // manage tags
        if (field == 'tags') {
          isi_baru = isi_baru
            .replace(/;/gim, ',')
            .replace(/[!@#$%^&*()+\-=\[\]{};:'`"\\|<>\/?~]/gim, '');
          let r = isi_baru.split(',');

          let r2 = [];
          r.forEach(el => {
            el = el.trim().toLowerCase();
            if (el) r2.push(el);
          });

          isi_baru = r2.sort().join(', ');
        }

        let link_ajax = 'ajax/ajax_crud.php?aksi=ubah&tb=sesi' +
          '&field_id_value=' + field_id_value +
          '&field_target=' + field_target +
          '&isi_baru=' + isi_baru;
        $.ajax({
          url: link_ajax,
          success: function(a) {
            if (a.trim() == 'sukses') {
              $('#isi_lama__' + field + '__' + id_sesi).text(isi_baru);
              $('#' + field + '__' + id_sesi).val(isi_baru); // update for tags
              $('#' + tid).slideUp();
            } else {
              alert(a)
            }
          }
        })

      });

    })
  </script>
<?php }
?>
<script>
  $(function() {
    $('.nav_lp').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
      console.log(aksi, id);

      $('#perhatian').slideUp();
      $('.div_lp').hide();
      $('#div_lp__' + id).fadeIn();
      $('.nav_lp').removeClass("nav_lp_selected");
      $('#nav_lp__' + id).addClass("nav_lp_selected");
    })
  });
</script>