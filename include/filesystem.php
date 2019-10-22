<?php

class Filesystem
{
	public $dir_path;
	public $pagination_url;
	public $arraySearch = [];
	public $sort = [];

	public function __construct($dir_path, $pagination_url, $sort)
	{
		$this->dir_path = $dir_path;
		$this->pagination_url = $pagination_url;
		$this->sort = $sort;
	}

	public function display_page($start_record, $record_per_page)
	{
		$output = '
			<table>
				<thead>
					<tr>
						<th><a href="include/action.php?dir_path=' . $this->dir_path . '&sort_by=name&sort_flag=' . $this->sort['flag'] . '&cmd=sorting">Name</a></th>
						<th><a href="include/action.php?dir_path=' . $this->dir_path . '&sort_by=extension&sort_flag=' . $this->sort['flag'] . '&cmd=sorting">Type</a></th>
						<th><a href="include/action.php?dir_path=' . $this->dir_path . '&sort_by=size&sort_flag=' . $this->sort['flag'] . '&cmd=sorting">Size</a></th>
						<th><a href="include/action.php?dir_path=' . $this->dir_path . '&sort_by=mtime&sort_flag=' . $this->sort['flag'] . '&cmd=sorting">Created</a></th>
						<th>Open</th>
						<th>Delete</th>
					</tr>
				</thead>
				<tbody>
			';
		if ($this->row_count()) {
			$newArr = Filesystem::arrayOrderBy($this->getDirectoryArray()[0], $this->sort['name'], $this->sort['flag']);
			foreach (array_slice($newArr, $start_record, $record_per_page) as $row) {
				$output .= '
				<tr>
					<td class="row">
						<form method="POST" action="include/action.php?&dir_path=' . $this->dir_path . '&sort_flag=' . $this->sort['flag'] . '&sort_by=' . $this->sort['name'] . '&cmd=update" class="col s12">
							<input type="hidden" name="old_name" value="' . $row['name'] . '">
							<input type="hidden" name="extension" value="' . $row['extension'] . '">
							<div class="input-field col s8">
								<input type="text" name="new_name" value="' . $row['name'] . '" class="validate">
							</div>
							<div class="input-field col s4">
								<button type=submit" class="waves-effect orange darken-1 white-text waves-teal btn-flat">RENAME</button>
							</div>
						</form>
					</td>
					<td>' . $row['extension'] . '</td>
					<td>' . $row['size'] . '</td>
					<td>' . $row['mtime'] . '</td>
					<td>';
				if ($row['extension'] == 'folder') {
					$output .= '<a href="' . $this->pagination_url . '?page=1&dir_path=' . $row['dir_path'] . '&sort_flag=' . $this->sort['flag'] . '&sort_by=' . $this->sort['name'] . '" class="waves-effect waves-teal btn-flat"><i class="material-icons left">' . $row['icon'] . '</i>Open</a>';
				} else {
					$output .= '<a class="waves-effect waves-teal btn-flat disabled"><i class="material-icons left">' . $row['icon'] . '</i>Open</a>';
				}
				$output .= '
					</td>
					<td>
						<form action="include/action.php?page=1&dir_path=' . $this->dir_path . '&sort_flag=' . $this->sort['flag'] . '&sort_by=' . $this->sort['name'] . '&cmd=delete" method="post">
							<input type="hidden" name="fileName" value="' . $row['dir_path'] . '">
							<button type="submit" class="waves-effect waves-light red accent-4 btn">Delete</button>
						</form>
					</td>
				</tr>';
			}
		}
		$output .= '
			</tbody>
		</table>
		';
		echo $output;
	}

	public function row_count()
	{
		return count($this->getDirectoryArray()[0]);
	}

	public function getDirectoryArray()
	{
		if (is_dir($this->dir_path)) {
			$directoryArray = [];
			$arraySearch = [];
			$files = scandir($this->dir_path);
			for ($i = 0, $j = 0; $i < count($files); $i++) {
				if ($files[$i] != '.' && $files[$i] != '..') {
					$file = pathinfo($files[$i]);
					if (isset($file['extension'])) {
						$arraySearch[$j] = $directoryArray[$j]['dir_path'] = $this->dir_path . '/' . $files[$i];
						$directoryArray[$j]['extension'] = $extension = $file['extension'];
						$directoryArray[$j]['size'] = filesize($directoryArray[$j]['dir_path']);
						$pos = strpos($files[$i], $extension);
						$directoryArray[$j]['name'] = substr($files[$i], 0, $pos - 1);
						$directoryArray[$j]['mtime'] = date("Y/m/d H:i:s", filemtime($directoryArray[$j]['dir_path']));
					} else {
						$directoryArray[$j]['name'] = $files[$i];
						$arraySearch[$j] = $directoryArray[$j]['dir_path'] = $this->dir_path . '/' . $directoryArray[$j]['name'];
						$directoryArray[$j]['extension'] = $extension = 'folder';
						$directoryArray[$j]['size'] = $this->GetDirectorySize($directoryArray[$j]['dir_path']);
						$stat = stat($directoryArray[$j]['dir_path']);
						$directoryArray[$j]['mtime'] = date("Y/m/d H:i:s", $stat['mtime']);
					}
					switch ($directoryArray[$j]['extension']) {
						case 'folder':
							$directoryArray[$j]['icon'] = 'folder_open';
							break;
						case 'png' || 'gif' || 'jpg':
							$directoryArray[$j]['icon'] = 'photo';
							break;
						case 'mp4':
							$directoryArray[$j]['icon'] = 'movie';
							break;
						case 'pdf':
							$directoryArray[$j]['icon'] = 'picture_as_pdf';
							break;
						case 'mp3':
							$directoryArray[$j]['icon'] = 'music_note';
							break;
						default:
							$directoryArray[$j]['icon'] = 'description';
							break;
					};
					$j++;
				}
			}
		}
		return array($directoryArray, $arraySearch);
	}

	private function GetDirectorySize($path)
	{
		$bytesTotal = 0;
		$path = realpath($path);
		if ($path !== false && $path != '' && file_exists($path)) {
			foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object) {
				$bytesTotal += $object->getSize();
			}
		}
		return $bytesTotal;
	}

	static public function arrayOrderBy()
	{
		$args = func_get_args();
		$data = array_shift($args);
		foreach ($args as $n => $field) {
			if (is_string($field)) {
				$tmp = array();
				foreach ($data as $key => $row)
					$tmp[$key] = $row[$field];
				$args[$n] = $tmp;
			}
		}
		$args[] = &$data;
		call_user_func_array('array_multisort', $args);
		return array_pop($args);
	}
}