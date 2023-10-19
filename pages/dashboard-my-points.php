<div class="tab-pane fade show active profile-overview pt-3" id="profile-points" role="tabpanel">
  <h5 class="card-title">My Points</h5>
  <div class='my_points'><?=$my_points?> LP</div>
  <p class="small fst-italic">Poin didapatkan dari aktifitas latihan, tugas, challenge, bertanya, menjawab (chats), dan aktifitas pembelajaran lainnya.</p>

  <h5 class="card-title">Point Details</h5>

  <div class="row">
    <div class="col-lg-3 col-md-4 label ">Poin Latihan</div>
    <div class="col-lg-9 col-md-8"><?=$d_peserta['poin_latihan']?> LP</div>
  </div>

  <div class="row">
    <div class="col-lg-3 col-md-4 label ">Poin tugas</div>
    <div class="col-lg-9 col-md-8"><?=$d_peserta['poin_tugas']?> LP</div>
  </div>

  <div class="row">
    <div class="col-lg-3 col-md-4 label ">Poin challenge</div>
    <div class="col-lg-9 col-md-8"><?=$d_peserta['poin_challenge']?> LP</div>
  </div>

  <div class="row">
    <div class="col-lg-3 col-md-4 label ">Poin bertanya</div>
    <div class="col-lg-9 col-md-8"><?=$d_peserta['poin_bertanya']?> LP</div>
  </div>

  <div class="row">
    <div class="col-lg-3 col-md-4 label ">Poin menjawab</div>
    <div class="col-lg-9 col-md-8"><?=$d_peserta['poin_menjawab']?> LP</div>
  </div>



</div>