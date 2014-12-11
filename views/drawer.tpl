<div id="drawer" class="drawer">
	<table>
		<tr class="item">
			<td><img src="<?php echo _HOST; ?>assets/icon-book.png" title="Books"/></td>
			<td><a href='<?php echo _HOST; ?>'>Book index</a></td>
		</tr>
		<tr class="item">
			<td><img src="<?php echo _HOST; ?>assets/icon-collect.png" title="Collections"/></td>
			<td><a href='<?php echo _HOST; ?>collections'>Collections</a></td>
		</tr>
	</table>
	<?php if (isset($_SESSION['User']) && $_SESSION['User']->employeeLevel >= User::USER_STAFF): ?>
	<div class="section">
		<div class="title">Administration</div>
		<table>
			<tr class="item">
				<td><img src="<?php echo _HOST; ?>assets/icon-book-list.png" title="List all books"/></td>
				<td><a href='<?php echo _HOST; ?>books/list'>View inventory</a></td>
			</tr>
			<tr class="item">
				<td><img src="<?php echo _HOST; ?>assets/icon-book-add.png" title="Add book"/></td>
				<td><a href='<?php echo _HOST; ?>book/add'>Add book</a></td>
			</tr>
			<tr class="item">
				<td><img src="<?php echo _HOST; ?>assets/icon-user-list.png" title="List all users"/></td>
				<td><a href='<?php echo _HOST; ?>users/list'>List users</a></td>
			</tr>
			<tr class="item">
				<td><img src="<?php echo _HOST; ?>assets/icon-user-add.png" title="Add new user"/></td>
				<td><a href='<?php echo _HOST; ?>user/add'>New user</a></td>
			</tr>
		</table>
	</div>
	<?php endif; ?>
</div>
