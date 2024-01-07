<?php

namespace App\Model\Facades;

use App\Model\Repositories\CommentsRepository;
use App\Model\Entities\Comments;
use Nette\Http\FileUpload;
use Nette\Utils\Strings;


/**
 * Class CommentsFacade
 * @package App\Model\Facades
 */
class CommentsFacade{

    /** @var CommentsRepository $commentsRepository  */
    private $commentsRepository;

    public function saveComment(Comments $comment):bool{
        return (bool)$this->commentsRepository->persist($comment);

    }
    public function __construct(CommentsRepository $CommentsRepository){
        $this->commentsRepository = $CommentsRepository;
    }

    public function findComment(int $id){
        return $this->commentsRepository->find($id);
    }

    /**
     * Metoda pro načtení jednoho komentáře
     * @param int $id
     * @return Comments
     * @throws \Exception
     */
    public function getComments(int $id):Comments {
        return $this->commentsRepository->find($id); //buď počítáme s možností vyhození výjimky, nebo ji ošetříme už tady a můžeme vracet např. null
    }

    /**
     * Metoda pro vyhledání kategorií
     * @param array|null $params = null
     * @param int $offset = null
     * @param int $limit = null
     * @return Comments[]
     */
    public function findComments(array $params=null,int $offset=null,int $limit=null):array {
        return $this->commentsRepository->findAllBy($params,$offset,$limit);
    }

    /**
     * Metoda pro smazání komentáře
     * @param Comments $comment
     * @return bool
     */
    public function deleteComment(Comments $comment):bool {
        try{
            return (bool)$this->commentsRepository->delete($comment);
        }catch (\Exception $e){
            return false;
        }
    }
}
