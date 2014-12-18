<?php
	$price = 0.0;
?><form class="bookSale-form material-form" method="POST" action="<?php echo _HOST; ?>books/sell" novalidate>
	<h1>Confirm sale</h1>
	<?php foreach ($viewBag['books'] as $copy): ?>
	<div class="item-sale">
		<div class="item-name"><?php echo $copy->book->title; ?></div>
		<div class="item-price">$<?php echo number_format($copy->book->salePrice, 2); $price += $copy->book->salePrice; ?></div>
		<div style="clear: both"></div>
	</div>
	<?php endforeach; ?>
	<div class="item-sale subtotal">
		<div class="item-name">Tax</div>
		<div class="item-price">$<?php echo number_format($price * TAX_RATE, 2); ?></div>
		<div style="clear: both"></div>
	</div>
	<div class="item-sale total">
		<div class="item-name">Total</div>
		<div class="item-price">$<?php echo number_format($price * (1 + TAX_RATE), 2); ?></div>
		<div style="clear: both"></div>
	</div>
	<div class="form-controls">
		<input type="button" value="Cancel" class="button-flat action-negative" />
		<input type="submit" value="Confirm" class="button-flat action-primary" />
	</div>
	<input type="hidden" name="final" value="yes" />
	<?php foreach ($_POST as $k => $v): ?>
	<input type="hidden" name="<?php echo $k; ?>" value="<?php echo $v; ?>" />
	<?php endforeach; ?>
</form>
