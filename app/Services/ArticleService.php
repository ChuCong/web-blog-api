<?php

namespace App\Services;

use App\Repositories\ArticleRepository;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Str;
// use Illuminate\Support\Facades\URL;

class ArticleService
{
    protected $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function getAll()
    {
        return $this->articleRepository->getAll();
    }

    public function getListArticleAdmin($limit, $page, $title)
    {
        $limit = $limit ?? 10;
        $page = $page ?? 1;
        $title = $title ?? null;
        return $this->articleRepository->getListArticle($limit, $page, $title);
    }

    public function getListArticleApi($limit, $page)
    {
        $limit = $limit ?? 10;
        $page = $page ?? 1;
        return $this->articleRepository->getListArticleApi($limit, $page);
    }

    public function create(array $data)
    {
        return $this->articleRepository->create($data);
    }

    public function find($id)
    {
        return $this->articleRepository->getById($id);
    }

    public function update($id, array $data)
    {
        return $this->articleRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->articleRepository->delete($id);
    }

    public function getBySlug($slug)
    {
        return $this->articleRepository->getBySlug($slug);
    }
    public function getByCategoryId($slug)
    {
        $article = $this->articleRepository->getBySlug($slug);
        if ($article !== null) {
            return $this->articleRepository->getByCategoryId($article->category_id);
        }
        return [];
    }
}
