<div class="card book-card">
	<h1 title="<?php echo htmlentities($viewBag['book']->title); ?>"><?php echo $viewBag['book']->title; ?></h1>
	<div class="info">
		<span class="pri1">$<?php echo number_format($viewBag['book']->salePrice, 2); ?></span>
		<span class="pri3"><?php echo Locale::getLanguageName($viewBag['book']->language); ?></span>
		<span class="pri3"><?php echo String::ordinal($viewBag['book']->edition); ?> edition</span>
	</div>
	<div class="buttons">
		<a href="#" class="card-button primary">View</a>
		<a href="#" class="card-button">Collect</a>
	</div>
</div>
