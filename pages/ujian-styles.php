<style>
  .div_soal {
    border-top: solid 2px #ddd;
    padding: 10px;
  }

  .no_dan_soal {
    display: grid;
    grid-template-columns: 20px auto;
    grid-gap: 5px
  }

  .blok-ujian-countdown {
    position: fixed;
    bottom: 10px;
    padding: 5px 10px;
    left: 10px;
    border-radius: 10px;
    border: solid 2px darkblue;
    box-shadow: 0 0 8px gray;
    z-index: 999;
    background: white;
    font: 30px consolas;
    color: darkred;
  }

  #sekian_soal_lagi {
    font-size: 12px;
  }

  #belum_dijawab_count2 {
    font-size: 20px;
  }
</style>
<?php
if ($dark) {
?>
  <style>
    .belum_dijawab {
      background: linear-gradient(#533, #511)
    }
  </style>
<?php
} else {
?>
  <style>
    .belum_dijawab {
      background: linear-gradient(#fee, #fcc)
    }
  </style>
<?php
}
