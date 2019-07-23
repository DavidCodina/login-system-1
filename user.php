<?php require 'includes/header.php'; ?>

<?php
////////////////////////////////////////////////////////////////////////////////
//
//  Initially, I was just using:
//
//        if (empty($_SESSION)) { ... }
//
//
//  However, this is problematic in that there could be other sessions not specifically
//  related to a user being logged in.
//  For that reason, it's better to be precise:
//
//        if ( empty($_SESSION['user_id']) ) { ... }
//
//
//////////////////////////////////////////////////////////////////////////////
?>


<!--
'includes/header.php' has session_start() in it.
This can potentially create conflicts when using header("Location: ...");
We don't need session_start() here.
It may not create an error on the local server, but it probably will on a live server.

However, I want to redirect if empty($_SESSION['user_id']).
This means I NEED to session_start().
But that I don't want to use header().
Thus, I am using Javascript to redirect instead:


      window.location.href = "index.php?error=user_permission_error";


It seems to work fine.
That said, I've seen this approach referred to as 'unreliable':

      https://stackoverflow.com/questions/27123470/redirect-in-php-without-use-of-header-method


Also, this means that if javascript is disabled that it won't work.
In that case, we have a backup plan:

      <meta http-equiv="refresh" content="0; url=index.php?error=user_permission_error"/>


This approach also comes from the above stackoverflow.com article.
If the meta tag gets invoked, it will redirect to index.php, but
the toast indication will never get executed because obviously there's no javascript.
-->


<?php if ( empty($_SESSION['user_id']) ): ?>
  <script>
    //Change this to use the 'message' session variable.
    window.location.href = "403.php?error=user_permission_error";
    //window.location.replace(URL);
  </script>

  <meta http-equiv="refresh" content="0; url=403.php?error=user_permission_error"/>
<?php endif; ?>

<?php
if ( empty($_SESSION['user_id']) ){
  exit();
}
?>


  <main>

    <h1 style="text-align:center; color: violet; text-shadow:-2px 2px 4px rgba(0,0,0,0.15);">
      <?php echo "Acount of: " . get_session_variable('user_username'); ?>
    </h1>

    <?php
    //////////////////////////////////////////////////////////////////////////
    //
    //  When Javascript is disabled:
    //
    //
    //  Originally, I was redirecting to here with user.php?status=logged_in.
    //  Then using:
    //
    //        if ($_GET['status'] == 'logged_in'){
    //          return "<span style='color:rgb(0,200,0);'>" .
    //                   "<strong>Success!!!</strong><br>You are now logged in!<br><br><span style='font-size: 400%;'>🎟</span>" .
    //                "</span>";
    //        }
    //
    //  to render the message (abstracted into get_status() in helpers.php)
    //
    //  This works pretty good.
    //  However, I chose instead to use a session variable.
    //  Once redirected here, PHP checks for that session variable, renders the
    //  message, unsets the session variable, and then refreshes the page a few
    //  seconds later.
    //
    //  The benefit of this approach is that if the user clicks another page, then
    //  clicks back, they will not see the login message again.
    //
    //
    ////////////////////////
    //
    //
    //  Note: there is a slightly different approach taken at the bottom of this page
    //  for when Javascript is enabled. In that case, a different session variable is created
    //  called 'logged_in_with_fetch', and the toast notification feature is implemented.
    //
    //  Again, the session variable is unset, so that if a user clicks back to this page,
    //  they will not get the message twice (as they would when passing the variable in the
    //  url).
    //
    //  Yes, it's a little bit more complicated, to do it this way, but it makes sense.
    //
    //
    //////////////////////////////////////////////////////////////////////////



    if ( isset( $_SESSION['logged_in_with_form']) ){
      echo "<div id='message-div'>" . $_SESSION['logged_in_with_form'] . "</div>";
      unset($_SESSION['logged_in_with_form']);

      //Since Javascript will be disabled, the only way to make the message go away
      //is to refresh the page after the session variable has been unset.
      //Note: the url attribute might not be necessary since we are refreshing SELF.
      echo "<meta http-equiv='refresh' content='3; url=user.php'/>";
    }
    ?>
  </main>


  <!-- Note: I have NOT included </body> and </html> in footer.php
  This way, I can include <script>s below as needed, and not worry about
  the scripts loading before the html has.
  Such cases could potentially create errors. -->
  <?php require 'includes/footer.php'; ?>


  <script src="scripts/js/helpers.js"></script>

  <!-- There's no need to include login.js within user.php. Why?
  Because the user will be redirected to register.php if they are not currently logged in
  (i.e., no session). However, if we disabled that redirect and the user manually navigated
  to user.php, then the login form would submit directly to login-process.php.
  The response message sent from login-process.php would then render directly to the browser,
  rather than being sent back to user.php.
  In other words, the app would break...

  Again, none of this is necessarily an issue because the user won't actually be able
  to access user.php unless they are logged in.
  Nonetheless, it's important to be aware of the consequences of not including login.js
  <script src="scripst/js/login-with-fetch.js"></script> -->

  <script src="scripts/js/logout-with-fetch.js"></script>
  <script src="scripts/js/delete-with-fetch.js"></script>


  <?php
  //////////////////////////////////////////////////////////////////////////////
  //
  //  Admittedly, this looks kind of confusing.
  //  Basically, there's a session variable called logged_in_with_fetch that is
  //  set when a user gets logged in, and the login POST request came from fetch.
  //  This session variable contains the success message to be rendered here.
  //
  //  Conversely, if a use logins in with the form, a session variable is set called
  //  'logged_in_with_form'. That success message will be rendered here also.
  //  Either one or the other will be rendered, but not both.
  //
  //
  //
  //////////////////////////////////////////////////////////////////////////////

  //It works better with ! first.
  if (! empty($_SESSION['logged_in_with_fetch']) ){
    $logged_in_with_fetch_message = $_SESSION['logged_in_with_fetch'];
  } else {
    $logged_in_with_fetch_message = '';
  }
  ?>


  <script>
  /* ===========================================================================

  =========================================================================== */


    const logged_in_with_fetch_message = "<?php echo $logged_in_with_fetch_message; ?>";

    if (logged_in_with_fetch_message){
      render_toast_div(logged_in_with_fetch_message);
    }
  </script>


  <?php
  $logged_in_with_fetch_message = ''; //I'm not sure if I even need to do this.
  unset($_SESSION['logged_in_with_fetch']);
  ?>
</body>
</html>
