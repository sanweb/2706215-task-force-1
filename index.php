<?php

declare(strict_types=1);

require_once __DIR__ . '/Task.php';

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

// Test Task::getActionNextStatus()
assert(
    Task::getActionNextStatus(Task::ACTION_CREATE) === Task::STATUS_NEW,
    'Create task action next status'
);
assert(
    Task::getActionNextStatus(Task::ACTION_CANCEL) === Task::STATUS_CANCELED,
    'Cancel task action next status'
);
assert(
    Task::getActionNextStatus(Task::ACTION_ASSIGN) === Task::STATUS_IN_PROGRESS,
    'Assign executor and start task action next status'
);
assert(
    Task::getActionNextStatus(Task::ACTION_COMPLETE) === Task::STATUS_COMPLETED,
    'Complete task action next status'
);
assert(
    Task::getActionNextStatus(Task::ACTION_REFUSE) === Task::STATUS_FAILED,
    'Refuse task action next status'
);

// Test $task->act() on positive scenarios
$task = new Task(Task::STATUS_NEW, 1);

assert(
    $task->act(Task::ACTION_CANCEL, Task::USER_ROLE_CUSTOMER) === Task::STATUS_CANCELED,
    'Cancel task with status new by customer'
);

$task = new Task(Task::STATUS_NEW, 1);

assert(
    $task->act(Task::ACTION_BID, Task::USER_ROLE_EXECUTOR) === Task::STATUS_NEW,
    'Bid to task with status new by executor'
);

assert(
    $task->act(Task::ACTION_ASSIGN, Task::USER_ROLE_CUSTOMER) === Task::STATUS_IN_PROGRESS,
    'Assign executor and start task by customer'
);

assert(
    $task->act(Task::ACTION_COMPLETE, Task::USER_ROLE_CUSTOMER) === Task::STATUS_COMPLETED,
    'Complete task with status in_progress by customer'
);

$task = new Task(Task::STATUS_NEW, 1);

assert(
    $task->act(Task::ACTION_ASSIGN, Task::USER_ROLE_CUSTOMER) === Task::STATUS_IN_PROGRESS,
    'Assign executor and start task by customer'
);

assert(
    $task->act(Task::ACTION_REFUSE, Task::USER_ROLE_EXECUTOR) === Task::STATUS_FAILED,
    'Refuse assigned task by executor'
);

// Test $task->act() on negative scenarios
assertRuntimeException(
    fn() => $task->act(Task::ACTION_REFUSE, Task::USER_ROLE_EXECUTOR),
    'Try to refuse already refused task by executor'
);

assertRuntimeException(
    fn() => $task->act(Task::ACTION_CANCEL, Task::USER_ROLE_CUSTOMER),
    'Try to cancel already refused task by customer'
);

$task = new Task(Task::STATUS_NEW, 1);

assertRuntimeException(
    fn() => $task->act(Task::ACTION_COMPLETE, Task::USER_ROLE_CUSTOMER),
    'Try to complete task with status new by customer'
);

assertRuntimeException(
    fn() => $task->act(Task::ACTION_CANCEL, Task::USER_ROLE_EXECUTOR),
    'Try to cancel task with status new by executor'
);

echo 'Все тесты прошли успешно';
