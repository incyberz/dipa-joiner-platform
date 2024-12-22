<?php
$caption = $jenis_sesi == 1 ? $count_sesi[1] : 'U';
$caption = $jenis_sesi ? $caption : 'T';
$nav_lp_selected = $hide_lp ? '' : 'nav_lp_selected';
$nav_lp_active = $hide_lp ? '' : 'nav_lp_active';
$nav_lp .= "<div class='gradasi-$warna[$jenis_sesi] p1 pl2 pr2 br5 pointer nav_lp $nav_lp_selected $nav_lp_active' id=nav_lp__$sesi[id]>$caption</div>";
