$(document).ready ->
    $('#deactivate_help').click (e) ->
        e.preventDefault()
        $panel = $(@).parents('.panel')
        url = "#{document.location.href}&ajax=1&action=hide_help"
        $.getJSON url, (data) ->
            $panel.slideUp()
