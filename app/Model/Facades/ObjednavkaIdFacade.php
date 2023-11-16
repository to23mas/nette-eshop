<?php

namespace App\Model\Facades;

use App\Model\Entities\ObjednavkaId;
use App\Model\Repositories\ObjednavkaIdRepository;

class ObjednavkaIdFacade
{
    /** @var ObjednavkaIdRepository $ObjednavkaIdRepository  */
    private $objednavkaIdRepository;

    public function saveComment(ObjednavkaId $objednavkaId):bool{
        return (bool)$this->objednavkaIdRepository->persist($objednavkaId);

    }
    public function __construct(ObjednavkaIdRepository $objednavkaIdRepository){
        $this->objednavkaIdRepository = $objednavkaIdRepository;
    }

    public function findId(int $id){
        return $this->objednavkaIdRepository->find($id);
    }

    /**
     * Metoda pro načtení jednoho komentáře
     * @param int $id
     * @return Comments
     * @throws \Exception
     */
    public function getId(int $id){
        return $this->objednavkaIdRepository->find($id); //buď počítáme s možností vyhození výjimky, nebo ji ošetříme už tady a můžeme vracet např. null
    }



}