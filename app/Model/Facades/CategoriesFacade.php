<?php

namespace App\Model\Facades;

use App\Model\Entities\Category;
use App\Model\Repositories\CategoryRepository;

class CategoriesFacade {
	private CategoryRepository $categoryRepository;

	public function __construct(CategoryRepository $categoryRepository){
		$this->categoryRepository=$categoryRepository;
	}

	/**
	 * @throws \Exception
	 */
	public function getCategory(int $id):Category {
		return $this->categoryRepository->find($id);
	}

	public function getIdByTitle(string $title): int
	{
		return $this->categoryRepository->getIdByTitle($title)->fetchSingle();
	}

	/**
	 * @return Category[]
	 */
	public function findCategories(?array $params=null,?int $offset=null,?int $limit=null):array {
		return $this->categoryRepository->findAllBy($params,$offset,$limit);
	}

	public function findCategoriesCount(?array $params=null):int {
		return $this->categoryRepository->findCountBy($params);
	}

	public function saveCategory(Category &$category):bool {
		return (bool)$this->categoryRepository->persist($category);
	}

	public function create(string $title, string $desc): void
	{
		$this->categoryRepository->create($title, $desc);
	}

	public function deleteById(int $categoryId): void
	{
		$this->categoryRepository->deleteById($categoryId);
	}


	public function deleteCategory(Category $category):bool {
		try{
			return (bool)$this->categoryRepository->delete($category);
		}catch (\Exception $e){
			return false;
		}
	}

}
