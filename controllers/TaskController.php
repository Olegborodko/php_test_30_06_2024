<?php
header('Content-Type: application/json');

require_once '../service/EmailService.php';
require_once '../models/TaskModel.php';

class TaskController
{
  private $taskModel;
  private $emailInstance;

  public function __construct()
  {
    $this->taskModel = new TaskModel();
    $this->emailInstance = new EmailService();
  }

  public function handleRequest()
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $action = isset($_POST['action']) ? $_POST['action'] : '';

      switch ($action) {
        case 'submitTask':
          $this->insertTask($_POST);
          break;
        case 'deleteTask':
          $this->deleteTask($_POST);
          break;
        default:
          http_response_code(400);
          echo json_encode(['status' => 'error']);
          break;
      }
    } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
      $this->getTasks();
    } else {
      http_response_code(405);
      echo json_encode(['status' => 'error']);
    }
  }

  private function insertTask($data)
  {
    $errors = $this->taskModel->validateTask($data);

    if (!empty($errors)) {
      http_response_code(400);
      echo json_encode(['status' => 'error', 'message' => $errors]);
      return;
    }

    $insertDb = $this->taskModel->saveTask(
      $data['fullname'],
      $data['email'],
      $data['duedate'],
      $data['title'],
      $data['description'],
    );

    if ($insertDb) {
      $sendEmail = $this->emailInstance->sendEmail(
        $data['email'],
        'new task ' . date('Y-m-d H:i:s'),
        $data,
      );
      if ($sendEmail) {
        echo json_encode(['status' => 'success']);
      } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => ['error send email']]);
      }
    } else {
      http_response_code(500);
      echo json_encode(['status' => 'error', 'message' => ['error database']]);
    }
  }

  private function getTasks()
  {
    $data = $this->taskModel->getTasks();
    if ($data) {
      echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
      http_response_code(404);
      echo json_encode(['status' => 'error']);
    }
  }

  private function deleteTask($data)
  {
  }
}

$controller = new TaskController();
$controller->handleRequest();