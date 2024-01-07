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

final class UsersFacade {

	public function __construct(
		private UserRepository $userRepository,
		private PermissionRepository $permissionRepository,
		private RoleRepository $roleRepository,
		private ResourceRepository $resourceRepository,
		private ForgottenPasswordRepository $forgottenPasswordRepository
	){ }

	/**
	 * @throws \Exception
	 */
	public function getUser(int $id):User {
		return $this->userRepository->find($id);
	}

	public function findAllBy(?array $where = null, ?int $offset, ?int $limit): array {
		return $this->userRepository->findAllBy($where, $offset, $limit);
	}

	public function findUsers(): array {
		return $this->userRepository->findAll();
	}

	public function findBy(array $args): array {
		return $this->userRepository->findAllBy($args);
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

	/**
   * Metoda vracející "přihlašovací identitu" pro daného uživatele
   * @param User $user
   * @return SimpleIdentity
   */
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

	#region metody pro zapomenuté heslo
	/**
   * Metoda pro vygenerování a uložení nového záznamu pro obnovu hesla
   * @param User $user
   * @return ForgottenPassword
   * @throws \LeanMapper\Exception\InvalidArgumentException
   */
	public function saveNewForgottenPasswordCode(User $user):ForgottenPassword {
		$forgottenPassword=new ForgottenPassword();
		$forgottenPassword->user=$user;
		$forgottenPassword->code=Random::generate(10);
		$this->forgottenPasswordRepository->persist($forgottenPassword);
		return $forgottenPassword;
	}

	/**
   * Metoda pro ověření, zda je platný zadaný kód pro obnovu uživatelského účtu
   * @param User|int $user
   * @param string $code
   * @return bool
   */
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

	/**
   * Metoda pro jednoduché smazání kódů pro obnovu hesla pro konkrétního uživatele
   * @param User|int $user
   */
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
	#endregion metody pro zapomenuté heslo

	#region metody pro authorizator
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

	public function getCount(?array $whereArr = null): int {
		return $this->userRepository->findCountBy($whereArr);
	}
}
