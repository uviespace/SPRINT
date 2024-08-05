<h3>Register</h3>



<form method="post" action="register.php">
	<?php foreach($error as $e): ?>
		<div class="alert alert-error"><?=$e?></div>
	<?php endforeach; ?>
	
	<label for="email">E-Mail:</label>
	<input id="email" type="email" size="40" maxlength="250" name="email" class="form-input" />

	<label for="name">Name:</label>
	<input id="name" type="text" size="40" maxlength="250" name="name" class="form-input" />

	<label for="pass_1">Password:</label>
	<input id="pass_1" type="password" size="40" maxlength="250" name="pass_1" class="form-input" />

	<label for="pass_2">Retype password:</label>
	<input id="pass_2" type="password" size="40" maxlength="250" name="pass_2" class="form-input" />

	<label for="code">Sign-up Code:</label>
	<input id="code" type="text" size="40" maxlength="250" name="code" class="form-input" />

	<input type="submit" value="Register" name="register" class="btn-submit" />


	<?php if($sign_up_success): ?>
		<div class="alert alert-success">You have been successfully registered. <a href="login.php">To the login</a></div>
	<?php endif; ?>
</form>
