<?php
if (isset($_POST['btn_upload_cropped_image'])) {

  include 'includes/resize_img.php';
  echo '<pre>';
  var_dump($_FILES);
  echo '</pre>';

  $id_target_peserta = $_POST['btn_upload_cropped_image'];

  // select war image
  $s = "SELECT war_image,nama FROM tb_peserta WHERE id=$_POST[btn_upload_cropped_image]";
  echo '<pre>';
  var_dump($s);
  echo '</pre>';
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $d = mysqli_fetch_assoc($q);
  $path_unverified_war_image = "$lokasi_profil/$d[war_image]";


  // new war_image
  $nama = $d['nama'];
  $date = date('ymdHis');
  $nama2 = strtolower(str_replace(' ', '_', trim($nama)));
  $new_war_image = "$id_target_peserta-war-$nama2-$date.jpg";


  // replace with new war_image
  if (move_uploaded_file($_FILES['war_profil']['tmp_name'], "$lokasi_profil/$new_war_image")) {
    echolog('move_uploaded_file sukses');

    // resize new war_image
    resize_img($new_war_image, '', 150, 150);

    // delete old war image
    echolog('delete old-unverified war image');
    unlink($path_unverified_war_image);

    echolog('update db with new war_image');
    $s = "UPDATE tb_peserta SET war_image='$new_war_image' WHERE id=$id_target_peserta";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo div_alert('success', 'Verified Success');
  }

  jsurl();
} elseif (isset($_POST['btn_approve_war_image'])) {
  $tmp = explode('__', $_POST['btn_approve_war_image']);
  $id = $tmp[0];
  $is_approve = $tmp[1];
  $target = "$lokasi_profil/$_POST[war_image]";

  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';

  if ($is_approve === '0') {
    # ============================================================
    # DELETE UNVERIFIED WAR IMAGE
    # ============================================================
    if (unlink($target)) {
      # ============================================================
      # SET WAR IMAGE WITH DEFAULT REJECTED
      # ============================================================
      $s = "UPDATE tb_peserta SET war_image='war_image_rejected.jpg' WHERE id=$id";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      echo div_alert('success', 'Reject Success');
      // jsurl();
    }
  } elseif ($is_approve === '1') {
    # ============================================================
    # RESIZE UNVERIFIED WAR IMAGE TO 150PX
    # ============================================================
    include 'includes/resize_img.php';
    resize_img($target, '', 150, 150);

    # ============================================================
    # STR_REPLACE UNVERIFIED WAR IMAGE
    # ============================================================
    $verified_war_image = str_replace('war_unverified', 'war', $_POST['war_image']);

    # ============================================================
    # RENAME FILE
    # ============================================================
    rename("$lokasi_profil/$_POST[war_image]", "$lokasi_profil/$verified_war_image");

    # ============================================================
    # UPDATE WAR IMAGE
    # ============================================================
    $s = "UPDATE tb_peserta SET war_image='$verified_war_image' WHERE id=$id";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo div_alert('success', 'Verified Success');
    jsurl();
  } else {
    die('Invalid kode btn_approve_war_image');
  }
  // rename("$lokasi_profil/war-$_POST[id_peserta].jpg", "$lokasi_profil/war-$_POST[id_peserta]-reject.jpg");
}


# ============================================================
# MAIN SELECT
# ============================================================
$s = "SELECT 
a.id,
a.image,
a.war_image,
a.nama 
FROM tb_peserta a 
JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
JOIN tb_kelas c ON b.kelas=c.kelas 
JOIN tb_room_kelas d ON c.kelas=d.kelas 
WHERE a.status=1 
AND a.war_image LIKE '%war_unverified%' 
AND d.id_room='$id_room'
";

# ============================================================
# EXCEPTION FOR ABI
# ============================================================
// if ($username == 'abi') {
//   $s = "SELECT id,war_image,nama FROM tb_peserta WHERE status=1 AND war_image LIKE '%war_unverified%'";
// }
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$divs = '';
while ($d = mysqli_fetch_assoc($q)) {
  $id = $d['id'];
  $src = "$lokasi_profil/$d[war_image]";
  if (file_exists($src)) {
    $divs .= "
    <div class=tengah>
      <div class=wadah>
        <div class=row>
          <div class='col-md-6'>
            <form method=post class=wadah>
              <img src='$src' class='img-fluid' />
              <input readonly name=war_image class='form-control form-control-sm mt1 mb2 f12 tengah' value='$d[war_image]' />
              <button class='btn btn-success w-100 mb2' name=btn_approve_war_image value=$d[id]__1>Approve War Image</button>
              <button class='btn btn-danger w-100 mb2' name=btn_approve_war_image value=$d[id]__0>Reject</button>
            </form>
          </div>
          <div class='col-md-6'>
            <form method=post enctype='multipart/form-data' class=wadah>
              <div class='border-bottom f12 mb2 pb1'>Crop dan Re-upload: <a target=_blank class=help href='?help&q=crop-dan-reupload-war-image'>See how</a></div>
              <input type=hidden name=id_peserta value=$id />
              <input type=file name=war_profil accept='.jpg' />
              <button class='btn btn-secondary btn-sm' name=btn_upload_cropped_image value=$id>Upload</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    ";
  } else { // physical image not exist
    # ============================================================
    # SET WAR IMAGE TO NULL
    # ============================================================
    $s = "UPDATE tb_peserta SET war_image=NULL WHERE id=$d[id]";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echolog('nulling war image because not exist.');
    exit;
  }
}

echo "<div class='flexysaasd flexas-center'>$divs</div>";
