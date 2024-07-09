<script>
  $(function() {
    $('.input_editable').focusout(function() {
      // alert($(this).prop('id'))
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let kolom = rid[0];
      let id = rid[1];

      let isi_lama = $('#' + kolom + '2__' + id).text();
      let isi_baru = $(this).val().trim();
      if (isi_lama == isi_baru) return;
      if (isi_baru == '') {
        let y = confirm('Ingin mengosongkan data?');
        if (!y) {
          // console.log(isi_lama);
          $('#' + tid).val(isi_lama);
          return;
        }
        // $('#'+tid).val(isi_lama);
      }

      // manage tags
      if (kolom == 'tags') {
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
        url: link_ajax,
        success: function(a) {
          if (a.trim() == 'sukses') {
            $('#' + tid).addClass('gradasi-hijau biru');
            $('#' + tid).val(isi_baru);
            $('#' + kolom + '2__' + id).text(isi_baru);
          } else {
            alert(a)
          }
        }
      })

    })
  })
</script>