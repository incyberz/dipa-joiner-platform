<?php
$add_sesi = $id_role != 2 ? '' : "
<div data-aos=fade data-aos-delay=600>
  <div class='btn_aksi f12 abu pointer kanan' id=form_add__toggle>
    $img_add Add Sesi
  </div>
  <form method=post class='hideit mt2 wadah gradasi-kuning' id=form_add>
    <select class='form-control' id=select_jenis_sesi>
      <option value=belum_milih>--Pilih Jenis Pekan--</option>
      <option value=0>0 - Minggu Tenang</option>
      <option value=1>1 - Perkuliahan Normal</option>
      <option value=2>2 - Pekan UTS</option>
      <option value=3>3 - Pekan UAS</option>
    </select>

    <div id=div_btn class=hideit>
      <button class='btn btn-success mt2 w-100' onclick='return confirm(`Add Sesi untuk $Room ini?`)' name=btn_add_sesi>Add Sesi</button>
    </div>
  </form>
</div>
";
?><script>
  $(function() {
    $('#select_jenis_sesi').change(function() {
      // jika belum milih maka hide div_btn else show div_btn
      $('#div_btn').slideToggle(this.value != 'belum_milih');
    });
  })
</script>