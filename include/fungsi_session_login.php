<?php
function only_user($username)
{
  if (!isset($_SESSION['dipa_username']) || $_SESSION['dipa_username'] != $username) {
    echo '<script>location.replace("?")</script>';
    exit;
  }
}

function login_only()
{
  if (!isset($_SESSION['dipa_username'])) {
    echo '<script>location.replace("?")</script>';
    exit;
  }
}

function instruktur_only()
{
  if (!isset($_SESSION['dipa_username']) || $_SESSION['dipa_id_role'] != 2) die('<script>location.replace("?")</script>');
}

function peserta_only()
{
  if (!isset($_SESSION['dipa_username']) || $_SESSION['dipa_id_role'] != 1) die('<script>location.replace("?")</script>');
}
