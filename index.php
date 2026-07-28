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

$task = new Task(TaskStatus::New, 1);

// Test $task->getNextStatusByAction()

assertTaskActionException(
    fn() => $task->act(TaskAction::Create, UserRole::Customer),
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
$task = new Task(TaskStatus::New, 1);

assert(
    $task->act(TaskAction::Cancel, UserRole::Customer) === TaskStatus::Canceled,
    'Cancel task with status new by customer'
);

$task = new Task(TaskStatus::New, 1);

assert(
    $task->act(TaskAction::Bid, UserRole::Executor) === TaskStatus::New,
    'Bid to task with status new by executor'
);

assert(
    $task->act(TaskAction::Assign, UserRole::Customer) === TaskStatus::InProgress,
    'Assign executor and start task by customer'
);

assert(
    $task->act(TaskAction::Complete, UserRole::Customer) === TaskStatus::Completed,
    'Complete task with status in_progress by customer'
);

$task = new Task(TaskStatus::New, 1);

assert(
    $task->act(TaskAction::Assign, UserRole::Customer) === TaskStatus::InProgress,
    'Assign executor and start task by customer'
);

assert(
    $task->act(TaskAction::Refuse, UserRole::Executor) === TaskStatus::Failed,
    'Refuse assigned task by executor'
);

// Test $task->act() on negative scenarios
assertTaskActionException(
    fn() => $task->act(TaskAction::Refuse, UserRole::Executor),
    'Try to refuse already refused task by executor'
);

assertTaskActionException(
    fn() => $task->act(TaskAction::Cancel, UserRole::Customer),
    'Try to cancel already refused task by customer'
);

$task = new Task(TaskStatus::New, 1);

assertTaskActionException(
    fn() => $task->act(TaskAction::Complete, UserRole::Customer),
    'Try to complete task with status new by customer'
);

assertTaskActionException(
    fn() => $task->act(TaskAction::Cancel, UserRole::Executor),
    'Try to cancel task with status new by executor'
);

echo 'Все тесты прошли успешно';
