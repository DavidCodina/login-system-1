<?php
/* =============================================================================

============================================================================= */


function render_message_div(){
  if ( isset( $_SESSION['message']) ){
    $message = $_SESSION['message'];
    echo "<div id='message-div'>" . $message . "</div>";

    unset($_SESSION['message']);
    echo "<meta http-equiv='refresh' content='3; url=" . $_SERVER['PHP_SELF'] ."'/>";

    //I am not using $_SESSION['message'] to convey transfer a logged out message
    //because logging out kills the session. Consequently, I am using the query string here.
  } elseif ( isset($_GET['logged_out_with_form']) ){
    if ($_GET['logged_out_with_form'] == 'true'){
      $message =  "<span style='color:rgb(0,200,0);'>" .
                    "<strong>Success!</strong><br>You are now logged out!<br><br><span style='font-size: 400%;'>👍🏼</span>" .
                  "</span>";

      echo "<div id='message-div'>" . $message . "</div>";

      //////////////////////////////////////////////////////////////////////////
      //
      //  $_SERVER['PHP_SELF'] seems smart enough to not rerender parameter:
      //
      //        ?logged_out_with_form=true
      //
      //////////////////////////////////////////////////////////////////////////

      echo "<meta http-equiv='refresh' content='3; url=" . $_SERVER['PHP_SELF'] ."'/>";
    }
  }
}


/* =============================================================================

============================================================================= */


function get_session_variable($var){
  if ( !empty($_SESSION) ){ //This check is probably unnecessary.
    if( isset($_SESSION[$var]) ){
      return $_SESSION[$var];
    } else {
      //Or just return '';
      return 'That session variable does not exist.';
    }
  }
}
?>
