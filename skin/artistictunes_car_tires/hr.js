/*
$Id: hr.js,v 1.1 2010/05/21 08:31:53 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*/

/*
hr_list - id of categories list
hr_menu - id of categories menu
*/

var hr_flag = false;

function hrMenuShow() {
  if (hr_flag) clearTimeout(hr_flag);
  if ($("#hr_menu").css("display") != "none") return;
  $("#hr_menu").hide();
  if (hrMenuCheck() <= 0) return;
  $("#menu_more").attr("src", alt_images_dir + "/custom/menu_more_yellow.gif");
  $("#hr_menu").show();
}

function hrMenuHide() {
  if (hr_flag) clearTimeout(hr_flag);
  hr_flag = setTimeout(function () { 
    $("#hr_menu").hide();
    $("#menu_more").attr("src", alt_images_dir + "/custom/menu_more.gif");
  }, 500);
}

function hrMenuCheck() {
  var len = 0;
  var hr_menu_html = "";
  var count = 0;

  $("#hr_menu").html("");
  a = $("#hr_list li").each(
    function (index) {
      if (index == 0) {
        len = $(this).offset().top;
      } else {
        if ($(this).offset().top > len) {
          count++;
          hr_menu_html += "<li>" + decodeURI($(this).html()) + "</li>";
        }
      }
    });
    $("#hr_menu").html(hr_menu_html);
    if (count == 0) {
      $(".more-categories").hide();
    } else {
      $(".more-categories").show();
    }
    return count;
}

$(window).resize(hrMenuCheck);
$(document).ready(hrMenuCheck);
