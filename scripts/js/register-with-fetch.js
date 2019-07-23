/* ===========================================================================
                          Show Password Feature
=========================================================================== */


const show_password_checkbox         = document.getElementById("show-password-checkbox");
const show_confirm_password_checkbox = document.getElementById("show-confirm-password-checkbox");


/* ================================
    toggle_password_visibility
================================ */


const toggle_password_visibility = (checkbox, password_input) => {
  try {
    (checkbox.checked) ? password_input.type = 'text' : password_input.type = 'password';
  } catch(error) {
    alert.log('This browser cannot switch type');
  }
}


/* ================================
        Event Listeners
================================ */


show_password_checkbox.addEventListener('change', function(e) {
  const registration_password_input = document.getElementById("registration-password-input");
  //Or you could pass it target: var target = e.target || e.srcElement;
  toggle_password_visibility(this, registration_password_input);
});


show_confirm_password_checkbox.addEventListener('change', function(e) {
  const registration_password_confirmation_input = document.getElementById("registration-password-confirmation-input");
  //Or you could pass it target: var target = e.target || e.srcElement;
  toggle_password_visibility(this, registration_password_confirmation_input);
});


/* =============================================================================
                Registration Form Submit Event Handler
============================================================================= */


const registration_form = document.getElementById("registration-form");


registration_form.addEventListener('submit', (e) => {
  e.preventDefault();


  const elements            = e.target.elements;
  const first               = elements.first.value.trim();
  const last                = elements.last.value.trim();
  const email               = elements.email.value.trim();
  const username            = elements.username.value.trim();
  const password            = elements.password.value.trim();
  const confirm_password    = elements.confirm_password.value.trim();
  const registration_submit = elements.registration_submit.value.trim();
  let   registration_form_errors = [];


  /* ===========================================================================
                                  Validation
  =========================================================================== */


  /* ================================
          Validate $first
  ================================ */


  if ( first === '' ){
    registration_form_errors.push("Please fill out the first name field. (JS)");
  } else if (! (/^[a-z]+$/i.test(first)) ) {
    //The test() method tests for a match in a string.
    //This method returns true if it finds a match, otherwise it returns false.
    registration_form_errors.push("Please use only letters in first name. (JS)");
  }


  /* ================================
          Validate $last
  ================================ */


  if ( last === '' ){
    registration_form_errors.push("Please fill out the last name field. (JS)")
  } else if (! (/^[a-z]+$/i.test(last)) ) {
    registration_form_errors.push("Please use only letters in last name. (JS)");
  }


  /* ================================
          Validate $email
  ================================ */

  //Note: if you want to test the Javascript or PHP email validation,
  //make sure to temporarily change it from type="email" to type="text" i
  //in the form.


  if (email === '') {
    registration_form_errors.push("Please fill out the email field. (JS)");
  } else if ( !(/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) ) {
    //https://tylermcginnis.com/validate-email-address-javascript/
    registration_form_errors.push("Please use a valid email. (JS)");
  }


  /* ================================
          Validate $username
  ================================ */


  if ( username === ''){
    registration_form_errors.push("Please fill out the username field. (JS)");
  }


  /* ================================
          Validate $password
  ================================ */


  if ( password === '' ){
    registration_form_errors.push("Please fill out the password field. (JS)");
  } else if (password.length < 6) {
    registration_form_errors.push("Please enter a password that is at least six characters. (JS)");
  } else if ( password !== confirm_password ) {
    registration_form_errors.push("The passwords must match. (JS)");
  }


  /* ================================
    Check if $formErrors is empty
  ================================ */


  //If the $formErrors array is NOT empty then do this.
  if ( registration_form_errors.length > 0 ) {
    //Output errors to <div id="form-errors-div">
    const form_errors_div = document.getElementById("form-errors-div");
    let items             = ``;

    registration_form_errors.forEach((form_error) => {
      items += `<li>${form_error}</code></li>`;
    });

    form_errors_div.innerHTML = "<ul id='form-errors-ul'><h3>Form Errors !!!</h3>" + items + "</ul>";


    const message = "<span style='color:rgb(200,0,0);'>Hey! There are errors!</span>";
    render_toast_div(message);

    elements.password.value         = '';
    elements.confirm_password.value = '';
    return; //return early
  } else {
    const form_errors_div = document.getElementById("form-errors-div");
    form_errors_div.innerHTML = "";
  }


  /* =========================================================================
                      Make fetch POST request to Server
  ========================================================================= */
  //If the registration_form_errors array IS empty then send data to server.


  //////////////////////////////////////////////////////////////////////////////
  //
  //  Construct a parameter string.
  //  If you mess any of this up, the error will be an unhandled promise rejection.
  //  Because the PHP breaks without the appropriate parameters.
  //  Unfortunately, the PHP doesn't really tell you this, it just fails to send back the
  //  appropriate response. Then if you disable this file and send the values directly through
  //  the form it appears to work fine. So that can be confusing.
  //
  //////////////////////////////////////////////////////////////////////////////

  const parameter_string = 'registration_submit=' + registration_submit +
                           '&first='              + first    +
                           '&last='               + last     +
                           '&email='              + email    +
                           '&username='           + username +
                           '&password='           + password +
                           '&confirm_password='   + confirm_password;


  fetch('scripts/php/register-with-fetch.php', {
    method:  'POST',
    body:    parameter_string,
    headers: { "Content-Type": "application/x-www-form-urlencoded" }
  })
  //If you get:
  //Unhandled Promise Rejection: SyntaxError: The string did not match the expected pattern.
  //Then try changing it to res.text() temporarily to see what the problem is.
  .then( (res) => res.json() )
  .then( (data) => {
    const message         = data.message;
    const status          = data.status;
    const form_errors     = data.form_errors;
    const form_errors_div = document.getElementById("form-errors-div");

    render_toast_div(message);


    //Output errors to <div id="form-errors-div">
    if (form_errors.length > 0){
      let items = ``;

      form_errors.forEach((form_error) => {
        items += `<li>${form_error}</li>`;
      });

      form_errors_div.innerHTML = "<ul id='form-errors-ul'><h3>Form Errors !!!</h3>" + items + "</ul>";

      elements.password.value         = '';
      elements.confirm_password.value = '';
    } else if (status === 'success') {
      form_errors_div.innerHTML = "";
      registration_form.reset();
    } else {
      elements.password.value         = '';
      elements.confirm_password.value = '';
    }
  });
});
