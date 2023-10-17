<style>
  .blok_rank,.blok_nilai_akhir{border-top: solid 1px #ccc; margin: 0 10px; text-align:center}
  .nama_peserta{font-size: 24px; margin:0}
  .rank_number{display:inline-block;font-size: 50px; color:blue; margin-left:10px;}
  .rank_th{display:inline-block; vertical-align:top; color:darkblue; padding-top:10px; margin-right: 10px;}
  .rank_of{color:#666}
  .rank_of_count{display: inline-block; margin: 0 3px;font-size: 20px}
  .nilai_akhir_hm{font-size:45px; color:#55f; font-weight: 600}
  .nilai_akhir_angka{color: #aa5;}
  

    
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

$pekerjaan = $pekerjaan=='' ? "$sebagai" : $pekerjaan;

$src = "assets/img/peserta/peserta-$id_peserta.jpg";
if(!file_exists($src)){
  $src_profil = 'assets/img/no_profile.jpg';
}else{
  $src_profil = $src;
}



?>
<section id="dashboard" class="dashboard">
  <div class="container">

    <div class="section-title" data-aos="fade-up">
      <h2>Dashboard</h2>
      <p>Welcome <span class='tebal darkblue'><?=$nama_peserta?></span>! Anda login sebagai <span class='tebal darkblue'><?=$sebagai?></span>.</p>
    </div>

    <div class="row profile">
      <div class="col-xl-4" data-aos=fade-up data-aos-delay=150>

        <div class="card mb2">
          <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

            <a href="?upload_profil" onclick='return confirm("Ingin mengupload foto profil?")'><img src="<?=$src_profil?>" alt="Profile" class="foto_profil"></a>
            <h2 class='nama_peserta'><?=$nama_peserta?></h2>
          </div>
          <div class='blok_rank'>
            <span class='darkblue'>Rank</span> <span class="rank_number"><?=$rank?></span><span class="rank_th"><?=$th?></span> <span class="rank_of">of <span class="rank_of_count"><?=$jumlah_peserta?></span> peserta</span>
          </div>
          
          <div class='blok_nilai_akhir'>
            <span class="abu">Nilai Akhir:</span> <span class="nilai_akhir_hm">B</span> <span class="nilai_akhir_angka">(80)</span> <a href="?nilai_saya"><i class="bi bi-arrow-right-circle-fill "></i></a>
          </div>

        </div>

      </div>

      <div class="col-xl-8" data-aos=fade-up data-aos-delay=300>

        <div class="card">
          <div class="card-body pt-3">
            <!-- Bordered Tabs -->
            <ul class="nav nav-tabs nav-tabs-bordered" role="tablist">

              <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview" aria-selected="true" role="tab">Overview</button>
              </li>

              <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit" aria-selected="false" tabindex="-1" role="tab">Edit Profile</button>
              </li>

              <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings" aria-selected="false" tabindex="-1" role="tab">Settings</button>
              </li>

              <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password" aria-selected="false" tabindex="-1" role="tab">Change Password</button>
              </li>

            </ul>
            <div class="tab-content pt-2">

              <div class="tab-pane fade show active profile-overview" id="profile-overview" role="tabpanel">
                <h5 class="card-title">About</h5>
                <p class="small fst-italic">Sunt est soluta temporibus accusantium neque nam maiores cumque temporibus. Tempora libero non est unde veniam est qui dolor. Ut sunt iure rerum quae quisquam autem eveniet perspiciatis odit. Fuga sequi sed ea saepe at unde.</p>

                <h5 class="card-title">Profile Details</h5>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label ">Full Name</div>
                  <div class="col-lg-9 col-md-8">Kevin Anderson</div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label">Company</div>
                  <div class="col-lg-9 col-md-8">Lueilwitz, Wisoky and Leuschke</div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label">Job</div>
                  <div class="col-lg-9 col-md-8">Web Designer</div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label">Country</div>
                  <div class="col-lg-9 col-md-8">USA</div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label">Address</div>
                  <div class="col-lg-9 col-md-8">A108 Adam Street, New York, NY 535022</div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label">Phone</div>
                  <div class="col-lg-9 col-md-8">(436) 486-3538 x29071</div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label">Email</div>
                  <div class="col-lg-9 col-md-8">k.anderson@example.com</div>
                </div>

              </div>

              <div class="tab-pane fade profile-edit pt-3" id="profile-edit" role="tabpanel">

                <!-- Profile Edit Form -->
                <form>
                  <div class="row mb-3">
                    <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                    <div class="col-md-8 col-lg-9">
                      <img src="assets/img/profile-img.jpg" alt="Profile">
                      <div class="pt-2">
                        <a href="#" class="btn btn-primary btn-sm" title="Upload new profile image"><i class="bi bi-upload"></i></a>
                        <a href="#" class="btn btn-danger btn-sm" title="Remove my profile image"><i class="bi bi-trash"></i></a>
                      </div>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="fullName" type="text" class="form-control" id="fullName" value="Kevin Anderson">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="about" class="col-md-4 col-lg-3 col-form-label">About</label>
                    <div class="col-md-8 col-lg-9">
                      <textarea name="about" class="form-control" id="about" style="height: 100px">Sunt est soluta temporibus accusantium neque nam maiores cumque temporibus. Tempora libero non est unde veniam est qui dolor. Ut sunt iure rerum quae quisquam autem eveniet perspiciatis odit. Fuga sequi sed ea saepe at unde.</textarea>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="company" class="col-md-4 col-lg-3 col-form-label">Company</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="company" type="text" class="form-control" id="company" value="Lueilwitz, Wisoky and Leuschke">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Job" class="col-md-4 col-lg-3 col-form-label">Job</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="job" type="text" class="form-control" id="Job" value="Web Designer">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Country" class="col-md-4 col-lg-3 col-form-label">Country</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="country" type="text" class="form-control" id="Country" value="USA">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Address" class="col-md-4 col-lg-3 col-form-label">Address</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="address" type="text" class="form-control" id="Address" value="A108 Adam Street, New York, NY 535022">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Phone" class="col-md-4 col-lg-3 col-form-label">Phone</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="phone" type="text" class="form-control" id="Phone" value="(436) 486-3538 x29071">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="email" type="email" class="form-control" id="Email" value="k.anderson@example.com">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Twitter" class="col-md-4 col-lg-3 col-form-label">Twitter Profile</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="twitter" type="text" class="form-control" id="Twitter" value="https://twitter.com/#">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Facebook" class="col-md-4 col-lg-3 col-form-label">Facebook Profile</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="facebook" type="text" class="form-control" id="Facebook" value="https://facebook.com/#">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Instagram" class="col-md-4 col-lg-3 col-form-label">Instagram Profile</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="instagram" type="text" class="form-control" id="Instagram" value="https://instagram.com/#">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Linkedin" class="col-md-4 col-lg-3 col-form-label">Linkedin Profile</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="linkedin" type="text" class="form-control" id="Linkedin" value="https://linkedin.com/#">
                    </div>
                  </div>

                  <div class="text-center">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                  </div>
                </form><!-- End Profile Edit Form -->

              </div>

              <div class="tab-pane fade pt-3" id="profile-settings" role="tabpanel">

                <!-- Settings Form -->
                <form>

                  <div class="row mb-3">
                    <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Email Notifications</label>
                    <div class="col-md-8 col-lg-9">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="changesMade" checked="">
                        <label class="form-check-label" for="changesMade">
                          Changes made to your account
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="newProducts" checked="">
                        <label class="form-check-label" for="newProducts">
                          Information on new products and services
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="proOffers">
                        <label class="form-check-label" for="proOffers">
                          Marketing and promo offers
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="securityNotify" checked="" disabled="">
                        <label class="form-check-label" for="securityNotify">
                          Security alerts
                        </label>
                      </div>
                    </div>
                  </div>

                  <div class="text-center">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                  </div>
                </form><!-- End settings Form -->

              </div>

              <div class="tab-pane fade pt-3" id="profile-change-password" role="tabpanel">
                <!-- Change Password Form -->
                <form>

                  <div class="row mb-3">
                    <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="password" type="password" class="form-control" id="currentPassword">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="newpassword" type="password" class="form-control" id="newPassword">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="renewpassword" type="password" class="form-control" id="renewPassword">
                    </div>
                  </div>

                  <div class="text-center">
                    <button type="submit" class="btn btn-primary">Change Password</button>
                  </div>
                </form><!-- End Change Password Form -->

              </div>

            </div><!-- End Bordered Tabs -->

          </div>
        </div>

      </div>      
    </div>
    

  </div>
</section>

