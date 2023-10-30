<?php 
$list_soal = '';
$s= "SELECT 
a.id as id_assign_soal,
b.soal,
b.kjs,
b.id as id_soal,
c.nama as paket_soal,
d.nama as tipe_soal 
FROM tb_assign_soal a 
JOIN tb_soal b ON a.id_soal=b.id 
JOIN tb_paket_soal c ON a.id_paket_soal=c.id 
JOIN tb_tipe_soal d ON b.tipe_soal=d.tipe_soal 
WHERE c.kelas = '$kelas' 
AND c.id=$id_paket_soal 
ORDER BY rand()
";


$div = '';
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_soal = mysqli_num_rows($q);
$debug .= "<br>jumlah_soal:<span id=jumlah_soal>$jumlah_soal</span>";

$i=0;
$new_tmp_jawabans = '';
while ($d=mysqli_fetch_assoc($q)) {
  $id_assign_soal = $d['id_assign_soal'];
  $new_tmp_jawabans.= $id_assign_soal."__$d[kjs]|";


  $i++;
  $div .= "
  <div class='div_soal belum_dijawab' id=div_soal__$id_assign_soal>
    <div class=row>
      <div class='col-md-8 mb2'>
        <div class=no_dan_soal>
          <div>$i.</div>
          <div>
            $d[soal] 
            <span class=debug>
              id_assign_soal:<span id=id_assign_soal__$id_assign_soal>$id_assign_soal</span> | 
              jawaban:<span id=jawaban__$id_assign_soal class=jawaban></span>
            </span> 
          </div>
        </div>
      </div>
      <div class=col-md-4>
        <div class='row'>
          <div class='col-sm-6 mb1'><span class='btn btn-secondary btn-sm btn-block btn_tf' id=btn_true__$id_assign_soal>True</span></div>
          <div class='col-sm-6 mb1'><span class='btn btn-secondary btn-sm btn-block btn_tf' id=btn_false__$id_assign_soal>False</span></div>
        </div>
      </div>
    </div>
  </div>
  ";
}

// autosave tmp_jawabans if null
$debug .= "<br>tmp_jawabans1: $tmp_jawabans <br>tmp_jawabans2: $new_tmp_jawabans";
if(strlen($new_tmp_jawabans) != strlen($tmp_jawabans)){
  $s = "UPDATE tb_paket_soal SET tmp_jawabans='$new_tmp_jawabans',tmp_jumlah_soal=$jumlah_soal WHERE id=$id_paket_soal";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

}

$list_soal = "<div data-aos='fade-up' data-aos-delay=300>$div</div>";






$tanggal_start = date('Y-m-d H:i:s');

$form_submit = "
  <form method=post>
    <input id=tanggal_start name=tanggal_start class='debug' value='$tanggal_start'>
    <input id=jawabans name=jawabans class='debug'>
    <div class='mt2' data-aos='fade-up' data-aos-delay='450'>
      <span id=span_submit class='btn btn-secondary btn-block' disabled>Belum bisa Submit Jawaban 
        <br>
        <span class=yellow>
          <span id=belum_dijawab_count style='font-size:40px'>
            $jumlah_soal
          </span>
          soal belum dijawab
        </span>
      </span>
      <div id='blok_btn_submit' class=hideit>
        <div class='mb2 mt2'>
          <input type='checkbox' id='check_submit'> 
          <label for='check_submit'>Saya sudah menjawab semua soal dan yakin untuk Submit</label>
        </div>
        <button class='btn btn-primary btn-block ' name=btn_submit id=btn_submit disabled>Submit Jawaban</button>
        <div class='small miring'><b>Perhatian!</b> Silahkan re-check kembali selama masih ada waktu!</div>

      </div>
    </div>
  </form>
";