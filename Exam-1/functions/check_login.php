<?php
session_start();
$bool_name = false;
$bool_pass = false;

if (isset($_SESSION['person'])) {
  if (isset($_POST['name'])) {
    if ($_POST['name'] == $_SESSION['person']['username'] || $_POST['name'] == $_SESSION['person']['email']) {
      $bool_name = true;
    }
    if ($_POST['password'] == $_SESSION['person']['password']) {
      $bool_pass = true;
    }
  }
} else {
  header('location: ../login.php?error=register');
  exit;
}

if ($bool_name && $bool_pass) {
  if ($_SESSION['person']['userType'] == 'user') {
    header('location: ../front/index.php');
    exit;
  } else if ($_SESSION['person']['userType'] == 'admin') {
    header('location: ../dashboard/index.php');
    exit;
  }
} elseif (!$bool_name) {
  header('location: ../login.php?error=name');
  exit;
} elseif (!$bool_pass) {
  header('location: ../login.php?error=pass');
  exit;
}
