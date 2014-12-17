<form class="login-form material-form" method="POST" action="<?php echo _HOST; ?>register" novalidate>
	<div class="input-group">
		<input type="text" name="name" id="login-name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" pattern=".+ .+" placeholder="First and last name" required />
		<div class="input-status input-error"></div>
	</div>
	<div class="input-group">
		<input type="email" name="email" id="login-email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" pattern=".+@.+" placeholder="Email address" required />
		<div class="input-status input-error"></div>
	</div>
	<div class="input-group">
		<input type="password" name="password" id="login-password" placeholder="Password" required />
		<div class="input-status input-error"></div>
	</div>
	<div class="input-group">
		<input type="password" name="passwordConfirm" id="login-passwordConfirm" placeholder="Confirm password" required />
		<div class="input-status input-error"></div>
	</div>
	<div class="form-controls">
		<input type="submit" value="Register" class="button-flat action-primary" />
	</div>
</form>
