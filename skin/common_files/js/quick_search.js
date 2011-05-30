/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Quick search
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: quick_search.js,v 1.3.2.1 2010/08/11 10:53:27 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/**
 * Registering keys to show/hide Quick Search form
 */
var clear_result = false;
var ajax_quick_search_script = current_location + "/quick_search.php";
var home_script = current_location + "/home.php";

/**
 * Closes quick search form
 * 
 * @return void
 * @see    ____func_see____
 * @since  1.0.0
 */
function close_quick_search() {
  $("#quick_search_body1, #quick_search_results").show();
  $("#quick_search_panel, #quick_search_body2, #quick_search_no_results, #quick_search_no_pattern").hide();
  clear_result = true;
}

/**
 * Proceed query for quick search form (Makes AJAX request)
 * 
 * @param string query Query string
 *  
 * @return mixed
 * @see    ____func_see____
 * @since  1.0.0
 */
function quick_search(query) {
  query = query.replace(/^\s+/g, "").replace(/\s+$/g, "");
  if (query == "") {
    $("#quick_search_results, #quick_search_no_results").hide();
    $("#quick_search_no_pattern").show();

    return false;
  }

  $("#quick_search_no_pattern, #quick_search_no_results").hide();
  $("#quick_search_body1").hide();
  $("#quick_search_body2").show();

  $("#quick_search_panel").show();

  clear_result = false;

  $.ajax(
    {
      url: ajax_quick_search_script,
      type: 'GET',
      data: { 
        "mode": "ajax_search",
        "query": query
      },
      dataType: 'json',
      success: function(data, textStatus) {
        if (clear_result || textStatus != "success")
          return false;

        if (data["result"] == "Y") {

          document.location.href = (data["mode"] == "single") ? data["url"] : ajax_quick_search_script + "?mode=search";

        } else if (data["result"] == "not_logged_in") {

          document.location.href = home_script;

        } else {

          $("#quick_search_results, #quick_search_body2").hide();
          $("#quick_search_no_results, #quick_search_body1").show();
          $("#quick_search_query").focus();

        }

        return "";
      }
    }
  );

  return "";
}

// Focus quick search panel on ctrl+~
var qsOpenerKeyCode = 192; // ~
var isCtrl = false;

$(document)
  .keyup(function (e) {
    if(e.which == 17)
      isCtrl=false;
  })
  .keydown(function (e) {
    if(e.which == 17) 
      isCtrl=true;

    if(e.which == qsOpenerKeyCode && isCtrl == true) {
      $("#quick_search_query").focus();
      return false;
    } 
});
