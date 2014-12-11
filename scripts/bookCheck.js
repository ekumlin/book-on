$(document).ready(function() {
	var $form = $("form").first(), $fieldBox = $form.find(".form-fields");
	$fieldBox.on("blur", "input[name^=copyId]", function() {
		var $lastIdField = $fieldBox.find("input[name^=copyId]").last(), $this = $(this);

		if ($lastIdField.val()) {
			var $box = $("<div class=\"input-group\">" + $("#blank-copy-field").html() + "</div>");
			newCopyField($fieldBox);
		}
	}).on("keydown", "input", function(e) {
		if (e.keyCode == 9 || e.keyCode == 13) {
			$(this).blur().closest(".input-group").next().find("input").focus();
			return false;
		}
	});

	newCopyField($fieldBox);
});

function newCopyField($parent) {
	var name = "copyId" + ($parent.find(".input-group").length + 1);
	var $box = $("<div class=\"input-group\">" + $("#blank-copy-field").html() + "</div>");
	$box.find("input").attr({
		id: name,
		name: name,
	});

	$parent.append($box);
}
