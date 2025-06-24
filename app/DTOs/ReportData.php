<?php

namespace App\DTOs;


class ReportData
{
    public function __construct(
        protected string|null $report,
    ) {}

    public function toArray(): string|null
    {
        if (is_string($this->report)) {
            return $this->report;
        }

        return $this->report;
    }
}
