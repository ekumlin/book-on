<?php
	$bookUrl = _HOST . 'book/' . $viewBag['book']->isbn;
?><div class="book-list-item">
	<div class="isbn"><a href="<?php echo $bookUrl; ?>"><?php echo String::formatIsbn($viewBag['book']->isbn); ?></a></div>
	<div class="title"><a href="<?php echo $bookUrl; ?>"><?php echo $viewBag['book']->title; ?></a></div>
	<div class="copies"><?php echo number_format($viewBag['book']->copies); ?></div>
	<div class="language"><?php echo Locale::getLanguageName($viewBag['book']->language); ?></div>
	<div class="edition"><?php echo String::ordinal($viewBag['book']->edition); ?> edition</div>
</div>
