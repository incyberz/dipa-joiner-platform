<script>

$(document).ready(function(){
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
  
  $("#nik").inputFilter(function(value) { return /^\d*$/.test(value); });
    
  $("#nik").keyup(function(){

    var nik = $(this).val();
    $("#div_after_nik").hide();

    if(nik.length!=16){
      $("#nik_ket").show();
      $("#nik_divide").show();
      $("#div_nik_info").hide();
      $("#nik_divide").text(nik.substring(0,4)+"-"+nik.substring(4,8)+"-"+nik.substring(8,12)+"-"+nik.substring(12,16));
      // $("#nama_kec").text("");
      // $("#nama_kab").text("");
      // $("#nama_prov").text("");

    }else{

      $("#nik_ket").hide();
      $("#nik_divide").hide();

      var err_nik = 0;

      var prv = nik.substring(0,2);
      var kab = nik.substring(2,4);
      var kec = nik.substring(4,6);
      var tgl = nik.substring(6,8);
      var bln = nik.substring(8,10);
      var thn = nik.substring(10,12);
      var nur = nik.substring(12,16); //no_urut
      // var akh = nik.substring(15,16); //1 digit terakhir


      // =======================================================================
      // CEK FORMAT NIK
      // =======================================================================
      if(parseInt(prv)<11) err_nik=1;
      if(parseInt(kab)==0) err_nik=1;
      if(parseInt(kec)==0) err_nik=1;
      if(parseInt(tgl)==0) err_nik=1;
      if(parseInt(bln)==0) err_nik=1;
      if(parseInt(nur)==0) err_nik=1;
      // if(parseInt(akh)==0) err_nik=1;

      if(parseInt(tgl)>71 || (parseInt(tgl)>31 && parseInt(tgl)<41)) err_nik=1;
      if(parseInt(bln)>12) err_nik=1;
      if(parseInt(thn)>10 && parseInt(thn)<60) err_nik=1;

      if(err_nik) {
        $("#nik_ket").show();
        $("#nik_ket").text("Format NIK ga betul masbroo! Silahkan lihat pada KTP atau Kartu Keluarga."); 
        // setview_input(id,0);
        return;
      }
      // =======================================================================
      // CEK FORMAT NIK
      // =======================================================================

      var nama_gender = "Laki-laki";
      var gender = 'L';
      var true_tgl = parseInt(tgl);
      if(parseInt(tgl)>40) {
        nama_gender = "Perempuan"; 
        gender = "P"; 
        true_tgl = parseInt(tgl)-40; 
        $("#gender").val("P")
      }else{
        $("#gender").val("L")
      }


      var nama_bulan = ["","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];

      var tahun = "";
      if(parseInt(thn)<50) tahun = "20"+thn;
      if(parseInt(thn)>=50) tahun = "19"+thn;

      var ttl = true_tgl+" "+nama_bulan[parseInt(bln)]+" "+tahun;
      var tanggal_lahir = tahun+"-"+bln+"-"+true_tgl;

      $("#ttl_tanggal").val(true_tgl).change();
      $("#ttl_bulan").val(parseInt(bln)).change();
      $("#ttl_tahun").val(parseInt(tahun)).change();

      var today = new Date();
      var birthday = new Date(bln+"/"+true_tgl+"/"+tahun);

      var ageDifMs = Date.now() - birthday.getTime();
      var ageDate = new Date(ageDifMs); // miliseconds from epoch
      var usia = Math.abs(ageDate.getUTCFullYear() - 1970);

      $("#ket_gender").text("Kamu "+nama_gender);
      $("#ket_ttl").text("Tanggal lahir: "+ttl);
      $("#ket_usia").text("Usia "+usia+" tahun");




      const date2 = new Date();
      const date1 = new Date(`${bln}/${tgl}/`+date2.getFullYear());
      // const date1 = new Date('12/31/2023');
      console.log(date1,date2);
      const diffTime = Math.abs(date2 - date1);
      const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)); 
      if(date1>date2){
        $('#ket_ultah').text("Ultah kamu "+diffDays + " hari lagi");
      }else{
        $('#ket_ultah').text("Ultah kamu "+diffDays + " hari yang lalu");
      }


      var link_ajax = "ajax/get_nama_daerah_by_nik.php?nik="+nik;

      $.ajax({
        url:link_ajax,
        success:function(a){
          if(a.substring(0,3)=="1__"){
            // alert("Sukses, a: "+a);
            var z = a.split("__");
            var ra = z[1].split(";");
            var nama_kec = ra[0];
            var nama_kab = ra[1];
            var nama_prov = ra[2];
            var kode_pos = ra[3];

            // $("#lokasi_kec").text(nama_kec+" - "+nama_kab+" - "+nama_prov);

            var kecamatan = "kec "+nama_kec+" "+nama_kab;
            // set_href("cari_kode_pos_nama_kec_ktp",kecamatan);

            $("#tempat_lahir").val(nama_kab);
            $("#tanggal_lahir").val(tanggal_lahir);
            // $("#alamat_rumah2").val("kec "+nama_kec+" "+nama_kab+" "+nama_prov);
            $("#ket_kec").text('Kec '+nama_kec);
            $("#ket_kab").text(nama_kab);
            $("#ket_prov").text('Prov '+nama_prov);

            $("#div_nik_info").fadeIn();
            $("#div_after_nik").fadeIn();

          }else{
            $("#nik_ket").show();
            $("#nik_ket").text("Error on AJAX-NIK; return: "+a);
          }
        }
      })
    }
  })
})
</script>
