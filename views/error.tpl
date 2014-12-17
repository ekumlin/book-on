<?php View::render('notice', array(
		'class' => 'error',
		'title' => 'We\'re so sorry!',
		'message' => $viewBag['error'],
	)); ?>
