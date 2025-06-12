<style>
  .border_blue {
    border: solid 3px blue !important;
    background: linear-gradient(#efe, #cfc);
  }
</style>
<?php
if (!$id_room) die(erid('id_room'));
instruktur_only();

$get_ta = $_GET['ta'] ?? $ta_aktif;
$mode = $_GET['mode'] ?? '';

$s = "SELECT ta FROM tb_ta";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$nav_ta = '';
while ($d = mysqli_fetch_assoc($q)) {
  $current = $d['ta'] == $get_ta ? 'blue bold' : '';
  $slash = $nav_ta ? ' | ' : '';
  $nav_ta .= "$slash<a href='?manage_kelas&ta=$d[ta]'><span class='$current'>$d[ta]</span></a>";
}

set_h2("Manage Kelas", "$nav_ta");
// $room['status'] = 5;
// $status_room = 5;
// include "$lokasi_pages/aktivasi_room.php";

include 'manage_kelas-processors.php';




















# ============================================================
# ROOM KELAS
# ============================================================
$s = "SELECT a.id as id_room_kelas,a.kelas,b.fakultas 
FROM tb_room_kelas a JOIN tb_kelas b ON a.kelas=b.kelas 
WHERE a.id_room=$id_room 
AND a.kelas != 'INSTRUKTUR'
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$arr_assigned_kelas = [];
while ($d = mysqli_fetch_assoc($q)) {
  array_push($arr_assigned_kelas, $d['kelas']);
}


# ============================================================
# MAIN SELECT
# ============================================================
$s = "SELECT * ,
(SELECT COUNT(1) FROM tb_kelas_peserta WHERE kelas=a.kelas) count
FROM tb_kelas a
WHERE a.ta=$get_ta 
AND a.kelas != 'INSTRUKTUR'
ORDER BY status DESC,fakultas,semester,prodi,shift";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$tr = '';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $color = $d['shift'] == 'P' ? 'blue' : 'darkred';

  if ($d['count']) {
    $btn_delete = "<span class='btn btn-secondary btn-sm' onclick='alert(`Tidak dapat hapus kelas karena sudah ada peserta.`)'>Delete</span>";
  } else {
    $btn_delete = "<button class='btn btn-danger btn-sm' name=btn_delete_kelas value='$d[kelas]' onclick='return confirm(`Delete kelas ini?`)'>Delete</button>";
  }

  if ($d['status']) {
    if ($d['ta'] == $ta_aktif) {
      $btn = "<button class='btn btn-primary btn-sm' name=btn_assign_room_kelas value='$d[kelas]' onclick='return confirm(`Confirm Assign?\n\nMasukan semua peserta pada kelas ke $Room ini?`)'>Assign</button>"; // aborted
      $btn = "<a href='?assign_kelas&untuk_kelas=$d[kelas]' class='btn btn-primary btn-sm'>Assign</a>";
    } else {
      $btn = "<span class='btn btn-secondary btn-sm' onclick='alert(`Tidak dapat Drop karena TA sudah tidak aktif.`)'>Drop</span>";
    }
  } else {
    $btn = "<span class='btn btn-secondary btn-sm' onclick='alert(`Tidak dapat Drop karena kelas sudah tidak aktif.`)'>Drop</span>";
    $color = 'abu miring';
  }

  if (in_array($d['kelas'], $arr_assigned_kelas)) {
    $btn_delete = '';
    $border_blue = 'border_blue';
    $btn = "<button class='btn btn-danger btn-sm' name=btn_drop_room_kelas value='$d[kelas]' onclick='return confirm(`Confirm Drop?\n\nDrop semua peserta kelas dari $Room ini?`)'>Drop</button>";
  } else {
    $border_blue = '';
  }


  $tr .= "
    <tr class='$color $border_blue'>
      <td>$i</td>
      <td>$d[fakultas]</td>
      <td>$d[jenjang]-$d[prodi]</td>
      <td>SM$d[semester]</td>
      <td>$d[shift]</td>
      <td>$d[sub_kelas]</td>
      <td>$d[caption]</td>
      <td><a href='?peserta_kelas'>$d[count]</a></td>
      <td>$btn $btn_delete</td>
    </tr>
  ";
}

if (!$mode) {
  $hide_form_view = '';
  $hide_form_add = 'hideit';
} elseif ($mode == 'add') {
  $hide_form_view = 'hideit';
  $hide_form_add = '';
} else {
  die("Belum ada handler untuk mode [$mode]");
}

$btn_add = $get_ta == $ta_aktif ? "<span class='btn btn-sm btn-success btn_toggle'>Add Kelas</span>" : "<span class='btn btn-sm btn-secondary' onclick='alert(`Add Kelas hanya dapat di TA Aktif ($ta_aktif)\n\nSilahkan pindahkan navigasi ke TA Aktif terlebih dahulu.`)'>Add Kelas</span>";

$opt_smt = '';
for ($i = 1; $i <= 8; $i++) {
  if ($ta_aktif % 2 == 0) {
    if ($i % 2 == 0) $opt_smt .= "<option value=$i>Semester $i</option>";
  } else {
    if ($i % 2 != 0) $opt_smt .= "<option value=$i>Semester $i</option>";
  }
}

echo "
  <form method=post id=form_view class='$hide_form_view'>
    <h3 class='mb2 tengah'>Kelas pada TA $get_ta: </h3>
    <div class='mt2 mb2 kiri'>$btn_add</div>
    <table class='table table-striped'>
      <thead class='gradasi-toska'>
        <th>No</th>
        <th>Fakultas</th>
        <th>Prodi</th>
        <th>Semester</th>
        <th>Shift</th>
        <th>Sub</th>
        <th>Label</th>
        <th>Peserta</th>
        <th>Aksi</th>
      </thead>
      $tr
    </table>
    <div class='mt4 mb4 kiri'>$btn_add</div>
  </form>


  <form method=post class='$hide_form_add wadah mt4 gradasi-kuning' id=form_add>
    <h3 class='mb2 tengah'>Add Kelas</h3>

    <div class=mb2>
      <select class='form-control' name=fakultas>
        <option value='FKOM'>Fakultas: FKOM</option>>
        <option value='FTEK'>Fakultas: FTEK</option>>
        <option value='FEBI'>Fakultas: FEBI</option>>
        <option value='FKIP'>Fakultas: FKIP</option>>
        <option value='FAPERTA'>Fakultas: FAPERTA</option>>
        <option value='SD'>Fakultas: SD</option>>
        <option value='SMP'>Fakultas: SMP</option>>
        <option value='SMA'>Fakultas: SMA</option>>
      </select>
    </div>

    <div class=mb2>
      <select class='form-control' name=jenjang>
        <option value='S1'>Jenjang: S1 (Sarjana)</option>>
        <option value='D3'>Jenjang: D3 (Diploma)</option>>
        <option value='SD'>Jenjang: SD (Sekolah Dasar)</option>>
        <option value='SP'>Jenjang: SP (SLTP)</option>>
        <option value='SA'>Jenjang: SA (SMA/MA)</option>>
      </select>
    </div>

    <div class=mb2>
      <input required minlength=2 maxlength=6 class='form-control' name=prodi placeholder='Singkatan Prodi...'>
      <div class='abu miring mt1 ml1 f12'>misal: SI, KA, BD</div>
    </div>

    <div class=mb2>
      <input required minlength=10 maxlength=50 class='form-control' name=nama_prodi placeholder='Nama Prodi Lengkap...'>
      <div class='abu miring mt1 ml1 f12'>misal: SISTEM INFORMASI, KOMPUTERISASI AKUNTANSI</div>
    </div>

    <div class=mb2>
      <select class='form-control' name=shift>
        <option value='R'>Kelas Reguler</option>>
        <option value='NR'>Kelas NR | Sore | Karyawan</option>>
      </select>
    </div>

    <div class=mb2>
      <input disabled class='form-control' value='TA Aktif: $ta_aktif'>
      <div class='abu miring mt1 ml1 f12'>hanya dapat membuat kelas baru di TA aktif saat ini.</div>
    </div>

    <div class=mb2>
      <select class='form-control' name=semester>
        $opt_smt
      </select>
    </div>

    <div class=mb2>
      <select class='form-control' name=sub_kelas>
        <option value=''>(hanya satu rombel)</option>>
        <option value='A'>Kelas A</option>>
        <option value='B'>Kelas B</option>>
        <option value='C'>Kelas C</option>>
        <option value='D'>Kelas D</option>>
        <option value='E'>Kelas E</option>>
      </select>
    </div>

    <div class=mb2>
      <input minlength=3 maxlength=50 class='form-control' name=caption placeholder='Label Kelas (opsional)...'>
      <div class='abu miring mt1 ml1 f12'>misal: SI/4-A, SI/4-B, KA-REG</div>
    </div>

    <div class=mb2>
      <button class='btn btn-primary w-100' name=btn_add_kelas>Add Kelas Baru</button>
    </div>


    <div class='mt4 mb4 tengah'><span class='btn btn-sm btn-warning btn_toggle'>Cancel</span></div>
  </form>
";
?>
<script>
  $(function() {
    $('.btn_toggle').click(function() {
      $('#form_view').slideToggle();
      $('#form_add').slideToggle();
    })
  })
</script>