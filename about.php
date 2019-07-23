<?php require 'includes/header.php'; ?>

  <main>

    <h1 style="text-align:center; color: violet; text-shadow:-2px 2px 4px rgba(0,0,0,0.15);">About</h1>

    <p style="width: 75%; margin: 50px auto 25px auto;">This project is a login system built with PHP,
    and procedural MYSQLI. When Javascript is enabled, it uses a custom toast notification feature
    that renders registration, login, and deletion related messages.
    Additionally, it will render connection errors, and SQL syntax errors.
    In a real-world application, we would not render these errors to the user, but I found them
    useful for this exercise.</p>


    <p style="width: 75%; margin: 50px auto 25px auto;"><code>403</code> Forbidden errors are redirected to <code>403.php</code>.
    Most likely, the user would never see this page because we would set restrictions and redirects
    In a real-world application, we wouldn't be using these types of messages at all.
    on the <code>.htaccess</code> file. That said, it's just an added bit of security.</p>


    <p style="width: 75%; margin:25px auto 50px auto;">To test the <code>403</code> errors use the following URLs:</p>

    <ul class="url-list">
      <li><a class="list-link" href="user.php">user.php</a></li>
      <li><a class="list-link" href="scripts/php/login-with-form.php">scripts/php/login-with-form.php</a></li>
      <li><a class="list-link" href="scripts/php/login-with-fetch.php">scripts/php/login-with-fetch.php</a></li>
      <li><a class="list-link" href="scripts/php/logout-with-form.php">scripts/php/logout-with-form.php</a></li>
      <li><a class="list-link" href="scripts/php/logout-with-fetch.php">scripts/php/logout-with-fetch.php</a></li>
      <li><a class="list-link" href="scripts/php/register-with-form.php">scripts/php/register-with-form.php</a></li>
      <li><a class="list-link" href="scripts/php/register-with-fetch.php">scripts/php/register-with-fetch.php</a></li>
      <li><a class="list-link" href="scripts/php/delete-with-form.php">scripts/php/delete-with-form.php</a></li>
      <li><a class="list-link" href="scripts/php/delete-with-fetch.php">scripts/php/delete-with-fetch.php</a></li>
    </ul>
  </main>


  <!-- Note: I have NOT included </body> and </html> in footer.php
  This way, I can include <script>s below as needed, and not worry about
  the scripts loading before the html has.
  Such cases could potentially create errors. -->
  <?php require 'includes/footer.php'; ?>


  <script src="scripts/js/helpers.js"></script>
  <script src="scripts/js/login-with-fetch.js"></script>
  <script src="scripts/js/logout-with-fetch.js"></script>
</body>
</html>
