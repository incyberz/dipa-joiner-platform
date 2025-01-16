<?php
if (isset($_POST['btn_approve_all'])) {
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';
  foreach ($_POST['range_poin'] as $kode => $poin) {
    $s = "UPDATE tb_bukti_proyek SET poin=$poin,verif_by=$id_peserta, verif_at=CURRENT_TIMESTAMP WHERE kode='$kode'";
    echolog($s);
    mysqli_query($cn, $s) or die(mysqli_error($cn));
  }
  jsurl();
}


$id_indikator = $_GET['id_indikator'] ?? udef('id_indikator');
$full_size = $_GET['full_size'] ?? null;
$all_bukti = $_GET['all_bukti'] ?? null;


// select indikator
$s = "SELECT * FROM tb_indikator WHERE id=$id_indikator";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$indikator = mysqli_fetch_assoc($q);
$min_poin = number_format($indikator['min_poin']);
$max_poin = number_format($indikator['max_poin']);

$sql_all_bukti = $all_bukti ? '1' : "a.verif_at is null";

// select bukti_proyek
$s = "SELECT a.*,
b.title,
c.nama as nama_peserta
FROM tb_bukti_proyek a 
JOIN tb_indikator b ON a.id_indikator=b.id 
JOIN tb_peserta c ON a.id_peserta=c.id 
WHERE a.id_indikator=$id_indikator
AND $sql_all_bukti
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$divs = '';
if (!mysqli_num_rows($q)) {
  $bukti = div_alert('danger tengah', "Belum ada satupun [ Bukti Proyek ]");
} else {
  $i = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $kode = $d['kode'];
    $eta = eta2($d['tanggal_submit']);

    $range_value = $d['poin'] ?? $indikator['min_poin'];
    $thumb_ = $full_size ? '' : 'thumb-';

    $divs .= "
      <div class='tengah py-4'>
        <div>$i</div>
        <div><img src='$lokasi_proyek/$thumb_$d[bukti]' class='img-fluid pointer thumb' id=thumb__$d[kode] /></div>
        <div>$d[nama_peserta]</div>
        <div>$eta</div>
        <div><b>Poin:</b> <span id=poin__$kode class='f24'>$range_value</span></div>
        <div class='d-flex gap-4'>
          <div class='tengah f14'>
            Min
            <div class='f12 abu'>$min_poin</div>
          </div>
          <div class='flex-fill'>
            <input 
              type='range' 
              class='form-range range range_poin' 
              min='$indikator[min_poin]' 
              max='$indikator[max_poin]' 
              id='range_poin__$kode' 
              name='range_poin[$kode]' 
              value='$range_value' 
              step='1' 
            >
          </div>
          <div class='tengah f14'>
            Max
            <div class='f12 abu'>$max_poin</div>
          </div>

        </div>
      </div>
    ";
  }
}

$t = explode('?', $_SERVER['REQUEST_URI']);
$url_full_size = !$t[1] ? '' : "?$t[1]&full_size=1";;
$url_all_bukti = !$t[1] ? '' : "?$t[1]&all_bukti=1";;

set_h2('Bukti Proyek', "
  <div class='tengah mb4'>
    <a href='?proyek_akhir'>$img_prev</a>
  </div>
  <h3>
  <b>Indikator:</b> $indikator[title]
  </h3>
  <div class='f12 tengah'>
    <a href='$url_full_size'>Full Size Gambar Bukti</a> | 
    <a href='$url_all_bukti'>All Bukti</a>
  </div>
");

$divs = $divs ? "$divs<button class='btn btn-primary w-100' name=btn_approve_all>Approve All</button>" : alert('Belum ada bukti yang harus Anda verifikasi.');

echo "
  <form method=post>
    $divs
  </form>
";















?>
<script>
  $(function() {
    // $('input[type=range]').on('input', function() {
    //   var val = $(this).val();
    //   $(this).parent().siblings('td').find('.poin').text(val);
    // });

    $('.thumb').click(function() {
      let src = $(this).prop('src');
      // replace thumb with original
      $(this).attr('src', src.replace('thumb-', ''));
    });

    $('.range_poin').change(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let kode = rid[1];
      console.log(aksi, kode);

      $('#poin__' + kode).text($(this).val());

    });
  })
</script>