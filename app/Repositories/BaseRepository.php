<?php

namespace App\Repositories;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserService
 * @package App\Services
 */
class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function pagination(
        array $column = ['*'],
        array $condition = [],
        int $perPage = 5,
        array $extend = [],
        array $orderBy = ['id', 'DESC'],
        array $join = [],
        array $relations = [],
        array $rawQuery = [],
    ) {
        $query = $this->model->select($column);
        return $query
            ->keyword($condition['keyword'] ?? null)
            ->publish($condition['publish'] ?? null)
            ->relationCount($relations ?? null)
            ->CustomWhere($condition['where'] ?? null)
            ->customWhereRaw($rawQuery['whereRaw'] ?? null)
            ->customJoin($join ?? null)
            ->customGroupBy($extend['groupBy'] ?? null)
            ->customOrderBy($orderBy ?? null)
            ->paginate($perPage)
            ->withQueryString()->withPath(env('APP_URL') . $extend['path']);
    }

    public function create($data)
    {
        $model = $this->model->create($data);
        return $model->fresh();
    }

    public function createRoute($data)
    {
        return $this->model->create($data);
    }

    public function createBatch(array $payload = [])
    {
        return $this->model->insert($payload);
    }

    public function update($model, $data)
    {
        $query = (is_numeric($model) ? $this->findById($model) : $model);
        $query->fill($data);
        $query->save();
        return $query;
    }

    public function updateByWhereIn(string $whereInField = '', array $whereIn = [], $data)
    {
        return $this->model->whereIn($whereInField, $whereIn)->update($data);
    }

    public function updateByWhere($condition = [], array $payload = [])
    {
        $query = $this->model->newQuery();
        foreach ($condition as $key => $val) {
            $value = $query->where($val[0], $val[1], $val[2])->get();
        }

        return $query->update($payload);
    }

    public function updateOrInsert(array $payload = [], array $condition = [])
    {
        return $this->model->upsert($payload, $condition);
    }

    public function destroy($model)
    {
        return $model->delete();
    }

    public function forceDelete(int $id = 0)
    {
        return $this->findById($id)->forceDelete();
    }

    public function forceDeleteByCondition(array $condition = [])
    {
        $query = $this->model->newQuery();
        foreach ($condition as $key => $val) {
            $query->where($val[0], $val[1], $val[2]);
        }
        return $query->forceDelete();
    }

    public function all(array $relation = [])
    {
        return $this->model->with($relation)->get();
    }

    public function findById(
        int $modelId,
        array $column = ['*'],
        array $relation = []
    ) {
        return $this->model->select($column)->with($relation)->findOrFail($modelId);
    }

    public function findByCondition(
        $condition = [],
        $flag = false,
        $relation = [],
        $orderBy = ['id', 'desc'],
        array $param = []
    ) {
        $query = $this->model->newQuery();
        foreach ($condition as $key => $val) {
            $query->where($val[0], $val[1], $val[2]);
        }

        if(isset($param['whereIn'])){
            $query->whereIn($param['whereInField'], $param['whereIn']);
        }
        $query->with($relation);
        $query->orderBy($orderBy[0], $orderBy[1]);
        return ($flag == false) ? $query->first() : $query->get();
    }

    public function createPivot($model, array $payload = [], string $relation = '')
    {
        return $model->{$relation}()->attach($model->id, $payload);
    }

    public function findByWhereHas(array $condition = [], string $relation = '', string $alias = '', $flag = false)
    {
        return $this->model->with('languages')->WhereHas($relation, function ($query) use ($condition, $alias) {
            foreach ($condition as $key => $val) {
                $query->where($alias . '.' . $key, $val);
            }
        })->first();
    }

    public function findWidgetItem(array $condition = [], string $alias = '', int $languageId = 3)
    {
        return $this->model->with([
            'languages' => function ($query) use ($languageId) {
                $query->where('language_id', '=', $languageId);
            }
        ])->WhereHas('languages', function ($query) use ($condition, $alias) {
            foreach ($condition as $key => $val) {
                $query->where($alias . '.' . $val[0], $val[1], $val[2]);
            }
        })->get();
    }

    // updatePivot($post, $payloadLanguage, 'languages')

    // public function createRelationPivot($model, array $payload = [])
    // {
    //     // dd($payload, $model);

    //     return $model->languages()->attach($model->id, $payload);
    // }


}
