<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Livewire\Attributes\Locked;

// FIXME: models in array cause model refetch when rehydrating the variable after update. Livewire stuff.

trait HasLoadMore {
    /** @var array<string, int> */
    #[Locked]
    public array $load_more_per_page = [];

    /** @var array<string, int> */
    #[Locked]
    public array $load_more_page = [];

    /** @var array<string, array<int, mixed>> */
    #[Locked]
    public array $load_more_data = [];

    /** @var array<string, int> */
    #[Locked]
    public array $load_more_total = [];

    /** @var array<string, string> */
    #[Locked]
    public array $load_more_query_method = [];

    /** @param array<string, array{query_method: string, per_page: int}> $configs */
    private function useLoadMore(array $configs): void {
        foreach ($configs as $key => $config) {
            $this->load_more_per_page[$key] = $config['per_page'];
            $this->load_more_page[$key] = 0;
            $this->load_more_data[$key] = [];
            $this->load_more_total[$key] = $this->{$config['query_method']}()->count();
            $this->load_more_query_method[$key] = $config['query_method'];
        }
    }

    public function loadMore(string $key): void {
        $this->fetchLoadMoreData($key, loading_more: true);
    }

    public function getLoadMoreTotal(string $key): int {
        return $this->load_more_total[$key];
    }

    public function getLoadMoreData(string $key): array {
        return $this->load_more_data[$key];
    }

    public function canLoadMore(string $key): bool {
        return \count($this->load_more_data[$key]) < $this->load_more_total[$key];
    }

    public function isLoadMoreEmpty(string $key): bool {
        if ($this->missingLoadMore($key)) {
            return true;
        }

        return count($this->load_more_data[$key]) === 0;
    }

    public function isLoadMoreNotEmpty(string $key): bool {
        return ! $this->isLoadMoreEmpty($key);
    }

    public function hasLoadMore(string $key): bool {
        return \array_key_exists($key, $this->load_more_data);
    }

    public function missingLoadMore(string $key): bool {
        return ! $this->hasLoadMore($key);
    }

    private function fetchLoadMoreData(string $key, bool $loading_more = false): void {
        $base_query = $this->{$this->load_more_query_method[$key]}();

        $new_data = $base_query
            ->limit($this->load_more_per_page[$key])
            ->when(
                $loading_more,
                fn (Builder $query) => $query->offset(\count($this->load_more_data[$key]))
            )
            ->get();

        $this->load_more_data[$key] = [...$this->load_more_data[$key], ...$new_data];
    }
}
