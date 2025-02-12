<?php
set_title('Manage Image');
include '../includes/resize_img.php';
$set_as_online_image = $_GET['set_as_online_image'] ?? '';

if (isset($_POST['btn_unlink'])) {
  // unlink("assets/img/peserta/peserta-$_POST[btn_unlink].jpg");
  unlink("assets/img/peserta/wars/peserta-$_POST[btn_unlink].jpg");
  jsurl();
}

// select all from tb_peserta
$s = "SELECT * FROM tb_peserta 
WHERE 1 -- image is null 
ORDER BY id  
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
$count_process = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $file_local = "assets/img/peserta/peserta-$d[id].jpg";
  $file_online = "assets/img/peserta/$d[image]";
  $img_local =  file_exists($file_local) ? "<img src='$file_local' width=100>" : '';
  $img_online =  ($d['image'] and file_exists($file_online)) ? "<img src='$file_online' width=100>" : '';
  /*
  if ($file_local == $file_online || !$img_local) {
  } else {

    # ============================================================
    # REPLACE ONLINE IMAGE DENGAN FILE LOKAL
    # ============================================================
    if (!$img_online) {
      $img_online = 'Online Image not exists';

      $count_process++;

      resize_img($file_local);

      if ($set_as_online_image) {
        $image = "$d[id]-" . strtolower(preg_replace('/[^a-z]/i', '', $d['nama'])) . '-default.jpg';
        $new_file = "assets/img/peserta/$image";
        copy($file_local, $new_file);
        unlink($file_local);
        $s = "UPDATE tb_peserta SET image='$image' WHERE id=$;d[id]";
        echolog('Set as Online Image...' . $image . "<br>$s");
        mysqli_query($cn, $s) or die(mysqli_error($cn));
      }
      $tr .= "
        <tr>
          <td>$count_process</td>
          <td>
            $d[nama] - $d[id]
          </td>
          <td>
            <div>$img_local</div>
            <button class='btn btn-sm btn-secondary mt1' name=btn_unlink value=$d[id]>Unlink</button>
          </td>
          <td>$img_online</td>
        </tr>
      ";
    }
  }
    */

  # ============================================================
  # IMAGE ONLINE TIDAK ADA DAN LOKAL PUN TIDAK ADA
  # ============================================================
  /*
  if (!$img_online and !$img_local) {
    $count_process++;


    // update image peserta menjadi null
    $s = "UPDATE tb_peserta SET image=null WHERE id=$;d[id]";
    mysqli_query($cn, $s) or die(mysqli_error($cn));


    echolog("Image Local & Online not exists: $file_online<br>auto-update DB");
  }
    */

  # ============================================================
  # IMAGE LOCAL ADA DAN IMAGE IS NULL
  # ============================================================
  /*
  if ($img_local and !$d['image']) {
    $count_process++;


    resize_img($file_local);

    if ($set_as_online_image) {
      $image = "$d[id]-" . strtolower(preg_replace('/[^a-z]/i', '', $d['nama'])) . '-default.jpg';
      $new_file = "assets/img/peserta/$image";
      copy($file_local, $new_file);
      unlink($file_local);
      $s = "UPDATE tb_peserta SET image='$image' WHERE id=$;d[id]";
      echolog('Set as Online Image...' . $image . "<br>$s");
      mysqli_query($cn, $s) or die(mysqli_error($cn));
    }
    $tr .= "
        <tr>
          <td>$count_process</td>
          <td>
            $d[nama] - $d[id]
          </td>
          <td>
            <div>$img_local</div>
            <button class='btn btn-sm btn-secondary mt1' name=btn_unlink value=$d[id]>Unlink</button>
          </td>
          <td>$img_online</td>
        </tr>
      ";
    // update image peserta menjadi null
    // $s = "UPDATE tb_peserta SET image=null WHERE id=$;d[id]";
    // mysqli_query($cn, $s) or die(mysqli_error($cn));


    // echolog("Image Local ada dan Image is null [$d[image]]");
  }
    */


  # ============================================================
  # WAR IMAGES
  # ============================================================
  /*
  $file_local = "assets/img/peserta/wars/peserta-$d[id].jpg";
  $file_online = "assets/img/peserta/$d[war_image]";
  $war_img_local =  file_exists($file_local) ? "<img src='$file_local' width=100>" : '';
  $war_img_online =  ($d['war_image'] and file_exists($file_online)) ? "<img src='$file_online' width=100>" : '';

  if ($war_img_local) {
    $count_process++;


    // resize_img($file_local);

    if ($set_as_online_image) {
      $war_image = "$d[id]-war-" . strtolower(preg_replace('/[^a-z]/i', '', $d['nama'])) . '-default.jpg';
      $new_file = "assets/img/peserta/$war_image";
      // echolog("copy $file_local to $new_file");
      copy($file_local, $new_file);
      // echolog("unlink $file_local ");
      unlink($file_local);
      $s = "UPDATE tb_peserta SET war_image='$war_image' WHERE id=$;d[id]";
      mysqli_query($cn, $s) or die(mysqli_error($cn));
    }
    $tr .= "
        <tr>
          <td>$count_process</td>
          <td>
            $d[nama] - $d[id]
          </td>
          <td>
            <div>$war_img_local</div>
            <button class='btn btn-sm btn-secondary mt1' name=btn_unlink value=$d[id]>Unlink</button>
          </td>
          <td>$war_img_online</td>
        </tr>
      ";
    // update war_image peserta menjadi null
    // $s = "UPDATE tb_peserta SET war_image=null WHERE id=$;d[id]";
    // mysqli_query($cn, $s) or die(mysqli_error($cn));


    // echolog("Image Local ada dan Image is null [$d[war_image]]");
  }
    */

  # ============================================================
  # SET PROFIL OK
  # ============================================================
  /*
  if ($d['image']) {
    // echolog('Image ada');
    $file = "assets/img/peserta/$d[image]";
    if (file_exists($file)) {
      // echolog("Image ada dan profil_ok: $d[profil_ok]");
      // OK
      if ($d['profil_ok'] != 1) {
        $count_process++;
        // echolog("Image ada, profil_ok != 1, profil_ok=$d[profil_ok]");
        // update profil ok
        $s = "UPDATE tb_peserta SET profil_ok=1 WHERE id=$;d[id]";
        echolog($s);
        // mysqli_query($cn, $s) or die(mysqli_error($cn));
      }
    } else {
      // $s = "UPDATE tb_peserta SET image=null WHERE id=$;d[id]";
      // mysqli_query($cn, $s) or die(mysqli_error($cn));
      alert('Image tidak ada', 'danger');
      // jsurl('?manage_image', 300);
      // exit;
    }
  }
*/

  # ============================================================
  # WAR PROFIL ADA, CUMA HILANG
  # ============================================================
  if ($d['war_image']) {
    $file = "assets/img/peserta/$d[war_image]";
    $war_image = "assets/img/peserta/$d[war_image]";

    $img_war_local =  file_exists($war_image) ? "<img src='$war_image' width=100>" : '';

    if (!$img_war_local) {
      $count_process++;

      // update war_image peserta menjadi null
      $s = "UPDATE tb_peserta SET war_image=null WHERE id=$d[id]";
    }
  }

  // if ($count_process >= 30) break;
}

$tb = "<table class=table>$tr</table>";

if ($set_as_online_image) {
  echo $tb;
  // exit;
  alert('Set as Online Image success', 'success');
  jsurl('?manage_image', 300);
} else {
  echo "
    <form method=post>
      $tb
      <a class='btn btn-primary w-100' href='?manage_image&set_as_online_image=1'>Set Local as Online Image</a>
    </form>
  ";
}
