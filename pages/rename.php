<section>
  <div class="container">

    <?php
    $s = "SELECT folder_uploads FROM tb_peserta WHERE id_role=1";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
    while ($d=mysqli_fetch_assoc($q)) {

      $path = "uploads/$d[folder_uploads]/latihan43.jpg";
      $to = "uploads/$d[folder_uploads]/latihan10.jpg";
      if(file_exists($path)){
        echo "<br>rename $path ... to $to";
        rename($path,$to);
      }else{
        //echo '<br> ... NOT EXIST';
      }
    }

    ?>
  </div>
</section>
