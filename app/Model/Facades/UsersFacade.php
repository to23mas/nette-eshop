<?php declare(strict_types=1);

namespace App\Model\Facades;

use App\Model\Api\Facebook\FacebookUser;
use App\Model\Authorizator\AuthenticatedRole;
use App\Model\Entities\ForgottenPassword;
use App\Model\Entities\Permission;
use App\Model\Entities\Resource;
use App\Model\Entities\Role;
use App\Model\Entities\User;
use App\Model\Repositories\ForgottenPasswordRepository;
use App\Model\Repositories\PermissionRepository;
use App\Model\Repositories\ResourceRepository;
use App\Model\Repositories\RoleRepository;
use App\Model\Repositories\UserRepository;
use LeanMapper\Exception\InvalidStateException;
use Nette\Security\SimpleIdentity;
use Nette\Utils\Random;

class UsersFacade{
	private UserRepository $userRepository;
	private PermissionRepository $permissionRepository;
	private RoleRepository $roleRepository;
	private ResourceRepository $resourceRepository;
	private ForgottenPasswordRepository $forgottenPasswordRepository;

	public function __construct(UserRepository $userRepository, PermissionRepository $permissionRepository,
		RoleRepository $roleRepository, ResourceRepository $resourceRepository,
		ForgottenPasswordRepository $forgottenPasswordRepository){
		$this->userRepository=$userRepository;
		$this->permissionRepository=$permissionRepository;
		$this->roleRepository=$roleRepository;
		$this->resourceRepository=$resourceRepository;
		$this->forgottenPasswordRepository=$forgottenPasswordRepository;
	}

	/**
   * @throws \Exception
   */
	public function getUser(int $id):User {
		return $this->userRepository->find($id);
	}

	public function findUsers(): array {
		return $this->userRepository->findAll();
	}

	/**
   * @throws \Exception
   */
	public function getUserByEmail(string $email):User {
		return $this->userRepository->findBy(['email'=>$email]);
	}

	public function saveUser(User &$user):bool {
		return (bool)$this->userRepository->persist($user);
	}

	/**
   * @throws \LeanMapper\Exception\InvalidArgumentException
   */
	public function getFacebookUserIdentity(FacebookUser $facebookUser):SimpleIdentity {
		try{
			$user = $this->userRepository->findBy(['facebook_id'=>$facebookUser->facebookUserId]);
		}catch (\Exception $e){
			try{
				$user = $this->getUserByEmail($facebookUser->email);
				$user->facebookId=$facebookUser->facebookUserId;
				$this->saveUser($user);
			}catch (\Exception $e){
				$user = new User();
				$user->name=$facebookUser->name;
				$user->email=$facebookUser->email;
				$user->role=null;
				$user->facebookId=$facebookUser->facebookUserId;
				$this->saveUser($user);
			}
		}

		return $this->getUserIdentity($user);
	}

	public function getUserIdentity(User $user):SimpleIdentity {
		//příprava pole pro seznam rolí
		$roles=[];
		//přidáme speciální roli pro přihlášené uživatele
		$roles[]=new AuthenticatedRole($user->userId);
		//přidáme další roli přiřazenou uživateli
		if (!empty($user->role)){
			$roles[]=$user->role->roleId;
		}
		//vytvoření a vrácení SimpleIdentity
		return new SimpleIdentity($user->userId,$roles,['name'=>$user->name,'email'=>$user->email]);
	}

	/**
   * @throws \LeanMapper\Exception\InvalidArgumentException
   */
	public function saveNewForgottenPasswordCode(User $user):ForgottenPassword {
		$forgottenPassword=new ForgottenPassword();
		$forgottenPassword->user=$user;
		$forgottenPassword->code=Random::generate(10);
		$this->forgottenPasswordRepository->persist($forgottenPassword);
		return $forgottenPassword;
	}

	public function isValidForgottenPasswordCode($user, string $code):bool {
		if ($user instanceof User){
			$user=$user->userId;
		}
		$this->forgottenPasswordRepository->deleteOldForgottenPasswords();
		try{
			$this->forgottenPasswordRepository->findBy(['user_id'=>$user, 'code'=>$code]);
			return true;
		}catch (\Exception $e){
			return false;
		}
	}

	public function deleteForgottenPasswordsByUser($user):void {
		try{
			if ($user instanceof User){
				$user=$user->userId;
			}
			$this->forgottenPasswordRepository->delete(['user_id' => $user]);
		}catch (InvalidStateException $e){
			//ignore error
		}
	}

	/**
   * @return Resource[]
   */
	public function findResources():array {
		return $this->resourceRepository->findAll();
	}

	/**
   * @return Role[]
   */
	public function findRoles():array {
		return $this->roleRepository->findAll();
	}

	/**
   * @return Permission[]
   */
	public function findPermissions():array {
		return $this->permissionRepository->findAll();
	}
}
