$(document).ready(function() {
	$('.content form').on('change', 'input', function() {
		var $this = $(this), $label = $this.siblings('label');
		var label = $(this).attr('placeholder');

		if (label) {
			if (!$label.length) {
				$label = $('<label for="' + $this.attr('id') + '">' + label + '</label>');
				$label.insertBefore($this);
			}
			$label.css('visibility', $this.val() ? 'visible' : 'hidden');
		}
	}).find('input').change();
});
