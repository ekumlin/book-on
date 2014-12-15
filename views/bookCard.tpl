<?php
	$bookUrl = _HOST . 'books/' . $viewBag['book']->isbn;
	$rating = round($viewBag['book']->rating * 2) / 2;
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
			<span class="pri<?php echo $rating > 0.0 ? 2 : 3; ?>" title="<?php echo $rating > 0.0 ? "{$rating} / 5" : "No rating"; ?>"><?php
				$stars = 5;
				if ($rating > 0.0) {
					while ($rating >= 0.75) {
						$rating -= 1.0;
						echo "&#9733;"; // Full star
						$stars--;
					}

					if ($rating >= 0.25) {
						echo "<span style='opacity:0.3'>&#9733;</span>"; // Half star (TODO)
						$stars--;
					}

					while ($stars > 0) {
						echo "&#9734;"; // Empty star
						$stars--;
					}
				} else {
					echo "No ratings";
				}
			?></span>
		</div>
		<div class="buttons">
			<a href="<?php echo $bookUrl; ?>" class="button-flat card-button primary">View</a>
			<a href="#" class="button-flat card-button">Collect</a>
		</div>
	</div>
</div>
