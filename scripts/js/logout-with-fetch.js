/* =============================================================================
                    Conditionally apply the Event Listener
============================================================================= */


const logout_form = document.getElementById("logout-form");

////////////////////////////////////////////////////////////////////////////////
//
//  The logout form is being conditionally rendered by the PHP.
//  Thus when it is not rendered we would get:
//
//    TypeError: null is not an object (evaluating 'logout_form.addEventListener')
//
//
//  This tells us that logout_form evaluates to null when there is no logout form.
//  Since null is falsy, we can use this here to conditionally create the
//  event listener only when the logout form exists.
//
////////////////////////////////////////////////////////////////////////////////


if (logout_form){
  logout_form.addEventListener('submit', (e) => {
    e.preventDefault();

    const elements      = e.target.elements;
    const logout_submit = elements.logout_submit.value.trim();


    //Construct a parameter string.
    const parameter_string = 'logout_submit=' + logout_submit;


    fetch('scripts/php/logout-with-fetch.php', {
      method:  'POST',
      body:    parameter_string,
      headers: { "Content-Type": "application/x-www-form-urlencoded" }
    })
    .then((res) => res.json())
    .then((data) => {
      const message = data.message;
      const status  = data.status;


      if (status === "logged_out_with_fetch"){
        ////////////////////////////////////////////////////////////////////////
        //
        //  Immediately redirect to index.php
        //  Note lougout-with-fetch.js behaves like it is in the same file where it is referenced.
        //  Thus when we redirect to index.php, we DO NOT need to use ../../
        //
        //  Should I do this the other way (Google vs)
        //  window.location.href = "index.php?logged_out_with_fetch=true";
        //
        ////////////////////////////////////////////////////////////////////////

        window.location.replace("index.php?logged_out_with_fetch=true");
      } else {
        ////////////////////////////////////////////////////////////////////////
        //
        //  If status is not "logged_out_with_fetch", then then 'toast' the message.
        //  Currently, there are no other "message"/"status" combinations that
        //  would potentially be sent from logout-with-fetch.php, so this will never
        //  actually get invoked. But, as is noted in logout-with-fetch.php, this
        //  feature is still useful to have here.
        //
        ////////////////////////////////////////////////////////////////////////

        render_toast_div(message);
      }
    });
  });
}//End of if (logout_form) { ... }
