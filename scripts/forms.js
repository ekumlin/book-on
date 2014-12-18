$(document).ready(function() {
	$('.content form').on('change', 'input', function() {
		var $this = $(this);
		if ($this.parents('.popup').length) {
			return;
		}

		var $label = $this.siblings('label');
		var label = $(this).attr('placeholder');

		if (label) {
			if (!$label.length) {
				$label = $('<label for="' + $this.attr('id') + '">' + label + '</label>');
				$label.insertBefore($this).css('opacity', 0);
			}
			$label.animate({ opacity: $this.val() ? 0.5 : 0 });
		}
	}).find('input').change();
});
