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
  header("Location: ../../403.php?error=delete_with_form_permission_error");
  exit(); //Technically not necessary.


} else {
  /* ===========================================================================
                    if ( isset($_POST['delete_user_submit']) ){
  =========================================================================== */


  if ( isset($_POST['delete_user_submit']) ){
    /* =========================================================================
                          Open Connection to Database
    ========================================================================= */


    $user_id = $_POST['user_id'];

    $connection = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD,DB_NAME);

    if ( mysqli_connect_error() ){
      $message = "<span style='color:rgb(200,0,0);'>" .
                     "We're Sorry. There was a failure in connecting to the database:<br><br>" .
                     "<span style='font-family:courier; font-size: 12px;'>" . mysqli_connect_error() ."</span>" .
                   "</span>";
      $status  = "connection_error";

      header("Location: ../../user.php");

      session_start();
      $_SESSION['message'] = $message;
      $_SESSION['status']  = $status;
      exit();


    } else {
      /* =======================================================================
                                 DELETE Record
      ======================================================================= */


      /* ================================
          Create a Prepared statement
      ================================ */


      //Create a template
      $sql = "DELETE FROM users WHERE user_id = ?";

      //Create a prepared statement.
      $stmt = mysqli_stmt_init($connection);

      //Prepare the prepared statement.
      if( !mysqli_stmt_prepare($stmt, $sql) ){
        //This message will be invoked if, for example, there is a syntax error with our SQL query.
        //I imagine there's a way to get the actual problem, but this suffices for now.

        $message = "<span style='color:rgb(200,0,0);'>" .
                       "<strong>Error:</strong> We're sorry. The SQL prepared statement failed:<br><br>" .
                       "<span style='font-family:courier; font-size: 12px;'>" . $sql . "</span>" .
                     "</span>";
        $status  = "sql_error";

        header("Location: ../../user.php");

        session_start();
        $_SESSION['message'] = $message;
        $_SESSION['status']  = $status;
        exit();


      } else {
        /* ================================
          Bind and Execute statement
        ================================ */


        //Bind the parameters to placeholders
        mysqli_stmt_bind_param($stmt, "s", $user_id);

        //Execute and close stmt
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        //Close connection
        mysqli_close($connection);


        /* ================================
        Log the user out (Destroy the session).
        ================================ */

        $message = "<span style='color:rgb(0,200,0);'>" .
                       "<strong>Success!</strong><br><br> Your account has been deleted.<br><br>" .
                       "<span style='font-size: 400%;'>🚀</span>" .
                     "</span>";
        $status  = "deleted_with_form";

        header("Location: ../../index.php?deleted_with_form=true");
        session_start();
        $_SESSION['message'] = $message;
        $_SESSION['status']  = $status;

        ////////////////////////////////////////////////////////////////////////
        //
        //  Rather than destroying the session here, I assign $message, and $status to session variables,
        //  and redirect to index.php?deleted_with_form=true.
        //  At this point, the user record has been deleted, but the user is still logged in
        //  (i.e., the session is still active).
        //  Once at index.php?deleted_with_form=true, we will:
        //
        //      session_unset();
        //      session_destroy();
        //
        //
        //  But actually, we want to do this:
        //
        //        if ( isset($_GET['deleted_with_form']) ){
        //          if ($_GET['deleted_with_form'] == 'true'){
        //            session_unset();
        //            session_destroy();
        //          }
        //        }
        //
        //
        //  inside of header.php AFTER render_message_div() and BEFORE the navigation.
        //  That way, the Account link and Logout feature will not be rendered.
        //  Rather, the Login and Signup features will display.
        //
        ////////////////////////////////////////////////////////////////////////
      }
    } //End of: if (mysqli_connect_error()){ ... } else { ... }
  }//End of: if ( isset($_POST['delete_user_submit']) ){
} //End of: if ($_SERVER['REQUEST_METHOD'] != 'POST'){ ... } else { ... }
?>
