
<?php
//Note: I have NOT included </body> and </html> in footer.php
//This way, I can include <script>s below the footer include, and not worry about
//the scripts loading before the html has.
//Such cases could potentially create errors.
?>

<footer>
  <div class="footer-icon-container">
    <a href="https://www.youtube.com"><img class="footer-icon"  src="images/youtube.svg"  alt="youtube icon"> </a>
    <a href="https://twitter.com"><img class="footer-icon"      src="images/twitter.svg"  alt="twitter icon"></a>
    <a href="https://github.com"><img class="footer-icon"       src="images/github.svg"   alt="github icon"></a>
    <a href="https://www.facebook.com"><img class="footer-icon" src="images/facebook.svg" alt="facebook icon"></a>
    <a href="https://codepen.io"><img class="footer-icon"       src="images/codepen.svg"  alt="codepen icon"></a>
    <a href="https://www.linkedin.com"><img class="footer-icon" src="images/linkedin.svg" alt="linkedin icon"></a>
  </div>


  <?php
  /* ===========================================================================
                         Conditionally render the delete button:
  =========================================================================== */


  $file_name = basename($_SERVER["SCRIPT_FILENAME"]);

  if ($file_name == 'user.php') {
    $delete_form = "<form id='delete-form' action='scripts/php/delete-with-form.php' method='POST'>" .
                     "<input type='hidden' name='user_id' value='" . $_SESSION['user_id'] . "'>" .
                     "<input class='submit' type='submit' name='delete_user_submit' value='DELETE Account'>" .
                   "</form>";
    echo $delete_form;
  }
  ?>
</footer>
