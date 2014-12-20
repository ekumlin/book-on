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
	}).on('click', '.removelink', function() {
		$(this).parent().remove();
		updateAuthorList($('.input-group.authors'));
	});

	updateAuthorList($('.input-group.authors'));
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
			alert('Failed to create publisher');
		});

		ei.preventDefault();
	});
}

function askSelectPublisher($group, $input, $popup, field, mode) {
	var $title = $popup.find('.title').text('Select publisher');
	var $body = $popup.find('.body').html('<div>Loading...</div>');

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
		$group.find('.selector-content').text($(this).text());
		$input.val(publisherId);

		ei.preventDefault();
	});
}

function askCreateAuthor($group, $input, $popup, field, mode) {
	var $title = $popup.find('.title').text('Create author');
	var $body = $popup.find('.body').text('');

	var $form = $('<form />');
	$form.addClass('material-form');
	$form.html($('#createAuthorFields').html());
	$body.append($form);

	openPopup($popup);
	positionPopup($popup);

	$popup.off().on('submit', 'form', function(ei) {
		$.ajax({
			url: BookOnData.host + 'api.php',
			data: {
				controller: 'inventory',
				action: 'addNewAuthor',
				'author-firstName': $(this).find('[name=firstName]').val(),
				'author-lastName': $(this).find('[name=lastName]').val(),
			},
			dataType: 'json',
		}).done(function(msg) {
			if (msg.success) {
				askSelectAuthor($group, $input, $popup, field, mode);
			} else {
				alert(msg.errstr);
			}
		}).fail(function(jqXHR, textStatus) {
			alert('Failed to create author');
		});

		ei.preventDefault();
	});
}

function askSelectAuthor($group, $input, $popup, field, mode) {
	var $title = $popup.find('.title').text('Select author');
	var $body = $popup.find('.body').html('<div>Loading...</div>');

	$.ajax({
		url: BookOnData.host + 'api.php',
		data: {
			controller: 'read',
			action: 'allAuthors',
		},
		dataType: 'json',
	}).done(function(msg) {
		if (msg.success) {
			$body.text('');

			for (var i in msg.data) {
				var $element = $('<div/>');
				$element.addClass('popup-list-item');
				$element.text(msg.data[i].lastName + ', ' + msg.data[i].firstName);
				$element.data('id', msg.data[i].id);

				$body.append($element);
			}

			positionPopup($popup);
		} else {
			alert(msg.errstr);
		}
	}).fail(function(jqXHR, textStatus) {
		alert('Failed to load authors');
	});

	openPopup($popup);
	positionPopup($popup);

	$popup.off().on('click', '.popup-list-item', function(ei) {
		var authorId = $(this).data('id');

		var isAuthorAdded = $group.find('.selector-content div').filter(function() {
			return $(this).data('authorid') == authorId;
		}).length > 0;

		closePopup($popup);

		if (!isAuthorAdded) {
			var $newDiv = $('<div/>');
			$newDiv.data('authorid', authorId).html('<span class="removelink">&#x2716;</span> ' + $(this).html());

			$group.find('.selector-content').append($newDiv);
			updateAuthorList($group);
		}

		ei.preventDefault();
	});
}

function updateAuthorList($group) {
	var ids = [];

	$group.find('.selector-content div').each(function() {
		ids[ids.length] = $(this).data('authorid');
	});

	$group.find('input[name=authorIds]').val(ids.join(','));
}
