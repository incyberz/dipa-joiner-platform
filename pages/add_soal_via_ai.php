<?php
instruktur_only();
$get_id_paket = $_GET['id_paket'] ?? die(erid('id_paket'));
$count_soal_gpt = 0;

// $null = '<span class="abu f12 miring consolas">null</span>';
// $abjad = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'];
// echo "<span class='hideit' id=id_paket>$get_id_paket</span>";
// $count_assign = 0;
// $id_soals = null;
// $id_soals_assigned = null;

$judul = 'Create Soal via AI';
set_h2($judul, "Membuat Soal 10x lebih cepat! via Teknologi AI", '?manage_paket_soal');














# =============================================
# PAKET SOAL PROPERTIES
# =============================================
$s = "SELECT 
a.id as id_paket,
a.nama as nama_paket,
b.id as id_sesi,
b.nama as nama_sesi,
b.no as no_sesi,
b.jenis as jenis_sesi

FROM tb_paket a 
JOIN tb_sesi b ON a.id_sesi=b.id 
WHERE a.id=$get_id_paket 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) die('Data Paket Soal tidak ditemukan.');
if (mysqli_num_rows($q) > 1) die('Data Paket Soal tidak unik.');
$paket = mysqli_fetch_assoc($q);






















if (isset($_POST['reply_gpt'])) {
  $json = json_decode($_POST['reply_gpt'], true);
  foreach ($json as $key => $v) {
    if (is_array($v)) {
      foreach ($v as $k2 => $v2) {

        foreach ($v2 as $k3 => $v3) {
          $v3 = htmlspecialchars(trim($v3), ENT_QUOTES, 'UTF-8');
          $v3 = mysqli_real_escape_string($cn, $v3);
          $v2[$k3] = $v3;
        }

        $kalimat_soal = $v2['kalimat_soal'] ?? erid('kalimat_soal');
        $opsi_a = $v2['opsi_a'] ?? erid('opsi_a');
        $opsi_b = $v2['opsi_b'] ?? erid('opsi_b');
        $opsi_c = $v2['opsi_c'] ?? erid('opsi_c');
        $opsi_d = $v2['opsi_d'] ?? erid('opsi_d');
        $kunci_jawab = $v2['kunci_jawab'] ?? erid('kunci_jawab');
        if (
          $kalimat_soal
          && $opsi_a
          && $opsi_b
          && $opsi_c
          && $opsi_d
          && $kunci_jawab
        ) {
          $count_soal_gpt++;
          $kunci_jawab = strtolower($kunci_jawab);
          $opsies = "$opsi_a~~~$opsi_b~~~$opsi_c~~~$opsi_d";
          if ($kunci_jawab == 'a') {
            $kjs = $opsi_a;
          } elseif ($kunci_jawab == 'b') {
            $kjs = $opsi_b;
          } elseif ($kunci_jawab == 'c') {
            $kjs = $opsi_c;
          } elseif ($kunci_jawab == 'd') {
            $kjs = $opsi_d;
          } else {
            die("Invalid kunci jawab: $kunci_jawab");
          }
        }

        $s = "INSERT INTO tb_soal (
          id_room,
          id_sesi,
          soal,
          opsies,
          kjs,
          id_pembuat,
          tipe_soal
        ) VALUES (
          $id_room,
          $paket[id_sesi],
          '$kalimat_soal',
          '$opsies',
          '$kjs',
          $id_peserta,
          'PG'
        )";
        $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

        # ============================================================
        # GET ID SOAL BARUSAN
        # ============================================================
        $s = "SELECT id FROM tb_soal WHERE soal = '$kalimat_soal'";
        $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
        $d = mysqli_fetch_assoc($q);
        $id_soal = $d['id'];

        # ============================================================
        # ASSIGN KE PAKET 
        # ============================================================
        $s = "SELECT id FROM tb_assign_soal WHERE id_soal = $id_soal AND id_paket=$get_id_paket";
        $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
        if (mysqli_num_rows($q)) { // telah ada
          // abaikan, ...
        } else {
          # ============================================================
          # INSERT ASSIGN
          # ============================================================
          $s = "INSERT INTO tb_assign_soal (id_paket,id_soal) VALUES ($get_id_paket,$id_soal)";
          $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
        }
      }
    } else {
      echo '<pre>';
      var_dump($v);
      echo '</pre>';
      die('Not an array soal');
    }
  }

  echo div_alert('success', "insert dan assign $count_soal_gpt soal sukses.");
  jsurl("?assign_soal&id_paket=$get_id_paket", 2000);
  exit;
}








































$text_gpt = "code json untuk 10 soal PG tentang: $paket[nama_sesi], mata kuliah: $nama_room, struktur json:{\"soal\":[{\"id\":...,\"kalimat_soal\":\"...\",\"opsi_a\":\"...\",\"opsi_b\":\"...\",\"opsi_c\":\"...\",\"opsi_d\":\"...\",\"kunci_jawab\":\"...\"}, ... ]}";

echo "
  <form method=post class='wadah gradasi-hijau'>
    <div class=mb1><i class=abu>Untuk sesi:</i> P$paket[no_sesi] - $paket[nama_sesi]</div>
    <div class=mb4><i class=abu>Paket Soal:</i> $paket[nama_paket]</div>
    Silahkan copas ke Chatgpt:
    <textarea class='mt1 mb2 form-control'>$text_gpt</textarea>
    Paste dari Chatgpt:
    <textarea class='mt1 mb2 form-control' required name=reply_gpt rows=10></textarea>
    <button class='mt2 btn btn-primary w-100'>Proses GPT</button>
  </form>

";
