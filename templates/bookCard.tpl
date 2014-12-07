<div class="card book-card">
	<h1><?php echo $viewBag['book']->title; ?></h1>
	<div><?php echo String::ordinal($viewBag['book']->edition); ?> edition</div>
</div>
