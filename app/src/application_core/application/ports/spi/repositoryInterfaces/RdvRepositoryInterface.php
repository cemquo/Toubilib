<?php

namespace toubilib\core\application\ports\spi\repositoryInterfaces;

interface RdvRepositoryInterface
{
    public function findAll(): array;
}
