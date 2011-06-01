<?php

/**
 * Entry point for Dx66 Ajax functions.
 * $mode controls the actual task to carry out
 * (it is coming from somewhere the xcart universe, just like $cart)
 * 
 * @author Adam Maschek <adam.maschek@gmail.com>
 */

require './auth.php';


//x_load(
//    'cart'
//    //'product'
//);

//x_session_register('cart');

//var_dump($cart['dx66_cart_expiry']);
//var_dump($mode);




if ($mode == 'get_cart_expiry') {
    
    $expiry = $cart['dx66_cart_expiry'];
    
    if (isset($expiry) && $expiry > 0 && $expiry - time() < 0) {
        //cart expired, empty cart and return -1
        require_once $xcart_dir . '/modules/Dx66/func.php';
        dx66_empty_cart($XCARTSESSID);
        echo "-1";
        die();
    }
    //otherwise just return the value stored in the session
    echo (int) $cart['dx66_cart_expiry'];
    die();
}

