<form action="" method="post" class="form-horizontal">
	<div class="form-group">
		<h3 class="signin-title text-center">Login User</h3>
	</div>
	<?php echo $alert ?>
	<div class="form-group">
		<input type="text" name="user_login" id="user-login" class="form-control input-lg text-center" placeholder="User Login" required />
		<input type="password" name="user_pass" id="user-pass" class="form-control input-lg text-center" placeholder="Password" required />
	</div>
	<div class="form-group">
		<button class="btn btn-warning btn-block btn-lg" name="submit" type="submit" value="in">Sign In</button>
		<p class="text-center text-warning">or <a href="<?php echo site_url() ?>">Cancel</a></p>
	</div>
</form>