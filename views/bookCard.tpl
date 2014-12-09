<div class="card book-card">
	<h1 title="<?php echo htmlentities($viewBag['book']->title); ?>"><?php echo $viewBag['book']->title; ?></h1>
	<div class="cover"><img src="<?php echo _HOST; ?>assets/cover-missing.png" title="No cover image"/></div>
	<div class="info">
		<?php if ($viewBag['book']->copies == 0): ?>
			<span class="pri3"><em>Out of stock</em></span>
		<?php endif; ?>
		<?php if ($viewBag['book']->copiesForRent > 0): ?>
			<span class="pri1">Rental available</span>
		<?php endif; ?>
		<?php if ($viewBag['book']->copiesForSale > 0): ?>
			<span class="pri1">Buy for $<?php echo $viewBag['book']->salePrice > 0 ? number_format($viewBag['book']->salePrice, 2) : "Give-away"; ?></span>
		<?php endif; ?>
	</div>
	<div class="info">
		<span class="pri3"><?php echo Locale::getLanguageName($viewBag['book']->language); ?></span>
		<span class="pri3"><?php echo String::ordinal($viewBag['book']->edition); ?> edition</span>
	</div>
	<div class="buttons">
		<a href="#" class="card-button primary">View</a>
		<a href="#" class="card-button">Collect</a>
	</div>
</div>
