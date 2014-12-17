<div class="card notice-card <?php echo isset($viewBag['class']) ? $viewBag['class'] : 'warning'; ?>-card">
	<h1><?php echo $viewBag['title']; ?></h1>
	<div class="text"><?php echo $viewBag['message']; ?></div>
</div>
