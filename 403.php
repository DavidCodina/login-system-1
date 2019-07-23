<?php require 'includes/header.php'; ?>

<?php
/* =============================================================================
                            Render 403 Errors
============================================================================= */


////////////////////////////////////////////////////////////////////////////////
//
//  Originally, I was using the toast notification feature to display 403 Forbidden messages
//  both in index.php and register.php doing this:
//
//        <script>
//          const error_message = "-?php echo get_error_message(); ?-";
//
//          if (error_message){
//            render_toast_div(error_message);
//          }
//        <script>
//
//
//  The problem I kept running into is that there's no good way to show these kinds of errors
//  Both when Javascript is enabled (with a toast notification), and also when Javascript
//  is disabled (without a toast notification).
//
//  I could render the messages using both approaches, and hide the [non-toast] div with
//  programmatic CSS (Javascript) when using toast notifications. However, I also wanted to make
//  the  [non-toast] div disappear after a short while:
//
//      echo "<meta http-equiv='refresh' content='3; url=" . $_SERVER['PHP_SELF'] ."'/>";
//
//
//  So... unless I wanted pages rendering [non-toast] div to ALWAYS refresh 3 seconds after one
//  is redirected to that page with a 403 error, then it's just not feasible.
//
//  For this type of error, the real solution is to just create a 403.php page and don't use
//  the toast notifications at all.
//
//  Finally, because these functions are only being used by 403.php.
//  It made more sense to declare them here, rather than in helpers.php
//  It makes 403.php more portable.
//
////////////////////////////////////////////////////////////////////////////////


function get_error_message(){
  if ( isset($_GET['error']) ){

    //This could be a switch...
    /* ================================
         'user_permission_error'
    ================================ */


    //http://localhost/mmtuts/php-tutorial-mmtuts/login-system-1/user.php
    if ($_GET['error'] == 'user_permission_error'){
      return "<span style='color:rgb(200,0,0);'><strong>Warning!</strong><br><br><span style='font-family:courier'>user.php</span> is for logged in users only!</span>";
    }

    /* ================================
      'login_with_X_permission_error'
    ================================ */


    //http://localhost/mmtuts/php-tutorial-mmtuts/login-system-1/scripts/php/login-with-form.php
    elseif ($_GET['error'] == 'login_with_form_permission_error'){
      return "<span style='color:rgb(200,0,0);'><strong>Warning!</strong><br><br><span style='font-family:courier'>login-with-form.php</span> is for POST requests only!</span>";
    }

    //http://localhost/mmtuts/php-tutorial-mmtuts/login-system-1/scripts/php/login-with-fetch.php
    elseif ($_GET['error'] == 'login_with_fetch_permission_error'){
      return "<span style='color:rgb(200,0,0);'><strong>Warning!</strong><br><br><span style='font-family:courier'>login-with-fetch.php</span> is for POST requests only!</span>";
    }


    /* ================================
    'logout_with_X_permission_error'
    ================================ */


    //http://localhost/mmtuts/php-tutorial-mmtuts/login-system-1/scripts/php/logout-with-form.php
    elseif ($_GET['error'] == 'logout_with_form_permission_error'){
      return "<span style='color:rgb(200,0,0);'><strong>Warning!</strong><br><br><span style='font-family:courier'>logout-with-form.php</span> is for POST requests only!</span>";
    }

    //http://localhost/mmtuts/php-tutorial-mmtuts/login-system-1/scripts/php/logout-with-fetch.php
    elseif ($_GET['error'] == 'logout_with_fetch_permission_error'){
      return "<span style='color:rgb(200,0,0);'><strong>Warning!</strong><br><br><span style='font-family:courier'>logout-with-fetch.php</span> is for POST requests only!</span>";
    }


    /* ================================
      'register_with_X_permission_error'
    ================================ */


    //http://localhost/mmtuts/php-tutorial-mmtuts/login-system-1/scripts/php/register-with-form.php
    elseif ($_GET['error'] == 'register_with_form_permission_error'){
      return "<span style='color:rgb(200,0,0);'><strong>Warning!</strong><br><br><span style='font-family:courier'>register-with-form.php</span> is for POST requests only!</span>";
    }

    //http://localhost/mmtuts/php-tutorial-mmtuts/login-system-1/scripts/php/register-with-fetch.php
    if ($_GET['error'] == 'register_with_fetch_permission_error'){
      return "<span style='color:rgb(200,0,0);'><strong>Warning!</strong><br><br><span style='font-family:courier'>register-with-fetch.php</span> is for POST requests only!</span>";
    }


    /* ================================
      'delete_with_X_permission_error'
    ================================ */


    //http://localhost/mmtuts/php-tutorial-mmtuts/login-system-1/scripts/php/delete-with-form.php
    elseif ($_GET['error'] == 'delete_with_form_permission_error'){
      return "<span style='color:rgb(200,0,0);'><strong>Warning!</strong><br><br><span style='font-family:courier'>delete-with-form.php</span> is for POST requests only!</span>";
    }

    //http://localhost/mmtuts/php-tutorial-mmtuts/login-system-1/scripts/php/delete-with-fetch.php
    elseif ($_GET['error'] == 'delete_with_fetch_permission_error'){
      return "<span style='color:rgb(200,0,0);'><strong>Warning!</strong><br><br><span style='font-family:courier'>delete-with-fetch.php</span> is for POST requests only!</span>";
    }

  /* ================================

  ================================ */

  } else {
    return "";
  }
}


function render_error_div(){
  if ( isset($_GET['error']) ) {
      echo "<div id='forbidden-error-div'>" . get_error_message($_GET['error'])  . "</div>";
  }
}
?>


<!-- ===========================================================================

============================================================================ -->


  <main>
    <h1 style="text-align:center; color: violet; text-shadow:-2px 2px 4px rgba(0,0,0,0.15);">403: Access Forbidden</h1>


  <?php render_error_div(); ?>

  </main>


  <?php require 'includes/footer.php'; ?>


  <script src="scripts/js/helpers.js"></script>
  <script src="scripts/js/login-with-fetch.js"></script>
  <script src="scripts/js/logout-with-fetch.js"></script>
</body>
</html>
