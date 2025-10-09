<?php

namespace App\Repositories;

use App\Models\Article;
use App\Repositories\BaseRepository;

use function App\Helpers\paginate;

class ArticleRepository extends BaseRepository
{
    public function getModel()
    {
        return Article::class;
    }

    protected function getQueryBuilder()
    {
        $query = $this->model->newQuery();
        return $this->query = $query;
    }

    public function getListArticle(
        $limit,
        $page,
        $title = null,
        $sortField = 'created_at',
        $sortOrder = 'desc'
    ) {
        $query = $this->getQueryBuilder();
        if (!empty($title)) {
            $query->where('title', 'like', "%{$title}%")->select('title');
        }

        $query->orderBy($sortField, $sortOrder);
        $total = $query->count();
        $offset = ($page - 1) * $limit;
        $article = $query->skip($offset)->take($limit)->get();

        $paginate = paginate($total, $limit, $page);
        return [
            'data' => $article,
            'paginate' => $paginate
        ];
    }

    public function getListArticleApi($limit, $page, $sortField = 'created_at', $sortOrder = 'DESC')
    {
        $query = $this->getQueryBuilder()->where('active', 1)->orderBy('created_at', 'DESC');
        $query->orderBy($sortField, $sortOrder);
        $total = $query->count();
        $offset = ($page - 1) * $limit;
        $article = $query->skip($offset)->take($limit)->get();

        $paginate = paginate($total, $limit, $page);
        return [
            'data' => $article,
            'paginate' => $paginate
        ];
    }

    public function getCategoryById($id)
    {
        $query = $this->getQueryBuilder()->where('id', $id);
        return $query->first();
    }

    public function getBySlug($slug)
    {
        $query = $this->getQueryBuilder()->where('slug', $slug)->with('category');
        return $query->first();
    }

    public function getByCategoryId($categoryId)
    {
        $query = $this->getQueryBuilder()
            ->where('category_id', $categoryId)
            ->where('active', 1);
        return $query->get();
    }
}
