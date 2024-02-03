<?php
$s = "SELECT a.id FROM tb_soal_pg a 
LEFT JOIN tb_war b ON a.id=b.id_soal AND b.id_penjawab=$id_peserta 
JOIN tb_sesi c ON a.id_sesi=c.id 
WHERE (a.id_status is null OR a.id_status >= 0) 
AND b.id is null 
AND c.id_room=$id_room 
AND a.id_pembuat!=$id_peserta 
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$available_questions = mysqli_num_rows($q);

$s = "UPDATE tb_war_summary SET available_questions=$available_questions WHERE id=$id_peserta AND id_room=$id_room";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
