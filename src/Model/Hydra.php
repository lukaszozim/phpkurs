<?php

namespace App\Model;

class Hydra
{
    public int $totalItems;
    public int $nextPage;
    public int $previousPage;
    public int $currentPage;
    public iterable $members;
}