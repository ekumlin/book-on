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

$(document).ready(function() {
	var animInTime = 150, animOutTime = 300;

	$("#hamburger").each(function() {
		var $this = $(this), $drawer = $("body > .drawer"), $curtain = $("#curtain");
		var initialLeft = $drawer.css("left");

		$curtain.click(function() {
			var currentLeft = $drawer.css("left");

			if (initialLeft != currentLeft) {
				$this.click();
			}
		});

		$this.click(function() {
			var currentLeft = $drawer.css("left");

			if (initialLeft == currentLeft) {
				$drawer.animate({
					left: 0,
				}, {
					easing: 'easeInOutQuad',
					duration: animInTime,
				});

				$curtain.show().fadeTo(0, 0.0).fadeTo(animInTime, 0.7);
			} else {
				$drawer.animate({
					left: initialLeft,
				}, {
					easing: 'easeInQuad',
					duration: animOutTime,
				});

				$curtain.fadeOut(animOutTime);
			}
		});
	});
<<<<<<< HEAD

	$("#drawer tr").click(function() {
		window.location = $(this).find("a").attr("href");
	});
});
=======
});
>>>>>>> efa772733e2c682436646a9795ef9e831b055d49
