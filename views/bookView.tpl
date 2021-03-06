<div class="book-info">
	<div class="title-block">
		<h1 title="<?php echo htmlentities($viewBag['book']->title); ?>"><?php echo $viewBag['book']->title; ?></h1>
		<div class="info">
			<?php
				View::render('rating', array(
						'rating' => $viewBag['book']->avgRating,
						'color' => 'fafafa',
						'clickable' => isset($_SESSION['User']),
						'isbn' => $viewBag['book']->isbn,
					));
				if ($viewBag['book']->ratings[0] > 0) {
					echo number_format($viewBag['book']->ratings[0]) . ' ratings';
				}
			?>
		</div><div class="info">by <?php
			$noComma = true;
			foreach ($viewBag['book']->authors as $a) {
				if (!$noComma) { echo ', '; } $noComma = false;
				echo "{$a->firstName} {$a->lastName}";
			}
		?></div><div class="info">
			<?php echo $viewBag['book']->publisher; ?>
		</div>
	</div>
	<h2>Details</h2>
	<div class="cover">
		<img src="<?php echo _HOST; ?>assets/cover-missing.png" title="No cover image"/>
		<?php if (Http::canAccess(User::USER_BASIC)): ?>
		<a href="#" class="button button-raised collect" data-collect="<?php echo $viewBag['book']->isbn; ?>">Collect</a><br/>
		<?php if (Http::canAccess(User::USER_STAFF)): ?>
		<a href="<?php echo _HOST; ?>books/edit/<?php echo $viewBag['book']->isbn; ?>" class="button button-raised edit">Edit</a>
		<?php endif; endif; ?>
	</div><div class="data">
		<?php if (Http::canAccess(User::USER_BASIC)): ?>
		<div id="collect-button"><a href="#" class="button button-floating collect" data-collect="<?php echo $viewBag['book']->isbn; ?>"><img src="<?php echo _HOST; ?>assets/icon-collect.white.png" title="Collect"/></a></div>
		<?php endif; ?>
		<table>
			<tr>
				<td><img src="<?php echo _HOST; ?>assets/icon-buy.png" title="Price"/></td>
				<td><div><?php echo $viewBag['book']->copiesForSale; ?> copies at $<?php echo number_format($viewBag['book']->salePrice, 2); ?></div></td>
			</tr>
			<tr class="section-end">
				<td><img src="<?php echo _HOST; ?>assets/icon-rent.png" title="Rent"/></td>
				<td><div><?php echo $viewBag['book']->copiesForRent; ?> copies for rent</div></td>
			</tr>
			<tr>
				<td><img src="<?php echo _HOST; ?>assets/icon-edition.png" title="Edition"/></td>
				<td><div><?php echo StringUtil::ordinal($viewBag['book']->edition); ?> edition</div></td>
			</tr>
			<tr>
				<td><img src="<?php echo _HOST; ?>assets/icon-language.png" title="Language"/></td>
				<td><div><?php echo Locale::getLanguageName($viewBag['book']->language); ?></div></td>
			</tr>
			<tr>
				<td><img src="<?php echo _HOST; ?>assets/icon-isbn.png" title="ISBN"/></td>
				<td><div><?php echo StringUtil::formatIsbn($viewBag['book']->isbn); ?></div></td>
			</tr>
			<tr>
				<td><img src="<?php echo _HOST; ?>assets/icon-pages.png" title="Page count"/></td>
				<td><div><?php echo number_format($viewBag['book']->pageCount); ?> pages</div></td>
			</tr>
		</table>
	</div>
	<h2>Reviews</h2>
	<div class="reviews">
		<div class="rating-summary">
			<div class="stats">
				<div class="average">
					<?php echo round($viewBag['book']->avgRating * 10) / 10; ?>
				</div>
				<?php echo number_format($viewBag['book']->ratings[0]); ?> ratings
			</div><div class="labels">
				<?php for ($i = 5; $i >= 1; $i--): ?>
				<div class="label"><img src="<?php echo _HOST; ?>assets/icon-collect.png" ?> <?php echo $i; ?></div>
				<?php endfor; ?>
			</div><div class="graph">
				<?php for ($i = 5; $i >= 1; $i--): ?>
				<div class="rank"><div class="bar bar<?php echo $i; ?>" style="width: <?php echo max($viewBag['book']->ratings[0] > 0.0 ? ($viewBag['book']->ratings[$i] * 100.0 / $viewBag['book']->ratings[0]) : 0.0, 2.0); ?>%"></div><div class="rating"><?php echo number_format($viewBag['book']->ratings[$i]); ?></div></div>
				<?php endfor; ?>
			</div>
		</div><div class="multicol">
			<?php
				if (count($viewBag['reviews']) > 0) {
					$i = 0;
					foreach ($viewBag['reviews'] as $r) {
						if ($i > 0 && $i % 2 == 0) {
							echo '</div><div class="multicol">';
						}

						View::render('bookReview', array(
								'review' => $r,
							));

						$i++;
					}
				} else {
					echo '<div class="no-review">No reviews yet. Be the first!</div>';
				}
			?>
		</div>
	</div>
</div>
