<style>
  .flexy-item {
    border: solid 1px #ccc;
    border-radius: 5px;
    width: 160px;
    padding: 10px
  }
</style>
<div class="section-title" data-aos="fade">
  <h2>Profil Peserta</h2>
  <p><?php if ($id_role == 2) echo "Berikut adalah profil $Peserta yang harus diverifikasi. | 
        <a href='?verifikasi_profil_peserta'>Unverified</a> | 
        <a href='?verifikasi_profil_peserta&profil_ok=1'>Accepted</a> | 
        <a href='?verifikasi_profil_peserta&profil_ok=-1'>Rejected</a>";
      if ($id_role == 1) echo 'Berikut adalah status verifikasi untuk Profil kamu'; ?>
  </p>
</div>
<!-- ============================================================== -->
<?php
$judul = "Profil Peserta";
set_title($judul);

login_only();
$get_profil_ok = $_GET['profil_ok'] ?? null;


$sql_id_peserta = $id_role == 1 ? "a.id=$id_peserta" : '1';
if ($id_role == 2) {
  $sql_profil_ok = $get_profil_ok ? "a.profil_ok=$get_profil_ok" : 'a.profil_ok is null';
} else {
  $sql_profil_ok = '1';
}
$s = "SELECT a.id as id_peserta,
a.nama as nama_peserta,
b.kelas,
a.profil_ok,
a.image  

FROM tb_peserta a 
JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
JOIN tb_kelas c ON b.kelas=c.kelas 
JOIN tb_room_kelas d ON c.kelas=d.kelas 
WHERE a.status=1 
AND c.ta = $ta_aktif  -- tahun ajar saat ini
AND d.id_room=$id_room -- di $Room ini
AND $sql_id_peserta  -- untuk _peserta ini atau semua (admin)
AND $sql_profil_ok   
AND c.kelas != 'INSTRUKTUR' 
";

// echo '<pre>';
// echo $s;
// echo '</pre>';
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) {
  $divs = div_alert('info', "Belum ada profil $Peserta yang harus Anda verifikasi.");
} else {
  $divs = '';
  $batas = 20;
  while ($d = mysqli_fetch_assoc($q)) {
    if (!$d['image']) continue;
    $id = $d['id_peserta'];
    $path = "$lokasi_profil/$d[image]";
    if (file_exists($path)) {
      $batas--;
      if ($batas >= 0) {
        if ($id_role == 2) {

          $btn_accept = $get_profil_ok == 1 ? '<button class="btn btn-secondary btn-sm btn-block " disabled>Accept</button>' : "<button class='btn btn-success btn-sm btn-block btn_verif' id=accept__$id>Accept</button>";
          $btn_reject = $get_profil_ok == -1 ? '<button class="btn btn-secondary btn-sm btn-block " disabled>Reject</button>' : "<button class='btn btn-danger btn-sm btn-block btn_verif' id=reject__$id>Reject</button>";

          $divs .= "
            <div class='flexy-item tengah bg-white' id=box__$id>
              <div>
                <img src='$path' class='foto_profil' />
              </div> 
              <div style='overflow:hidden;white-space: nowrap'>$d[nama_peserta]</div>
              <div class='abu mb2' style='font-size:10px; margin-top: -5px'>$d[kelas]</div>
        
              <div>
                <table width=100%>
                  <tr>
                    <td width=50%>
                      $btn_accept
                    </td>
                    <td width=50%>
                      $btn_reject
                    </td>
                  </tr>
                  <tr class=hideit>
                    <td colspan=2>
                      <button class='btn btn-primary btn-sm btn-block btn_verif' id=formal__$id>Formal</button>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          ";
        } else {
          $status = [
            -1 => '<span class=red>Profil kurang layak.</span> Silahkan ganti dengan foto profil close-up (setengah badan) atau pas-foto ijazah. Kemungkinan ditolak antara lain: memakai avatar, foto terlalu jauh, foto landscape, dll',
            0 => '<span class=red>Unverified.</span> Profil kamu belum diverifikasi, masih terdapat fitur yang dibatasi yang mengharuskan verified profile',
            1 => "<span class=green>Accepted.</span> Profil bebas kamu sudah diverifikasi oleh $Trainer, kamu bisa mengakses seluruh fitur",
            2 => "<span class=green>Accepted.</span> Profil formal kamu sudah diverifikasi oleh $Trainer, kamu bisa mengakses seluruh fitur",
          ];
          $ok = $d['profil_ok'] ?? 0;
          $status_profil = $status[$ok];
          $divs = "
            <div class=tengah>
              <a href='?upload_profil'><img src='$path' class='foto_profil' /></a>
              <div><span class='miring abu'>Status profile:</span>$status_profil</div>
            </div>
          ";
        }
      }
    }
  }
}


echo $id_role == 2 ? "<div class='wadah gradasi-hijau flexy'>$divs</div>" : "<div class='wadah tengah'>$divs</div>";
if ($id_role == 2) {

  echolog('include verifikasi_war_profil.php');
  include 'verifikasi_war_profil.php';
}















?><script>
  $(function() {
    $('.btn_verif').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id_peserta = rid[1];

      let link_ajax = `ajax/set_profil_ok.php?id_peserta=${id_peserta}&aksi=${aksi}`;

      $.ajax({
        url: link_ajax,
        success: function(a) {
          if (a.trim() == 'sukses') {
            $('#box__' + id_peserta).fadeOut();
          } else {
            alert(a)
          }
        }
      })

    })
  })
</script>