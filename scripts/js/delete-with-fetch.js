/* =============================================================================
                                delete_account()
============================================================================= */


const delete_form = document.getElementById("delete-form");


////////////////////////////////////////////////////////////////////////////////
//
//  With the login form, we needed to conditionally apply it's event listener.
//  based on whether or not the login form was being rendered.
//  However, in this case, there's no need to conditionally apply this event listener because
//  it's specifically related to user.php, which will always render the the delete button.
//
////////////////////////////////////////////////////////////////////////////////

delete_form.addEventListener('submit', (e) => {
  e.preventDefault();

  const confirmed = confirm("Are you sure you want to delete your account?");

  if (confirmed) {
    const elements           = e.target.elements;
    const user_id            = elements.user_id.value.trim();
    const delete_user_submit = elements.delete_user_submit.value.trim();
    const parameter_string   =  'user_id=' + user_id + '&delete_user_submit=' + delete_user_submit;


    fetch('scripts/php/delete-with-fetch.php', {
      method:  'POST',
      body:    parameter_string,
      headers: { "Content-Type": "application/x-www-form-urlencoded" }
    })
    .then((res) => res.json() )
    .then((data) => {
      const message = data.message;
      const status  = data.status;


      if (status === "deleted_with_fetch"){
        ////////////////////////////////////////////////////////////////////////
        //
        //  Immediately redirect to index.php
        //  Note delete-with-fetch.js behaves like it is in the same file where it is referenced.
        //  Thus when we redirect to index.php, we DO NOT need to use ../../
        //
        //  Should I do this the other way (Google vs)
        //  window.location.href = "index.php?deleted_with_fetch=true";
        //
        ////////////////////////////////////////////////////////////////////////
        window.location.replace("index.php?deleted_with_fetch=true");
      } else {
        render_toast_div(message);
      }
    });
    //add a catch()
  } else {
    render_toast_div("Your account has NOT been deleted.<br><br><span style='font-size: 400%;'>🤩</span>");
  }
});



//
