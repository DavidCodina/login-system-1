<?php require 'includes/header.php'; ?>


  <!-- =========================================================================

  ========================================================================== -->

  <main>
    <h1 style="text-align:center; color: violet; text-shadow:-2px 2px 4px rgba(0,0,0,0.15);">Home</h1>










  </main>


  <!-- Note: I have NOT included </body> and </html> in footer.php
  This way, I can include <script>s below as needed, and not worry about
  the scripts loading before the html has.
  Such cases could potentially create errors. -->
  <?php require 'includes/footer.php'; ?>


  <script src="scripts/js/helpers.js"></script>
  <script src="scripts/js/login-with-fetch.js"></script>
  <script src="scripts/js/logout-with-fetch.js"></script>
  <script>
    /* =========================================================================
                     Check for $_GET['logged_out_with_fetch']
    ========================================================================= */


    ////////////////////////////////////////////////////////////////////////////
    //
    //
    //  When the user logs out with fetch, logout-with-fetch.php will redirect here:
    //
    //       header("Location: ../../index.php?logged_out_with_fetch=true");
    //
    //
    //  In this case, we will create a logged out confirmation message, and assign it to $is_logged_out.
    //  Otherwise we set $is_logged_out = "".
    //
    //  If Javascirpt is enabled, we will then create a corresponding Javascript variable called
    //  is_logged_out, and assign $is_logged_out (PHP) to is_logged_out (JS).
    //
    //  Then we will conditionally render a toast notification.
    //
    ////////////////////////////////////////////////////////////////////////////


    <?php
    if ( isset($_GET['logged_out_with_fetch']) ){
      if ($_GET['logged_out_with_fetch'] == 'true'){
        $is_logged_out = "<span style='color:rgb(0,200,0);'>" .
                    "<strong>Success!</strong><br>You are now logged out!<br><br><span style='font-size: 400%;'>👍🏼</span>" .
                  "</span>";
      }
    } else {
      $is_logged_out = "";
    }
    ?>


    const is_logged_out = "<?php echo $is_logged_out ?>";

    if (is_logged_out) { render_toast_div(is_logged_out); }



    /* =========================================================================
                     Check for $_GET['deleted_with_fetch']
    ========================================================================= */


    <?php
    if ( isset($_GET['deleted_with_fetch']) ){
      if ($_GET['deleted_with_fetch'] == 'true'){
        $is_deleted = "<span style='color:rgb(0,200,0);'>" .
                        "<strong>Success!</strong><br>Your user account has been deleted.<br><br><span style='font-size: 400%;'>👍🏼</span>" .
                      "</span>";
      }
    } else {
      $is_deleted = "";
    }
    ?>


    const is_deleted = "<?php echo $is_deleted ?>";

    if (is_deleted) { render_toast_div(is_deleted); }
  </script>
</body>
</html>
