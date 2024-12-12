<?php
session_start();
include '../includes/insho_functions.php';
include '../conn.php';
include '../../insho_styles.php';
set_h2('Destroy Room');
$target_id_room = $_GET['id_room'] ?? die('Target Room unspecified.');

die('Page ini tidak bisa diakses secara langsung. Call Developer!');

$arr = [
  'challenge' => [
    'assign_challenge' => [
      'bukti_challenge'
    ],
    'sublevel_challenge'
  ],
  'latihan' => [
    'assign_latihan' => [
      'bukti_latihan'
    ]
  ],
  'paket_war',
  'penilaian_weekly',
  'poin',
  'polling_answer',
  'presensi_summary',
  'room_kelas' => [
    'bertanya'
  ],
  'sesi' => [
    'paket' => [
      'assign_soal',
      'paket_kelas' => [
        'jawabans'
      ],
    ],
    'presensi',
    'sesi_kelas',
    'soal_peserta' => [
      'war'
    ],
  ],
  'war_summary',
  'soal',
];

foreach ($arr as $k => $v) {
  if (is_array($v)) {

    $s = "SELECT id FROM tb_$k WHERE id_room=$target_id_room";
    echolog("@ $s");
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    while ($d = mysqli_fetch_assoc($q)) {

      foreach ($v as $k2 => $v2) {
        if (is_array($v2)) {
          $s2 = "SELECT id FROM tb_$k2 WHERE id_$k=$d[id]";
          echolog("@@ $s2");
          $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
          while ($d2 = mysqli_fetch_assoc($q2)) {
            foreach ($v2 as $k3 => $v3) {
              if (is_array($v3)) {





                // $id = $k3=='paket_kelas' ?

                $s3 = "SELECT id FROM tb_$k3 WHERE id_$k2=$d2[id]";
                if ($k3 == 'paket_kelas') {
                  $s3 = "SELECT paket_kelas FROM tb_$k3 WHERE id_$k2=$d2[id]";
                }
                echolog("@@ @@ $s3");
                $q3 = mysqli_query($cn, $s3) or die(mysqli_error($cn));
                while ($d3 = mysqli_fetch_assoc($q3)) {
                  foreach ($v3 as $k4 => $v4) {
                    if ($k3 == 'paket_kelas') {
                      $s4 = "DELETE FROM tb_$v4 WHERE $k3 = '$d3[paket_kelas]'";
                    } else {
                      $s4 = "DELETE FROM tb_$v4 WHERE id_$k3 = $d3[id]";
                    }
                    echolog(">> >> >> $s4");
                    $q4 = mysqli_query($cn, $s4) or die(mysqli_error($cn));
                  }
                }

                $s3 = "DELETE FROM tb_$k3 WHERE id_$k2 = $d2[id]";
                echolog("-- -- $s3");
                $q3 = mysqli_query($cn, $s3) or die(mysqli_error($cn));
              } else {
                $s3 = "DELETE FROM tb_$v3 WHERE id_$k2 = $d2[id]";
                echolog(">> >> $s3");
                $q3 = mysqli_query($cn, $s3) or die(mysqli_error($cn));
              }
            }
          }

          $s2 = "DELETE FROM tb_$k2 WHERE id_$k = $d[id]";
          echolog("-- $s2");
          $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
        } else {
          $s2 = "DELETE FROM tb_$v2 WHERE id_$k = $d[id]";
          echolog(">> $s2");
          $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
        }
      }
    }



    $s = "DELETE FROM tb_$k WHERE id_room=$target_id_room";
    echolog("- $s");
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  } else {

    $s = "DELETE FROM tb_$v WHERE id_room=$target_id_room";
    echolog("- $s");
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  }
}

$s = "DELETE FROM tb_room WHERE id=$target_id_room";
echolog($s);
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
