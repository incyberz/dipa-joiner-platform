<?php
# =========================================================
# GET TMP WAR DATA
# =========================================================
$last_update_war_tmp = '';
$must_update = 0;

$war_rank = 0;
$war_points = 0;
$war_point_quiz = 0;
$war_point_reject = 0;
$war_point_passive = 0;

$accuracy = 0;

$count_answer = 0;
$count_answer_right = 0;
$count_answer_false = 0;
$count_reject = 0;
$count_not_answer = 0;

$my_question_banned = 0;
$my_question_unverified = 0;
$my_question_verified = 0;
$my_question_decided = 0;
$my_question_promoted = 0;

$available_questions = 0;

$s = "SELECT * FROM tb_war_summary WHERE id_peserta=$id_peserta AND id_room=$id_room";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q)) {
  $d = mysqli_fetch_assoc($q);

  $war_rank = $d['war_rank'];
  $war_points = $d['war_points'];
  $war_point_quiz = $d['war_point_quiz'];
  $war_point_reject = $d['war_point_reject'];
  $war_point_passive = $d['war_point_passive'];

  $count_answer_right = $d['count_answer_right'];
  $count_answer_false = $d['count_answer_false'];
  $count_reject = $d['count_reject'];
  $count_not_answer = $d['count_not_answer'];

  $my_question_banned = $d['my_question_banned'];
  $my_question_unverified = $d['my_question_unverified'];
  $my_question_verified = $d['my_question_verified'];
  $my_question_decided = $d['my_question_decided'];
  $my_question_promoted = $d['my_question_promoted'];

  $available_questions = $d['available_questions'];
  $poin_membuat_soal = $d['poin_membuat_soal'];
  $poin_tumbuh_soal = $d['poin_tumbuh_soal'];
  $last_update = $d['last_update'];

  $selisih_war = strtotime('now') - strtotime($d['last_update']);
  // if($selisih_war>3600){
  if ($selisih_war > 600) {
    $must_update = 1; // if > 10 menit must update
  }
} else {
  // jika belum punya data war summary
  $must_update = 1;
  $s = "INSERT INTO tb_war_summary (id_peserta,id_room,last_update) VALUES ($id_peserta,$id_room,'2020-1-1')";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
}

# =========================================================
# GET REALTIME WAR DATA
# =========================================================
if ($must_update) {

  $from_perang = "FROM tb_war p WHERE p.id_penjawab=$id_peserta and p.id_room=$id_room";
  $from_soal = "FROM tb_soal_peserta p 
  JOIN tb_sesi q ON p.id_sesi=q.id 
  WHERE p.id_pembuat=$id_peserta 
  AND q.id_room=$id_room";

  $s = "SELECT 
  (SELECT last_update FROM tb_war_summary WHERE id=$id_peserta AND id_room=$id_room) as last_update,
  (SELECT SUM(poin_penjawab) $from_perang AND p.is_benar >= 0) as war_point_quiz,
  (SELECT SUM(poin_penjawab) $from_perang AND p.is_benar < 0) as war_point_reject,

  (SELECT COUNT(1) $from_perang AND p.is_benar = 1) as count_answer_right,
  (SELECT COUNT(1) $from_perang AND p.is_benar = 0) as count_answer_false,
  (SELECT COUNT(1) $from_perang AND p.is_benar < 0) as count_reject,
  (SELECT COUNT(1) $from_perang AND p.is_benar is null ) as count_not_answer,

  (SELECT COUNT(1) $from_soal AND p.id_status < 0) as my_question_banned,
  (SELECT COUNT(1) $from_soal AND (p.id_status = 0 OR p.id_status is null)) as my_question_unverified,
  (SELECT COUNT(1) $from_soal AND p.id_status = 1) as my_question_verified,
  (SELECT COUNT(1) $from_soal AND p.id_status = 2) as my_question_decided,
  (SELECT COUNT(1) $from_soal AND p.id_status = 3) as my_question_promoted,

  (SELECT SUM(p.poin_membuat_soal) $from_soal) as poin_membuat_soal,
  (
    SELECT SUM(poin_pembuat) 
    FROM tb_war p 
    WHERE p.id_pembuat=$id_peserta 
    AND p.id_room=$id_room) as poin_tumbuh_soal,

  (
    SELECT COUNT(1) FROM tb_soal_peserta a 
    LEFT JOIN tb_war b ON a.id=b.id_soal_peserta AND b.id_penjawab=$id_peserta 
    JOIN tb_sesi c ON a.id_sesi=c.id 
    WHERE (a.id_status is null OR a.id_status >= 0) 
    AND b.id is null 
    AND a.id_pembuat!=$id_peserta 
    AND c.id_room=$id_room) as available_questions

  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $d = mysqli_fetch_assoc($q);

  $war_point_quiz = $d['war_point_quiz'];
  $war_point_reject = $d['war_point_reject'];
  $poin_tumbuh_soal = $d['poin_tumbuh_soal'];
  $poin_membuat_soal = $d['poin_membuat_soal'];

  $war_point_passive = $poin_membuat_soal + $poin_tumbuh_soal;

  $count_answer_right = $d['count_answer_right'];
  $count_answer_false = $d['count_answer_false'];
  $count_reject = $d['count_reject'];
  $count_not_answer = $d['count_not_answer'];

  $my_question_banned = $d['my_question_banned'];
  $my_question_unverified = $d['my_question_unverified'];
  $my_question_verified = $d['my_question_verified'];
  $my_question_decided = $d['my_question_decided'];
  $my_question_promoted = $d['my_question_promoted'];

  $available_questions = $d['available_questions'];
  $last_update = $d['last_update'];

  $war_points = $war_point_quiz + $war_point_reject + $poin_membuat_soal + $poin_tumbuh_soal;

  $s = "UPDATE tb_war_summary SET 
    war_points = '$war_points',
    war_point_quiz = '$war_point_quiz',
    war_point_reject = '$war_point_reject',
    war_point_passive = '$war_point_passive',

    count_answer_right = '$count_answer_right',
    count_answer_false = '$count_answer_false',
    count_reject = '$count_reject',
    count_not_answer = '$count_not_answer',

    my_question_banned = '$my_question_banned',
    my_question_unverified = '$my_question_unverified',
    my_question_verified = '$my_question_verified',
    my_question_decided = '$my_question_decided',
    my_question_promoted = '$my_question_promoted',

    poin_membuat_soal = '$poin_membuat_soal',
    poin_tumbuh_soal = '$poin_tumbuh_soal',
    available_questions = '$available_questions',
    last_update = CURRENT_TIMESTAMP

    WHERE id_peserta=$id_peserta 
    AND id_room=$id_room 
  ";
  // die($s);
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));



  # ====================================================
  # GET MANUAL RANK AND RE-UPDATE RANK
  # ====================================================
  $s = "SELECT a.id_peserta 
  FROM tb_war_summary a 
  JOIN tb_peserta b ON a.id_peserta=b.id 
  WHERE id_room=$id_room 
  AND b.status=1 
  AND b.id_role = $id_role 
  ORDER BY a.war_points DESC";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $i = 1;
  $war_rank = 1;
  while ($d = mysqli_fetch_assoc($q)) {
    if ($d['id_peserta'] == $id_peserta) {
      $war_rank = $i;
      break;
    }
    $i++;
  }


  // reupdate rank
  $s = "UPDATE tb_war_summary SET war_rank=$war_rank WHERE id_peserta=$id_peserta AND id_room=$id_room";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  die('<script>location.reload()</script>');
}


if ($available_questions > 99) $available_questions = 99;

$last_update_available_question = $last_update;
# ============================================================
# AUTO-SELF UPDATE EVERY HOUR | AVAILABLE SOAL
# ============================================================
$selisih = $id_role == 1 ? (strtotime('now') - strtotime($last_update_available_question)) : 0;
if ($selisih > 3600) {
  include 'includes/update_available_question.php';
}
