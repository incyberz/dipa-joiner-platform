<?php 
if(!$id_peserta) jsurl('?login'); 
if(!$id_room) jsurl('?pilih_room'); 
?>

<div class="section-title" data-zzz-aos="fade-up">
  <?php if(!$status_room) echo div_alert('info', meme('closed',6).'<hr>Room ini sudah ditutup.');  ?>
  <h2>Dashboard</h2>
  <p>Welcome 
    <span class='tebal darkblue'><?=$nama_peserta?></span>
    ! Anda login sebagai 
    <span class='tebal darkblue'><?=$sebagai?></span>
    kelas 
    <span class='tebal darkblue'><?=$kelas?> </span>
    pada Room 
    <span class='tebal darkblue'><?=$nama_room?></span>.
  </p>
</div>

<div class="row ">
  <div class="col-xl-4" data-zzz-aos=fade-up data-zzz-aos-delay=150>
    <?php include 'dashboard-rank.php'; ?>
  </div>

  <div class="col-xl-8" data-zzz-aos=fade-up data-zzz-aos-delay=300>
    <?php include 'dashboard-my-points.php'; ?>
  </div>      
</div>

<?php include 'dashboard_room_stats.php'; ?>


