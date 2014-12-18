$(document).ready(function() {
	var $form = $('form.bookCheck-form').first(), $fieldBox = $form.find('.form-fields');
	$fieldBox.on('blur', 'input[name^=copyId]', function() {
		var $lastIdField = $fieldBox.find('input[name^=copyId]').last(), $this = $(this);

		if ($lastIdField.val()) {
			var $box = $('<div class="input-group">' + $('#blank-copy-field').html() + '</div>');
			newCopyField($fieldBox);
		}
	}).on('keydown', 'input', function(e) {
		if (e.keyCode == 9 || e.keyCode == 13) {
			var $this = $(this);
			$this.blur();

			var $inputs = $form.find('input[name][placeholder]');
			var thisIndex = $inputs.index($this);
			var $nextInput = $inputs.get(thisIndex + 1);

			$nextInput.focus();

			return false;
		}
	});

	$('form').on('click', 'input.action-negative', function() {
		if (confirm('Are you sure you want to cancel this transaction? All purchase data will be lost.')) {
			window.location = window.location;
		}
	});

	newCopyField($fieldBox);
});

function newCopyField($parent) {
	var $box = $('<div class=\'input-group\'>' + $('#blank-copy-field').html() + '</div>'), $lastInput = $parent.find('.input-group:last input');
	var index = $lastInput.length ? (parseInt($parent.find('.input-group:last input').attr('name').match(/[0-9]+$/g)) + 1) : 1;
	$box.find('input').each(function() {
		var name = $(this).attr('name') + index;
		$(this).attr({
			id: name,
			name: name,
		});
	});

	$parent.append($box);
	$parent.closest('form').find('input[name=maxCopyIndex]').val(index);
	$box.hide().slideDown();
}
