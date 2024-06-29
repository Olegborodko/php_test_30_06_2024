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
}
