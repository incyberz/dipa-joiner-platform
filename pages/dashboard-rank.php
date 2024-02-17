<?php
$nilai_akhir_show = $nilai_akhir ? $nilai_akhir : '?';
?>
<style>
  .blok_rank,.blok_nilai_akhir{border-top: solid 1px #ccc; margin: 0 10px; text-align:center}
  .nama_peserta{font-size: 24px; margin:0}
  .rank_number{display:inline-block;font-size: 50px; color:blue; margin-left:10px;}
  .rank_th{display:inline-block; vertical-align:top; color:darkblue; padding-top:10px; margin-right: 10px;}
  .rank_of{color:#666}
  .rank_of_count{display: inline-block; margin: 0 3px;font-size: 20px}
  .nilai_akhir_hm{font-size:45px; color:#55f; font-weight: 600}
  .nilai_akhir_angka{color: #aa5;}
  .my_points{font-size:30px; color:#55f; font-weight: 600}
</style>
<div class="card mb2">
  <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

    <a href="?upload_profil" onclick='return confirm("Ingin mengupload foto profil?")'><img src="<?=$path_profil?>" alt="Profile" class="foto_profil"></a>
    <h2 class='nama_peserta'><?=$nama_peserta?></h2>
    <div><?=$kelas?></div>
  </div>
  <div class='blok_rank'>
    <span class='darkblue'>Rank</span> <span class="rank_number"><?=$rank_kelas?></span><span class="rank_th"><?=$th?></span> <span class="rank_of">of <span class="rank_of_count"><?=$total_peserta_kelas?></span> peserta</span>
  </div>
  
  <div class='blok_nilai_akhir'>
    <span class="abu">Nilai Akhir:</span> <span class="nilai_akhir_hm"><?=$hm?></span> <span class="nilai_akhir_angka">(<?=$nilai_akhir_show?>)</span> <a href="?nilai_akhir"><i class="bi bi-arrow-right-circle-fill "></i></a>
  </div>

</div>
