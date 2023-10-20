<?php
$jumlah_verif = 0;
$rjenis = ['latihan','tugas','challenge'];
foreach ($rjenis as $key => $jenis) {
  $s = "SELECT 1 FROM tb_bukti_$jenis WHERE tanggal_verifikasi is null"; 
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $jumlah_verif += mysqli_num_rows($q);
}

$jumlah_ask = 0;
$s = "SELECT 1 FROM tb_pertanyaan WHERE verif_status is null"; 
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_ask = mysqli_num_rows($q);

?>
<header id="header" class="fixed-top d-flex align-items-center">
  <div class="container d-flex align-items-center justify-content-between">

    <div class="logo">
      <img src="assets/img/dipa-logo.png" alt="dipa-logo" class="img-fluid">
      <!-- <h1>DIPA</h1> -->
      <!-- Uncomment below if you prefer to use an image logo -->
      <!-- <a href="index.html"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->
    </div>

    <nav id="navbar" class="navbar">
      <ul>
        <li><a class="nav-link scrollto active" href="?">Home</a></li>
        <li><a class="nav-link scrollto" href="?pengajar">Pengajar</a></li>
        <li><a class="nav-link scrollto" href="?teams">Teams</a></li>
        <!-- <li><a class="nav-link scrollto" href="?peserta">Peserta</a></li> -->
        <li><a class="nav-link scrollto" href="?grades">Grades</a></li>
        <!-- <li><a class="nav-link scrollto" href="#services">Services</a></li>
        <li><a class="nav-link scrollto " href="#portfolio">Portfolio</a></li>
        <li><a class="nav-link scrollto" href="#team">Team</a></li>
        <li><a class="nav-link scrollto" href="#pricing">Pricing</a></li>
        <li class="dropdown"><a href="#"><span>Drop Down</span> <i class="bi bi-chevron-down"></i></a>
          <ul>
            <li><a href="#">Drop Down 1</a></li>
            <li class="dropdown"><a href="#"><span>Deep Drop Down</span> <i class="bi bi-chevron-right"></i></a>
              <ul>
                <li><a href="#">Deep Drop Down 1</a></li>
                <li><a href="#">Deep Drop Down 2</a></li>
                <li><a href="#">Deep Drop Down 3</a></li>
                <li><a href="#">Deep Drop Down 4</a></li>
                <li><a href="#">Deep Drop Down 5</a></li>
              </ul>
            </li>
            <li><a href="#">Drop Down 2</a></li>
            <li><a href="#">Drop Down 3</a></li>
            <li><a href="#">Drop Down 4</a></li>
          </ul>
        </li>
        <li><a class="nav-link scrollto" href="#contact">Contact</a></li> -->
        <?php
        $nickname_show = isset($_SESSION['dipa_master_username']) ? "Login As $username | $my_points LP" : "$username | $my_points LP";        
        $unlog_link = isset($_SESSION['dipa_master_username']) ? "<li><a href='?login_as&unlog'>Unlog</a></li>" : '';
        if($is_login){
          $li_verif = ($id_role<=1 || $jumlah_verif==0) ? '' : "<li><a href='?verif' class='proper'><span class='biru tebal'>Verif</span> <span class='count_badge badge_red' id='jumlah_verif'>$jumlah_verif</span></a></li>";
          $li_ask = ($id_role<=1 || $jumlah_ask==0) ? '' : "<li><a href='?chats' class='proper'><span class='biru tebal'>Chats</span> <span class='count_badge badge_red' id='jumlah_ask'>$jumlah_ask</span></a></li>";
          echo "
          $li_verif $li_ask
            <li class='dropdown'><a  href='#'><span class='tebal darkblue'>Belajar</span> <i class='bi bi-chevron-down'></i></a>
              <ul>
                <li><a href='?list_sesi'>List Sesi</a></li>
                <li><a href='?activity&jenis=latihan'>Latihan Praktikum</a></li>
                <li><a href='?activity&jenis=tugas'>Tugas Proyek</a></li>
                <li><a href='?activity&jenis=challenge'>Challenges</a></li>
                <li><a href='?bertanya'>Bertanya</a></li>
                <li><a href='?my_questions'>Pertanyaan Saya</a></li>
                <li><a href='?chats'>Chats</a></li>
                <li class=hideit><a href='?quiz'>Kuis PG</a></li>
              </ul>
            <li class='dropdown'><a class=getstarted href='#'><span>$nickname_show</span> <i class='bi bi-chevron-down'></i></a>
              <ul>
                <li><a href='?get_point'>Dapatkan Poin</a></li>
                <li><a href='?my_points'>My Points</a></li>
                <li><a href='?upload_profil'>My Profile</a></li>
                <li class='hideit'><a href='?my_testimony'>My Testimony</a></li>
                <li class=hideit><a href='?my_biodata'>My Biodata</a></li>
                <li><a href='?ubah_password'>Ubah Password</a></li>
                <li><a href='?logout' onclick='return confirm(\"Yakin untuk Logout?\")' class=red>Logout</a></li>
              </ul>
            </li>
            $unlog_link
          ";

        }else{
          echo "<li><a class='getstarted scrollto' href='?login'>Login</a></li>";
        }
        ?>
      </ul>
      <i class="bi bi-list mobile-nav-toggle"></i>
    </nav><!-- .navbar -->

  </div>
</header>
