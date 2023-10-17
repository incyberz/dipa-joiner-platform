<?php
// if($id_role<=1) die('<script>location.replace("?")</script>');
$img_delete = '<img class=zoom src="assets/img/icons/delete.png" height=20px>';

$sub_judul = $id_role==2 ? 'Berikut adalah pertanyaan dari para Peserta yang harus Anda tanggapi!' : "Halo $nama_peserta! Silahkan Anda bergabung pada chats berikut!";

$o='';

$s = "SELECT a.*, b.nama as penanya, c.nama as topik,
(SELECT count(1) FROM tb_jawaban WHERE id_pertanyaan=a.id) jumlah_penjawab   
FROM tb_pertanyaan a 
JOIN tb_peserta b ON a.id_penanya=b.id 
JOIN tb_sesi c ON a.id_sesi=c.id 
WHERE 1 ORDER BY tanggal DESC";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$count_all = mysqli_num_rows($q);
$count_ok=0;
$count_banned=0;
$count_closed=0;
$count_replied=0;
$count_unreply=0;

$li=div_alert('danger','Belum ada satu pun pertanyaan.');
$belum_dijawab = '--belum dijawab--';
$i=0;

if($count_all>0){
  $li='';
  while ($d=mysqli_fetch_assoc($q)) {
    $i++;
    $id = $d['id'];

    $ask_ok = '';    
    $ask_banned = '';    
    $ask_closed = '';    
    $ask_replied = '';    
    $ask_unreply = '';    
    if($d['verif_status']==1){
      $count_ok++;
      $ask_ok = 'ask_ok';
    }elseif($d['verif_status']==-1){
      $count_banned++;
      $ask_banned = 'ask_banned';
    }elseif($d['verif_status']==='0'){
      $count_closed++;
      $ask_closed = 'ask_closed';
    }

    $jumlah_penjawab = $d['jumlah_penjawab'];
    if($jumlah_penjawab){
      $count_replied++;
      $ask_replied = 'ask_replied';
    }else{
      if($ask_closed!='ask_closed' AND $ask_banned!='ask_banned'){
        $count_unreply++;
        $ask_unreply = 'ask_unreply';
      }
    }

    $tanggal_show = date('D, d M Y H:i',strtotime($d['tanggal']));
    $topik_show = ucwords(strtolower($d['topik']));

    $hideit = $ask_unreply=='ask_unreply' ? '' : 'hideit';

    if($ask_banned=='ask_banned'){
      $blok_reply = '<div class="red kecil miring consolas stop_ask stop_banned">--banned--</div>';
    }elseif($ask_closed=='ask_closed'){
      $blok_reply = '<div class="green kecil miring consolas stop_ask stop_closed">--closed--</div>';
    }else{
      $blok_reply = "
        <div class='$hideit' id=blok_reply_input__$id><input class='form-control reply_input' id=reply_input__$id></div>
        <span class='blue pointer per reply_toggle' id=reply_toggle__$id>Reply</span>
      ";
    }

    $debug = 0 ? '' : "<span class=debug>ask_ok:$ask_ok | ask_banned:$ask_banned | ask_closed:$ask_closed | ask_replied:$ask_replied | ask_unreply:$ask_unreply</span>"; 

    if($jumlah_penjawab){
      // ada reply
      $red_bold = 'green';
      $s2 = "SELECT a.*,b.nama as penjawab  
      FROM tb_jawaban a 
      JOIN tb_peserta b ON a.id_penjawab=b.id 
      WHERE id_pertanyaan=$id";
      $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));

      $li_jawab = '';
      while ($d2=mysqli_fetch_assoc($q2)) {
        $selisih = strtotime('now') - strtotime($d2['tanggal']);
        $selisih_m = intval($selisih/60);
        $selisih_h = intval($selisih_m/60);
        $selisih_d = intval($selisih_h/24);
        $selisih_m = $selisih_m % 60 ==0 ? '' : $selisih_m % 60 . 'm';
        $selisih_h = $selisih_h % 24 ==0 ? '' : $selisih_h % 24 . 'h';
        $selisih_d = $selisih_d == 0 ? '' : $selisih_d . 'd';
        $selisih_m = $selisih_d > 0 ? '' : $selisih_m;
        $selisih_jawab_show = $selisih<60 ? 'barusan' : "$selisih_d $selisih_h $selisih_m ago'"; //zzz
        $poin = $d2['poin'] ?? 0;
        $poin_show = $id_role==1 ? "$poin <span class='lp'>LP</span>" : "<span class='pointer per biru count_badge badge_pink set_poin' id=set_poin__$d2[id]>$poin</span> <span class='lp'>LP</span>";
        $delete = ($d2['id_penjawab']==$id_peserta || $id_role==2) ? "- <span class='delete_chat pointer darkred per' id=delete_chat__$d2[id]>$img_delete</span>" : '';
        $by = $d2['id_penjawab']==$id_peserta ? 'me' : ucwords(strtolower($d2['penjawab']));

        $li_jawab .= "
          <li id=li_jawab__$d2[id]>
            $d2[jawaban] - 
            <span class='abu'>$selisih_jawab_show</span> - 
            <a href='?peserta&id_peserta=$d2[id_penjawab]'>by $by</a> - 
            $poin_show  
            $delete
          </li>
        ";
      }

      $jawabans = "<ul class='wadah ul_jawabans' id=ul_jawabans__$id>$li_jawab</ul>";

      
      
    }else{
      $red_bold = 'red bold';
      $jawabans = $belum_dijawab;
    }

    $poin_pertanyaan = $d['poin'] ?? 0;
    $poin_pertanyaan_show = $id_role==1 
    ? "$poin_pertanyaan <span class='lp'>LP</span>" 
    : "<span class='pointer per biru count_badge badge_pink set_poin' id=set_poin_pertanyaan__$id>$poin_pertanyaan</span> <span class='lp'>LP</span>";
    $poin_pertanyaan_show = ($ask_banned=='ask_banned' || $ask_closed=='ask_closed') ? '' : $poin_pertanyaan_show;
    $delete_pertanyaan = ($d['id_penanya']==$id_peserta || $id_role==2) ? "<span class='delete_chat pointer darkred per' id=delete_pertanyaan__$id>$img_delete</span>" : '';
    $delete_pertanyaan = $ask_closed=='ask_closed' ? '' : $delete_pertanyaan;

    $set_close = "<span class='set_pertanyaan pointer darkred per green' id=set_close__$id>Close</span>";
    $set_banned = "<span class='set_pertanyaan pointer darkred per red' id=set_banned__$id>Banned</span>";
    $set_pertanyaan = ($id_role>=2) ? "$set_close | $set_banned" : '';

    $li .= "
      <div class='li_ask $ask_ok $ask_banned $ask_closed $ask_replied $ask_unreply '>
        $debug
        <div class=blok_pertanyaan id=blok_pertanyaan__$id>
          <div class='ask_from kecil miring abu'>
            <div>From: <a href='?peserta&id_peserta=$d[id_penanya]'>$d[penanya]</a> - $tanggal_show</div>
            <div>Topik: $topik_show</div>
            <div>$poin_pertanyaan_show</div>
            <div>$delete_pertanyaan</div>
            <div>$set_pertanyaan</div>
          </div>
          <div class='pertanyaan darkblue'><span id=pertanyaan__$id>$d[pertanyaan]</span><span class=debug> id:$id</span></div>
          <div class='kecil miring $red_bold'>
            <div class=jawabans>$jawabans</div> 
            $blok_reply
          </div>
        </div>
      </div>
    ";
  }
}

$nav = "
  <div class='tengah mb2 ' data-aos=fade-up>
  <span class='nav_item per ' id=nav_item__all>All<span class='count_badge badge_gray'>$count_all</span></span>  
    <span class='nav_item per nav_active' id=nav_item__unreply>Unreply<span class='count_badge badge_red'>$count_unreply</span></span> | 
    <span class='nav_item per' id=nav_item__replied>Replied<span class='count_badge badge_green'>$count_replied</span></span> | 
    <span class='nav_item per' id=nav_item__closed>Closed<span class='count_badge badge_pink'>$count_closed</span></span> | 
    <span class='nav_item per' id=nav_item__banned>Banned<span class='count_badge badge_red'>$count_banned</span></span> | 
    <span class='debug' id=type2>type2</span>  

  </div>
";

$o = "
  $o
  $nav
  <div class='wadah gradasi-hijau' data-aos=fade-up>
    <span class=debug id=belum_dijawab>$belum_dijawab</span>
    $li
  </div>
";

?>
<style>.lp{
  font-size: 6pt;
  color: gray
}.li_ask{
  /* display:grid; */
  /* grid-template-columns:20px auto; */
  /* grid-gap: 5px; */
  margin-bottom: 15px;
  padding-bottom: 10px;
  border-bottom: solid 1px #ccc;
}.ask_from{
  display:flex;
  flex-wrap: wrap; 
  column-gap:10px;
}.nav_item{
  display: inline-block;
  cursor: pointer;
}.nav_active{
  border:solid 1px #88f;
  background: linear-gradient(#efe,#6f6);
  border-radius: 5px;
  padding: 5px 5px 5px 7px

}

.stop_ask{
  background: #cccccc88;
  padding: 5px;
  text-align:right;
}.stop_banned{
  border-bottom: solid 2px red;
}.stop_closed{
  border-bottom: solid 2px green;
}

.ask_banned{
  color: gray !important;
  background: #ffcccc44;
}.ask_replied,.ask_banned,.ask_closed{
  display:none;
}

.ul_jawabans{
  margin: 5px 0 5px 0;
  border: solid 1px #ccc;
  border-radius: 8px;
  padding-left: 20px;
}.ul_jawabans li{
  margin-bottom: 5px;
}</style>
<section id="about" class="about">
  <div class="container">
    <div class="section-title" data-aos="fade-up">
      <h2 class=proper>Chats</h2>
      <p><?=$sub_judul?></p>
    </div>
    <?=$o?>
  </div>
</section>















<script>
  $(function(){
    let jenis = $('#jenis').text();
    let id_sesi = $('#id_sesi').text();
    let id_jenis = $('#id_jenis').text();

    $('.reply_input').keyup(function(){
      $(this).val($(this).val().replace(/['"]/gim,'`'));
    })
    
    $('.set_poin').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let type = rid[0];
      let id = rid[1];
      let tabel = '';

      if(type=='set_poin'){
        tabel = 'jawaban';
      }else if(type=='set_poin_pertanyaan'){
        tabel = 'pertanyaan';
      }else{
        alert('unhandle the type of set_point.');
        return;
      }

      let nilai = $(this).text();
      let nilai_baru = prompt(`Berapa nilai baru untuk ${tabel} ini?`,nilai);
      if(!nilai_baru) return;
      if(isNaN(nilai_baru) || nilai_baru<-300 || nilai_baru>500){
        alert('Silahkan masukan angka dari -300 s.d 500');
        return;
      }

      nilai_baru = parseInt(nilai_baru);

      let link_ajax = `ajax/ajax_set_poin.php?tabel=${tabel}&nilai_baru=${nilai_baru}&id=${id}`;
      // alert(link_ajax);
      $.ajax({
        url:link_ajax,
        success:function(a){
          // console.log(a);
          if(a.trim()=='sukses'){
            $('#'+tid).text(nilai_baru);
          }else{
            alert(a)
          }
        }
      })
    })

    $('.delete_chat').click(function(){
      // alert('delete chat is ready to code.')
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let type = rid[0];
      let id = rid[1];
      let tabel = '';

      if(type=='delete_pertanyaan'){
        tabel = 'pertanyaan';
      }else if(type=='delete_chat'){
        tabel = 'jawaban';
      }else{
        alert('unhandle the type of set_point : '+type);
        return;
      }

      let y = confirm(`Yakin untuk menghapus ${tabel} ini?\n\nTidak ada fitur undo.`);
      if(!y) return;
      
      if(tabel=='pertanyaan'){
        let y2 = confirm(`Perhatian! Menghapus pertanyaan akan menghapus seluruh data jawabannya! Lanjut menghapus pertanyaan?\n\nTidak ada fitur undo.`);
        if(!y2) return;
      }

      let link_ajax = `ajax/ajax_crud_chats.php?aksi=hapus&tabel=${tabel}&isi_baru=&kolom=kolom&id=${id}`;
      alert(link_ajax);
      $.ajax({
        url:link_ajax,
        success:function(a){
          // console.log(a);
          if(a.trim()=='sukses'){
            if(tabel=='jawaban'){
              $('#li_jawab__'+id).html('<span class="kecil miring abu">--chat dihapus--</span>');
            }else if(tabel=='pertanyaan'){
              $('#blok_pertanyaan__'+id).html('<span class="kecil miring abu">--pertanyaan ini telah dihapus--</span>');
            }else{
              alert('Sukses ajax tanpa handler.')
            }
          }else{
            alert(a)
          }
        }
      })


    })

    $('.set_pertanyaan').click(function(){
      alert('set_pertanyaan is ready to code.')
    })

    $('.reply_toggle').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let id = rid[1];
      console.log(id);

      
      let new_reply = $('#reply_input__'+id).val().trim();
      if(new_reply.length>0){
        let verif_status = 1; //ok
        let link_ajax = `ajax/ajax_reply_answer.php?id_pertanyaan=${id}&verif_status=${verif_status}&reply=${new_reply}`;
        $.ajax({
          url:link_ajax,
          success:function(a){
            console.log(new_reply, a);
            if(a.trim()=='sukses'){
              $('#reply_input__'+id).val('');
              $('#blok_reply_input__'+id).fadeOut();
              // console.log("$('#blok_reply_input__'+id).fadeOut()");

              // append reply to ul_jawabans
              let id_peserta = $('#id_peserta').text();
              let nama_peserta = $('#nama_peserta').text();
              // console.log(id_peserta,nama_peserta); 
              // return;
              $('#ul_jawabans__'+id).append(`
              <li>
                ${new_reply} - 
                <span class='abu'>barusan</span> - 
                <a href='?peserta&id_peserta=${id_peserta}'>by ${nama_peserta}</a> - 
                0 <span class='lp'>LP</span>  
              </li>
              `)
            }else{
              alert(a)
            }
          }
        })
      }else{
        $('#blok_reply_input__'+id).fadeToggle();
      }


    })
    $('.nav_item').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let type = rid[1];
      let type2 = $('#type2').text();
      if(type==type2) return;

      if(type=='all'){
        $('.li_ask').fadeIn();
      }else{
        $('.li_ask').hide();
        $('.ask_'+type).fadeIn();
        $('#type2').text(type);
      }

      $('.nav_item').removeClass('nav_active')
      $(this).addClass('nav_active')
      // console.log(type);
    })


  })
</script>