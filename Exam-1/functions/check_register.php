<?php

session_start();

$empty_errors = [];
$errors_validate = [];
$num_error = 0;

function check_empty($value)
{
  global $empty_errors, $num_error;
  if ($value == '') {
    $empty_errors[$num_error] = 'empty';
  }
  $num_error++;
}

$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password1 = $_POST['password1'];
$password2 = $_POST['password2'];
$userType = @$_POST['userType'];
$image = @$_FILES['image'];

check_empty($username); // 0
check_empty($email); // 1
check_empty($password1); // 2

// username validate
if (isset($empty_errors[0])) {
  $errors_validate['username'] = 'Username cannot be empty.';
} else if (strlen($username) < 5 || strlen($username) > 20) {
  $errors_validate['username'] = 'Username must be between 5 and 20 characters long.';
} else {
  preg_match('/[\w\s]+/i', $username, $userTest);
  if (strlen($userTest[0]) != strlen($username)) {
    $errors_validate['username'] = 'Username can only contain letters, numbers, and underscores.';
  }
}

// email validate
if (isset($empty_errors[1])) {
  $errors_validate['email'] = 'Email cannot be empty.';
} else {
  $email = filter_var($email, FILTER_VALIDATE_EMAIL);
  if ($email == false) {
    $errors_validate['email'] = 'Invalid email format.';
  }
}

// password validate
if (isset($empty_errors[2])) {
  $errors_validate['password'] = 'Password cannot be empty.';
} elseif (strlen($password1) < 8) {
  $errors_validate['password'] = 'Password must be at least 8 characters long.';
} else {
  preg_match('/[\w\s]+/i', $password1, $passTest);
  if (strlen($passTest[0]) != strlen($password1)) {
    $errors_validate['password'] = 'Password can only contain letters, numbers, and underscores.';
  } else if ($password1 != $password2) {
    $errors_validate['password2'] = 'Passwords do not match';
  }
}

// user type validate
if (!isset($_POST['userType'])) {
  $errors_validate['userType'] = 'Must Check on Role cannot be empty.';
}

// image validate
// الخاص بالصوره name يمكن ان التلاعب في ال
if (!isset($_FILES['image']) || $image['error'] == 4) {
  $errors_validate['image'] = 'Error uploading image.';
} else {
  $types = ['jpg', 'jpeg', 'png'];
  $type = @end(explode('.', $image['name']));
  if (in_array($type, $types)) {
    if ($image['size'] > 1.5 * 1024 * 1024) {
      $errors_validate['image'] = 'Image is too large. Maximum size is 1.5 megabytes.';
    }
  } else {
    $errors_validate['image'] = 'Invalid image type. Only JPEG, PNG, and JPG are allowed.';
  }
}

$_SESSION['person'] = [
  'username' => $username,
  'email' => $email,
  'password1' => $password1,
  'password2' => $password2,
  'userType' => $userType,
];

if (empty($errors_validate)) {
  $new_name_image = uniqid() . '.' . $type;
  move_uploaded_file($image['tmp_name'], '../uploads/profile/' . $new_name_image);

  $_SESSION['person'] = [
    'username' => $username,
    'email' => $email,
    'password' => $password1,
    'userType' => $userType,
    'image' => $new_name_image
  ];


  header('location: ../login.php');
} else {
  $_SESSION['errors'] = $errors_validate;
  header('location: ../register.php');
  exit;
}