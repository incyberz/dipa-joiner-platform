
  <style>
  .text-zoom{cursor: pointer; transition: .2s}
  .text-zoom:hover{letter-spacing: .5px; font-weight: bold}
  .rank_number{display:inline-block; color:blue;}
  .rank_th{display:inline-block; vertical-align:top; color:darkblue; padding-top:10px; margin-right: 10px;}
  
  #blok_summary {max-width: 500px; margin:auto}
  #blok_accuracy {max-width: 360px; margin:auto}
  </style>
<?php
# =================================================================
login_only();
$link3 = "<a href='?perang_soal'>Perang Home</a>";
$link5 = "<a href='?war_leaderboard'>War Leaderboard</a>";
echo "
  <div class='section-title' data-aos-zzz='fade-up'>
    <h2>War Summary</h2>
    <p>
      <div>$link3 | $link5</div>
    </p>
  </div>
";

# =========================================================
# INITIAL VARIABLE
# =========================================================
// move to user_vars

$s = "SELECT 1 FROM tb_war_summary WHERE id_room=$id_room";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_player = mysqli_num_rows($q);






# =========================================================
# COUNT CALCULATION
# =========================================================
$count_answer = $count_answer_right + $count_answer_false;
$count_play_quiz = $count_answer + $count_reject + $count_not_answer;
$my_questions = $my_question_banned + $my_question_unverified + $my_question_verified + $my_question_decided + $my_question_promoted;

$war_points = $war_point_quiz + $war_point_reject + $war_point_passive ;

$accuracy = !$count_play_quiz ? 0 : number_format(100*($count_answer_right+$count_reject)/$count_play_quiz,0);

if($war_rank%10==1){
  $war_th = 'st';
}elseif($war_rank%10==2){
  $war_th = 'nd';
}elseif($war_rank%10==3){
  $war_th = 'rd';
}else{
  $war_th = 'th';
}

$sty_not_answer = $count_not_answer ? 'red' : 'abu';

$sty_banned = $my_question_banned ? 'red' : 'abu';
$sty_unverified = $my_question_unverified ? 'purple' : 'abu';
$sty_verified = $my_question_verified ? 'green' : 'abu';
$sty_decided = $my_question_decided ? 'blue' : 'abu';
$sty_promoted = $my_question_promoted ? 'blue bold' : 'abu';


$update_war_info = '<div class="kecil miring abu">Last update: '.eta(-$selisih_war).', update tiap 1 jam</span>';

if($accuracy>=90){
  $chart_color = '#00ff00'; //green
}elseif($accuracy>=80){
  $chart_color = '#00ffff'; //toska
}elseif($accuracy>=70){
  $chart_color = '#00aaff'; //blue
}elseif($accuracy>=60){
  $chart_color = '#ffff00'; //yellow
}elseif($accuracy>=40){
  $chart_color = '#FF8888'; //light red
}else{
  $chart_color = '#ff0000'; //red
}

$blok_accuracy = "
<div class='' id='blok_accuracy'>
  <div id='accuracyChart'></div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      new ApexCharts(document.querySelector('#accuracyChart'), {
        series: [$accuracy],
        chart: {
          height: 350,
          type: 'radialBar',
          toolbar: {
            show: false,
          },
          
        },
        fill: {
            colors: ['$chart_color']
          },
        plotOptions: {
          radialBar: {
            dataLabels: {
              name: {
                fontSize: '22px',
              },
              value: {
                fontSize: '30px',
              },
              total: {
                show: true,
                label: 'Accuracy',
                formatter: function (w) {
                  return '$accuracy%';
                },
              },
            },
          },
        },
        labels: ['Accuracy'],
      }).render();
    });
  </script>
</div>
<script src='assets/vendor/apexcharts/apexcharts.min.js'></script>
";

$available_questions_10 = $available_questions > 10 ? '10' : $available_questions;
$btn_play_quiz = "<a href='?perang_soal&mode=random' class='btn btn-primary btn-block'>Play $available_questions_10 Quiz !!</a>";
$btn_tanam_soal = "<a href='?tanam_soal' class='btn btn-success btn-block'>Tanam Soal</a>";
$btn_saran = $available_questions ? $btn_play_quiz : $btn_tanam_soal;














echo "
  <div class='wadah gradasi-hijau' id=blok_summary>
    <div class='wadah tengah bg-white'>
      <div class=darkblue>$nama_peserta</div>
      <div>War Rank</div>
      <div class=''><span class='rank_number f50'>$war_rank</span> <span class='rank_th'>$war_th</span></div>
      <div class=abu>of $jumlah_player <span class=f12>players</span></div>

    </div>

    $blok_accuracy


    <div class='tengah darkblue'>War Points Total: 
      <div class='blue f30 mb4'>$war_points <span class='f20'>LP</span></div>
    </div>
    <div class='wadah bg-white'>
      <div class='text-zoom darkblue'><span class='gray kecil miring'>Play Quiz Points:</span> $war_point_quiz LP</div>
      <div class='text-zoom purple'><span class='gray kecil miring'>Reject Points:</span> $war_point_reject LP</div>
      <div class='text-zoom green'><span class='gray kecil miring'>Passive Points:</span> $war_point_passive LP</div>
    </div>
    <div class=tengah>War Counts:</div>
    <div class='wadah bg-white'>
      <div>Play Quiz Count: $count_play_quiz</div>
      <div class='wadah kecil miring'>
        <div class='text-zoom '>Answer Count: $count_answer</div>
        <div class='text-zoom green'>~ Answer Right: $count_answer_right</div>
        <div class='text-zoom darkred'>~ Answer False: $count_answer_false</div>
        <div class='text-zoom purple'>Reject Count: $count_reject</div>
        <div class='text-zoom $sty_not_answer'>Not Answer Count: $count_not_answer</div>
        <hr />
        <div class='kecil miring abu'>Accuracy: $accuracy; diambil dari persentase Menjawab Benar / Menjawab Salah</div>
      </div>
      <div>My Questions: $my_questions</div>
      <div class='wadah kecil miring'>
        <div class='text-zoom $sty_banned'>Banned: $my_question_banned</div>
        <div class='text-zoom $sty_unverified'>Unverified: $my_question_unverified</div>
        <div class='text-zoom $sty_verified'>Verified: $my_question_verified</div>
        <div class='text-zoom $sty_decided'>Decided: $my_question_decided</div>
        <div class='text-zoom $sty_promoted'>Promoted: $my_question_promoted</div>
        <hr />
        <div class='text-zoom'>Poin membuat soal: $poin_membuat_soal</div>
        <div class='text-zoom'>Poin tumbuh soal: $poin_tumbuh_soal</div>
      </div>
      <div class='text-zoom darkblue'>Available Questions: $available_questions</div>
      $btn_saran
    </div>
    $update_war_info
  </div>
";

















?>
<script>
  $(function(){

  })
</script>
