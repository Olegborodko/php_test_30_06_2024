<?php

class TaskController
{
  public function handleRequest()
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $action = isset($_POST['action']) ? $_POST['action'] : '';

      switch ($action) {
        case 'submitTask':
          $this->submitTask($_POST);
          break;
        case 'deleteTask':
          $this->deleteTask($_POST);
          break;
        default:
          http_response_code(400);
          echo json_encode(['status' => 'error']);
          break;
      }
    } else {
      http_response_code(405);
      echo json_encode(['status' => 'error']);
    }
  }

  private function submitTask($data)
  {
    $errors = [];

    if (empty($data['fullName']) || empty($data['email']) || empty($data['dueDate']) || empty($data['taskName']) || empty($data['taskDescription'])) {
      $errors[] = 'не все поля заполнены';
    }

    if (strlen($data['taskDescription']) > 1000) {
      $errors[] = 'описание задачи имеет больше 1000 символов';
    }

    if (!preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $data['dueDate'])) {
      $errors[] = 'дата должна быть в формате dd.mm.YYYY';
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'e-mail введен некорректно';
    }

    if (!empty($errors)) {
      echo json_encode(['status' => 'success', 'message' => 'Задача обновлена успешно']);
    } else {
      echo json_encode(['status' => 'success', 'message' => 'Задача обновлена успешно']);
    }
  }

  private function deleteTask($data)
  {
  }
}

$controller = new TaskController();
$controller->handleRequest();