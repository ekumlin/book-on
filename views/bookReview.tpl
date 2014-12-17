<?php
	$rating = round($viewBag['review']->rating * 10) / 10;
?><div class="review">
	<div class="head">
		<div class="name"><?php echo $viewBag['review']->user->name; ?></div><div class="rating" title="<?php echo $rating > 0.0 ? "{$rating} / 5" : "No rating"; ?>"><?php View::render('rating', array('rating' => $viewBag['review']->rating, 'color' => 'fafafa')); ?></div>
	</div>
	<?php if ($viewBag['review']->review): ?>
	<div class="body">
		<?php echo $viewBag['review']->review; ?>
	</div>
	<?php endif; ?>
</div>
