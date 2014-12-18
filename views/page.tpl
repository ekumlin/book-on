<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title><?php echo (isset($viewBag['title']) && $viewBag['title']) ? "{$viewBag['title']} :: " : ''; ?>Book-On</title>
		<link href="<?php echo _HOST; ?>favicon.ico" rel="shortcut icon">
		<link href="<?php echo _HOST; ?>favicon.ico" rel="icon">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<?php
			$styles = array_merge(array('base'), $viewBag['styles']);
			foreach ($styles as $style) {
				echo '<link href="' . _HOST . 'styles/' . $style . '.css" rel="stylesheet" type="text/css"/>';
			}
		?>
	</head>
	<body>
		<?php View::render('header', array('searchTarget' => $viewBag['searchTarget'])); ?>
		<div id="curtain" style="display: none"></div>
		<?php View::render('drawer'); ?>
		<div class="content">
			<div class="container">
				<?php echo $viewBag['body']; ?>
			</div>
		</div>
		<?php
			$scripts = array_merge(array('jquery-1.11.1.min', 'base'), $viewBag['scripts']);
			foreach ($scripts as $script) {
				echo '<script type="text/javascript" src="' . _HOST . 'scripts/' . $script . '.js"></script>';
			}
		?>
		<input type="hidden" name="php-host" value="<?php echo _HOST; ?>" />
		<?php View::render('popup', array('class' => 'collect', 'content' => '')); ?>
	</body>
</html>
