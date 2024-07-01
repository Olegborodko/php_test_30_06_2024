$(document).ready(function () {
  $("#duedate").datepicker(
    {
      dateFormat: "dd.mm.yy"
    }
  );

  $('#taskForm').on('submit', function (event) {
    event.preventDefault();

    $('#taskForm .submit').prop('disabled', true);
    let data = $(this).serialize() + '&action=submitTask';

    $.ajax({
      url: 'controllers/TaskController.php',
      type: 'POST',
      data,
      success: function () {
        alert('Задача добавлена успешно');
        $('#taskForm')[0].reset();
        $('#formErrors').html('');
        $('#taskForm .submit').prop('disabled', false);
        refreshTasks();
      },
      error: function (xhr) {
        $('#taskForm .submit').prop('disabled', false);
        try {
          var response = JSON.parse(xhr.responseText);
          if (response.status === 'error') {
            let formattedText = "<ul>";
            for (let i = 0; i < response.message.length; i++) {
              formattedText += `<li>${response.message[i]}</li>`;
            }
            formattedText += "</ul>";
            $('#formErrors').html(formattedText);
          }
        } catch (e) {
          console.error(e);
        }
      }
    });
  });

  function refreshTasks(sortingField = '', direction = 'ASC') {
    $.ajax({
      url: 'controllers/TaskController.php?sorting=' + sortingField + '&direction=' + direction,
      type: 'GET',
      success: function (data) {
        let tableBody = $('#taskTable tbody');
        tableBody.empty();

        data.data.forEach(function (task) {
          var row = '<tr>' +
            '<td>' + task.id + '</td>' +
            '<td>' + task.full_name + '</td>' +
            '<td>' + task.title + '</td>' +
            '<td>' + task.description + '</td>' +
            '<td>' + task.created_at + '</td>' +
            '<td>' + task.due_date + '</td>' +
            '<td><button data-task="' + task.id + '" class="btn btn-info btnDeleteTask">Удалить</button></td>' +
            '</tr>';
          tableBody.append(row);
        });
      },
      error: function (xhr) {
        console.error('Error: ', xhr.status, xhr.statusText);
      }
    });
  }

  $(document).on('click', '#taskTable .btnDeleteTask', function () {
    let taskId = $(this).attr("data-task");

    $.ajax({
      url: 'controllers/TaskController.php?taskId=' + taskId,
      type: 'DELETE',
      data: taskId,
      success: function () {
        refreshTasks();
      },
      error: function (xhr) {
        console.error('Error: ', xhr.status, xhr.statusText);
      }
    });
  });

  function changeDirection(value){
    if (value ==='ASC'){
      return 'DESC';
    } else {
      return 'ASC';
    }
  }

  let directionСreatedAt = 'DESC';
  $("#taskTable .dataCreateSort").click(function () {
    directionСreatedAt = changeDirection(directionСreatedAt);
    refreshTasks('created_at', directionСreatedAt);
  });

  let directionDueDate = 'DESC';
  $("#taskTable .dataEndSort").click(function () {
    directionDueDate = changeDirection(directionDueDate);
    refreshTasks('due_date', directionDueDate);
  });

  refreshTasks();
});