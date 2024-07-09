<style>
  .blok_list_sesi {
    display: grid;
    grid-template-columns: 100px auto;
    grid-gap: 10px
  }

  @media (max-width:450px) {
    .blok_list_sesi {
      display: block;
    }
  }
</style>
<?php
# ============================================================
# SHOW LIST SESI
# ============================================================
// get list latihan
$s = "SELECT id as id_assign, id_sesi 
FROM tb_assign_latihan 
WHERE id_room_kelas='$id_room_kelas' 
ORDER BY id_latihan 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
while ($d = mysqli_fetch_assoc($q)) {
  if (isset($rlats[$d['id_sesi']])) {
    array_push($rlats[$d['id_sesi']], $d['id_assign']);
  } else {
    $rlats[$d['id_sesi']][0] = $d['id_assign'];
  }
}

// get list challenge
$s = "SELECT id as id_assign, id_sesi 
FROM tb_assign_challenge 
WHERE id_room_kelas='$id_room_kelas' 
ORDER BY id_challenge 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
while ($d = mysqli_fetch_assoc($q)) {
  if (isset($rchals[$d['id_sesi']])) {
    array_push($rchals[$d['id_sesi']], $d['id_assign']);
  } else {
    $rchals[$d['id_sesi']][0] = $d['id_assign'];
  }
}

// echo '<pre>';
// var_dump($rchals);
// echo '</pre>';
$count_sesi = [];
$count_sesi[0] = 0;
$count_sesi[1] = 0;
$count_sesi[2] = 0;
$count_sesi[3] = 0;


# ============================================================
# MAIN SELECT SESI
# ============================================================
$s = "SELECT 
a.nama as nama_sesi,
a.jenis,
a.durasi,
a.tags,
a.deskripsi,
a.awal_presensi,
a.status_pelaksanaan,
a.status_kelengkapan,
a.id as id_sesi,
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
while ($d_sesi = mysqli_fetch_assoc($q)) {
  if ($d_sesi['jenis'] === '0') {
    $div_list .= div_alert('info tenang', 'Minggu tenang');
  } elseif ($d_sesi['jenis'] == 2) {
    $div_list .= div_alert('danger tenang', 'Pekan UTS');
  } elseif ($d_sesi['jenis'] == 3) {
    $div_list .= div_alert('danger tenang', 'Pekan UAS');
  } elseif ($d_sesi['jenis'] == 1) {

    $no_sesi++;
    $id_sesi = $d_sesi['id_sesi'];
    $nama_sesi = $d_sesi['nama_sesi'];
    $jenis = $d_sesi['jenis'];
    $count_sesi[$jenis]++;

    $lats = '';
    if (isset($rlats[$id_sesi])) {
      $j = 0;
      foreach ($rlats[$id_sesi] as $key => $value) {
        $j++;
        $lats .= "<a href='?activity&jenis=latihan&id_assign=$value' class='btn btn-success btn-sm mb1' onclick='return confirm(\"Menuju laman Latihan?\")'>L$j</a> ";
      }
    }

    $chals = '';
    if (isset($rchals[$id_sesi])) {
      $j = 0;
      foreach ($rchals[$id_sesi] as $key => $value) {
        $j++;
        $chals .= "<a href='?activity&jenis=challenge&id_assign=$value' class='btn btn-danger btn-sm mb1' onclick='return confirm(\"Menuju laman Challenge?\")'>C$j</a> ";
      }
    }

    if ($d_sesi['tags'] != '') {
      $r = explode(', ', $d_sesi['tags']);
      sort($r);
      $tags_show = '<span class="darkblue kecil miring">' . implode(', ', $r) . '</span>';
      $asks = "<a href='?bertanya&id_sesi=$id_sesi' style='display:inline-block;margin-left:10px' onclick='return confirm(\"Ingin mengajukan pertanyaan pada sesi ini?\")'><img src='assets/img/icons/ask.png' class=zoom height=30px></a>";
    } else {
      $tags_show = '<span class="red kecil miring">belum ada tags</span>';
      $asks = '';
    }

    $nama_show = $d_sesi['nama_sesi'];
    $ket_show = $d_sesi['deskripsi'];
    $tags_show = $tags_show;
    $fitur_sesi = "$lats $chals $asks";

    $pelaksanaan = $d_sesi['jadwal_kelas'] ? $d_sesi['jadwal_kelas'] : '<span class="f12 miring abu">belum dilaksanakan</span>';

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
              <a href='?list_sesi&id_sesi=$id_sesi&no_sesi=$no_sesi&nama_sesi=$nama_sesi'>$img_edit</a>
            </div>
          </div>

          <div class='kecil miring abu mt3 mb1'>Nama dan deskripsi sesi</div>
          <div class='nama_sesi f14 bold darkblue'>$nama_show</div>
          <div class='kecil miring abu'>$ket_show</div>

          <div class='kecil miring abu mt3 mb1'>Tag-tag materi</div>
          <div class='mb3'>$tags_show</div>

          <div class='mt1'>$fitur_sesi</div>
          <div class='mt1'>$pelaksanaan</div>
        </div>
      </div>
    ";
  } else {
    die(div_alert('danger', "Jenis sesi: $d_sesi[jenis], belum terdefinisi."));
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