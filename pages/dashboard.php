<style>
  .blok_rank,.blok_nilai_akhir{border-top: solid 1px #ccc; margin: 0 10px; text-align:center}
  .nama_peserta{font-size: 24px; margin:0}
  .rank_number{display:inline-block;font-size: 50px; color:blue; margin-left:10px;}
  .rank_th{display:inline-block; vertical-align:top; color:darkblue; padding-top:10px; margin-right: 10px;}
  .rank_of{color:#666}
  .rank_of_count{display: inline-block; margin: 0 3px;font-size: 20px}
  .nilai_akhir_hm{font-size:45px; color:#55f; font-weight: 600}
  .nilai_akhir_angka{color: #aa5;}
  .my_points{font-size:30px; color:#55f; font-weight: 600}
  

    
  /*--------------------------------------------------------------
  # Profie Page
  --------------------------------------------------------------*/
  .profile .profile-card img {
    max-width: 120px;
  }

  .profile .profile-card h2 {
    font-size: 24px;
    font-weight: 700;
    color: #2c384e;
    margin: 10px 0 0 0;
  }

  .profile .profile-card h3 {
    font-size: 18px;
  }

  .profile .profile-card .social-links a {
    font-size: 20px;
    display: inline-block;
    color: rgba(1, 41, 112, 0.5);
    line-height: 0;
    margin-right: 10px;
    transition: 0.3s;
  }

  .profile .profile-card .social-links a:hover {
    color: #012970;
  }

  .profile .profile-overview .row {
    margin-bottom: 20px;
    font-size: 15px;
  }

  .profile .profile-overview .card-title {
    color: #012970;
  }

  .profile .profile-overview .label {
    font-weight: 600;
    color: rgba(1, 41, 112, 0.6);
  }

  .profile .profile-edit label {
    font-weight: 600;
    color: rgba(1, 41, 112, 0.6);
  }

  .profile .profile-edit img {
    max-width: 120px;
  }  
</style>
<?php
if(!isset($_SESSION['dipa_username'])){
  echo '<script>location.replace("?")</script>';
  exit;
}

$pekerjaan = (!isset($pekerjaan) || $pekerjaan=='') ? "$sebagai" : $pekerjaan;

$src = "assets/img/peserta/peserta-$id_peserta.jpg";
if(!file_exists($src)){
  $src_profil = 'assets/img/no_profil.jpg';
}else{
  $src_profil = $src;
}



?>
<section id="dashboard" class="dashboard">
  <div class="container">

    <div class="section-title" data-aos="fade-up">
      <?php if(!$status_room) echo div_alert('info', meme('closed',6).'<hr>Room ini sudah ditutup.');  ?>
      <h2>Dashboard</h2>
      <p>Welcome <span class='tebal darkblue'><?=$nama_peserta?></span>! Anda login sebagai <span class='tebal darkblue'><?=$sebagai?></span>.</p>
    </div>

    <div class="row profile">
      <div class="col-xl-4" data-aos=fade-up data-aos-delay=150>
        <?php include 'dashboard-rank.php'; ?>
      </div>

      <div class="col-xl-8" data-aos=fade-up data-aos-delay=300>

        <div class="card">
          <div class="card-body pt-3">
            <!-- Bordered Tabs -->
            <ul class="nav nav-tabs nav-tabs-bordered" role="tablist">

              <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-points" aria-selected="true" role="tab">Points</button>
              </li>

              <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-overview" aria-selected="false" tabindex='-1' role="tab">Profile</button>
              </li>

              <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit" aria-selected="false" tabindex="-1" role="tab">Edit Profile</button>
              </li>

              <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings" aria-selected="false" tabindex="-1" role="tab">Settings</button>
              </li>

            </ul>
            <div class="tab-content pt-2">

              <?php include 'dashboard-my-points.php'; ?>
              <?php include 'dashboard-about.php'; ?>
              <?php include 'dashboard-edit-profile.php'; ?>
              <?php include 'dashboard-settings.php'; ?>

            </div><!-- End Bordered Tabs -->

          </div>
        </div>

      </div>      
    </div>
    

  </div>
</section>

