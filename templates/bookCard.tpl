<div class="card book-card">
	<h1 title="<?php echo htmlentities($viewBag['book']->title); ?>"><?php echo $viewBag['book']->title; ?></h1>
	<div><?php echo String::ordinal($viewBag['book']->edition); ?> edition</div>
	<div class="buttons">
		<a href="#" class="card-button primary">View</a>
		<a href="#" class="card-button">Collect</a>
	</div>
</div>
