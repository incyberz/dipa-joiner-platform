<section>
  <div class='section-title' data-aos-zzz='fade-up'>
    <h2>Pilih Room</h2>
    <p>
      Welcome <u><?=$nama_peserta?></u>! Kamu berada di kelas <u><?=$kelas?></u>. Silahkan Pilih Room!
    </p>
  </div>

  <div class=container>
    <form method="post">
      <div class=row>
        <?php
        # =====================================================
        # PROCESSOR PILIH ROOM
        # =====================================================
        if(isset($_POST['btn_pilih'])){

          $_SESSION['dipa_id_room'] = $_POST['btn_pilih'];
          jsurl('?');
        }

        $s = "SELECT a.*,
        a.nama as room,
        a.status as status_room,
        a.id as id_room,
        b.nama as creator,
        b.id as id_creator 
        
        FROM tb_room a 
        JOIN tb_peserta b ON a.created_by=b.id  
        ORDER BY a.status DESC
        ";
        $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
        while($d=mysqli_fetch_assoc($q)){
          if($d['status_room']==1){
            $status = 'Aktif';
            $gradasi = 'hijau';
            $btn = "<button class='btn btn-success mt2 w-100' name=btn_pilih value=$d[id_room]>Pilih</button>";
          }elseif($d['status_room']==-1){
            $status = 'Closed';
            $gradasi = 'kuning';
            $btn = "<button class='btn btn-warning mt2 w-100' name=btn_pilih value=$d[id_room] onclick='return confirm(\"Pilih Closed Room untuk melihat history?\")'>Closed</button>";
          }else{
            $status = 'Belum Aktif';
            $gradasi = 'merah';
            $btn = "<span class='btn btn-secondary mt2 w-100' onclick='alert(\"Room belum diaktifkan oleh Dosen. Segera hubungi beliau via whatsApp!\")'>Inactive</span>";
          }
          
          if($id_room==$d['id_room']){
            $wadah_active = 'wadah_active';
            $btn = "<span class='btn btn-secondary mt2 w-100' onclick='alert(\"Kamu sedang berada di room ini.\")'>Selected</span>";
          }else{
            $wadah_active = '';

          }

          echo "
            <div class='col-md-4 col-lg-3'>
              <div class='wadah $wadah_active gradasi-$gradasi tengah'>
                <div class='darkblue f18'>$d[room]</div>
                <div class=f12>Status: $status</div>
                <img src='assets/img/peserta/peserta-$d[id_creator].jpg' alt='pengajar' class='foto_profil'>
                <div>By: $d[creator]</div>
                $btn
              </div>
            </div>
          ";
        }


        ?>


      </div>
    </form>
  </div>
</section>";