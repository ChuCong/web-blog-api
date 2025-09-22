<?php

namespace App\Services;
use App\Repositories\CategoryRepository;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Str;
// use Illuminate\Support\Facades\URL;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAll()
    {
        return $this->categoryRepository->getAll();
    }

    public function getListCategoryAdmin($limit, $page, $title)
    {
        $limit = $limit ?? 10;
        $page = $page ?? 1;
        $title = $title ?? null;
        return $this->categoryRepository->getListCategory($limit, $page, $title);
    }

    public function getListCategory($limit, $page)
    {
        $limit = $limit ?? 10;
        $page = $page ?? 1;
        return $this->categoryRepository->getListTitleCategory($limit, $page);
    }
    
    public function create(array $data)
    {
        return $this->categoryRepository->create($data);
    }

    public function find($id)
    {
        return $this->categoryRepository->getById($id);
    }

    public function update($id, array $data)
    {
        return $this->categoryRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->categoryRepository->delete($id);
    }

    public function getBySlug($slug , $limit, $page)
    {
        $limit = $limit ?? 10;
        $page = $page ?? 1;
        return $this->categoryRepository->getBySlug($slug, $limit, $page);
    }
}
