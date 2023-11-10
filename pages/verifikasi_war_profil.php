<?php
if(isset($_POST['btn_upload'])){
  // echo '<pre>';
  // var_dump($_FILES);
  // echo '</pre>';

  // $id_peserta = $_POST['id_peserta'];
  $target = "assets/img/peserta/wars/peserta-$_POST[id_peserta].jpg";
  if(move_uploaded_file($_FILES['war_profil']['tmp_name'],$target)){
    echo div_alert('success','Upload Success');
    rename("assets/img/peserta/war-$_POST[id_peserta].jpg","assets/img/peserta/wars/peserta-$_POST[id_peserta]-hi.jpg");
  }
  
}elseif(isset($_POST['btn_reject'])){
  rename("assets/img/peserta/war-$_POST[id_peserta].jpg","assets/img/peserta/war-$_POST[id_peserta]-reject.jpg");
  echo div_alert('danger','Reject Success');
}

$s = "SELECT * FROM tb_peserta WHERE status=1";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
while($d=mysqli_fetch_assoc($q)){
  $id=$d['id'];
  if(file_exists("assets/img/peserta/war-$id.jpg")){
    echo "
    <div class=tengah>
    <div class=wadah>
      <img src='assets/img/peserta/war-$id.jpg' style='max-width: 400px'>
      <div>$d[nama]</div>
        <form method=post enctype='multipart/form-data'>
          reupload:
          <input type=hiddena name=id_peserta value=$id />
          <input type=file name=war_profil accept='.jpg' />
          <button name=btn_upload>Upload</button>
          <button name=btn_reject>Reject</button>
        </form>
      </div>
    </div>
    ";
  }
}
