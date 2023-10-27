<?php
$id_mode=$_GET['id_mode'] ?? 2; //UTS
$id_jenis_soal=$_GET['id_jenis_soal'] ?? 2; //TF

# =======================================================
# MODE UJIAN
# =======================================================
# 1. Ujian Harian
# 2. Ujian Tengah Semester
# 3. Ujian Akhir Semester
# 4. Ujian Praktikum
$mode[1] = 'Ujian Harian';
$mode[2] = 'Ujian Tengah Semester';
$mode[3] = 'Ujian Akhir Semester';
$mode[4] = 'Ujian Praktikum Harian';
$mode[5] = 'Proyek Tugas Akhir';


# =======================================================
# JENIS SOAL
# =======================================================
# 1. PG
# 2. TF
# 3. MC : Multiple Check
# 4. ISIAN
$jenis_soal[1] = ['PG','Pilihan Ganda'];
$jenis_soal[2] = ['TF','True False'];
$jenis_soal[3] = ['MC','Multi Check'];
$jenis_soal[4] = ['IS','Isian Singkat'];
$jenis_soal[5] = ['UR','Uraian'];







?>
<section>
  <div class="container">

    <div class="section-title" data-aos="fade-up">
      <h2>Ujian</h2>
      <p><?=$mode[$id_mode]?></p>
    </div>

    <div class="wadah gradasi-hijau tengah" data-aos='fade-up' data-aos-delay='200'>
      <h1>Comming soon !!</h1>
      Ujian akan diselenggarakan dalam:
      <div> 
        <div class=consolas style="font-size:50px">00:00:00</div>
        detik lagi
      </div>
    </div>
  </div>
</section>


<script>
  $(function(){
    
  })
</script>