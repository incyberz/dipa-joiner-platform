<?php
login_only();
$judul = 'Peserta Kelas';
set_title($judul);

# ================================================================
# NAVIGATION VARIABLES AND VIEW RULES
# ================================================================
$blok_kelas = '';
$img_detail = img_icon('detail');
$get_kelas = $_GET['kelas'] ?? '';
$get_keyword = $_GET['keyword'] ?? '';
$get_mode = $_GET['mode'] ?? 'fast';
$jumlah_peserta = 0;
// mode untuk instruktur: detail || full

// peserta hanya bisa melihat kelas nya saja
$sql_kelas = ($id_role == 1 and $get_kelas == '') ? "a.kelas = '$kelas'" : '1';

// peserta hanya mode fast only
if ($get_mode != 'fast' and $id_role == 1) $get_mode = 'fast';

// terdapat kalkulasi sesi aktif untuk mode detail
if ($get_mode != 'fast') include 'include/date_managements.php';














# ================================================================
# MAIN SELECT
# ================================================================
$s = "SELECT a.kelas, a.id as id_room_kelas 
FROM tb_room_kelas a 
WHERE a.id_room=$id_room 
AND $sql_kelas 
AND a.kelas != 'INSTRUKTUR' 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
while ($d = mysqli_fetch_assoc($q)) { // loop room kelas

  $s2 = "SELECT 
  d.id as id_peserta,
  d.nama,
  d.image,
  d.war_image,
  b.kelas

  FROM tb_kelas_peserta a 
  JOIN tb_kelas b ON a.kelas=b.kelas 
  JOIN tb_room_kelas c ON b.kelas=c.kelas 
  JOIN tb_peserta d ON a.id_peserta=d.id  
  WHERE c.id=$d[id_room_kelas] 
  AND b.status=1 
  AND d.status=1 
  AND d.nama NOT LIKE '%dummy%'
  ORDER BY b.shift, b.prodi,d.nama";

  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));

  if (mysqli_num_rows($q2)) {
    $list_peserta = '';
  } else {
    $list_peserta = div_alert('danger', "Tidak ada peserta pada kelas $d[kelas]. | <a href='?assign_peserta_kelas&kelas=$d[kelas]'>$img_add Assign</a>");
  }

  $no = 0;
  while ($d2 = mysqli_fetch_assoc($q2)) {
    $nama = ucwords(strtolower($d2['nama']));
    $jumlah_peserta++;

    if ($get_mode == 'fast') {
      $src2 = "$lokasi_profil/peserta-$d2[id_peserta].jpg";
      $src = "$lokasi_profil/wars/peserta-$d2[id_peserta].jpg";
      $sty = '';
      $link_super_delete = '';
      if (file_exists($src)) {
        // do nothing
      } elseif (file_exists($src2) and !file_exists($src)) {
        // profil ada tapi belum jadi profil wars
        $sty = 'border:solid 3px blue';
        $src = $src2;
      } else {
        $src = 'assets/img/img_na.jpg';
        $link_super_delete = "<a href='?super_delete_peserta&keyword=$d2[nama]'>$img_delete</a>";
      }
      $list_peserta .= "
      <div class='kecil tengah abu'>
        <img src='$src' class='foto_profil' style='$sty'>
        <div>$nama $link_super_delete</div>
      </div>";
    } elseif ($get_mode == 'detail') {
      $no++;

      # ================================================================
      # GET DATA POIN TIAP PESERTA
      # ================================================================
      $s3 = "SELECT * FROM tb_poin WHERE id_peserta=$d2[id_peserta] AND id_room=$id_room";
      $q3 = mysqli_query($cn, $s3) or die(mysqli_error($cn));

      $d_poin = mysqli_fetch_assoc($q3);
      if ($d_poin) {
        $d_poin_show = '';
        foreach ($d_poin as $key => $value) {
          $d_poin_show .= "<div><span class='abu miring'>$key:</span> $value</div>";
        }
      } else {
        $d_poin_show = "<span class=red>Belum ada data poin untuk peserta ini</span>";
      }

      # ================================================================
      # GET PRESENSI TIAP PESERTA
      # ================================================================
      if (1) {
        $target_kelas_presensi = $d2['kelas'];
        $s3 = "SELECT a.*,
        a.id as id_sesi,
        (
          SELECT 1 FROM tb_presensi 
          WHERE id_peserta=$d2[id_peserta] 
          AND id_sesi=a.id) sudah_presensi, 
        (
          SELECT 1 FROM tb_presensi 
          WHERE id_peserta=$d2[id_peserta] 
          AND id_sesi=a.id
          AND is_ontime=1) sudah_presensi_ontime
  
        FROM tb_sesi a 
        WHERE a.id_room=$id_room";
        // echo "<pre>$s3</pre>";
        $q3 = mysqli_query($cn, $s3) or die(mysqli_error($cn));
        $presenters_kelas_last_active_sesi = 0;
        $sesi_aktif = 0;
        $count_presensi = 0; // jumlah_presensi tiap peserta
        $count_presensi_ontime = 0; // yang ontime saja
        while ($d3 = mysqli_fetch_assoc($q3)) {
          $sudah_presensi = $d3['sudah_presensi'];
          if ($sudah_presensi) $count_presensi++;
          $sudah_presensi_ontime = $d3['sudah_presensi_ontime'];
          if ($sudah_presensi_ontime) $count_presensi_ontime++;

          $awal_presensi = $d3['awal_presensi'];
          $akhir_presensi = $d3['akhir_presensi'];

          $tnow = strtotime('now');
          $tawal = strtotime($awal_presensi);
          $takhir = strtotime($akhir_presensi);

          # ===============================================================
          # MINGGU AKTIF SAAT INI
          # ===============================================================
          $is_telat_now = 0;
          $sudah_dibuka = $tnow >= $tawal ? 1 : 0;
          $belum_ditutup = $tnow < $takhir ? 1 : 0;
          $sedang_berlangsung = $sudah_dibuka && $belum_ditutup ? 1 : 0;

          $sesi_aktif++;
          if ($sudah_dibuka) { // sudah dibuka

            if ($belum_ditutup) { // berlangsung
            } else { // sudah dibuka, dan sudah ditutup (lampau, telat presensi)
              $is_telat_now = 1;
            }
          } else { // belum dibuka
            $sesi_aktif--;
          }
        }

        $presensi_show = "
        <div><span class='abu miring f12'>Presensi:</span> $count_presensi of $sesi_aktif</div>
        <div><span class='abu miring f12'>Ontime:</span> $count_presensi_ontime of $sesi_aktif</div>
        ";
      }


      # ================================================================
      # GET ASSIGNED DATA LATIHAN UNTUK KELAS INI TIAP PESERTA
      # ================================================================
      $data_jenis['latihan'] = '';
      $data_jenis['challenge'] = '';
      $arr_jenis = ['latihan', 'challenge'];
      foreach ($arr_jenis as $jenis) {
        $s3 = "SELECT a.id as id_assign, 
        a.is_wajib,
        b.nama,
        (b.basic_point + b.ontime_point) as sum_point,
        c.no, 
        (
          SELECT 1 FROM tb_bukti_$jenis 
          WHERE id_assign_$jenis=a.id 
          AND id_peserta=$d2[id_peserta]) sudah_mengerjakan,   
        (
          SELECT status FROM tb_bukti_$jenis 
          WHERE id_assign_$jenis=a.id 
          AND id_peserta=$d2[id_peserta]) status_mengerjakan   
        FROM tb_assign_$jenis a 
        JOIN tb_$jenis b ON a.id_$jenis=b.id 
        JOIN tb_sesi c ON a.id_sesi=c.id 
        -- WHERE no is not null 
        WHERE 1  
        AND id_room_kelas='$d[id_room_kelas]'
        order by c.no, sum_point";
        // echo "<pre>$s3</pre>";
        $q3 = mysqli_query($cn, $s3) or die(mysqli_error($cn));
        $count_latihan = mysqli_num_rows($q3);
        $count_latihan_wajib = 0;
        $count_sudah_mengerjakan = 0;
        if (!$count_latihan) {
          $data_jenis[$jenis] .= div_alert('danger', "Belum ada $jenis pada room ini. ~ <a href='?activity&jenis=$jenis'>$img_add </a>");
        } else {
          $rno = '';
          while ($d3 = mysqli_fetch_assoc($q3)) {
            if ($d3['is_wajib']) $count_latihan_wajib++;
            if ($d3['sudah_mengerjakan']) $count_sudah_mengerjakan++;
            $primary = $d3['sudah_mengerjakan'] ? 'warning' : 'primary';
            $primary = $d3['status_mengerjakan'] ? 'success' : $primary;
            $sum_point = number_format($d3['sum_point'], 0);
            $is_wajib_icon = $d3['is_wajib'] ? '<b class="consolas f12 gradasi-kuning red bordered br10" style="padding: 5px 10px">Wajib</b>' : '';
            $rno .= "
              <div>
                <a class='btn btn-$primary btn-sm mb2' href='?activity&jenis=$jenis&id_assign=$d3[id_assign]'>
                  P$d3[no] 
                  ~ 
                  $d3[nama]
                  ~ 
                  $sum_point
                </a> $is_wajib_icon
              </div>
            ";
          }

          $data_jenis[$jenis] .= "
            <div class=><span class='proper f12 abu miring'>$jenis :</span> $count_sudah_mengerjakan of $count_latihan</div>
            <div class=' hideit'><span class='proper f12 abu miring'>wajib :</span> $count_latihan_wajib</div>
            <div class='wadah hideit'>
              $rno
            </div>
          ";
        }
      }

      $uts_manual = '-';
      $uas_manual = '-';
      $uts_pg = '-';
      $uas_pg = '-';

      $akumulasi_poin = $d_poin['akumulasi_poin'] ?? 0;
      $src = "$lokasi_profil/wars/$d2[war_image]";
      $src_na = "$lokasi_img/img_na.jpg";
      $src = file_exists($src) ? $src : $src_na;

      $boleh_delete = 0;
      $btn_delete = $boleh_delete ? "<a href='#'>$img_delete</a>" : "<span onclick='alert(`Tidak bisa menghapus peserta ini karena sudah pernah melaksanakan aktifitas belajar.`)'>$img_delete_disabled</span>";

      $list_peserta .= "
        <tr>
          <td>$no</td>
          <td class='kecil tengah abu'>
            <img src='$src' class='foto_profil'>
          </td>
          <td>
            <div>$nama</div>
            <div class='f12 abu'>$d2[kelas]</div>
            <div class='f12 abu'>Points: $akumulasi_poin <span class=btn_aksi id=detail_poin$d2[id_peserta]__toggle>$img_detail</span></div>
            <div id=detail_poin$d2[id_peserta] class='wadah hideit f12'>
              $d_poin_show
            </div>
          </td>
          <td>
            <div>$presensi_show</div>
            <div>$data_jenis[latihan]</div>
            <div>$data_jenis[challenge]</div>
          <td>
            <div><span class='abu miring f12'>UTS Manual:</span> $uts_manual</div>
            <div><span class='abu miring f12'>UAS Manual:</span> $uas_manual</div>
            <div><span class='abu miring f12'>UTS PG:</span> $uts_pg</div>
            <div><span class='abu miring f12'>UAS PG:</span> $uas_pg</div>
          </td>
          <td>
            <div>$btn_delete</div>
          </td>
        </tr>
      ";
    } else {
      die("Belum ada handler untuk list-peserta pada mode: $get_mode");
    }
  }

  $link_assign = "
    <a href='?assign_peserta_kelas&kelas=$d[kelas]'>
      $img_add 
      <span class='green f14'>
        Assign Peserta Kelas
      </span>
    </a>
  ";

  if ($get_mode == 'fast') {
    $blok_kelas .= "
      <div class='wadah gradasi-hijau' zzzdata-aos='fade-up' data-aos-delay='150'>
        Peserta Kelas $d[kelas]
        <div class='wadah bg-white flexy mt1'>
          $list_peserta
        </div>
        $link_assign
      </div>
    ";
  } elseif ($get_mode == 'detail') {
    $blok_kelas .= "
      <div class='wadah' zzzdata-aos='fade-up' data-aos-delay='150'>
        <div class=sub_form>Detail Mode</div>
        Peserta Kelas $d[kelas]
        <table class='table mt1'>
          <thead>
            <th>No</th>
            <th>Profil Peserta</th>
            <th>Detail Info</th>
            <th>Aktifitas</th>
            <th>Ujian</th>
            <th>Aksi</th>
          </thead>
          $list_peserta
        </table>      
        $link_assign
      </div>
    ";
  } else {
    die("Belum ada handler untuk mode: $get_mode");
  }
}

if ($id_role == 2) {
  $mode_fast_show = $get_mode == 'fast' ? 'Mode Fast' : '<a href="?peserta_kelas">Mode Fast</a>';
  $mode_detail_show = $get_mode == 'detail' ? 'Mode Detail' : '<a href="?peserta_kelas&mode=detail">Mode Detail</a>';
  $pilih_mode = "<div>$mode_fast_show | $mode_detail_show</div>";
} else {
  $pilih_mode = '';
}

echo "
<div class='section-title' zzzdata-aos='fade-up'>
  <h2>$judul</h2>
  <p>Peserta Kelas MK $room :: $jumlah_peserta peserta $pilih_mode</p>
  
</div>
$blok_kelas 
";
