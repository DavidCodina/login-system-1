<?php require 'includes/header.php'; ?>



  <main>
    <h1 style="text-align:center; color: violet; text-shadow: -2px 2px 4px rgba(0,0,0,0.15);">Sign up for Tek Solutions!</h1>

    <div id="form-errors-div">
      <?php
      if ( isset( $_SESSION['form_errors']) ){
        echo "<ul id='form-errors-ul'><h3>Form Errors !!!</h3>";
        foreach ($_SESSION['form_errors'] as $formError) {
          echo "<li>$formError</li>";
        }
        echo "</ul>";
      }
      ?>
    </div>

    <?php
      if ( isset( $_SESSION['status']) ) {
        if ($_SESSION['status'] === 'success'){
          unset($_SESSION['form_data']);
        }
      }
    ?>


    <!-- Technically, we don't need action="register-process.php" method="POST"
    Because we are interrupting the submission with Javascript.
    That said, using an AJAX call makes testing register-process.php difficult.
    It becomes like a black box.
    Thus I have left these attributes in, so that I can direct to register-process.php
    and see any possible errors with the code (while testing).
    -->
    <form id="registration-form" action="scripts/php/register-with-form.php" method="POST">
      <h3>REGISTER</h3>

      <div class="form-group">
        <label>First Name:</label>
        <input
          type="text"
          name="first"
          placeholder="First Name.."
          value='<?php echo ( isset($_SESSION['form_data'])) ? $_SESSION['form_data']['first'] : ''; ?>'
        />
      </div>

      <div class="form-group">
        <label>Last Name:</label>
        <input
          type="text"
          name="last"
          placeholder="Last Name.."
          value='<?php echo ( isset($_SESSION['form_data'])) ? $_SESSION['form_data']['last'] : ''; ?>'
        />
      </div>

      <div class="form-group">
        <label>Email:</label>
        <input
          type="text"
          name="email"
          placeholder="Email.."
          value='<?php echo ( isset($_SESSION['form_data'])) ? $_SESSION['form_data']['email'] : ''; ?>'
        />
      </div>

      <div class="form-group">
        <label>Username:</label>
        <input
          type="text"
          name="username"
          placeholder="User Name.."
          value='<?php echo ( isset($_SESSION['form_data'])) ? $_SESSION['form_data']['username'] : ''; ?>'
        />
      </div>

      <div class="form-group">
        <label>Password:</label>
        <input id="registration-password-input" type="password" name="password" placeholder="Password..">

        <input  id="show-password-checkbox" type="checkbox" style="width: auto;">
        <label for="show-password-checkbox" style="width: auto; font-weight: normal; font-size: 12px;">Show Password</label>
      </div>

      <div class="form-group">
        <label>Confirm Password:</label>
        <input id="registration-password-confirmation-input" type="password" name="confirm_password" placeholder="Password..">

        <input  id="show-confirm-password-checkbox" type="checkbox" style="width: auto;">
        <label for="show-confirm-password-checkbox" style="width: auto; font-weight: normal; font-size: 12px;">Show Confirm Password</label>
      </div>

      <div class="submit-group">
        <input class="submit" type="submit" name="registration_submit" value="Submit">
      </div>
    </form>


    <?php
    ////////////////////////////////////////////////////////////////////////////
    //
    //  When $_SESSION['form_data'] is created, $_SESSION['registration_data_creation_time'].
    //  is also created. Then below we check to see if form_data was created longer than
    //  0 seconds ago. If it was, then we unset form_data.
    //
    //  Note: the first time we go to register.php, form_data will not have been created.
    //  When we submit the form, form_data and registration_data_creation_time are created if there is an error.
    //  We then redirct back here.
    //  Now that registration_data_creation_time exists it checks to see if $difference > 2 (seconds)
    //  If it is, then we'll unset form_data
    //  It is not, so form_data is not unset.
    //
    //  However, in cases, where the registration is submitted directly by form,
    //  the register.php page will refresh after 3 seconds.
    //  In that case, the difference is greater than 0.
    //
    //  But... the script happens BELOW the registration form.
    //  This means that the form will still repopulate, THEN erase the data.
    //
    //  This implementation allows for the form to repopulate under different circumstances.
    //  However, it also is safe in that the data will not persist indefinitely.
    //
    //  Note: this does not prevent instances whereby a user can click back to the form data.
    //  That said, the passwords are erased in all circumstances.
    //
    ////////////////////////////////////////////////////////////////////////////


    function unset_form_data(){
      //Unset form data after a couple of seconds.
      if (isset($_SESSION['registration_data_creation_time'])){
        //echo "form_data_creation now exists.<br>";
        //If          now    -  then
        $difference = time() - $_SESSION['registration_data_creation_time'];

        if ( $difference > 2 ) {
          if (isset($_SESSION['form_data'])){
            unset($_SESSION['form_data']);
          }
          //echo '$difference is greater than 2 seconds, so $_SESSION["form_data"] has been unset.';
        }
      }
    }


    function unset_form_errors(){
      //Unset error data after a couple of seconds.
      if (isset($_SESSION['registration_data_creation_time'])){
        //echo "form_data_creation now exists.<br>";
        //If          now    -  then
        $difference = time() - $_SESSION['registration_data_creation_time'];

        if ( $difference > 2 ) {
          if (isset($_SESSION['form_errors'])){
            unset($_SESSION['form_errors']);
          }
          //echo '$difference is greater than 2 seconds, so $_SESSION["form_errors"] has been unset.';
        }
      }
    }

    unset_form_data();
    unset_form_errors();
    ?>
  </main>


  <!-- Note: I have NOT included </body> and </html> in footer.php
  This way, I can include <script>s below as needed, and not worry about
  the scripts loading before the html has.
  Such cases could potentially create errors. -->
  <?php require 'includes/footer.php'; ?>


  <script src="scripts/js/helpers.js"></script>
  <script src="scripts/js/login-with-fetch.js"></script>
  <script src="scripts/js/logout-with-fetch.js"></script>
  <<script src="scripts/js/register-with-fetch.js"></script>
</body>
</html>
