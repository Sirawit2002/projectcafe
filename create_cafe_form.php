<?php $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); ?>
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
