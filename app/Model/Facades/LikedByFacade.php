<?php

namespace App\Model\Facades;
use App\Model\Repositories\LikedByRepository;
use App\Model\Entities\LikedBy;
class LikedByFacade
{
    /** @var LikedByRepository $likedByRepository  */
    private $likedByRepository;

    public function __construct(LikedByRepository $likedByRepository){
        $this->likedByRepository = $likedByRepository;
    }

    public function liked(LikedBy $like):bool{
        return (bool)$this->likedByRepository->persist($like);
    }



}