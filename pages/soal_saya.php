<?php
# =================================================================
login_only();
$abjad = ['a', 'b', 'c', 'd'];
$kj = '';
$status_soal = '<span class="darkred">unverified</span>';

$link = "<a href='?tanam_soal'>Tanam Soal</a>";
$link2 = "<a href='?perang_soal'>Perang Soal</a>";
echo "
  <div class='section-title' data-aos-zzz='fade-up'>
    <h2>Soal Saya</h2>
    <p>
      <div>$link | $link2</div>
      <div class='kecil abu'>Kamu akan mendapat Passive Point jika temanmu menjawab soal milikmu</div> 
    </p>
  </div>
";

$s = "SELECT a.*,
(SELECT SUM(poin_pembuat) FROM tb_war WHERE id_soal_peserta=a.id AND id_pembuat=$id_peserta) earned_points, 
(SELECT status FROM tb_status_soal WHERE id=a.id_status) status_soal 
FROM tb_soal_peserta a 
JOIN tb_sesi b ON a.id_sesi=b.id 
WHERE id_pembuat=$id_peserta AND b.id_room=$id_room";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$r = rand(1, 9);
$div = div_alert('danger tengah', "<img class=meme src='assets/img/meme/dont-have-$r.jpg'><div class=mt2>Kamu belum punya Soal PG di room ini.</div> <hr> $link");
if (mysqli_num_rows($q)) {
  $div = '';
  $i = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $id = $d['id'];
    $earned_points = $d['earned_points'] ?? 0;
    $tanggal = date('D, M d, Y ~ H:i:s', strtotime($d['tanggal']));

    $ropsies = explode('~~~', $d['opsies']);
    $opsies = '';
    foreach ($ropsies as $key => $value) {
      if ($value == $d['jawaban']) $kj = strtoupper($abjad[$key]);
      $biru = $value == $d['jawaban'] ? 'biru' : '';
      $opsies .= "<div class='$biru'>$abjad[$key]. $value</div>";
    }

    $status_soal = $d['status_soal'] == '' ? $status_soal : "<span class='hijau tebal'>$d[status_soal]</span>";

    $div .= "
      <div class='kecil miring abu'>$i</div>
      <div class=biru>$d[kalimat_soal]</div>
      <div class='ml2'>
        <div class='mt1 mb2 '>$opsies</div>
        <div class='hijau hideit'>KJ: $kj</div>
        <div class='kecil miring'>Status soal: $status_soal</div>
        <div class='kecil miring abu'>Created at $tanggal</div>
        <div class='kecil biru'>Earned Points : $earned_points LP</div>

      </div>
      <hr>
    ";
  }
}

echo $div;
