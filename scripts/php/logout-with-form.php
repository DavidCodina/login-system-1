<?php
//session_start(); was here, but that can potentially interfere with header()

////////////////////////////////////////////////////////////////////////////////
//
//  How to fix “Headers already sent” error in PHP:
//
//  https://stackoverflow.com/questions/8028957/how-to-fix-headers-already-sent-error-in-php
//  Some functions modifying the HTTP header are:
//
//      header / header_remove
//      session_start / session_regenerate_id
//      setcookie / setrawcookie
//
//
//  Thus, rather than putting session_start() at the top of the page, I put it below,
//  AFTER header("Location: ...");
//
//  Note: the error does not occur in my local server, but it did occur on the live server.
//
//
//////////////////////
//
//  This issue may also be caused by extra lines at the beginning or end of the file.
//
//
////////////////////////////////////////////////////////////////////////////////


include_once "config.php";


if ( $_SERVER['REQUEST_METHOD'] != 'POST' ){
  header("Location: ../../403.php?error=logout_with_form_permission_error");
  //exit(); //Technically not necessary.


} else {
  /* ===========================================================================
                    if ( isset($_POST['logout_submit']) ){
  =========================================================================== */


  if ( isset($_POST['logout_submit']) ){
    header("Location: ../../index.php?logged_out_with_form=true");
    session_start();
    session_unset();
    session_destroy();
  }
}
?>
