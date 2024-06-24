<?php
if (isset($_SESSION['dipa_username'])) {
  echo '<script>location.replace("?dashboard")</script>';
  exit;
}
?>
<?php
include 'about.php';
// include 'teams.php';
include 'leaderboard.php';
