<?php
	$rating = round($viewBag['review']->rating * 2) / 2;
?><div class="review">
	<div class="head">
		<div class="name"><?php
			echo $viewBag['review']->user->name;
			if (isset($_SESSION['User']) && $_SESSION['User']->employeeLevel >= User::USER_STAFF) {
				echo "<br/><span class=\"email\">{$viewBag['review']->user->email}</span>";
			}
		?></div><div class="rating" title="<?php echo $rating > 0.0 ? "{$rating} / 5" : "No rating"; ?>"><?php
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
		?></div>
	</div>
	<?php if ($viewBag['review']->review): ?>
	<div class="body">
		<?php echo $viewBag['review']->review; ?>
	</div>
	<?php endif; ?>
</div>
