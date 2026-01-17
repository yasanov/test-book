<?php

declare(strict_types=1);

namespace app\services;

use app\repositories\ReportRepository;

class ReportService
{
    public function __construct(
        private readonly ReportRepository $reportRepository
    ) {
    }

    /**
     * @param int $year
     * @return array
     */
    public function getTopAuthorsByYear(int $year): array
    {
        return $this->reportRepository->getTopAuthorsByYear($year);
    }
}
