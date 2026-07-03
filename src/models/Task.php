<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\models;

use RuntimeException;
use InvalidArgumentException;

final class Task
{
    public const STATUS_NEW = 'new';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    public const ACTION_CREATE = 'create'; // ?
    public const ACTION_CANCEL = 'cancel';
    public const ACTION_BID = 'bid'; // ?
    public const ACTION_ASSIGN = 'assign';
    public const ACTION_COMPLETE = 'complete';
    public const ACTION_REFUSE = 'refuse';

    public const USER_ROLE_CUSTOMER = 'customer';
    public const USER_ROLE_EXECUTOR = 'executor';

    private static array $statusMap = [
        self::STATUS_NEW => 'Новое',
        self::STATUS_CANCELED => 'Отменено',
        self::STATUS_IN_PROGRESS => 'В работе',
        self::STATUS_COMPLETED => 'Выполнено',
        self::STATUS_FAILED => 'Провалено',
    ];

    private static array $actionMap = [
        self::ACTION_CREATE => 'Создать',
        self::ACTION_CANCEL => 'Отменить',
        self::ACTION_BID => 'Откликнуться',
        self::ACTION_ASSIGN => 'Назначить',
        self::ACTION_COMPLETE => 'Завершить',
        self::ACTION_REFUSE => 'Отказаться',
    ];

    private static array $actionNextStatusMap = [
        self::ACTION_CREATE => self::STATUS_NEW,
        self::ACTION_CANCEL => self::STATUS_CANCELED,
        //self::ACTION_BID => self::STATUS_NEW,
        self::ACTION_ASSIGN => self::STATUS_IN_PROGRESS,
        self::ACTION_COMPLETE => self::STATUS_COMPLETED,
        self::ACTION_REFUSE => self::STATUS_FAILED,
    ];

    /**
     * @todo Move to appropriate place
     */
    private static array $userRoleMap = [
        self::USER_ROLE_CUSTOMER => 'Заказчик',
        self::USER_ROLE_EXECUTOR => 'Исполнитель',
    ];

    private string $status;
    private int $customerId;
    private ?int $executorId;

    public static function getStatuses(): array
    {
        return self::$statusMap;
    }

    public static function getStatusLabel(string $status): ?string
    {
        return self::$statusMap[$status] ?? null;
    }

    public static function getActions(): array
    {
        return self::$actionMap;
    }

    public static function getActionNextStatus(string $action): ?string
    {
        return self::$actionNextStatusMap[$action] ?? null;
    }

    public function __construct(string $status, int $customerId, ?int $executorId = null)
    {
        if (isset(self::$statusMap[$status]) && $customerId > 0) {
            $this->status = $status;
            $this->customerId = $customerId;
            $this->executorId = $executorId > 0 ? $executorId : null;
        } else {
            throw new InvalidArgumentException(sprintf(
                'Invalid task data (status: %s, customerId: %d, executorId: %s)',
                $status,
                $customerId,
                $executorId === null ? 'null' : (string) $executorId
            ));
        }
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getAvailableActions(): array
    {
        $availableActions = [];

        if ($this->status === self::STATUS_NEW) {
            $availableActions = [
                self::ACTION_CANCEL,
                self::ACTION_BID,
                self::ACTION_ASSIGN,
            ];
        } elseif ($this->status === self::STATUS_IN_PROGRESS) {
            $availableActions = [
                self::ACTION_COMPLETE,
                self::ACTION_REFUSE,
            ];
        }

        return $availableActions;
    }

    public function getAllowedActions(string $userRole): array
    {
        $allowedActions = [];

        if ($userRole === self::USER_ROLE_CUSTOMER) {
            $allowedActions = [
                self::ACTION_CREATE,
                self::ACTION_CANCEL,
                self::ACTION_ASSIGN,
                self::ACTION_COMPLETE,
            ];
        } elseif ($userRole === self::USER_ROLE_EXECUTOR) {
            $allowedActions = [
                self::ACTION_BID,
                self::ACTION_REFUSE,
            ];
        }

        return $allowedActions;
    }

    public function act(string $action, string $userRole): string
    {
        if (
            !in_array($action, $this->getAvailableActions(), true)
            || !in_array($action, $this->getAllowedActions($userRole), true)
        ) {
            throw new RuntimeException("Cannot perform $action by $userRole on status {$this->status}");
        }

        $this->status = self::getActionNextStatus($action) ?: $this->status;

        return $this->status;
    }
}
