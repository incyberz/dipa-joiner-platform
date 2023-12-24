
<div class="card mb2">
  <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

    <a href="?upload_profil" onclick='return confirm("Ingin mengupload foto profil?")'><img src="<?=$src_profil?>" alt="Profile" class="foto_profil"></a>
    <h2 class='nama_peserta'><?=$nama_peserta?></h2>
    <div><?=$kelas?></div>
  </div>
  <div class='blok_rank'>
    <span class='darkblue'>Rank</span> <span class="rank_number"><?=$rank_kelas?></span><span class="rank_th"><?=$th?></span> <span class="rank_of">of <span class="rank_of_count"><?=$total_kelas_ini?></span> peserta</span>
  </div>
  
  <div class='blok_nilai_akhir'>
    <span class="abu">Nilai Akhir:</span> <span class="nilai_akhir_hm"><?=$hm?></span> <span class="nilai_akhir_angka">(<?=$nilai_akhir?>)</span> <a href="?nilai_akhir"><i class="bi bi-arrow-right-circle-fill "></i></a>
  </div>

</div>
