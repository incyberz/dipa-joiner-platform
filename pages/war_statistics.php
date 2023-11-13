<section id="about" class="about"><div class="container">
  <style>
  .text-zoom{cursor: pointer; transition: .2s}
  .text-zoom:hover{letter-spacing: .5px; font-weight: bold}
  .rank_number{display:inline-block; color:blue;}
  .rank_th{display:inline-block; vertical-align:top;}
  
  #blok_summary {max-width: 500px; margin:auto}
  #blok_accuracy {max-width: 360px; margin:auto}
  </style>
<?php
# =================================================================
login_only();
$link3 = "<a href='?perang_soal'>Perang Home</a>";
$link5 = "<a href='?tanam_soal'>Tanam Soal</a>";
echo "
  <div class='section-title' data-aos-zzz='fade-up'>
    <h2>War Statistics</h2>
    <p>
      <div>$link3 | $link5</div>
      
    </p>
  </div>
";

# =========================================================
# INITIAL VARIABLE
# =========================================================
$active_players = '123';
$new_questions = '123';
$war_counts = '123';
$now_show = '';
$ahad_skg_show = '';
$ahad_depan_show = '';







echo "
  <div class='wadah gradasi-hijau' id=blok_summary>
    <div class='tengah abu mb1 kecil'>Statistic Hari ini | $now_show</div>
    <div class='wadah bg-white' id=blok_summary>
      <div class='flexy  pt1 pb1'>
        <div style='flex:2' class='kecil abugelap'>Active Players:</div>
        <div style='flex:3'> $active_players <span class='kecil abugelap'>players</span></div>
      </div>
      <div class='flexy btop pt1 pb1'>
        <div style='flex:2' class='kecil abugelap'>New Questions:</div>
        <div style='flex:3'> $new_questions <span class='kecil abugelap'>questions</span></div>
      </div>
      <div class='flexy btop pt1 pb1'>
        <div style='flex:2' class='kecil abugelap'>War Counts:</div>
        <div style='flex:3'> $war_counts <span class='kecil abugelap'>answers</span></div>
      </div>
    </div>

    <div class='tengah abu mb1 kecil mt4'>Minggu ini | $ahad_skg_show s.d $ahad_depan_show</div>
    <div class='wadah bg-white' id=blok_summary>
      <div class='flexy  pt1 pb1'>
        <div style='flex:2' class='kecil abugelap'>Active Players:</div>
        <div style='flex:3'> $active_players <span class='kecil abugelap'>players</span></div>
      </div>
      <div class='flexy btop pt1 pb1'>
        <div style='flex:2' class='kecil abugelap'>New Questions:</div>
        <div style='flex:3'> $new_questions <span class='kecil abugelap'>questions</span></div>
      </div>
      <div class='flexy btop pt1 pb1'>
        <div style='flex:2' class='kecil abugelap'>War Counts:</div>
        <div style='flex:3'> $war_counts <span class='kecil abugelap'>answers</span></div>
      </div>
    </div> 
  </div>
";

















?></div></section>
<script>
  $(function(){

  })
</script>
