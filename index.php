<?php
session_start();

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function isAdmin() {
    if (isset($_SESSION['role'])) {
        return $_SESSION['role'] == 1 || strtolower($_SESSION['role']) === 'admin';
    }
    if (isset($_SESSION['user_role'])) {
        return $_SESSION['user_role'] == 1 || strtolower($_SESSION['user_role']) === 'admin';
    }
    return false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<style>
		.menu-tray {
			position: fixed;
			top: 16px;
			right: 16px;
			background: rgba(255,255,255,0.95);
			border: 1px solid #e6e6e6;
			border-radius: 8px;
			padding: 6px 10px;
			box-shadow: 0 4px 10px rgba(0,0,0,0.06);
			z-index: 1000;
		}
		.menu-tray a { margin-left: 8px; }
		
		.btn-logout {
			background-color: #dc3545;
			border-color: #dc3545;
			color: white;
		}
		
		.btn-logout:hover {
			background-color: #c82333;
			border-color: #bd2130;
			color: white;
		}
		
		.btn-admin {
			background-color: #28a745;
			border-color: #28a745;
			color: white;
		}
		
		.btn-admin:hover {
			background-color: #218838;
			border-color: #1e7e34;
			color: white;
		}
	</style>
</head>
<body>

	<div class="menu-tray">
		<span class="me-2">Menu:</span>
		
		<?php if (!isLoggedIn()): ?>
			<!-- Not logged in: Show Register and Login -->
			<a href="login/register.php" class="btn btn-sm btn-outline-primary">Register</a>
			<a href="login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
			
		<?php elseif (isLoggedIn() && isAdmin()): ?>
			<!-- Logged in as admin: Show Logout and Category -->
			<a href="actions/logout_user_action.php" class="btn btn-sm btn-logout">Logout</a>
			<a href="admin/category.php" class="btn btn-sm btn-admin">Category</a>
			
		<?php elseif (isLoggedIn() && !isAdmin()): ?>
			<!-- Logged in as regular user: Show only Logout -->
			<a href="actions/logout_user_action.php" class="btn btn-sm btn-logout">Logout</a>
			
		<?php endif; ?>
	</div>

	<div class="container" style="padding-top:120px;">
		<div class="text-center">
			<?php if (isLoggedIn()): ?>
				<h1>Welcome<?php echo isset($_SESSION['name']) ? ', ' . htmlspecialchars($_SESSION['name']) : ''; ?>!</h1>
				<p class="text-muted">
					<?php if (isAdmin()): ?>
						You are logged in as an administrator. Use the Category button to manage categories.
					<?php else: ?>
						You are logged in as a customer. Enjoy browsing!
					<?php endif; ?>
				</p>
			<?php else: ?>
				<h1>Welcome</h1>
				<p class="text-muted">Use the menu in the top-right to Register or Login.</p>
			<?php endif; ?>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
