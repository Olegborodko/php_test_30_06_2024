<?php
require_once '../db/Database.php';

class TaskModel
{
  private $connDb;

  public function __construct()
  {
    $databaseInstance = new Database();
    ;
    $this->connDb = $databaseInstance->getConnection();
  }

  public function validateTask($data)
  {
    $errors = [];

    if (empty($data['fullname']) || empty($data['email']) || empty($data['duedate']) || empty($data['title']) || empty($data['description'])) {
      $errors[] = 'Не все поля заполнены';
    }

    if (strlen($data['description']) > 1000) {
      $errors[] = 'Описание задачи имеет больше 1000 символов';
    }

    if (!preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $data['duedate'])) {
      $errors[] = 'Дата должна быть в формате dd.mm.YYYY';
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'E-mail введен некорректно';
    }

    return $errors;
  }

  public function saveTask($full_name, $email, $due_date, $title, $description)
  {
    $due_date = explode('.', $due_date);
    $mysql_date = $due_date[2] . '-' . $due_date[1] . '-' . $due_date[0];

    $query = 'INSERT INTO tasks (full_name, email, due_date, title, description) VALUES (?, ?, ?, ?, ?)';

    try {
      $stmt = $this->connDb->prepare($query);
      $stmt->execute([$full_name, $email, $mysql_date, $title, $description]);
      return true;
    } catch (PDOException $e) {
      error_log('Database error: ' . $e->getMessage());
      return false;
    }
  }

  public function getTasks($data)
  {
    $query = 'SELECT id, full_name, title, description, DATE_FORMAT(created_at, "%d.%m.%Y") as created_at, DATE_FORMAT(due_date, "%d.%m.%Y") as due_date FROM tasks WHERE 1';

    $params = [];

    if ($data['findfield'] && $data['findfield'] == 'full_name' && $data['findvalue']) {
      $query .= ' AND full_name LIKE :full_name';
      $params[':full_name'] = '%' . $data['findvalue'] . '%';
    }

    if ($data['findfield'] && $data['findfield'] == 'title' && $data['findvalue']) {
      $query .= ' AND title LIKE :title';
      $params[':title'] = '%' . $data['findvalue'] . '%';
    }

    if ($data['sorting'] && $data['direction']) {
      $query .= ' ORDER BY ' . $data['sorting'] . ' ' . $data['direction'];
    }

    try {
      $stmt = $this->connDb->prepare($query);
      $stmt->execute($params);

      $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $tasks;
    } catch (PDOException $e) {
      error_log('Database error: ' . $e->getMessage());
      return false;
    }
  }

  public function deleteTask($taskId)
  {
    if (!is_numeric($taskId)) {
      return false;
    }

    $query = 'DELETE FROM tasks WHERE id = :id';

    try {
      $stmt = $this->connDb->prepare($query);
      $stmt->execute([':id' => $taskId]);

      if ($stmt->rowCount() > 0) {
        return true;
      } else {
        return false;
      }
    } catch (PDOException $e) {
      error_log('Database error: ' . $e->getMessage());
      return false;
    }
  }
}
