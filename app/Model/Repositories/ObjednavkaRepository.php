<?php

namespace App\Model\Repositories;


/**
 * Class CommentsRepository
 * @package App\Model\Repositories
 */
class ObjednavkaRepository extends BaseRepository
{
    public function selectNextId():Array{
        $text = "SELECT `auto_increment` FROM INFORMATION_SCHEMA.TABLES WHERE table_name = 'objednavka'";
        return $this->connection->fetchAll($text);

    }
}