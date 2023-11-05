<section id="pengajar" class="pengajar section-bg">
  <div class="container">

    <div class="section-title" data-aos="fade-up">
      <h2>Pengajar</h2>
      <p>Assalamu'alaikum! Halo semuanya! Perkenalkan nama Bapak, Iin Sholihin, seorang Freelance Programmer, Lecturer, dan juga Farmer. Untuk lebih detail silahkan klik Link Github bapak.</p>
    </div>

 

    <div data-aos=fade-up>
      <div class="blok_profile_pengajar">
        <img src="assets/img/pengajar/abi.jpg" class="foto_profil" onclick="alert('Hai!! Ingin belajar animasi CSS seperti ini?!')">
      </div>
      <a class='btn btn-primary btn-block' href="https://github.com/incyberz" target=_blank onclick='return confirm("Ingin membuka Profil Github Pa Iin di jendela baru?")'>Goto: Githubnya Pak Iin.</a>

      <div class="wadah mt-4 gradasi-hijau">
        <div class='mb-2'>Jika punya pesan buat pa iin, silahkan ketik aja disini ya:</div>
        <div class="kecil miring abu">)* minimal 50 karakter. Anda mengetik <span id=jumlah_karakter>0</span> of 300 karakter</div>
        <textarea class='form-control mb2' rows=5 id=pesan_wa maxlength=300></textarea>
        <button class="btn btn-success btn-block" onclick="alert('Isi dulu pesannya ya! Minimal 50 karakter, maksimal 300 karakter.')" id=btn_kirim>Kirim Pesan WhatsApp</button>
        <a class="btn btn-primary btn-block hideit" id=link_send_wa >Kirim Pesan WhatsApp</a>
        <div class="kecil miring abu">Powered by WhatsApp Gateway .. wanna learn?!</div>
      </div>

    </div>

  </div>
</section>

<script type="text/javascript">
  $(document).ready(function(){
    $("#pesan_wa").keyup(function(){

      let pesan_wa = $(this).val().trim();
      let nama_peserta = $("#nama_peserta").text();
      let d = new Date();

      let text_wa = pesan_wa + ". [From: DIPA Joiner Gamified Training System - "+d+"]";

      // let no_wa_pengajar = $("#no_wa_pengajar").text();
      let no_wa_pengajar = '87729007318';
      let link_api = "https://api.whatsapp.com/send?phone=62"+no_wa_pengajar+"&text="+text_wa;
      if(pesan_wa.length>=50 && pesan_wa.length <=300){
        $("#link_send_wa").prop("href",link_api);
        $("#btn_kirim").hide();
        $("#link_send_wa").fadeIn();
        // console.log(link_api);
      }else{
        $("#link_send_wa").hide();
        $("#btn_kirim").fadeIn();

      }

      $('#jumlah_karakter').text(pesan_wa.length);
    })
    $("#pesan_wa").keyup();

  })
</script>