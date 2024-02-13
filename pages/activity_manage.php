<hr>
<div class="wadah gradasi-kuning">
  <?php
  $img_detail = img_icon('detail');

  $s = "SELECT * FROM tb_assign_$jenis WHERE id=$id_assign";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)){
    $d = mysqli_fetch_assoc($q);
    
    # ====================================
    # MANAGE ASSIGNED JENIS
    # ====================================
    $tr='';
    foreach ($d as $key => $value){
      if(substr($key,0,2)=='id') continue;
      $tr.= "
        <tr>
          <td>$key</td>
          <td>
            <input class='form-control' name=$key value='$value' />
          </td>
        </tr>
      ";
    }
    echo "
    <h5 class='darkblue proper'>Manage Assigned $jenis <span class=btn_aksi id=form_assign__toggle>$img_detail</span></h5>
    <form method=post id=form_assign class=hideit>
      <table class=table>
        $tr
      </table>
      <button class='btn btn-primary w-100' name=btn_update_assign value=$id_assign>Update Assign $jenis :: $kelas</button>
    </form>
    ";

    // beda wadah
    echo '</div><div class="wadah gradasi-kuning">';

    # ====================================
    # MANAGE LATIHAN/CHALLENGE
    # ====================================
    $id = $d['id_'.$jenis];
    $s = "SELECT * FROM tb_$jenis WHERE id=$id";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

    $d = mysqli_fetch_assoc($q);
    $tr='';
    foreach ($d as $key => $value){
      if($key=='id'||$key=='id_room') continue;
      $rows = 2 + intval(strlen($value)/30);
      $input = strlen($value)>30 
      ? "<textarea class='form-control' name=$key rows=$rows>$value</textarea>" 
      : "<input class='form-control' name=$key value='$value' />";
      $tr.= "
        <tr>
          <td>$key</td>
          <td>
            $input
          </td>
        </tr>
      ";
    }
    echo "
    <h5 class='darkblue proper'>Update $jenis Properties <span class=btn_aksi id=form_properties__toggle>$img_detail</span></h5>
    <form method=post id=form_properties class=hideit>
      <table class=table>
        $tr
      </table>
      <button class='btn btn-primary w-100' name=btn_update_jenis value=$id>Update $jenis</button>
    </form>
    ";

  }else{
    echo div_alert('danger',"Data Assign $jenis tidak ditemukan.");
  }



  ?>
</div>