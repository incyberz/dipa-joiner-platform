<?php
$single_show = '';
$blok_kelas = '';
$nilai_akhir = 0;
$bobot_penyesuaian = 0;



foreach ($rbobot as $key => $value) {
  $sub_nilai_akhir = round(($rkonversi[$key] * $rbobot[$key]) / 100, 2);
  $nilai_akhir += $sub_nilai_akhir;
  $abu = $rbobot[$key] ? '' : 'abu f10 miring';
  $kolom = str_replace('_', ' ', $key);
  $kolom = str_replace('uts', 'UTS', $kolom);
  $kolom = str_replace('uas', 'UAS', $kolom);

  $sub_nilai_akhir_sty = ($rbobot[$key] and !$sub_nilai_akhir) ? 'red' : 'darkblue';
  $gradasi = $rbobot[$key] ?  gradasi_nilai($rkonversi[$key], $awal_nilai) : 'kuning';
  $count_of = $rbobot[$key] ?  $rvalue_of[$key] : '';
  $hasil_konversi = $rkonversi[$key] === null ? $null  : $rkonversi[$key];

  if ($key == 'nilai_uts' and !$room_count['sudah_uts']) {
  } elseif ($key == 'nilai_uas' and !$room_count['sudah_uas']) {
  } else {
    $bobot_penyesuaian += $rbobot[$key];
  }

  $single_show .= "
    <div class='p2 $abu gradasi-$gradasi'>
      <div class=row>
        <div class='col-md-4 miring darkblue proper'>
          <a href='$rlink[$key]'>$kolom</a>
        </div>
        <div class='col-md-3'>$count_of</div>
        <div class='col-md-3'>
          $hasil_konversi <span class='kecil miring abu'>x $rbobot[$key]%</span>
        </div>
        <div class='col-md-2 kanan $sub_nilai_akhir_sty'>
          $sub_nilai_akhir
        </div>
      </div>
    </div>
  ";
}

$nilai_akhir = $nilai_akhir > 100 ? 100 : $nilai_akhir;

// penyesuaian nilai akhir jika belum UTS | UAS
$nilai_akhir_penyesuaian = round($nilai_akhir * $total_bobot / $bobot_penyesuaian, 2);
if ($room_count['sudah_uas']) {
  $Nilai_Akhir = 'Nilai Akhir';
} elseif ($room_count['sudah_uts']) {
  $Nilai_Akhir = "Nilai Akhir (Pasca-UTS)
    <div class='consolas abu'>$nilai_akhir x $total_bobot / $bobot_penyesuaian = </div>
  ";
  $nilai_akhir = $nilai_akhir_penyesuaian;
} else {
  $Nilai_Akhir = "Nilai Akhir (Tanpa-UTS/UAS)
    <div class='consolas abu'>$nilai_akhir x $total_bobot / $bobot_penyesuaian = </div>
  ";
  $nilai_akhir = $nilai_akhir_penyesuaian;
}

$nilai_akhir = $nilai_akhir > 100 ? 100 : $nilai_akhir;

$blok_kelas .= "
  <div class=wadah>
    <h3 class='darkblue mt3 mb3'>$nama_peserta <div class='miring abu kecil'>$kelas</div></h3>
    $single_show
    <div class='btop p2 gradasi-toska'>
      <div class=row>
        <div class='col-md-10 darkblue'>
          $Nilai_Akhir
        </div>
        <div class='col-md-2 f30 blue kanan'>
          $nilai_akhir
        </div>
      </div>
    </div>
  </div>
";

// auto-save for self
$s = "UPDATE tb_poin SET nilai_akhir=$nilai_akhir WHERE id_peserta=$id_peserta AND id_room=$id_room";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
