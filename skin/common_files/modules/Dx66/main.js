/**
 * Javascript functions added by Dx66 company.
 * Currently only handles cart timeout functions.
 * 
 * @author Adam Maschek <adam.maschek@gmail.com>
 */


/**
 * EVENT HOOKS
 */
$(document).ready(function() {
    
    //hook ajax success handler on product detail and list views
    $('div.product-details,div.products-list').bind("ajaxSuccess", function(a, resp, ajax) {
        
        //only do it if the ajax called url was that of the cart.php
        if (ajax.url.match(/cart\.php$/)) {
		
            //we suppose the result was a success
            //we could parse out the json object, but somewhy its embedded in HTML:(
			
            //wipe out cookie value and check the new expiry
            $.cookie('dx66_cart_expiry', null);
            //clear existing timer
            clearInterval(dx66_timer);
            dx66_check_cart_expiry();
        }
    });
 	
    //do the checking on the non-empty cart view and on the home view
    if (
        $('form[name="cartform"]').length > 0 ||
        location.href.match(/home\.php$/)
        ) {
        dx66_check_cart_expiry();
    }
});


/**
 * GLOBALS
 * 
 */

/**
 * @var dx66_timer Holds the reference to the single timer object to count down.
 */
var dx66_timer;


/**
 * GLOBAL FUNCTIONS
 */

/**
 * Displays the countdown popup, and initiates the timer to count down.
 * 
 * @param min The minute needed to be displayed
 * @param sec The second needed to be displayed
 * 
 * @todo minute and second values are not zero prefixed
 * @todo language is fixed to english
 */
function dx66_countdown(min, sec) {
    
    if ($('#dx66_countdown').length == 0) {
        $('body').append("<div id='dx66_countdown'></div>");
    }
    $("#dx66_countdown").attr('title', 'Hurry up!').html("You have <div id='counter'>" + min + ":" + sec + "</div> minutes to order the items in your cart!").dialog();

    dx66_timer = setInterval( function() {
        var parts = $('#counter').text().split(':');
        var min = parseInt(parts[0], 10);
        var sec = parseInt(parts[1], 10);
        if (--sec < 0) {
            sec = 59;
            min--;
        }
        $('#counter').text(min + ':' + sec);
        if (min < 0 || min == 0 && sec == 0) {
            //timeup: stop counter and call ajax to wipe cart
            clearInterval(dx66_timer);
            dx66_check_cart_expiry();
        }
    }, 1000);

}

/**
 * Called when the ajax call returns -1 and it means that the
 * cart was recently wiped.
 * We show a warning to the user.
 */
function dx66_countdown_timeup() {
    
    //reset the cookie to the value which means the cart is empty
    $.cookie('dx66_cart_expiry', 0);
    
    if ($('#dx66_countdown').length == 0) {
        $('body').append("<div id='dx66_countdown'></div>");
    }
    $("#dx66_countdown").attr('title', "Sorry!").html("We have emptied your cart!").dialog();
    
}

/**
 * Fethes the value of the dx66_cart_expiry setting via ajax and places the value
 * in a cookie.
 * After successful return, it evaluates the returned value via the
 * dx66_cart_expiry function.
 * 
 * @todo - what if the ajax call fails?
 */
function dx66_ajax_get_cart_expiry() {
    $.ajax({
        url: "dx66.php",
        data: {
            mode: 'get_cart_expiry'
        },
        success: function(resp){
            $.cookie('dx66_cart_expiry', resp);
            dx66_check_cart_expiry();
        }
    });
}


/**
 * Checks the value of the dx66_cart_expiry cookie.
 * If the cookie doesnt exist, it fetches via ajax and runs this function again.
 * If the value is a positive value, it checks if the expiry time is reached.
 * If not yet, it displays the counter.
 * If the expiry reached, it fetches again the value via ajax, because maybe new product was placed in the cart.
 * If the value is negative(-1),it means that the cart was wiped recently,
 * so we need to display the warning.
 * 
 * @todo - make sure we never get into an infinite ajax loop
 */
function dx66_check_cart_expiry() {
    //console.log('dx66_cart_expiry: ' + $.cookie('dx66_cart_expiry'));
    
    if ($.cookie('dx66_cart_expiry') == null) {
        //cookie not set, fetch it via ajax
        dx66_ajax_get_cart_expiry();
        return;
    }
    
    if ($.cookie('dx66_cart_expiry') > 0) {
        //calculate cookie age
        var timestamp = $.cookie('dx66_cart_expiry');
        //note: JS uses milliseconds whereas PHP uses seconds
        //hence the division by 1000
        var timediff  = timestamp - Math.floor(new Date().getTime() / 1000);
        if (timediff > 0) {
            var min = Math.floor(timediff / 60);
            var sec = Math.floor(timediff % 60);
            dx66_countdown(min, sec);
            return;
        }
        else {
            //according to the cookie the cart is expired, but maybe 
            //new product was put into the cart in the meantime, so fetch it via 
            //ajax again
            dx66_ajax_get_cart_expiry();
            return;
        }
    }
    
    if ($.cookie('dx66_cart_expiry') < 0) {
        //the last ajax operation wiped out the cart, this means we have to show timeup
        dx66_countdown_timeup();
        return;
    }
    
    //explicit else cookie value is 0
    //dont do anything, nothing is in the cart
}
