<?php
	$bookUrl = _HOST . 'books/' . $viewBag['book']->isbn;
?><div class="list-item book-list-item">
	<div class="isbn"><a href="<?php echo $bookUrl; ?>"><?php echo StringUtil::formatIsbn($viewBag['book']->isbn); ?></a></div>
	<div class="title"><a href="<?php echo $bookUrl; ?>"><?php echo $viewBag['book']->title; ?></a></div>
	<div class="copies"><?php echo number_format($viewBag['book']->copies); ?></div>
	<div class="language"><?php echo Locale::getLanguageName($viewBag['book']->language); ?></div>
	<div class="edition"><?php echo StringUtil::ordinal($viewBag['book']->edition); ?> edition</div>
</div>
