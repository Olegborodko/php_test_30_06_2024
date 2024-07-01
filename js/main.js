$(document).ready(function () {
  $("#duedate").datepicker(
    {
      dateFormat: "dd.mm.yy"
    }
  );

  $('#taskForm').on('submit', function (event) {
    event.preventDefault();

    $('#taskForm .submit').prop('disabled', true);

    $.ajax({
      url: 'controllers/TaskController.php',
      type: 'POST',
      data: $(this).serialize(),
      success: function () {
        alert('Задача добавлена успешно');
        $('#taskForm')[0].reset();
        $('#formErrors').html('');
        $('#taskForm .submit').prop('disabled', false);
        refreshTasks({});
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

  function refreshTasks(data) {
    $.ajax({
      url: 'controllers/TaskController.php',
      data,
      type: 'GET',
      success: function (data) {
        $('#taskTable tbody').children(':not(.findFields)').remove();

        data.data.forEach(function (task) {
          let row = '<tr>' +
            '<td>' + task.id + '</td>' +
            '<td>' + task.full_name + '</td>' +
            '<td>' + task.title + '</td>' +
            '<td>' + task.description + '</td>' +
            '<td>' + task.created_at + '</td>' +
            '<td>' + task.due_date + '</td>' +
            '<td><button data-task="' + task.id + '" class="btn btn-info btnDeleteTask">Удалить</button></td>' +
            '</tr>';
          $('#taskTable tbody .findFields').before(row);
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
        refreshTasks({});
      },
      error: function (xhr) {
        console.error('Error: ', xhr.status, xhr.statusText);
      }
    });
  });

  function changeDirection(value) {
    if (value === 'ASC') {
      return 'DESC';
    } else {
      return 'ASC';
    }
  }

  let directionСreatedAt = 'DESC';
  $("#taskTable .dataCreateSort").click(function () {
    directionСreatedAt = changeDirection(directionСreatedAt);
    refreshTasks({
      sorting: 'created_at',
      direction: directionСreatedAt
    });
  });

  let directionDueDate = 'DESC';
  $("#taskTable .dataEndSort").click(function () {
    directionDueDate = changeDirection(directionDueDate);
    refreshTasks({
      sorting: 'due_date',
      direction: directionDueDate
    });
  });

  $(document).on('input', '#taskTable #findFullName', function () {
    let value = $(this).val();

    if (value.length > 1) {
      refreshTasks({
        findfield: 'full_name',
        findvalue: value,
      })
    } else {
      refreshTasks({});
    }
  });

  $(document).on('input', '#taskTable #findTaskTitle', function () {
    let value = $(this).val();

    if (value.length > 1) {
      refreshTasks({
        findfield: 'title',
        findvalue: value,
      })
    } else {
      refreshTasks({});
    }
  });

  refreshTasks();
});