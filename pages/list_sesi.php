<style>
.no_sesi{font-size:40px; color:darkred}
.nama_sesi{font-size:14px;font-weight:bold;color:darkblue}
.text-right{text-align:right}
.fixed{position:fixed; top:90px;right:15px;background:yellow; border-radius:10px; padding:10px;z-index:999}
</style>
<?php
$judul = 'List Sesi';
$edit_mode = $_GET['edit_mode'] ?? '';
if($id_role==2 and $edit_mode){
  $toggle_edit = '<div class=fixed><a class="btn btn-danger btn-sm" href="?list_sesi">Exit Edit Mode</a></div>';
}else if($id_role==2 and !$edit_mode){
  $toggle_edit = '<div class=fixed><a class="btn btn-success btn-sm" href="?list_sesi&edit_mode=1">Edit Mode</a></div>';
}else{
  $toggle_edit = '';
}
  


$s = "SELECT * FROM tb_assign_latihan WHERE kelas='$kelas' ORDER BY no ";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
while ($d=mysqli_fetch_assoc($q)) {
  if(isset($rlats[$d['id_sesi']])){
    array_push($rlats[$d['id_sesi']],$d['no']);
  }else{
    $rlats[$d['id_sesi']][0] = $d['no']; 
  }
}

$s = "SELECT * FROM tb_assign_tugas WHERE kelas='$kelas' ORDER BY no ";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
while ($d=mysqli_fetch_assoc($q)) {
  if(isset($rtugas[$d['id_sesi']])){
    array_push($rtugas[$d['id_sesi']],$d['no']);
  }else{
    $rtugas[$d['id_sesi']][0] = $d['no']; 
  }
}

$s = "SELECT * FROM tb_assign_challenge WHERE kelas='$kelas' ORDER BY no ";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
while ($d=mysqli_fetch_assoc($q)) {
  if(isset($rchallenge[$d['id_sesi']])){
    array_push($rchallenge[$d['id_sesi']],$d['no']);
  }else{
    $rchallenge[$d['id_sesi']][0] = $d['no']; 
  }
}

// echo '<pre>';
// var_dump($rlats);
// echo '</pre>';

$s = "SELECT * FROM tb_sesi ORDER BY no";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$tr='';
$i=0;
while ($d=mysqli_fetch_assoc($q)) {
  $i++;
  $id=$d['id'];

  $latihans = '';
  if(isset($rlats[$i])){
    foreach ($rlats[$i] as $key => $value) {
      $latihans .= "<a href='?activity&jenis=latihan&no=$value' class='btn btn-success btn-sm' onclick='return confirm(\"Menuju laman Latihan?\")'>L$value</a> ";
    }
  }

  $tugas = '';
  if(isset($rtugas[$i])){
    foreach ($rtugas[$i] as $key => $value) {
      $tugas .= "<a href='?activity&jenis=tugas&no=$value' class='btn btn-primary btn-sm' onclick='return confirm(\"Menuju laman Tugas?\")'>T$value</a> ";
    }
  }

  $challenges = '';
  if(isset($rchallenge[$i])){
    foreach ($rchallenge[$i] as $key => $value) {
      $challenges .= "<a href='?activity&jenis=challenge&no=$value' class='btn btn-danger btn-sm' onclick='return confirm(\"Menuju laman Challenge?\")'>C$value</a> ";
    }
  }

  if($d['tags']!=''){
    $r = explode(', ',$d['tags']);
    sort($r);
    $tags_show = '<span class="darkblue kecil miring">'.implode(', ',$r).'</span>';
    $asks = "<a href='?bertanya&id_sesi=$id' style='display:inline-block;margin-left:10px' onclick='return confirm(\"Ingin mengajukan pertanyaan pada sesi ini?\")'><img src='assets/img/icons/ask.png' class=zoom height=30px></a>";
  }else{
    $tags_show = '<span class="red kecil miring">belum ada tags</span>';
    $asks = '';
  }

  $selisih = strtotime('now') - strtotime($d['tanggal']);
  $hijau = $selisih>0 ? 'kuning' : 'hijau';
  $lampau = $selisih>0 ? '<code class=miring>(sesi lampau)</code>' : '';
  $disabled = $selisih>0 ? 'disabled' : '';

  if($id_role==2 and $edit_mode){
    $r = explode(', ',$d['tags']); sort($r);
    $tags_sort = implode(', ',$r);
    $nama_show = "$lampau<input class='form-control input_editable mb1' name=nama id=nama__$id value='$d[nama]' $disabled>";
    $ket_show = "<textarea class='form-control input_editable mb1' name=ket id=ket__$id>$d[ket]</textarea>";
    $tags_show = "<input class='form-control input_editable mb1' name=tags id=tags__$id value='$tags_sort'>";
    $tanggal_show = "pelaksanaan:<input class='form-control input_editable mb1' name=tanggal id=tanggal__$id value='$d[tanggal]'  $disabled>durasi (menit):<input class='form-control input_editable mb1' name=durasi id=durasi__$id value='$d[durasi]' $disabled>";
    $fitur_sesi = '';
  }else{
    $nama_show = $d['nama']." $lampau";
    $ket_show = $d['ket'];
    $tanggal_show = date('D, d M Y H:i',strtotime($d['tanggal'])).' :: '.$d['durasi'].' menit';
    $tags_show = $tags_show;
    $fitur_sesi = "$latihans $tugas $challenges $asks";
  }
  
  $debug = '';

  $tr.= "
    <div class='wadah gradasi-$hijau' data-aos='fade-up' style='display:grid;grid-template-columns: 100px auto;grid-gap:10px'>
      <div class='text-center wadah bg-white'>
        sesi 
        <div class='no_sesi'>$i</div>
      </div>
      <div>
        $debug 
        <div class=nama_sesi>$nama_show</div>
        <div class='kecil miring abu'>$ket_show</div>
        <div class='kecil miring abu'>tags : $tags_show</div>
        <div class='kecil miring abu'>$tanggal_show</div>
        <div class='mt1'>$fitur_sesi</div>
      </div>
    </div>
  ";
}
$list = "<div>$toggle_edit$tr</div>";
















?>
<section id="about" class="about">
  <div class="container">

    <div class="section-title" data-aos="fade-up">
      <h2><?=$judul?></h2>
      <p>Berikut adalah Sesi-sesi Perkuliahan Matematika Informatika</p>
    </div>

    <?=$list?>
  </div>
</section>


































<?php if($edit_mode) { ?>
<script>
  $(function(){
    $('.input_editable').focusout(function(){
      // alert($(this).prop('id'))
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let kolom = rid[0];
      let id = rid[1];

      let isi_lama = $('#'+kolom+'2__'+id).text();
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

      // manage tags
      if(kolom=='tags'){
        isi_baru = isi_baru
        .replace(/;/gim, ',')
        .replace(/[!@#$%^&*()+\-=\[\]{};:'`"\\|<>\/?~]/gim, '');
        let r = isi_baru.split(',');

        let r2 = [];
        r.forEach(el => {
          r2.push(el.trim().toLowerCase());
        });

        isi_baru = r2.sort().join(', ');
      }

      let aksi = 'ubah';
      let link_ajax = `ajax/ajax_crud_sesi.php?aksi=${aksi}&id=${id}&kolom=${kolom}&isi_baru=${isi_baru}`
      // alert(link_ajax);
      $.ajax({
        url:link_ajax,
        success:function(a){
          if(a.trim()=='sukses'){
            $('#'+tid).addClass('gradasi-hijau biru');
            $('#'+tid).val(isi_baru);
            $('#'+kolom+'2__'+id).text(isi_baru);
          }else{
            alert(a)
          }
        }
      })

    })
  })
</script>
<?php } ?>