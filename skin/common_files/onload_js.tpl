{*
$Id: onload_js.tpl,v 1.10.2.1 2010/10/27 10:02:13 ferz Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=onload_js}

{if $smarty.get.is_install_preview}
{literal}
//  Fix problem with refreshing of the skin preview  
//  during skin installation
var _ts = new Date();
$('link').each(function(){
$(this).attr('href', this.href + '?' + _ts.valueOf());
});
{/literal}
{/if}

{if $config.SEO.clean_urls_enabled eq "Y"}
{literal}
//  Fix a.href if base url is defined for page
function anchor_fix() {
var links = document.getElementsByTagName('A');
var m;
var _rg = new RegExp("(^|" + self.location.host + xcart_web_dir + "/)#([\\w\\d_]+)$")
for (var i = 0; i < links.length; i++) {
  if (links[i].href && (m = links[i].href.match(_rg))) {
    links[i].href = 'javascript:void(self.location.hash = "' + m[2] + '");';
  }
}
}

if (window.addEventListener)
window.addEventListener("load", anchor_fix, false);

else if (window.attachEvent)
window.attachEvent("onload", anchor_fix);
{/literal}
{/if}

{literal}
function initDropOutButton() {

  if ($(this).hasClass('activated-widget'))
    return;

  $(this).addClass('activated-widget');

  var dropOutBoxObj = $(this).parent().find('.dropout-box');

  // Process the onclick event on a dropout button 
  $(this).click(
    function(e) {
      e.stopPropagation();
      $('.dropout-box').removeClass('current');
      dropOutBoxObj
        .toggle()
        .addClass('current');
      $('.dropout-box').not('.current').hide();
      if (dropOutBoxObj.offset().top + dropOutBoxObj.height() - $('#center-main').offset().top - $('#center-main').height() > 0) {
        dropOutBoxObj.css('bottom', '-2px');
      }
    }
  );
 
  // Click on a dropout layer keeps the dropout content opened
  $(this).parent().click(
    function(e) { 
      e.stopPropagation(); 
    }
  );

  // shift the dropout layer from the right hand side 
  // if it's out of the main area
  var borderDistance = ($("#center-main").offset().left + $("#center-main").outerWidth()) - ($(this).offset().left + dropOutBoxObj.outerWidth());
  if (!isNaN(borderDistance) && borderDistance < 0) {
    dropOutBoxObj.css('left', borderDistance+'px');
  }

  // Fix for IE6
  if ($.browser.msie && $.browser.version < 7) {
    dropOutBoxObj.bgiframe();
  }
}

$(document).ready( function() {
  $('body').click(
    function() {
      $('.dropout-box')
        .filter(function() { return $(this).css('display') != 'none'; } )
        .hide();
    }
  );
  $('div.dropout-container div.drop-out-button').each(initDropOutButton);
}
);
{/literal}

{if $config.UA.browser eq "MSIE"}
{literal}
// Fix with positioning (z-indexes)
var i = 100;
$(document).ready(function(){
$(".products div.item").each(function() {
  $(this).css({'background-color' : $(this).css('background-color'), 'z-index' : i--});
});
change_width_iefix();
});
{/literal}

{if $config.UA.version eq "6.0"}
{literal}
// Fix for displaying PNG images in IE6
$.event.add( window, 'load', function() {
$('img.png-image').each( function() {
  if (this.src && this.src.search(/\.png($|\?)/) != -1) {
    if (this.currentStyle.width == 'auto')
      this.style.width = this.offsetWidth + 'px';

    if (this.currentStyle.height == 'auto')
      this.style.height = this.offsetHeight + 'px';

    pngFix(this);
  }
});
});
{/literal}
{/if}
{/if}

{literal}
// Position:absolute elements will not move when window is resized 
// if a sibling contains float elements and a clear:both element
// https://bugzilla.mozilla.org/show_bug.cgi?id=442542
// FireFox 3.0+
if (navigator.userAgent.toLowerCase().search(/firefox\/(3\.\d+)/i) != -1 && typeof(window.$) != 'undefined') {
$.event.add( window, 'resize', function() {
  var h = document.getElementById('header');
  if (!h || $(h).css('position') != 'absolute')
    return;

  document.getElementById('header').style.position = 'static';
  setTimeout(
    function() {
      document.getElementById('header').style.position = 'absolute';
    },
  20);
});
}
{/literal}

var md = {$config.Appearance.delay_value|default:10}*1000;

{literal}
$(document).ready( function() {

$('form').not('.skip-auto-validation').each(function() {
  applyCheckOnSubmit(this);
});

$('a.toggle-link').live(
  'click',
  function(e) {
    $('#' + $(this).attr('id').replace('link', 'box')).toggle();
  }
);

$("#dialog-message").fadeIn('slow').delay(md).fadeOut('slow');

});
{/literal}

{if $products ne "" or $free_products ne "" or $product ne ""}
{literal}
if (products_data == undefined) {
var products_data = [];
}
{/literal}
{/if}

{/capture}

{load_defer file="onload_js" direct_info=$smarty.capture.onload_js type="js" queue="1"}

{if $active_modules.Product_Options ne ""}
{load_defer file="modules/Product_Options/func.js" type="js"}
{/if}

{if $products or $free_products}
{load_defer file="js/check_quantity.js" type="js"}
{if $active_modules.Feature_Comparison and not $printable and $products_has_fclasses}
{load_defer file="modules/Feature_Comparison/products_check.js" type="js"}
{/if}
{/if}

