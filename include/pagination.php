<?php
class Pagination {
	private $dir_path;
	public $sort = [];
	 public function pagination_display($data) {
		if (isset($data['pagination_url']) && !empty($data['pagination_url'])) {
			$pagination_url = $data['pagination_url'];
		} else {
			echo 'No pagination URL mentioned here...';
			return false;
		}
		if (isset($data['total_records']) && !empty($data['total_records'])) {
			$total_records = $data['total_records'];
		} else {
			echo 'No total records mentioned here...';
			return false;
		}
		if (isset($data['records_per_page']) && !empty($data['records_per_page'])) {
			$records_per_page = $data['records_per_page'];
		} else {
			echo 'No records per page mentioned here...';
			return false;
		}
		$total_pages = $this -> total_pages($data);
		if (isset($_GET['page'])) {
			$current_page = $_GET['page'];
		} else {
			$current_page = '';
		}
		if ($current_page == '' || $current_page < 1 || $current_page > $total_pages) {
			$current_page = 1;
			$start_record = 0;
		} else {
			$start_record = ($current_page * $records_per_page) - $records_per_page;
		}
		 $this -> pagination_control($current_page, $total_pages, $pagination_url);

		return 0;
	}

	public function __construct($dir_path, $sort)
	{
		$this->dir_path = $dir_path;
		$this->sort = $sort;
	}

	private function total_pages($data) {
		$total_records = $data['total_records'];
		$records_per_page = $data['records_per_page'];
	 	$total_pages = ceil($total_records / $records_per_page);
	 	return $total_pages;
	}

	private function pagination_control($current_page, $total_pages, $pagination_url) {
		echo '<ul class="pagination center">';
		$previous = $current_page - 1;
	 	$next = $current_page + 1;
	 	echo '<li class="waves-effect"><a href="' . $pagination_url . '?page=1&dir_path=' . $this->dir_path . '&sort_flag=' . $this->sort['flag'] . '&sort_by=' . $this->sort['name'] . '"><<<</a></li>';
	 	if ($current_page >= 2) {
			echo '<li class="waves-effect"><a href="' . $pagination_url . '?page=' . $previous . '&dir_path=' . $this->dir_path . '&sort_flag=' . $this->sort['flag'] . '&sort_by=' . $this->sort['name'] . '"><<</a></li>';
		}
	 	$start_page = 1;
	 	if ($current_page <= $total_pages && $current_page > ($start_page + 2)) {
			$start_page = $current_page - 2;
		}
	 	if ($current_page <= $total_pages && $current_page > ($start_page + 2)) {
			$end_page = $current_page + 2;
		} else {
	 		$end_page = $total_pages;
		}

	 	for ($start_page; $start_page <= $end_page; $start_page++) {
	 		if ($current_page == $start_page) {
	 			echo '<li class="active blue"><a href="#">' . $start_page . '</a></li>';
			} else {
				echo '<li class="waves-effect"><a href="' . $pagination_url . '?page=' . $start_page . '&dir_path=' . $this->dir_path . '&sort_flag=' . $this->sort['flag'] . '&sort_by=' . $this->sort['name'] . '">' . $start_page . '</a></li>';
			}
		}

	 	if ($current_page < $total_pages) {
			echo '<li class="waves-effect"><a href="' . $pagination_url . '?page=' . $next . '&dir_path=' . $this->dir_path . '&sort_flag=' . $this->sort['flag'] . '&sort_by=' . $this->sort['name'] . '">>></a></li>';
		}

		echo '<li class="waves-effect"><a href="' . $pagination_url . '?page=' . $total_pages . '&dir_path=' . $this->dir_path . '&sort_flag=' . $this->sort['flag'] . '&sort_by=' . $this->sort['name'] . '">>>></a></li>';
		echo  '</ul>';
	}

	public function start_record($data) {
	 	if (isset($_GET['page'])) {
	 		$current_page = $_GET['page'];
		} else {
	 		$current_page = '';
		}
	 	$total_pages = $this -> total_pages($data);
		if (isset($data['records_per_page']) && !empty($data['records_per_page'])) {
			$records_per_page = $data['records_per_page'];
		} else {
			echo 'No records per page mentioned here...';
			return false;
		}
		if ($current_page == '' || $current_page < 1 || $current_page > $total_pages) {
			$start_record = 0;
			return $start_record;
		} else {
			$start_record = ($current_page * $records_per_page) - $records_per_page;
			return $start_record;
		}
	}
}