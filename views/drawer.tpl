<div id="drawer" class="drawer">
	<table>
		<tr class="item">
			<td><img src="<?php echo _HOST; ?>assets/icon-book.png" title="Books"/></td>
			<td><a href="<?php echo _HOST; ?>">Book index</a></td>
		</tr>
		<tr class="item mobile-only">
			<td><img src="<?php echo _HOST; ?>assets/icon-search.png" title="Search books"/></td>
			<td><a href="#" class="searchbox-open">Search</a></td>
		</tr>
	</table>
	<?php if (isset($_SESSION["User"])): ?>
	<div class="section">
		<div class="title">My account</div>
		<table>
			<tr class="item">
				<td><img src="<?php echo _HOST; ?>assets/icon-book-held.png" title="View my held books"/></td>
				<td><a href="<?php echo _HOST; ?>books/held">Held books</a></td>
			</tr>
			<tr class="item">
				<td><img src="<?php echo _HOST; ?>assets/icon-collect.png" title="Collections"/></td>
				<td><a href="<?php echo _HOST; ?>collections">Collections</a></td>
			</tr>
		</table>
	</div>
	<div class="section desktop-only">
		<div class="title">Rentals</div>
		<table>
			<tr class="item">
				<td><img src="<?php echo _HOST; ?>assets/icon-book-checkin.png" title="Check books in"/></td>
				<td><a href="<?php echo _HOST; ?>books/checkin">Check books in</a></td>
			</tr>
			<tr class="item">
				<td><img src="<?php echo _HOST; ?>assets/icon-book-checkout.png" title="Check books out"/></td>
				<td><a href="<?php echo _HOST; ?>books/checkout">Check books out</a></td>
			</tr>
			<?php if (Http::canAccess(User::USER_STAFF)): ?>
			<tr class="item">
				<td><img src="<?php echo _HOST; ?>assets/icon-buy.png" title="Sell books"/></td>
				<td><a href="<?php echo _HOST; ?>books/sell">Sell books</a></td>
			</tr>
			<?php endif; ?>
		</table>
	</div>
	<?php if (Http::canAccess(User::USER_STAFF)): ?>
	<div class="section">
		<div class="title">Administration</div>
		<table>
			<tr class="item desktop-only">
				<td><img src="<?php echo _HOST; ?>assets/icon-book-add.png" title="Add book"/></td>
				<td><a href="<?php echo _HOST; ?>books/add">Add new book</a></td>
			</tr>
			<tr class="item">
				<td><img src="<?php echo _HOST; ?>assets/icon-book-list.png" title="List all books"/></td>
				<td><a href="<?php echo _HOST; ?>books/list">View book list</a></td>
			</tr>
			<tr class="item desktop-only">
				<td><img src="<?php echo _HOST; ?>assets/icon-book-add-copy.png" title="Add copy of book"/></td>
				<td><a href="<?php echo _HOST; ?>books/addcopy">Add to inventory</a></td>
			</tr>
			<tr class="item">
				<td><img src="<?php echo _HOST; ?>assets/icon-user-list.png" title="List all users"/></td>
				<td><a href="<?php echo _HOST; ?>users/list">List users</a></td>
			</tr>
		</table>
	</div>
	<?php endif; ?>
	<?php endif; ?>
</div>
