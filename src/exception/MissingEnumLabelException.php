<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\exception;

/**
 * Thrown when an existing enum case has no label.
 */
final class MissingEnumLabelException extends \LogicException
{
}
