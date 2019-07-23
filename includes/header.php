<?php session_start(); ?>
<?php require 'scripts/php/helpers.php'; ?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title> - </title>
  <link rel="stylesheet" href="style.css">
</head>


<body>
  <div id="toast-div"></div>
  <?php render_message_div(); ?>


  <?php
  if ( isset($_GET['deleted_with_form']) ){
    if ($_GET['deleted_with_form'] == 'true'){
      session_unset();
      session_destroy();
    }
  }
  ?>

  <header>
    <nav class="primary-nav">
      <!-- Here we are linking to the image not from the location of header.php, but
      from the location of the file that includes/requires header.php -->
      <a id="home-link" href="index.php"><img id="link-logo-img" src="images/logo.png" alt="logo"/></a>
      <a id="about-link" href="about.php">About</a>

      <?php
      /* =======================================================================
                     Conditionally the login form and signup link
      ======================================================================= */


      ////////////////////////////////////////////////////////////////////////////////
      //
      //  Initially, I was just using:
      //
      //        if (empty($_SESSION)) { ... }
      //
      //
      //  However, this is problematic in that there could be other sessions
      //  not specifically related to a user being logged in.
      //  For that reason, it's better to be precise:
      //
      //        if ( empty($_SESSION['user_id']) ) { ... }
      //
      //
      ////////////////////////////////////////////////////////////////////////////////


      if ( empty($_SESSION['user_id']) ) {
        $login_and_signup = <<<LOGIN_FORM_AND_SIGNUP_LINK
<form id="login-form" action="scripts/php/login-with-form.php" method="POST">
  <input type="text"     name="username" placeholder="Username / Email.." />
  <input type="password" name="password" placeholder=" Password.." />
  <input class="submit" type="submit" name="login_submit" value="Login">
</form>

<a id="register-link" href="register.php">Sign Up</a>
LOGIN_FORM_AND_SIGNUP_LINK;

        echo $login_and_signup;
      } else {
        echo "<a id='account-link' href='user.php'>Account</a>";

        $logout_form = <<<LOGOUT_FORM
<form id="logout-form" action="scripts/php/logout-with-form.php" method="POST">
<input class="submit" type="submit" name="logout_submit" value="Logout">
</form>
LOGOUT_FORM;
        echo $logout_form;
      }
      ?>
    </nav>

    <?php
    /* =========================================================================
                           Conditionally render the banner:
    ========================================================================= */


    ////////////////////////////////////////////////////////////////////////////
    //
    //
    //  Initially, I was doing this:
    //
    //      $file_name = basename(__FILE__);
    //
    //
    //  However, this was giving me: header.php.
    //  What I really wanted was:
    //
    //      $file_name = basename($_SERVER["SCRIPT_FILENAME"]);
    //
    //
    ////////////////////////////////////////////////////////////////////////////

    $file_name = basename($_SERVER["SCRIPT_FILENAME"]);
    if ($file_name == 'index.php') {
      //Here we are linking to the image not from the location of header.php, but
      //from the location of the file that includes/requires header.php
      echo "<img id='header-banner' src='images/banner.jpg' alt='banner image'/>";
    }
    ?>
  </header>

  <noscript><strong>Warning!</strong> Please enable Javascript in your browser for this page to function correctly.</noscript>
