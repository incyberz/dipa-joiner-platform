<?php
# ================================================
# SESSION SECURITY
# ================================================
include 'session_user.php';

# ================================================
# FUNCTIONS
# ================================================
function clean($string) {
	// return $string;
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
   return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}

function sortnclean($str){
	$ck = strtolower(clean($str));
	$rk = explode('-', $ck); 
	sort($rk);
	
	$rkh = []; 

	$j=0;
	for ($i=0; $i < count($rk); $i++){
		if($i!= count($rk)-1) if($rk[$i]==$rk[$i+1]) continue;
		$rkh[$j] = $rk[$i];
		$j++;
	}
	$hasil = ''; 
	for ($i=0; $i < count($rkh); $i++) $hasil .= $rkh[$i].' ';
	return $hasil;
}





# ================================================
# GET VARIABEL
# ================================================
$my_tags = $_GET['my_tags'] ?? die(erid('my_tags')); if($my_tags=='') die(erid("my_tags::null"));
$kalimat_soal = $_GET['kalimat_soal'] ?? die(erid('kalimat_soal')); if($kalimat_soal=='') die(erid("kalimat_soal::null"));


# ================================================
# SIMILARITY CHECK
# ================================================
$rtags_soal = explode(',',$my_tags);
$tags_like = '( 0 ';
for ($i=0; $i < count($rtags_soal); $i++) {
	$ctag = trim($rtags_soal[$i]); 
	$tags_like .= " OR a.tags like '%$ctag%'";
}
$tags_like .= ' ) ';

// $tags_like = '1'; //zzz debug


$s = "SELECT 
a.id as id_soal,
a.kalimat_soal,
a.opsies,
a.id_pembuat as soal_creator, 
a.tanggal as tanggal_buat,  
b.nama as pembuat_soal 

from tb_soal_pg a 
join tb_peserta b on a.id_pembuat=b.id 
join tb_sesi c on a.id_sesi=c.id 
where 1  
and $tags_like 
and c.id_room='$id_room'
";


// die($s);
$q = mysqli_query($cn,$s) or die('Error jx_soal_similarity_check. '.mysqli_error($cn));
$total_soal = mysqli_num_rows($q);


if($total_soal==0) die('sukses__1');

$rsoal = [];
$i=0;
while ($d=mysqli_fetch_assoc($q)) {

	$opsies = str_replace('~~~',' ',$d['opsies']);
	$kalimat_soal_db = "$d[kalimat_soal] $opsies";

	similar_text(sortnclean($kalimat_soal), sortnclean($kalimat_soal_db), $persen_similar);
	$persen_similar = round($persen_similar,2);

	$rsoal[$i] = [$persen_similar,$d['id_soal'],$d['kalimat_soal'],$d['soal_creator'],$d['pembuat_soal'],$d['tanggal_buat']]; 
	$i++;
	// die("sukses__1__<hr><span style='color: red'>$persen_similar</span> || Kalimat soal: $kalimat_soal. <hr>kalimat soal db: $kalimat_soal_db");
}

usort($rsoal, function($a,$b){
	if($a[0]==$b[0]) return 0;
	return $a[0] < $b[0]?1:-1;
});


$z='';
$count_row_similar = 3;
$reach_too_similar = '';
if($total_soal<$count_row_similar) $count_row_similar=$total_soal;
for ($i=0; $i < $count_row_similar ; $i++) { 
	$persen_similar = $rsoal[$i][0];
	if($persen_similar>75){
		$reach_too_similar = $persen_similar;

		$id_soal 			= $rsoal[$i][1];
		$kalimat_soal = $rsoal[$i][2];
		$soal_creator = $rsoal[$i][3];
		$pembuat_soal = $rsoal[$i][4];
		$tanggal_buat = $rsoal[$i][5];

		$z.="<hr><b>Similarity: $persen_similar%</b> | <small>Terlalu sama dengan soal yang dibuat oleh: $pembuat_soal at $tanggal_buat <div class='mt1 ml2'><span class=abu>Kalimat soal:</span> $kalimat_soal</div></small>";

	}
}

$final_similar = $reach_too_similar ? $reach_too_similar : $persen_similar;

echo "sukses__$final_similar"."__$z";








?>