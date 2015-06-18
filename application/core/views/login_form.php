<form action="" method="post" class="form-horizontal">
	<div class="form-group">
		<h3 class="signin-title text-center">Login User</h3>
	</div>
	<?php echo $alert ?>
	<div class="form-group">
		<div class="input-group">
			<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
			<input type="text" name="user_login" id="user-login" class="form-control" placeholder="Your Username Here" required />
		</div>
	</div>
	<div class="form-group">
		<div class="input-group">
			<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
			<input type="password" name="user_pass" id="user-pass" class="form-control" placeholder="Type Your Password" required />
		</div>
	</div>
	<div class="form-group">
		<div class="checkbox">
			<label>
				<input type="checkbox" name="remember_me" id="rememeber-me"> Remember Me
			</label>
		</div>
	</div>
	<div class="form-group">
		<button class="btn btn-primary btn-block" name="submit" type="submit" value="in">Sign In</button>
	</div>
</form>