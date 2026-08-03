<?php

declare(strict_types=1);

use Sanweb\Taskforce\components\TaskAction\TaskActionFactory;
use Sanweb\Taskforce\components\TaskState\TaskStateMachine;
use Sanweb\Taskforce\models\Task;
use Sanweb\Taskforce\enum\TaskStatus;
use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\exception\TaskActionException;
use Sanweb\Taskforce\models\User;
use Sanweb\Taskforce\services\TaskService;

require_once __DIR__ . '/vendor/autoload.php';

// helper for negative scenarios that throws TaskActionException
function assertTaskActionException(callable $callback, string $message): void
{
    $exceptionThrown = false;

    try {
        $callback();
    } catch (TaskActionException $exception) {
        $exceptionThrown = true;
    }

    assert($exceptionThrown === true, $message);
}

$taskService = new TaskService(new TaskStateMachine());

$userId = 1;
$customerId = 1;
$executorId = 2;
$customerUser = new User($customerId, false);
$executorUser = new User($executorId, true);

$task = new Task(TaskStatus::New, $customerId, null);

assertTaskActionException(
    fn() => $taskService->performAction($task, TaskAction::Create, $customerUser),
    'Try to call create task action'
);

assert(
    TaskActionFactory::create(TaskAction::Cancel)->getNextStatus() === TaskStatus::Canceled,
    'Cancel task action next status'
);
assert(
    TaskActionFactory::create(TaskAction::Assign)->getNextStatus() === TaskStatus::InProgress,
    'Assign executor and start task action next status'
);
assert(
    TaskActionFactory::create(TaskAction::Complete)->getNextStatus() === TaskStatus::Completed,
    'Complete task action next status'
);
assert(
    TaskActionFactory::create(TaskAction::Refuse)->getNextStatus() === TaskStatus::Failed,
    'Refuse task action next status'
);

// Test $task->act() on positive scenarios
$task = new Task(TaskStatus::New, $customerId, null);

assert(
    $taskService->performAction(
        $task,
        TaskAction::Cancel,
        $customerUser
    )->getStatus() === TaskStatus::Canceled,
    'Cancel task with status new by customer'
);

assert(
    $taskService->performAction(
        $task,
        TaskAction::Bid,
        $executorUser
    )->getStatus() === TaskStatus::New,
    'Bid to task with status new by executor'
);

assert(
    $taskService->performAction(
        $task,
        TaskAction::Assign,
        $customerUser,
        ['executor_id' => $executorId]
    )->getStatus() === TaskStatus::InProgress,
    'Assign executor and start task by customer'
);

$task = new Task(TaskStatus::InProgress, $customerId, $executorId);
assert(
    $taskService->performAction(
        $task,
        TaskAction::Complete,
        $customerUser
    )->getStatus() === TaskStatus::Completed,
    'Complete task with status in_progress by customer'
);

$task = new Task(TaskStatus::New, $customerId, null);

assert(
    $taskService->performAction(
        $task,
        TaskAction::Assign,
        $customerUser,
        ['executor_id' => $executorId]
    )->getStatus() === TaskStatus::InProgress,
    'Assign executor and start task by customer'
);

$task = new Task(TaskStatus::InProgress, $customerId, $executorId);
assert(
    $taskService->performAction(
        $task,
        TaskAction::Refuse,
        $executorUser
    )->getStatus() === TaskStatus::Failed,
    'Refuse assigned task by executor'
);

$task = new Task(TaskStatus::Failed, $customerId, $executorId);
// Test $task->act() on negative scenarios
assertTaskActionException(
    fn() => $taskService->performAction(
        $task,
        TaskAction::Refuse,
        $executorUser
    ),
    'Try to refuse already refused task by executor'
);

assertTaskActionException(
    fn() => $taskService->performAction(
        $task,
        TaskAction::Cancel,
        $customerUser
    ),
    'Try to cancel already refused task by customer'
);

$task = new Task(TaskStatus::New, $customerId, null);

assertTaskActionException(
    fn() => $taskService->performAction(
        $task,
        TaskAction::Complete,
        $customerUser
    ),
    'Try to complete task with status new by customer'
);

assertTaskActionException(
    fn() => $taskService->performAction(
        $task,
        TaskAction::Cancel,
        $executorUser
    ),
    'Try to cancel task with status new by executor'
);

// Cancel
$task = new Task(TaskStatus::New, $customerId, null);
$task = $taskService->performAction(
    $task,
    TaskAction::Cancel,
    $customerUser
);
echo $task->getStatus()->label() . '<br>' . PHP_EOL;

// Bid - Assign - Complete
$task = new Task(TaskStatus::New, $customerId, null);
$task = $taskService->performAction(
    $task,
    TaskAction::Bid,
    $executorUser,
    ['price' => 1000]
);
echo $task->getStatus()->label() . PHP_EOL;

$task = $taskService->performAction(
    $task,
    TaskAction::Assign,
    $customerUser,
    ['executor_id' => $executorId]
);
echo $task->getStatus()->label() . PHP_EOL;

$task = $taskService->performAction(
    $task,
    TaskAction::Complete,
    $customerUser
);
echo $task->getStatus()->label() . '<br>' . PHP_EOL;

// Assign - Refuse
$task = new Task(TaskStatus::New, $customerId, null);
$task = $taskService->performAction(
    $task,
    TaskAction::Assign,
    $customerUser,
    ['executor_id' => $executorId]
);
echo $task->getStatus()->label() . PHP_EOL;

$task = $taskService->performAction(
    $task,
    TaskAction::Refuse,
    $executorUser
);
echo $task->getStatus()->label() . '<br>' . PHP_EOL;

echo 'Все тесты прошли успешно';
