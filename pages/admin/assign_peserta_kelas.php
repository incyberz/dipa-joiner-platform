<?php
$from_kelas = $_GET['from_kelas'] ?? '';
$get_kelas = $_GET['kelas'] ?? die(erid('kelas'));
# ============================================================
# PROCESSORS FILTER 
# ============================================================
if (isset($_POST['btn_filter_kelas'])) {
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';
  jsurl("?assign_peserta_kelas&kelas=$get_kelas&from_kelas=$_POST[select_from_kelas]");
}









# ============================================================
# HEADER ASSIGN PESERTA KELAS
# ============================================================
if (!$ta_aktif) die(erid('ta_aktif'));
set_h2(
  'Assign Peserta Kelas',
  "Proses memasukan $Peserta ke Grup Kelas 
  <b class=darkblue>$get_kelas</b>
  pada TA. 
  <b class=darkblue>$ta_aktif</b>
  <div class=mt2>
    <a href='?peserta_kelas'>
      $img_prev
    </a>
  </div>
  "
);
instruktur_only();


# ==================================================
# SELECT FROM KELAS TERDAHULU
# ==================================================
$s = "SELECT * FROM tb_kelas WHERE ta < $ta_aktif";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$opt = '<option value=0>--Pilih--</option>';
$opt .= '<option value=all>Semua Kelas</option>';
while ($d = mysqli_fetch_assoc($q)) {
  $selected = $d['kelas'] == $from_kelas ? 'selected' : '';
  $opt .= "<option value='$d[kelas]' $selected>$d[kelas] ~ TA.$d[ta]</option>";
}
$select_from_kelas = "<select class='form-control' name=select_from_kelas>$opt</select>";



# ==================================================
# PESERTA IN KELAS
# ==================================================
$arr_id_peserta = [];
$s = "SELECT 
a.id as id_kelas_peserta,
c.id as id_peserta,
c.nama as nama_peserta 

FROM tb_kelas_peserta a   
JOIN tb_kelas b ON a.kelas=b.kelas    
JOIN tb_peserta c ON a.id_peserta=c.id 
WHERE b.kelas='$get_kelas' 
AND b.ta=$ta_aktif
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q) == 0) {
  $tr_peserta_kelas = div_alert('danger', "Belum ada $Peserta dari kelas $get_kelas.");
} else {
  $i = 0;
  $tr_peserta_kelas = '';
  while ($d = mysqli_fetch_assoc($q)) {
    array_push($arr_id_peserta, $d['id_peserta']);
    $i++;
    $tr_peserta_kelas .= "
      <tr id=tr__$d[id_peserta]>
        <td>$i</td>
        <td class=upper>$d[nama_peserta]</td>
        <td><button class='btn btn-danger btn-sm btn_aksi_assign' id=drop__$d[id_peserta]>Drop</button></td>
      </tr>
    ";
  }
}


# ==================================================
# PESERTA ALL 
# ==================================================
$join_from_kelas = !$from_kelas ? 'WHERE 1' : "
  JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
  WHERE b.kelas = '$from_kelas'
";


$s = "SELECT 
a.id as id_peserta, 
a.nama as nama_peserta, 
(
  SELECT p.kelas 
  FROM tb_kelas_peserta p 
  JOIN tb_kelas q ON p.kelas=q.kelas  
  WHERE id_peserta=a.id
  AND q.ta=$ta_aktif) kelas 

FROM tb_peserta a 
-- JOIN tb_kelas_peserta
$join_from_kelas 
AND status=1 
AND id_role=1
ORDER BY kelas,a.nama    
";
$q = mysqli_query($cn, $s) or die("$s<hr>" . mysqli_error($cn));
if (mysqli_num_rows($q) == 0) {
  $tr_peserta_all = div_alert('danger', "Belum ada $Peserta pada database.");
} else {
  $i = 0;
  $tr_peserta_all = '';
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $assign = $d['kelas'] ? "<span class='f12 miring abu'>$d[kelas]</span>" : "<button class='btn btn-sm btn-success btn_aksi_assign' id=assign__$d[id_peserta]>Assign</button>";
    $tr_peserta_all .= "
      <tr>
        <td>$i</td>
        <td class=upper>$d[nama_peserta]</td>
        <td>$assign</td>
      </tr>
    ";
  }
}


# ==================================================
# FINAL OUTPUT 
# ==================================================
$Semua_Peserta = $from_kelas ? "Dari kelas: $from_kelas" : "Semua Kelas";
echo "
  <div class='row'>
    <div class='col-6'>
      <div class=wadah>
        <h2>$Semua_Peserta</h2>
        <form method=post>
          <div class='flexy '>
            <div>$select_from_kelas</div>
            <div><button class='btn btn-sm btn-info' name=btn_filter_kelas>Filter</button></div>
          </div>
        </form>
        <table class='table'>
          <thead>
            <th>No</th>
            <th>NAMA PESERTA</th>
            <th>AKSI</th>
          </thead>
          $tr_peserta_all
        </table>
      </div>
    </div>

    <div class='col-6'>
      <div class=wadah>
        <h2>Peserta Kelas <span id=get_kelas>$get_kelas</span> </h2>
        <table class='table'>
          <thead>
            <th>No</th>
            <th>NAMA PESERTA</th>
            <th>AKSI</th>
          </thead>
          $tr_peserta_kelas
        </table>
      </div>
    </div>
  </div>
";
?><script>
  $(function() {
    let kelas = $('#kelas').text();
    let get_kelas = $('#get_kelas').text();
    $('.btn_aksi_assign').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id_peserta = rid[1];


      let link_ajax = `ajax/ajax_assign_peserta_kelas.php?aksi=${aksi}&id_peserta=${id_peserta}&kelas=${get_kelas}`;
      $.ajax({
        url: link_ajax,
        success: function(a) {
          if (a.trim() == 'sukses') {
            if (aksi == 'assign') {
              $('#assign__' + id_peserta).fadeOut();
            } else if (aksi == 'drop') {
              $('#tr__' + id_peserta).fadeOut();
            }
          } else {
            console.log(a);
            alert('Tidak bisa ' + aksi + ' data.');
          }
        }
      })


    })
  })
</script>