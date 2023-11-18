<style>th{background: linear-gradient(#cfc,#afa)}</style>
<?php
login_only();
include 'include/arr_kelas.php';

echo "<section><div class=container>
<div class='section-title' data-aos='fade'>
  <h2>Nilai Akhir</h2>
  <p>Berikut adalah Rekap Nilai dan Nilai Akhir Anda</p>
</div>";





# =======================================================
# INITIAL VARIABLE
# =======================================================
$img['delete'] = '<img class=zoom src="assets/img/icons/delete.png" height=25px />';
foreach ($arr_kelas as $k => $jp) $data_csv[$k] = '';









# =======================================================
# LIST PESERTA | HIMSELFT
# =======================================================
$sql_id_peserta = $id_role==1 ? "a.id=$id_peserta" : '1';
$nama_paket_soal_uts = 'Soal UTS Semester 1 TA. 2023/2024';
$nama_paket_soal_uas = 'Soal UTS Semester 1 TA. 2023/2024';
$nama_paket_soal_remed_uts = 'Soal Pasca UTS';
$nama_paket_soal_remed_uas = 'Soal Pasca UAS';

$from_tb_jawabans = "FROM tb_jawabans p 
  JOIN tb_paket_soal q ON p.id_paket_soal=q.id 
  WHERE p.id_peserta=a.id 
  AND q.nama ";

$s = "SELECT  
a.id as id_peserta,
a.nama as nama_peserta,
a.nim,
a.kelas,
a.rank_global,
a.rank_kelas,
b.*,
(SELECT jumlah_ontime FROM tb_presensi_summary WHERE id_peserta=a.id AND id_room=$id_room) jumlah_ontime,
(SELECT jumlah_presensi FROM tb_presensi_summary WHERE id_peserta=a.id AND id_room=$id_room) total_ontime,
(SELECT COUNT(1) FROM tb_bukti_latihan WHERE id_peserta=a.id) jumlah_latihan,
(SELECT COUNT(1) FROM tb_bukti_tugas WHERE id_peserta=a.id) jumlah_tugas,
(SELECT COUNT(1) FROM tb_bukti_challenge WHERE id_peserta=a.id) jumlah_challenge,


(
  SELECT p.nilai $from_tb_jawabans = '$nama_paket_soal_uts'
  ORDER BY p.nilai DESC LIMIT 1) nilai_uts, 
(
  SELECT p.tanggal_submit $from_tb_jawabans = '$nama_paket_soal_uts'
  ORDER BY p.nilai DESC LIMIT 1) tanggal_submit_uts, 


(
  SELECT p.nilai $from_tb_jawabans = '$nama_paket_soal_uas'
  ORDER BY p.nilai DESC LIMIT 1) nilai_uas ,
(
  SELECT p.tanggal_submit $from_tb_jawabans = '$nama_paket_soal_uas'
  ORDER BY p.nilai DESC LIMIT 1) tanggal_submit_uas, 


(
  SELECT p.nilai $from_tb_jawabans = '$nama_paket_soal_remed_uts'
  ORDER BY p.nilai DESC LIMIT 1) nilai_remed_uts ,
(
  SELECT p.tanggal_submit $from_tb_jawabans = '$nama_paket_soal_remed_uts'
  ORDER BY p.nilai DESC LIMIT 1) tanggal_submit_remed_uts, 


(
  SELECT p.nilai $from_tb_jawabans = '$nama_paket_soal_remed_uas'
  ORDER BY p.nilai DESC LIMIT 1) nilai_remed_uas ,
(
  SELECT p.tanggal_submit $from_tb_jawabans = '$nama_paket_soal_remed_uas'
  ORDER BY p.nilai DESC LIMIT 1) tanggal_submit_remed_uas, 


(
  SELECT count(1) FROM tb_peserta WHERE status=1 AND kelas=a.kelas) total_kelas_ini 

FROM tb_peserta a 
JOIN tb_kelas b ON a.kelas=b.kelas 
WHERE a.status=1 
AND password is not null 
AND a.id_role=1 
AND $sql_id_peserta
ORDER BY a.kelas, a.nama
";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$rbobot['Count Ontime'] = 15;
$rbobot['Count Latihan'] = 15;
$rbobot['Count Tugas'] = 0;
$rbobot['Count Challenge'] = 10;
$rbobot['Rank Global'] = 10;
$rbobot['Rank Kelas'] = 15;
$rbobot['UTS'] = 15;
$rbobot['UAS'] = 20;
$rbobot['Remed UTS'] = 5;
$rbobot['Remed UAS'] = 0;

$tr='';
$td_bobot = '';
$th_komponen = '';
foreach ($rbobot as $komponen => $bobot) {
  $th_komponen.="<th>$komponen</th>";
  $td_bobot.="<td>$bobot%</td>";
}
$tr = '';
$thead = "
  <thead>
    <th width=4%>No</th>
    <th width=31%>Nama</th>
    $th_komponen
    <th>Nilai Akhir</th>
  </thead>
  <tr class=' abu miring'>
    <td colspan=2>Bobot</td>
    $td_bobot
    <td>100%</td>
  </tr>
";
$tr_empty = '<tr><td colspan=5>&nbsp;</td></tr>';
$no=0;
$i=0;
$last_kelas = '';
$nama_peserta = '';

$jumlah_latihan=0;
$jumlah_tugas=0;
$jumlah_challenge=0;
$rank_global=0;
$rank_kelas=0;

$konversi_latihan=0;
$konversi_tugas=0;
$konversi_challenge=0;
$konversi_rank_global=0;
$konversi_rank_kelas=0;

$nilai_akhir=0;
$total_kelas_ini=0;
$nilai_uts=0;
$nilai_uas=0;

$nilai_remed_uts=0;
$nilai_remed_uas=0;

while ($d=mysqli_fetch_assoc($q)) {
  $i++;
  $no++;
  $kelas_ini = $d['kelas'];
  if($last_kelas!=$d['kelas'] and $i!=1) $tr.=$tr_empty;
  if($last_kelas!=$d['kelas']){
    $tr.=$thead;
    $no=1;
    
    // HEADER CSV
    $reguler = $d['shift']=='P' ? 'Reguler' : 'NR';
    $data_csv[$kelas_ini].= "\n\nDAFTAR HADIR MAHASISWA DAN NILAI UTS TAHUN AKADEMIK 2023-2024 GANJIL\n\n";
    $data_csv[$kelas_ini].= "Prodi,$d[jenjang] - $d[nama_prodi] - $reguler\n";
    $data_csv[$kelas_ini].= "Mata Kuliah,Matematika Informatika\n";
    $data_csv[$kelas_ini].= "Semester / Kelas,$d[semester] / $d[kode_kelas]\n";
    $data_csv[$kelas_ini].= "Dosen,Iin S.T. M.Kom\n\n";
    $data_csv[$kelas_ini].= "NO,NAMA,NIM,TIMESTAMP KEHADIRAN,NILAI TUGAS,NILAI UTS,KETERANGAN\n";
  }
  $nama_peserta = strtoupper($d['nama_peserta']);

  $jumlah_ontime=$d['jumlah_ontime'];
  $total_ontime=$d['total_ontime']; // jumlah_presensi
  $jumlah_latihan=$d['jumlah_latihan'];
  $jumlah_tugas=$d['jumlah_tugas'];
  $jumlah_challenge=$d['jumlah_challenge'];
  $rank_global=$d['rank_global'];
  $rank_kelas=$d['rank_kelas'];
  $total_kelas_ini=$d['total_kelas_ini'];
  $nilai_uts=$d['nilai_uts'];
  $nilai_uas=$d['nilai_uas'];
  $nilai_remed_uts=$d['nilai_remed_uts'];
  $nilai_remed_uas=$d['nilai_remed_uas'];

  $red = $jumlah_latihan==0 ? 'gradasi-merah' : '';
  $red = ($jumlah_latihan>0 && $jumlah_latihan<=3) ? 'gradasi-kuning' : $red;

  $delete = $jumlah_latihan>3 ? '' : "<span class='delete_peserta pointer' id=delete_peserta__$d[id_peserta] >$img[delete]</span>";


  if($jumlah_ontime==0){
    $konversi_ontime=0;
  }elseif($jumlah_ontime==1 and $total_ontime==1){
    $konversi_ontime = 100;
  }elseif($jumlah_ontime==$total_ontime){
    $konversi_ontime = 100;
  }else{
    $konversi_ontime = number_format(50 + ($jumlah_ontime-1)*((round($total_ontime*8/10,0)/$total_ontime)*(100/$total_ontime)),0);
    if($konversi_ontime>100) $konversi_ontime=100;
  }

  if($jumlah_latihan==0){
    $konversi_latihan=0;
  }elseif($jumlah_latihan==1 and $total_latihan==1){
    $konversi_latihan = 100;
  }elseif($jumlah_latihan==$total_latihan){
    $konversi_latihan = 100;
  }else{
    $konversi_latihan = number_format(50 + ($jumlah_latihan-1)*((round($total_latihan*8/10,0)/$total_latihan)*(100/$total_latihan)),0);
    if($konversi_latihan>100) $konversi_latihan=100;
  }

  if($jumlah_tugas==0){
    $konversi_tugas=0;
  }elseif($jumlah_tugas==1 and $total_tugas==1){
    $konversi_tugas = 100;
  }elseif($jumlah_tugas==$total_tugas){
    $konversi_tugas = 100;
  }else{
    $konversi_tugas = number_format(50 + ($jumlah_tugas-1)*((round($total_tugas*8/10,0)/$total_tugas)*(100/$total_tugas)),0);
    if($konversi_tugas>100) $konversi_tugas=100;
  }

  if($jumlah_challenge==0){
    $konversi_challenge=0;
  }elseif($jumlah_challenge==1 and $total_challenge==1){
    $konversi_challenge = 100;
  }elseif($jumlah_challenge==$total_challenge){
    $konversi_challenge = 100;
  }else{
    $konversi_challenge = round(50 + ($jumlah_challenge-1)*((round($total_challenge*8/10,0)/$total_challenge)*(100/$total_challenge)),0);
    if($konversi_challenge>100) $konversi_challenge=100;
  }

  $konversi_rank_global = round(110-(($d['rank_global']-1)*((round($total_peserta*8/10,0)/$total_peserta)*(100/$total_peserta))),0);
  if($konversi_rank_global>100) $konversi_rank_global=100;

  $konversi_rank_kelas = round(110-(($d['rank_kelas']-1)*((round($d['total_kelas_ini']*8/10,0)/$d['total_kelas_ini'])*(100/$d['total_kelas_ini']))),0);
  if($konversi_rank_kelas>100) $konversi_rank_kelas=100;

  $nilai_tugas = round((
    $rbobot['Count Ontime'] * $konversi_ontime + 
    $rbobot['Count Latihan'] * $konversi_latihan + 
    $rbobot['Count Tugas'] * $konversi_tugas + 
    $rbobot['Count Challenge'] * $konversi_challenge + 
    $rbobot['Rank Global'] * $konversi_rank_global + 
    $rbobot['Rank Kelas'] * $konversi_rank_kelas  
    )/65,0);

  $nilai_akhir = round((
    $rbobot['Count Ontime'] * $konversi_ontime + 
    $rbobot['Count Latihan'] * $konversi_latihan + 
    $rbobot['Count Tugas'] * $konversi_tugas + 
    $rbobot['Count Challenge'] * $konversi_challenge + 
    $rbobot['Rank Global'] * $konversi_rank_global + 
    $rbobot['Rank Kelas'] * $konversi_rank_kelas + 
    $rbobot['UTS'] * $d['nilai_uts'] + 
    $rbobot['Remed UTS'] * $d['nilai_remed_uts'] + 
    $rbobot['Remed UAS'] * $d['nilai_remed_uas'] + 
    $rbobot['UAS'] * $d['nilai_uas']
    )/100,0);
  if($nilai_akhir>100) $nilai_akhir=100;

  $tr.= "
  <tr class='$red'>
    <td>$no</td>
    <td>$nama_peserta<div class='kecil miring abu'>$d[kelas]</div></td>
    <td>$d[jumlah_ontime]<div class='kecil miring abu'>$konversi_ontime</div></td>
    <td>$d[jumlah_latihan]<div class='kecil miring abu'>$konversi_latihan</div></td>
    <td>$d[jumlah_tugas]<div class='kecil miring abu'>$konversi_tugas</div></td>
    <td>$d[jumlah_challenge]<div class='kecil miring abu'>$konversi_challenge</div></td>
    <td>$d[rank_global] <span class='kecil miring abu'>of $total_peserta</span><div class='kecil miring abu'>$konversi_rank_global</div></td>
    <td>$d[rank_kelas] <span class='kecil miring abu'>of $d[total_kelas_ini]</span><div class='kecil miring abu'>$konversi_rank_kelas</div></td>
    <td>$d[nilai_uts]</td>
    <td>$d[nilai_uas]</td>
    <td>$d[nilai_remed_uts]</td>
    <td>$d[nilai_remed_uas]</td>
    <td>$nilai_akhir $delete</td>
  </tr>";

  //for repeat header
  $last_kelas = $d['kelas'];

  //autosave nilai_akhir
  if($nilai_uts=='') $nilai_uts='NULL';
  if($nilai_uas=='') $nilai_uas='NULL';
  if($nilai_remed_uts=='') $nilai_remed_uts='NULL';
  if($nilai_remed_uas=='') $nilai_remed_uas='NULL';
  $s2 = "UPDATE tb_peserta SET 
  uts=$nilai_uts,
  uas=$nilai_uas,
  remed_uts=$nilai_remed_uts,
  remed_uas=$nilai_remed_uas,
  nilai_akhir='$nilai_akhir' 
  WHERE id=$d[id_peserta]";
  $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));

  $tanggal_submit_uts = $d['tanggal_submit_uts'] ?? '-'; // ZZZ tanggal submit only for UTS
  $nim = $d['nim'] ?? '-';
  $data_csv[$kelas_ini] .= "$no,$nama_peserta,$nim,$tanggal_submit_uts,$nilai_tugas,$nilai_uts,-\n";
}

$link_download_csv = '';
if($id_role!=1){
  foreach ($arr_kelas as $k => $jp){
    echo "<pre class=debug>$data_csv[$k]</pre>";
    $fcsv = fopen("csv/$k.csv", "w+") or die("$path_csv cannot accesible.");
    fwrite($fcsv, $data_csv[$k]);
    fclose($fcsv);
    $link_download_csv.= "<a href='csv/$k.csv' target=_blank class='btn btn-success btn-sm'>$k</a> ";
  }
}

$div_row[1] = ['Count Ontime',"$jumlah_ontime <span class='kecil miring abu'>of $total_ontime</span>",$konversi_ontime.' <span class="kecil miring abu">x '.$rbobot['Count Ontime'].'%</span>'];
$div_row[2] = ['Count Latihan',"$jumlah_latihan <span class='kecil miring abu'>of $total_latihan</span>",$konversi_latihan.' <span class="kecil miring abu">x '.$rbobot['Count Latihan'].'%</span>'];
$div_row[3] = ['Count Tugas',"$jumlah_tugas <span class='kecil miring abu'>of $total_tugas</span>",$konversi_tugas.' <span class="kecil miring abu">x '.$rbobot['Count Tugas'].'%</span>'];
$div_row[4] = ['Count Challenge',"$jumlah_challenge <span class='kecil miring abu'>of $total_challenge</span>",$konversi_challenge.' <span class="kecil miring abu">x '.$rbobot['Count Challenge'].'%</span>'];
$div_row[5] = ['Rank Global',"$rank_global <span class='kecil miring abu'>of $total_peserta</span>",$konversi_rank_global.' <span class="kecil miring abu">x '.$rbobot['Rank Global'].'%</span>'];
$div_row[6] = ['Rank Kelas',"$rank_kelas <span class='kecil miring abu'>of $total_kelas_ini</span>",$konversi_rank_kelas.' <span class="kecil miring abu">x '.$rbobot['Rank Kelas'].'%</span>'];
$div_row[7] = ['UTS','-',$nilai_uts.' <span class="kecil miring abu">x '.$rbobot['UTS'].'%</span>'];
$div_row[8] = ['UAS','-',$nilai_uas.' <span class="kecil miring abu">x '.$rbobot['UAS'].'%</span>'];
$div_row[9] = ['Remed UTS','-',$nilai_remed_uts.' <span class="kecil miring abu">x '.$rbobot['Remed UTS'].'%</span>'];
$div_row[10] = ['Remed UAS','-',$nilai_remed_uas.' <span class="kecil miring abu">x '.$rbobot['Remed UAS'].'%</span>'];
$div_row[11] = ['<span class=darkblue>Nilai Akhir</span>','-',"<span class=blue style=font-size:30px>$nilai_akhir</span>"];

$rows='';
foreach ($div_row as $v) {
  $rows.="
    <div class='btop pt2 mb2'>
      <div class=row>
        <div class='col-md-4 miring abu'>
          $v[0]
        </div>
        <div class=col-md-4>
          $v[1]
        </div>
        <div class=col-md-4>
          $v[2]
        </div>
      </div>
    </div>
  ";
}

echo $id_role==2 ? "<table class='table'>$tr</table><div class=wadah><div class=mb1>Download CSV:</div>$link_download_csv</div>" : "
<div class=wadah data-aos=fade-up >
  <h3 class='darkblue mt3 mb3'>$nama_peserta <span class='miring abu kecil'>$kelas</span></h3>
  $rows
</div>";










echo "</div></section>";
?>





















<script>
  $(function(){
    $('.delete_peserta').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let id_peserta = rid[1];

      let link_ajax = "ajax/ajax_delete_peserta.php?id_peserta="+id_peserta;
      $.ajax({
        url:link_ajax,
        success:function(a){
          if(a.trim()=='sukses'){
            console.log(a);
          }else{
            alert(a);
          }
        }
      })
    })
  })
</script>