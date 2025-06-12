<style>
  .absen {
    display: inline-block;
    height: 25px;
    width: 25px;
    text-align: center;
    color: white;
    border-radius: 50%;
  }

  .absen-sakit {
    background: gray;
  }

  .absen-izin {
    background: yellow;
    color: black;
  }

  .absen-alfa {
    background: red;
  }

  @media (max-width: 775px) {
    .desktop_only_775 {
      display: none;
    }
  }
</style>

<?php
# =================================================================
instruktur_only();

$img_ontime = img_icon('check');
$img_late = img_icon('check_brown');
$img_reject = img_icon('reject');
$icon_mhs = img_icon('mhs');
$icon_gray = '<span class="br50 pointer" style="display:inline-block;width:20px;height:20px;background:#ccc;">&nbsp;</span>';

$get_kelas = $_GET['kelas'] ?? '';
$get_id_sesi = $_GET['id_sesi'] ?? '';
$nav_mode = "Table Mode | Show Profile | Mobile Mode";
$nav_mode = ''; //zzz on develop
set_h2('Rekap Presensi', "<a href='?presensi'>$img_prev</a> $nav_mode");

$param_awal = 'presensi_rekap';
include 'navigasi_room_kelas.php';

$show_absen = [
  -1 => "<span class='absen absen-sakit'>S</span>",
  -2 => "<span class='absen absen-izin'>I</span>",
  -9 => "<span class='absen absen-alfa'>A</span>",
];

$nav_id_sesi = '';


# ====================================================
# INITIAL VALUE
# ====================================================
$rid_sesi = [];

# ====================================================
# GET DATA ARRAY SESI
# ====================================================
$s = "SELECT a.*, 
a.id as id_sesi, 
a.nama as nama_sesi,
(
  SELECT count(1) FROM tb_sesi WHERE id=a.id 
  AND '$today' > awal_presensi) is_presensi_aktif 
FROM tb_sesi a 
WHERE a.id_room=$id_room
AND jenis=1";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$count_sesi_aktif = 0;
if (!mysqli_num_rows($q)) {
  echo div_alert('danger', "Belum ada sesi pada $Room $singkatan_room.");
} else {
  while ($d = mysqli_fetch_assoc($q)) {
    $rid_sesi[$d['no']] = $d['id_sesi'];
    $arr_is_sesi_aktif[$d['id_sesi']] = $d['is_presensi_aktif'];
    if ($d['is_presensi_aktif']) $count_sesi_aktif++;
  }
}

# ====================================================
# GET LIST PESERTA
# ====================================================
$sql_kelas = $target_kelas ? "c.kelas='$target_kelas'" : '1';
if ($get_kelas) $sql_kelas = "c.kelas = '$get_kelas'";

$s = "SELECT 
a.id as id_kelas_peserta, 
b.id as id_peserta, 
b.nama as nama_peserta ,
b.image,
b.war_image,
c.kelas,
(
  SELECT id FROM tb_sesi 
  WHERE id_room=$id_room 
  AND awal_presensi<='$today' 
  AND akhir_presensi>'$today'
  LIMIT 1) id_sesi 
FROM tb_kelas_peserta a 
JOIN tb_peserta b ON a.id_peserta=b.id 
JOIN tb_kelas c ON a.kelas=c.kelas  
JOIN tb_room_kelas d ON c.kelas=d.kelas 
WHERE d.id_room=$id_room 
AND b.status=1 -- _peserta aktif
AND b.id_role = 1  -- _peserta only
-- AND $sql_kelas 
AND c.kelas = '$get_kelas'
ORDER BY c.shift, c.kelas, b.nama 
";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$table_tr = '';
if (!mysqli_num_rows($q)) {
  if ($get_kelas)
    echo div_alert('danger', "Belum ada <a href='?peserta_kelas'>$Peserta pada kelas [$get_kelas] ini</a>.");
} else {

  $table_tag = "<table class='table mt4'>";

  $th_sesi = '';
  $j = 0;
  foreach ($rid_sesi as $no => $id_sesi) {
    $j++;
    $desktop_only_775 = $j < $count_sesi_aktif  ? 'desktop_only_775' : '';
    if ($get_id_sesi) $nav_id_sesi .= "
      <a class='nav-id_sesi d-block hover' href=?presensi_rekap&kelas=$get_kelas&id_sesi=$id_sesi>P$j</a>
    ";
    if ($get_id_sesi and $id_sesi != $get_id_sesi) continue;
    $th_sesi .= "
      <th class=$desktop_only_775>
        <a href='?presensi_rekap&kelas=$get_kelas&id_sesi=$id_sesi'>P$j
      </th>
    ";
  }

  $thead = "
    <thead class='gradasi-toska'>
      <th>No</th>
      <th>Peserta / Kelas</th>
      $th_sesi
    </thead>
  ";

  $table_tr = '';
  $i = 0;
  $last_kelas = '';
  while ($d = mysqli_fetch_assoc($q)) {
    $id_kelas_peserta = $d['id_kelas_peserta'];
    $nama = ucwords(strtolower($d['nama_peserta']));
    $d['war_image'] = $d['war_image'] ? $d['war_image'] : $d['image'];

    $s2 = "SELECT id as id_sesi,
    a.awal_presensi,
    a.akhir_presensi,
    (
      SELECT is_ontime FROM tb_presensi 
      WHERE id_peserta=$d[id_peserta] 
      AND id_sesi=a.id) is_ontime,
    (
      SELECT tanggal FROM tb_presensi 
      WHERE id_peserta=$d[id_peserta] 
      AND id_sesi=a.id) tanggal_presensi,
    (SELECT absen FROM tb_absen WHERE id_sesi=a.id AND id_peserta=$d[id_peserta]) kode_absen   
    FROM tb_sesi a 
    WHERE a.id_room=$id_room 
    AND jenis=1
    ";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    $td_presensi = '';
    $j = 0;
    while ($d2 = mysqli_fetch_assoc($q2)) {

      // skip jika filtered by Pertemuan ke-
      if ($get_id_sesi and $d2['id_sesi'] != $get_id_sesi) continue;

      $icon_hadir = '';
      if ($d2['kode_absen']) {
        $icon_hadir = $show_absen[$d2['kode_absen']];
      } else {
        if ($d2['is_ontime'] === '1') {
          $icon_hadir = $img_ontime;
        } elseif ($d2['is_ontime'] === '0') {
          $icon_hadir = $img_late;
        } else {
          if ($arr_is_sesi_aktif[$d2['id_sesi']]) {
            $icon_hadir = $img_reject;
          } else {
            $icon_hadir = '-';
          }
        }
      }
      $j++;
      $desktop_only_775 = $j < $count_sesi_aktif  ? 'desktop_only_775' : '';
      # ============================================================
      # ICON HADIR
      # ============================================================
      $id_cell = "$d[id_peserta]--$d2[id_sesi]";
      $td_presensi .= "
        <td class='$desktop_only_775' id=$id_cell>
          <span class='presensi_toggle' id=presensi_toggle--$id_cell>$icon_hadir</span>
          <div class='hideit blok_set_presensi' id=blok_set_presensi--$id_cell>
            <div class='card p-2 pt-2 f12'>
              <div>Tanggal Presensi: $d2[tanggal_presensi]</div>
              <div>Batasan Presensi: $d2[awal_presensi] - $d2[akhir_presensi]</div>
              <div class='mt2 f16 flexy consolas' style=gap:2px>
                <button class='btn btn-warning btn-sm set_cell' id=set_cell--$id_cell--kode-1>S</button>
                <button class='btn btn-warning btn-sm set_cell' id=set_cell--$id_cell--kode-2>I</button>
                <button class='btn btn-danger btn-sm set_cell' id=set_cell--$id_cell--kode-9>A</button>
                <button class='btn btn-success btn-sm set_cell' id=set_cell--$id_cell--kode1>H</button>
              </div>

            </div>
            </div>
        </td>
      ";
    }

    # ==============================================================
    # FINAL OUTPUT :: SHOW IMAGE OR COMPACT
    # ==============================================================
    if ($last_kelas != $d['kelas']) {
      $table_tr .= "</table>$table_tag$thead";
      $i = 0;
    }

    $path = "$lokasi_profil/$d[war_image]";
    $path_na = 'assets/img/no_profil.jpg';
    if (file_exists($path)) {
      $src_profile = $path;
      $icon = $icon_mhs;
      $poin = 1000;
    } else {
      $src_profile = $path_na;
      $icon = $icon_gray;
      $poin = 200;
    }

    $id[-2] = "set_absen__$d[id_peserta]__$d[id_sesi]__0__-2";
    $id[-1] = "set_absen__$d[id_peserta]__$d[id_sesi]__0__-1";
    $id[0] = "set_absen__$d[id_peserta]__$d[id_sesi]__0__0";
    $id[1] = "set_absen__$d[id_peserta]__$d[id_sesi]__1000__1";
    $id[2] = "set_absen__$d[id_peserta]__$d[id_sesi]__1000__2";

    $blok_sia = "
      <div class=flexy style=gap:2px>
        <button class='consolas btn btn-warning btn-sm set_absen' id=$id[-2]>I</button>
        <button class='consolas btn btn-warning btn-sm set_absen' id=$id[-1]>S</button>
        <button class='consolas btn btn-danger btn-sm set_absen' id=$id[0]>A</button>
        <button class='consolas btn btn-success btn-sm set_absen' id=$id[1]>H</button>
        <button class='consolas btn btn-info btn-sm set_absen' id=$id[2]>D</button>
      </div>
    ";

    $i++;
    $table_tr .= "
      <tr>
        <td>$i</td>
        <td>
          $nama <span class=btn_aksi id=profil_peserta$d[id_peserta]__toggle>$icon</span>
          <div class='f12 abu'>$d[kelas]</div>
          <div class='hideit ' id=profil_peserta$d[id_peserta]>
            <img src='$src_profile' class=foto_profil>
            $blok_sia
          </div>
        </td>
        $td_presensi
      </tr>
    ";


    $last_kelas = $d['kelas'];
  } // end while list _peserta


  echo "
    <div class='d-flex gap-2 justify-content-center'>$nav_id_sesi</div>
    $table_tr
    </table>
  ";
}









?>
<script>
  $(function() {
    $('.set_absen').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id_peserta = rid[1];
      let id_sesi = rid[2];
      let poin = rid[3];
      let kode_absen = rid[4];

      let link_ajax = `ajax/ajax_set_presensi_offline.php?id_peserta=${id_peserta}&id_sesi=${id_sesi}&poin=${poin}&kode_absen=${kode_absen}`;
      console.log(link_ajax);
      $.ajax({
        url: link_ajax,
        success: function(a) {
          if (a.trim() == 'sukses') {
            alert('sukses');
          } else {
            alert(a);
          }
        }
      })
    });

    // saat icon presensi tiap cell di klik
    $('.presensi_toggle').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('--');
      let aksi = rid[0];
      let id_peserta = rid[1];
      let id_sesi = rid[2];
      console.log(id_peserta, id_sesi);
      $('.blok_set_presensi').hide();
      $(`#blok_set_presensi--${id_peserta}--${id_sesi}`).show();
    });

    // saat icon S-I-A-H tiap cell di klik
    $('.set_cell').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('--');
      let aksi = rid[0];
      let id_peserta = rid[1];
      let id_sesi = rid[2];
      let kode = rid[3];
      let kode_absen = kode.replace('kode', '');
      let poin = 0;

      let link_ajax = `ajax/ajax_set_absen.php?id_peserta=${id_peserta}&id_sesi=${id_sesi}&poin=${poin}&kode_absen=${kode_absen}`;
      console.log(link_ajax);
      $.ajax({
        url: link_ajax,
        success: function(a) {
          if (a.trim() == 'OK') {
            location.reload();
          } else {
            alert(a);
          }
        }
      })
    });
  })
</script>