
/**
 * Tenbucks admin script
 *
 *  @author    Web In Color <contact@prestashop.com>
 *  @copyright 2012-2015 Web In Color
 *  @license   http://www.apache.org/licenses/  Apache License
 *  International Registered Trademark & Property of Web In Color
 */

(function() {
  $(document).ready(function() {
    return $('#deactivate_help').click(function(e) {
      var $panel, url;
      e.preventDefault();
      $panel = $(this).parents('.panel');
      url = document.location.href + "&ajax=1&action=hide_help";
      return $.getJSON(url, function(data) {
        return $panel.slideUp();
      });
    });
  });

}).call(this);

//# sourceMappingURL=back.js.map
