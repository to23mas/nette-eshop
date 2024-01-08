<?php

namespace App\Model\Facades;

use App\Model\Repositories\SizeRepository;
use App\Model\Entities\Size;
use Nette\Http\FileUpload;
use Nette\Utils\Strings;


/**
 * Class SizeFacade
 * @package App\Model\Facades
 */
class SizeFacade{

    /** @var SizeRepository $SizeRepository  */
    private $SizeRepository;

    public function saveComment(Size $comment):bool{
        return (bool)$this->SizeRepository->persist($comment);

    }
    public function __construct(SizeRepository $SizeRepository){
        $this->SizeRepository = $SizeRepository;
    }

    public function findComment(int $id){
        return $this->SizeRepository->find($id);
    }

    /**
     * Metoda pro načtení jednoho komentáře
     * @param int $id
     * @return Size
     * @throws \Exception
     */
    public function getSize(int $id):Size {
        return $this->SizeRepository->find($id); //buď počítáme s možností vyhození výjimky, nebo ji ošetříme už tady a můžeme vracet např. null
    }

    /**
     * Metoda pro načtení komentářů u produktu
     * @param int $id id produktu
     * @return Size
     * @throws \Exception
     */
    public function findSizeByProductId(int $productId) {
        return $this->SizeRepository->findSizeByProductId($productId);
    }

    /**
     * Metoda pro vyhledání kategorií
     * @param array|null $params = null
     * @param int $offset = null
     * @param int $limit = null
     * @return Size[]
     */
    public function findSize(array $params=null,int $offset=null,int $limit=null):array {
        return $this->SizeRepository->findAllBy($params,$offset,$limit);
    }

    /**
     * Metoda pro smazání komentáře
     * @param Size $comment
     * @return bool
     */
    public function deleteComment(Size $comment):bool {
        try{
            return (bool)$this->SizeRepository->delete($comment);
        }catch (\Exception $e){
            return false;
        }
    }
}