<?php
# ============================================================
# CUSTOM AKSI
# ============================================================
$aksi = $_GET['aksi'] ?? '';
if ($aksi == 'reset_room_kelas') {
  echolog('dropping classes');
  $s = "DELETE FROM tb_room_kelas WHERE id_room=$id_room AND kelas != 'INSTRUKTUR'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echolog("updating $Room status");
  $s = "UPDATE tb_room set status=5 WHERE id=$id_room";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', 'Reset kelas sukses');
  jsurl('?aktivasi_room', 3000);
}

# ============================================================
# ADD GRUP KELAS
# ============================================================
include 'add_kelas-processor.php';

# ============================================================
# RESET AWAL SESI
# ============================================================
if (isset($_POST['reset_awal_sesi'])) {
  $s = "UPDATE tb_room SET awal_sesi=NULL, status=2 WHERE id=$id_room";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', 'Reset Awal Sesi sukses.');
  jsurl('', 2000);
  exit;
}

# ============================================================
# BATALKAN AKTIVASI
# ============================================================
if (isset($_POST['btn_batalkan_aktivasi'])) {
  unset($_SESSION['dipa_id_room']);
  jsurl();
}

# ============================================================
# BTN NEXT AKTIVASI
# ============================================================
if (isset($_POST['btn_aktivasi'])) {
  echolog("Validation $Room data");






  // exception awal_sesi harus hari senin
  if (isset($_POST['awal_sesi'])) {
    $awal_sesi = $_POST['awal_sesi'];
    if ($awal_sesi) {
      $w = date('w', strtotime($awal_sesi));
      if ($w != 1 && $room['jeda_sesi'] == 7) {
        echo div_alert('danger', "Awal sesi minggu pertama harus hari Senin, Anda memilih hari $nama_hari[$w].");
        jsurl('', 2000);
        exit;
      }
    }
  }

  $new_status = $_POST['btn_aktivasi'];
  unset($_POST['btn_aktivasi']);

  if ($_POST) {




    # ============================================================
    # EXCEPTION FOR ARRAY AWAL PRESENSI
    # ============================================================
    if (isset($_POST['awal_presensi'])) {
      $arr = $_POST['awal_presensi'];
      $durasi = $room['durasi_tatap_muka'] ?? 90;
      $no_sesi_normal = 0;

      foreach ($arr as $no => $str) {
        $arr2 = explode('--', $str);
        $awal_presensi = $arr2[0];
        $akhir_presensi = date('Y-m-d', strtotime('+6 day', strtotime($awal_presensi)));
        $jenis = $arr2[1];
        if ($jenis == 1) {
          $no_sesi_normal++;
          $nama = "Pertemuan ke-$no_sesi_normal";
        } elseif ($jenis == 0) {
          $nama = "Sesi tenang";
        } elseif ($jenis == 2) {
          $nama = "UTS";
        } elseif ($jenis == 3) {
          $nama = "UAS";
        } else {
          die("Jenis sesi: ($jenis) tidak valid");
        }

        $s = "SELECT id FROM tb_sesi WHERE id_room=$id_room AND no=$no";
        // echolog($s);
        $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
        if (mysqli_num_rows($q)) {
          echolog('updating sesi');
          $d = mysqli_fetch_assoc($q);
          $s = "UPDATE tb_sesi SET
            awal_presensi = '$awal_presensi',
            akhir_presensi = '$akhir_presensi',
            durasi = $durasi
          WHERE id=$d[id]
          ";
        } else {
          echolog('inserting sesi');
          $s = "INSERT INTO tb_sesi (
            id_room,
            jenis,
            no,
            nama,
            awal_presensi,
            akhir_presensi,
            durasi
          ) VALUES (
            $id_room,
            $jenis,
            $no,
            '$nama',
            '$awal_presensi',
            '$akhir_presensi',
            $durasi
          )";
        }
        $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      }
      unset($_POST['awal_presensi']);
    }

    # ============================================================
    # EXCEPTION FOR ARRAY NAMA PRESENSI
    # ============================================================
    if (isset($_POST['nama_sesi'])) {
      $arr = $_POST['nama_sesi'];
      foreach ($arr as $id_sesi => $nama) {
        $s = "UPDATE tb_sesi SET nama='$nama' WHERE id=$id_sesi";
        $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
        echolog("inserting id: $id_sesi");
      }

      unset($_POST['nama_sesi']);
    }


    # ============================================================
    # EXCEPTION FOR ARRAY ROOM KELAS
    # ============================================================
    if (isset($_POST['room_kelas'])) {
      $arr = $_POST['room_kelas'];
      foreach ($arr as $key => $kelas) {
        $s = "SELECT 1 FROM tb_room_kelas WHERE id_room=$id_room AND kelas='$kelas'";
        echolog('checking duplikat kelas');
        $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
        if (mysqli_num_rows($q)) {
          echolog('skip updating, not necessary');
        } else {
          echolog('assigning kelas');
          $s = "INSERT INTO tb_room_kelas (
            ta,
            id_room,
            kelas
          ) VALUES (
            $ta,
            $id_room,
            '$kelas'
          )";
          $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
        }
      }

      // drop not checked kelas
      $s = $select_all_from_tb_room_kelas;
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      while ($d = mysqli_fetch_assoc($q)) {
        // $id=$d['id'];
        if (!in_array($d['kelas'], $arr)) {
          echolog("drop kelas: $d[kelas]");
          $s2 = "DELETE FROM tb_room_kelas WHERE id_room=$id_room AND kelas='$d[kelas]'";
          $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
        }
      }

      unset($_POST['room_kelas']);
    }

    # ============================================================
    # EXCEPTION FOR JADWAL KELAS
    # ============================================================
    if (isset($_POST['jadwal_kelas'])) {
      $arr = $_POST['jadwal_kelas'];

      // unset radio__kelas
      foreach ($arr as $kelas => $value) {
        unset($_POST['radio__' . $kelas]);
      }

      // unset($_POST['sesi_kelas_count']);






      $s = "SELECT id as id_sesi FROM tb_sesi WHERE id_room=$id_room";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      if (!mysqli_num_rows($q)) {
        die(div_alert('danger', "Belum ada data sesi pada $Room ini."));
      } else {
        while ($d = mysqli_fetch_assoc($q)) {
          foreach ($arr as $kelas => $jadwal_kelas) {
            $s2 = "SELECT 1 FROM tb_sesi_kelas WHERE id_sesi=$d[id_sesi] AND kelas='$kelas'";
            echolog('checking duplikat jadwal kelas');
            $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
            if (mysqli_num_rows($q2)) {
              echolog('skip updating ----------------------------------- ');
            } else {
              echolog('assigning jadwal kelas');
              $s3 = "INSERT INTO tb_sesi_kelas (
                id_sesi,
                kelas,
                jadwal_kelas
              ) VALUES (
                $d[id_sesi],
                '$kelas',
                '$jadwal_kelas'
              )";
              $q3 = mysqli_query($cn, $s3) or die(mysqli_error($cn));
            }
          } // end foreach jadwal_kelas
        } // end while sesi in $room
      } // end if ada data sesi

      unset($_POST['jadwal_kelas']);
    }

    # ============================================================
    # EXCEPTION FOR SYARAT PRESENSI
    # ============================================================
    if (isset($_POST['syarat_presensi'])) {
      $arr = $_POST['syarat_presensi'];

      $koloms = '__';
      $isis = '__';
      $pairs = '__';
      foreach ($arr as $kolom => $isi) {
        $koloms .= ",$kolom";
        $isis .= ",'$isi'";
        $pairs .= ",$kolom = '$isi' ";
      }
      $koloms = str_replace('__,', '', $koloms);
      $isis = strtoupper(str_replace('__,', '', $isis));
      $pairs = strtoupper(str_replace('__,', '', $pairs));

      $s = "INSERT INTO tb_syarat_presensi ($koloms,id_room) 
      VALUES ($isis,$id_room) 
      ON DUPLICATE KEY UPDATE 
      $pairs
      ";
      echolog("Executing: $s");
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

      unset($_POST['syarat_presensi']);
    }

    # ============================================================
    # EXCEPTION FOR SYARAT PRESENSI
    # ============================================================
    if (isset($_POST['bobot'])) {
      $arr = $_POST['bobot'];

      $koloms = '__';
      $isis = '__';
      $pairs = '__';
      foreach ($arr as $kolom => $isi) {
        $isi = strtoupper($isi);
        $koloms .= ",$kolom";
        $isis .= ",'$isi'";
        $pairs .= ",$kolom = '$isi'";
      }
      $koloms = str_replace('__,', '', $koloms);
      $isis = str_replace('__,', '', $isis);
      $pairs = str_replace('__,', '', $pairs);


      $s = "INSERT INTO tb_bobot ($koloms,id_room) VALUES ($isis,$id_room)
      ON DUPLICATE KEY UPDATE $pairs 
      ";
      echolog("Executing: $s");
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

      unset($_POST['bobot']);
    }



































    # ============================================================
    # NORMAL PAIRS | NON-ARRAY
    # ============================================================
    $pairs = '__';
    foreach ($_POST as $key => $value) {
      echo '<pre>';
      var_dump($_POST);
      echo '</pre>';
      if ($value === false || $value === '') {
        echo div_alert('danger', "Input aktivasi [$key] tidak boleh dikosongkan.");
        // jsurl('', 3000);
        exit;
      } else {
        if (is_array($value)) {
          echolog("key: $key");
          die(div_alert('danger', "Tidak bisa menggunakan array dalam input aktivasi ke data $Room."));
        }
        $value = clean_sql($value);
        $pairs .= ",$key='$value'";
      }
    }
    $pairs .= ",status=$new_status";
    $pairs = str_replace('__,', '', $pairs);

    $s = "UPDATE tb_room SET $pairs WHERE id=$id_room";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo div_alert('success', 'Update sukses.');
    jsurl('', 1000);
  } else {
    echo div_alert('danger', 'Tidak ada data yang bisa diproses.');
    jsurl('', 2000);
  }



  exit;
}
