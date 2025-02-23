<?php
function resize_img($img_asal, $img_resized = '', $max_width = 1000, $max_height = 1000, $min_width = 50, $min_height = 50)
{
  $pesan = '';
  $orig_image = imagecreatefromjpeg($img_asal);
  $image_info = getimagesize($img_asal);
  $width_orig  = $image_info[0];
  $height_orig = $image_info[1];

  if ($width_orig < $min_width || $height_orig < $min_height) {
    $pesan = "<div>Resolusi image terlalu kecil. Silahkan pilih gambar dengan min-size $min_width x $min_height pixel !</div>";
  } else if ($width_orig > $max_width || $height_orig > $max_height) {
    if ($width_orig > $height_orig) {
      $width = $max_width;
      $height = round($height_orig * $max_width / $width_orig, 0);
    } else {
      $height = $max_height;
      $width = round($width_orig * $max_height / $height_orig, 0);
    }
    $pesan .= "<br>Current : $width_orig x $height_orig px";
    $pesan .= "<br>Resize to : $width x $height px";

    $target_img = imagecreatetruecolor($width, $height);
    imagecopyresampled($target_img, $orig_image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
    $img_output = $img_resized ? $img_resized : $img_asal;
    imagejpeg($target_img, $img_output, 80);
  } else {
    if ($img_asal == $img_resized || $img_resized == '') {
      $pesan .= '<br>No need to be resized.';
    } else {
      copy($img_asal, $img_resized);
      $pesan .= '<br>No need to be resized. Just copy to the Image Resized.';
    }
  }

  return $pesan;
}
