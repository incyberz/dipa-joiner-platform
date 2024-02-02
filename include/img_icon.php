<style>
.img_icon{
  cursor: pointer;
  transition:.2s;
}
.img_icon:hover{transform:scale(1.2)}
</style>
<?php
function img_icon($nama,$w=20,$h=20){
  $me = "assets/img/icons/$nama.png";
  $width = $w.'px';
  $height = $h.'px';
  if(file_exists($me)){
    return "<img class=img_icon src='$me' width=$width height=$height />";
  }else{
    return '<span class=red>icon-not-found</span>';
  }
}

// function img_icon_src($nama){
//   $r = rand(1,9);
//   $me = "assets/img/img_icon/$nama-$r.png";
//   if(file_exists($me)){
//     return $me;
//   }else{
//     $r = rand(1,14);
//     return "assets/img/img_icon/random/$r.png";
//   }
// }