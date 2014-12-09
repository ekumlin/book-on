<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html>
	<head>
		<title><?php echo $viewBag['title']; ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<?php
			$styles = array_merge(array('base'), $viewBag['styles']);
			foreach ($styles as $style) {
				echo '<link href="' . _HOST . 'styles/' . $style . '.css" rel="stylesheet" type="text/css"/>';
			}
		?>
	</head>
	<body>
		<?php View::render('header'); ?>
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
	</body>
</html>
