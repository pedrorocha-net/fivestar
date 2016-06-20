###*
# @file
#
# Fivestar AJAX for updating fivestar widgets.
###

###*
# Create a degradeable star rating interface out of a simple form structure.
###

(($) ->
# Create local scope.

  Drupal.ajax::commands.fivestarUpdate = (ajax, response, status) ->
    response.selector = $('.fivestar-form-item', ajax.element.form)
    ajax.commands.insert ajax, response, status
    return

  return
) jQuery