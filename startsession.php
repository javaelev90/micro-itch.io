<?php    
    // Cookie life time is set to a week
    session_set_cookie_params([
        "lifetime" => 60 * 60 * 24 * 7,
        "secure"=> true
    ]);
    session_start();
?>