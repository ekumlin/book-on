<table class="header">
	<tr>
		<td class="left">
			<span id="hamburger"><img src="<?php echo _HOST; ?>assets/hamburger.png"/></span>
			<a href="<?php echo _HOST; ?>" id="logo">Book-On</a>
		</td>
		<td class="middle">
			<form action="<?php echo _HOST . $viewBag['searchTarget']; ?>" method="GET">
				<input type="search" id="header-search" name="q" value="<?php echo isset($_GET['q']) ? trim($_GET['q']) : ''; ?>" placeholder="Search all books" />
			</form>
		</td>
		<td class="right">
			<div class="login"><?php if (isset($_SESSION['User'])): ?>
				<span class="username"><?php echo $_SESSION['User']->name; ?></span>
				<a href="<?php echo _HOST; ?>logout" class="button-flat logout">Log out</a>
			<?php else: ?>
				<a href="<?php echo _HOST; ?>login" class="button-flat login">Log in or register</a>
			<?php endif; ?></div>
		</td>
	</tr>
</table>
