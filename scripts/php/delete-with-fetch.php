<?php
include_once "config.php";


if ( $_SERVER['REQUEST_METHOD'] != 'POST' ){
  header("Location: ../../403.php?error=delete_with_fetch_permission_error");
  //exit(); //Technically not necessary.


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


        //Why use session_unset() before session_destroy()?
        //That's a good question. See here for more info:
        //https://stackoverflow.com/questions/45882855/do-you-need-to-use-session-unset-before-session-destroy
        session_start();
        session_unset();
        session_destroy();

        ////////////////////////////////////////////////////////////////////////////
        //
        //
        //  The message does not actually get used by delete-with-fetch.js,
        //  But I am including it here all the same.
        //  What actually happens is that delete-with-fetch.js checks that
        //
        //      data.status == "deleted_with_fetch"
        //
        //
        //  Then if that's true it redirects to:
        //
        //       window.location.replace("index.php?deleted_with_fetch=true");
        //
        //
        //  Thus we do not call:
        //
        //        header("Location: ../../index.php?deleted_with_fetch=true");
        //
        //
        //  in this file. Instead, we delegate that responsibility to deleted-with-fetch.js.
        //  We respond back to deleted-with-fetch.js with a $data array containing a
        //  "status" key that is then used by deleted-with-fetch.js to redirect.
        //
        //  Note: I am also including a "message" key in the $data array.
        //  However, it's not actually used by deleted-with-fetch.js when:
        //
        //        $status  = "deleted_with_fetch";
        //
        //  Instead, when deleted-with-fetch.js redirects to index.php?deleted_with_fetch=true,
        //  index.php uses that parameter to render the message there (which is constructed anew).
        //
        //  However, when there is some other kind of "message" / "status", we will use this
        //  design to render other types of messages with the toast notification.
        //
        //
        ////////////////////////////////////////////////////////////////////////////


        $message = "<span style='color:rgb(0,200,0);'>" .
                       "<strong>Success!</strong><br><br> Your account has been deleted.<br><br>" .
                       "<span style='font-size: 400%;'>🚀</span>" .
                     "</span>";
        $status  = "deleted_with_fetch";

      }
    } //End of: if (mysqli_connect_error()){ ... } else { ... }
    $data = ["message" => $message, "status" => $status];
    echo json_encode($data);
  }//End of: if ( isset($_POST['delete_user_submit']) ){
} //End of: if ($_SERVER['REQUEST_METHOD'] != 'POST'){ ... } else { ... }
?>
