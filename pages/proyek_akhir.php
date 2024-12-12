<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<?php
$lokasi_proyek = "uploads/__proyek";
$target_kelas = $id_role == 1 ? $kelas : $target_kelas;

# ============================================================
# PROCESSORS 
# ============================================================
if (isset($_POST['btn_upload'])) {
  $id_sub_proyek = $_POST['btn_upload'];

  # ============================================================
  # DELETE OLD FILE
  # ============================================================
  $s = "SELECT bukti FROM tb_bukti_proyek WHERE id_peserta=$id_peserta AND id_sub_proyek=$id_sub_proyek";
  // ZZZ HERE

  foreach ($_FILES as $fitur => $arr) {
    $tmp_name = $arr['tmp_name'];
    $date = date('YmdHis');
    $unique = "$id_peserta-$id_room-$fitur";
    $new_name = "$username-$fitur-$date.jpg";
    if (move_uploaded_file($tmp_name, "$lokasi_proyek/$new_name")) {
      $s = "INSERT INTO tb_bukti_proyek (
        kode,
        id_peserta,
        id_sub_proyek,
        bukti
      ) VALUES (
        '$unique',
        $id_peserta,
        $id_sub_proyek,
        '$new_name'
      ) ON DUPLICATE KEY UPDATE 
        bukti='$new_name',
        tanggal_submit=NOW(),
        verif_by = NULL,
        verif_at = NULL,
        poin = NULL
      ";
      // die($s);
      mysqli_query($cn, $s) or die(mysqli_error($cn));
      echo 'berhasil upload';
      jsurl();
    } else {
      echo "gagal upload | tmp_name: $tmp_name | lokasi_proyek/new_name: $lokasi_proyek/$new_name";
    }
  }
}



# ============================================================
# MAIN SELECT
# ============================================================
set_h2('Proyek Akhir', $target_kelas);
$img_next = img_icon('next');
$img_reject = img_icon('reject');


# ============================================================
# SUB PROYEK
# ============================================================
$s = "SELECT * FROM tb_sub_proyek WHERE id_room=$id_room";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$arr_fitur = [];
while ($d = mysqli_fetch_assoc($q)) {
  $arr_fitur[$d['id']] = [
    'fitur' => $d['fitur'],
    'label' => $d['label'],
  ];
}





# ============================================================
# PESERTA PADA ROOM INI
# ============================================================
$sql_target_kelas = $target_kelas ? "b.kelas = '$target_kelas'" : 1;
$s = "SELECT 
b.kelas,  
d.username,  
d.id as id_peserta,
d.nama as nama_peserta,
(
  SELECT nama FROM tb_proyek 
  WHERE id_peserta=d.id 
  AND id_room=$id_room) judul_proyek,
(
  SELECT COUNT(1) FROM tb_bukti_proyek p 
  JOIN tb_sub_proyek p2 ON p.id_sub_proyek=p2.id
  WHERE p.id_peserta=d.id 
  AND p2.id_room=$id_room) count_bukti

FROM tb_room_kelas a 
JOIN tb_kelas b ON a.kelas=b.kelas 
JOIN tb_kelas_peserta c ON b.kelas=c.kelas 
JOIN tb_peserta d ON c.id_peserta=d.id

WHERE a.id_room = $id_room 
AND a.ta = $ta 
AND d.status = 1 -- peserta aktif
-- AND d.id_role = 1 -- peserta 
AND $sql_target_kelas
ORDER BY b.kelas, d.nama  
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$tr = '';
$i = 0;
$input = "<input class='form-control edit_nama_proyek'>";
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $nama = strtoupper($d['nama_peserta']);
  $login_as = $id_role == 2 ? "LOGIN AS $d[username] ZZZ" : '';
  $status = '<div class="f12 miring abu">belum diverifikasi</div>';
  $judul_proyek = $d['judul_proyek'];
  $input = "<input class='form-control edit_nama_proyek' id=edit_nama_proyek__$d[id_peserta] value='$d[judul_proyek]'>";



  # ============================================================
  # ARRAY BUKTI PROYEK
  # ============================================================
  $arr_bukti = [];
  if ($d['count_bukti']) {
    $s = "SELECT * FROM tb_bukti_proyek WHERE id_peserta=$d[id_peserta]";
    $q_bukti = mysqli_query($cn, $s) or die(mysqli_error($cn));
    while ($d_bukti = mysqli_fetch_assoc($q_bukti)) {
      $arr_bukti[$d_bukti['kode']] = $d_bukti;
    }
  }



  # ============================================================
  # INPUT PROGRES
  # ============================================================
  $input_progres = '';
  foreach ($arr_fitur as $id_sub_proyek => $arr) {

    $gambar_bukti = '';
    $unique = "$id_peserta-$id_room-$arr[fitur]";


    if (isset($arr_bukti[$unique])) {
      $bukti = $arr_bukti[$unique]['bukti'];
      $gambar_bukti = "<img src='$lokasi_proyek/$bukti' class='w100'>";
    }
    $input_progres .= "
      <div class='wadah mt2'>
        <div class='f14 abu mb1 mt1'>$arr[label]:</div>
        $gambar_bukti
        <form method=post class='flexy' enctype='multipart/form-data'>
          <div>
            <input required type=file class='form-control' name=$arr[fitur] accept='.jpg,.jpeg'>
          </div>
          <div>
            <button class='btn btn-primary btn-sm' name=btn_upload value=$id_sub_proyek>Upload</button>
          </div>
        </form>
      </div>
    ";
  }


  # ============================================================
  # PROYEK SAYA
  # ============================================================
  if ($id_peserta == $d['id_peserta']) {
    $judul_proyek = $judul_proyek ? "$input$input_progres" : $input;
  }

  # ============================================================
  # LOOP STATUS HANDLER
  # ============================================================
  if ($id_role == 2) {
    $status = "
      $img_check 
      $img_reject 
      $img_next 
    ";
  }



  $tr .= "
    <tr>
      <td>$i</td>
      <td>$d[kelas]</td>
      <td>$nama</td>
      <td>
        $judul_proyek
      </td>
      <td>
        $status
      </td>
    </tr>
  ";
}

echo "
  <table id=myTableZZZ class='table table-striped table-hover'>
    <thead>
      <th>No</th>
      <th>Kelas</th>
      <th>Nama</th>
      <th>Judul Proyek</th>
      <th>Status Proyek</th>
    </thead>
    $tr
  </table>
";



?>
<script>
  let table = new DataTable('#myTable');
  let cnama_proyek = '';

  $(function() {
    $(".edit_nama_proyek").focusout(function() {
      let nama_proyek = $(this).val();
      if (!nama_proyek) return;
      if (cnama_proyek == nama_proyek) return;

      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id_peserta = rid[1];
      console.log(aksi, id_peserta);

      if (nama_proyek.length >= 3) {
        let link_ajax = `pages/proyek_akhir-ajax.php?nama_proyek=${nama_proyek}&id_peserta=${id_peserta}`

        $.ajax({
          url: link_ajax,
          success: function(a) {
            cnama_proyek = nama_proyek;
            alert(a)
          }
        })

      } else {
        alert('Nama Proyek minimal 3 karakter.');

      }

    })
  })
</script>