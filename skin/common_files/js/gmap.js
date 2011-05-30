/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Google map widget object
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: gmap.js,v 1.3 2010/07/02 14:07:02 joy Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

var GMap = {
  map:      false,
  geocoder: false,
  options:  {},

  // Create map show marker 
  createMap: function(obj, text, coord) {
    if (!this.map) {
      this.options.center = coord;
      this.map = new google.maps.Map(obj, this.options);
    } else {
      if (this.map.getDiv() != obj) {
        this.map = new google.maps.Map(obj, this.options);
      }
      this.map.setCenter(coord);
    }

    this.marker = new google.maps.Marker({
      map: this.map,
      position: coord,
      clickable: true,
      title: text
    });

    this.infowindow = new google.maps.InfoWindow({
      content: text,
      position: coord
    });

    this.infowindow.open(this.map, this.marker);
  },

  // Show google map (geocode from address string)
  show: function (id, address) {
    obj = document.getElementById(id);
    if (typeof(google) == 'undefined') {
      alert(gmapGeocodeError + "no map");
      $(obj).hide();
    } else {
      this.init();
      this.geocode(obj, address);
    }
  },

  init: function () {
    if (!this.geocoder) {
      this.geocoder = new google.maps.Geocoder();
      this.options = {
        zoom: 8,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        noClear: true
      };
    }
  },

  // get geocode for address string
  geocode: function (obj, address) {
    this.geocoder.geocode(
      {'address': address}, 
      function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          $(obj).show();
          GMap.createMap(obj, address, results[0].geometry.location);
        } else {
          alert(gmapGeocodeError);
          $('#modal-background,#gmap_modal').hide();
        }
      }
    );
  },

  // show google map in inner modal window
  showModal: function (address, descr) {
    Modal.open('gmap_modal', '200px', '20px', '600px', '450px');
    $('#gmap_modal_title').html(descr);
    GMap.show('gmap_modal_body', address);
  }

};

