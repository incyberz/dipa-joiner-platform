<!-- versi 1.0.0 -->
<script>
  $(function(){
    let link_ajax = '';
    let id_po = $('#id_po').text();
    let tb = $('#tb').text();

    $('.btn_aksi').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let tb = rid[0];
      let aksi = rid[1];
      let id_item = rid[2] ?? '';
      let kolom = rid[3] ?? '';

      console.log(tb,aksi,id_item);

      if(aksi=='delete_file'){
        let y = confirm('Yakin untuk menghapus file ini?');
        if(!y) return;
        let file = rid[2];
        let path = rid[3];
        if(!file || !path){
          console.log('EMPTY one of file or path', file, path);
        }else{
          link_ajax = `ajax/delete_file.php?path=${path}&file=${file}`;
          console.log(' delete FILE with AJAX: ', link_ajax);
          $.ajax({
            url:link_ajax,
            success:function(a){
              if(a.trim()=='sukses'){
                $('#'+tb).fadeOut();
                console.log('source_'+tb+' hide');
              }else{
                alert('Tidak dapat menghapus file.');
                console.log(a);
              }
            }
          })

        }
      }else if(aksi=='delete'){
        let y = confirm('Yakin untuk hapus?');
        if(!y) return;

        console.log('#source_'+tb+'__'+id_item);
        if(id_item==''){
          $('#source_'+tb).fadeOut();
          console.log('hide '+'#source_'+tb);
          $('#'+tb).fadeOut();
          console.log('#'+tb+' NO AJAX DELETE');
        }else{
          $('#source_'+tb+'__'+id_item).fadeOut();
          console.log('hide '+'#source_'+tb+'__'+id_item);
          $('#'+tb+'__'+id_item).fadeOut();
          console.log('hide '+'#'+tb+'__'+id_item);
          // ====================================================
          // AJAX DELETE
          // ====================================================
          link_ajax = `ajax/crud.php?tb=${tb}&aksi=delete&id=${id_item}`;
          console.log(' delete with AJAX: ', link_ajax);

          $.ajax({
            url:link_ajax,
            success:function(a){
              if(a.trim()=='sukses'){
                // location.reload(); //zzz
                console.log('delete with AJAX sukses.');
              }else{
                alert(`Tidak dapat menghapus data ${tb}.`);
                console.log(a);
              }
            }
          })

          // if(kolom!=''){
          //   link_ajax = `ajax/crud.php?tb=${tb}&aksi=delete&id=${id_item}`;
          //   console.log(' delete with AJAX: ', link_ajax);

          //   $.ajax({
          //     url:link_ajax,
          //     success:function(a){
          //       if(a.trim()=='sukses'){
          //         location.reload(); //zzz
          //       }else{
          //         alert(`Tidak dapat menghapus data ${tb}.`);
          //         console.log(a);
          //       }
          //     }
          //   })
          // }else{
          //   console.log('not delete to from_db');

          // }
        }


      }else if(aksi=='toggle' || aksi=='pra_tambah'){
        if(id_item!=''){
          $('#'+tb+'__'+id_item).slideToggle();
          console.log('toggle #'+tb+'__'+id_item);
        }else{
          $('#'+tb).slideToggle(); 
          console.log('toggle'+'#'+tb);
        }
      }else if(aksi=='close' || aksi=='cancel'){
        if(id_item!=''){
          let id = rid[2];
          $('#'+tb+'__'+id).slideUp();
          console.log('#'+tb+'__'+id+' slideUp');
        }else{
          $('#'+tb).slideUp();
          console.log('#'+tb+' slideUp');
        }
      }else if(aksi=='tambah'){
        console.log(aksi,tb);
        link_ajax = `ajax/crud.php?tb=${tb}&aksi=insert&id=new`;
        $.ajax({
          url:link_ajax,
          success:function(a){
            if(a.trim()=='sukses'){
              location.reload();
            }else if(a.toLowerCase().search('Duplicate entry')){
              alert(`Data NEW-${tb} sudah ada. Silahkan edit kontennya!`)
              console.log(a);
            }else{
              alert(`Tidak dapat menambah ${tb} baru.`);
              console.log(a);
            }
          }
        })
      }else if(aksi=='save_item' || aksi=='delete_item'){
        // save item = add item ke list
        // delete item = remove from list
        if(aksi=='delete_item'){
          let y = confirm('Yakin untuk hapus list-item?');
          if(!y) return;
        }

        let kolom = rid[3];
        // console.log(aksi, 'untuk', kolom, id_po);

        if(id_po.length>0){
          let z = document.getElementsByClassName(kolom);
          let value_items = '';
          let cid = '';
          for (let i = 0; i < z.length; i++) {
            if(id_item==i){
              cid = i;
              continue;
            } 
            value_items += z[i].innerText + ' ___';
            console.log('LOOP =======',value_items);
          }
  
          if(aksi=='save_item'){
            value_items += $('#update_value__'+kolom+'__'+cid).val();
          }
          
          value_items = value_items.replace(/\s\s+/g, ' ');
          
          console.log(value_items, kolom, id_po);
          link_ajax = `ajax/crud.php?tb=po&aksi=update&id=${id_po}&kolom=${kolom}&value=${value_items}`;
          console.log(link_ajax);

          $.ajax({
            url:link_ajax,
            success:function(a){
              if(a.trim()=='sukses'){
                location.reload();
              }else{
                alert('Tidak bisa menyimpan item.');
                console.log(a);
              }
            }
          })

        }

      }else if(aksi=='simpan'){
        let value = $('#input_'+kolom).val().replace(/\s\s+/g, ' ');
        link_ajax = `ajax/crud.php?tb=${tb}&aksi=update&kolom=${kolom}&value=${value}&id=${id_po}`;
        $.ajax({
          url:link_ajax,
          success:function(a){
            if(a.trim()=='sukses'){
              // location.reload();
              $('#edit_'+kolom).slideUp();
              $('#target_'+kolom).text(value);
            }else{
              alert('Tidak bisa menyimpan item.');
              console.log(a);
            }
          }
        })

        console.log(id_po, kolom, value);
      }else{
        alert('Handler untuk aksi: '+aksi+" akan segera dibangun. Terimakasih sudah mencoba!");
        console.log('Belum ada handler untuk aksi: '+aksi);
      }


      
    })
  })
</script>