$(document).ready(function() {
	var $form = $('form.bookEdit-form').first();
	$form.on('click', 'a.select, a.create', function(e) {
		var $group = $(this).closest('.input-group'),
			$input = $group.find('input[type=hidden]'),
			$popup = $('.popup.bookEditPopup');
		var field = $input.attr('name'),
			mode = $(this).hasClass('create') ? 'create' : 'select';

		if (field == 'publisher') {
			var $title = $popup.find('.title').text('Create publisher');
			var $body = $popup.find('.body').text('Loading...');

			$.ajax({
				url: BookOnData.host + 'api.php',
				data: {
					controller: 'read',
					action: 'allPublishers',
				},
				dataType: 'json',
			}).done(function(msg) {
				if (msg.success) {
					$body.text('');

					for (var i in msg.data) {
						var $element = $('<div/>');
						$element.addClass('popup-list-item');
						$element.text(msg.data[i].name);
						$element.data('id', msg.data[i].id);

						$body.append($element);
					}

					positionPopup($popup);
				} else {
					alert('Failed to load publishers');
				}
			}).fail(function(jqXHR, textStatus) {
				alert('Failed to load publishers');
			});

			openPopup($popup);
			positionPopup($popup);

			$popup.off().on('click', '.popup-list-item', function(ei) {
				var publisherId = $(this).data('id');

				closePopup($popup);
				$input.val(publisherId);
				$group.find('.selector-content').text($(this).text());

				ei.preventDefault();
			});
		}

		e.preventDefault();
	});
});
