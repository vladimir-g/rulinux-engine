// hightlight.js

var highLighted;

function highLight(toHighLight)
{
    if (highLighted==toHighLight) {
      return;
    }

    if (highLighted) {
      highLighted.className="msg";
    }

    highLighted = toHighLight;
    highLighted.className = "msg highLighted";
}

function highlightMessage(id)
{
  var toHighLight = document.getElementById(id);

  if (toHighLight)
  {
    highLight(toHighLight);
  }
}

function parseHash()
{
  var results = location.hash.match(/^#(msg[1-9]\d*)$/);
  if (results) {
    highlightMessage(results[1]);
  }
}

setInterval(parseHash, 1000);

(function (h) { h.className = h.className + ' js'; })(document.documentElement);

(function ($) {
    
    $(function () {
        // Filter list
        $('a.filter-link').click(function (e) {
            e.preventDefault();
            $($(this).data('fblock')).toggle();
            if ($(this).hasClass('opened'))
                $(this).removeClass('opened');
            else
                $(this).addClass('opened');
        });
        // Toggle filtered message
        $('a.toggle-hidden').click(function (e) {
            e.preventDefault();
            $($(this).data('hidden')).toggle();
        });
    });

})(jQuery);
