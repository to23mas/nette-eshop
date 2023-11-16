<?php

namespace App\Model\Authorization;

use App\Model\Entities\Category;
use App\Model\Entities\Comments;
use App\Model\Entities\User;
use App\Model\Entities\Objednavka;
use App\Model\Entities\Permission;
use App\Model\Facades\UsersFacade;
use Nette\Security\Role;

/**
 * Class Authorizator
 * @package App\Model\Authorization
 */
class Authorizator extends \Nette\Security\Permission {

    /**
     * Metoda pro ověření uživatelských oprávnění
     * @param Role|string|null $role
     * @param \Nette\Security\Resource|string|null $resource
     * @param string|null $privilege
     * @return bool
     */
    public function isAllowed($role=self::ALL, $resource=self::ALL, $privilege=self::ALL):bool {

        //TODO tady mohou být kontroly pro jednotlivé entity
        if ($resource instanceof Category){
            return $this->categoryResourceIsAllowed($role, $resource, $privilege);
        }

        if ($resource instanceof Comments){
            return $this->commentsResourceIsAllowed($role, $resource, $privilege);
        }

        if ($resource instanceof User){
            return $this->usersResourceIsAllowed($role, $resource, $privilege);
        }

        if ($resource instanceof Objednavka){
            return $this->usersResourceIsAllowed($role, $resource, $privilege);
        }

        return parent::isAllowed($role, $resource, $privilege);
    }

    private function categoryResourceIsAllowed($role, Category $resource, $privilege){
        switch ($privilege){
            case 'delete':
                //TODO kontrola, jestli jsou v kategorii nějaké produkty - pokud ano, nesmažeme ji
        }
        //když nebyl odchycen konkrétní stav, vrátíme výchozí hodnotu oprávnění (případně bychom se mohli ptát také na resource Front:Category či Admin:Category)
        return parent::isAllowed($role, 'Category', $privilege);
    }

    private function commentsResourceIsAllowed($role, Comments $resource, $privilege){
        switch ($privilege){
            case 'delete':
                //TODO kontrola, jestli jsou v kategorii nějaké produkty - pokud ano, nesmažeme ji
        }
        //když nebyl odchycen konkrétní stav, vrátíme výchozí hodnotu oprávnění (případně bychom se mohli ptát také na resource Front:Category či Admin:Category)
        return parent::isAllowed($role, 'Comments', $privilege);
    }

    private function usersResourceIsAllowed($role, User $resource, $privilege){
        switch ($privilege){
            case 'delete':
                //TODO kontrola, jestli jsou v kategorii nějaké produkty - pokud ano, nesmažeme ji
        }
        //když nebyl odchycen konkrétní stav, vrátíme výchozí hodnotu oprávnění (případně bychom se mohli ptát také na resource Front:Category či Admin:Category)
        return parent::isAllowed($role, 'Users', $privilege);
    }

    private function objednavkaResourceIsAllowed($role, Objednavka $resource, $privilege){
        switch ($privilege){
            case 'delete':
                //TODO kontrola, jestli jsou v kategorii nějaké produkty - pokud ano, nesmažeme ji
        }
        //když nebyl odchycen konkrétní stav, vrátíme výchozí hodnotu oprávnění (případně bychom se mohli ptát také na resource Front:Category či Admin:Category)
        return parent::isAllowed($role, 'Objednavka', $privilege);
    }


    /**
     * Authorizator constructor - načte kompletní strukturu oprávnění
     * @param UsersFacade $usersFacade
     */
    public function __construct(UsersFacade $usersFacade){
        foreach ($usersFacade->findResources() as $resource){
            $this->addResource($resource->resourceId);
        }

        foreach ($usersFacade->findRoles() as $role){
            $this->addRole($role->roleId);
        }

        foreach ($usersFacade->findPermissions() as $permission){
            if ($permission->type==Permission::TYPE_ALLOW){
                $this->allow($permission->roleId,$permission->resourceId,$permission->action?$permission->action:null);
            }else{
                $this->deny($permission->roleId,$permission->resourceId,$permission->action?$permission->action:null);
            }
        }
    }

}