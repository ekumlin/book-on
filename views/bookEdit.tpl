<form class="bookEdit-form material-form" method="POST" action="<?php echo _HOST; ?>books/<?php echo $viewBag['target']; ?>">
	<h1><?php echo $viewBag['target'] == 'add' ? 'Adding new book' : "Editing \"{$viewBag['book']->title}\""; ?></h1>
	<div class="input-group">
		<input type="tel" name="isbn" id="bookEdit-isbn" pattern="^[0-9-]+$" value="<?php echo $viewBag['book']->isbn; ?>" placeholder="ISBN" required />
		<div class="input-status input-error"></div>
	</div>
	<div class="input-group">
		<input type="text" name="bookTitle" id="bookEdit-bookTitle" value="<?php echo $viewBag['book']->title; ?>" placeholder="Title" maxlength="500" required />
		<div class="input-status input-error"></div>
		<div class="input-status input-dependent" data-dependence="bookEdit-bookTitle" data-attribute="length"></div>
	</div>
	<div class="input-group">
		<label>Language</label>
		<select name="language">
			<?php
				if (strlen($viewBag['book']->language) == 2) {
					$defaultLang = $viewBag['book']->language;
				} else {
					$defaultLang = config('language');
				}
				foreach (Locale::getLanguageList() as $iso => $name) {
					$isSelected = $iso == $defaultLang ? ' selected' : '';
					echo "<option value=\"{$iso}\"{$isSelected}>{$name}</option>";
				}
			?>
		</select>
	</div>
	<div class="input-group">
		<input type="number" name="edition" id="bookEdit-edition" value="<?php echo $viewBag['book']->edition; ?>" pattern="^[0-9]+$" placeholder="Edition" />
		<div class="input-status input-error"></div>
	</div>
	<div class="input-group">
		<input type="number" name="salePrice" id="bookEdit-salePrice" value="<?php echo number_format($viewBag['book']->salePrice, 2); ?>" pattern="^[0-9.]+$" step="any" placeholder="Price" />
		<div class="input-status input-error"></div>
	</div>
	<div class="input-group">
		<input type="number" name="pageCount" id="bookEdit-pageCount" value="<?php echo $viewBag['book']->pageCount; ?>" pattern="^[0-9]+$" placeholder="Pages" />
		<div class="input-status input-error"></div>
	</div>
	<div class="input-group">
		<label>Publisher</label>
		<div class="selector">
			<a href="#" class="button-flat action-primary select">Select</a>
			<a href="#" class="button-flat action-creative create">Create</a>
			<div class="selector-content"><?php echo $viewBag['book']->publisher; ?></div>
		</div>
		<input type="hidden" name="publisher" value="" />
	</div>
	<div class="input-group disabled">
		<label>Author</label>
		<div class="selector">
			<a href="#" class="button-flat action-primary select">Select</a>
			<a href="#" class="button-flat action-creative create">Create</a>
			<div class="selector-content">
				<?php foreach ($viewBag['book']->authors as $a): ?>
				<div><?php echo "{$a->firstName} {$a->lastName}"; ?></div>
				<?php endforeach; ?>
			</div>
		</div>
		<input type="hidden" name="authors" value="" />
	</div>
	<div class="form-controls">
		<input type="button" value="Discard" class="button-flat action-negative" />
		<input type="submit" value="Save" class="button-flat action-primary" />
	</div>
</form>
<?php View::render('popup', array('class' => 'bookEditPopup', 'content' => '')); ?>
<?php View::render('bookEditPublisher'); ?>
