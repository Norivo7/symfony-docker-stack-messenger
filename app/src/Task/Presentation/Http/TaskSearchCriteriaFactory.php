<?php

declare(strict_types=1);

namespace App\Task\Presentation\Http;

use App\Task\Application\Query\Criteria\TaskSearchCriteria;
use App\Task\Domain\Enums\TaskStatus;
use App\Task\Presentation\Http\Exception\InvalidTaskFilterException;
use Symfony\Component\HttpFoundation\Request;

final readonly class TaskSearchCriteriaFactory
{
    public function createFromRequest(Request $request): TaskSearchCriteria
    {
        $statusParam = $request->query->get('status');
        $createdFromParam = $request->query->get('createdFrom');
        $createdToParam = $request->query->get('createdTo');

        $status = null;
        if (null !== $statusParam) {
            try {
                $status = TaskStatus::from($statusParam);
            } catch (\ValueError $exception) {
                $allowedStatuses = array_map(
                    static fn (TaskStatus $case) => $case->value,
                    TaskStatus::cases()
                );

                throw new InvalidTaskFilterException(sprintf('Invalid status filter "%s". Allowed values: %s', $statusParam, implode(', ', $allowedStatuses)), previous: $exception);
            }
        }

        $createdFrom = null;
        if (null !== $createdFromParam) {
            try {
                $createdFrom = new \DateTimeImmutable($createdFromParam);
            } catch (\Exception $exception) {
                throw new InvalidTaskFilterException(sprintf('Invalid createdFrom filter "%s". Expected ISO format.', $createdFromParam), previous: $exception);
            }
        }

        $createdTo = null;
        if (null !== $createdToParam) {
            try {
                $createdTo = new \DateTimeImmutable($createdToParam);
            } catch (\Exception $exception) {
                throw new InvalidTaskFilterException(sprintf('Invalid createdTo filter "%s". Expected ISO format.', $createdToParam), previous: $exception);
            }
        }

        return new TaskSearchCriteria(
            status: $status,
            createdFrom: $createdFrom,
            createdTo: $createdTo,
        );
    }
}
