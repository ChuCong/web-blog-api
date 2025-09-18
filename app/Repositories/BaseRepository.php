<?php

namespace App\Repositories;

use App\Repositories\RepositoryInterface;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var array
     */
    private $allowed_operator = ['>', '>=', '=', '!=', '<>', '<', '<=', 'like', 'not like', 'in', 'not in', 'Null', 'NotNull'];

    /**
     * @var array
     */
    private $allowed_order = ["asc", "desc"];

    protected $model;
    protected $query;
    protected $skipCriteria = false;
    protected $criteria;

    public function __construct()
    {
        $this->setModel();
    }

    public function setModel()
    {
        $this->model = app()->make(
            $this->getModel()
        );
    }

    abstract public function getModel();

    public function getAll()
    {
        return $this->model->all();
    }

    public function getById($id)
    {
        $result = $this->model->findOrFail($id);

        return $result;
    }

    public function create($attibutes = [])
    {
        return $this->model->create($attibutes);
    }

    public function update($id, $attibutes = [])
    {
        $result = $this->getById($id);
        if ($result) {
            $result->update($attibutes);
            return $result;
        }

        return false;
    }

    public function updateOrCreate($id, array $data)
    {
        return $this->model->updateOrCreate(['id' => $id], $data);
    }

    public function delete($id)
    {
        $result = $this->getById($id);
        if ($result) {
            $result->delete();

            return true;
        }

        return false;
    }

    public function deleteAll(array $conditions)
    {
        return $this->model->where($conditions)->delete();
    }

    public function getByRelation($column, $relation)
    {
        $result = $this->model->where($column, $relation)->paginate(config('numbers.paginate'));

        return $result;
    }

    public function findWhere($where, $columns = ['*'], $or = false)
    {
        $model = $this->model;

        foreach ($where as $field => $value) {
            if ($value instanceof \Closure) {
                $model = (!$or)
                    ? $model->where($value)
                    : $model->orWhere($value);
            } elseif (is_array($value)) {
                if (count($value) === 3) {
                    list($field, $operator, $search) = $value;
                    $model = (!$or)
                        ? $model->where($field, $operator, $search)
                        : $model->orWhere($field, $operator, $search);
                } elseif (count($value) === 2) {
                    list($field, $search) = $value;
                    $model = (!$or)
                        ? $model->where($field, '=', $search)
                        : $model->orWhere($field, '=', $search);
                }
            } else {
                $model = (!$or)
                    ? $model->where($field, '=', $value)
                    : $model->orWhere($field, '=', $value);
            }
        }
        return $model;
    }

    public function paginate($limit = null)
    {
        return $this->model->orderBy('created_at', 'DESC')->paginate($limit);
    }

    public function findWhereIn($field, array $values, $columns = ['*'])
    {
        return $this->model->whereIn($field, $values)->get($columns);
    }

    public function findNotIn($field, array $values, $columns = ['*'])
    {
        $model = $this->model->whereNotIn($field, $values);

        return $model->get($columns);
    }

    public function findWhereInPaginate($field, array $values, $limit)
    {
        $model = $this->model->whereIn($field, $values)
            ->latest()
            ->paginate($limit);

        return $model;
    }

    public function findByWhereInAndWhere($field1, array $values, $field2, $value2)
    {
        $model = $this->model->whereIn($field1, $values)->where([$field2 => $value2]);

        return $model;
    }
    public function search($field, $value)
    {
        $model = $this->model
            ->where($field, 'like', '%' . $value . '%');

        return $model;
    }
    public function createImage($attibutes = [], $foreignId)
    {
        return $this->model->create($attibutes, $foreignId);
    }

    public function getLimit($limit)
    {
        return $this->model->orderBy('created_at', 'DESC')->skip(0)->take($limit)->get();
    }

    public function select($columns = ['*'])
    {
        return $this->model->select($columns)->get();
    }

    public function countWhere(array $condition = [])
    {
        $this->addCondition($condition);
        return $this->model->count();
    }

    public function validateIdExsit($id)
    {
        $model = $this->model;
        $data = $model::pluck('id')->toArray();
        if (in_array($id, $data)) {
            return true;
        }

        return false;
    }

    /**
     * @param array $conditions
     * @return bool|mixed|null
     */
    protected function addCondition(array $conditions = [])
    {
        $this->validateCondition($conditions);

        foreach ($conditions as $condition) {

            $attribute = $condition[0];
            $operator = $condition[1];
            $value = null;
            if (isset($condition[2])) {
                $value = $condition[2];
            }
            if ($operator == "=") {
                $this->model = $this->model->where($attribute, "=", $value);
            }

            if ($operator == ">") {
                $this->model = $this->model->where($attribute, ">", $value);
            }

            if ($operator == ">=") {
                $this->model = $this->model->where($attribute, ">=", $value);
            }

            if ($operator == "<") {
                $this->model = $this->model->where($attribute, "<", $value);
            }

            if ($operator == "<=") {
                $this->model = $this->model->where($attribute, "<=", $value);
            }

            if ($operator == "<>") {
                $this->model = $this->model->where($attribute, "<>", $value);
            }

            if ($operator == "!=") {
                $this->model = $this->model->where($attribute, "!=", $value);
            }

            if ($operator == "in") {
                $this->model = $this->model->whereIn($attribute, $value);
            }

            if ($operator == "not int") {
                $this->model = $this->model->whereNotIn($attribute, $value);
            }

            if ($operator == "like") {
                $this->model = $this->model->where($attribute, "like", $value);
            }

            if ($operator == "not like") {
                $this->model = $this->model->where($attribute, "not like", $value);
            }

            if ($operator == "Null") {
                $this->model = $this->model->whereNull($attribute);
            }

            if ($operator == "NotNull") {
                $this->model = $this->model->whereNotNull($attribute);
            }
        }

        return $this->model;
    }

    /**
     * @param array $conditions
     * @return boolean
     */
    private function validateCondition(array $conditions = [])
    {
        foreach ($conditions as $condition) {
            if (!is_array($condition)) {
                die("condition error");
            }

            $attribute = $condition[0];
            $operator = $condition[1];

            if (!in_array($operator, $this->allowed_operator)) {
                die("condition error");
            }
        }

        return true;
    }

    private function validateOrderBy(array $orderBy = [])
    {
        $check = true;
        if (!$orderBy || !is_array($orderBy)) {
            $check = false;
        }

        if (!isset($orderBy[0]) || !isset($orderBy[1])) {
            $check = false;
        }

        $order = isset($orderBy[1]) ? $orderBy[1] : '';
        if (!in_array($order, $this->allowed_order)) {
            $check = false;
        }

        return $check;
    }

    protected function orderBy(array $orderBys = [])
    {

        //$orderBy is a empty array
        if (!$orderBys || !is_array($orderBys)) {
            return $this->model;
        }

        if (!isset($orderBys[0]) || !is_array($orderBys[0])) {
            $orderBys = [
                0 => $orderBys,
            ];
        }

        foreach ($orderBys as $orderBy) {
            $check = $this->validateOrderBy($orderBy);
            if (!$check) {
                continue;
            }
            $attribute = $orderBy[0];
            $order = $orderBy[1];
            $this->model = $this->model->orderBy($attribute, $order);
        }

        return $this->model;
    }
    protected function getQueryBuilder()
    {
        return $this->query = $this->model->newQuery();
    }
    public function findOne($attribute, $value, array $columns = ['*'])
    {
        return $this->getQueryBuilder()->where($attribute, "=", $value)->first($columns);
    }

    public function findWhereOne(array $conditions = [], array $with = [], array $columns = ['*'], int $limit = 20, int $offset = 0, array $orderBy = [])
    {
        $this->getQueryBuilder();
        $this->addCondition($conditions);

        $this->query->when($offset, function ($query) use ($offset) {
            $query->offset($offset);
        });
        $this->query->when($limit, function ($query) use ($limit) {
            $query->limit($limit);
        });
        $this->query->when(!empty($with), function ($query) use ($with) {
            $query->with($with);
        });

        $result = $this->orderBy($orderBy)->first($columns);
        if ($result) {
            return $result;
        }
        return null;
    }

    public function findWhereWithColumns(
        array $conditions = [],
        array $withHasSelectColumns = [],
        array $columns = ['*'],
        int|null $limit = 20,
        int|null $offset = 0,
        array $orderBy = []
    ) {
        $this->getQueryBuilder();
        $this->addCondition($conditions);

        $this->query->when($offset, function ($query) use ($offset) {
            $query->offset($offset);
        });
        $this->query->when($limit, function ($query) use ($limit) {
            $query->limit($limit);
        });
        $this->query->when(!empty($withHasSelectColumns), function ($query) use ($withHasSelectColumns) {
            foreach ($withHasSelectColumns as $table => $columns) {
                if (is_array($columns)) { //Exam for this case: $with = ['user' => ['uuid', 'name'], 'exam' => ['slug', 'name']]
                    $query->with([$table => function ($query) use ($columns) {
                        $query->select($columns);
                    }]);
                } else {
                    $query->with($columns); //in normal cass: $table is index (0, 1 ..) and $columns will be name of with relationship
                    //Ex: $with = ['user','exam']
                }
            }
        });
        $result = $this->orderBy($orderBy)->get($columns);
        if ($result && count($result) > 0) {
            return $result;
        } else {
            return array();
        }
    }

    public function findWhereOneWithColumns(array $conditions = [], array $withHasSelectColumns = [], array $columns = ['*'], int $limit = 20, int $offset = 0, array $orderBy = [])
    {
        $this->getQueryBuilder();
        $this->addCondition($conditions);

        $this->query->when($offset, function ($query) use ($offset) {
            $query->offset($offset);
        });
        $this->query->when($limit, function ($query) use ($limit) {
            $query->limit($limit);
        });
        $this->query->when(!empty($withHasSelectColumns), function ($query) use ($withHasSelectColumns) {
            foreach ($withHasSelectColumns as $table => $columns) {
                if (is_array($columns)) { //Exam for this case: $with = ['user' => ['uuid', 'name'], 'exam' => ['slug', 'name']]
                    $query->with([$table => function ($query) use ($columns) {
                        $query->select($columns);
                    }]);
                } else {
                    $query->with($columns); //in normal cass: $table is index (0, 1 ..) and $columns will be name of with relationship
                    //Ex: $with = ['user','exam']
                }
            }
        });

        $result = $this->orderBy($orderBy)->first($columns);
        if ($result) {
            return $result;
        }
        return null;
    }

    public function deleteWhere($attribute, $value)
    {
        $model = $this->getQueryBuilder()->where($attribute, '=', $value)->first();
        if (isset($model)) {
            return $model->delete();
        }
    }
}
