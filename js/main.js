$(document).ready(function () {
	// CREATE FOLDER
	$(document).on("click", "#create_folder", function () {
		$("#action").val("create");
		$("#folder_name").val("");
		$("#folder_button").val("Create");
		$("#old_name").val("");
		$("#change_title").text("Create Folder");
		$('#folderModal').modal('open');
	});

	$(document).on("click", "#folder_button", function () {
		console.log('Work');
		let folder_name = $("#folder_name").val();
		let action = $("#action").val();
		let old_name = $("#old_name").val();
		let path_name = $('#path_name').val();
		if (folder_name !== "") {
			$.ajax({
				url: "include/action.php",
				method: "POST",
				data: {
					folder_name: folder_name,
					action: action,
					old_name: old_name,
					path_name: path_name
				},
				success: function (data) {
					console.log(data);
					$("#folderModal").modal("close");
					alert(data);
					$('#error').html(data);
					location.reload();
				}
			});
		} else {
			alert("Enter Folder Name");
		}
	});

});
