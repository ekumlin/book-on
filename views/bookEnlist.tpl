<form class="bookCheck-form material-form" method="POST" action="<?php echo _HOST; ?>books/addcopy" novalidate>
	<h1>Add copies to inventory</h1>
	<div id="blank-copy-field" style="display: none"><?php View::render('bookCopyIsbnEntry'); ?></div>
	<div class="form-fields"><?php echo isset($viewBag['fields']) ? $viewBag['fields'] : ''; ?></div>
	<div class="input-group">
		<input type="checkbox" name="areSoldBooks" id="bookCheck-areSoldBooks" /> <label for="bookCheck-areSoldBooks">These books are to be sold</label>
	</div><div class="form-controls">
		<input type="submit" value="Submit" class="button-flat action-primary" />
	</div>
	<input type="hidden" name="maxCopyIndex" value="0" />
</form>
