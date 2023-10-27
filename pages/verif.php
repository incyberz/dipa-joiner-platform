<?php
if($id_role<=1) die('<script>location.replace("?")</script>');

$kelas = $_GET['kelas'] ?? '';
if($kelas==''){


  $s = "SELECT kelas FROM tb_kelas";
  $links = '';
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  while ($d=mysqli_fetch_assoc($q)) {
    $links .= "<a class='btn btn-primary btn-sm' href='?verif&kelas=$d[kelas]'>$d[kelas]</a> ";
  }

  die("
    <section class='about'>
      <div class='container'>
        <div class='mb2'>Silahkan pilih kelas:</div>
        $links

      </div>
    </section>

  ");
}



$o='';
$s = "SELECT 1 FROM tb_peserta WHERE status=1 and id_role=1 and kelas='$kelas'";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jpa = mysqli_num_rows($q);
$o .= "<div class=mb2 data-aos=fade-up >Jumlah peserta aktif <span class=darkred>$kelas</span> : $jpa peserta.</div>";

$img_check_path = 'assets/img/icons/check.png';
$rjenis = ['latihan','tugas','challenge'];
foreach ($rjenis as $key => $jenis){
  $s = "SELECT a.no,b.*, 
  (SELECT count(1) FROM tb_bukti_$jenis WHERE id_$jenis=a.id)  as jumlah_submiter,
  (SELECT count(1) FROM tb_bukti_$jenis WHERE id_$jenis=a.id AND tanggal_verifikasi is not null AND status=1)  as jumlah_verif,
  (SELECT count(1) FROM tb_bukti_$jenis WHERE id_$jenis=a.id AND tanggal_verifikasi is not null AND status=-1)  as jumlah_reject
  FROM tb_assign_$jenis a 
  JOIN tb_act_$jenis b ON a.id_$jenis=b.id 
  WHERE a.kelas='$kelas' 
  ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  
  $last_jenis = '';
  $delay = 0;
  $i = 0;
  if(mysqli_num_rows($q)){
    while ($d=mysqli_fetch_assoc($q)) {
      $i++;

      $jumlah_submiter = $d['jumlah_submiter']>$jpa ? $jpa : $d['jumlah_submiter'];
      $jumlah_verif = $d['jumlah_verif'];
      $jumlah_reject = $d['jumlah_reject'];
      $jumlah_unverif = $jumlah_submiter-$jumlah_verif-$jumlah_reject;

      if($jumlah_submiter==$jumlah_verif){
        $jumlah_show = '<span class="hijau miring">all verified</span>';
      }elseif($jumlah_unverif>0 and $jumlah_reject>0){
        $jumlah_show = "<span class='hijau miring'>$jumlah_verif verified</span>";
        $jumlah_show .= " :: <span class='red miring'>$jumlah_unverif unverified</span>";
        $jumlah_show .= " :: <span class='darkred miring'>$jumlah_reject rejected</span>";
      }elseif($jumlah_unverif>0){
        $jumlah_show = "<span class='hijau miring'>$jumlah_verif verified</span>";
        $jumlah_show .= " :: <span class='red miring'>$jumlah_unverif unverified</span>";
      }elseif($jumlah_reject>0){
        $jumlah_show = "<span class='hijau miring'>$jumlah_verif verified</span>";
        $jumlah_show .= " :: <span class='darkred miring'>$jumlah_reject rejected</span>";
      }

      $persen_submiter = round($jumlah_submiter/$jpa*100,0);
      $green = $persen_submiter==100 ? 'hijau' : 'darkred';
      $jumlah_show = $persen_submiter==0 ? '<span class="abu miring">none</span>' : $jumlah_show;

      $judul = $last_jenis==$jenis ? '' : "<div class='tebal darkblue proper mb2 '>verifikasi $jenis</div>";
      $delay = $last_jenis==$jenis ? $delay : $delay += 300;
      $wadah = $last_jenis==$jenis ? '' : "<div class=wadah data-aos=fade-up data-aos-delay=$delay>";
      $end_wadah = ($last_jenis==$jenis or $last_jenis=='') ? '' : '</div>';

      echo "<h1>$last_jenis==$jenis</h1>";

      $grad = $persen_submiter>=50 ? '#faf,#f5f' : '#faa,#f55';
      $grad = $persen_submiter>=75 ? '#aaf,#55f' : $grad;
      $grad = $persen_submiter>=90 ? '#9d9,#3d3' : $grad;

      $o .= "
        $end_wadah$wadah$judul$d[no]. 
        <a href='?activity&jenis=$jenis&no=$d[no]'>$d[nama]</a> : 


        <div class='ml4 mb2 kecil'>
          <span class=$green>$jumlah_submiter ($persen_submiter%) submiter</span> 
          :: 
          $jumlah_show
          <div class='progress'>
            <div class='progress-bar' role=progressbar aria-valuenow=$persen_submiter aria-valuemin=0 aria-valuemax=100 style='width:$persen_submiter%; background: linear-gradient($grad)'>
            </div>
          </div> 
        </div>
      ";
      $last_jenis = $jenis;
    } //end while
  }else{
    $o.= "<div class='mb4 mt4 abu miring wadah' data-aos=fade-left data-aos-delay=300>Verifikasi $jenis belum ada.";
  }

  $o .= '</div>';
} //end foreach

?>
<!-- <style>
.tiga_digit{width:50px;text-align:center;font-family:consolas}
.label_editable{display:inline-block; width:120px;}
</style> -->
<section class="about">
  <div class="container">

    <div class="section-title" data-aos="fade-up">
      <h2 class=proper>Verifikasi</h2>
    </div>

    <?=$o?>

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
  })
</script>