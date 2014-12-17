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

/* Check browser support for CSS transitions */
$.support.transition = (function(){
	var thisBody = document.body || document.documentElement,
		thisStyle = thisBody.style,
		support = thisStyle.transition !== undefined || thisStyle.WebkitTransition !== undefined || thisStyle.MozTransition !== undefined || thisStyle.MsTransition !== undefined || thisStyle.OTransition !== undefined;
	return support;
})();

/* Project specific data */
var BookOnData = {
	host: undefined,
	transitionQuick: 150,
	transitionNormal: 200,
	transitionSlow: 300,
};

/* Begin code on load */
$(document).ready(function() {
	var animInTime = BookOnData.transitionNormal, animOutTime = BookOnData.transitionSlow;
	var $curtain = $('#curtain'), $drawer = $('body > .drawer');

	BookOnData.host = $('input[name=php-host]').val();

	/* Drawer */
	$('#hamburger').each(function() {
		var $this = $(this);
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
				$(".popup").hide();
				animateDrawer($drawer, 0, 'inOut', animInTime);
				setCurtain($curtain, true, animInTime);
			} else {
				animateDrawer($drawer, initialLeft, 'in', animOutTime);
				setCurtain($curtain, false, animOutTime);
			}
		});
	});

	$('#drawer tr').click(function() {
		window.location = $(this).find('a').attr('href');
	});

	/* All popups */
	$curtain.click(function() {
		$('.popup:visible .close').click();
	});

	$(this).keyup(function(e) {
		if (e.keyCode == 27) {
			if ($curtain.is(':visible')) {
				$curtain.click();
			}
		}
	});

	$(window).resize(function() {
		positionPopup($('.popup'));
	});

	$('body').on('click', '.popup .close', function(e) {
		closePopup($(this).closest('.popup'));

		e.preventDefault();
	});

	/* Collections */
	$('a.collect').click(function(e) {
		$(".popup").hide();

		var $popup = $('.popup.collect'), $title = $popup.find('.title'), $body = $popup.find('.body');
		var isbn = $(this).data('collect');
		$title.text('Choose a collection');
		$body.text('Loading...');
		positionPopup($popup);

		$.ajax({
			url: BookOnData.host + 'api.php',
			data: {
				controller: 'collection',
				action: 'viewCollection',
				collectionId: 0,
			},
			dataType: 'json',
		}).done(function(msg) {
			$body.text('');

			for (var i in msg.data) {
				var isAdded = false;
				for (var j in msg.data[i].items) {
					if (msg.data[i].items[j].isbn == isbn) {
						isAdded = true;
						break;
					}
				}

				$body.append('<div class="popup-list-item' + (isAdded ? ' disabled' : '') + '" data-id="' + msg.data[i].collectionId + '">' + msg.data[i].name + '<div class="status">' + (isAdded ? '&#10003;' : '') + '</div></div>');
			}

			positionPopup($popup);
		}).fail(function(jqXHR, textStatus) {
			$popup.find('.body').text('Failed to load: ' + textStatus);
		});

		openPopup($popup);

		$popup.on('click', '.popup-list-item', function() {
			var $this = $(this);

			$this.addClass('disabled');

			$.ajax({
				url: BookOnData.host + 'api.php',
				data: {
					controller: 'collection',
					action: 'addCollectedBook',
					collectionId: $this.data('id'),
					isbn: isbn,
				},
				dataType: 'json',
			}).done(function(msg) {
				$this.find('.status').html('&#10003;');
			}).fail(function(jqXHR, textStatus) {
				alert('Failed to add book to collection');
			});
		});

		e.preventDefault();
	});

	$('a.uncollect').click(function(e) {
		var $this = $(this);
		var isbn = $this.data('collect'), collection = $this.closest('.collection-item-list').data('collectionid');

		if (confirm('Are you sure you want to delete this item from this collection?')) {
			$.ajax({
				url: BookOnData.host + 'api.php',
				data: {
					controller: 'collection',
					action: 'removeCollectedBook',
					collectionId: collection,
					isbn: isbn,
				},
				dataType: 'json',
			}).done(function(msg) {
				if (msg.success) {
					$this.closest('.list-item').remove();
				} else {
					alert('Failed to remove book from collection');
				}
			}).fail(function(jqXHR, textStatus) {
				alert('Failed to remove book from collection');
			});
		}

		e.preventDefault();
	});

	/* Ratings */
	$('body').on('mousemove', '.ratingbox.clickable', function(e) {
		var width = $(this).width(), ratio = Math.min(Math.floor(e.offsetX / width * 5 + 1) / 5, 1.0);
		$(this).find('.clickbar').css('width', (ratio * 100) + '%')
	}).on('mouseout', '.ratingbox.clickable', function(e) {
		$(this).find('.clickbar').css('width', 0);
	});
});

/* Functions */
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

function closePopup($popup) {
	var duration = BookOnData.transitionSlow;
	$popup.off().fadeOut(duration);
	setCurtain($('#curtain'), false, duration);
}

function openPopup($popup) {
	var duration = BookOnData.transitionQuick;
	$popup.off().show().fadeTo(0, 0.0).fadeTo(duration, 1.0);
	positionPopup($popup);
	setCurtain($('#curtain'), true, duration);
}

function positionPopup($popups) {
	var $window = $(window);
	var centerX = $window.width() / 2, centerY = $window.height() / 2;
	$popups.each(function() {
		var $this = $(this);
		$this.css({
			left: centerX - $this.outerWidth() / 2,
			top: centerY - $this.outerHeight() / 2,
		});
	});
}

function setCurtain($curtain, show, duration) {
	if (show && !$curtain.is(':visible')) {
		$curtain.show().fadeTo(0, 0.0).fadeTo(duration, 0.7);
		$('html, body').addClass('noscroll');
		return;
	}
	if (!show && $curtain.is(':visible')) {
		$curtain.fadeOut(duration);
		$('html, body').removeClass('noscroll');
		return;
	}
}
