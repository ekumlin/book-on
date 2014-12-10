<?php
	// TODO Obviously not permanent
	foreach ($viewBag['collections'] as $obj) {
		echo "<a href='" . _HOST . "collection/{$obj->collectionId}'>{$obj->name}</a><br/>";
	}
?>
