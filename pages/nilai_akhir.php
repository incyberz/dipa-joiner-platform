<style>th{background: linear-gradient(#cfc,#afa)}</style>
<?php
login_only();

echo "<section><div class=container>
<div class='section-title' data-aos='fade-up'>
  <h2>Monitoring Peserta</h2>
  <p>Peserta yang Aktif dan yang tidak</p>
</div>";





# =======================================================
# INITIAL VARIABLE
# =======================================================
$img['delete'] = '<img class=zoom src="assets/img/icons/delete.png" height=25px />';





# =======================================================
# COUNT LATIHAN / TUGAS / CHALLENGE
# =======================================================
$s = "SELECT 
(SELECT COUNT(1) FROM tb_peserta WHERE status=1) total_peserta,
(SELECT COUNT(1) FROM tb_act_latihan WHERE status=1) total_latihan,
(SELECT COUNT(1) FROM tb_act_tugas WHERE status=1) total_tugas,
(SELECT COUNT(1) FROM tb_act_challenge WHERE status=1) total_challenge
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
$total_peserta = $d['total_peserta'];
$total_latihan = $d['total_latihan'];
$total_tugas = $d['total_tugas'];
$total_challenge = $d['total_challenge'];





# =======================================================
# LIST PESERTA | HIMSELFT
# =======================================================
$sql_id_peserta = $id_role==1 ? "a.id=$id_peserta" : '1';
$nama_paket_soal_uts = 'Latihan Soal UTS';
$nama_paket_soal_uas = 'Latihan Soal UTS';
$s = "SELECT a.*,
(SELECT COUNT(1) FROM tb_bukti_latihan WHERE id_peserta=a.id) jumlah_latihan,
(SELECT COUNT(1) FROM tb_bukti_tugas WHERE id_peserta=a.id) jumlah_tugas,
(SELECT COUNT(1) FROM tb_bukti_challenge WHERE id_peserta=a.id) jumlah_challenge,
(
  SELECT p.nilai FROM tb_jawabans p 
  JOIN tb_paket_soal q ON p.id_paket_soal=q.id 
  WHERE p.id_peserta=a.id 
  AND q.nama = '$nama_paket_soal_uts'
  ORDER BY p.nilai DESC LIMIT 1) nilai_uts, 
(
  SELECT p.nilai FROM tb_jawabans p 
  JOIN tb_paket_soal q ON p.id_paket_soal=q.id 
  WHERE p.id_peserta=a.id 
  AND q.nama = '$nama_paket_soal_uas'
  ORDER BY p.nilai DESC LIMIT 1) nilai_uas ,
(
  SELECT count(1) FROM tb_peserta WHERE status=1 AND kelas=a.kelas) jumlah_peserta_kelas 

FROM tb_peserta a 
WHERE status=1 
AND password is not null 
AND a.id_role=1 
AND $sql_id_peserta
ORDER BY a.kelas, a.nama
";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$komponens['Latihan'] = 25;
$komponens['Tugas'] = 0;
$komponens['Challenge'] = 10;
$komponens['Rank Global'] = 10;
$komponens['Rank Kelas'] = 20;
$komponens['UTS'] = 15;
$komponens['UAS'] = 20;

$tr='';
$td_bobot = '';
$th_komponen = '';
foreach ($komponens as $komponen => $bobot) {
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
while ($d=mysqli_fetch_assoc($q)) {
  $i++;
  $no++;
  if($last_kelas!=$d['kelas'] and $i!=1) $tr.=$tr_empty;
  if($last_kelas!=$d['kelas']) $tr.=$thead;
  if($last_kelas!=$d['kelas']) $no=1;
  $nama = strtoupper($d['nama']);

  $red = $d['jumlah_latihan']==0 ? 'gradasi-merah' : '';
  $red = ($d['jumlah_latihan']>0 && $d['jumlah_latihan']<=3) ? 'gradasi-kuning' : $red;

  $delete = $d['jumlah_latihan']>3 ? '' : "<span class='delete_peserta pointer' id=delete_peserta__$d[id] >$img[delete]</span>";


  if($d['jumlah_latihan']==0){
    $konversi_latihan=0;
  }elseif($d['jumlah_latihan']==1 and $total_latihan==1){
    $konversi_latihan = 100;
  }elseif($d['jumlah_latihan']==$total_latihan){
    $konversi_latihan = 100;
  }else{
    $konversi_latihan = number_format(50 + ($d['jumlah_latihan']-1)*((round($total_latihan*8/10,0)/$total_latihan)*(100/$total_latihan)),0);
    if($konversi_latihan>100) $konversi_latihan=100;
  }

  if($d['jumlah_tugas']==0){
    $konversi_tugas=0;
  }elseif($d['jumlah_tugas']==1 and $total_tugas==1){
    $konversi_tugas = 100;
  }elseif($d['jumlah_tugas']==$total_tugas){
    $konversi_tugas = 100;
  }else{
    $konversi_tugas = number_format(50 + ($d['jumlah_tugas']-1)*((round($total_tugas*8/10,0)/$total_tugas)*(100/$total_tugas)),0);
    if($konversi_tugas>100) $konversi_tugas=100;
  }

  if($d['jumlah_challenge']==0){
    $konversi_challenge=0;
  }elseif($d['jumlah_challenge']==1 and $total_challenge==1){
    $konversi_challenge = 100;
  }elseif($d['jumlah_challenge']==$total_challenge){
    $konversi_challenge = 100;
  }else{
    $konversi_challenge = round(50 + ($d['jumlah_challenge']-1)*((round($total_challenge*8/10,0)/$total_challenge)*(100/$total_challenge)),0);
    if($konversi_challenge>100) $konversi_challenge=100;
  }

  $konversi_rank_global = round(110-(($d['rank_global']-1)*((round($total_peserta*8/10,0)/$total_peserta)*(100/$total_peserta))),0);
  if($konversi_rank_global>100) $konversi_rank_global=100;

  $konversi_rank_kelas = round(110-(($d['rank_kelas']-1)*((round($d['jumlah_peserta_kelas']*8/10,0)/$d['jumlah_peserta_kelas'])*(100/$d['jumlah_peserta_kelas']))),0);
  if($konversi_rank_kelas>100) $konversi_rank_kelas=100;

  $nilai_akhir = round((
    $komponens['Latihan'] * $konversi_latihan + 
    $komponens['Tugas'] * $konversi_tugas + 
    $komponens['Challenge'] * $konversi_challenge + 
    $komponens['Rank Global'] * $konversi_rank_global + 
    $komponens['Rank Kelas'] * $konversi_rank_kelas + 
    $komponens['UTS'] * $d['nilai_uts'] + 
    $komponens['UAS'] * $d['nilai_uas'])/100,0);

  $tr.= "
  <tr class='$red'>
    <td>$no</td>
    <td>$nama<div class='kecil miring abu'>$d[kelas]</div></td>
    <td>$d[jumlah_latihan]<div class='kecil miring abu'>$konversi_latihan</div></td>
    <td>$d[jumlah_tugas]<div class='kecil miring abu'>$konversi_tugas</div></td>
    <td>$d[jumlah_challenge]<div class='kecil miring abu'>$konversi_challenge</div></td>
    <td>$d[rank_global] <span class='kecil miring abu'>of $total_peserta</span><div class='kecil miring abu'>$konversi_rank_global</div></td>
    <td>$d[rank_kelas] <span class='kecil miring abu'>of $d[jumlah_peserta_kelas]</span><div class='kecil miring abu'>$konversi_rank_kelas</div></td>
    <td>$d[nilai_uts]</td>
    <td>$d[nilai_uas]</td>
    <td>$nilai_akhir $delete</td>
  </tr>";

  $last_kelas = $d['kelas'];
}

echo "<table class='table'>$tr</table>";










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
            // zzz
            console.log(a);
          }else{
            alert(a);
          }
        }
      })
    })
  })
</script>