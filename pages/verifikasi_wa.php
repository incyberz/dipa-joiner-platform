<section>
  <div class="container">

    <div class="section-title" data-aos="fade-up">
      <h2>Verifikasi WA</h2>
      <p>Untuk mengakses semua fitur kamu harus update dengan nomor whatsapp yang aktif.</p>
    </div>

    <div class="wadah gradasi-hijau" data-aos='fade-up' data-aos-delay='200'>
      <label for="no_wa" class='tengah mb1'>Nomor WhatsApp <span class='kecil abu miring'>* yang aktif</span></label>
      <input type="text" class="form-control tengah" maxlength=14 id=no_wa autocomplete=off style='color:gray'>
      <div class="tengah consolas" style='font-size:30px' id=no_wa2>628X-XXX-XXX-XXX</div>
      <div class="tengah consolas red" style='font-size:10px' id=no_wa_invalid>awali dg "08..." atau "62..."</div>
      <div>
        <a class="btn btn-primary btn-block" id=btn_verifikasi>Verifikasi</a>
      </div>
    </div>
  </div>
</section>


<script>
  $(function(){
    (function($) {
      $.fn.inputFilter = function(inputFilter) {
        return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
          if (inputFilter(this.value)) {
            this.oldValue = this.value;
            this.oldSelectionStart = this.selectionStart;
            this.oldSelectionEnd = this.selectionEnd;
          } else if (this.hasOwnProperty("oldValue")) {
            this.value = this.oldValue;
            this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
          } else {
            this.value = '';
          }
        });
      };
    }(jQuery));
    
    $("#no_wa").inputFilter(function(value) { return /^\d*$/.test(value); });
    $("#no_wa").keyup(function() { 
      let val = $(this).val();
      let val2 = val.substring(0,4)
        + '-' + val.substring(4,7)
        + '-' + val.substring(7,10)
        + '-' + val.substring(10,14);
      val2 = val2=='' ? '-' : val2;
      $('#no_wa2').text(val2);


      if(val.substring(0,2)=='08' || val.substring(0,2)=='62'){
        $('#no_wa_invalid').text('');
        if(val.substring(0,2)=='08'){
          val = '628' + val.substring(2,14);
          $('#no_wa').val(val);
        }
      }else{
        $('#no_wa_invalid').text('awali dg "08..." atau "62..."');
        if(val.length>2){
          $(this).val('');
        }
        return;
      }




      if(val.length>10){
        $('#btn_verifikasi').fadeIn();
      }else{
        $('#btn_verifikasi').fadeOut();
      }
    });
  })
</script>