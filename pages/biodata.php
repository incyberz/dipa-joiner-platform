<?php
$unset = '<span class="consolas red miring kecil">unset</span>';


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
  echo $s;
  $link_back = "<hr><a href='?'>Kembali ke Home</a>";

  try {
    $q = mysqli_query($cn,$s) or throw new Exception(mysqli_error($cn));
    echo div_alert('success', "Update success. $link_back");
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
# SELECT MAIN DATA
# =========================================
$s = "SELECT * FROM tb_biodata WHERE id=$id_peserta";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_show = mysqli_num_rows($q);

$tr = '';
$i = 0;
$tambah_id = '';
while($d=mysqli_fetch_assoc($q)){
  $i++;
  $id = $d["id"];

  # =========================================
  # GET PROPERTIES
  # =========================================
  $li_ket = '';
  $inputs = '';
  foreach ($colField as $key => $field) {
    if($field=='id'
    ||$field=='tanggal_update'
    ||$field=='tanggal_lahir'
    ) continue;
    // echo "<h1>field : $field</h1>";
    $nama_kolom = ucwords(str_replace('_',' ',$field));
    $isi = $d[$field]=='' ? $unset : $d[$field];
    $li_ket .= "
    <li><span class='miring abu proper'>$nama_kolom:</span> $isi</li> 
    ";
    

    # =========================================
    # KEY FIELD HANDLERS
    # =========================================
    if($colKey[$key]=='MUL'){
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
  
      
    }

    # =========================================
    # FINAL OUTPUT OF INPUT | TEXTAREA | SELECT
    # =========================================
    $inputs .= "
    <div class='mb1 darkabu '>$nama_kolom </div>
    $input
    ";
  }


  
  

  # ==============================================================
  # FINAL OUTPUT TR 
  # ==============================================================
  $tr .= "
    <tr>
      <td colspan=4>
        <form method=post class=formulir>
          <input name=id value=$id type=hidden>
          <div class='wadah gradasi-hijau'>

            <p style='text-align:center;'><img src='assets/img/lihat_ktp.jpg' class='rounded-circle' width='200px'><br><small>Silahkan lihat KTP Anda untuk melihat Nomor Induk Kependudukan!</small></p>

            <div class='darkabu mb1 tengah'>Nomor KTP</div>
            <input type='text' class='input_isian form-control tengah' required minlength='16' maxlength='16' id='nik' name='nik' value='$nik'> 
            <div id='nik_divide' class='blue f24 tengah'></div>
            <small id='nik_ket' class='merah hideit mt1 kecil'>Silahkan masukan 16 digit NIK KTP sesuai yang tertera di KTP/KK Anda.</small>
            <div id='div_nik'>
              <div id='ket_gender'></div> 
              <div id='ket_ttl'></div> 
              <div id='ket_usia'></div> 
            </div>
            <input type='hiddena' name='gender' id='gender' value='$gender'>
            <input type='hiddena' name='tanggal_lahir' id='tanggal_lahir' value='$tanggal_lahir'>

            <hr>
            
            $inputs

            <div class=' '>
              <button class='btn btn-success btn-sm' name=btn_update>Update</button>
            </div>
          </div>
        </form>

      </td>
    </tr>
  ";
}

$info = "Belum ada data biodata pada database.";
$clear = '';
$gradasi = '';


echo "
<section class='pengajar section-bg'>
  <div class='container'>
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
  })
</script>