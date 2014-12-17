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
			$(this).blur().closest('.input-group').next().find('input').focus();
			return false;
		}
	});

	newCopyField($fieldBox);
});

function newCopyField($parent) {
	var $box = $('<div class=\'input-group\'>' + $('#blank-copy-field').html() + '</div>'), $lastInput = $parent.find('.input-group:last input');
	var index = $lastInput.length ? (parseInt($parent.find('.input-group:last input').attr('name').match(/[0-9]+$/g)) + 1) : 1, name = 'copyId' + index;
	$box.find('input').attr({
		id: name,
		name: name,
	});

	$parent.append($box);
	$parent.closest('form').find('input[name=maxCopyIndex]').val(index);
	$box.hide().slideDown()
}
