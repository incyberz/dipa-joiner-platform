<style>
  .blok_rank,
  .blok_nilai_akhir,
  .blok_progres {
    border-top: solid 1px #ccc;
    margin: 0 10px;
    text-align: center
  }

  .blok_progres label {
    font-size: 12px;
    color: gray;
    margin: 10px 0 5px 0;
  }

  .nama_peserta {
    font-size: 24px;
    margin: 0
  }

  .rank_number {
    display: inline-block;
    font-size: 50px;
    color: blue;
    margin-left: 10px;
  }

  .rank_th {
    display: inline-block;
    vertical-align: top;
    color: darkblue;
    padding-top: 10px;
    margin-right: 10px;
  }

  .rank_of {
    color: #666
  }

  .rank_of_count {
    display: inline-block;
    margin: 0 3px;
    font-size: 20px
  }

  .nilai_akhir_hm {
    font-size: 45px;
    color: #55f;
    font-weight: 600
  }

  .nilai_akhir_angka {
    color: #aa5;
  }

  .my_points {
    font-size: 30px;
    color: #55f;
    font-weight: 600
  }
</style>
<?php
function progres($Label, $href, $count, $count_of, $persen_count, $styles = '')
{
  return "
    <a href='?$href'>
      <label>$Label $count of $count_of ($persen_count%)</label>
      <div class='progress'>
        <div class='progress-bar' style='width: $persen_count%; $styles;'></div>
      </div>
    </a>  
  ";
}

$nilai_akhir_show = $nilai_akhir ? $nilai_akhir : '?';

echo '<pre>';
var_dump($room_count);
echo '</pre>';

$progres['presensi'] = progres(
  'Presensi',
  'presensi',
  12,
  $room_count['count_presensi'],
  85
);
$progres['latihan'] = progres(
  'Latihan',
  'activity&jenis=latihan',
  12,
  $room_count['count_latihan'],
  75
);
$progres['challenge'] = progres(
  'Challenge',
  'activity&jenis=challenge',
  12,
  $room_count['count_challenge'],
  65
);
$progres['ujian'] = progres(
  'Ujian',
  'ujian',
  12,
  $room_count['count_ujian'],
  25
);

$blok_progres = "
  <div class='mt2 mb4'>
    $progres[presensi]
    $progres[latihan]
    $progres[challenge]
    $progres[ujian]
  </div>
";
?>

<div class="card mb2">
  <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

    <a href="?upload_profil" onclick='return confirm("Ingin mengupload foto profil?")'><img src="<?= $src_profil ?>" alt="Profile" class="foto_profil"></a>
    <h2 class='nama_peserta'><?= $nama_peserta ?></h2>
    <div><?= $kelas ?></div>
  </div>
  <div class='blok_rank'>
    <span class='darkblue'>Rank</span>
    <span class="rank_number"><?= $rank_kelas ?></span>
    <span class="rank_th"><?= $th ?></span>
    <span class="rank_of">of
      <a href="?peserta_kelas">
        <span class="rank_of_count"><?= $total_peserta_kelas ?></span>
        peserta
      </a>
    </span>
  </div>

  <div class='blok_nilai_akhir'>
    <span class="abu">Nilai Akhir:</span>
    <span class="nilai_akhir_hm"><?= $hm ?></span>
    ~
    <a href="?nilai_akhir">
      <span class="">
        <?= $nilai_akhir_show ?>
      </span>
      <?= $img_next ?>
    </a>
  </div>

  <div class='blok_progres'>
    <?= $blok_progres ?>
  </div>
</div>