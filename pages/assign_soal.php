<?php
instruktur_only();
$null = '<span class="abu f12 miring consolas">null</span>';
$abjad = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'];
$get_id_paket = $_GET['id_paket'] ?? '';
echo "<span class='hideit' id=id_paket>$get_id_paket</span>";

$judul = 'Assign Soal';
set_h2($judul, "Silahkan cek atau cek-all kemudian klik Assign untuk memasukan soal ke Paket", '?manage_paket_soal');

















if (isset($_POST['btn_assign_all']) || isset($_POST['btn_drop_all'])) {
  $mode = isset($_POST['btn_assign_all']) ? 'assign_all' : 'drop_all';
  if ($mode == 'assign_all') {
    // select all id_soal
    $arr = explode(',', $_POST['id_soals']);
    foreach ($arr as $id_soal) {
      if (!$id_soal) continue;

      // cek exist
      $s = "SELECT 1 FROM tb_assign_soal WHERE id_paket=$get_id_paket AND id_soal=$id_soal";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      if (!mysqli_num_rows($q)) {
        // insert
        $s = "INSERT INTO tb_assign_soal (id_paket,id_soal) VALUES ($get_id_paket,$id_soal)";
        $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
        echolog("inserting... id_soal: $id_soal");
      } else {
        // skip insert
      }
    }
  } else {
    $s = "DELETE FROM tb_assign_soal WHERE id_paket=$get_id_paket";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echolog("unsigning all soal...");
  }
  jsurl('', 2000);
  exit;
}

// if (isset($_POST['btn_assign']) || isset($_POST['btn_drop'])) {
//   if (isset($_POST['id_soal'])) {
//     $id_paket = $_POST['btn_assign'] ?? $_POST['btn_drop'];
//     foreach ($_POST['id_soal'] as $id_soal) {
//       if (isset($_POST['btn_drop'])) {
//         // proses drop
//         $s = "DELETE FROM tb_assign_soal WHERE id_soal=$id_soal AND id_paket=$id_paket";
//         $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
//         echolog("Dropping id_soal: $id_soal");
//       } else {
//         // proses assign
//         $s = "SELECT 1 FROM tb_assign_soal WHERE id_soal=$id_soal AND id_paket=$id_paket";
//         $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
//         if (mysqli_num_rows($q)) {
//           echolog('Soal sudah ter-assign.');
//         } else {
//           $s = "INSERT INTO tb_assign_soal (id_soal,id_paket) VALUES ($id_soal,$id_paket)";
//           $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
//           echolog("Assigning id_soal: $id_soal");
//         }
//       }
//     }
//   }

//   echo div_alert('success', 'Assign soal berhasil.');
//   jsurl('', 2000);
//   exit;
// }






































# =============================================
# PAKET SOAL PROPERTIES
# =============================================
$s = "SELECT a.nama as nama_paket_soal,
(
  SELECT COUNT(1) 
  FROM tb_jawabans p 
  JOIN tb_paket_kelas q ON p.paket_kelas=q.paket_kelas  
  WHERE q.id_paket=a.id) count_penjawab, 
(
  SELECT COUNT(1) FROM tb_assign_soal 
  WHERE id_paket=$get_id_paket) assigned_soal 

FROM tb_paket a WHERE a.id=$get_id_paket";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) die('Data Paket Soal tidak ditemukan.');
$d = mysqli_fetch_assoc($q);
$nama_paket_soal = $d['nama_paket_soal'];
$count_penjawab = $d['count_penjawab'];
$assigned_soal = $d['assigned_soal'];

if ($count_penjawab) echo div_alert('danger', "Perhatian! Paket ini sudah dijawab oleh <a target=_blank href='?monitoring_ujian&id_paket=$get_id_paket'>$count_penjawab $Peserta</a>. Anda tidak dapat lagi melakukan proses Assign ataupun Drop Soal dari Paket Soal ini.");
$disabled_assign = $count_penjawab ? 'disabled' : '';

# =============================================
# MAIN SELECT SOAL
# =============================================
$s = "SELECT a.*, 
a.id as id_soal,
(
  SELECT 1 FROM tb_assign_soal 
  WHERE id_soal=a.id
  AND id_paket='$get_id_paket') sudah_assign, 
(
  SELECT 1 FROM tb_assign_soal 
  WHERE id_soal=a.id AND id_paket=$get_id_paket) is_assigned, 
(
  SELECT COUNT(1) FROM tb_assign_soal 
  WHERE id_paket=$get_id_paket) count_assign, 
(
  SELECT no FROM tb_sesi  
  WHERE id=a.id_sesi
  AND a.id_sesi IS NOT NULL
  ) no_sesi -- optional 
FROM tb_soal a 
WHERE a.id_room=$id_room 
AND a.tipe_soal='PG' 
ORDER BY a.id_sesi, a.id 
";
// echolog($s);
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$count_soal = mysqli_num_rows($q);
if (!$count_soal) {
  $tr = div_alert('danger', "Belum ada data soal untuk $Room ini.");
} else {
  $tr = '';
  $no = 0;
  $id_soals = '';
  $id_soals_assigned = '';
  while ($d = mysqli_fetch_assoc($q)) {
    $id_soal = $d['id_soal'];
    $id_soals .= "$id_soal,";
    if ($d['is_assigned']) $id_soals_assigned .=  "$id_soal,";
    $arr = explode('~~~', $d['opsies']);
    $opsies = '';
    foreach ($arr as $key => $value) {
      $opsies .= "<span class='' style='display:inline-block;margin-right:15px'>$abjad[$key]. <span id=opsi_$abjad[$key]__$id_soal>$value</span></span>";
    }

    $pembahasan_show = $d['pembahasan'] ? "<div class='miring abu f14' id=pembahasan__$id_soal>$d[pembahasan]</div>" : $null;

    $count_assign = $d['count_assign'];
    $list_paket = $null;
    if ($count_assign) {
      $s2 = "SELECT b.nama as nama_paket_soal 
      FROM tb_assign_soal a 
      JOIN tb_paket b ON a.id_paket=b.id 
      WHERE a.id_soal=$id_soal";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
      $li = '';
      while ($d2 = mysqli_fetch_assoc($q2)) {
        $li .= "<li>$d2[nama_paket_soal]</li>";
      }
      $list_paket = "<ol>$li</ol>";
    }

    $no++;

    $gradasi = $d['sudah_assign'] ? 'gradasi-hijau' : '';
    $hide_btn_assign = $d['sudah_assign'] ? 'style="display:none"' : '';
    $hide_btn_drop = $d['sudah_assign'] ? '' : 'style="display:none"';

    $no_sesi = $d['no_sesi'] ? "P-$d[no_sesi]" : $null;

    $tr .= "
      <tr class='tr_soal $gradasi' id=tr_soal__$id_soal>
        <td>$no</td>
        <td>
          <span id=kalimat_soal__$id_soal>$d[soal]</span>
          <div class='f12 abu'>$opsies</div>
        </td>
        <td>
          $no_sesi
        </td>
        <td width=10% class=kanan>
          <button class='btn btn-primary btn-sm assign-soal ' $hide_btn_assign $disabled_assign id=assign__$id_soal>Assign</button>
          <button class='btn btn-danger btn-sm assign-soal' $hide_btn_drop $disabled_assign id=drop__$id_soal>Drop</button>
        </td>
      </tr>
    ";
  } // end while
  // $tr = $tr ? $tr : tr_col('Soal tidak ada atau semua sudah di di-assign ke Paket Soal.', 'p4 gradasi-kuning consolas f12 miring abu');
}


$btn_assign_all = ($count_assign == $count_soal || $count_penjawab) ? '' : "
  <button 
    name='btn_assign_all' 
    class='btn btn-primary btn-sm' 
    onclick='return confirm(`Yakin ingin memasukan $count_soal soal ke paket ini?`)'
    >
    Assign All [$count_soal]
  </button>
";
$btn_drop_all = (!$count_assign || $count_penjawab) ? '' : "
  <button 
    name='btn_drop_all' 
    class='btn btn-danger btn-sm' 
    onclick='return confirm(`Yakin ingin mengosongkan soal pada paket ini?`)'
    >
    Drop All [$count_assign]
  </button>
";


# ================================================ -->
# FINAL ECHO
# ================================================ -->
echo "
<div class='gradasi-hijau p2 tengah' style='width:100vw;height:60px;position:fixed;bottom:0; left:0; border-top:solid 1px #ccc'>
  <a href='?manage_paket_soal' >
    $img_prev
  </a>
  Assigned Soal: <span class='f24 blue' id=assigned_soal>$assigned_soal</span> 
</div>
<h2 class='bordered br5 f20 consolas tengah p2 gradasi-toska mb2' >
  Assign Soal untuk Paket <b class='darkblue'>$nama_paket_soal</b>
</h2>
<table class=table>
  <thead class=gradasi-toska>
    <th class=proper>no</th>
    <th class=proper colspan=2>
      <div class='flexy flex-between'>
        <div class=pt2>
          Terdapat $count_soal Soal yang Tersedia
        </div>
        <div>
          <form method=post class='m0 p0'>
            <input type=hidden name=id_soals value='$id_soals'>
            <input type=hidden name=id_soals_assigned value='$id_soals_assigned'>
            $btn_assign_all
            $btn_drop_all
          </form>
        </div>
      </div>
    </th>
  </thead>
  $tr
</table>
";




































?>
<script type="text/javascript">
  let assigned_soal = 0;

  function set_assigned_soal_count(aksi) {
    if (aksi == 'drop') {
      assigned_soal--;
    } else if (aksi == 'assign') {
      assigned_soal++;
    }
    $('#assigned_soal').text(assigned_soal);

  }
  $(function() {
    assigned_soal = parseInt($('#assigned_soal').text());
    // $('#toggle_cekall').click(function() {
    //   $('.cek_soal').prop('checked', $(this).prop('checked'));
    // });
    // $('#toggle_dropall').click(function() {
    //   $('.drop_soal').prop('checked', $(this).prop('checked'));
    // });
    $('.assign-soal').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id_soal = rid[1];
      console.log(aksi, id_soal);
      let id_paket = $('#id_paket').text();
      let link_ajax = `ajax/ajax_assign_soal.php?aksi=${aksi}&id_soal=${id_soal}&id_paket=${id_paket}`;
      $.ajax({
        url: link_ajax,
        success: function(a) {
          // alert(a);
          if (a.trim() == 'sukses') {
            set_assigned_soal_count(aksi);
            if (aksi == 'assign') {
              $('#tr_soal__' + id_soal).addClass('gradasi-hijau');
              $('#assign__' + id_soal).hide();
              $('#drop__' + id_soal).show();
            } else {
              $('#tr_soal__' + id_soal).removeClass('gradasi-hijau');
              $('#assign__' + id_soal).show();
              $('#drop__' + id_soal).hide();
            }
          } else {
            alert(a)
          }
        }
      })
    })
  })
</script>