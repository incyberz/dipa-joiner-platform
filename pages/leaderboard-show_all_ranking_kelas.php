<?php
instruktur_only();
$div_rank = '';
$col = intval(12 / count($rrank_kelas)); // bootstrap col
if ($col < 4) $col = 4;

foreach ($rrank_kelas as $ckelas => $ids) {

  $tr = '';
  $i = 0;
  foreach ($ids as $key => $id_pes) {
    $i++;
    $nama = strtoupper($rpeserta[$id_pes]['nama']);
    $ckelas = str_replace('--', '-', str_replace("-$ta_aktif", '', $rpeserta[$id_pes]['kelas']));
    $rpoin = $row[$id_pes];
    $poin = 0;
    foreach ($rpoin as $k2 => $v2) {
      if ($k2 == 'rank_kelas' || $k2 == 'rank_room') continue;
      $poin += intval($v2);
    }
    $rank = $key + 1;
    $poin = number_format($poin);
    $hideit = $i > 10 ? 'hideit' : '';
    $tr .= "
      <tr class='$hideit tr__$ckelas'>
        <td>$rank</td>
        <td>$nama</td>
        <td>$poin</td>
      </tr>
    ";
  }

  $show_all = count($ids) > 10  ? "<div class='tengah abu mb4'><i class='show_all pointer' id=show_all__$ckelas>Show All</i></div>" : '';



  $div_rank .= "
    <div class='col-lg-$col f12'>
      <h3 class='f16 bold darkblue tengah gradasi-toska border-top p2'>$ckelas</h3>
      <table class='table table-striped table-hover mb1'>
        <thead>
          <th>Rank</th>
          <th>Nama</th>
          <th>Poin</th>
        </thead>
        $tr
      </table>
      $show_all
    </div>
  ";
}

echo "
  <div class=row>$div_rank</div>
  <script>
    $(function() {
      $('.show_all').click(function() {
        let tid = $(this).prop('id');
        let rid = tid.split('__');
        let aksi = rid[0];
        let id = rid[1];
        $('.tr__' + id).show();
        $(this).hide();
      })
    })
  </script>  
";
