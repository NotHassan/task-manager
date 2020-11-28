window.onload = function() {
	$('#tasks').DataTable({
		"ordering": false,
		"info": false
	});

	document.querySelector('.container').style.visibility = "visible";

	$('#edit-task-modal').on('show.bs.modal', function(e) {
		var task_data = $(e.relatedTarget).parents('tr').find('td');
		
		$(this).find('#edit-id').val(task_data[0].innerText);
		$(this).find('#edit-name').val(task_data[1].innerText);
		$(this).find('#edit-description').val(task_data[2].innerText);
	});

	$('#delete-task-modal').on('show.bs.modal', function(e) {
		var task_data = $(e.relatedTarget).parents('tr').find('td');
		
		$(this).find('#delete-id').val(task_data[0].innerText);
		$(this).find('#delete-name').val(task_data[1].innerText);
		$(this).find('#delete-description').val(task_data[2].innerText);
	});
}
