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
