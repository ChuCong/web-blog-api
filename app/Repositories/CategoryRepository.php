<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\BaseRepository;

use function App\Helpers\paginate;

class CategoryRepository extends BaseRepository
{
    public function getModel()
    {
        return Category::class;
    }

    protected function getQueryBuilder()
    {
        $query = $this->model->newQuery();
        return $this->query = $query;
    }

    public function getListCategory(
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
        $category = $query->skip($offset)->take($limit)->get();

        $paginate = paginate($total, $limit, $page);
        return [
            'data' => $category,
            'paginate' => $paginate
        ];
    }

    public function getListTitleCategory(
        $limit,
        $page,
        $sortField = 'created_at',
        $sortOrder = 'DESC'
    ) {
        $query = $this->getQueryBuilder()->where('active', 1)->orderBy('created_at', 'DESC');
        $query->orderBy($sortField, $sortOrder);
        $total = $query->count();
        $offset = ($page - 1) * $limit;
        $category = $query->skip($offset)->take($limit)->get();

        $paginate = paginate($total, $limit, $page);
        return [
            'data' => $category,
            'paginate' => $paginate
        ];
    }

    public function getCategoryById($id)
    {
        $query = $this->getQueryBuilder()->where('id', $id);
        return $query->first();
    }

    public function getBySlug($slug, $limit, $page)
    {
        $category = $this->getQueryBuilder()->where('slug', $slug)->first();

        if (!$category) {
            return null;
        }

        $query = $category->articles()->orderBy('created_at', 'DESC');

        $total = $query->count();
        $offset = ($page - 1) * $limit;
        $articles = $query->skip($offset)->take($limit)->get();

        $paginate = paginate($total, $limit, $page);

        return [
            'category' => $category,
            'articles' => $articles,
            'paginate' => $paginate
        ];
    }
}
