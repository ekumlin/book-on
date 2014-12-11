<<<<<<< HEAD
<form class="bookEdit-form material-form" method="POST" action="<?php echo _HOST; ?>book/<?php echo $viewBag['target']; ?>" novalidate>
=======
<form class="bookEdit-form material-form" method="POST" action="<?php echo _HOST; ?>book/add" novalidate>
>>>>>>> efa772733e2c682436646a9795ef9e831b055d49
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
		<select name="language">
			<?php
				global $CONFIG;
				if (strlen($viewBag['book']->language) == 2) {
					$defaultLang = $viewBag['book']->language;
				} else {
					$defaultLang = $CONFIG['language'];
				}
				foreach (Locale::getLanguageList() as $iso => $name) {
					$isSelected = $iso == $defaultLang ? ' selected' : '';
					echo "<option value=\"{$iso}\"{$isSelected}>{$name}</option>";
				}
			?>
		</select>
	</div>
	<div class="input-group">
<<<<<<< HEAD
		<input type="number" name="edition" id="bookEdit-edition" value="<?php echo $viewBag['book']->edition; ?>" pattern="^[0-9]+$" placeholder="Edition" />
		<div class="input-status input-error"></div>
	</div>
	<div class="input-group">
		<input type="number" name="salePrice" id="bookEdit-salePrice" value="<?php echo $viewBag['book']->salePrice; ?>" pattern="^[0-9.]+$" placeholder="Price" />
		<div class="input-status input-error"></div>
	</div>
	<div class="input-group">
		<input type="number" name="pageCount" id="bookEdit-pageCount" value="<?php echo $viewBag['book']->pageCount; ?>" pattern="^[0-9]+$" placeholder="Pages" />
=======
		<input type="number" name="edition" id="bookEdit-edition" pattern="^[0-9]+$" placeholder="Edition" />
		<div class="input-status input-error"></div>
	</div>
	<div class="input-group">
		<input type="number" name="salePrice" id="bookEdit-salePrice" pattern="^[0-9.]+$" placeholder="Price" />
		<div class="input-status input-error"></div>
	</div>
	<div class="input-group">
		<input type="number" name="pageCount" id="bookEdit-pageCount" pattern="^[0-9]+$" placeholder="Pages" />
>>>>>>> efa772733e2c682436646a9795ef9e831b055d49
		<div class="input-status input-error"></div>
	</div>
	<div class="input-group">
		TODO: Publisher
	</div>
	<div class="input-group">
		TODO: Author
	</div>
	<div class="form-controls">
		<input type="button" value="Discard" class="button-flat action-negative" />
		<input type="submit" value="Save" class="button-flat action-primary" />
	</div>
</form>
