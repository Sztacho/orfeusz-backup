<?php

namespace App\Utils;


class Paginator
{
    private int $total;
    private int $lastPage;
    private array $items;

    public function paginate(array $data, int $page = 1, int $limit = 10): Paginator
    {
        $this->total = count($data);
        $this->lastPage = $this->total / $limit;

        $objects = [];

        foreach ($data as $key => $value) {

        }

        return $this;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    public function getItems(): array
    {
        return $this->items;
    }


}