$(document).ready(function () {
  $("#duedate").datepicker(
    {
      dateFormat: "dd.mm.yy"
    }
  );

  $('#taskForm').on('submit', function (event) {
    event.preventDefault();
    let data = $(this).serialize() + '&action=submitTask';

    $.ajax({
      url: 'controllers/TaskController.php',
      type: 'POST',
      data,
      success: function (response) {
        alert('Задача добавлена успешно');
        // $('#taskForm')[0].reset();
      },
      error: function (xhr, status, error) {
        console.log('Error: ' + error);
      }
    });
  });
});