/* =============================================================================
                    Conditionally apply the Event Listener
============================================================================= */


const login_form = document.getElementById("login-form");

////////////////////////////////////////////////////////////////////////////////
//
//  The login form is being conditionally rendered by the PHP.
//  Thus when it is not rendered we would get:
//
//    TypeError: null is not an object (evaluating 'login_form.addEventListener')
//
//
//  This tells us that login_form evaluates to null when there is no login form.
//  Since null is falsy, we can use this here to conditionally create the
//  event listener only when the login form exists.
//
////////////////////////////////////////////////////////////////////////////////


if (login_form){
  login_form.addEventListener('submit', (e) => {
    e.preventDefault();

    const elements     = e.target.elements;
    const username     = elements.username.value.trim(); //Remember: this might also be an email.
    const password     = elements.password.value.trim();
    const login_submit = elements.login_submit.value.trim();


    //It would be better to use a toast for this.
    //Or look into styling alert messages.
    if ( username === '' || password === '' ){
      //alert("Looks like you need to fill in one of the fields.");
      render_toast_div("<span style='color:rgb(200,0,0);'>Looks like you forgot to fill in one of the fields.</span><br><br><span style='font-size: 400%;'>🤪</span>");
      return;
    } else if (password.length < 6) {
      render_toast_div("<span style='color:rgb(200,0,0);'>Looks like your password is not at least six characters.</span><br><br><span style='font-size: 400%;'>😫</span>");
      //alert("Looks like your password is not at least six characters.");
      return;
    }


    //Construct a parameter string.
    const parameter_string = 'login_submit=' + login_submit + '&username=' + username + '&password=' + password;


    fetch('scripts/php/login-with-fetch.php', {
      method:  'POST',
      body:    parameter_string,
      headers: { "Content-Type": "application/x-www-form-urlencoded" }
    })
    .then((res) => res.json())
    .then((data) => {
      const message = data.message;
      const status  = data.status;


      if (status === "logged_in"){
        //Immediately redirect to user.php
        //This file behaves like it is in the same file where it is referenced.
        //Thus when we redirect to user.php, we DO NOT need to use ../../

        //window.location.replace("user.php?status=logged_in");
        window.location.replace("user.php");
      } else {
        //If status is not success, then then 'toast' the message.
        render_toast_div(message);
      }
    });
  });

}//End of if (login_form) { ... }
