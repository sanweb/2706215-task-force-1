<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\exception;

/**
 * Thrown when task action is unavailable in current status
 * or forbidden for current user.
 */
final class TaskActionException extends \Exception
{
}
