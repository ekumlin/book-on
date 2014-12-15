<?php
	$collectionUrl = _HOST . 'collections/' . $viewBag['collection']->collectionId;
?><div class="list-item collection-list-item">
	<div class="name"><a href="<?php echo $collectionUrl; ?>"><?php echo $viewBag['collection']->name; ?></a></div>
	<div class="bookCount"><?php echo count($viewBag['collection']->items); ?></div>
</div>
