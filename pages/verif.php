<style>
  .suggest {
    color: darkblue;
    transition: .2s;
    cursor: pointer;
  }

  .suggest:hover {
    color: blue;
    font-weight: bold;
  }
</style>
<?php
if ($id_role <= 1) jsurl('?');
$get_kelas = $_GET['kelas'] ?? '';
$get_history = $_GET['history'] ?? '';
$show_img = $_GET['show_img'] ?? '';


if ($get_history) {
  $judul = 'Histori Verifikasi';
  $judul2 = 'Cek Verifikasi';
  $url = '';
  $sql_not = 'not';
  $h2_history = 'History';
} else {
  $judul = 'Verifikasi Latihan dan Challenge';
  $judul2 = 'Histori Verifikasi';
  $url = '&history=1';
  $sql_not = '';
  $h2_history = 'Verifikasi';
}
set_title($judul);

echo "
  <div class='flexy flex-between'>
    <h1 class='abu tebal f12 mb2'>$judul</h1>
    <h2><a class=' tebal f12 mb2' href='?verif$url'>$judul2</a></h2>
  </div>
";

include 'verif-processors.php';

function menit_show($m)
{
  if (!$m || intval($m) < 1) return null;
  if ($m >= 60 * 24 * 365) {
    // 1 year
    return intval($m / (60 * 24 * 365)) . ' tahun';
  } elseif ($m >= 60 * 24 * 30) {
    // 1 month
    return intval($m / (60 * 24 * 30)) . ' bulan';
  } elseif ($m >= 60 * 24) {
    // 1 day
    return intval($m / (60 * 24)) . ' hari';
  } elseif ($m >= 60) {
    // 1 hour
    return intval($m / (60)) . ' jam';
  } else {
    return $m . ' menit';
  }
}

# =============================================================
# NAVIGASI BY KELAS
# =============================================================
$param_awal = "verif&history=$get_history";
include 'navigasi_room_kelas.php';
$sql_kelas = $get_kelas ? "g.kelas = '$get_kelas'" : '1';

# =============================================================
# KEYWORD HANDLER
# =============================================================
$keyword = $_GET['keyword'] ?? '';
if (isset($_POST['keyword'])) {
  $keyword = $_POST['keyword'];
  jsurl("?verif&keyword=$keyword");
}
$sql_keyword = $keyword ? "(e.nama LIKE '%$keyword%' OR d.nama LIKE '%$keyword%' OR g.kelas LIKE '%$keyword%')" : '1';

$jumlah_verif = 0;
$rjenis = ['latihan', 'challenge'];
foreach ($rjenis as $key => $jenis) {
  $s = "SELECT 
  a.id as id_bukti,
  a.*,
  b.id as id_assign,
  c.no as no_sesi,
  c.nama as nama_sesi,
  d.id as id_peserta,
  d.nama as nama_peserta,
  d.folder_uploads,
  e.id as id_jenis,
  e.nama as nama_jenis,
  e.*,
  g.kelas,
  (SELECT nama FROM tb_peserta WHERE id=a.verified_by) verifikator,
  (SELECT nama FROM tb_sublevel_challenge WHERE id=a.id_sublevel) nama_sublevel


  FROM tb_bukti_$jenis a 
  JOIN tb_assign_$jenis b ON a.id_assign_$jenis=b.id 
  JOIN tb_sesi c ON b.id_sesi=c.id
  JOIN tb_peserta d ON a.id_peserta=d.id 
  JOIN tb_$jenis e ON b.id_$jenis=e.id 
  JOIN tb_kelas_peserta f ON f.id_peserta=d.id 
  JOIN tb_kelas g ON f.kelas=g.kelas 
  JOIN tb_room_kelas h ON g.kelas=h.kelas 
  WHERE a.verified_by is $sql_not null 
  AND c.id_room = $id_room 
  AND h.id_room = $id_room 
  AND $sql_kelas 
  AND $sql_keyword 
  ORDER BY a.tanggal_upload, e.nama,g.kelas, d.nama, c.no 
  ";
  // echo '<pre>';
  // var_dump($s);
  // echo '</pre>';
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $row_count = mysqli_num_rows($q);


  if ($row_count) {
    $tr = '';
    $limit = 50;
    $jumlah_verif += $row_count;
    $i = 0;
    $id_all = ''; // untuk approve all
    while ($d = mysqli_fetch_assoc($q)) {
      $i++;
      $total_get_point = $d['get_point'] + $d['poin_antrian'] + $d['poin_apresiasi'];

      if ($i > $limit) {
        $tr .= "
          <tr>
            <td colspan=100% class='red f12 miring'>Data limitted, hanya tampil $limit row dari total $row_count. Silahkan Approve/Reject !</td>
          </tr>
        ";
        break;
      } else { // row <= 10, belum break

        $id_jenis = $d['id_jenis'];
        $id_bukti = $d['id_bukti'];
        $basic_point = $d['basic_point'];
        $ontime_point = $d['ontime_point'];
        $id_all .= "$id_bukti,";

        if ($jenis == 'latihan') {
          $href = "uploads/$d[folder_uploads]/$d[image]";
          if (file_exists($href)) {
            if ($show_img) {
              $img_caption = "<img src='$href' style=max-width=300px class='img-thumbnail' />";
            } else {
              $img_caption = 'Show Image';
            }
            $link_show_image = "<a target=_blank href='$href'>$img_caption</a>";
          } else {
            $link_show_image = '<span class="red consolas f12 miring">Image missing</span>';
          }

          $show_bukti = "
            <div class='darkblue tebal f14 consolas'>
              $link_show_image
            </div>
          ";
        } else {
          $link = strlen($d['link']) > 30 ? substr($d['link'], 0, 30) . '...' : $d['link'];
          $show_bukti = "<a class='consolas f14 tebal' target=_blank href='$d[link]'>Open Link</a>
          <div class='f10 abu'>$link</div>
          ";
        }

        if (!$get_history) {
          $value_btn_approve = "1__$id_bukti" . "__$jenis" . "__$jenis";
          $value_btn_reject = "-1__$id_bukti" . "__$jenis" . "__$jenis";

          $poin_apresiasi = $basic_point;
          $poin_apresiasi += $jenis == 'latihan' ? 0 : $ontime_point;
          // die("ZZZ: $poin_apresiasi");

          $range_apresiasi = "<input 
            type='range' 
            class='form-range range' 
            min='0' 
            max='$poin_apresiasi' 
            id='range__poin_apresiasi' 
            value='0' 
            step='1' 
            name=poin_apresiasi
          >";

          $Q1 = intval($poin_apresiasi / 4);
          $Q1 = $Q1 > 1000 ? intval($Q1 / 1000) . 'k' : $Q1;
          $Q2 = intval($poin_apresiasi / 2);
          $Q2 = $Q2 > 1000 ? intval($Q2 / 1000) . 'k' : $Q2;
          $Q3 = intval($poin_apresiasi * 3 / 4);
          $Q3 = $Q3 > 1000 ? intval($Q3 / 1000) . 'k' : $Q3;
          $Q4 = $poin_apresiasi;
          $Q4 = $Q4 > 1000 ? intval($Q4 / 1000) . 'k' : $Q4;
          $range_ruler = "
            <div class='flexy flex-between f12 abu mb2 mt1'>
              <div>0</div>
              <div>$Q1</div>
              <div>$Q2</div>
              <div>$Q3</div>
              <div>$Q4</div>
            </div>
          ";

          $Submiter = $jenis == 'latihan' ? 'Submiter' : 'Challenger';

          $form_approve = "
          <div class='hideit wadah gradasi-hijau' id=form_approve$id_bukti>
            <form method=post>
              <div class='consolas f10 abu mb2'>Form Approve</div>
              <div class='f14 abu mb1'>Poin Apresiasi (opsional)</div>
              $range_apresiasi
              $range_ruler
              
              <div class='f14 green miring pt2 pb2 border-top border-bottom mb2'>
                <div class=suggest id=suggest1__$id_bukti>The First $Submiter!</div>
                <div class=suggest id=suggest2__$id_bukti>Second $Submiter!</div>
                <div class=suggest id=suggest3__$id_bukti>Third $Submiter!</div>
                <div class=suggest id=suggest4__$id_bukti>Perfect!</div>
                <div class=suggest id=suggest5__$id_bukti>Mantaf!</div>
              </div>

              <input name=apresiasi id=apresiasi__$id_bukti class='form-control form-control-sm mb2' placeholder='Apresiasi Selamat! Anda berhasil...'>
              <button class='btn btn-success btn-sm w-100' name=btn_approve value='$value_btn_approve'>Approve</button>
            </form>
          </div>
          ";

          $form_reject = "
          <div class='hideit wadah gradasi-merah' id=form_reject$id_bukti>
            <form method=post>
              <div class='consolas f10 abu mb2'>Form Reject</div>
              <textarea name=alasan_reject class='form-control form-control-sm mb2' placeholder='Alasan reject...' rows=4 required minlength=10></textarea>
              <button class='btn btn-danger btn-sm w-100' name=btn_approve value='$value_btn_reject'>Reject</button>
            </form>
          </div>
          ";
        }

        $img_detail = img_icon('detail');
        $img_approve = img_icon('check');
        $img_reject = img_icon('reject');
        $icon_peserta = img_icon('mhs');

        $src_profil = "$lokasi_profil/$d[war_image]";
        $src_profil_hi = "$lokasi_profil/wars/peserta-$d[id_peserta]-hi.jpg";
        if (file_exists($src_profil)) {
          $dual_id = $id_peserta . "__$id_bukti";
          if ($show_img) {
            // always show img
            $div_img_peserta = "<div><a target=_blank href='$src_profil_hi'><img class='foto_profil' src='$src_profil' /></a></div>";
            $span_icon = '';
          } else {
            $div_img_peserta = "<div class=hideit id=div_img_peserta__$dual_id>$src_profil</div>";
            $span_icon = "<span class=icon_peserta id=icon_peserta__$dual_id>$icon_peserta</span>";
          }
        } else {
          $div_img_peserta = '';
          $span_icon = '';
        }

        $total_get_point_show = number_format($d['get_point'], 0);
        if ($d['poin_antrian']) {
          $total_get_point_show .= ' <span class="green bold">+ ' . number_format($d['poin_antrian']) . '</span> ';
        }

        $get_point_show = number_format($d['get_point'], 0);
        $basic_point_show = number_format($d['basic_point'], 0);
        $ontime_point_show = number_format($d['ontime_point'], 0);
        $ontime_dalam_show = menit_show($d['ontime_dalam']);
        $ontime_deadline_show = menit_show($d['ontime_deadline']);

        $max_point = $d['get_point'] == ($d['basic_point'] + $d['ontime_point']) ? '<span class="green bold">max-point</span>' : '';

        if ($get_history) { // mode history

          $tgl = date('M d, H:i', strtotime($d['tanggal_verifikasi']));
          $td_approve = "
          <div>by: $d[verifikator]</div>
          <div class='f12 abu'>at $tgl</div>
          ";
        } else { // mode biasa
          $td_approve = "
            <div class='f12 bold consolas mb1'>
              <span class='btn_aksi pointer darkblue' id=form_approve" . $id_bukti . "__toggle>$img_approve</span>
              <span class='btn_aksi pointer darkred' id=form_reject" . $id_bukti . "__toggle>$img_reject</span>
            </div>
            $form_approve
            $form_reject
          ";
        }

        $link_nama_jenis = "<a href='?verif&keyword=$d[nama_jenis]'>$d[nama_jenis]</a>";

        $nama_jenis_show = $jenis == 'challenge' ? "
        <div class='abu miring f12'>$link_nama_jenis</div>
        $d[nama_sublevel]
        " : $link_nama_jenis;


        $tr .= "
          <tr>
            <td>$i</td>
            <td>
              <a href='?verif&keyword=$d[nama_peserta]'>$d[nama_peserta]</a>  $span_icon 
              <div class='f12 abu'><a href='?verif&keyword=$d[kelas]'>$d[kelas]</a></div> 
              $div_img_peserta
            </td>
            <td>
              $nama_jenis_show 
              <span class='btn_aksi' id=detail" . $id_bukti . "__toggle>$img_detail</span>
              <div class='f12 abu'>$total_get_point_show LP $max_point</div> 
              <div class='hideit f12 abu wadah mt1' id=detail$id_bukti>
                <ul class='p0 pl2 m0'>
                  <li>P$d[no_sesi] $d[nama_sesi]</li>
                  <li>Basic point: $basic_point_show</li>
                  <li>Ontime point: $ontime_point_show</li>
                  <li>Ontime dalam: $ontime_dalam_show</li>
                  <li>Ontime deadline: $ontime_deadline_show</li>
                </ul>
              </div>
            </td>
            <td>$show_bukti</td>
            <td>$td_approve</td>
          </tr>
        ";
      } // end row <= 10
    }

    $bg_green = $keyword ? 'background:#0f0;' : '';
    $clear = $keyword ? '<div class=pt2><a href=?verif class="f12">Clear</a></div>' : '';
    $not_jenis = $jenis == 'latihan'
      ? '<span class="not_jenis abu f12 pointer" id=not_jenis__challenge>Challenge</span>'
      : '<span class="not_jenis abu f12 pointer" id=not_jenis__latihan>Latihan</span>';
    $hide = ($jenis == 'challenge' and !$keyword) ? 'hideit' : ''; //default blok challenge is hide
    $hide = ''; //zzz debug jangan hide jika latihan not found

    $img_mhs = img_icon('mhs');
    $arr = explode('?', $_SERVER['REQUEST_URI']);
    $href_show_img = $arr[1] . '&show_img=1';

    echo "
    <div class='$hide' id=blok_$jenis>
      <h2 class='proper f18 mt4 darkblue gradasi-biru p2'>$h2_history Bukti $jenis | <a href='?$href_show_img' onclick='return confirm(\"Show All Image? Ini akan memakan bandwith internet yang lumayan besar.\")'>$img_mhs</a> | $not_jenis</h2>
      <table class=table>
        <thead>
          <th>No</th>
          <th>Nama Peserta</th>
          <th class=proper>
            <form method=post class='p0 m0'>
              <input type=hidden name=show_img value=$show_img>
              <div class=flexy style='gap:5px'>
                <div>
                  <input class='form-control form-control-sm' name=keyword value='$keyword' placeholder='Filter nama $jenis' style='width:160px;$bg_green'>
                  <button class=hideit>Filter</button>
                </div>
                $clear
              </div>
            </form>
          </th>
          <th class=proper>Bukti $jenis</th>
          <th>Approve</th>
        </thead>
        $tr
        <tr>
          <td colspan=3>&nbsp;</td>
          <td colspan=100%>
            <form method=post>
              <input type=hidden name=jenis value='$jenis'>
              <button class='btn btn-success proper' name=btn_approve_all value=$id_all>Approve Semua Bukti diatas</button>
            </form>
          </td>
        </tr>
      </table>
    </div>
    ";
  } else { // no need verif
    $pada_kelas = $get_kelas ? " pada kelas $get_kelas" : '';
    $clear_keyword = $keyword ? " [keyword: <b class='consolas darkblue'>$keyword</b>] | <a href='?verif'>Clear Keyword</a>" : '';
    if ($get_history) {
      echo div_alert('info', "Data history bukti $jenis tidak ditemukan $pada_kelas $clear_keyword");
    } else {
      echo div_alert('info', "Data bukti $jenis tidak ditemukan $pada_kelas $clear_keyword");
    }
  } // end no need verif

}



?>
<script>
  $(function() {
    $(".not_jenis").click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let jenis = rid[1];
      console.log(jenis);
      if (jenis == 'latihan') {
        $('#blok_challenge').slideUp();
        $('#blok_latihan').slideDown();
      } else {
        $('#blok_latihan').slideUp();
        $('#blok_challenge').slideDown();
      }
    });
    $(".icon_peserta").click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id_peserta = rid[1];
      let id_bukti = rid[2];
      let dual_id = id_peserta + '__' + id_bukti;
      let src = $("#div_img_peserta__" + dual_id).text();
      $("#div_img_peserta__" + dual_id).html(`<img class='foto_profil' src='${src}'/>`);
      $("#div_img_peserta__" + dual_id).fadeIn();
      $("#icon_peserta__" + dual_id).fadeOut();
    });
    $(".suggest").click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id_bukti = rid[1];
      console.log(aksi, id_bukti);
      $("#apresiasi__" + id_bukti).val(
        $(this).text()
      );
    });
  })
</script>