<?php
namespace App\Core;

class Paginator {
    private int $totalItems;
    private int $perPage;
    private int $currentPage;
    private int $totalPages;

    public function __construct(int $totalItems, int $perPage, int $currentPage) {
        $this->totalItems = $totalItems;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
        $this->totalPages = ceil($totalItems / $perPage);
    }

    public function getOffset(): int {
        return ($this->currentPage - 1) * $this->perPage;
    }

    public function getLimit(): int {
        return $this->perPage;
    }

    public function hasPages(): bool {
        return $this->totalPages > 1;
    }

    public function hasPrevious(): bool {
        return $this->currentPage > 1;
    }

    public function hasNext(): bool {
        return $this->currentPage < $this->totalPages;
    }

    public function getPreviousPage(): int {
        return $this->currentPage - 1;
    }

    public function getNextPage(): int {
        return $this->currentPage + 1;
    }

    public function getTotalItems(): int {
        return $this->totalItems;
    }

    public function getTotalPages(): int {
        return $this->totalPages;
    }

    public function getCurrentPage(): int {
        return $this->currentPage;
    }
    
    public function getPageRange(int $delta = 2): array {
        $range = [];
        $start = max(1, $this->currentPage - $delta);
        $end = min($this->totalPages, $this->currentPage + $delta);
        
        for ($i = $start; $i <= $end; $i++) {
            $range[] = $i;
        }
        
        return $range;
    }
}
