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
      error: function (xhr) {
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
});