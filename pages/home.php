<?php
if(isset($_SESSION['dipa_username'])){
  echo '<script>location.replace("?dashboard")</script>';
  exit;
}
?>
<section id="about" class="about">
  <div class="container">

    <div class="section-title" data-aos="fade-up">
      <h2>DIPA</h2>
    </div>

    <div class="row content">
      <div class="col-lg-6" data-aos="fade-up" data-aos-delay="150">
        <p>
          Gamified Platform <b>DIPA Joiner</b> memangkas gap antara Dunia Pendidikan dan Dunia Industri secara fun, transparan, dan akurat.
        </p>
        <ul>
          <li><i class="ri-check-double-line"></i> Fun Learning Management System</li>
          <li><i class="ri-check-double-line"></i> Fully transparent in every aspect of Student's Grading</li>
          <li><i class="ri-check-double-line"></i> Very accurate! Evaluation being metered with timestamp of events</li>
          <li><i class="ri-check-double-line"></i> Student, lecturer, professional, and stakeholder can join together</li>
        </ul>
      </div>
      <div class="col-lg-6 pt-4 pt-lg-0" data-aos="fade-up" data-aos-delay="300">
        <p>
          Aplikasi ini dibuat oleh Iin Sholihin, M.Kom, seorang Dosen sekaligus Web Developer. Tahap awal join tiap <code>Mahasiswa</code> diwajibkan menggandeng <code>Dunia Industri</code> untuk project yang ia bangun. Mahasiswa juga wajib mencantumkan mentor dari kalangan <code>Professional</code> (Divisi IT pada Dunia Industri atau perorangan). Dosen bertugas sebagai <code>Instruktur</code>, koordinator, dan sebagai wakil mentor. InsyaAllah dengan adanya aplikasi ini akan mereduksi gap antara Dunia Pendidikan dengan Dunia Industri.
        </p>
        <a href="?pengajar" class="btn-learn-more">Tentang Pengajar</a>
      </div>
    </div>

  </div>
</section>

<?php
include 'home_list_peserta.php';