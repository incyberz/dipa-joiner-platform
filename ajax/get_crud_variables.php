<?php
$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : die(erid("aksi"));if($aksi=='')die(erid('aksi(NULL)'));
$id = isset($_GET['id']) ? $_GET['id'] : die(erid("id"));if($id=='')die(erid('id(NULL)'));
$kolom = isset($_GET['kolom']) ? $_GET['kolom'] : die(erid("kolom"));if($kolom=='')die(erid('kolom(NULL)'));
$isi_baru = isset($_GET['isi_baru']) ? $_GET['isi_baru'] : die(erid("isi_baru"));

// if($isi_baru=='')die(erid('isi_baru(NULL)'));