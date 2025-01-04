<?php
$form_bukti = '';
if (!$id_jenis) die('id_jenis at activity sub level show is missing.');
include 'activity_sublevel_process.php';

$arr = explode('?', $_SERVER['REQUEST_URI']);
$params = "?$arr[1]";

$id_challenge = $id_jenis;


$s2 = "SELECT a.*, 
a.id as id_sublevel,
a.no as no_sublevel,
a.nama as nama_sublevel,
a.*,
b.nama as nama_challenge 

FROM tb_sublevel_challenge a 
JOIN tb_challenge b ON a.id_challenge=b.id 
WHERE a.id_challenge=$id_challenge 
ORDER BY no
";
$q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
$tr = '';
$max_poin = 0;
if (mysqli_num_rows($q2) == 0) {
  echo div_alert('danger', "Belum ada data sublevel.");
} else {
  while ($d2 = mysqli_fetch_assoc($q2)) {
    $id_sublevel = $d2['id_sublevel'];
    $no_sublevel = $d2['no_sublevel'];
    $max_poin += $d2['poin'];
    $poin_show = $d2['poin'] ? number_format($d2['poin'], 0) : $unset;
    $objective_show = $d2['objective'] ?? "Objective: $unset";
    $nama_sublevel = $d2['nama_sublevel'];
    $poin = $d2['poin'];
    $nama_challenge = $d2['nama_challenge'];
    $objective = $d2['objective'];

    $hubungi_instruktur = $id_role == 2 ? '' : "<div class='darkred mt1 f12 miring'>Silahkan hubungi $Trainer agar segera melengkapi data sublevel.</div>";
    if ($closed) {
      $btn_submit = "<span class='btn btn-secondary btn-sm' onclick='`$Room Challenge ini sudah closed.`)'>Closed</span>";
    } else {
      $btn_submit = ($d2['objective'] && $d2['poin']) ? "<a href='$params&id_sublevel=$id_sublevel&no_sublevel=$no_sublevel' class='btn btn-primary btn-sm' name=btn_submit_sublevel value=$id_sublevel>Submit</a>"
        : "<button disabled class='btn btn-secondary btn-sm'>Can`t Submit</button>$hubungi_instruktur";
    }

    if ($id_role == 2) {
      $edit_sublevel_toggle = "<span class=btn_aksi id=sublevel$id_sublevel" . "__toggle>$img_edit</span>";
      $blok_edit_sublevel = "
        <div class='hideit wadah gradasi-kuning mt2 f14' id=sublevel$id_sublevel>
          <form method=post>
            <div class='f10 abu consolas mb2'>Form Edit Sublevel of <b class='f18 darkblue'>$nama_challenge</b> Challenge</div>
            Nama Sublevel
            <input name=nama_sublevel required minlength=5 maxlength=100 class='form-control form-control-sm mt1 mb2' placeholder='Nama Sublevel' value='$nama_sublevel'>
            Objective (Petunjuk Pengerjaan)
            <textarea name=objective required minlength=50 maxlength=1000 class='form-control form-control-sm mt1 mb2' placeholder='Sublevel Objectives... berisi petunjuk bagaimana caranya mendapatkan poin untuk level tersebut.' rows=5>$objective</textarea>
            Sublevel Poin
            <input name=poin_sublevel required type=number min=100 max=1000000 class='form-control form-control-sm mt1 mb1' placeholder='Points reward untuk sublevel ini.' value='$poin'>
            <div class='abu f12 miring mb2'>Poin ini akan digabung dengan sublevel poin lainnya + basic poin + dynamic ontime poin.</div>
            <button class='btn btn-primary btn-sm' name=btn_update_sublevel value=$id_sublevel>Update Sublevel :: id~$id_sublevel</button>
          </form>
        </div>
      ";
    } else {
      $edit_sublevel_toggle = '';
      $blok_edit_sublevel = '';
    }

    $tr .= "
      <tr>
        <td>$d2[no]</td>
        <td>
          <div class='mb1'>
            <span class='darkblue consolas bold'>$nama_sublevel</span> 
            $edit_sublevel_toggle
          </div>
          <div class='f14 abu'>$objective_show</div>
          $blok_edit_sublevel
        </td>
        <td>
          <div class='darkblue consolas bold mb1'>$poin_show</div>
        </td>
        <td>
          $btn_submit
        </td>
      </tr>
    ";
  }
}

$max_poin_show = number_format($max_poin, 0);

$form_bukti = "
  <div class=wadah>
    <h2 class='consolas f12 tebal abu mb2'>Sublevels of Challenge</h2>
    <table class='table table-striped'>
      <thead>
        <th>No</th>
        <th>Sublevel dan Objective</th>
        <th>Poin</th>
        <th>Submit</th>
      </thead>
      $tr
      <tr class='consolas f18'>
        <td colspan=2 align=right>Max-Point</td>
        <td colspan=2  class='darkblue bold'>$max_poin_show LP</td>
      </tr>
    </table>
  </div>
";
