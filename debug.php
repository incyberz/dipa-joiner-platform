<?php
# ============================================================
# USER VARS DEBUGGING
# ============================================================
echo "
  <div class='debug'>
  <hr>
    <div class='tebal biru'>Debugging User Vars</div>
    <br>id_peserta: <span id=id_peserta>$id_peserta</span>
    <br>nama_peserta: <span id=nama_peserta>$nama_peserta</span>
    <br>kelas: <span id=kelas>$kelas</span>
    <br>tahun_ajar: <span id=tahun_ajar>$tahun_ajar</span>
    <br>sebagai: <span id=sebagai>$sebagai</span>
    <br>id_role: <span id=id_role>$id_role</span>
  </div>
";


# ============================================================
# ROOM VARS DEBUGGING
# ============================================================
echo "
  <div class='debug'>
  <hr>
    <div class='tebal biru'>Debugging Room Vars</div>
    <br>id_room: <span id=id_room>$id_room</span>
    <br>id_room_kelas: <span id=id_room_kelas>$id_room_kelas</span>
    <br>room: <span id=room>$room</span>
    <br>nama_room: <span id=nama_room>$nama_room</span>
    <br>total_peserta_kelas [$kelas]: <span id=total_peserta_kelas>$total_peserta_kelas</span>
    <br>total_peserta [$room]: <span id=total_peserta>$total_peserta</span>
  </div>
";
