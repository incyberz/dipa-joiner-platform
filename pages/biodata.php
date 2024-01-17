<?php
$unset = '<span class="consolas red miring kecil">unset</span>';

$pesan_update = '';
if(isset($_POST['btn_update'])){
  $pairs = '';
  foreach ($_POST as $key => $value) {
    if($key=="id" || $key=='btn_update') continue;
    $value = $value=='' ? 'NULL' : '\''.clean_sql(strtoupper($value)).'\'';
    $pairs .= "$key = $value, ";
  }

  $id = $_POST["id"];
  $s = "UPDATE tb_biodata SET $pairs WHERE id=$id ";
  $s = str_replace(',  WHERE', ' WHERE',$s); // hilangkan koma
  // echo $s;
  $link_back = "<hr><a href='?'>Kembali ke Home</a> | <a href='?ujian'>Ke Menu Ujian</a>";

  try {
    $q = mysqli_query($cn,$s) or throw new Exception(mysqli_error($cn));
    $pesan_update = div_alert('success', "Update success. $link_back");
  } catch (Exception $e) {
    if(strpos("salt$e",'Duplicate entry')){
      echo div_alert('danger',"Input kode sudah ada di database. Silahkan memakai kode unik lainnya. $link_back");
    }else{
      echo div_alert('danger',"Tidak bisa menjalankan Query SQL.");
    }
  }

}else{

}
# =========================================
# DESCRIBE THIS TABLE
# =========================================
$s = "DESCRIBE tb_biodata";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$colField = [];
$colType = [];
$colLength = [];
$colNull = [];
$colKey = [];
$colDefault = [];
while($d=mysqli_fetch_assoc($q)){
  array_push($colField, $d['Field']);
  array_push($colNull, $d['Null']);
  array_push($colKey, $d['Key']);
  array_push($colDefault, $d['Default']);

  if($d['Type']=='date'){
    $Type = 'date';
    $Length = 10;
  }else if($d['Type']=='timestamp'){
    $Type = 'timestamp';
    $Length = 19;
  }else{
    $pos = strpos($d['Type'],'(');
    $pos2 = strpos($d['Type'],')');
    $len = strlen($d['Type']);
    $len_type = $len - ($len-$pos);
    $len_length = $len - ($len-$pos2) - $len_type - 1;
  
    $Type = substr($d['Type'],0,$len_type);
    $Length = intval(substr($d['Type'],$pos+1, $len_length));
  }

  array_push($colType, $Type);
  array_push($colLength, $Length);
  // echo "<h1>Length : $Length</h1>";
}

// echo '<pre>';
// var_dump($colType);
// echo '</pre>';

# =========================================
# AUTO INSERT BLANKO BIODATA
# =========================================
if(!$punya_biodata){
  $s = "INSERT INTO tb_biodata (id) VALUES ($id_peserta)";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
}


# =========================================
# SELECT MY BIODATA
# =========================================
$s = "SELECT * FROM tb_biodata WHERE id=$id_peserta";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);

$isi_data = [];
foreach ($colField as $field) {
  $isi_data[$field] = $d[$field];
}

$id = $d["id"];

# =========================================
# GET PROPERTIES
# =========================================
$inputs = '';
foreach ($colField as $key => $field) {
  if($field=='id'
  ||$field=='tanggal_update'
  ||$field=='tanggal_lahir'
  ||$field=='gender'
  ||$field=='nik'
  ||$field=='bekerja_sebagai'
  ||$field=='bekerja_di'
  ) continue;
  // echo "<h1>field : $field</h1>";
  $nama_kolom = ucwords(str_replace('_',' ',$field));
  

  # =========================================
  # KEY FIELD HANDLERS
  # =========================================
  if($field=='sudah_bekerja'){
    $input = "
      <div class='kecil mb2'>
        <div class='mb1'>
          <label>
            <input type=radio name=sudah_bekerja class=sudah_bekerja value=0 required> Baru rencana ...
          </label>
        </div>
        <div class='mb1'>
          <label>
            <input type=radio name=sudah_bekerja class=sudah_bekerja value=0 required> Udah pernah ngirim2 lamaran
          </label>
        </div>
        <div class='mb1'>
          <label>
            <input type=radio name=sudah_bekerja class=sudah_bekerja value=0 required> Lagi nunggu hasil interview
          </label>
        </div>
        <div class='mb1'>
          <label>
            <input type=radio name=sudah_bekerja class=sudah_bekerja value=0 required> Udah kerja cuma keluar lagi
          </label>
        </div>
        <div class='mb1'>
          <label>
            <input type=radio name=sudah_bekerja class=sudah_bekerja value=1 required> Baru kerja, masih training
          </label>
        </div>
        <div class='mb1'>
          <label>
            <input type=radio name=sudah_bekerja class=sudah_bekerja value=1 required> Saya bekerja di perusahaan
          </label>
        </div>
      </div>

      <div id=jika_bekerja class=hideit>
        <input class='form-control mb2' name=bekerja_di id=bekerja_di required placeholder='Bekerja di ...' />
        <input class='form-control mb2' name=bekerja_sebagai id=bekerja_sebagai required placeholder='Bekerja sebagai ...' />
      </div>
    ";
  }else  if($field=='status_menikah'){
    $input = "
      <div class='kecil mb2'>
        <div class='mb1'>
          <label>
            <input type=radio name=status_menikah value=0 required> Belum pengen nikah
          </label>
        </div>
        <div class='mb1'>
          <label>
            <input type=radio name=status_menikah value=0 required> Udah punya calon
          </label>
        </div>
        <div class='mb1'>
          <label>
            <input type=radio name=status_menikah value=0 required> Udah tunangan
          </label>
        </div>
        <div class='mb1'>
          <label>
            <input type=radio name=status_menikah value=1 required> Saya sudah menikah
          </label>
        </div>
      </div>
    ";
  }elseif($field=='agama'){
    $input = "
      <div class='kecil mb2'>
        <div class='mb1'>
          <label>
            <input type=radio name=agama value=1 required> Islam (ngikut ortu)
          </label>
        </div>
        <div class='mb1'>
          <label>
            <input type=radio name=agama value=1 required> Islam (KTP)
          </label>
        </div>
        <div class='mb1'>
          <label>
            <input type=radio name=agama value=1 required> Islam (mayoritas)
          </label>
        </div>
        <div class='mb1'>
          <label>
            <input type=radio name=agama value=1 required> Islam (ittiba`)
          </label>
        </div>
        <div class='mb1'>
          <label>
            <input type=radio name=agama value=2 required> Katolik
          </label>
        </div>
        <div class='mb1'>
          <label>
            <input type=radio name=agama value=3 required> Protestan
          </label>
        </div>
        <div class='mb1'>
          <label>
            <input type=radio name=agama value=4 required> Hindu
          </label>
        </div>
        <div class='mb1'>
          <label>
            <input type=radio name=agama value=5 required> Budha
          </label>
        </div>
        <div class='mb1'>
          <label>
            <input type=radio name=agama value=0 required> Atheis
          </label>
        </div>
        <div class='mb1'>
          <label>
            <input type=radio name=agama value=0 required> Agama Alien
          </label>
        </div>
        <div class='mb1'>
          <label>
            <input type=radio name=agama value=0 required> I`m god !
          </label>
        </div>
        <div class='mb1'>
          <label>
            <input type=radio name=agama value=6 required> Lainnya
          </label>
        </div>
      </div>
    ";
  }else if($colKey[$key]=='MUL'){
    # =========================================
    # CREATE INPUT SELECT
    # =========================================
    if($colField[$key]=='satuan'){
      $s2 = "SELECT satuan FROM tb_satuan";
    }else{
      $arr = explode('_', $colField[$key]);
      $s2 = "SELECT id,nama FROM tb_$arr[1] WHERE status=1";
    }
    // echo "$s2";
    $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));
    $opt = '';
    while($d2=mysqli_fetch_assoc($q2)){
      if($colField[$key]=='satuan'){
        $selected = $d2['satuan'] == $d[$colField[$key]] ? 'selected' : '';
        $opt .= "<option value='$d2[satuan]' $selected>$d2[satuan]</option>";
      }else{
        $selected = $d2['id'] == $d[$colField[$key]] ? 'selected' : '';
        $opt .= "<option value='$d2[id]' $selected>$d2[nama]</option>";
      }

    }

    $disabled_change_role = ($id_role!=9 and biodata=='user') ? 'disabled' : '';
    $input = "<select class='form-control mb2' name='$field' $disabled_change_role>$opt</select>";
    
  }else{
    # =========================================
    # NORMAL INPUT OR TEXTAREA
    # =========================================
    $param_max = '';
    $param_maxlength = '';
    $param_step = '';

    $ftype = $colType[$key];

    if($ftype=='varchar'||$ftype=='char'){
      $type = 'text';
      $param_maxlength = $ftype=='varchar' ? "maxlength='$colLength[$key]' " : "maxlength='$colLength[$key]' minlength='$colLength[$key]' ";
    }elseif($ftype=='int'||$ftype=='smallint'||$ftype=='tinyint'||$ftype=='decimal'){
      $type = 'number';
      if($ftype=='decimal') $param_step = 'step="0.01"';

    }elseif($ftype=='timestamp' || $ftype=='date') {
      $type = 'date';
    }else{
      die(div_alert('danger',"Type of field: $ftype belum ditentukan."));
    }
    $params = "
      type='$type'
      class='form-control mb2' 
      id='$field' 
      name='$field' 
      value='$d[$field]' 
      placeholder='$nama_kolom' 
      $param_max 
      $param_maxlength 
      $param_step 
      required
    ";

    if($colLength[$key]>100){
      $input = "<textarea $params>$d[$field]</textarea>";
    }else{
      $input = "<input $params>";
    }

    if($field=='wirausaha_bidang') $input.= "<div class='kecil miring abu mt1 mb2'>Strip jika tidak punya wirausaha</div>";

    
  }

  # =========================================
  # FINAL OUTPUT OF INPUT | TEXTAREA | SELECT
  # =========================================
  $inputs .= "
  <div class='mb1 darkabu '>$nama_kolom </div>
  $input
  ";
}



$hide_after_nik = $isi_data['nik']=='' ? 'hideit' : '';

# ==============================================================
# FINAL OUTPUT TR 
# ==============================================================
$tr = "
  <tr>
    <td colspan=4>
      <form method=post class=formulir>
        <input name=id value=$id type=hidden>
        <div class='wadah gradasi-hijau'>

          <p style='text-align:center;'><img src='assets/img/lihat_ktp.jpg' class='rounded-circle' width='200px'><div class='f12 miring darkblue mt1 tengah'>Sediain KTP dlu ya!</div></p>

          <div class='darkabu mb1 tengah'>Nomor KTP</div>
          <input type='text' class='input_isian form-control tengah' required minlength='16' maxlength='16' id='nik' name='nik' value='$isi_data[nik]'> 
          <div id='nik_divide' class='blue f24 tengah mt2'></div>
          <div id='nik_ket' class='tengah merah hideit mt2 kecil'>Silahkan masukan 16 digit NIK KTP sesuai yang tertera di KTP/KK Anda.</div>
          <div id='div_nik_info' class='kecil mt2 hideit'>
            <ul>
              <li id='ket_gender'></li> 
              <li id='ket_ttl'></li> 
              <li id='ket_kec'></li> 
              <li id='ket_kab'></li> 
              <li id='ket_prov'></li>
              <li id='ket_usia'></li> 
              <li id='ket_ultah'></li> 
              <li id='ket_pacar'>Pacar kamu adalah <span class='pointer abu miring' id=show_pacar>...(show)</span></li> 
              <li id='ket_tanggal_nikah' class='hideit'>Tanggal nikah <span class='pointer abu miring' id=show_tanggal_nikah>...(show)</span></li> 
            </ul> 
          </div>
          <input type='hidden' name='gender' id='gender' value='$isi_data[gender]'>
          <input type='hidden' name='tanggal_lahir' id='tanggal_lahir' value='$isi_data[tanggal_lahir]'>
          <div id=div_after_nik class='$hide_after_nik'>
          <hr>
            $inputs

            <div class=' '>
              <button class='btn btn-success btn-sm' name=btn_update>Update</button>
            </div>
          </div>
        </div>
      </form>

    </td>
  </tr>
";

$info = "Belum ada data biodata pada database.";
$clear = '';
$gradasi = '';


echo "
<section class='pengajar section-bg'>
  <div class='container'>
    $pesan_update
    <div class='section-title'>
      <h2>BIODATA</h2>
      <p>Silahkan isi biodata kamu agar dapat mengakses fitur lainnya!</p>
    </div>

    <table class='table table-hover'>
      $tr
    </table>
  </div>
</section>
";

include 'biodata_nik_handle.php';
















?><script>
  $(function(){
    $('.sudah_bekerja').click(function(){
      let val = $(this).val();
      if(val==1){
        $('#jika_bekerja').fadeIn();
        $('#bekerja_di').val('');
        $('#bekerja_sebagai').val('');
      }else{
        $('#jika_bekerja').fadeOut();
        $('#bekerja_di').val('-');
        $('#bekerja_sebagai').val('-');

      }
    });


    $('#show_pacar').click(function(){
      let y = confirm("Try to connect Disdukcapil API to retrieve this data?\n\nShow me!");
      if(!y) return;

      $('#show_pacar').hide();
      $('#show_pacar').html('<span class="kecil miring red">... (maaf, system merahasiakan hal ini)</span>');
      $('#show_pacar').fadeIn(2000);
      $('#ket_tanggal_nikah').fadeIn(4000);

    })
    $('#show_tanggal_nikah').click(function(){
      let y = confirm("Try to connect Kemenag API to retrieve this data?\n\nYes, connect.");
      if(!y) return;

      $('#show_tanggal_nikah').hide();
      $('#show_tanggal_nikah').html('<span class="kecil miring red">... (Kemenag API not responded. Kamu penasaran ya!? Sepertinya kamu jomblo!!)</span>');
      $('#show_tanggal_nikah').fadeIn(4000);

    });


  })
</script>