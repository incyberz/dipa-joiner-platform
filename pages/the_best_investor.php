<style>
  .text-zoom {
    cursor: pointer;
    transition: .2s
  }

  .text-zoom:hover {
    letter-spacing: .5px;
    font-weight: bold
  }

  .rank_number {
    display: inline-block;
    color: blue;
  }

  .rank_th {
    display: inline-block;
    vertical-align: top;
  }

  #blok_summary {
    max-width: 500px;
    margin: auto
  }

  #blok_accuracy {
    max-width: 360px;
    margin: auto
  }
</style>
<?php
# =================================================================
login_only();
$link3 = "<a href='?perang_soal'>Perang Home</a>";
$link5 = "<a href='?the_best_accuracy'>Best Accuracy</a>";
echo "
  <div class='section-title' data-aos-zzz='fade-up'>
    <h2>The Best Investor</h2>
    <p>
      <div>$link3 | $link5</div>
    </p>
  </div>
";

# =========================================================
# INITIAL VARIABLE
# =========================================================



# =========================================================
# MAIN SELECT
# =========================================================
$s = "SELECT (
  a.my_question_unverified +
  a.my_question_verified +
  a.my_question_decided +
  a.my_question_promoted 
  ) my_questions, 
b.id as id_peserta, 
b.war_image,
b.nama as nama_peserta 
FROM tb_war_summary a 
JOIN tb_peserta b ON a.id_peserta=b.id 
WHERE b.id_role=1 
AND id_room=$id_room 
ORDER BY my_questions DESC LIMIT 10
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$rnama = [];
$rpoints = [];
while ($d = mysqli_fetch_assoc($q)) {
  // if($d['my_questions']==0) continue;

  $img = $id_role == 1 ? '' : "<img src='$lokasi_profil/$d[war_image]' class='profil_pembuat' ><br> ";

  array_push($rnama, $img . $d['nama_peserta']);
  array_push($rpoints, $d['my_questions'] ?? 0);
}

$juara1 = ucwords(strtolower($rnama[0]));
$juara2 = ucwords(strtolower($rnama[1]));
$juara3 = ucwords(strtolower($rnama[2]));

$poin_juara1 = number_format($rpoints[0], 0);
$poin_juara2 = number_format($rpoints[1], 0);
$poin_juara3 = number_format($rpoints[2], 0);

$juara4 = '';
$btop = '';
foreach ($rnama as $key => $nama) {
  if ($key < 3) continue;
  if ($key > 3) $btop = 'btop';
  $juara_ke = $key + 1;
  $points = number_format($rpoints[$key], 0);
  $nama = ucwords(strtolower($nama));
  $juara4 .= "
    <div class='flexy $btop'>
      <div class='mt1 mb1' style='flex: 1'>$juara_ke <span class='rank_th f10'>th</span></div>
      <div class='mt1 mb1' style='flex: 3'>$nama</div>
      <div class='mt1 mb1' style='flex: 2'>$points <span class='kecil miring abu'>Soal</span></div>
    </div>
  ";
}










echo "
  <div class='wadah gradasi-hijau' id=blok_summary>

    <div class='wadah tengah ' style='background: linear-gradient(#ffbbff,#fef)'>
      <img src=assets/img/gif/medal1-1.gif height=90px>
      <div class='darkblue mt1 f20'>$juara1</div>
      <div class=' darkblue '>$poin_juara1 Soal</div>
    </div>


    <div class=row>
      <div class=col-6>
        <div class='wadah tengah bg-white' >
          <img src=assets/img/gif/medal2-1.gif height=70px>
          <div class='darkblue mt1'>$juara2</div>
          <div class='kecil darkblue'>$poin_juara2 Soal</div>
        </div>
      </div>
      <div class=col-6>
        <div class='wadah tengah bg-white'>
          <img src=assets/img/gif/medal3-1.gif height=70px>
          <div class='darkblue mt1'>$juara3</div>
          <div class='kecil darkblue'>$poin_juara3 Soal</div>
        </div>
      </div>
    </div>

    <div class='wadah bg-white' style='background: linear-gradient(#fff,#aff)'>
      $juara4
    </div>


    
  </div>
";

















?>
<script>
  $(function() {

  })
</script>