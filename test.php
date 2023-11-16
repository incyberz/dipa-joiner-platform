<?php
  function tambah_spasi($a){
    $a = str_replace('<',' < ',$a);
    $a = str_replace('>',' > ',$a);
    $a = str_replace('< =','<=',$a);
    $a = str_replace('> =','>=',$a);
    $a = str_replace('  ',' ',$a);
    return $a;
  }

  $b = '<b>asd a <= b then</b>';
  echo '<textarea>'.tambah_spasi("$b").'</textarea>';

