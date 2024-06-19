<?php
include 'activity_sublevel_process.php';

$form_bukti = '';
if (!$id_jenis) die('id_jenis at activity sub level submit is missing.');
$id_challenge = $id_jenis;

$no_sublevel = $_GET['no_sublevel'] ?? die(erid('no_sublevel'));
if (!$no_sublevel) die('no_sublevel at activity sub level submit is null.');


# ========================================================
# GET POINT CALCULATION
# ========================================================
if (!$tanggal_assign) die('dibutuhkan tanggal_assign pada activity sub level submit.');
// if(!$ontime_dalam) die('dibutuhkan ontime_dalam pada activity sub level submit.');
// if(!$ontime_deadline) die('dibutuhkan ontime_deadline pada activity sub level submit.');
if (!$ontime_point) die('dibutuhkan ontime_point pada activity sub level submit.');
$selisih = strtotime('now') - strtotime($tanggal_assign);

$sisa_ontime_point = 0;
if ($selisih < $ontime_dalam * 60) {
  $get_point = $basic_point + $ontime_point;
} else if ($selisih > $ontime_dalam * 60 + $ontime_deadline * 60) {
  $get_point = $basic_point;
} else {
  // echo 'if3<br>';
  $telat_point = round((($selisih - $ontime_dalam * 60) / ($ontime_deadline * 60)) * $ontime_point, 0);
  $sisa_ontime_point = $ontime_point - $telat_point;
  $get_point = $basic_point + $sisa_ontime_point;
}
$get_point_show = number_format($get_point, 0);
# ========================================================


$s2 = "SELECT a.*, 
a.id as id_sublevel,
a.nama as nama_sublevel
FROM tb_sublevel_challenge a 
WHERE id_challenge=$id_challenge 
AND no<=$no_sublevel 
ORDER BY no
";
$q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
$tr = '';
$max_poin = 0;
$id_sublevel_selected = null;
$last_nama_sublevel = '';
if (mysqli_num_rows($q2) == 0) {
  echo div_alert('danger', "Belum ada data sublevel.");
} else {
  while ($d2 = mysqli_fetch_assoc($q2)) {
    $max_poin += $d2['poin'];
    $poin_show = number_format($d2['poin'], 0);
    $last_nama_sublevel = $d2['nama_sublevel'];
    $id_sublevel_selected = $d2['id_sublevel'];
    $tr .= "
      <tr>
        <td>$d2[no]</td>
        <td>
          <div class='darkblue consolas bold mb1'>$d2[nama_sublevel]</div>
          <div class='f14 abu'>$d2[objective]</div>
          <div class='mt2 mb2'>
            <label class='pointer label_check'>
              <input type=checkbox required> 
              Saya menyatakan telah mengerjakan objective ini dan menyertakannya pada bukti challenge
            </label>
          </div>
        </td>
        <td>
          <div class='darkblue consolas bold mb1'>$poin_show</div>
        </td>
      </tr>
    ";
  }
}

$total_point = $max_poin + $get_point + $poin_antrian;
$max_poin_show = number_format($max_poin);
$poin_antrian_show = number_format($poin_antrian);
$total_point_show = number_format($total_point);

$link_includes_show = $link_includes ? "<li>link harus mengandung kata: <u id=link_includes>$link_includes</u></li>" : '';
$link_excludes_show = $link_excludes ? "<li>link tidak boleh ada kata: <u id=link_excludes>$link_excludes</u></li>" : '';

$id_sublevel_and_total_point = $id_sublevel_selected . "__$total_point";

$form_bukti = "
  <style>.label_check{transition:.2s}.label_check:hover{letter-spacing:.5px;color:darkblue}</style>
  <div class=wadah>
    <h2 class='consolas f12 tebal abu mb2'>Selected Sublevels</h2>
    <p class=darkblue><a href='?activity&jenis=challenge&id_assign=$id_assign'>Pilih Sublevel lain</a> | Kamu memilih Sublevel: <b>$last_nama_sublevel</b>.</p>
    <p class='f12 darkblue'>Kamu harus menyatakan bahwa semua Objectives sudah kamu kerjakan dan upload link bukti challenge milikmu.</p>
    <form method=post>
      <table class='table table-striped'>
        <thead>
          <th>No</th>
          <th>Sublevel dan Objective</th>
          <th>Poin</th>
        </thead>
        $tr
        <tr class='consolas f18'>
          <td colspan=2 align=right>Sublevel points</td>
          <td class='darkblue bold' align=right>$max_poin_show</td>
        </tr>
        <tr class='consolas f18'>
          <td colspan=2 align=right>Basic + Dynamic Ontime Point</td>
          <td class='darkblue bold' align=right>$get_point_show</td>
        </tr>
        <tr class='consolas f18'>
          <td colspan=2 align=right>Bonus First Submit</td>
          <td class='darkblue bold' align=right>$poin_antrian_show</td>
        </tr>
        <tr class='consolas f18'>
          <td colspan=2 align=right>Total Point yang didapat</td>
          <td class='darkblue bold' align=right>$total_point_show</td>
        </tr>
      </table>
      <div class='consolas abu f12 mb1'>Paste Link Bukti Challenge disini !</div>
      <input type=text minlength=15 maxlength=100 required name=bukti_link id=bukti_link class='form-control mb2'>
      <ul class='f14'>
        $link_includes_show
        $link_excludes_show
      </ul>
      <button class='btn btn-primary w-100' name=btn_submit_link id=btn_submit_link value='$id_sublevel_and_total_point' disabled>Submit Link Bukti Challenge</button>
    </form>
  </div>
";
?>
<script>
  $(function() {
    $('#bukti_link').change(function() {
      let val = $(this).val();
      $('#btn_submit_link').prop('disabled', 1);

      let includes_fail = 0;
      let link_includes = $('#link_includes').text();
      if (link_includes) {
        let arr_link_includes = link_includes.split(',');

        arr_link_includes.forEach((value) => {
          if (val.includes(value)) {
            console.log('include:', value);
          } else {
            includes_fail = 1;
          }
        })

        if (includes_fail) {
          console.log('includes_fail, return.');
          alert('Link harus mengandung kata \n\n' + link_includes);
          return;
        }
      }

      let excludes_fail = 0;
      let link_excludes = $('#link_excludes').text();
      if (link_excludes) {
        let arr_link_excludes = link_excludes.split(',');
        for (let i = 0; i < arr_link_excludes.length; i++) {
          if (val.includes(arr_link_excludes[i].trim())) {
            excludes_fail = 1;
          } else {
            console.log('exclude:', arr_link_excludes[i]);
          }
        }

        if (excludes_fail) {
          console.log('excludes_fail, return.');
          alert('Link tidak boleh ada kata \n\n' + link_excludes);
          return;
        }
      }

      $('#btn_submit_link').prop('disabled', 0);


    })
  })
</script>