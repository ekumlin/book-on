$(document).ready(function() {
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
					alert(msg.errstr);
				}
			}).fail(function(jqXHR, textStatus) {
				alert('Failed to remove book from collection');
			});
		}

		e.preventDefault();
	});
});
