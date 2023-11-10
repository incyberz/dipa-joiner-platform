<?php
function eta($eta,$indo=1){
  $menit = '';
  $jam = '';
  $hari = '';
  $minggu = '';
  $bulan = '';

  if($eta>=0){
    if($eta<60){
      return $indo ? "$eta detik lagi" : "$eta seconds left";
    }elseif($eta < 60*60){
      $menit = ceil($eta/60);
      return $indo ? "$menit menit lagi" : "$menit minutes left";
    }elseif($eta < 60*60*24){
      $jam = ceil($eta/(60*60));
      return $indo ? "$jam jam lagi" : "$jam hours left";
    }elseif($eta < 60*60*24*7){
      $hari = ceil($eta/(60*60*24));
      return $indo ? "$hari hari lagi" : "$hari days left";
    }elseif($eta < 60*60*24*7*4){
      $minggu = ceil($eta/(60*60*24*7));
      return $indo ? "$minggu minggu lagi" : "$minggu weeks left";
    }elseif($eta < 60*60*24*365){
      $bulan = ceil($eta/(60*60*24*7*4));
      return $indo ? "$bulan bulan lagi" : "$bulan monts left";
    }else{
      $tahun = ceil($eta/(60*60*24*365));
      return $indo ? "$tahun tahun lagi" : "$tahun years left";
    }
  }else{
    if($eta> -60){
      $eta = -$eta;
      return $indo ? "$eta detik yang lalu" : "$eta seconds left";
    }elseif($eta > -60*60){
      $menit = ceil($eta/60);
      $menit = -$menit;
      return $indo ? "$menit menit yang lalu" : "$menit minutes left";
    }elseif($eta > -60*60*24){
      $jam = ceil($eta/(60*60));
      $jam = -$jam;
      return $indo ? "$jam jam yang lalu" : "$jam hours left";
    }elseif($eta > -60*60*24*7){
      $hari = ceil($eta/(60*60*24));
      $hari = -$hari;
      return $indo ? "$hari hari yang lalu" : "$hari days left";
    }elseif($eta > -60*60*24*7*4){
      $minggu = ceil($eta/(60*60*24*7));
      $minggu = -$minggu;
      return $indo ? "$minggu minggu yang lalu" : "$minggu weeks left";
    }elseif($eta > -60*60*24*365){
      $bulan = ceil($eta/(60*60*24*7*4));
      $bulan = -$bulan;
      return $indo ? "$bulan bulan yang lalu" : "$bulan monts left";
    }else{
      $tahun = ceil($eta/(60*60*24*365));
      $tahun = -$tahun;
      return $indo ? "$tahun tahun yang lalu" : "$tahun years left";
    }    
  }
}