<?php

namespace App\Service;

use App\Model\Hydra;

class HydraService
{
    public function transformToHydra(iterable $members, int $page, ?int $nextPage, ?int $prevPge,int $total): Hydra
    {
        $hydra = new Hydra();
        $hydra->currentPage = $page;
        $hydra->nextPage = $nextPage;
        $hydra->previousPage = $prevPge;
        $hydra->totalItems = $total;
        $hydra->members = $members;

        return $hydra;
    }
}