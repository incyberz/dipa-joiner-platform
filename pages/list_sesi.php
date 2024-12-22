<?php
$arr_bahan_ajar = ['bahan_ajar', 'file_ppt', 'video_ajar', 'file_lain'];
include 'list_sesi-processors.php';
include 'list_sesi-styles.php';
include 'list_sesi-functions.php';

$img_ask = img_icon('ask');
$img_play_kuis = img_icon('gray');
$img_tanam_soal = img_icon('gray');
$img_gray = img_icon('gray');
$img_up = img_icon('up');
$img_up_disabled = img_icon('up_disabled');

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
  $id_sesi = $sesi['id'];
  $jenis_sesi = $sesi['jenis'];
  $count_sesi[$jenis_sesi]++;

  $hide_lp = $sesi['id'] == $sesi_aktif_id ? '' : 'hideit'; // hide all sesi except this

  if ($jenis_sesi === '0') {
    include 'list_sesi-loop_ui_minggu_tenang.php';
  } elseif ($jenis_sesi == 2) {
    include 'list_sesi-loop_ui_uts.php';
  } elseif ($jenis_sesi == 3) {
    include 'list_sesi-loop_ui_uas.php';
  } elseif ($jenis_sesi == 1) { // sesi normal

    $no_sesi++;

    # ============================================================
    # UI HANDLER AT LOOP SESI NORMAL
    # ============================================================
    $fiturs = []; // activities
    include 'list_sesi-loop_ui_handler.php';

    # ============================================================
    # JADWAL KELAS HANDLER AT LOOP SESI NORMAL
    # ============================================================
    $ui_jadwal = '';
    include 'list_sesi-loop_jadwal_handler.php';

    # ============================================================
    # BLOK EDIT SESI :: TRAINER ONLY
    # ============================================================
    $edit_sesi = '';
    include 'list_sesi-loop_edit_sesi.php';

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
            $ui_bahan_ajar            
            $ui_acts            
            $ui_jadwal
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
  include 'list_sesi-loop_nav.php';
}

# ============================================================
# FORM ADD SESI
# ============================================================
include 'list_sesi-add_sesi.php';

# ============================================================
# FORM ADD SESI
# ============================================================
include 'list_sesi-laporkan_error.php';

# ============================================================
# NAVIGASI SESI
# ============================================================
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
echo "
  <div class='flexy flex-center'>
    <div style=max-width:700px>
      $div_lp
      <div class='flexy flex-between'>
        $laporkan_error
        $add_sesi
      </div>
    </div>
  </div>
";

































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
              $('#belum_ada__' + field + '__' + id_sesi).hide();
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