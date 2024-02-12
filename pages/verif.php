<?php
if($id_role<=1) jsurl('?');
$get_kelas = $_GET['kelas'] ?? '';
$get_history = $_GET['history'] ?? '';

if($get_history){
  $judul = 'Histori Verifikasi';
  $judul2 = 'Cek Verifikasi';
  $url = '';
  $sql_not = 'not';
  $h2_history = 'History';
}else{
  $judul = 'Verifikasi Latihan dan Challenge';
  $judul2 = 'Histori Verifikasi';
  $url = '&history=1';
  $sql_not = '';
  $h2_history = 'Verifikasi';
}

echo "
  <div class='flexy flex-between'>
    <h1 class='abu tebal f12 mb2'>$judul</h1>
    <h2><a class=' tebal f12 mb2' href='?verif$url'>$judul2</a></h2>
  </div>
";

include 'verif_process.php';

function menit_show($m){
  if(!$m || intval($m)<1) return null;
  if($m >= 60*24*365){
    // 1 year
    return intval($m/(60*24*365)).' tahun';
  }elseif($m >= 60*24*30){
    // 1 month
    return intval($m/(60*24*30)).' bulan';
  }elseif($m >= 60*24){
    // 1 day
    return intval($m/(60*24)).' hari';
  }elseif($m >= 60){
    // 1 hour
    return intval($m/(60)).' jam';
  }else{
    return $m.' menit';
  }
}

$param_awal = "verif&history=$get_history";
include 'navigasi_kelas.php';

$sql_kelas = $get_kelas ? "g.kelas = '$get_kelas'" : '1';

$jumlah_verif = 0;
$rjenis = ['latihan','challenge'];
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
  (SELECT nama FROM tb_peserta WHERE id=a.verified_by) verifikator 

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
  ORDER BY g.kelas, d.nama, c.no 
  "; 
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $row_count = mysqli_num_rows($q);

  if($row_count){
    $tr = '';
    $limit = 10;
    $jumlah_verif += $row_count;
    $i=0;
    while($d=mysqli_fetch_assoc($q)){
      $i++;
      if($i>$limit){
        $tr.= "
          <tr>
            <td colspan=100% class='red f12 miring'>Data limitted, hanya tampil $limit row dari total $row_count. Silahkan Approve/Reject !</td>
          </tr>
        ";
        break;
      }else{ // row <= 10

        $id_jenis=$d['id_jenis'];
        $id_bukti=$d['id_bukti'];
  
        if($jenis=='latihan'){
          $href = "uploads/$d[folder_uploads]/latihan-$d[id_assign].jpg";
          if(file_exists($href)){
            $link_show_image = "<a target=_blank href='$href'>Show Image</a>";
          }else{
            $link_show_image = '<span class="red consolas f12 miring">Image missing</span>';
          }
          
          $show_bukti = "
            <div class='darkblue tebal f14 consolas'>
              $link_show_image
            </div>
          ";
        }else{
          $link = strlen($d['link'])>30 ? substr($d['link'],0,30).'...' : $d['link'];
          $show_bukti = "<a class='consolas f14 tebal' target=_blank href='$d[link]'>Open Link</a>
          <div class='f10 abu'>$link</div>
          ";
        }
  
        if(!$get_history){
          $form_approve = "
          <div class='hideit wadah gradasi-hijau' id=form_approve$id_bukti>
            <form method=post>
              <div class='consolas f10 abu mb2'>Form Approve</div>
              <input name=poin_tambahan class='form-control form-control-sm mb2' placeholder='Nilai tambahan dari instruktur'>
              <input name=apresiasi class='form-control form-control-sm mb2' placeholder='Apresiasi Selamat! Anda berhasil...'>
              <button class='btn btn-success btn-sm w-100' name=btn_approve value='1__".$id_bukti."__$jenis'>Approve</button>
            </form>
          </div>
          ";
    
          $form_reject = "
          <div class='hideit wadah gradasi-merah' id=form_reject$id_bukti>
            <form method=post>
              <div class='consolas f10 abu mb2'>Form Reject</div>
              <textarea name=alasan_reject class='form-control form-control-sm mb2' placeholder='Alasan reject...' rows=4 required minlength=10></textarea>
              <button class='btn btn-danger btn-sm w-100' name=btn_approve value='-1__$id_bukti'>Reject</button>
            </form>
          </div>
          ";
        }
  
        $img_detail = img_icon('detail');
        $img_approve = img_icon('check');
        $img_reject = img_icon('reject');
        $icon_peserta = img_icon('mhs');
  
        $src_profil = "assets/img/peserta/peserta-$d[id_peserta].jpg";
        if(file_exists($src_profil)){
          $dual_id = $id_peserta."__$id_bukti";
          $div_img_peserta = "<div class=hideit id=div_img_peserta__$dual_id>$src_profil</div>";
          $span_icon = "<span class=icon_peserta id=icon_peserta__$dual_id>$icon_peserta</span>";
        }else{
          $div_img_peserta = '';
          $span_icon = '';
        }

        $get_point_show = number_format($d['get_point'],0);
        $basic_point_show = number_format($d['basic_point'],0);
        $ontime_point_show = number_format($d['ontime_point'],0);
        $ontime_dalam_show = menit_show($d['ontime_dalam']);
        $ontime_deadline_show = menit_show($d['ontime_deadline']);

        $max_point = $d['get_point']==($d['basic_point']+$d['ontime_point']) ? '<span class="green bold">max-point</span>' : '';

        if($get_history){

          $tgl = date('M d, H:i',strtotime($d['tanggal_verifikasi']));
          $td_approve = "
          <div>by: $d[verifikator]</div>
          <div class='f12 abu'>at $tgl</div>
          ";
        }else{
          $td_approve = "
            <div class='f12 bold consolas mb1'>
              <span class='btn_aksi pointer darkblue' id=form_approve".$id_bukti."__toggle>$img_approve</span>
              <span class='btn_aksi pointer darkred' id=form_reject".$id_bukti."__toggle>$img_reject</span>
            </div>
            $form_approve
            $form_reject
          ";
        }

        $nama_jenis_show = $jenis=='challenge' ? "
        <div class='abu miring f12'>$d[nama_jenis]</div>
        Nama Sublevel
        " 
        : $d['nama_jenis'];


        $tr.= "
          <tr>
            <td>$i</td>
            <td>
              $d[nama_peserta]  $span_icon 
              <div class='f12 abu'>$d[kelas]</div> 
              $div_img_peserta
            </td>
            <td>
              $nama_jenis_show 
              <span class='btn_aksi' id=detail".$id_bukti."__toggle>$img_detail</span>
              <div class='f12 abu'>$get_point_show LP $max_point</div> 
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


    echo "
      <h2 class='proper f18 mt4 darkblue gradasi-biru p2'>$h2_history Bukti $jenis</h2>
      <table class=table>
        <thead>
          <th>No</th>
          <th>Nama</th>
          <th class=proper>Nama $jenis</th>
          <th class=proper>Bukti $jenis</th>
          <th>Approve</th>
        </thead>
        $tr
      </table>
    ";


  }else{ // no need verif
    $pada_kelas = $get_kelas ? " pada kelas $get_kelas" : '.';
    if($get_history){
      echo div_alert('info',"Belum ada history $jenis yang telah Anda verifikasi$pada_kelas");
    }else{
      echo div_alert('info',"Belum ada $jenis yang harus Anda verifikasi$pada_kelas");
    }
  } // end no need verif

}



?>
<script>
  $(function(){
    $(".icon_peserta").click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id_peserta = rid[1];
      let id_bukti = rid[2];
      let dual_id = id_peserta + '__' + id_bukti;
      let src = $("#div_img_peserta__"+dual_id).text();
      $("#div_img_peserta__"+dual_id).html(`<img class='foto_profil' src='${src}'/>`);
      $("#div_img_peserta__"+dual_id).fadeIn();
      $("#icon_peserta__"+dual_id).fadeOut();
    })
  })
</script>