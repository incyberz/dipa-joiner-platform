<style>
  .img_icon {
    cursor: pointer;
    transition: .2s;
  }

  .img_icon:hover {
    transform: scale(1.2)
  }
</style>
<?php
function img_icon($nama, $w = 20, $h = 20)
{
  $me = "assets/img/icons/$nama.png";
  $width = $w . 'px';
  $height = $h . 'px';
  if (file_exists($me)) {
    return "<img class=img_icon src='$me' width=$width height=$height />";
  } else {
    return '<span class=red>icon-not-found</span>';
  }
}

$img_edit = img_icon('edit');
$img_add = img_icon('add', 22, 20);
$img_delete = img_icon('delete');
$img_hapus = img_icon('delete');
