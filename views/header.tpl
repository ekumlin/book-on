<div class="header">
	<span id="hamburger"><img src="<?php echo _HOST; ?>assets/hamburger.png"/></span>
	<a href="<?php echo _HOST; ?>" id="logo">Book-On</a>
	<input type="search" id="header-search" placeholder="Search" />
	<div class="login"><?php if (isset($_SESSION['User'])): ?>
		<span class="username"><?php echo $_SESSION['User']->name; ?></span>
		<a href="<?php echo _HOST; ?>logout" class="button-flat logout">Log out</a>
	<?php else: ?>
		<a href="<?php echo _HOST; ?>login" class="button-flat login">Log in or register</a>
	<?php endif; ?></div>
</div>
