<style>
.meme{
  border-radius: 10px;
  max-width: 250px;
  box-shadow: 0 0 3px gray;
  padding: 3px;
  background: white;
}
</style>
<?php
function meme($nama){
  $r = rand(1,9);
  $me = "assets/img/meme/$nama-$r.jpg";
  if(file_exists($me)){
    return "<img class=meme src='$me' />";
  }else{
    $r = rand(1,14);
    return "<img class=meme src='assets/img/meme/random/$r.jpg' />";
  }
}

function meme_src($nama){
  $r = rand(1,9);
  $me = "assets/img/meme/$nama-$r.jpg";
  if(file_exists($me)){
    return $me;
  }else{
    $r = rand(1,14);
    return "assets/img/meme/random/$r.jpg";
  }
}