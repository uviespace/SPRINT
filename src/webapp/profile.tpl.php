<h2>Hello <?=$user[0]["name"]?></h2>


<form method="POST">
	<div style="max-width: 600px;">
		<label for="user_name" class="settings-label-standard">Name:</label>
		<input class="form-input modal-input" name="user_name" type="text" value="<?=$user[0]["name"]?>" />

		<label for="user_email" class="settings-label-standard">E-Mail:</label>
		<input class="form-input modal-input" name="user_email" type="text" value="<?=$user[0]["email"]?>" />
	</div>

	<button type="submit" class="btn" name="update_profile" value="update_profile">Update</button>
</form>


<div style="margin-top: 64px;"></div>
<h3>Reset Password</h3>


<form method="POST">
	<div style="max-width: 600px">
		<label for="pass_old" class="settings-label-standard">Old Password:</label>
		<input class="form-input modal-input" name="pass_old" type="password" />

		<label for="pass_first" class="settings-label-standard">New Password:</label>
		<input class="form-input modal-input" name="pass_first" type="password" />

		<label for="pass_second" class="settings-label-standard">New Password again:</label>
		<input class="form-input modal-input" name="pass_second" type="password" />

		<?php if ($pass_errors["new_pass_old_pass_same"]): ?>
			<div class="alert alert-error">New password is the same as old password</div>
		<?php endif; ?>

		<?php if ($pass_errors["pass_confirm_wrong"]): ?>
			<div class="alert alert-error">Passwords are not the same</div>
		<?php endif; ?>

		<?php if ($pass_errors["old_pass_incorrect"]): ?>
			<div class="alert alert-error">Old password is incorrect</div>
		<?php endif; ?>

		<?php if ($pass_errors["pass_updated"]): ?>
			<div class="alert alert-success">Password successfully udpated</div>
		<?php endif; ?>

		<button type="submit" class="btn" name="update_password" value="update_password">Update Password</button>
	</div>
</form>
