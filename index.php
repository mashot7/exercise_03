<?php
if (!file_exists('./directory')) {
	mkdir('./directory', 0777, true);
}
if (isset($_GET['dir_path'])) {
	$dir_path = $_GET['dir_path'];
} else {
	$dir_path = 'directory';
}
$breadcrumbs = explode('/', $dir_path);
if (isset($_GET['sort_by'])) {
	$sort['name']  = $_GET['sort_by'];
} else {
	$sort['name']  = 'extension';
}
if (isset($_GET['sort_flag'])) {
	$sort['flag'] = (int)$_GET['sort_flag'];
} else {
	$sort['flag'] = 4;
}
require_once('include/filesystem.php');
require_once('include/pagination.php');

$data['pagination_url'] = './index.php';
$files = new Filesystem($dir_path, $data['pagination_url'], $sort);
$directoryArray = $files->getDirectoryArray()[0];
$arraySearch = $files->getDirectoryArray()[1];
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
	      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Document</title>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="stylesheet" href="css/materialize.css">
</head>
<body>
<div class="container mt4">
	<button type="button" name="create_folder" id="create_folder"
	        class="waves-effect amber darken-1 waves-light btn modal-trigger">Create Folder
	</button>
	<div class="row">
		<form method="post" action="include/action.php" class="col s8" enctype="multipart/form-data">
			<div class="row">
				<div class="file-field input-field s4">
					<div class="waves-effect darken-1 teal waves-light btn">
						<span>Browse</span>
						<input type="file" name="file"/>
					</div>
					<div class="file-path-wrapper s2">
						<label>
							<input class="file-path validate" type="text" placeholder="Upload file"/>
							<input type="hidden" name="dir_path" value="<?= $dir_path ?>">
						</label>
					</div>
					<div class="right">
						<label>
							<input class="waves-effect darken-1 teal waves-light btn" type="submit" value="submit">
						</label>
					</div>
				</div>
		</form>
	</div>
</div>

	<nav>
		<div class="nav-wrapper blue row">
			<div class="col s12">
				<?php
				for ($i = 0; $i < count($breadcrumbs); $i++) {
					$href = $breadcrumbs[0];
					for ($j = 0; $j < $i; $j++)
						$href .= '/' . $breadcrumbs[$j + 1];
					echo '<a href="' . $data['pagination_url'] . '?page=1&dir_path=' . $href . '&sort_flag=' . $sort['flag'] . '&sort_by=' . $sort['name'] . '" class="breadcrumb">' . ucfirst($breadcrumbs[$i]) . '</a>';
				}
				?>
			</div>
		</div>
	</nav>
	<?php
	$row_count = $files->row_count();
	if ($row_count) {
		$data['total_records'] = $row_count;
		$data['records_per_page'] = 5;
		$page = new Pagination($dir_path, $sort);
		$start_record = $page->start_record($data);
		$record_per_page = $data['records_per_page'];
		$files->display_page($start_record, $record_per_page);
		$page->pagination_display($data);
	} else {
		echo '<h5 class="center">No data available...</h5>';
	}
	?>
<script src="https://code.jquery.com/jquery-2.2.4.js"></script>
<script src="js/materialize.js"></script>
<script src="js/main.js"></script>

</body>
</html>

<div id="folderModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><span id="change_title">Create Folder</span></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="input-field col s12">
						<label for="folder_name">Folder name</label><input name="folder_name" type="text" id="folder_name"
						                                                   class="validate" placeholder="">
						<input type="hidden" name="action" id="action">
						<input type="hidden" name="old_name" id="old_name">
						<input type="hidden" name="path_name" id="path_name" value="<?= $dir_path ?>">
						<input type="button" value="Create" name="folder_button" id="folder_button" class="btn btn-info">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" class="modal-close waves-effect waves-green btn-flat">Close</a>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function () {
		$('#folderModal').modal();
	});
</script>
