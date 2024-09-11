<?php
$rjenis = ['latihan', 'challenge', 'pertanyaan', 'jawaban'];
$data = [];

foreach ($rjenis as $key => $jenis) {
  if ($jenis == 'pertanyaan') {
    $s = "SELECT a.*,b.nama as nama_sesi,
    (SELECT nama FROM tb_peserta WHERE id=a.verif_by) verifikator   
    FROM tb_bertanya a 
    JOIN tb_sesi b ON a.id_sesi=b.id 
    WHERE a.id_penanya=$id_peserta 
    ORDER BY a.id_sesi, a.tanggal DESC 
    ";
  } elseif ($jenis == 'jawaban') {
    $s = "SELECT a.*, c.nama as nama_sesi, b.pertanyaan, d.nama as penanya,
    (SELECT nama FROM tb_peserta WHERE id=a.verif_by) verifikator   
    FROM tb_bertanya_reply a 
    JOIN tb_bertanya b ON a.id_pertanyaan=b.id 
    JOIN tb_sesi c ON b.id_sesi=c.id 
    JOIN tb_peserta d ON b.id_penanya=d.id 
    WHERE a.id_penjawab=$id_peserta  
    ORDER BY b.id_sesi, a.tanggal DESC 
    ";
  } else {
    $s = "SELECT a.*, c.nama,
    (SELECT nama FROM tb_peserta WHERE id=a.verified_by) as verifikator  
    FROM tb_bukti_$jenis a 
    JOIN tb_assign_$jenis b ON a.id_assign_$jenis=b.id 
    JOIN tb_$jenis c ON b.id_$jenis=c.id
    WHERE a.id_peserta=$id_peserta 
    ORDER BY b.no 
    ";
  }
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $data[$jenis] = '<div data-aos=fade-up>' . div_alert('danger', "Belum ada data poin $jenis. Silahkan klik menu $jenis!") . '</div>';
  if (mysqli_num_rows($q)) {
    $data[$jenis] = '';
    $summary = 0;
    while ($d = mysqli_fetch_assoc($q)) {
      if ($jenis == 'latihan' || $jenis == 'challenge') {
        $hijau = $d['tanggal_verifikasi'] == '' ? 'merah' : 'hijau';
        $tanggal_upload = date('d-m-y H:i', strtotime($d['tanggal_upload']));
        $tanggal_verifikasi = date('d-m-y H:i', strtotime($d['tanggal_verifikasi']));
        $get_point_value = $d['tanggal_verifikasi'] == '' ? 0 : $d['get_point'];
        if ($d['tanggal_verifikasi'] == '') {
          $verif_info = '<span class="red">belum diverifikasi.</span>';
          $get_point_color = 'red';
        } else {
          if ($d['status'] == 1) {
            $summary += $get_point_value;
            $get_point_color = 'blue';
            $verif_info = "Verif by: $d[verifikator] at $tanggal_verifikasi";
          } else {
            $get_point_color = 'red';
            $verif_info = "<span class=red>Rejected: $d[alasan_reject]</span>";
          }
        }

        $data[$jenis] .= "
        <div class='wadah bg-white' data-aos=fade-up>
          <div class='row'>
            <div class='col-lg-5 mb2'>
              <div class=darkblue>$d[nama] <span class=debug>$d[id]</span></div>
              <div class='kecil miring'>Upload at $tanggal_upload</div>
            </div>
            <div class='col-lg-5 mb2'>
                <div class='tebal $get_point_color'>$d[get_point] LP</div>
                <div class='kecil miring abu'>$verif_info</div>
            </div>
            <div class='col-lg-2'>
              <div class='kecil miring abu' style='text-align:right'>Summary:</div>
              <div class='darkblue' style='text-align:right'>$summary LP</div>
            </div>
          </div>
        </div>
        ";
      } else {
        // handler pertanyaan & jawaban
        if ($jenis == 'pertanyaan') {
          $poin = $d['poin'] ?? 0;
          $pertanyaan = $d['pertanyaan'];
          $nama_sesi = $d['nama_sesi'];
          $verifikator = $d['verifikator'];
          $verif_date_show = date('D, M d, Y, H:i', strtotime($d['verif_date']));

          $summary += (100 + $poin);

          $data[$jenis] .= "
          <div class='wadah bg-white' data-aos=fade-up>
            <div class='kecil miring abu'>Topik: $nama_sesi</div>
            <div class='darkblue mb2 mt1'>$pertanyaan</div>
            <div class=wadah>
              <div class=darkblue>Get Point : 100 LP + $poin LP</div>
              <div class='kecil miring abu'>Verif by : $verifikator at $verif_date_show</div>
            </div>
            <div class='darkblue kanan pr4'>Summary: $summary</div>
          </div>
          ";
        } elseif ($jenis == 'jawaban') {

          $poin = $d['poin'] ?? 0;
          $penanya = $d['penanya'];
          $pertanyaan = $d['pertanyaan'];
          $jawaban = $d['jawaban'];
          $nama_sesi = $d['nama_sesi'];

          if ($d['verifikator'] == '') {
            $verif_info = '<span class="abu kecil miring">unverified</span>';
            $green = 'kecil abu';
          } else {
            $verifikator = $d['verifikator'];
            $verif_date_show = date('D, M d, Y, H:i', strtotime($d['verif_date']));
            $verif_info = "Verif by : $verifikator at $verif_date_show";
            $green = 'green';
          }

          $summary += (10 + $poin);

          $data[$jenis] .= "
          <div class='wadah bg-white' data-aos=fade-up>
            <div class='kecil miring abu'>Topik: $nama_sesi</div>
            <div class='kecil miring abu'>Pertanyaan dari: $penanya</div>
            <div class='darkblue kecil mb2 mt1'>$pertanyaan</div>
            <ul class='green miring'><li>kamu menjawab: $jawaban</li></ul>
            <div class=wadah>
              <div class='$green'>Get Point : 10 LP + $poin LP</div>
              <div class='kecil miring abu'>$verif_info</div>
            </div>
            <div class='green kanan pr4'>Summary: $summary</div>
          </div>
          ";
        }
      }
    }
  }
}



?>

<div class="section-title" data-aos="fade-up">
  <h2>My Points</h2>
  <p>Berikut adalah history poin yang dikumpulkan melalui latihan, challenge, pertanyaan, dan aktifitas training lainnya.</p>
</div>

<div class="wadah gradasi-hijau" data-aos=fade-up>
  <h3>Poin Latihan</h3>
  <?= $data['latihan'] ?>
</div>

<div class="wadah gradasi-pink" data-aos=fade-up>
  <h3>Poin Challenge</h3>
  <?= $data['challenge'] ?>
</div>


<div class="wadah gradasi-hijau" data-aos=fade-up>
  <h3>Poin Bertanya</h3>
  <?= $data['pertanyaan'] ?>
</div>

<div class="wadah gradasi-kuning" data-aos=fade-up>
  <h3>Poin Menjawab</h3>
  <?= $data['jawaban'] ?>
</div>