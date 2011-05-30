{*
$Id: ajax.rating.tpl,v 1.1 2010/05/21 08:31:53 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name="ajax_rating"}
var lbl_rated = '{$lng.lbl_rated|wm_remove|escape:javascript}';
var lbl_error = '{$lng.lbl_error|wm_remove|escape:javascript}';
var lbl_cancel_vote = '{$lng.lbl_cancel_vote|wm_remove|escape:javascript}';
var rating_msie6_bug_flag;
{literal}
if (!rating_msie6_bug_flag && $.browser.msie && parseInt($.browser.version) < 7) {
  rating_msie6_bug_flag = true;
  $(document).ready(
    function() {
      setTimeout(
        function() {
          $('li.star-0').css('position', 'relative');
        },
        500
      );
    }
  );
}
{/literal}
{/capture}
{load_defer file="ajax_rating" direct_info=$smarty.capture.ajax_rating type="js"}
{load_defer file="modules/Customer_Reviews/ajax.rating.js" type="js"}
