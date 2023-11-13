
<section id="login_as" class="login_as">
  <div class="container">

    <div class="section-title" data-aos="fade-up">
      <h2>Login As</h2>
      <!-- <p>This page is ready to code ...</p> -->
    </div>

    <?php
    instruktur_only();
    // if($id_role==1) die(erid('roles'));
    $judul = 'Login As';

    echo '<pre>';
    var_dump($_SESSION);
    echo '</pre>';

    if($id_role==2){
      $new_username = $_GET['username'];
      $_SESSION['dipa_master_username'] = $_SESSION['dipa_username'];
      $_SESSION['dipa_username'] = $new_username;
      echo "<script>alert('Login as $new_username sukses.')</script>";
      echo '<script>location.replace("?")</script>';
      exit;  
    }

    if(isset($_GET['unlog'])){
      $_SESSION['dipa_username'] = $_SESSION['dipa_master_username'];
      unset($_SESSION['dipa_master_username']);

      echo div_alert('success', 'Unlog success.');
      echo '<script>location.replace("?")</script>';
      exit;  
    }

    // if(isset($_SESSION['dipa_master_username'])){
    //   echo "<a href='?login_as&unlog'>Back to Master Username</a>";
    // }





















    ?>    

    <div class="alert alert-success" data-aos="fade-up" aos-delay=150>
      <h4>New Session Started</h4>
      <ul>
        <li>Login as : <?=$new_username?></li>
      </ul>
      <button class='btn btn-primary btn-block' onclick='location.reload()'>Refresh</button>
    </div>


  </div>
</section>