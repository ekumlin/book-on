$(document).ready(function() {
	var $form = $('form.bookEdit-form').first();
	$form.on('click', 'a.select, a.create', function(e) {
		var $group = $(this).closest('.input-group'),
			$input = $group.find('input[type=hidden]'),
			$popup = $('.popup.bookEditPopup');
		var field = $input.attr('name'),
			mode = $(this).hasClass('create') ? 'create' : 'select';

		e.preventDefault();

		if ($group.hasClass('disabled')) {
			return;
		}

		if (field == 'publisher') {
			if (mode == 'select') {
				askSelectPublisher($group, $input, $popup, field, mode);
			} else {
				askCreatePublisher($group, $input, $popup, field, mode);
			}
		} else {
			if (mode == 'select') {
				askSelectAuthor($group, $input, $popup, field, mode);
			} else {
				askCreateAuthor($group, $input, $popup, field, mode);
			}
		}
	});
});

function askCreatePublisher($group, $input, $popup, field, mode) {
	var $title = $popup.find('.title').text('Create publisher');
	var $body = $popup.find('.body').text('');

	var $form = $('<form />');
	$form.addClass('material-form');
	$form.html($('#createPublisherFields').html());
	$body.append($form);

	openPopup($popup);
	positionPopup($popup);

	$popup.off().on('submit', 'form', function(ei) {
		$.ajax({
			url: BookOnData.host + 'api.php',
			data: {
				controller: 'read',
				action: 'createPublisher',
				name: $(this).find('[name=name]').val(),
				address: $(this).find('[name=address]').val(),
				phone: $(this).find('[name=phone]').val(),
			},
			dataType: 'json',
		}).done(function(msg) {
			if (msg.success) {
				closePopup($popup);
				$input.val(msg.data.id);
				$group.find('.selector-content').text(msg.data.name);
			} else {
				alert(msg.errstr);
			}
		}).fail(function(jqXHR, textStatus) {
			alert('Failed to load publishers');
		});

		ei.preventDefault();
	});
}

function askSelectPublisher($group, $input, $popup, field, mode) {
	var $title = $popup.find('.title').text('Select publisher');
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
			alert(msg.errstr);
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
