<!-- <link rel='stylesheet' href='assets/css/radio-toolbar.css'> -->
<style>
  .ilustrasi {
    max-width: 100px;
    display: inline-block;
    margin: 15px;
  }

  .label {
    transition: .2s;
    color: #228;
  }

  .label:hover {
    letter-spacing: .4px;
    font-weight: bold;
    color: blue;
  }
</style>
<?php
# ============================================================
# AKTIVASI SISTEM PRESENSI
# ============================================================
$arr = [
  'learning_path' => [
    'caption' => 'Learning Path',
    'image' => 'learning_path.png',
    'desc' => 'Membaca materi yang diberikan oleh instruktur. Materi dapat berupa teks paragraf, modul PDF, rekaman mp3, atau video pembelajaran. Jika instruktur belum memberikan materi maka peserta belum bisa Tanam Soal, Play Quiz, atau aktifitas lainnya. Instruktur wajib: 1. Update nama sesi (Learning Path); 2. Input tag-tag materi. Opsi lain bersifat opsional.',
    'options' => [
      1 => 'Wajib mengakses Learning Path'
    ],
    'selected_option' => 1,
  ],
  'tanam_soal' => [
    'caption' => 'Tanam Soal',
    'image' => 'tanam_soal.png',
    'desc' => 'Untuk membuktikan bahwa peserta sudah mengakses Learning Path maka Peserta wajib membuat Soal PG lengkap dengan opsi dan kunci jawabannya. Dalam kalimat soal atau opsi-opsinya wajib terdapat tag-tag yang ditentukan oleh instruktur pada Learning Path.',
    'options' => [
      0 => 'Tidak perlu membuat soal dari materi',
      1 => 'Minimal membuat 1 Soal PG',
      2 => 'Minimal membuat 2 Soal PG',
      3 => 'Minimal membuat 3 Soal PG',
      4 => 'Minimal membuat 4 Soal PG',
      5 => 'Minimal membuat 5 Soal PG',
    ],
    'selected_option' => 1,
  ],
  'quiz' => [
    'caption' => 'Play Quiz',
    'image' => 'quiz.png',
    'desc' => 'Yaitu menjawab (atau me-reject) soal-soal yang dibuat oleh peserta lain pada satu Room melalui proses Tanam Soal. Default waktu menjawab adalah 30 detik. Baik menjawab dengan benar ataupun salah tetap mendapat poin. Jika ingin me-reject harus memilih salah satu alasan, apakah tidak ada jawaban, soal asal-asalan, atau alasan lainnya.',
    'options' => [
      0 => 'Tidak perlu mengerjakan kuis',
      1 => 'Minimal 1 kali Play Kuis',
      2 => 'Minimal 2 kali Play Kuis',
      3 => 'Minimal 3 kali Play Kuis',
      4 => 'Minimal 4 kali Play Kuis',
      5 => 'Minimal 5 kali Play Kuis',
    ],
    'selected_option' => 1,
  ],
  'latihan' => [
    'caption' => 'Submit Latihan',
    'image' => 'latihan.png',
    'desc' => 'Yaitu upload bukti mengerjakan latihan (jika sudah ada). Instruktur harus membuat latihan, menentukan poin, petunjuk pengerjaan, sifat latihan (opsional atau wajib) dan menentukan waktu pengerjaannya. Secara default peserta menjawab di buku catatan, difoto, kemudian diupload pada latihan tersebut.',
    'options' => [
      0 => 'Tidak perlu mengerjakan latihan',
      1 => 'Wajib mengerjakan latihan yang berstatus wajib saja',
      2 => 'Wajib mengerjakan semua latihan yang ada',
    ],
    'selected_option' => 1,
  ],
  'challenge' => [
    'caption' => 'Beat Challenge',
    'image' => 'challenge.png',
    'desc' => 'Yaitu misi/tugas special dari Instruktur, Praktisi, atau Dunia Industri. Reward sangat besar sesuai dengan Sub Level Challenge tersebut. Reward dapat berupa points, promotions, atau bahkan Rupiah. Goal Challenge bersifat praktis artinya hampir sesuai dengan yang dibutuhkan oleh Dunia Industri. Instruktur bertindak sebagai manager challenge baik yang berasal dari instruktur itu sendiri atau dari Praktisi/Dunia Industri.',
    'options' => [
      0 => 'None - Semua challenge bersifat opsional',
      1 => 'Wajib mengerjakan challenge dari instruktur saja',
      2 => 'Wajib mengerjakan semua challenge yang ada',
    ],
    'selected_option' => 0,
  ],
  'project' => [
    'caption' => 'Build Project',
    'image' => 'project.png',
    'desc' => 'Pembuktian keilmuan peserta dengan cara membuat produk, alat, atau layanan (jasa) yang siap dipasarkan (digunakan) di Dunia Industri. Peserta, Instruktur, dan Pihak Industri harus terlibat dalam pembuatan System Requirements, Progress report, dan Budgeting. Instruktur wajib membuat MoU dan MoA agar Project Goal dapat terwujud dalam waktu sekitar satu semester.',
    'options' => [
      0 => 'None - Build Project bersifat opsional',
      1 => 'Wajib minimal masuk sebagai anggota Tim Build Project',
      2 => 'Wajib presentasi prototyping project',
      3 => 'Wajib implementasi real project yang dibuat',
    ],
    'selected_option' => 0,
  ],
];




$tr = '';
$i = 0;
foreach ($arr as $key => $syarat) {
  $i++;
  $opt = '';
  foreach ($syarat['options'] as $key2 => $opsi_syarat) {
    $selected = $key2 == $syarat['selected_option'] ? 'selected' : '';
    $opt .= "<option value='$key2' $selected>$opsi_syarat</option>";
  }
  $tr .= "
    <tr>
      <td>
        <img src='assets/img/ilustrasi/$syarat[image]' alt='img-$key' class='ilustrasi'>
      </td>
      <td>
        <h4>$syarat[caption]</h4>
        <p>$syarat[desc]</p>
        <div class='mb4'>
          <label class='pointer label'>
            <input required type='checkbox'>
            Saya sudah faham mengenai <u>$syarat[caption]</u>
          </label>
        </div>
      </td>
      <td>
        <div class='abu f14 miring mb2 mt2'>Syarat Presensi #$i </div>
        <select class='form-control' name=syarat_presensi[$key]>
          $opt
        </select>
      </td>
    </tr>
  ";
}

$inputs = "
<div class='wadah gradasi-hijau'>
  <p class='tengah f20 darkblue'>Syarat agar peserta dapat mengisi Presensi Online:</p>
  <hr>
  <table class='table'>
    <thead>
      <th width=20%>&nbsp;</th>
      <th width=40%>Fitur DIPA Joiner</th>
      <th>Syarat Presensi</th>
    </thead>
    $tr
  </table>

</div>
";
?>