<?php
////////////////////////////////////////////////////////////////////////////////
//
//  Don't put session_start() here.
//  If we did use it here, it would/could conflict with:
//
//         header("Location: ...");
//
//
//  It may not create an error on the local server, but it probably will on a live server.
//
//////////////////////////////////////////////////////////////////////////////

include_once "config.php";


if ( $_SERVER['REQUEST_METHOD'] != 'POST' ){
  header("Location: ../../403.php?error=login_with_fetch_permission_error");
  exit();
} else {

  /* ===========================================================================
                    if ( isset($_POST['login_submit']) ){
  =========================================================================== */


  if ( isset($_POST['login_submit']) ){
    //You could put session_start() here, but I prefer to put it right before $_SESSION is used.

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
      //You don't want to create validation that would indicate to the user
      //that the username/email or password is incorrect.
      //However you can do some basic validation for empty fields.


      //Create a template
      $sql = "SELECT * FROM users WHERE user_username = ? OR user_email = ?";

      //Create a prepared statement.
      $stmt = mysqli_stmt_init($connection);

      //Prepare the prepared statement.
      if( !mysqli_stmt_prepare($stmt, $sql) ){
        //This message will be invoked if, for example, there is a syntax error with our SQL query.
        //I imagine there's a way to get the actual problem, but this suffices for now.


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


          ////////////////////////////////////////////////////////////////////
          //
          //  In this case we are explicitly using true and false in conjunction with an
          //  ifelse, and not just an else. Why?
          //  It was explained that this is a failsafe.
          //  Suppose that for some reason password_verify() returned something else instead.
          //  I don't know... The mmtuts guy can't think of an actual example, but he prefers
          //  to do it this way. For now, I will go along with it.
          //
          ////////////////////////////////////////////////////////////////////

          if ($passwordsMatch == false) {
            //We don't want to indicate that the password was incorrect.
            //Rather, we simply say that one or the other was incorrect.

            $message = "<span style='color:rgb(200,0,0);'>" .
                         "The Username/Email or Password may be incorrect. (A)<br><br><span style='font-size: 400%;'>😮</span>" .
                       "</span>";

            $status  = "login_error";


          } elseif ($passwordsMatch == true) {
            session_start();

            //////////////////////////////////////////////////////////////////
            //
            //  Here we will login the user.
            //  To do so we use a session.
            //  We will set a session variable for each field in the record that we might want to use.
            //  That said, I imagine we could also just set one session variable with ALL the data.
            //
            //  Initially I set the session variables as:
            //
            //        $_SESSION['id']       = $row['user_id'];
            //        $_SESSION['first']    = $row['user_first'];
            //        $_SESSION['last']     = $row['user_last'];
            //        $_SESSION['email']    = $row['user_email'];
            //        $_SESSION['username'] = $row['user_username'];
            //
            //
            //  However, that's somewhat vague.
            //  In larger applications this could be problematic.
            //  For example, you might want to have ids for several different things.
            //  Thus it's better to be more specific.
            //  This means not only prepending user_ for the databse field names,
            //  but also the PHP session variables.
            //
            //////////////////////////////////////////////////////////////////

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
                        Send Data Back to login-with-fetch.js
    ========================================================================= */


    //This is used by user.php.
    //This wasn't being read correctly by the Javascript until I got the quotes just right.
    //Make sure that there are not spaces between css property:value.
    $_SESSION['logged_in_with_fetch'] = '<span style='. "color:rgb(0,200,0);" . '>' .
                                        '<strong>Success!!!</strong><br>You are now logged in!<br><br><span style=' . "font-size:400%;" . '>🎟</span>' .
                                        '</span>';
    $data = [
      "message"     => $message,
      "status"      => $status
      //I may add formErrors later...
    ];

    echo json_encode($data);

  } //End of if ( isset($_POST['login_submit']) ){
} //End of if ( $_SERVER['REQUEST_METHOD'] != 'POST' ){
?>
