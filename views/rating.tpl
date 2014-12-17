<?php
	$isbn = (isset($viewBag['isbn']) && $viewBag['isbn']) ? $viewBag['isbn'] : 0;
	$clickable = (isset($viewBag['clickable']) && $viewBag['clickable']);
	if ($viewBag['rating'] > 0.0 || $clickable):
?><div class="ratingbox<?php echo $clickable ? ' clickable' : ''; ?>" title="<?php echo round($viewBag['rating'] * 10) / 10; ?> / 5" data-isbn="<?php echo $isbn; ?>"><div><img src="<?php echo _HOST; ?>assets/ratingbox-<?php echo $viewBag['color']; ?>.png" /></div><div class="bar" style="width: <?php echo $viewBag['rating'] * 100 / 5; ?>%"></div><div class="clickbar" style="width: 0"></div></div><?php
	else:
?>No ratings<?php
	endif;
?>
