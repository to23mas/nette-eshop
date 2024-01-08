<?php

namespace App\Model\Repositories;

/**
 * Class CommentsRepository
 * @package App\Model\Repositories
 */
class CommentsRepository extends BaseRepository{

    public function findCommentsByProductId(int $productId)
    {

        $query = $this->connection->select('*')
            ->from($this->getTable())
            ->where('product_id = ',$productId);
        return $this->createEntities($query->fetchAll());
    }

}