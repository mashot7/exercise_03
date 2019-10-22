<?php

// ---------------------------- CREATE ----------------------------
if (isset($_POST['action'])) {
	if (isset($_GET['dir_path'])) {
		$dir_path = $_GET['dir_path'];
	} else {
		$dir_path = 'directory/';
	}
//	CREATE FOLDER
	if ($_POST['action'] == 'create') {
		$path = '../'.$_POST['path_name'] . '/' . $_POST['folder_name'];
		if (!file_exists($path)) {
			mkdir($path, 0777, true);
			echo 'Folder Created.';
		} else {
			echo 'Folder Already Created.';
		}
	}
}

// ---------------------------- UPLOAD ----------------------------
if (isset($_FILES['file'])) {
	if (isset($_POST['dir_path']) && is_dir('../' . $_POST['dir_path'])) {
		$dir_path = $_POST['dir_path'];
	} else {
		$dir_path = 'directory';
	}
	print_r($_POST);
	require_once('filesystem.php');
	$sort['name'] = 'name';
	$sort['flag'] = 4;
	$files = new Filesystem('../' .$dir_path, './include/action.php', $sort);
	$arraySearch = $files->getDirectoryArray()[1];

	if (!file_exists($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
		header('location: ../index.php?dir_path=' . $dir_path);
		exit();
	}

	$arrayOne = [];
	$k = 0;
	$file = $_FILES['file'];
	$arrayOne['fileName'] = $file['name'];
	$fileExt = explode('.', $arrayOne['fileName']);
	$arrayOne['fileActualExt'] = strtolower(end($fileExt));
	$increment = '';
	$pos = strpos($arrayOne['fileName'], '.' . $arrayOne['fileActualExt']);
	$arrayOne['name'] = substr($arrayOne['fileName'], 0, $pos);
	while(file_exists('../' . $dir_path . '/' . $arrayOne['name'] . $increment . '.' . $arrayOne['fileActualExt']) || file_exists('../' . $dir_path . '/' . $arrayOne['name'] . '(' . $increment . ').' . $arrayOne['fileActualExt'])) {
		$increment++;
	}
	if ($increment != '') {
		$arrayOne['fileName'] = $arrayOne['name'] . '(' . $increment . ').' . $arrayOne['fileActualExt'];
	} else {
		$arrayOne['fileName'] = $arrayOne['name'] . $increment . '.' . $arrayOne['fileActualExt'];
	}
	$arrayOne['path'] = '../' . $dir_path . '/' . $arrayOne['fileName'];
	// end of RENAME
	$pos = strpos($arrayOne['fileName'], '.' . $arrayOne['fileActualExt']);
	$arrayOne['name'] = substr($arrayOne['fileName'], 0, $pos);
	$arrayOne['tmpName'] = $file['tmp_name'];
	$arrayOne['fileSize'] = $file['size'];
	$arrayOne['fileError'] = $file['error'];

	if ($arrayOne['fileError'] === 0) {
		move_uploaded_file($arrayOne['tmpName'], $arrayOne['path']);
		header('Location: ../index.php?dir_path=' . $dir_path);
		exit();
	} else {
		echo "There was an error uploading your file!";
		exit();
	}
}

// ---------------------------- SORTING ----------------------------
if (isset($_GET['cmd'])) {
	$sort['name'] = $_GET['sort_by'];
	if ($_GET['cmd'] == 'sorting'){
		$sort['flag'] = $_GET['sort_flag'] == 3 ? 4 : 3;
		$dir_path = $_GET['dir_path'];
		if (is_dir('../' . $dir_path)) {
			header('location: ../index.php?dir_path=' . $dir_path . '&sort_by=' . $sort['name'] . '&sort_flag=' . $sort['flag']);
			exit();
		} else {
			header('location: ../index.php');
			exit();
		}
// ---------------------------- DELETE ----------------------------
	} elseif ($_GET['cmd'] == 'delete') {
		$sort['flag'] = $_GET['sort_flag'];
		$dir_path = $_GET['dir_path'];
		$filename = $_POST['fileName'];
		if (is_dir('../' . $filename)) {
			rmdir('../' . $filename);
			header('location: ../index.php?dir_path=' . $dir_path . '&sort_by=' . $sort['name'] . '&sort_flag=' . $sort['flag']);
			exit();
		} else {
			unlink('../' . $filename);
			header('location: ../index.php?dir_path=' . $dir_path . '&sort_by=' . $sort['name'] . '&sort_flag=' . $sort['flag']);
			exit();
		}
// ---------------------------- UPDATE ----------------------------
	} elseif ($_GET['cmd'] == 'update') {
		$old_name = $_POST['old_name'];
		$new_name = $_POST['new_name'];
		$dir_path = $_GET['dir_path'];
		if ($old_name == $new_name) {
			header('location: ../index.php?dir_path=' . $dir_path . '&sort_by=' . $sort['name'] . '&sort_flag=' . $sort['flag']);
			exit();
		}
		$extension = $_POST['extension'];
		if ($extension == 'folder') {
			$increment = '';
			while(is_dir('../' . $dir_path . '/' . $new_name . $increment) || is_dir('../' . $dir_path . '/' . $new_name . '(' . $increment . ')')) {
				$increment++;
			}
			if ($increment != '') {
				$new_name = '../' . $dir_path . '/' . $new_name . '(' . $increment . ')';
			} else {
				$new_name = '../' . $dir_path . '/' . $new_name;
			}
			$old_name = '../' . $dir_path . '/' . $old_name;
		} else {
			$increment = '';

			while(file_exists('../' . $dir_path . '/' . $new_name . $increment . '.' . $extension) || file_exists('../' . $dir_path . '/' . $new_name . '(' . $increment . ').' . $extension)) {
				$increment++;
			}

			if ($increment != '') {
				$new_name = '../' . $dir_path . '/' . $new_name . '(' . $increment . ').' . $extension;
			} else {
				$new_name = '../' . $dir_path . '/' . $new_name . $increment . '.' . $extension;
			}
			$old_name = '../' . $dir_path . '/' . $old_name . '.' . $extension;
		}
		rename($old_name, $new_name);
		header('location: ../index.php?dir_path=' . $dir_path . '&sort_by=' . $sort['name'] . '&sort_flag=' . $sort['flag']);
		exit();
	}
}