<?php
session_start();
    $_SESSION['dipa_username'] = 'abi';
    $_SESSION['dipa_id_role'] = 2;
    $_SESSION['dipa_id_peserta'] = 51;
    header('location:index.php');