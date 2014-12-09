<div class="book-info">
	<h1 title="<?php echo htmlentities($viewBag['book']->title); ?>"><?php echo $viewBag['book']->title; ?></h1>
	<table>
		<tr>
			<td rowspan=7><img src="<?php echo _HOST; ?>assets/cover-missing.png" title="No cover image"/></td>
			<td colspan=3>by <?php
				$noComma = true;
				foreach ($viewBag['book']->authors as $a) {
					if (!$noComma) { echo ', '; } $noComma = false;
					echo "{$a->firstName} {$a->lastName} (Author)";
				}
			?><br/><?php echo $viewBag['book']->publisher; ?></td>
		</tr>
		<tr>
			<td>(ICON)</td>
			<td><div><?php echo $viewBag['book']->copiesForSale; ?> copies at $<?php echo $viewBag['book']->salePrice; ?></div></td>
		</tr>
		<tr>
			<td>(ICON)</td>
			<td><div><?php echo $viewBag['book']->copiesForRent; ?> copies for rent</div></td>
		</tr>
		<tr>
			<td>(ICON)</td>
			<td><div><?php echo $viewBag['book']->isbn; ?></div></td>
		</tr>
		<tr>
			<td>(ICON)</td>
			<td><div><?php echo Locale::getLanguageName($viewBag['book']->language); ?></div></td>
		</tr>
		<tr>
			<td>(ICON)</td>
			<td><div><?php echo number_format($viewBag['book']->pageCount); ?> pages</div></td>
		</tr>
		<tr>
			<td>(ICON)</td>
			<td><div><?php echo String::ordinal($viewBag['book']->edition); ?> edition</div></td>
		</tr>
	</table>
</div>
