/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Modal popup object
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: modal.js,v 1.2 2010/05/27 13:43:07 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

var Modal = {

  obj: [],
  bg: false,

  open: function (id, left, top, width, height) {
    if (!this.bg) {
      $("body").append('<div id="modal-background"></div>');
      this.bg = "#modal-background";
      var context = this;
      $(window).resize(
        function () {
          context.resize();
        }
      );
    }

    if (typeof(this.obj[id]) == "undefined") {
      this.create(id, left, top, width, height);
    }

    $(this.bg).show();
    this.resize();
    $(this.obj[id]).fadeIn('slow');
  },

  resize: function () {
    $(this.bg).width(document.documentElement.scrollWidth);
    $(this.bg).height(document.documentElement.scrollHeight);
  },

  create: function (id, left, top, width, height) {
    var context = this;
    $("body").append(
      '<div id="' + id + '" class="modal-window" style="' +
      ' display:none' +
      '; width:' + width +
      '; height: ' + height +
      '; left: ' + left +
      '; top: ' + top +
      '"><div class="modal-title" id="' + id + '_title"></div><a href="javascript:void(0);" class="modal-close" id="' + id + '_close" >' + lbl_close + '</a>' +
      '<div class="modal-body" id="' + id + '_body"></div></div>'
    );

    $("#" + id + "_close").click(function() {context.close(id);});

    this.obj[id] = '#' + id;
  },

  close: function (id) {
    $(this.obj[id]).fadeOut('slow');
    $(this.bg).hide();
  }

};

