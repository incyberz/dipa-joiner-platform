<?php
$meme = meme('dont-have');
$hasil_submit = "<div class='kecil miring merah tengah p2'>kamu belum mengerjakan.<div class=mt2>$meme</div></div>";
if ($id_bukti) {
  $s2 = "SELECT a.*,b.no as no_lat, 
  (
    SELECT nama FROM tb_peserta 
    WHERE id=a.verified_by) as verifikator

  FROM tb_bukti_$jenis a 
  JOIN tb_assign_$jenis b ON a.id_assign_$jenis=b.id 
  JOIN tb_$jenis c ON b.id_$jenis=c.id  
  WHERE a.id=$id_bukti";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  $d_bukti = mysqli_fetch_assoc($q2);
  $tanggal_upload = $d_bukti['tanggal_upload'];
  $get_point = $d_bukti['get_point'];
  $poin_antrian = $d_bukti['poin_antrian'];
  $poin_apresiasi = $d_bukti['poin_apresiasi'];
  $tanggal_verifikasi = $d_bukti['tanggal_verifikasi'];
  $verifikator = $d_bukti['verifikator'];
  $status = $d_bukti['status'];
  $alasan_reject = $d_bukti['alasan_reject'];

  $total_get_point = $get_point + $poin_antrian + $poin_apresiasi;
  $total_get_point_show = number_format($total_get_point, 0);
  $tanggal_upload_show = hari_tanggal($tanggal_upload) . ', ' . eta2($tanggal_upload);

  $id_sublevel = '';
  $nama_sublevel = '';
  $no_sublevel = '';
  if ($jenis == 'challenge') {
    $id_sublevel = $d_bukti['id_sublevel'] ?? die('id_sublevel is null at activity show.');
    $s3 = "SELECT nama as nama_sublevel,no as no_sublevel FROM tb_sublevel_challenge WHERE id=$id_sublevel";
    $q3 = mysqli_query($cn, $s3) or die(mysqli_error($cn));
    $d3 = mysqli_fetch_assoc($q3);
    $nama_sublevel = $d3['nama_sublevel'];
    $no_sublevel = $d3['no_sublevel'];
  }

  $form_hapus = "
    <form method=post>
      <button class='btn btn-danger btn-block proper' name=btn_hapus_bukti onclick='return confirm(\"Yakin untuk menghapus dan upload kembali bukti $jenis?\")' value='$id_bukti'>Hapus bukti $jenis</button>
    </form>
  ";

  if ($tanggal_verifikasi != '' and $status == 1) {
    $verif_icon = img_icon('check');
    $verif_opsi = div_alert('success', "Selamat! Bukti kamu sudah terverifikasi oleh $verifikator pada $tanggal_verifikasi");
  } elseif ($status == -1) {
    $verif_icon = "<div class='red kecil miring'>( rejected :: $alasan_reject )</div>";
    $verif_opsi = div_alert('danger', "Maaf, bukti kamu ditolak dengan alasan $alasan_reject.$form_hapus");
  } else {
    $verif_icon = '<div class="red f12 ">( belum diverifikasi )</div>';
    $verif_opsi = "<div class='tengah abu f14 mb2'>Bukti kamu <span class='darkred bold'>belum diverifikasi</span>, kamu masih boleh menghapusnya dan re-submit kembali</div>$form_hapus";
  }


  if ($jenis == 'latihan') {
    $src = "uploads/$folder_uploads/$d_bukti[image]";
    $image_bukti = "
      <div class=mb2>
        <a href='$src' target=_blank onclick='return confirm(\"Buka gambar di Tab baru?\")'>
          <img src='$src' class='img-fluid'>
        </a>
        <div class=mt2>$verif_opsi</div>
      </div>
    ";
  } else if ($jenis == 'challenge') {
    $image_bukti = "<a href='$d_bukti[link]' target=_blank>$d_bukti[link]</a><div class=mt2>$verif_opsi</div>";
  } else {
    die("Jenis activity: $jenis unhandled action.");
  }

  $menit = round((strtotime($tanggal_upload) - strtotime($tanggal_assign)) / 60, 0);
  $hari = intval($menit / (60 * 24));
  $jam = intval($menit / 60) % 24;
  $sisa_menit = $menit % 60;

  $dikerjakan_dalam = $jam ? "$jam jam $sisa_menit menit" : "$sisa_menit menit";
  $dikerjakan_dalam = $hari ? "$hari hari $dikerjakan_dalam" : $dikerjakan_dalam;


  $sublevel_info = $jenis == 'challenge' ? "<li class=kecil>Sublevel: <span class='f20 darkblue'>Level $no_sublevel # $nama_sublevel</span></li>" : '';


  $poin_apresiasi = $basic_point;
  $poin_apresiasi += $jenis == 'latihan' ? 0 : $ontime_point;
  $poin_apresiasi_show = $d_bukti['poin_apresiasi'] ? '<span class="bold green">' . number_format($d_bukti['poin_apresiasi']) . '</span>' : '<span class="brown bold">0 s.d ' . number_format($poin_apresiasi) . ' LP</span>';

  $hasil_submit = "
  <div class='abu f14 mb1 tengah'>Total Get Point: </div>
  <div class='border-top border-bottom p2 gradasi-toska tengah f20 darkblue bold mb2'>
    $total_get_point_show LP 
    $verif_icon
    <div class='f14 green'>$d_bukti[apresiasi]</div>
  </div> 
  <ul class='p0 pl3'>
    $sublevel_info
    <li class=kecil>Poin Apresiasi: $poin_apresiasi_show</li>
    <li class=kecil>Tanggal Upload: $tanggal_upload_show</li>
    <li class=kecil>Dikerjakan dalam $dikerjakan_dalam</li>
  </ul>
  <div>$image_bukti</div>
  ";
}

$btn_hapus_bukti = '';
if ($status == -1 and $jenis == 'challenge') {
  $btn_hapus_bukti = "
  <form method=post>
    <button class='btn btn-danger btn-sm' name=btn_hapus_bukti  id=challenge__$id_assign_jenis onclick='return confirm(\"Yakin untuk hapus Challenge dan Reupload kembali?\")' value='$id_bukti'>Hapus dan Reupload</button>
  </form>
  ";
}

$hasil_submit = "
  <div class='wadah'>
    <h3 class='tengah darkblue border-bottom f16 pb2'>Hasil Submit $Jenis</h3>
    $hasil_submit
    $btn_hapus_bukti
  </div>
";
