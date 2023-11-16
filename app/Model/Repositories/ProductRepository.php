<?php

namespace App\Model\Repositories;

/**
 * Class ProductRepository
 * @package App\Model\Repositories
 */
class ProductRepository extends BaseRepository{

    /**
    * Metoda pro nalezení kroduktů s danou kategorií
     * @param int|nul $categoryId
     * @param bool|null $available
     * @param int|null $offset
     * @param int|null $limit
     * @return Product[]
    */

    public function findAllByCategoryAndAvailable(?int $categoryId=null, ?bool $available=null, ?int $offset=null, ?int $limit=null):array
    {
        $query = $this->connection->select('*')->from($this->getTable());

        if ($categoryId){
            $query->where('category_id in (SELECT category_id FROM category where category_id=?)',$categoryId);
        }

        /*if ($available!==null){
            $query->where(['av']) Možná
        }*/
        $query->orderBy('title');

        return $this->createEntities($query->fetchAll($offset,$limit));
       // return $this->createEntities($query->fetchAll(2,2));
    }

    public function findCountByCategoryAndAvailable(?int $categoryId=null, ?bool $available=null, ?int $offset=null, ?int $limit=null):int
    {
        $query = $this->connection->select('count(*) as pocet')->from($this->getTable());

        if ($categoryId){
            $query->where('category_id in (SELECT category_id FROM category where category_id=?)',$categoryId);
        }

        /*if ($available!==null){
            $query->where(['av']) Možná
        }*/
        $query->orderBy('title');

        return $query->fetchSingle();
    }


}