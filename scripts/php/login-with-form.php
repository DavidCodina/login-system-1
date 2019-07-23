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
  header("Location: ../../403.php?error=login_with_form_permission_error");
  exit(); //Technically not necessary.


} else {
  /* ===========================================================================
                    if ( isset($_POST['login_submit']) ){
  =========================================================================== */


  if ( isset($_POST['login_submit']) ){
    $email    = trim($_POST['username']); //This makes dealing with the possibility of an email easier.
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);


    /* =========================================================================
                          Open Connection to Database
    ========================================================================= */


    $connection = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD,DB_NAME);

    if ( mysqli_connect_error() ){

      $message = "<span style='color:rgb(200,0,0);'>" .
                       "We're Sorry. There was a failure in connecting to the database:<br><br>" .
                       "<span style='font-family:courier; font-size: 12px;'>" . mysqli_connect_error() ."</span>" .
                     "</span>";

      $status = "connection_error";


    } else {
      /* =======================================================================
                                    Log in
      ======================================================================= */

      //Create a template
      $sql = "SELECT * FROM users WHERE user_username = ? OR user_email = ?";

      //Create a prepared statement.
      $stmt = mysqli_stmt_init($connection);

      //Prepare the prepared statement.
      if( !mysqli_stmt_prepare($stmt, $sql) ){
        ////////////////////////////////////////////////////////////////////////
        //
        //  This message will be invoked if, for example, there is a syntax error with our SQL query.
        //  I imagine there's a way to get the actual problem, but this suffices for now.
        //
        ////////////////////////////////////////////////////////////////////////

        $message =  "<span style='color:rgb(200,0,0);'>" .
                         "<strong>Error:</strong> We're sorry. The SQL prepared statement failed:<br><br>" .
                         "<span style='font-family:courier; font-size: 12px;'>" . $sql . "</span>" .
                       "</span>";

        $status = "sql_error";


      } else {
        /* ================================
            Bind and Execute statement
        ================================ */

        //Bind the parameters to placeholders
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);

        mysqli_stmt_execute($stmt);


        //Can't use this.
        //$result = mysqli_stmt_get_result($stmt);


        //Originallly, I was using mysqli_store_result($connection);
        //But that didn't allow for mysqli_stmt_num_rows($stmt) to work:
        mysqli_stmt_store_result($stmt);

        mysqli_stmt_bind_result($stmt, $user_id, $user_first, $user_last, $user_email, $user_username, $user_password);

        $resultCheck = mysqli_stmt_num_rows($stmt);  //Not mysqli_num_rows($result);

        if ($resultCheck > 0) {
          //This is where we will verify the password.
          //Then if the password is correct, we will log the user in, by creating
          //a session for the user with the relevant session variables.


          //I'm just assuming here that there will only be one row.
          //Otherwise, it makes more sense to push the row to a $results array.
          while (mysqli_stmt_fetch($stmt)) {

            $row = [
              "user_id"       => $user_id,
              "user_first"    => $user_first,
              "user_last"     => $user_last,
              "user_email"    => $user_email,
              "user_username" => $user_username,
              "user_password" => $user_password
            ];
          }


          $passwordsMatch = password_verify($password, $row['user_password']);

          if ($passwordsMatch == false) {

            ////////////////////////////////////////////////////////////////////////
            //
            //  You don't want to create validation that would indicate to the user
            //  that the username/email or password is incorrect.
            //  However you can do some basic validation for empty fields.
            //
            //  We don't want to indicate that the password was incorrect.
            //  Rather, we simply say that one or the other was incorrect.
            //
            ////////////////////////////////////////////////////////////////////////


            $message = "<span style='color:rgb(200,0,0);'>" .
                         "The Username/Email or Password may be incorrect. (A)<br><br><span style='font-size: 400%;'>😮</span>" .
                       "</span>";

            $status  = "login_error";


          } elseif ($passwordsMatch == true) {
            session_start();

            $_SESSION['user_id']       = $row['user_id'];
            $_SESSION['user_first']    = $row['user_first'];
            $_SESSION['user_last']     = $row['user_last'];
            $_SESSION['user_email']    = $row['user_email'];
            $_SESSION['user_username'] = $row['user_username'];


            //The success message isn't actually being used
            //Because on success the user is immediately redirected.
            //Nonetheless, I have left this here.
            $message = "<span style='color:rgb(0,200,0);'>" .
                         "<strong>Success!!!</strong><br>You are now logged in!<br><br><span style='font-size: 400%;'>🎟</span>" .
                       "</span>";

            $status = "logged_in";
          }


        } else { //if ($resultCheck > 0) { ... } else { ...
          //////////////////////////////////////////////////////////////////////
          //
          //  We actually know that the Username / Email is what's incorrect.
          //  Because there were no results with that Username / Email
          //  However, we don't want to volunteer that information because
          //  it makes a hacker's job that much easier.
          //
          //////////////////////////////////////////////////////////////////////

          $message = "<span style='color:rgb(200,0,0);'>" .
                            "The Username/Email or Password may be incorrect. (B)<br><br><span style='font-size: 400%;'>😮</span>" .
                          "</span>";

          $status = "login_error";
        }
        mysqli_stmt_close($stmt);
      }
      //Put this inside of if (mysqli_connect_error()){ ... }
      //That is, inside of the else part.
      mysqli_close($connection);
    } //End of if ( mysqli_connect_error() ){

    ////////////////////////////////////////////////////////////////////////////
    //
    //  DO NOT put myqsli_close($conection) here.
    //  We have disabled potential connection warnings with @:
    //
    //    $connection = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD,DB_NAME);
    //
    //
    //  This allows us to gracefully handle things like inappropriate database configuartions.
    //  [i.e., define('DB_USER', 'wrong_user'); ]
    //  However, if a database connection is never established, then putting
    //  myqsli_close($conection) here will cause a different kind of problem:
    //
    //      Warning: mysqli_close() expects parameter 1 to be mysqli, boolean given in
    //      /Applications/XAMPP/xamppfiles/htdocs/mmtuts/php-tutorial-mmtuts/complete-login-system/app/login-process.php
    //      on line 127
    //
    //
    ////////////////////////////////////////////////////////////////////////////



    /* =========================================================================
                            Send Data and Redirect
    ========================================================================= */


    if ($status === "logged_in" ) {

      //If the login attempt is successful, we'll redirect to user.php.

      //This may not work on the live server...
      header("Location: ../../user.php");
      session_start();

      //Only set $_SESSION['formErrors'] if $formErrors is not empty.
      //For not doing client-side validation.
      // if ( isset($_SESSION['formErrors']) && count($formErrors) === 0) {
      //   unset($_SESSION['formErrors']);
      // } else if (!empty($formErrors)){
      //   $_SESSION['formErrors'] = $formErrors;
      // }

      $_SESSION['message'] = $message;
      $_SESSION['status']  = $status;
      $_SESSION['logged_in_with_form'] = '<span style='. "color:rgb(0,200,0);" . '>' .
                                          '<strong>Success!!!</strong><br>You are now logged in!<br><br><span style=' . "font-size:400%;" . '>🎟</span>' .
                                          '</span>';

    } else {
      //If the login attempt is unsuccessful, we'll redirect to back to the previous
      //page (referrer):

      $referrer = basename($_SERVER['HTTP_REFERER']);

      //This may not work on the live server...
      header("Location: ../../$referrer");
      session_start();


      //When the form is submitted directly, then assign $message and
      //$status to session variables.
      $_SESSION['message'] = $message;
      $_SESSION['status']  = $status;
    }

  } //End of if ( isset($_POST['login_submit']) ){
} //End of if ( $_SERVER['REQUEST_METHOD'] != 'POST' ){
?>
