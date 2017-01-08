<?php
	$bookUrl = _HOST . 'books/' . $viewBag['bookCopy']->book->isbn;
?><div class="list-item book-held-list-item">
	<div class="isbn"><a href="<?php echo $bookUrl; ?>"><?php echo StringUtil::formatIsbn($viewBag['bookCopy']->book->isbn); ?></a></div>
	<div class="title"><a href="<?php echo $bookUrl; ?>"><?php echo $viewBag['bookCopy']->book->title; ?></a></div>
	<div class="returnDate" title="<?php echo date(config('datetime-format'), $viewBag['bookCopy']->returnDate); ?>"><?php echo $viewBag['bookCopy']->returnDate ? date(config('date-format'), $viewBag['bookCopy']->returnDate) : ''; ?></div>
	<div class="rentalDate" title="<?php echo date(config('datetime-format'), $viewBag['bookCopy']->rentalDate); ?>"><?php echo $viewBag['bookCopy']->rentalDate ? date(config('date-format') . ' (' . config('time-format') . ')', $viewBag['bookCopy']->rentalDate) : ''; ?></div>
	<div class="copyNumber"><?php echo $viewBag['bookCopy']->copyId; ?></div>
</div>
