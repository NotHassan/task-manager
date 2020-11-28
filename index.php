<?php
	define('DB_SERVER', 'localhost');
	define('DB_USERNAME', 'root');
	define('DB_PASSWORD', '');
	define('DB_NAME', 'project');

	session_start();
	$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

	// User is logging out
	if(isset($_GET['logout'])) {
		$_SESSION = array();
		header('Location: .');
	}

	// For currently logged in users
	if(isset($_SESSION['active'])) {
		if(isset($_POST['task-request'])) {

			// User is adding a task
			if(isset($_POST['add'])) {
				$stmt = $conn->prepare('INSERT INTO tasks (name, description) VALUES (?, ?)');
				$stmt->bind_param('ss', $_POST['name'], $_POST['description']);

			// User is updating a task
			} elseif(isset($_POST['update'])) {
				$stmt = $conn->prepare('UPDATE tasks SET name=?, description=? WHERE id=?');
				$stmt->bind_param('ssi', $_POST['name'], $_POST['description'], $_POST['id']);
			
			// User is deleting a task
			} elseif(isset($_POST['delete'])) {
				$stmt = $conn->prepare('DELETE FROM tasks WHERE id=?');
				$stmt->bind_param('i', $_POST['id']);

			// Shouldn't ever be executed but is here so that the stmt->execute and stmt->close lines below don't cause errors
			} else {
				exit(false);
			}
	
			$stmt->execute();
			$stmt->close();
		}
	// For not currently logged in users
	} else {
		if(isset($_POST['user-request'])) {

			// User is trying to log in
			if(isset($_POST['login'])) {
				$stmt = $conn->prepare('SELECT id, name, username, password FROM users WHERE username=?');
				$stmt->bind_param('s', $_POST['username']);
				$stmt->execute();
				$stmt->store_result();
				
				// User is found
				if($stmt->num_rows) {
					$stmt->bind_result($id, $name, $username, $password);
					$stmt->fetch();
					
					// Password is correct
					if(md5($_POST['password']) == $password) {
						$_SESSION['active'] = true;
						$_SESSION['id'] = $id;
						$_SESSION['name'] = htmlspecialchars($name);
						$_SESSION['username'] = htmlspecialchars($username);

					// Password is incorrect
					} else {

					}
				// User can't be found
				} else {

				}
			// User is trying to register
			} elseif(isset($_POST['register'])) {
				$_POST['password'] = md5($_POST['password']);
				$_POST['password2'] = md5($_POST['password2']);

				// The password fields do match
				if($_POST['password'] == $_POST['password2']) {
					$stmt = $conn->prepare('INSERT INTO users (name, username, password) VALUES (?, ?, ?)');
					$stmt->bind_param('sss', $_POST['name'], $_POST['username'], $_POST['password']);
					
					// Add user
					if($stmt->execute()) {
						$stmt = $conn->prepare('SELECT id, name, username FROM users WHERE name=? AND username=? AND password=?');
						$stmt->bind_param('sss', $_POST['name'], $_POST['username'], $_POST['password']);
						$stmt->execute();
						$stmt->bind_result($id, $name, $username);
						$stmt->fetch();
						$stmt->close();
						
						// Login the user into their account after registering
						$_SESSION['active'] = true;
						$_SESSION['id'] = $id;
						$_SESSION['name'] = htmlspecialchars($name);
						$_SESSION['username'] = htmlspecialchars($username);

					// User already exists
					} else {

					}

				// The password fields do not match
				} else {

				}
			}
		}
	}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Project</title>
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
		<meta name="viewport" content="width=device-width" />
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<!-- jQuery DataTables -->
		<link rel="stylesheet" href="//cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
		<!-- Cuistom CSS -->
		<style>
			.clicker:hover {
				cursor: pointer;
			}
		</style>
		<!-- Font Awesome -->
		<script src="https://kit.fontawesome.com/e04ac5c7b6.js" crossorigin="anonymous"></script>
	</head>
	<body>
		<nav class="navbar navbar-expand-lg bg-dark navbar-dark">
			<a class="navbar-brand" href="/Project">Project</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarToggler">
				<ul class="navbar-nav ml-auto">
					<?php
						if(isset($_SESSION['active'])) {
					?>
					<li class="navbar-item">
						<a class="nav-link">Welcome, <?php echo $_SESSION['name']; ?></a>
					</li>
					<li class="navbar-item">
						<a class="nav-link clicker" href="./?logout">Logout</a>
					</li>
					<?php
						} else {
					?>
					<li class="navbar-item">
						<a class="nav-link clicker" data-toggle="modal" data-target="#login-modal">Login</a>
					</li>
					<li class="navbar-item">
						<a class="nav-link clicker" data-toggle="modal" data-target="#register-modal">Register</a>
					</li>
					<?php
						}
					?>
				</ul>
			</div>
		</nav>
		<div class="container" style="visibility: hidden;">
			<div class="row">
				<div class="col-md-12 bodycontent pt-4">
					<?php
						if(isset($_SESSION['active'])) {
					?>
					<table class="table table-striped table-bordered table-responsive" id="tasks">
						<thead>
							<tr>
								<th colspan="3" class="text-center align-middle h3">Tasks</th>
								<th class="text-center"><button class="btn btn-primary" data-toggle="modal" data-target="#add-task-modal">Add Task</button></th>
							</tr>
							<tr>
								<th class="text-center">ID</th>
								<th class="text-center">Name</th>
								<th class="text-center">Description</th>
								<th class="text-center">Actions</th>
							</tr>
						</thead>
						<tbody><?php
								$stmt = $conn->prepare('SELECT * FROM tasks');
								$stmt->execute();
								$stmt->bind_result($id, $name, $description);

								while ($stmt->fetch()) {

									// Weird PHP formatting to ensure the HTML is formatted correctly when echo'd
									echo <<<END

							<tr>
								<td class="text-center">$id</td>
								<td>$name</td>
								<td>$description</td>
								<td class="text-center">
									<i class="fas fa-edit clicker" data-toggle="modal" data-target="#edit-task-modal" title="Edit"></i>
									/
									<i class="fas fa-trash clicker" data-toggle="modal" data-target="#delete-task-modal" title="Delete"></i>
								</td>
							</tr>
END;
								}

								$stmt->close();
							?>

						</tbody>
					</table>
					<?php
						} else {
							echo "<p class='text-center h1 mt-4 pt-4'>Please login to use this tool.</p>";
						}
					?>
				</div>
			</div>
		</div>
		<div class="modal fade" id="register-modal" tabindex="-1" role="dialog" aria-labelledby="registerModalTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<form method="POST">
						<div class="modal-header">
							<h5 class="modal-title">Register</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="register-name" class="col-form-label">Name:</label>
								<input type="text" class="form-control" id="register-name" name="name">
							</div>
							<div class="form-group">
								<label for="register-username" class="col-form-label">Username:</label>
								<input type="text" class="form-control" id="register-username" name="username">
							</div>
							<div class="form-group">
								<label for="register-password" class="col-form-label">Password:</label>
								<input type="password" class="form-control" id="register-password" name="password">
							</div>
							<div class="form-group">
								<label for="register-password-confirm" class="col-form-label">Confirm Password:</label>
								<input type="password" class="form-control" id="register-password-confirm" name="password2">
							</div>
						</div>
						<div class="modal-footer">
							<input type="hidden" name="user-request">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary" name="register">Sign Up</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="loginModalTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<form method="POST">
						<div class="modal-header">
							<h5 class="modal-title">Login</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="login-username" class="col-form-label">Username:</label>
								<input type="text" class="form-control" id="login-username" name="username">
							</div>
							<div class="form-group">
								<label for="login-password" class="col-form-label">Password:</label>
								<input type="password" class="form-control" id="login-password" name="password">
							</div>
						</div>
						<div class="modal-footer">
							<input type="hidden" name="user-request">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary" name="login">Login</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="modal fade" id="add-task-modal" tabindex="-1" role="dialog" aria-labelledby="addTaskTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<form method="POST">
						<div class="modal-header">
							<h5 class="modal-title">Add Task</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="add-name" class="col-form-label">Name:</label>
								<input type="text" class="form-control" id="add-name" name="name" max="50">
							</div>
							<div class="form-group">
								<label for="add-description" class="col-form-label">Description:</label>
								<textarea class="form-control" id="add-description" name="description" rows="3" maxlength="200"></textarea>
							</div>
						</div>
						<div class="modal-footer">
							<input type="hidden" name="task-request">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary" name="add">Add Task</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="modal fade" id="edit-task-modal" tabindex="-1" role="dialog" aria-labelledby="editTaskTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<form method="POST">
						<div class="modal-header">
							<h5 class="modal-title">Edit Task</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="edit-username" class="col-form-label">ID:</label>
								<input type="text" class="form-control" id="edit-id" name="id" readonly>
							</div>
							<div class="form-group">
								<label for="edit-name" class="col-form-label">Name:</label>
								<input type="text" class="form-control" id="edit-name" name="name" maxlength="50">
							</div>
							<div class="form-group">
								<label for="edit-description" class="col-form-label">Description:</label>
								<textarea class="form-control" id="edit-description" name="description" rows="3" maxlength="200"></textarea>
							</div>
						</div>
						<div class="modal-footer">
							<input type="hidden" name="task-request">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary" name="update">Save</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="modal fade" id="delete-task-modal" tabindex="-1" role="dialog" aria-labelledby="deleteTaskTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<form method="POST">
						<div class="modal-header">
							<h5 class="modal-title">Delete Task</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<p>Are you sure you want to <strong style="color: red">delete</strong> the following task?</p>
							<div class="form-group">
								<label for="delete-id" class="col-form-label">ID:</label>
								<input type="text" class="form-control" id="delete-id" name="id" readonly>
							</div>
							<div class="form-group">
								<label for="delete-name" class="col-form-label">Name:</label>
								<input type="text" class="form-control" id="delete-name" name="name" readonly>
							</div>
							<div class="form-group">
								<label for="delete-description" class="col-form-label">Description:</label>
								<textarea class="form-control" id="delete-description" name="description" rows="3" maxlength="200" readonly></textarea>
							</div>
						</div>
						<div class="modal-footer">
							<input type="hidden" name="task-request">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary" name="delete">Delete</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
		<!-- jQuery DataTables -->
		<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
		<!-- Custom JavaScript -->
		<script src="./js/script.js"></script>
	</body>
</html>