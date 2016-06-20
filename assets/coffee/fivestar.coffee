###*
# @file
#
# Fivestar JavaScript behaviors integration.
###

###*
# Create a degradeable star rating interface out of a simple form structure.
#
# Originally based on the Star Rating jQuery plugin by Wil Stuckey:
# http://sandbox.wilstuckey.com/jquery-ratings/
###

(($) ->
  Drupal.behaviors.fivestar =
    attach: (context) ->
# debugger;
      $(context).find('div.fivestar-form-item').once('fivestar').each ->
        $this = $(this)
        $container = $('<div class="fivestar-widget clearfix"></div>')
        $select = $('select', $this)
        # Setup the cancel button
        $cancel = $('option[value="0"]', $this)
        if $cancel.length
          $('<div class="cancel"><a href="#0" title="' + $cancel.text() +'">' +
                $cancel.text() + '</a></div>').appendTo $container
        # Setup the rating buttons
        $options = $('option', $this).not('[value="-"], [value="0"]')
        index = -1
        $options.each (i, element) ->
          classes = 'star-' + i + 1
          classes += if (i + 1) % 2 == 0 then ' even' else ' odd'
          classes += if i == 0 then ' star-first' else ''
          classes += if i + 1 == $options.length then ' star-last' else ''
          $('<div class="star"><a href="#' + element.value +
              '" title="' + element.text + '">' + element.text +
              '</a></div>').addClass(classes).appendTo $container

          if element.value == $select.val()
            index = i + 1
          return
        if index != -1
          $container.find('.star').slice(0, index).addClass 'on'
        $container.addClass 'fivestar-widget-' + $options.length

        $container.find('a').bind(
          'click',
          $this,
          Drupal.behaviors.fivestar.rate
        ).bind 'mouseover', $this, Drupal.behaviors.fivestar.hover

        $container.bind 'mouseover mouseout',
          $this, Drupal.behaviors.fivestar.hover

        # Attach the new widget and hide the existing widget.
        $select.after($container).css 'display', 'none'
        # Allow other modules to modify the widget.
        # Drupal.attachBehaviors($this);
        return
      return
    rate: (event) ->
      $this = $(this)
      $widget = event.data
      value = @hash.replace('#', '')
      $('select', $widget).val(value).change()

      if value == 0
        $this_star = $this.parent().parent().find('.star')
      else
        $this_star = $this.closest('.star')

      $this_star.prevAll('.star').andSelf().addClass 'on'
      $this_star.nextAll('.star').removeClass 'on'
      if value == 0
        $this_star.removeClass 'on'
      event.preventDefault()
      return
    hover: (event) ->
      $this = $(this)
      $widget = event.data
      $target = $(event.target)
      $stars = $('.star', $this)
      if event.type == 'mouseover'
        index = $stars.index($target.parent())
        $stars.each (i, element) ->
          if i <= index
            $(element).addClass 'hover'
          else
            $(element).removeClass 'hover'
          return
      else
        $stars.removeClass 'hover'
      return
  return) jQuery