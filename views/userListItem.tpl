<?php
	$userUrl = _HOST . 'users/' . $viewBag['user']->cardNumber;
?><div class="list-item user-list-item">
	<div class="cardNumber"><a href="<?php echo $userUrl; ?>"><?php echo String::formatIsbn($viewBag['user']->cardNumber); ?></a></div>
	<div class="name"><a href="<?php echo $userUrl; ?>"><?php echo $viewBag['user']->name; ?></a></div>
	<div class="email"><?php echo $viewBag['user']->email; ?></div>
	<div class="userType"><?php echo $viewBag['user']->getUserType(); ?></div>
	<div class="accountStatus"><?php echo $viewBag['user']->getAccountStatus(); ?></div>
</div>
