<?php

declare(strict_types=1);

use Sanweb\Taskforce\models\Task;
use Sanweb\Taskforce\enum\TaskStatus;
use Sanweb\Taskforce\enum\TaskAction;
use Sanweb\Taskforce\enum\UserRole;

require_once __DIR__ . '/vendor/autoload.php';

// helper for negative scenarios that throws RuntimeException
function assertRuntimeException(callable $callback, string $message): void
{
    $exceptionThrown = false;

    try {
        $callback();
    } catch (RuntimeException $exception) {
        $exceptionThrown = true;
    }

    assert($exceptionThrown === true, $message);
}

$task = new Task(TaskStatus::New, 1);

// Test $task->getActionNextStatus()
assert(
    $task->getActionNextStatus(TaskAction::Create) === TaskStatus::New,
    'Create task action next status'
);
assert(
    $task->getActionNextStatus(TaskAction::Cancel) === TaskStatus::Canceled,
    'Cancel task action next status'
);
assert(
    $task->getActionNextStatus(TaskAction::Assign) === TaskStatus::InProgress,
    'Assign executor and start task action next status'
);
assert(
    $task->getActionNextStatus(TaskAction::Complete) === TaskStatus::Completed,
    'Complete task action next status'
);
assert(
    $task->getActionNextStatus(TaskAction::Refuse) === TaskStatus::Failed,
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
assertRuntimeException(
    fn() => $task->act(TaskAction::Refuse, UserRole::Executor),
    'Try to refuse already refused task by executor'
);

assertRuntimeException(
    fn() => $task->act(TaskAction::Cancel, UserRole::Customer),
    'Try to cancel already refused task by customer'
);

$task = new Task(TaskStatus::New, 1);

assertRuntimeException(
    fn() => $task->act(TaskAction::Complete, UserRole::Customer),
    'Try to complete task with status new by customer'
);

assertRuntimeException(
    fn() => $task->act(TaskAction::Cancel, UserRole::Executor),
    'Try to cancel task with status new by executor'
);

echo 'Все тесты прошли успешно';
