<?php
require_once "app/models/Task.php";

class TaskController extends Controller
{
    /**
     * Добавление задачи
     */
    function actionAdd()
    {
        if ($_POST["form"]) {
            $model = new Task($_POST["form"]);
            if (!$model->save()) {
                echo json_encode(["success" => 0, "message" => $model->getError()]);
                return;
            }

            echo json_encode(['success' => 1]);
            return;
        }

        $this->view->generate('task/add.php');
    }

    /**
     * Редактирование задачи
     */
    function actionEdit()
    {
        $model = $this->loadModel($_GET['id']);

        if ($_POST["form"]) {
            if (User::isGuest()){
                echo json_encode(["success" => 0, "message" => "Недостаточно прав для выполнения этой операции"]);
                return;
            }

            $task = new Task($_POST["form"]);
            $task->id = $model->id;
            $task->label = "отредактировано администратором";
            if (!$task->save()) {
                echo json_encode(["success" => 0, "message" => $task->getError()]);
                return;
            }

            echo json_encode(['success' => 1]);
            return;
        }

        if (User::isGuest())
            header('Location: http://'.$_SERVER['HTTP_HOST'].'/');

        $this->view->generate('task/edit.php', [
            'model' => $model,
        ]);
    }

    /**
     * Задача выполнена
     */
    function actionCompleted()
    {
        if (User::isGuest()){
            echo json_encode(["success" => 0, "message" => "Недостаточно прав для выполнения этой операции"]);
            return;
        }

        $model = $this->loadModel($_GET['id']);
        $model->status = 1;
        if (!$model->save()) {
            echo json_encode(["success" => 0, "message" => $model->getError()]);
            return;
        }

        echo json_encode(['success' => 1]);
        return;
    }

    /**
     * Загрузка модели
     *
     * @param $id - id задачи из BD
     * @return mixed|Task
     */
    function loadModel($id)
    {
        $model = Task::getDataOne($id);
        if (!$model)
            Route::ErrorPage(404, "Not Found");

        $model = new Task($model);
        return $model;
    }
}