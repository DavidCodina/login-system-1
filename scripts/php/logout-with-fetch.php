<?php
include_once "config.php";


if ( $_SERVER['REQUEST_METHOD'] != 'POST' ){
  header("Location: ../../403.php?error=logout_with_fetch_permission_error");
  //exit(); //Technically not necessary.


} else {
  /* ===========================================================================
                    if ( isset($_POST['logout_submit']) ){
  =========================================================================== */


  if ( isset($_POST['logout_submit']) ){
    session_start();
    //Why use session_unset() before session_destroy()?
    //That's a good question. See here for more info:
    //https://stackoverflow.com/questions/45882855/do-you-need-to-use-session-unset-before-session-destroy
    session_unset();
    session_destroy();

    ////////////////////////////////////////////////////////////////////////////
    //
    //
    //  The message does not actually get used by logout-with-fetch.js,
    //  But I am including it here all the same.
    //  What actually happens is that logout-with-fetch.js checks that
    //
    //      data.status == "logged_out_with_fetch"
    //
    //
    //  Then if that's true it redirects to:
    //
    //       window.location.replace("index.php?logged_out_with_fetch=true");
    //
    //
    //  Thus we do not call:
    //
    //        header("Location: ../../index.php?logged_out_with_fetch=true");
    //
    //
    //  in this file. Instead, we delegate that responsibility to logout-with-fetch.js.
    //  We respond back to logout-with-fetch.js with a $data array containing a
    //  "status" key that is then used by logout-with-fetch.js to redirect.
    //
    //  Note: I am also including a "message" key in the $data array.
    //  However, it's not actually used by logout-with-fetch.js when:
    //
    //        $status  = "logged_out_with_fetch";
    //
    //  Instead, when logout-with-fetch.js redirects to index.php?logged_out_with_fetch=true,
    //  index.php uses that parameter to render the message there (which is constructed anew).
    //
    //  However, if there were some other kind of "message" / "status", we could still use this
    //  design to render other types of messages with the toast notification.
    //  For example, in login-with-fetch.php, login user errors, connection errors, and SQL
    //  syntax errors are passed to login-with-fetch.js as $message values, and rendered.
    //
    //  Since we are not connecting to the database here, there's really no other types of message
    //  to forward to logout-with-fetch.js, but (again) it's useful to leave the code as it is
    //  just in case we want to pass other types of messages in future projects.
    //
    ////////////////////////////////////////////////////////////////////////////


    $message = "<span style='color:rgb(0,200,0);'>" .
               "<strong>Success!</strong><br>You are now logged out!<br><br><span style='font-size: 400%;'>👍🏼</span>" .
               "</span>";
    $status  = "logged_out_with_fetch";
    $data    =  ["message" => $message, "status" => $status];

    echo json_encode($data);
  } //End of if ( isset($_POST['logout_submit']) ){
} //End of if ( $_SERVER['REQUEST_METHOD'] != 'POST' ){
?>
