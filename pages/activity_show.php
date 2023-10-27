<?php
# =====================================================================
# NOMOR LAT | TUGAS | CHAL SUDAH TERPILIH
# =====================================================================
$s = "SELECT 
a.tanggal,
a.no,
a.id as id_jenis,
c.nama,
c.ket,
c.basic_point,
c.ontime_point,
c.ontime_dalam,
c.ontime_deadline,
c.ket,
b.id as id_sesi,
b.wag, 
b.no as no_sesi, 
(SELECT id FROM tb_bukti_$jenis WHERE id_peserta=$id_peserta AND id_$jenis=a.id) as id_bukti
FROM tb_assign_$jenis a 
JOIN tb_sesi b ON a.id_sesi=b.id 
JOIN tb_act_$jenis c ON a.id_$jenis=c.id 
WHERE a.no=$no 
AND kelas='$kelas'
";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0) die(section(div_alert('danger',"Maaf, $jenis dengan nomor $no tidak ada.<hr><a class=proper href='?activity&jenis=$jenis'>Pilih $jenis</a>")));
if(mysqli_num_rows($q)>1) die(erid('no::duplicate'));
$d=mysqli_fetch_assoc($q);


$id_jenis = $d['id_jenis'];
$id_sesi = $d['id_sesi'];
$id_bukti = $d['id_bukti'];
$tanggal_jenis = $d['tanggal'];

$pada_wag = "<a href='$d[wag]' target=_blank>Lihat Pada Whatsapp Group P$d[no_sesi]</a>";

$hasil = '<div class="kecil miring merah">kamu belum mengerjakan.</div>';
if($id_bukti!=''){
  $s = "SELECT a.*,b.no as no_lat, 
  (SELECT nama FROM tb_peserta WHERE id=a.verified_by) as verified_by_name 
  FROM tb_bukti_$jenis a 
  JOIN tb_assign_$jenis b ON a.id_$jenis=b.id 
  WHERE a.id=$id_bukti";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $d_lat = mysqli_fetch_assoc($q);
  $tanggal_upload = $d_lat['tanggal_upload'];
  $get_point = $d_lat['get_point'];
  $tanggal_verifikasi = $d_lat['tanggal_verifikasi'];
  $verified_by_name = $d_lat['verified_by_name'];
  $status = $d_lat['status'];
  $alasan_reject = $d_lat['alasan_reject'];

  $form_hapus = "
    <form method=post>
      <input class=debug name=id_bukti value=$id_bukti>
      <input class=debug name=no_jenis value=$d[no]>
      <button class='btn btn-danger btn-block proper' name=btn_hapus onclick='return confirm(\"Yakin untuk menghapus dan upload kembali bukti $jenis?\")'>Hapus bukti $jenis</button>
    </form>
  ";

  if($tanggal_verifikasi!='' AND $status==1){
    $verif_icon = "<img src='$img_check_path' height=25px>";
    $verif_opsi = div_alert('success',"Bukti kamu sudah terverifikasi oleh $verified_by_name pada $tanggal_verifikasi");
  }elseif($status==-1){
    $verif_icon = "<span class='red kecil miring'>(rejected :: $alasan_reject)</span>"; 
    $verif_opsi = div_alert('danger',"Maaf, bukti kamu ditolak dengan alasan $alasan_reject.$form_hapus");
  }else{
    $verif_icon = '<span class="red kecil miring">(belum diverifikasi)</span>';
    $verif_opsi = "Bukti kamu belum diverifikasi, kamu masih boleh menghapusnya.$form_hapus";
  }
  

  if($jenis=='latihan' || $jenis=='tugas'){
    $jpg = $jenis=='latihan' ? 'jpg' : 'zip';
    $path_file = "uploads/$folder_uploads/$jenis$d[no].$jpg";
    $img_or_zip = $jenis=='latihan' ? "
      <div class=mb2 style=margin-left:-30px>
        <a href='$path_file' target=_blank onclick='return confirm(\"Buka gambar di Tab baru?\")'>
          <img src='$path_file' class='img-fluid'>
        </a>
        <div class=mt2>$verif_opsi</div>
      </div>
    " : "
      <a href='$path_file' target=_blank onclick='return confirm(\"Download file ZIP?\")'>Download ZIP File</a>
      <div class=mt2>$verif_opsi</div>
    ";

    $scr = $img_or_zip ;
  }else if($jenis=='challenge'){
    $scr = "<a href='$d_lat[link]' target=_blank>$d_lat[link]</a>";
  }else{
    die("Jenis activity: $jenis unhandled action.");
  }
  
  $menit = round((strtotime($tanggal_upload)-strtotime($tanggal_jenis))/60,0);
  $jam = intval($menit/60);
  $sisa_menit = $menit % 60;

  $menit_show = $jam ? "$jam jam $sisa_menit menit" : "$sisa_menit menit";

  $tanggal_upload_show = date('d/m/y H:i',strtotime($tanggal_upload));

  $screenshoot = $jenis=='latihan' ? 'Screenshoot' : 'Link bukti '.$jenis;
  $hasil = "
  <ul>
    <li><b class=darkblue>Get Point: $get_point LP</b> $verif_icon</li>
    <li class=kecil>Tanggal Upload: $tanggal_upload_show</li>
    <li class=kecil>Dikerjakan dalam $menit_show</li>
    <li class=kecil>$screenshoot: $scr</li>
  </ul>
  ";
}

$btn_hapus = '';
if($status==-1 and $jenis=='challenge'){
  $btn_hapus = "
  <form method=post>
    <input class=debug name=id_bukti value=$id_bukti>
    <input class=debug name=no_jenis value=$d[no]>
    <button class='btn btn-danger btn-sm' name=btn_hapus  id=challenge__$id_jenis onclick='return confirm(\"Yakin untuk hapus Challenge dan Reupload kembali?\")'>Hapus dan Reupload</button>
  </form>
  ";
}

$hasil = "<div class='wadah'><div>Hasil $jenis:</div>$hasil$btn_hapus</div>";

$info_ekstensi = [
  'latihan' => 'ekstensi harus JPG, jika tugas coding posisikan bukti screenshoot: kiri code, kanan hasil',
  'tugas' => 'ekstensi harus ZIP, masukan semua file web dan file database (SQL) kamu ke file ZIP, lalu upload!',
  'challenge' => 'harus berupa link-online diawali dg http atau https, misal: http://iin-sholihin.github.io, https://insho.rf.gd',
];

$accept_ekstensi = [
  'latihan' => '.jpg,.jpeg',
  'tugas' => '.zip',
  'challenge' => '',
];

$input_type = [
  'latihan' => 'file',
  'tugas' => 'file',
  'challenge' => 'text minlength=15 maxlength=100',
];

$btn_upload = $id_role!=3 ? "<button class='btn btn-primary btn-block' name=btn_upload>Upload</button>" : "<span class='btn btn-primary btn-block' onclick='alert(\"Anda login sebagai Supervisor! Terima kasih sudah mencoba upload.\")'>Upload</span>";

$form = ($id_bukti!='') ? div_alert('success','Kamu sudah mengerjakan.') : "
<form method=post enctype=multipart/form-data>
  Bukti kamu mengerjakan:
  <div class=mb2>
    <input class=debug name=id_jenis value='$id_jenis'>
    <input class=form-control type=$input_type[$jenis] name=bukti accept='$accept_ekstensi[$jenis]' required>
    <div class='kecil miring abu'>)* $info_ekstensi[$jenis].</div>
  </div>
  $btn_upload
</form>
";

$disabled = $id_role==2 ? '' : 'disabled';
$tanggal_jenis_show = $id_role==2 
? "<input $disabled class='input_editable ' id=tanggal_$jenis"."__$id_jenis value='$d[tanggal]'> <button class='btn btn-success btn-sm btn-block mt1 mb2' id=set_now>Set Now</button>"
: date('d/m/y H:i',strtotime($d['tanggal']));


$main_block = "
<div class='wadah gradasi-hijau' data-aos=fade-up>
  $pesan_upload
  <div class=debug>
    no2:<span id=no2__$id_jenis>$d[no]</span> | 
    nama2:<span id=nama2__$id_jenis>$d[nama]</span> | 
    ket2:<span id=ket2__$id_jenis>$d[ket]</span> | 
    tanggal_".$jenis."2:<span id=tanggal_$jenis"."2__$id_jenis>$d[tanggal]</span> | 
    basic_point2:<span id=basic_point2__$id_jenis>$d[basic_point]</span> | 
    ontime_point2:<span id=ontime_point2__$id_jenis>$d[ontime_point]</span> | 
    ontime_dalam2:<span id=ontime_dalam2__$id_jenis>$d[ontime_dalam]</span> | 
    ontime_deadline2:<span id=ontime_deadline2__$id_jenis>$d[ontime_deadline]</span> | 
  </div>
  <h3 class='mb4 proper'>$jenis <input $disabled class='input_editable tiga_digit' id=no__$id_jenis value='$d[no]'><span class=debug>id_jenis:<span id=id_jenis>$d[id_jenis]</span> | id_sesi:<span id=id_sesi>$d[id_sesi]</span></span></h3>
  <div class=proper>Nama $jenis: <input $disabled class='form-control input_editable ' id=nama__$id_jenis value='$d[nama]'></div>
  <div class='mt1 mb2'>
    <textarea class='form-control input_editable' id=ket__$id_jenis $disabled>$d[ket]</textarea>
  </div>
  <ul class='kecil miring consolas'>
    <li><b class=label_editable>Tanggal mulai</b>: $tanggal_jenis_show</li>
    <li><b class=label_editable>Basic Point</b>: <input $disabled id=basic_point__$id_jenis class='input_editable tiga_digit' value='$d[basic_point]'> LP</li>
    <li><b class=label_editable>Ontime Point</b>: <input $disabled id=ontime_point__$id_jenis class='input_editable tiga_digit' value='$d[ontime_point]'> LP</li>
    <li><b class=label_editable>Ontime dalam</b>: <input $disabled id=ontime_dalam__$id_jenis class='input_editable tiga_digit' value='$d[ontime_dalam]'> menit</li>
    <li><b class=label_editable>Ontime deadline</b>: <input $disabled id=ontime_deadline__$id_jenis class='input_editable tiga_digit' value='$d[ontime_deadline]'> menit</li>
  </ul>
  $form 
  $hasil
</div>";












?>
<style>
.tiga_digit{width:50px;text-align:center;font-family:consolas}
.label_editable{display:inline-block; width:120px;}
</style>
<!-- <div class='gradasi-kuning text-center container' style='position:sticky; top:65px; z-index:999; padding:10px'>
  <a href='?verif'>Verifikasi Aktifitas Peserta zz<span class='badge badge-danger'>7</span></a>
</div> -->
<section id="activity" class="activity">
  <div class="container">

    <div class="section-title" data-aos="fade-up">
      <h2 class=proper><?=$jenis?></h2>
      <p><?=$yaitu?></p>
    </div>


    <?=$main_block?>

  
    <?php
    if($id_role>=2){

      $verif_icon = $_GET['verif'] ?? '';

      $kolom_link = $jenis=='challenge' ? 'link' : '0';
      $s = "SELECT a.id,a.folder_uploads,a.nama,
      (SELECT id FROM tb_bukti_$jenis WHERE id_peserta=a.id AND id_$jenis=$id_jenis) id_jenis, 
      (SELECT 1 FROM tb_bukti_$jenis WHERE id_peserta=a.id AND id_$jenis=$id_jenis) sudah_mengerjakan, 
      (SELECT 1 FROM tb_bukti_$jenis WHERE id_peserta=a.id AND id_$jenis=$id_jenis AND tanggal_verifikasi is null) belum_verif, 
      (SELECT 1 FROM tb_bukti_$jenis WHERE id_peserta=a.id AND id_$jenis=$id_jenis AND tanggal_verifikasi is not null AND status=-1) kena_reject, 
      (SELECT $kolom_link FROM tb_bukti_$jenis WHERE id_peserta=a.id AND id_$jenis=$id_jenis) link  
      FROM tb_peserta  a 
      WHERE a.id_role=1  
      AND status = 1 
      ORDER BY a.nama
      ";
      // echo $s;
      $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
      $rsudah = [];
      $rbelum = [];
      $runverif = [];
      $rreject = [];
      $rid = [];
      $rid_jenis = [];
      $rfu = [];
      $rlink = [];
      while ($d=mysqli_fetch_assoc($q)) {
        $folder_uploads = $d['folder_uploads'];
        $nama = ucwords(strtolower($d['nama']));
        if($d['sudah_mengerjakan']){
          array_push($rsudah,$nama);
          if($d['belum_verif']){
            array_push($rid,$d['id']);
            array_push($rid_jenis,$d['id_jenis']);
            array_push($runverif,$nama);
            array_push($rfu,$folder_uploads);
            if($jenis=='challenge'){
              $rlink[$d['id']] = $d['link'];
            }
          } 
          if($d['kena_reject']){
            array_push($rreject,$nama);
          }
        }else{
          array_push($rbelum,$nama);
        }
      }

      
      $sudah = ''; foreach ($rsudah as $key => $value) $sudah .= ($key+1).". $value; ";
      $belum = ''; foreach ($rbelum as $key => $value) $belum .= ($key+1).". $value; ";
      $reject = ''; foreach ($rreject as $key => $value) $reject .= ($key+1).". $value; ";


      $unverif = ''; 
      foreach ($runverif as $key => $value){
        if($jenis=='latihan'){
          $path_bukti = "uploads/$rfu[$key]/$jenis$no.jpg";
          $bukti_show = file_exists($path_bukti) ? "<img src='$path_bukti' class=img-fluid>" : "$path_bukti :: Bukti gambar tidak ada. $pada_wag";
        }elseif($jenis=='tugas'){
          $path_bukti = "uploads/$rfu[$key]/$jenis$no.zip";
          $bukti_show = file_exists($path_bukti) ? "<a href='$path_bukti' target=_blank>Download ZIP File</a>" : "$path_bukti :: Bukti file ZIP tidak ada. $pada_wag";
        }elseif($jenis=='challenge'){
          $link = $rlink[$rid[$key]];
          $bukti_show = "Challenge's Link : <a href='$link' target=_blank>$link</a>";
        }

        $btn_accept = $id_role!=3 ? "<button class='btn btn-success btn-sm btn_aksi btn-block mb1' id=accept__$rid_jenis[$key]>Accept</button>" : "<button class='btn btn-success btn-sm btn-block mb1' onclick='alert(\"Anda Login sebagai Supervisor! Terimakasih sudah mencoba Accept $jenis dari Peserta. Poin peserta akan direkap jika sudah terverifikasi oleh instruktur.\")'>Accept</button>";
        
        $btn_reject = $id_role!=3 ? "<button class='btn btn-danger btn-sm btn_aksi btn-block mb1' id=reject__$rid_jenis[$key]>Reject</button>" : "<button class='btn btn-danger btn-sm btn-block mb1' onclick='alert(\"Anda Login sebagai Supervisor! Terimakasih sudah mencoba Reject $jenis dari Peserta. Reject wajib disertai dengan alasan reject agar peserta segera re-upload revisi $jenis-nya.\")'>Reject</button>";
        
        $img_or_zip = $verif_icon ? "
          <div class=wadah id=blok_bukti__$rid_jenis[$key]>
            $bukti_show 
            <div class='row mt-2'> 
              <div class='col-sm-6'>
                $btn_accept
              </div>
              <div class=col-sm-6>
                $btn_reject
              </div>
            </div>
          </div>
        " : '';
        $unverif .= ($key+1).". $value; <span class=debug>id_bukti: $rid_jenis[$key]</span> $img_or_zip<br>";
      } 

      $btn = ($verif_icon=='' and count($runverif)>0) ? "<a href='?activity&jenis=$jenis&no=$no&verif=1' class='proper btn btn-success btn-block'>Show Image / Bukti $jenis</a>" : '';

      $belum_diverif = count($runverif)==0 ? '<span class="green miring">-- all verified --</span> ' : '<div class="tebal darkred">Belum diverifikasi '.count($runverif).' peserta:</div>';

      $info_reject = count($rreject) ? "<div class='wadah red'>Rejected: $reject</div>" : '';

      echo '<div class=wadah data-aos=fade-up><div class="tebal biru">Dikerjakan oleh '.count($rsudah).' peserta:</div>'.$sudah.$info_reject.'</div>';
      echo '<div class=wadah data-aos=fade-up>'.$belum_diverif.$unverif.$btn.'</div>';
      echo '<div class=wadah data-aos=fade-up><div class="tebal red">Belum mengerjakan '.count($rbelum).' peserta:</div>'.$belum.'</div>';

    } 
    ?>

  </div>
</section>














<script>
  $(function(){
    let jenis = $('#jenis').text();
    let id_sesi = $('#id_sesi').text();
    let id_jenis = $('#id_jenis').text();
    // alert(id_jenis);

    $('#set_now').click(function(){
      // alert(1)
      let nd = new Date();
      let y = nd.getFullYear();
      let m = nd.getMonth()+1;
      let d = nd.getDate();
      let h = nd.getHours();
      let i = nd.getMinutes();
      // console.log(y,m,d,h,i)

      let z = confirm('Isi tanggal mulai ke saat ini?'); 
      if(!z) return;

      $('#tanggal_'+jenis+'__'+id_jenis).val(`${y}-${m}-${d} ${h}:${i}`);
    })

    $('.input_editable').focusout(function(){
      // alert($(this).prop('id'))
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let kolom = rid[0];
      let id_jenis = rid[1];

      let isi_lama = $('#'+kolom+'2__'+id_jenis).text();
      let isi_baru = $(this).val().trim();
      if(isi_lama==isi_baru) return;
      if(isi_baru==''){
        let y = confirm('Ingin mengosongkan data?');
        if(!y){
          // console.log(isi_lama);
          $('#'+tid).val(isi_lama);
          return;
        }
        // $('#'+tid).val(isi_lama);
      }
      let aksi = 'ubah';
      let link_ajax = `ajax/ajax_crud_jenis.php?aksi=${aksi}&id=${id_jenis}&kolom=${kolom}&isi_baru=${isi_baru}&id_sesi=${id_sesi}&jenis=${jenis}`
      // alert(link_ajax);
      $.ajax({
        url:link_ajax,
        success:function(a){
          if(a.trim()=='sukses'){
            $('#'+tid).addClass('gradasi-hijau biru');
            $('#'+kolom+'2__'+id_jenis).text(isi_baru);
          }else{
            alert(a)
          }
        }
      })

    })

    $('.btn_aksi').click(function(){
      // alert($(this).prop('id'))
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];

      let alasan_reject='';
      if(aksi=='reject'){
        alasan_reject = prompt('Alasan Reject (min 20 char):','Tidak sesuai request. Silahkan baca keterangan '+jenis+' dengan baik.').replace(/['"]/gim,'');
        if(alasan_reject.length<20){
          alert('Silahkan masukan alasan reject minimal 20 karakter.');
          return;
        }
      }else if(aksi=='accept'){
        let y = confirm('Ingin verifikasi (accept) tugas ini?');
        if(!y) return; 
      }else{
        alert('Unhandle aksi: '+aksi);
        return;
      }

      let link_ajax = `ajax/ajax_verif_bukti_jenis.php?aksi=${aksi}&id=${id}&jenis=${jenis}&alasan_reject=${alasan_reject}`
      // alert(link_ajax);
      $.ajax({
        url:link_ajax,
        success:function(a){
          if(a.trim()=='sukses'){
            let h = aksi=='accept' ? '<span class="hijau tebal">Anda telah accept bukti ini.</span>' : '<span class="red">Anda telah reject bukti ini dengan alasan: <quote class=miring>'+alasan_reject+'</quote>.</span>';
            $('#blok_bukti__'+id).html(h);
          }else{
            alert(a)
          }
        }
      })

    })    
  })
</script>