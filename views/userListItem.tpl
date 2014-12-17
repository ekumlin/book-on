<div class="list-item user-list-item">
	<div class="cardNumber"><?php echo String::formatIsbn($viewBag['user']->cardNumber); ?></div>
	<div class="name"><?php echo $viewBag['user']->name; ?></div>
	<div class="email"><?php echo $viewBag['user']->email; ?></div>
	<div class="userType"><?php echo $viewBag['user']->getUserType(); ?></div>
	<div class="accountStatus"><?php echo $viewBag['user']->getAccountStatus(); ?></div>
</div>
