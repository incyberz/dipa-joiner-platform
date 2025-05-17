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

$progres['presensi'] = progres(
  'Presensi',
  'presensi',
  $my_poin['count_presensi'],
  $room_count['count_presensi_aktif'],
  $room_count['count_presensi_aktif'] ? round($my_poin['count_presensi'] * 100 / $room_count['count_presensi_aktif']) : 0

);
$progres['latihan'] = progres(
  'Latihan',
  'activity&jenis=latihan',
  $my_poin['count_latihan_verified'],
  $room_count['count_latihan'],
  $room_count['count_latihan'] ? round($my_poin['count_latihan_verified'] * 100 / $room_count['count_latihan']) : 0
);
$progres['challenge'] = progres(
  'Challenge',
  'activity&jenis=challenge',
  $my_poin['count_challenge_verified'],
  $room_count['count_challenge'],
  $room_count['count_challenge'] ? round($my_poin['count_challenge_verified'] * 100 / $room_count['count_challenge']) : 0
);
$progres['ujian'] = progres(
  'Ujian',
  'ujian',
  $my_poin['count_ujian'],
  $room_count['count_ujian'],
  $room_count['count_ujian'] ? round($my_poin['count_ujian'] * 100 / $room_count['count_ujian']) : 0
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
    <div><?= $username ?></div>
  </div>
  <div class='blok_rank'>
    <span class='darkblue'>Rank</span>
    <span class="rank_number"><?= $rank_kelas ?></span>
    <span class="rank_th"><?= $th ?></span>
    <span class="rank_of">of
      <a href="?peserta_kelas">
        <span class="rank_of_count"><?= $total_peserta_kelas ?></span>
        <?= $Peserta ?>
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

  <div class='blok_progres hideit ZZZ'>
    <?= $blok_progres ?>
  </div>
</div>