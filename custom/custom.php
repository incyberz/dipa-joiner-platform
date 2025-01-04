<?php
# ============================================================
# META CUSTOM
# ============================================================
$is_custom = true;
$meta_title = "LMS FKOM - Masoem University";
$meta_description = "Fun e-Learning Management System (LMS) berbasis Game Mechanics (Gamification) bagi Mitra (Dunia Industri), Praktisi, dan Akademisi. Dengan Rank System, Leaderboard, Play Quiz, dan Tanam Soal, menjadikan Pembelajaran seindah permainan.";
$meta_keywords = "learning management system, fun lms, gamification, game mechanic, rank, leaderboard, quiz, bank soal, pembelajaran jarak jauh";

# ============================================================
# CUSTOM GLOBAL VARIABLE 
# ============================================================
$Institusi = 'Universitas Masoem';
$Nama_LMS = 'LMS FKOM';
$Room = 'Course'; // Room | Course | Mapel
$Trainer = 'Dosen'; // Instruktur | Trainer | Dosen | Guru
$Peserta = 'Mhs'; // Peserta | Mhs | Siswa
$Praktisi = 'Praktisi'; // Praktisi | Professional
$Mitra = 'Mitra'; // Mitra | DUDI | Industri
$Join = 'Daftar'; // Join | Daftar | Register | Admisi
$Slogan = 'Belajar Tanpa Batas, Belajar Kapan saja, dimana saja.';
$Leaderboard = 'Peringkat';

# ============================================================
# KONTAK OPERATOR LMS
# ============================================================
$ops = [
  'nama' => 'Iin Sholihin',
  'username' => 'abi',
  'whatsapp' => '6287729007318',
  'email' => 'isholihin87@gmail.com',
];


# ============================================================
# DEPENDED VARIABLE
# ============================================================
$custom_arr_peran = [
  'peserta' => "Saya $Peserta $Institusi",
  'instruktur' => "Saya $Trainer $Institusi",
  'praktisi' => "Peran saya sebagai $Praktisi",
  'mitra' => "Peran saya sebagai $Mitra",
];

$custom = [];
$custom['instruktur'] = $Trainer;
