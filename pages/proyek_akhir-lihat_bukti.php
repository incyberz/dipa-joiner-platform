<?php
$id_indikator = $_GET['id_indikator'] ?? udef('id_indikator');

// select indikator
$s = "SELECT * FROM tb_indikator WHERE id=$id_indikator";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$indikator = mysqli_fetch_assoc($q);
$poin_min = number_format($indikator['poin_min']);
$poin_max = number_format($indikator['poin_max']);

// select bukti_proyek
$s = "SELECT a.*,
b.title,
c.nama as nama_peserta
FROM tb_bukti_proyek a 
JOIN tb_indikator b ON a.id_indikator=b.id 
JOIN tb_peserta c ON a.id_peserta=c.id 
WHERE a.id_indikator=$id_indikator";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) {
  $bukti = div_alert('danger tengah', "Belum ada satupun [ Bukti Proyek ]");
} else {
  $div = '';
  $i = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $kode = $d['kode'];

    $div .= "
      <div class='tengah py-4'>
        <div>$i</div>
        <div><img src='$lokasi_proyek/thumb-$d[bukti]' class='img-fluid pointer thumb' id=thumb__$d[kode] /></div>
        <div>$d[nama_peserta]</div>
        <div>$d[tanggal_submit]</div>
        <div class='d-flex gap-4'>
          <div class='tengah f14'>
            Min
            <div class='f12 abu'>$poin_min</div>
          </div>
          <div class='flex-fill'>
            <input 
              type='range' 
              class='form-range range' 
              min='$indikator[poin_min]' 
              max='$indikator[poin_max]' 
              id='poin' 
              name='poin[$kode]' 
              value='0' 
              step='1' 
            >
          </div>
          <div class='tengah f14'>
            Max
            <div class='f12 abu'>$poin_max</div>
          </div>

        </div>
      </div>
    ";
  }
}

set_h2("Bukti Proyek", "$indikator[title]");

echo "
$div
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
  })
</script>