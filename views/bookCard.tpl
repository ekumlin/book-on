<?php
	$bookUrl = _HOST . 'books/' . $viewBag['book']->isbn;
	$rating = round($viewBag['book']->rating * 10) / 10;
?><div class="card-box columnar">
	<div class="card book-card">
		<h1 title="<?php echo htmlentities($viewBag['book']->title); ?>"><a href="<?php echo $bookUrl; ?>"><?php echo $viewBag['book']->title; ?></a></h1>
		<div class="cover"><a href="<?php echo $bookUrl; ?>"><img src="<?php echo _HOST; ?>assets/cover-missing.png" title="No cover image"/></a></div>
		<div class="info">
			<?php if ($viewBag['book']->copies == 0): ?>
				<span class="pri3"><em>Out of stock</em></span>
			<?php endif; ?>
			<?php if ($viewBag['book']->copiesForSale > 0): ?>
				<span class="pri1">Get it for <?php echo $viewBag['book']->salePrice > 0 ? '$' . number_format($viewBag['book']->salePrice, 2) : "free"; ?></span>
			<?php endif; ?>
			<?php if ($viewBag['book']->copiesForRent > 0): ?>
				<span class="pri1">Rental available</span>
			<?php endif; ?>
		</div>
		<div class="info">
			<span class="pri3"><?php echo Locale::getLanguageName($viewBag['book']->language); ?></span>
			<span class="pri3"><?php echo String::ordinal($viewBag['book']->edition); ?> edition</span>
			<span class="pri<?php echo $rating > 0.0 ? 2 : 3; ?>" title="<?php echo $rating > 0.0 ? "{$rating} / 5" : "No rating"; ?>"><?php View::render('rating', array('rating' => $viewBag['book']->rating, 'color' => 'white')); ?></span>
		</div>
		<div class="buttons">
			<a href="<?php echo $bookUrl; ?>" class="button-flat card-button primary">View</a>
			<a href="#" class="button-flat card-button">Collect</a>
		</div>
	</div>
</div>
