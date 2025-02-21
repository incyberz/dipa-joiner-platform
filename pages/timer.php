<?php
$timer = "
  <i class=hideit id=selisih>$selisih</i>
  <div class='flexy flex-center bordered gradasi-kuning br10 f20 mt2 mb2'>
    <div class='d-flex consolas '>
      <div class='p1' id=timer-h>00</div>
      <div class='p1'>:</div>
      <div class='p1' id=timer-m>00</div>
      <div class='p1'>:</div>
      <div class='p1' id=timer-d>00</div>
    </div>
  </div>
";
?>
<script>
  $(function() {
    let selisih = parseInt($('#selisih').text());
    let d, m, h;

    if (selisih > 0) {
      d = selisih % 60;
      m = parseInt(selisih / 60) % 60;
      h = parseInt(selisih / (60 * 60));
    }

    let timer = setInterval(() => {
      if (d == 0) {
        d = 59;
        if (m == 0) {
          m = 59;
          if (h == 0) {
            location.reload();
          } else {
            h--;
          }
        } else {
          m--;

        }
      } else {
        d--;
      }

      h = h < 10 ? '0' + parseInt(h) : h;
      m = m < 10 ? '0' + parseInt(m) : m;
      d = d < 10 ? '0' + parseInt(d) : d;

      $('#timer-h').text(h);
      $('#timer-m').text(m);
      $('#timer-d').text(d);
    }, 1000);

  })
</script>