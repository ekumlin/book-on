<form class="login-form material-form" method="POST" action="<?php echo _HOST; ?>login">
	<div class="input-group">
		<input type="number" name="cardNumber" id="login-cardNumber" placeholder="Card number" pattern="^[0-9]+$" maxlength="9" required />
		<div class="input-status input-error"></div>
		<div class="input-status input-dependent" data-dependence="login-cardNumber" data-attribute="length"></div>
	</div>
	<div class="input-group">
		<input type="password" name="password" id="login-password" placeholder="Password" required />
		<div class="input-status input-error"></div>
		<div class="input-status input-dependent" data-dependence="login-password" data-attribute="length"></div>
	</div>
	<div class="form-controls">
		<a href="<?php echo _HOST; ?>register" class="button-flat action-creative">Register</a>
		<input type="submit" value="Log in" class="button-flat action-primary" />
	</div>
</form>