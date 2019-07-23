/* =============================================================================
                                render_toast_div
============================================================================= */


//https://www.kirupa.com/html5/emoji.htm
const render_toast_div = (message = 'Somebody forgot to add the message... ☹️') => {
  let toast_div        = document.getElementById("toast-div");
  toast_div.innerHTML  = message;

  toast_div.classList.add("show");
  //////////////////////////////////////////////////////////////////////////////
  //
  //
  //  Note: this was originally set to 3 seconds:
  //
  //      setTimeout(() => { toast_div.classList.remove("show"); }, 3000);
  //
  //
  //  Which corresponded to the animation being set as:
  //
  //      -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
  //      animation:         fadein 0.5s, fadeout 0.5s 2.5s;
  //
  //
  //  However, because I added two seconds, I also had to change the
  //  CSS animating timing:
  //
  //      -webkit-animation: fadein 0.5s, fadeout 0.5s 4.5s;
  //      animation:         fadein 0.5s, fadeout 0.5s 4.5s;
  //
  //
  //
  //  Update: I changed it back to 3 seconds.
  //
  //////////////////////////////////////////////////////////////////////////////

  setTimeout(() => { toast_div.classList.remove("show"); }, 3000);
}
