<?php

namespace App\Repositories;

interface RepositoryInterface
{
    public function getAll();

    public function getById($id);

    public function create($attibutes = []);

    public function update($id, $attibutes = []);

    public function delete($id);

    public function getByRelation($column, $relation);

    public function findWhere($where, $columns = ['*'], $or = false);

    public function paginate($number);

    public function findWhereIn($field, array $values, $columns = ['*']);
}
