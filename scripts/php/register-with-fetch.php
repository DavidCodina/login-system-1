<?php
////////////////////////////////////////////////////////////////////////////////
//
//  We don't need session_start() here.
//  And, in fact, if we did use it here, it would
//  conflict with: header("Location: register.php?error=register_error");
//  It may not create an error on the local server, but it probably will on a live server.
//
////////////////////////////////////////////////////////////////////////////////
include_once "config.php";


if ( $_SERVER['REQUEST_METHOD'] != 'POST' ){
  //I would prefer to handle this with a session.
  header("Location: ../../403.php?error=register_with_fetch_permission_error");
  exit();


} else {
  /* ===========================================================================
                    if ( isset($_POST['registration_submit']) ){
  =========================================================================== */


  //////////////////////////////////////////////////////////////////////////////
  //
  //  Assuming that this file may handle multiple POST requests, I am checking
  //  here for $_POST['registration_submit']
  //  This is the name attribute of the <input type="submit"> for our regiatration form.
  //  Thus this code will only run if $_POST['registration_submit'] is set.
  //  The actual value that I set it to is not important.
  //
  //////////////////////////////////////////////////////////////////////////////


  if ( isset($_POST['registration_submit']) ){
    /* =========================================================================
                          Open Connection to Database
    ========================================================================= */


    $first            = trim($_POST['first']);
    $last             = trim($_POST['last']);
    $email            = strtolower(trim($_POST['email']));
    $username         = trim($_POST['username']);
    $password         = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);


    ////////////////////////////////////////////////////////////////////////////
    //
    //  The connection is opened before we do server-side validation.
    //  Why? Because we'll actually need to check certain inputs against
    //  data stored in the database.
    //  For example, if a user is registering, we need to check their username and email
    //  to make sure it's not already taken.
    //
    //  Use @ to suppress the warning, so the program won't break.
    //  Then handle it gracefully.
    //  Otherwise, the warning will prevent register.js from getting a JSON response.
    //
    //  We can create a connection error by putting in a faulty user name in the config.php
    //
    ////////////////////////////////////////////////////////////////////////////


    $connection = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD,DB_NAME);
    $formErrors = []; //Create an empty $errors array, and push each individual error to that array.

    //I have also seen: if (!$connection) { ... }
    if ( mysqli_connect_error() ){
      $message = "<span style='color:rgb(200,0,0);'>" .
                       "We're Sorry. There was a failure in connecting to the database:<br><br>" .
                       "<span style='font-family:courier; font-size: 12px;'>" . mysqli_connect_error() ."</span>" .
                     "</span>";

      $status = "connection_error";
    } else {
      /* =======================================================================
                                      Validation
      ======================================================================= */



      /* ================================
              Validate $first
      ================================ */


      if ( empty($first) ){
        array_push($formErrors, "Please fill out the first name field. (PHP)");
      } elseif (!preg_match("/^[a-zA-Z]*$/", $first)) {
        array_push($formErrors, "Please use only letters in first name. (PHP)");
      }


      /* ================================
              Validate $last
      ================================ */


      if ( empty($last) ){
        array_push($formErrors, "Please fill out the last name field. (PHP)");
      } elseif (!preg_match("/^[a-zA-Z]*$/", $last)) {
        array_push($formErrors, "Please use only letters in last name. (PHP)");
      }


      /* ================================
              Validate $email
      ================================ */


      if ( empty($email) ){
        array_push($formErrors, "Please fill out the email field. (PHP)");
      } elseif ( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
        //Change type="email" to type="text" in the form to really test this.
        array_push($formErrors, "Please use a valid email. (PHP)");


      } else {
        /* ================================
            Create a Prepared statement
        ================================ */


        //Create a template
        $sql = "SELECT * FROM users WHERE user_email = ?";

        //Create a prepared statement.
        $stmt = mysqli_stmt_init($connection);

        //Prepare the prepared statement.
        if( !mysqli_stmt_prepare($stmt, $sql) ){
          //////////////////////////////////////////////////////////////////////
          //
          //  This message will be invoked if, for example, there is a syntax error with our SQL query.
          //  I imagine there's a way to get the actual problem, but this suffices for now.
          //  Note: logging this kind of error to the user is generally not a good idea.
          //  In other words, logging the sql statement back to the browser isn't advisable
          //  becuase it might expose sensitive data.
          //  That said, it can be helpful during development.
          //
          //////////////////////////////////////////////////////////////////////

          $message =  "<span style='color:rgb(200,0,0);'>" .
                           "<strong>Error:</strong> The SQL prepared statement failed:<br><br>" .
                           "<span style='font-family:courier; font-size: 12px;'>" . $sql . "</span>" .
                         "</span>";

          $status = "sql_error";

          //////////////////////////////////////////////////////////////////////
          //
          //  In this case, we actually need to immediately exit().
          //  Why? Because if we don't, the validation check for duplicate emails will
          //  never run (the error will never get pushed to $formErrors), and the
          //  process will continue as if everything is fine.
          //
          //////////////////////////////////////////////////////////////////////


          $data = [
            "message"     => $message,
            "status"      => $status,
            "form_errors" => $formErrors
            //No need to send form data because the request was made with AJAX.
          ];

          echo json_encode($data);
          exit();


        } else {
          /* ================================
            Bind and Execute statement
          ================================ */


          //Bind the parameters to placeholders
          mysqli_stmt_bind_param($stmt, "s", $email);

          mysqli_stmt_execute($stmt);

          //////////////////////////////////////////////////////////////////////
          //
          //  Can't use this on some hosting sites:
          //
          //      $result = mysqli_stmt_get_result($stmt);
          //
          //  It requires the MYSQLnd module, and they won't allow it on shared hosting accounts.
          //  Thus we have to write this differently.
          //
          //////////////////////////////////////////////////////////////////////


          //Originallly, I was using mysqli_store_result($connection);
          //But that didn't allow for mysqli_stmt_num_rows($stmt) to work:
          mysqli_stmt_store_result($stmt);


          //Here, we could bind the result (then loop through it),
          //but we don't actually need it for anything.
          //mysqli_stmt_bind_result($stmt, $user_id, $user_first, $user_last, $user_email, $user_username, $user_password);

          $resultCheck = mysqli_stmt_num_rows($stmt);  //Not mysqli_num_rows($result);

          if ($resultCheck > 0) {
            array_push($formErrors, "That email already exists in our database. (PHP)");
          }
          mysqli_stmt_close($stmt);
        }
      }


      /* ================================
              Validate $username
      ================================ */

      //$username should not be any form of admin
      //$username should not have swear words.

      if ( empty($username) ){
        array_push($formErrors, "Please fill out the username field. (PHP)");


      } else {
        /* ================================
            Create a Prepared statement
        ================================ */


        //Create a template
        $sql = "SELECT * FROM users WHERE user_username = ?";

        //Create a prepared statement.
        $stmt = mysqli_stmt_init($connection);

        //Prepare the prepared statement.
        if( !mysqli_stmt_prepare($stmt, $sql) ){
          //////////////////////////////////////////////////////////////////////
          //
          //  This message will be invoked if, for example, there is a syntax error with our SQL query.
          //  I imagine there's a way to get the actual problem, but this suffices for now.
          //  Note: logging this kind of error to the user is generally not a good idea.
          //  In other words, logging the sql statement back to the browser isn't advisable
          //  becuase it might expose sensitive data.
          //  That said, it can be helpful during development.
          //
          //////////////////////////////////////////////////////////////////////

          $message = "<span style='color:rgb(200,0,0);'>" .
                           "<strong>Error:</strong> The SQL prepared statement failed:<br><br>" .
                           "<span style='font-family:courier; font-size: 12px;'>" . $sql . "</span>" .
                         "</span>";

          $status = "sql_error";


          //////////////////////////////////////////////////////////////////////
          //
          //  In this case, we actually need to immediately exit().
          //  Why? Because if we don't, the validation check for duplicate emails will
          //  never run (the error will never get pushed to $formErrors), and the
          //  process will continue as if everything is fine.
          //
          //////////////////////////////////////////////////////////////////////


          $data = [
            "message"     => $message,
            "status"      => $status,
            "form_errors" => $formErrors
            //No need to send form data because the request was made with AJAX.
          ];
          echo json_encode($data);
          exit();


        } else {
          /* ================================
            Bind and Execute statement
          ================================ */


          //Bind the parameters to placeholders
          mysqli_stmt_bind_param($stmt, "s", $username);

          mysqli_stmt_execute($stmt);

          //////////////////////////////////////////////////////////////////////
          //
          //  Can't use this on some hosting sites:
          //
          //      $result = mysqli_stmt_get_result($stmt);
          //
          //  It requires the MYSQLnd module, and they won't allow it on shared hosting accounts.
          //  Thus we have to write this differently.
          //
          //////////////////////////////////////////////////////////////////////

          //Originallly, I was using mysqli_store_result($connection);
          //But that didn't allow for mysqli_stmt_num_rows($stmt) to work:
          mysqli_stmt_store_result($stmt);

          //Here, we could bind the result (then loop through it),
          //but we don't actually need it for anything.
          //mysqli_stmt_bind_result($stmt, $user_id, $user_first, $user_last, $user_email, $user_username, $user_password);

          $resultCheck = mysqli_stmt_num_rows($stmt);  //Not mysqli_num_rows($result);

          if ($resultCheck > 0) {
            array_push($formErrors, "That username already exists in our database. (PHP)");
          }

          mysqli_stmt_close($stmt);
        }
      }


      /* ================================
              Validate $password
      ================================ */


      if ( empty($password) ){
        array_push($formErrors, "Please fill out the password field. (PHP)");
      } elseif (strlen($password) < 6) {
        array_push($formErrors, "Please enter a password that is at least six characters. (PHP)");
      } elseif ($password !== $confirm_password) {
        array_push($formErrors, "The passwords must match. (PHP)");
      } else {
        ////////////////////////////////////////////////////////////////////////
        //
        //  The video didn't explicityly go into where to implement password_hash()
        //  However, if the user provides an empty string for the password field,
        //  and we then call password_hash() on that field, it will still convert
        //  it to a long string of gibberish.
        //  Therefore, it makes sense to only call password_hash() after all
        //  of the validation checks for that piece of data.
        //
        ////////////////////////////////////////////////////////////////////////

        $password = password_hash($password, PASSWORD_DEFAULT);
      }


      /* ================================
        Check if $formErrors is empty
      ================================ */


      //If the $formErrors array is NOT empty then do this.
      if (! empty($formErrors) ) {
        $message =  "<span style='color:rgb(200,0,0);'>There are form errors.</span>";
        $status  = "user_error";


      } else {
        /* =====================================================================
                                     INSERT Data
        ===================================================================== */
        //If the $formErrors array IS empty then do continue with the
        //database operations.


        /* ================================
          Create a Prepared statement
        ================================ */


        //Create a template
        $sql = "INSERT INTO users (user_first, user_last, user_email, user_username, user_password) VALUES (?, ?, ?, ?, ?)";

        //Create a prepared statement.
        $stmt = mysqli_stmt_init($connection);

        //Prepare the prepared statement.
        if( !mysqli_stmt_prepare($stmt, $sql) ){
          //////////////////////////////////////////////////////////////////////
          //
          //  This will get triggered if, for example, we mispell one of the field names in the INSERT statement.
          //  Note: logging this kind of error to the user is generally not a good idea.
          //  In other words, logging the sql statement back to the browser isn't advisable
          //  becuase it might expose sensitive data.
          //  That said, it can be helpful during development.
          //
          //////////////////////////////////////////////////////////////////////

          $message = "<span style='color:rgb(200,0,0);'>" .
                           "<strong>Error:</strong> The SQL prepared statement failed:<br><br>" .
                           "<span style='font-family:courier; font-size: 12px;'>" . $sql . "</span>" .
                         "</span>";



          $status  = "sql_error";


          $data = [
            "message"     => $message,
            "status"      => $status,
            "form_errors" => $formErrors
            //No need to send form data because the request was made with AJAX.
          ];
          echo json_encode($data);
          exit();


        } else {
          /* ================================
            Bind and Execute statement
          ================================ */


          //Bind the parameters to placeholders
          mysqli_stmt_bind_param($stmt, "sssss", $first, $last, $email, $username, $password);

          mysqli_stmt_execute($stmt);
          mysqli_stmt_close($stmt);


          $message = "<span style='color:rgb(0,200,0);'>" .
                            "<strong>Success!</strong><br><br> Thank you for registering with us. You may log in now.<br><br>" .
                            "<span style='font-size: 400%;'>🚀</span>" .
                          "</span>";

          $status = "success";
        }
      } //End of if (! empty($errors) ) {

      //////////////////////////////////////////////////////////////////////
      //
      //  Put this inside of if (mysqli_connect_error()){ ... }
      //  That is, inside of the else part.
      //  See comment in login.process.php for why.
      //
      //////////////////////////////////////////////////////////////////////
      mysqli_close($connection);
    } //End of if (mysqli_connect_error()){


    $data = [
      "message"     => $message,
      "status"      => $status,
      "form_errors" => $formErrors
      //No need to send form data because the request was made with AJAX.
    ];

    echo json_encode($data);

  } //End of if ( isset($_POST['registration_submit']) ){
} //End of if (  $_SERVER['REQUEST_METHOD'] != 'POST' ){


//////////////////////////////////////////////////////////////////////
//
//  Sometimes, the ending PHP tag is known to cause this issue.
//  In a file containing PHP and nothing else, removing it often
//  solves "headers already sent" problem.
//
//////////////////////////////////////////////////////////////////////
