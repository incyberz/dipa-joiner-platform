<h2>Tambah Ujian PG</h2>
<style>
  .unclicked{background: #aaa;}
  .opsi{margin-top:4px; font-size:small; border: none;color:#555}
  .blok_opsi{display:grid; grid-template-columns: 25px auto 80px; gap:8px}
  .blok_info{background: #efe}
</style>

<?php
$kalimat_soal = '';
$opsi['a'] = '';
$opsi['b'] = '';
$opsi['c'] = '';
$opsi['d'] = '';
$pembahasan = '';
# =================================================================
# HANDLE SUBMIT
# =================================================================
if(isset($_POST['btn_simpan'])){
  $kalimat_soal = $_POST['kalimat_soal2'] ?? die(erid('kalimat_soal2::null'));
  $opsi['a'] = $_POST['opsi__a2'] ?? die(erid('opsi__a2::null'));
  $opsi['b'] = $_POST['opsi__b2'] ?? die(erid('opsi__b2::null'));
  $opsi['c'] = $_POST['opsi__c2'] ?? die(erid('opsi__c2::null'));
  $opsi['d'] = $_POST['opsi__d2'] ?? die(erid('opsi__d2::null'));
  $pembahasan = $_POST['pembahasan'];

  if($kalimat_soal=='') die(erid('kalimat_soal::null'));
  if($opsi['a']=='') die(erid('opsi__a::null'));
  if($opsi['b']=='') die(erid('opsi__b::null'));
  if($opsi['c']=='') die(erid('opsi__c::null'));
  if($opsi['d']=='') die(erid('opsi__d::null'));

  $kalimat_soal = clean_sql($kalimat_soal);
  $opsi['a'] = clean_sql($opsi['a']);
  $opsi['b'] = clean_sql($opsi['b']);
  $opsi['c'] = clean_sql($opsi['c']);
  $opsi['d'] = clean_sql($opsi['d']);
  $pembahasan = clean_sql($pembahasan);

  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';

  $opsies = "$opsi[a]~~~$opsi[b]~~~$opsi[c]~~~$opsi[d]";
  $jawaban = $opsi['a'];
  if($_POST['kj']=='b') $jawaban = $opsi['b'];
  if($_POST['kj']=='c') $jawaban = $opsi['c'];
  if($_POST['kj']=='d') $jawaban = $opsi['d'];

  $status = $id_role==1 ? 'NULL' : 2;

  $s = "INSERT INTO tb_soal_pg 
  (id_sesi,id_pembuat,kalimat_soal,tags,opsies,jawaban,id_status) values 
  ($_POST[id_sesi],$id_peserta,'$kalimat_soal','$_POST[my_tags]','$opsies','$jawaban',$status)
  ";
  // echo "<pre>$s</pre>";
  // zzz poin membuat soal belum disimpan
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  echo div_alert('success','Simpan Soal sukses. | <a href="?soal_saya">Soal Saya</a> |  <a href="?tanam_soal">Tanam Lagi</a>');
  // echo '<script>location.replace("?soal_saya")</script>';
  exit;
}




$rabjad = ['a','b','c','d'];
$opsies = '';
foreach ($rabjad as $abjad) {
  $opsies.= "
    <div class='blok_opsi'>
      <div class=tengah>$abjad.</div>
      <div>
        <input class='form-control opsies user_input' id=opsi__$abjad name=opsi__$abjad minlength=3 maxlength=30 required value='$opsi[$abjad]'>
      </div>
      <div>
        <span class='btn btn-sm btn-info btn-block opsi unclicked set_kj' id=set_kj__$abjad name=set_kj__$abjad>Set KJ</span>
      </div>
    </div>
  ";
}

$form = "
  <form method=post>

    <div class='form-group mb2'>
      <label for=kalimat_soal>Kalimat soal:</label>
      <textarea name=kalimat_soal id=kalimat_soal class='form-control user_input' rows=6 minlength=30 required>$kalimat_soal</textarea required>
      <div class='kecil miring abu mt1' id=minimal_30_huruf>minimal 30 huruf s.d 300 huruf</div>
    </div>

    $opsies

    <div class='wadah mt2 blok_info'>
      <div class='miring abu' style='font-size:10px'>Syarat simpan soal:</div>
      <div class='kecil miring darkred mt2' id=opsi_error></div>
      <div class='kecil miring darkred mt2' id=info_kj>Kunci Jawaban belum di Set</div>
      <div class='kecil miring darkred mt2' id=info_tags>Tags belum ada di kalimat atau opsi soal</div>
      <div class='kecil miring abu mt1 mb2' id=info_tags_awal>
        Pilihan Tags: 
        <span id=tags class=green>$tags</span>.
      </div>
      <div class='kecil miring darkred mt2 hideit' id=blok_similaritas>
        <span class='btn btn-secondary btn-sm' id=btn_cek_similaritas>Cek Similaritas</span>
        <span id=similaritas>0% </span>
        <span id=similaritas_info>???</span>
      </div>
      <div id=blok_reset_similaritas class='hideit mt2'>
        <span class='btn btn-primary btn-sm btn-block mb2' id=btn_reset_similaritas>Perbaiki Kalimat Soal</span>
        <div class='kecil darkblue miring tengah'>Ubahlah kalimat soal atau opsi agar agar angka similaritas tidak terlalu tinggi</div>
      </div>
    </div>

    <div id=info_poin class=mb2></div>

    <div id=blok_ubah_kalimat class='hideit mt2'>
      <span class='btn btn-secondary btn-sm btn-block mb2' id=btn_ubah_kalimat>Ubah Kalimat Soal</span>
      <div class='kecil darkblue miring tengah'>Saat ini kamu boleh mengubah kalimat soal, membuat pembahasan, atau langsung simpan soal</div>
    </div>

    <div id=blok_toggle_pembahasan class='hideit mt2'>
      <span class='btn btn-secondary btn-sm kecil' id=toggle_pembahasan>Tambah Pembahasan (+50 LP):</span>
    </div>
    <div id=blok_pembahasan class='hideit mt2'>
      <textarea name=pembahasan id=pembahasan class=form-control rows=6 minlength=30>$pembahasan</textarea>
      <div class='kecil miring abu'>Sifatnya opsional, minimal 30 huruf</div>
    </div>

    <div class=form-group>
      <button name=btn_simpan id=btn_simpan class='btn btn-primary btn-block' disabled>Simpan Soal Saya</button>
    </div>

  </form>
";

echo $form;