<?php
instruktur_only();

# =========================================================
# GET TMP WAR DATA
# =========================================================
$s = "SELECT 
a.id as id_peserta,
a.nama as nama_peserta
FROM tb_peserta a 
JOIN tb_kelas_peserta b ON a.id=b.id_peserta
JOIN tb_kelas c ON b.kelas=c.kelas 
JOIN tb_room_kelas d ON c.kelas=d.kelas
WHERE c.ta=$ta_aktif
AND c.status=1 -- kelas aktif
AND d.id_room=$id_room 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) {
  echo div_alert('danger', "Belum ada data $Peserta.");
} else {
  while ($d = mysqli_fetch_assoc($q)) {
    $from_perang = "FROM tb_war p WHERE p.id_penjawab=$d[id_peserta] and p.id_room=$id_room";
    $from_soal = "FROM tb_soal_peserta p 
    JOIN tb_sesi q ON p.id_sesi=q.id 
    WHERE p.id_pembuat=$d[id_peserta] 
    AND q.id_room=$id_room";

    $s_war = "SELECT 
    (SELECT last_update FROM tb_war_summary WHERE id=$d[id_peserta] AND id_room=$id_room) as last_update,
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
      WHERE p.id_pembuat=$d[id_peserta] 
      AND p.id_room=$id_room) as poin_tumbuh_soal,

    (
      SELECT COUNT(1) FROM tb_soal_peserta a 
      LEFT JOIN tb_war b ON a.id=b.id_soal_peserta AND b.id_penjawab=$d[id_peserta] 
      JOIN tb_sesi c ON a.id_sesi=c.id 
      WHERE (a.id_status is null OR a.id_status >= 0) 
      AND b.id is null 
      AND a.id_pembuat!=$d[id_peserta] 
      AND c.id_room=$id_room) as available_questions

    ";
    $q_war = mysqli_query($cn, $s_war) or die(mysqli_error($cn));
    $d_war = mysqli_fetch_assoc($q_war);

    $war_point_quiz = $d_war['war_point_quiz'];
    $war_point_reject = $d_war['war_point_reject'];
    $poin_tumbuh_soal = $d_war['poin_tumbuh_soal'];
    $poin_membuat_soal = $d_war['poin_membuat_soal'];

    $war_point_passive = $poin_membuat_soal + $poin_tumbuh_soal;

    $count_answer_right = $d_war['count_answer_right'];
    $count_answer_false = $d_war['count_answer_false'];
    $count_reject = $d_war['count_reject'];
    $count_not_answer = $d_war['count_not_answer'];

    $my_question_banned = $d_war['my_question_banned'];
    $my_question_unverified = $d_war['my_question_unverified'];
    $my_question_verified = $d_war['my_question_verified'];
    $my_question_decided = $d_war['my_question_decided'];
    $my_question_promoted = $d_war['my_question_promoted'];

    $available_questions = $d_war['available_questions'];
    $last_update = $d_war['last_update'];

    $war_points = $war_point_quiz + $war_point_reject + $poin_membuat_soal + $poin_tumbuh_soal;

    $s2 = "UPDATE tb_war_summary SET 
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

      WHERE id_peserta=$d[id_peserta] 
      AND id_room=$id_room 
    ";
    // die($s);
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));



    # ====================================================
    # GET MANUAL RANK AND RE-UPDATE RANK
    # ====================================================
    $s2 = "SELECT a.id_peserta 
    FROM tb_war_summary a 
    JOIN tb_peserta b ON a.id_peserta=b.id 
    WHERE id_room=$id_room 
    AND b.status=1 
    AND b.id_role = 1  -- _peserta only
    ORDER BY a.war_points DESC";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    $i = 1;
    $war_rank = 1;
    while ($d2 = mysqli_fetch_assoc($q2)) {
      if ($d2['id_peserta'] == $d['id_peserta']) {
        $war_rank = $i;
        break;
      }
      $i++;
    }


    // reupdate rank
    $s2 = "UPDATE tb_war_summary SET war_rank=$war_rank WHERE id_peserta=$d[id_peserta] AND id_room=$id_room";
    echolog("Updating war-points $d[nama_peserta] | war_points: $war_points | war_rank: $war_rank");
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  }
}
