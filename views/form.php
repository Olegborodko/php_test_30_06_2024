<form id="taskForm" class="taskForm" novalidate>
  <div class="mb-3">
    <label for="fullname" class="form-label">ФИО исполнителя:</label>
    <input type="text" class="form-control" id="fullname" name="fullname" required>
  </div>
  <div class="mb-3">
    <label for="email" class="form-label">E-mail:</label>
    <input type="email" class="form-control" id="email" name="email" required>
  </div>
  <div class="mb-3">
    <label for="duedate" class="form-label">Дата завершения задачи:</label>
    <input type="text" class="form-control" id="duedate" name="duedate" required>
  </div>
  <div class="mb-3">
    <label for="title" class="form-label">Название задачи:</label>
    <input type="text" class="form-control" id="title" name="title" required>
  </div>
  <div class="mb-3">
    <label for="description" class="form-label">Описание задачи:</label>
    <textarea class="form-control" id="description" name="description" maxlength="1000" required></textarea>
  </div>
  <button type="submit" class="btn btn-primary submit">Добавить задачу</button>
</form>
<div id="formErrors">
</div>