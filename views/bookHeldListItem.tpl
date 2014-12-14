<?php
	global $CONFIG;
	$bookUrl = _HOST . 'books/' . $viewBag['bookCopy']->book->isbn;
?><div class="list-item book-held-list-item">
	<div class="isbn"><a href="<?php echo $bookUrl; ?>"><?php echo String::formatIsbn($viewBag['bookCopy']->book->isbn); ?></a></div>
	<div class="title"><a href="<?php echo $bookUrl; ?>"><?php echo $viewBag['bookCopy']->book->title; ?></a></div>
	<div class="returnDate" title="<?php echo date($CONFIG['datetime-format'], $viewBag['bookCopy']->returnDate); ?>"><?php echo date("{$CONFIG['date-format']} ({$CONFIG['time-format']})", $viewBag['bookCopy']->returnDate); ?></div>
	<div class="rentalDate" title="<?php echo date($CONFIG['datetime-format'], $viewBag['bookCopy']->rentalDate); ?>"><?php echo date("{$CONFIG['date-format']} ({$CONFIG['time-format']})", $viewBag['bookCopy']->rentalDate); ?></div>
	<div class="copyNumber"><?php echo $viewBag['bookCopy']->copyId; ?></div>
</div>
