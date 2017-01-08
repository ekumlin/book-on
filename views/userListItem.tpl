<div class="list-item user-list-item">
	<div class="cardNumber"><?php echo StringUtil::formatIsbn($viewBag['user']->cardNumber); ?></div>
	<div class="name"><?php echo $viewBag['user']->name; ?></div>
	<div class="email"><?php echo $viewBag['user']->email; ?></div>
	<div class="userType"><?php echo $viewBag['user']->getUserType(); ?></div>
	<div class="accountStatus"><?php echo $viewBag['user']->getAccountStatus(); ?></div>
	<div class="actions"><a href="#" class="button-flat action-negative resetpassword" data-user="<?php echo $viewBag['user']->cardNumber; ?>">Reset Password</a></div>
</div>
