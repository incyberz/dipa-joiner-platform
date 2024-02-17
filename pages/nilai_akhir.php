<style>th{background: linear-gradient(#cfc,#afa)}</style>
<?php
login_only();
include 'include/arr_kelas.php';

echo "
<div class='section-title' data-zzz-aos='fade'>
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
$nama_paket_soal_uas = 'Soal UAS 2023';
$nama_paket_soal_remed_uts = 'Soal Pasca UTS';
$nama_paket_soal_remed_uas = 'Soal Remed UAS';

$from_tb_jawabans = "FROM tb_jawabans p 
  JOIN tb_paket_soal q ON p.id_paket_soal=q.id 
  WHERE p.id_peserta=a.id 
  AND p.id_room=$id_room 
  AND q.nama ";

$s = "SELECT  
a.id as id_peserta,
a.nama as nama_peserta,
a.nim,
b.kelas,
b.*,
c.*,
(SELECT jumlah_ontime FROM tb_presensi_summary WHERE id_peserta=a.id AND id_room=$id_room) jumlah_ontime,
(
  SELECT COUNT(1) FROM tb_sesi p 
  JOIN tb_sesi_kelas q ON p.id=q.id_sesi 
  WHERE p.id_room=$id_room 
  AND p.awal_presensi <= '$now') count_sesi_aktif,
(
  SELECT COUNT(1) FROM tb_bukti_latihan p 
  JOIN tb_assign_latihan q ON p.id_assign_latihan=q.id  
  WHERE p.id_peserta=a.id 
  AND q.id_room_kelas=$id_room_kelas) count_latihan,
(
  SELECT COUNT(1) FROM tb_bukti_challenge p 
  JOIN tb_assign_challenge q ON p.id_assign_challenge=q.id  
  WHERE p.id_peserta=a.id 
  AND q.id_room_kelas=$id_room_kelas) count_challenge,
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
  ORDER BY p.nilai DESC LIMIT 1) submit_uas ,
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
  SELECT count(1) FROM tb_kelas_peserta p  
  JOIN tb_kelas q ON p.kelas=q.kelas  
  WHERE q.tahun_ajar=$tahun_ajar 
  AND q.kelas=b.kelas) total_peserta_kelas,
(
  SELECT rank_global FROM tb_poin   
  WHERE id_room=$id_room  
  AND id_peserta=a.id) rank_global,
(
  SELECT rank_kelas FROM tb_poin   
  WHERE id_room=$id_room  
  AND id_peserta=a.id) rank_kelas


FROM tb_peserta a 
JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
JOIN tb_kelas c ON b.kelas=c.kelas 
WHERE a.status=1 
AND password is not null 
AND a.id_role=1 
AND $sql_id_peserta
ORDER BY b.kelas, a.nama
";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)>1 and $id_role==1) die('Duplicate result found at nilai_akhir');

$rbobot['Count Ontime'] = 25;
$rbobot['Count Latihan'] = 25;
$rbobot['Count Challenge'] = 10;
$rbobot['Rank Global'] = 15;
$rbobot['Rank Kelas'] = 25;
$rbobot['UTS'] = 0;
$rbobot['UAS'] = 0;
$rbobot['Remed UTS'] = 5;
$rbobot['Remed UAS'] = 5;

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

$count_latihan=0;
$count_challenge=0;
$rank_global=0;
$rank_kelas=0;

$konversi_latihan=0;
$konversi_challenge=0;
$konversi_rank_global=0;
$konversi_rank_kelas=0;

$nilai_akhir=0;
$total_peserta_kelas=0;
$nilai_uts=0;
$nilai_uas=0;
$submit_uas='';

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
  $count_sesi_aktif=$d['count_sesi_aktif']; 
  if(!$count_sesi_aktif) die(div_alert('danger',"count_sesi_aktif : $count_sesi_aktif canot be null"));

  $count_latihan=$d['count_latihan'];
  $count_challenge=$d['count_challenge'];
  $rank_global=$d['rank_global'];
  $rank_kelas=$d['rank_kelas'];
  $total_peserta_kelas=$d['total_peserta_kelas'];
  $nilai_uts=$d['nilai_uts'];
  $nilai_uas=$d['nilai_uas'];
  $submit_uas=$d['submit_uas'];
  $nilai_remed_uts=$d['nilai_remed_uts'];
  $nilai_remed_uas=$d['nilai_remed_uas'];

  $red = $count_latihan==0 ? 'gradasi-merah' : '';
  $red = ($count_latihan>0 && $count_latihan<=3) ? 'gradasi-kuning' : $red;

  $delete = $count_latihan>3 ? '' : "<span class='delete_peserta pointer' id=delete_peserta__$d[id_peserta] >$img[delete]</span>";


  if($jumlah_ontime==0){
    $jumlah_ontime=0;
    $konversi_ontime=0;
  }elseif($jumlah_ontime==1 and $count_sesi_aktif==1){
    $konversi_ontime = 100;
  }elseif($jumlah_ontime==$count_sesi_aktif){
    $konversi_ontime = 100;
  }else{
    $konversi_ontime = number_format(50 + ($jumlah_ontime-1)*((round($count_sesi_aktif*8/10,0)/$count_sesi_aktif)*(100/$count_sesi_aktif)),0);
    if($konversi_ontime>100) $konversi_ontime=100;
  }

  if($count_latihan==0){
    $konversi_latihan=0;
  }elseif($count_latihan==1 and $total_latihan==1){
    $konversi_latihan = 100;
  }elseif($count_latihan==$total_latihan){
    $konversi_latihan = 100;
  }else{
    $konversi_latihan = !$total_latihan ? 0 : number_format(50 + ($count_latihan-1)*((round($total_latihan*8/10,0)/$total_latihan)*(100/$total_latihan)),0);
    if($konversi_latihan>100) $konversi_latihan=100;
  }

  // }else{
  // }

  if($count_challenge==0){
    $konversi_challenge=0;
  }elseif($count_challenge==1 and $total_challenge==1){
    $konversi_challenge = 100;
  }elseif($count_challenge==$total_challenge){
    $konversi_challenge = 100;
  }else{
    $konversi_challenge = round(50 + ($count_challenge-1)*((round($total_challenge*8/10,0)/$total_challenge)*(100/$total_challenge)),0);
    if($konversi_challenge>100) $konversi_challenge=100;
  }

  if($rank_global){
    $konversi_rank_global = round(110-(($rank_global-1)*((round($total_peserta*8/10,0)/$total_peserta)*(100/$total_peserta))),0);
    if($konversi_rank_global>100) $konversi_rank_global=100;
  }


  if($rank_kelas){
    $konversi_rank_kelas = round(110-(($rank_kelas-1)*((round($d['total_peserta_kelas']*8/10,0)/$d['total_peserta_kelas'])*(100/$d['total_peserta_kelas']))),0);
    if($konversi_rank_kelas>100) $konversi_rank_kelas=100;
  }

  $nilai_harian = round((
    $rbobot['Count Ontime'] * $konversi_ontime + 
    $rbobot['Count Latihan'] * $konversi_latihan + 
    $rbobot['Count Challenge'] * $konversi_challenge + 
    $rbobot['Rank Global'] * $konversi_rank_global + 
    $rbobot['Rank Kelas'] * $konversi_rank_kelas  
    )/65,0);

  $nilai_akhir = round((
    $rbobot['Count Ontime'] * $konversi_ontime + 
    $rbobot['Count Latihan'] * $konversi_latihan + 
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
    <td>$d[count_latihan]<div class='kecil miring abu'>$konversi_latihan</div></td>
    <td>$d[count_challenge]<div class='kecil miring abu'>$konversi_challenge</div></td>
    <td>$d[rank_global] <span class='kecil miring abu'>of $total_peserta</span><div class='kecil miring abu'>$konversi_rank_global</div></td>
    <td>$d[rank_kelas] <span class='kecil miring abu'>of $d[total_peserta_kelas]</span><div class='kecil miring abu'>$konversi_rank_kelas</div></td>
    <td>$d[nilai_uts]</td>
    <td>$d[nilai_uas]<div class='abu f12'>$submit_uas</div></td>
    <td>$d[nilai_remed_uts]</td>
    <td>$d[nilai_remed_uas]</td>
    <td>$nilai_akhir $delete</td>
  </tr>";

  //for repeat header
  $last_kelas = $d['kelas'];

  $belum = '<span class="consolas darkred f12 miring">belum</span>';
  $nilai_uts_show = $nilai_uts ? $nilai_uts : $belum;
  $remed_uts_show = $nilai_remed_uts ? $nilai_remed_uts : $belum;
  $nilai_uas_show = $nilai_uas ? $nilai_uas : $belum;
  $remed_uas_show = $nilai_remed_uas ? $nilai_remed_uas : $belum;
  
  //autosave nilai_akhir
  if($nilai_uts=='') $nilai_uts='NULL';
  if($nilai_uas=='') $nilai_uas='NULL';
  if($nilai_remed_uts=='') $nilai_remed_uts='NULL';
  if($nilai_remed_uas=='') $nilai_remed_uas='NULL';
  $s2 = "UPDATE tb_poin SET 
  uts=$nilai_uts,
  uas=$nilai_uas,
  remed_uts=$nilai_remed_uts,
  remed_uas=$nilai_remed_uas,
  nilai_akhir='$nilai_akhir' 
  WHERE id_peserta=$d[id_peserta] 
  AND id_room=$id_room 
  ";
  // echo '<pre>';
  // var_dump($s2);
  // echo '</pre>';
  $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));

  $tanggal_submit_uts = $d['tanggal_submit_uts'] ?? '-'; // ZZZ tanggal submit only for UTS
  $nim = $d['nim'] ?? '-';
  $data_csv[$kelas_ini] .= "$no,$nama_peserta,$nim,$tanggal_submit_uts,$nilai_harian,$nilai_uts,-\n";
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

$div_row[1] = ['Count Ontime',"$jumlah_ontime <span class='kecil miring abu'>of $count_sesi_aktif</span>",$konversi_ontime.' <span class="kecil miring abu">x '.$rbobot['Count Ontime'].'%</span>'];
$div_row[2] = ['Count Latihan',"$count_latihan <span class='kecil miring abu'>of $total_latihan</span>",$konversi_latihan.' <span class="kecil miring abu">x '.$rbobot['Count Latihan'].'%</span>'];
$div_row[3] = ['Count Challenge',"$count_challenge <span class='kecil miring abu'>of $total_challenge</span>",$konversi_challenge.' <span class="kecil miring abu">x '.$rbobot['Count Challenge'].'%</span>'];
$div_row[4] = ['Rank Global',"$rank_global <span class='kecil miring abu'>of $total_peserta</span>",$konversi_rank_global.' <span class="kecil miring abu">x '.$rbobot['Rank Global'].'%</span>'];
$div_row[5] = ['Rank Kelas',"$rank_kelas <span class='kecil miring abu'>of $total_peserta_kelas</span>",$konversi_rank_kelas.' <span class="kecil miring abu">x '.$rbobot['Rank Kelas'].'%</span>'];
$div_row[6] = ['UTS','-',$nilai_uts_show.' <span class="kecil miring abu">x '.$rbobot['UTS'].'%</span>'];
$div_row[7] = ['UAS','-',$nilai_uas_show.' <span class="kecil miring abu">x '.$rbobot['UAS'].'%</span>'];
$div_row[8] = ['Remed UTS','-',$remed_uts_show.' <span class="kecil miring abu">x '.$rbobot['Remed UTS'].'%</span>'];
$div_row[9] = ['Remed UAS','-',$remed_uas_show.' <span class="kecil miring abu">x '.$rbobot['Remed UAS'].'%</span>'];
$div_row[10] = ['<span class=darkblue>Nilai Akhir</span>','-',"<span class=blue style=font-size:30px>$nilai_akhir</span>"];

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