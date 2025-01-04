<?php
login_only();
include 'proyek_akhir-styles.php';

$aksi = $_GET['aksi'] ?? '';
$lokasi_proyek = "uploads/__proyek";
if ($aksi == 'lihat_bukti') {
  include 'proyek_akhir-lihat_bukti.php';
} else {

  if (!is_dir($lokasi_proyek)) mkdir($lokasi_proyek);
  $target_kelas = $id_role == 1 ? $kelas : $target_kelas;
  $img_loading =  "<img src='assets/img/gif/loading.gif' style='width: 20px;'>";

  # ============================================================
  # PROCESSORS 
  # ============================================================
  include 'proyek_akhir-processors.php';



  # ============================================================
  # MAIN SELECT
  # ============================================================
  set_h2('Proyek Akhir', "<div>Room $room[nama]</div>$target_kelas");
  if ($id_role == 2) include 'proyek_akhir-manage.php';
  $img_next = img_icon('next');
  $img_reject = img_icon('reject');


  # ============================================================
  # SUB PROYEK
  # ============================================================
  $s = "SELECT * FROM tb_indikator WHERE id_room=$id_room";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $arr_fitur = [];
  if (mysqli_num_rows($q)) {
    while ($d = mysqli_fetch_assoc($q)) {
      $arr_fitur[$d['id']] = [
        'indikator' => $d['indikator'],
        'title' => $d['title'],
      ];
    }

    # ============================================================
    # PESERTA PADA ROOM INI
    # ============================================================
    $sql_target_kelas = $target_kelas ? "b.kelas = '$target_kelas'" : 1;
    $s = "SELECT 
    b.kelas,  
    d.username,  
    d.id as id_peserta,
    d.nama as nama_peserta,
    (
      SELECT nama FROM tb_proyek 
      WHERE id_peserta=d.id 
      AND id_room=$id_room) judul_proyek,
    (
      SELECT COUNT(1) FROM tb_bukti_proyek p 
      JOIN tb_indikator p2 ON p.id_indikator=p2.id
      WHERE p.id_peserta=d.id 
      AND p2.id_room=$id_room) count_bukti
    
    FROM tb_room_kelas a 
    JOIN tb_kelas b ON a.kelas=b.kelas 
    JOIN tb_kelas_peserta c ON b.kelas=c.kelas 
    JOIN tb_peserta d ON c.id_peserta=d.id
    
    WHERE a.id_room = $id_room 
    AND a.ta = $ta 
    AND d.status = 1 -- _peserta aktif
    -- AND d.id_role = 1 -- _peserta 
    AND $sql_target_kelas
    ORDER BY b.kelas, d.nama  
    ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

    $tr = '';
    $i = 0;
    $input_nama_proyek = '';
    while ($d = mysqli_fetch_assoc($q)) {
      $i++;
      $bg_ganjil = $i % 2 == 0 ? 'bg-putih' : 'bg-abu';
      $nama = strtoupper($d['nama_peserta']);
      $login_as = $id_role == 2 ? "<a target=_blank href='?login_as&username=$d[username]'>$img_login_as</a>" : '';
      $status = '<div class="f12 miring abu">belum diverifikasi</div>';

      $input_nama_proyek = "<input class='form-control edit_nama_proyek mt2' id=edit_nama_proyek__$d[id_peserta] value='$d[judul_proyek]'>";



      # ============================================================
      # ARRAY BUKTI PROYEK
      # ============================================================
      $arr_bukti = [];
      if ($d['count_bukti']) {
        $s = "SELECT * FROM tb_bukti_proyek WHERE id_peserta=$d[id_peserta]";
        $q_bukti = mysqli_query($cn, $s) or die(mysqli_error($cn));
        while ($d_bukti = mysqli_fetch_assoc($q_bukti)) {
          $arr_bukti[$d_bukti['kode']] = $d_bukti;
        }
      }

      # ============================================================
      # PERSEN PROGRESS
      # ============================================================
      $count_fitur = count($arr_fitur);
      $persen = $d['count_bukti'] ? round(($d['count_bukti'] / $count_fitur) * 100) : 0;
      $icon = $d['count_bukti'] == $count_fitur ? img_icon('check') : $img_loading;
      $progress_of = $d['count_bukti'] ? "$d[count_bukti] of $count_fitur $icon" : '<i class=abu>belom</i>';
      $div_progress = "
        <div class='d-flex'>
          <div class='progress bordered' style='min-width: 150px;'>
            <div class='progress-bar bg-success' role='progressbar' style='width: $persen%' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100'></div>
          </div>
          <div class='ml2 f12 abu'>
            $progress_of
          </div>
        </div>
      ";

      # ============================================================
      # PROYEK SAYA
      # ============================================================
      if ($id_peserta == $d['id_peserta']) {
        # ============================================================
        # INPUT PROGRES
        # ============================================================
        $bg_ganjil = '';
        $border_mine = 'border-mine gradasi-kuning';
        $input_progres = '';
        $j = 0;
        if ($arr_fitur) {

          foreach ($arr_fitur as $id_indikator => $arr) {
            $j++;
            $gambar_bukti = '';
            $link_thumb_bukti = '';
            $gradasi = 'merah';
            $unique = "$id_peserta-$id_room-$arr[indikator]";


            if (isset($arr_bukti[$unique])) {
              $bukti = $arr_bukti[$unique]['bukti'];
              $gambar_bukti = "<img src='$lokasi_proyek/$bukti' class='w100'>";
              $link_thumb_bukti = "
                <a target=_blank onclick='return confirm(`Buka gambar?`)' href='$lokasi_proyek/$bukti'>
                  <img src='$lokasi_proyek/thumb-$bukti' class='w100'>
                </a>
              ";
              $gradasi = 'hijau';
            }
            $input_progres .= "
              <div class='wadah mt2 gradasi-$gradasi'>
                <div class='f14 abu mb1 mt1'>$j. $arr[title]:</div>
                $link_thumb_bukti
                <form method=post class='flexy' enctype='multipart/form-data'>
                  <div>
                    <input required type=file class='form-control mt1' name=$arr[indikator] accept='.jpg,.jpeg'>
                  </div>
                  <div>
                    <button class='btn btn-primary btn-sm mt1' name=btn_upload value=$id_indikator>Upload</button>
                  </div>
                </form>
              </div>
            ";
          }
          $blok_proyek = $input_progres ? "$input_nama_proyek$input_progres" : $input_nama_proyek;
        } else {
          $blok_proyek = div_alert('danger', "Tidak ada [ Indikator Proyek ] yang diminta dari $Trainer. Silahkan hubungi beliau untuk kejelasannya!");
        }
      } else {
        # ============================================================
        # PROYEK PESERTA LAIN
        # ============================================================
        $border_mine = '';
        $blok_proyek = "
          $d[judul_proyek]
        ";
      }

      # ============================================================
      # LOOP STATUS HANDLER
      # ============================================================
      if ($id_role == 2) {
        $status = "
          $img_check 
          $img_reject 
          $img_next 
        ";
      }

      $tr .= "
        <div class=''>
          <div class='row $bg_ganjil pt4 pb4 $border_mine'>
            <div class='col-md-4'>
              $i. $nama $login_as
              <div class='f12 abu miring'>$d[kelas]</div>
            </div>
            <div class='col-md-4'>
              <div class='mt2'>
                $div_progress
              </div>
              $blok_proyek
            </div>
            <div class='col-md-4 f12'>
              <b>Status:</b> $status
            </div>
          </div>
        </div>
      ";
    }

    echo "
      $tr
    ";
  } else {
    echo div_alert('info', "<span class=red>Belum ada Proyek Akhir.</span><hr>Tidak ada satu pun indikator pada $Room ini atau Trainer belum merencanakannya.");
  }

?>
  <script>
    let cnama_proyek = '';

    $(function() {
      $(".edit_nama_proyek").focusout(function() {
        let nama_proyek = $(this).val();
        if (!nama_proyek) return;
        if (cnama_proyek == nama_proyek) return;

        let tid = $(this).prop('id');
        let rid = tid.split('__');
        let aksi = rid[0];
        let id_peserta = rid[1];
        console.log(aksi, id_peserta);

        if (nama_proyek.length >= 3) {
          let link_ajax = `pages/proyek_akhir-ajax.php?nama_proyek=${nama_proyek}&id_peserta=${id_peserta}`

          $.ajax({
            url: link_ajax,
            success: function(a) {
              cnama_proyek = nama_proyek;
              alert(a)
            }
          })

        } else {
          alert('Nama Proyek minimal 3 karakter.');

        }

      })
    })
  </script>
<?php }
