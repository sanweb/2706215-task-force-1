<?php

declare(strict_types=1);

use Sanweb\Taskforce\models\Task;
use Sanweb\Taskforce\enum\TaskStatus;
use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\enum\UserRole;
use Sanweb\Taskforce\exception\TaskActionException;

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

$userId = 1;
$customerId = 1;
$executorId = 2;
$task = new Task(TaskStatus::New, $customerId, null);

// Test $task->getNextStatusByAction()

assertTaskActionException(
    fn() => $task->act(TaskAction::Create, UserRole::Customer, $userId),
    'Try to call create task action'
);
assert(
    $task->getNextStatusByAction(TaskAction::Cancel) === TaskStatus::Canceled,
    'Cancel task action next status'
);
assert(
    $task->getNextStatusByAction(TaskAction::Assign) === TaskStatus::InProgress,
    'Assign executor and start task action next status'
);
assert(
    $task->getNextStatusByAction(TaskAction::Complete) === TaskStatus::Completed,
    'Complete task action next status'
);
assert(
    $task->getNextStatusByAction(TaskAction::Refuse) === TaskStatus::Failed,
    'Refuse task action next status'
);

// Test $task->act() on positive scenarios
$task = new Task(TaskStatus::New, $customerId, null);

assert(
    $task->act(TaskAction::Cancel, UserRole::Customer, $userId) === TaskStatus::Canceled,
    'Cancel task with status new by customer'
);

$task = new Task(TaskStatus::New, $customerId, null);

assert(
    $task->act(TaskAction::Bid, UserRole::Executor, $executorId) === TaskStatus::New,
    'Bid to task with status new by executor'
);

assert(
    $task->act(TaskAction::Assign, UserRole::Customer, $userId) === TaskStatus::InProgress,
    'Assign executor and start task by customer'
);

$task = new Task(TaskStatus::InProgress, $customerId, $executorId);
assert(
    $task->act(TaskAction::Complete, UserRole::Customer, $userId) === TaskStatus::Completed,
    'Complete task with status in_progress by customer'
);

$task = new Task(TaskStatus::New, $customerId, null);

assert(
    $task->act(TaskAction::Assign, UserRole::Customer, $userId) === TaskStatus::InProgress,
    'Assign executor and start task by customer'
);

$task = new Task(TaskStatus::InProgress, $customerId, $executorId);
assert(
    $task->act(TaskAction::Refuse, UserRole::Executor, $executorId) === TaskStatus::Failed,
    'Refuse assigned task by executor'
);

$task = new Task(TaskStatus::Failed, $customerId, $executorId);
// Test $task->act() on negative scenarios
assertTaskActionException(
    fn() => $task->act(TaskAction::Refuse, UserRole::Executor, $executorId),
    'Try to refuse already refused task by executor'
);

assertTaskActionException(
    fn() => $task->act(TaskAction::Cancel, UserRole::Customer, $userId),
    'Try to cancel already refused task by customer'
);

$task = new Task(TaskStatus::New, $customerId, null);

assertTaskActionException(
    fn() => $task->act(TaskAction::Complete, UserRole::Customer, $userId),
    'Try to complete task with status new by customer'
);

assertTaskActionException(
    fn() => $task->act(TaskAction::Cancel, UserRole::Executor, $executorId),
    'Try to cancel task with status new by executor'
);

echo 'Все тесты прошли успешно';
