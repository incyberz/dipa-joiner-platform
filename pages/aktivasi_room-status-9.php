<style>
  .input_persen {
    width: 70px;
  }
</style>
<?php
# ============================================================
# AKTIVASI BOBOT PENILAIAN
# ============================================================
$arr = [
  'count_presensi_offline' => [
    'default' => 5,
    'desc' => 'Jumlah Presensi Manual dicek oleh instruktur ke tiap peserta',
  ],
  'count_presensi_online' => [
    'default' => 5,
    'desc' => 'Jumlah Presensi Online bagi peserta yang sudah memenuhi seluruh Syarat Presensi',
  ],
  'count_ontime' => [
    'default' => 10,
    'desc' => 'Presensi Online tepat waktu, tidak melebihi batas minggu sesi',
  ],
  'count_latihan' => [
    'default' => 5,
    'desc' => 'Jumlah Submit Latihan (seluruh jenis latihan)',
  ],
  'count_latihan_wajib' => [
    'default' => 10,
    'desc' => 'Jumlah Submit Latihan yang sifatnya wajib dikerjakan',
  ],
  'count_challenge' => [
    'default' => 5,
    'desc' => 'Jumlah Submit Hasil Challenge',
  ],
  'count_challenge_wajib' => [
    'default' => 5,
    'desc' => 'Jumlah Submit Challenge Khusus yang wajib dikerjakan',
  ],
  'rank_room' => [
    'default' => 10,
    'desc' => 'Ranking Peserta berdasarkan Jumlah Peserta Room',
  ],
  'rank_kelas' => [
    'default' => 15,
    'desc' => 'Ranking Peserta berdasarkan Jumlah Peserta Kelas',
  ],
  'nilai_uts' => [
    'default' => 10,
    'desc' => 'Nilai UTS yang berasal dari Fitur Ujian Online atau input secara manual',
  ],
  'nilai_remed_uts' => [
    'default' => 2,
    'desc' => 'Nilai Remed UTS yang berasal dari Fitur Ujian Online',
  ],
  'nilai_uas' => [
    'default' => 15,
    'desc' => 'Nilai UAS yang berasal dari Fitur Ujian Online atau input secara manual',
  ],
  'nilai_remed_uas' => [
    'default' => 3,
    'desc' => 'Nilai Remed UAS yang berasal dari Fitur Ujian Online',
  ],
];

$tr = '';
$total_bobot = 0;
foreach ($arr as $key => $value) {
  $kolom = key2kolom($key);
  $bobot = $value['default'];
  $total_bobot += $bobot;
  $desc = $value['desc'];
  $tr .= "
    <tr id=tr__$key >
      <td width=200px>
        <h4 class='f16 darkblue'>$kolom</h4>
        <p class='f12 abu'>$desc</p>
      </td>
      <td>
        <div class='flexy mb2' style=gap:5px>
          <div>
            <input type=number required min=0 max=50 class='form-control input_persen' name=bobot[$key] id=bobot__$key value=$bobot style='color:darkblue'>
          </div>
          <div class='pt2 abu'>
            %
          </div>
        </div>
        <input type='range' class='form-range range' min='0' max='20' id='range__$key' value='$bobot' step='1'>
        <div class='flexy flex-between f12 abu miring'>
          <div>0</div>
          <div>5</div>
          <div>10</div>
          <div>15</div>
          <div>20</div>
        </div>

      </td>
    </tr>
  ";
}

$inputs = "
  <table class=table>
    $tr
  </table>

  <div  id=blok_total_bobot class='gradasi-toska p2 tengah' style='position:fixed; bottom:0; left:0; width:100vw; height:70px; border-top:solid 1px #ccc; z-index:1000'>
  Total Bobot : <span id=total_bobot class='f30 biru tebal'>$total_bobot</span> <span id=warning_bobot class='red tebal f24'></span>
  </div>
";
?>
<script>
  $(function() {


    $('.range').change(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let field = rid[1];
      let val = $(this).val();

      $('#bobot__' + field).val(val);

      let total_bobot = 0;
      let max_bobot = 0;
      $('.range').each(function() {
        let nilai = parseInt($(this).val());
        total_bobot += nilai;
        if (nilai > max_bobot) max_bobot = nilai;
      });
      $('#total_bobot').text(total_bobot);
      console.log(max_bobot);

      let warning_bobot = '';
      let disabled = 0;
      if (total_bobot < 100 || total_bobot > 105) {
        warning_bobot = ' ~ Perhatian! Total Bobot harus antara 100 s.d 105';
        disabled = 1;
      }
      $('#warning_bobot').text(warning_bobot);
      $('#btn_aktivasi').prop('disabled', disabled);


      // coloring
      // $('.range').each(function() {
      //   let tid = $(this).prop('id');
      //   let rid = tid.split('__');
      //   let aksi = rid[0];
      //   let field = rid[1];
      //   let red = 155 + parseInt($(this).val()) / max_bobot * 100;
      //   $('#tr__' + field).prop('style', `background: linear-gradient(to right, white ,RGB(${red},200,200))`);
      // });

    })
  })
</script>