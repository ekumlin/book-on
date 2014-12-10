<form class="login-form" method="POST" action="<?php echo _HOST; ?>login">
	<div class="input-group">
		<input type="text" name="cardNumber" id="login-cardNumber" placeholder="Card number" pattern="^[0-9]+$" maxlength="9" required />
		<div class="input-status input-error"></div>
		<div class="input-status input-dependent" data-dependence="login-cardNumber" data-attribute="length"></div>
	</div>
	<div class="input-group">
		<input type="password" name="password" id="login-password" placeholder="Password" required />
		<div class="input-status input-error"></div>
		<div class="input-status input-dependent" data-dependence="login-password" data-attribute="length"></div>
	</div>
	<div class="form-controls">
		<input type="button" name="cancel" value="Register" class="button-flat" />
		<input type="submit" name="submit" value="Log in" class="button-flat" />
	</div>
</form>