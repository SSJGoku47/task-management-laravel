<?php

namespace App\Helpers;

use DateTimeImmutable;

class TaskAlgorithm
{
    /**
     * @param array<int, array<string, mixed>> $tasks
     * @return array<int, array<string, mixed>>
     */
    public static function topTasks(array $tasks, int $n): array
    {
        $today = new DateTimeImmutable('today');

        usort($tasks, static function (array $a, array $b) use ($today) {
            $aOverdue = self::isOverdue($a, $today);
            $bOverdue = self::isOverdue($b, $today);

            if ($aOverdue !== $bOverdue) {
                return $aOverdue ? -1 : 1;
            }

            if (($a['priority'] ?? 0) !== ($b['priority'] ?? 0)) {
                return ($b['priority'] ?? 0) <=> ($a['priority'] ?? 0);
            }

            return ($b['project_importance'] ?? 0) <=> ($a['project_importance'] ?? 0);
        });

        return array_slice($tasks, 0, $n);
    }

    private static function isOverdue(array $task, DateTimeImmutable $today): bool
    {
        if (($task['status'] ?? 'todo') !== 'todo') {
            return false;
        }

        $createdAt = new DateTimeImmutable((string) ($task['created_at'] ?? 'now'));
        $deadline = $createdAt->modify('+3 days')->setTime(0, 0);

        return $deadline < $today;
    }
}
