<div class="card book-card">
	<h1 title="<?php echo htmlentities($viewBag['book']->title); ?>"><?php echo $viewBag['book']->title; ?></h1>
	<div class="info">
		<?php if ($viewBag['book']->copies > 0): ?>
			<span class="pri1">$<?php echo $viewBag['book']->salePrice > 0 ? number_format($viewBag['book']->salePrice, 2) : "Rental"; ?></span>
		<?php else: ?>
			<span class="pri3"><em>Out of stock</em></span>
		<?php endif; ?>
		<span class="pri3"><?php echo Locale::getLanguageName($viewBag['book']->language); ?></span>
		<span class="pri3"><?php echo String::ordinal($viewBag['book']->edition); ?> edition</span>
	</div>
	<div class="buttons">
		<a href="#" class="card-button primary">View</a>
		<a href="#" class="card-button">Collect</a>
	</div>
</div>
