/* Easing functions taken from jQuery UI source code */
$.extend($.easing, {
	easeInQuad: function (x, t, b, c, d) {
		return c*(t/=d)*t + b;
	},
	easeOutQuad: function (x, t, b, c, d) {
		return -c *(t/=d)*(t-2) + b;
	},
	easeInOutQuad: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t + b;
		return -c/2 * ((--t)*(t-2) - 1) + b;
	},
});

$.support.transition = (function(){
	var thisBody = document.body || document.documentElement,
		thisStyle = thisBody.style,
		support = thisStyle.transition !== undefined || thisStyle.WebkitTransition !== undefined || thisStyle.MozTransition !== undefined || thisStyle.MsTransition !== undefined || thisStyle.OTransition !== undefined;
	return support;
})();

$(document).ready(function() {
	var animInTime = 200, animOutTime = 300;

	$('#hamburger').each(function() {
		var $this = $(this), $drawer = $('body > .drawer'), $curtain = $('#curtain');
		var initialLeft = $drawer.css('left');

		$curtain.click(function() {
			var currentLeft = $drawer.css('left');

			if (initialLeft != currentLeft) {
				$this.click();
			}
		});

		$this.click(function() {
			var currentLeft = $drawer.css('left');

			if (initialLeft == currentLeft) {
				animateDrawer($drawer, 0, 'inOut', animInTime);
				$curtain.show().fadeTo(0, 0.0).fadeTo(animInTime, 0.7);
				$('html, body').addClass('noscroll');
			} else {
				animateDrawer($drawer, initialLeft, 'in', animOutTime);
				$curtain.fadeOut(animOutTime);
				$('html, body').removeClass('noscroll');
			}
		});
	});

	$('#drawer tr').click(function() {
		window.location = $(this).find('a').attr('href');
	});
});

function animateDrawer($drawer, leftPosition, easingType, durationMillis) {
	if ($.support.transition) {
		$drawer.css('transition', 'left ' + durationMillis + 'ms ' + (easingType == 'in' ? 'ease-in' : 'ease-in-out'))
		       .css('left', leftPosition);
	} else {
		$drawer.animate({
			left: leftPosition,
		}, {
			easing: easingType == 'in' ? 'easeInQuad' : 'easeInOutQuad',
			duration: durationMillis,
		});
	}
}
