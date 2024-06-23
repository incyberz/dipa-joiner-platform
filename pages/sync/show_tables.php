<?php
$mode = $_GET['mode'] ?? 'field';
$not_mode = $mode == 'field' ? 'row' : 'field';
set_h2("$mode Synchronizer", "<a href='?sync&mode=$not_mode'>$not_mode-sync</a>");

$s = "SHOW TABLES";
$q = mysqli_query($cn1, $s) or die(mysqli_error($cn1));
$tables = [];
while ($d = mysqli_fetch_assoc($q)) array_push($tables, $d["Tables_in_$db_name1"]);

$q = mysqli_query($cn2, $s) or die(mysqli_error($cn2));
$tables2 = [];
while ($d = mysqli_fetch_assoc($q)) array_push($tables2, $d["Tables_in_$db_name2"]);

$tr = '';
$i = 0;
foreach ($tables as $table) {
  $i++;

  $link_check = "<a href='?sync&aksi=check&table=$table'>$img_check</a>";
  $link_prev = "<a href='?sync&aksi=update&table=$table&db_target=$db_name1'>$img_prev</a>";
  $link_next = "<a href='?sync&aksi=update&table=$table&db_target=$db_name2'>$img_next</a>";
  $link_add_left = "<a href='?sync&aksi=add&table=$table&db_name=$db_name1'>$img_add</a>";
  $link_add_right = "<a href='?sync&aksi=add&table=$table&db_target=$db_name2'>$img_add</a>";
  $link_delete_left = "<a href='?sync&aksi=delete&table=$table&db_target=$db_name1'>$img_delete</a>";
  $link_delete_right = "<a href='?sync&aksi=delete&table=$table&db_target=$db_name2'>$img_delete</a>";

  $link_check_warning = "<a href='?sync&aksi=check&table=$table'>$img_warning</a>";
  # ============================================================
  # FIELDS COUNT
  # ============================================================
  $td_field = '';
  if ($mode == 'field') {
    $gradasi = '';
    $aksi_field = '';
    $count_field1 = '?';
    $count_field2 = '?';

    $s = "DESCRIBE $table";
    $q1 = mysqli_query($cn1, $s) or die(mysqli_error($cn1));
    $count_field1 = mysqli_num_rows($q1);

    if (in_array($table, $tables2)) {
      $q2 = mysqli_query($cn2, $s) or die(mysqli_error($cn2));
      $count_field2 = mysqli_num_rows($q2);
    } else {
      $count_field2 = $null;
    }

    if ($count_field1 == $count_field2) {
      $check_icon = $link_check;
      $gradasi = 'hijau';
      $aksi_field = $link_check;
    } else {
      $gradasi =  'kuning';
      $aksi_field = "$link_check_warning";
    }

    // $d1 = mysqli_fetch_assoc($q1);
    // $d2 = mysqli_fetch_assoc($q2);
    // $checkField = $d1['Field'] == $d2['Field'] ? $img_check : $img_warning;

    $arr = [1, 2];
    $arr2 = ['Field', 'Type', 'Null', 'Key', 'Default'];
    $arr_q = [$q1, $q2];

    // initializing array
    foreach ($arr as $k => $v) {
      foreach ($arr2 as $k2 => $v2) {
        $arr_field[$v][$v2] = [];
      }
    }

    foreach ($arr as $k => $v) {
      while ($d = mysqli_fetch_assoc($arr_q[$k])) {
        foreach ($arr2 as $k2 => $v2) {
          array_push($arr_field[$v][$v2], $d[$v2]);
        }
      }
    }

    foreach ($arr2 as $k2 => $v2) {
      $icon = $arr_field[1][$v2] == $arr_field[2][$v2] ? $img_check : $img_warning;
      $td_field .= "
        <td>
          <div class='f12 abu'>$v2</div>
          $icon
        </td>
      ";
    }
  }


  # ============================================================
  # ROWS COUNTS
  # ============================================================
  if ($mode == 'row') {
    $gradasi = '';
    $aksi_row = '';
    $count_row1 = '?';
    $count_row2 = '?';

    $s = "SELECT 1 FROM $table";
    $q = mysqli_query($cn1, $s) or die(mysqli_error($cn1));
    $count_row1 = mysqli_num_rows($q);

    if (in_array($table, $tables2)) {
      $q = mysqli_query($cn2, $s) or die(mysqli_error($cn2));
      $count_row2 = mysqli_num_rows($q);
    } else {
      $count_row2 = $null;
    }

    if (!$count_row1 and !$count_row2) {
      $gradasi = 'abu';
      $aksi_row = "$link_delete_left $link_delete_right";
    } elseif ($count_row1 == $null || $count_row2 == $null) {
      $gradasi = 'merah';
      if ($count_row1 == $null) {
        $aksi_row = "$link_add_left $img_gray";
      } else {
        $aksi_row = "$img_gray $link_add_right";
      }
    } else {
      if ($count_row1 == $count_row2) {
        $check_icon = $link_check;
        $gradasi = 'hijau';
        $aksi_row = $link_check;
      } else {
        $gradasi =  'kuning';
        if ($count_row1 < $count_row2) {
          $aksi_row = "$link_prev";
        } else {
          $aksi_row = $link_next;
        }
      }
    }
  }

  $td_row = $mode != 'row' ? '' : "
    <td class=right>$count_row1</td>
    <td class=tengah>$aksi_row</td>
    <td class=left>$count_row2</td>
  ";


  # ============================================================
  # FINAL TR
  # ============================================================
  $tr .= "
    <tr class='gradasi-$gradasi'>
      <td>$i</td>
      <td>$table</td>
      <td class=right>$count_field1</td>
      <td class=tengah>$aksi_field</td>
      <td class=left>$count_field2</td>
      $td_field
      $td_row
    </tr>
  ";
}

$th_row = $mode != 'row' ? '' : "
  <th class=right>
    RECORDS
    <div class='abu f12'>$db_name1</div>
  </th>
  <th class=tengah>&nbsp;</th>
  <th class=left>
    RECORDS
    <div class='abu f12'>$db_name2</div>
  </th>
";
$th_field = "<th colspan=5>FIELD-PROPS</th>";

# ============================================================
# FINAL ECHO LIST TABLES
# ============================================================
echo "
  <table class=table>
    <thead class=gradasi-toska>
      <th>NO</th>
      <th>TABLE</th>
      <th class=right>
        COUNT FIELD
        <div class='abu f12'>$db_name1</div>
      </th>
      <th class=tengah>&nbsp;</th>
      <th class=left>
        COUNT FIELD
        <div class='abu f12'>$db_name2</div>
      </th>
      $th_field
      $th_row
    </thead>
    $tr
  </table>
";
