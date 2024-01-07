<?php

namespace App\Model\Repositories;

/**
 * Class ProductRepository
 * @package App\Model\Repositories
 */
class ProductRepository extends BaseRepository{

    public function getProductsByFilter(array $filter)
    {
        // Předpokládáme, že $filter obsahuje pole kategorií
        $categoryIds = $filter['categoryIds'] ?? [];
    $id = '1';
        $query = $this->connection->select('*')
            ->from($this->getTable())
        ->where('category_id in (' , $filter,')');
        return $this->createEntities($query->fetchAll());
    }

}