<?php

namespace App\Model\Facades;


use App\Model\Repositories\ObjednavkaRepository;

/**
 * Class ObjednavkaFacade
 * @package App\Model\Facades
 */
class ObjednavkaFacade
{

    /** @var ObjednavkaRepository $objednavkaRepository  */
    private $objednavkaRepository;

    public function saveObjednavka($objednavka):bool{
        return (bool)$this->objednavkaRepository->persist($objednavka);

    }
    public function __construct(ObjednavkaRepository $objednavkaRepository){
        $this->objednavkaRepository = $objednavkaRepository;
    }

    public function findObjednavka(int $id){
        return $this->objednavkaRepository->find($id);
    }

    /**
     * Metoda pro načtení jedné objednávky
     * @param int $id
     * @return Objednavka
     * @throws \Exception
     */
    public function getObjednavka(int $id) {
        return $this->objednavkaRepository->find($id); //buď počítáme s možností vyhození výjimky, nebo ji ošetříme už tady a můžeme vracet např. null
    }

    /**
     * Metoda pro vyhledání objednávek
     * @param array|null $params = null
     * @param int $offset = null
     * @param int $limit = null
     * @return Objednavka[]
     */
    public function findObjednavkas(array $params=null,int $offset=null,int $limit=null):array {
        return $this->objednavkaRepository->findAllBy($params,$offset,$limit);
    }

    /**
     * Metoda pro smazání objdnávky
     * @param Objednavka $objednavka
     * @return bool
     */
    public function deleteObjednavka(Objednavka $objednavka):bool {
        try{
            return (bool)$this->objednavkaRepository->delete($objednavka);
        }catch (\Exception $e){
            return false;
        }
    }

    public function getNextId(){
        $this->objednavkaRepository->selectNextId();
    }
}