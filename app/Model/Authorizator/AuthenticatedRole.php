<?php

namespace App\Model\Authorizator;

/**
 * Class AuthenticatedRole
 * @package App\Model\Authorizator
 */
class AuthenticatedRole implements \Nette\Security\Role{
  /** @var int $userId */
  public $userId;

  /**
   * AuthenticatedRole constructor.
   * @param int $userId
   */
  public function __construct(int $userId){
    $this->userId=$userId;
  }

  /**
   * @inheritDoc
   */
  function getRoleId():string{
    return 'authenticated';
  }
}