<?php
# ============================================================
# ROUTING DAFTAR | LIST ROOM
# ============================================================
$aktivasi_room = $_GET['aktivasi_room'] ?? '';
$daftar_ke_room = $_GET['daftar_ke_room'] ?? '';

if ($aktivasi_room) {
  // set session id_room
  $_SESSION['dipa_id_room'] = $aktivasi_room;
  jsurl('?aktivasi_room');
} elseif ($daftar_ke_room) {
  include 'daftar_anggota.php';
} else {
  // $info_kelas = $id_role == 1 ? "Kamu berada di kelas <u>$kelas</u>." : '';
  // set_h2("Pilih $Room", "Welcome <u>$nama_peserta</u>! $info_kelas Silahkan Pilih $Room!");
  set_h2("Pilih $Room", "Welcome <b>$nama_peserta</b>!");
  include 'includes/arr_status_room.php';
  include 'pilih_room_main.php';
}
