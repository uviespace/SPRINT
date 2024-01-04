<h3>Login</h3>

<?php if (isset($error_msg)) : ?>
	<div class="error-msg"><?=$error_msg?></div>
<?php endif; ?>

<form action="?login=1" method="post">
	<label for="email">E-Mail:</label>
	<input id="email" type="email" size="40" maxlength="250" name="email" class="form-input">

	<label for="pass">Password:</label>
	<input id="pass" type="password" size="40" maxlength="250" name="password" class="form-input">

	<input type="submit" value="Login" class="btn-submit">
</form>
