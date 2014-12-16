<?php if ($viewBag['rating'] > 0.0):
?><div class="ratingbox"><div><img src="<?php echo _HOST; ?>assets/ratingbox-<?php echo $viewBag['color']; ?>.png" /></div><div class="bar" style="width: <?php echo $viewBag['rating'] * 100 / 5; ?>%"></div></div><?php
else:
?>No ratings<?php
endif;
?>
