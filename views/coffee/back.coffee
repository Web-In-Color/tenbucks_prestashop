###*
 * Tenbucks admin script
 *
 *  @author    Web In Color <contact@prestashop.com>
 *  @copyright 2012-2015 Web In Color
 *  @license   http://www.apache.org/licenses/  Apache License
 *  International Registered Trademark & Property of Web In Color
###

$(document).ready ->
    $('#deactivate_help').click (e) ->
        e.preventDefault()
        $panel = $(@).parents('.panel')
        url = "#{document.location.href}&ajax=1&action=hide_help"
        $.getJSON url, (data) ->
            $panel.slideUp()
