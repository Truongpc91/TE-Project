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

    public function __construct(Model $model){
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
    ){
        // dd($condition);
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
                ->withQueryString()->withPath(env('APP_URL').$extend['path']);
    }
    
    public function create($data){
        // dd($data, $this->model);
        $model =  $this->model->create($data);
        return $model->fresh();
    }

    public function createBatch(array $payload = []){       
        return $this->model->insert($payload);
    }

    public function update($model, $data){
        // dd($model, $data);

        return $model->update($data);
    }

    public function updateByWhereIn(string $whereInField = '', array $whereIn = [], $data){
        return $this->model->whereIn($whereInField, $whereIn)->update($data);
    }

    public function updateByWhere($condition = [], array $payload = []){
        $query = $this->model->newQuery();
        foreach($condition as $key => $val){
            $value = $query->where($val[0], $val[1] , $val[2])->get();
        }

        return $query->update($payload);
    }

    public function destroy($model){
        return $model->delete();
    }

    public function all(array $relation = []){
        return $this->model->with($relation)->get();
    }

    public function findById(
        int $modelId,
        array $column = ['*'],
        array $relation = []
    ){
        return $this->model->select($column)->with($relation)->findOrFail($modelId);  
    }

    public function findByCondition($condition = []){
        $query = $this->model->newQuery();
        foreach($condition as $key => $val){
            $query->where($val[0], $val[1] , $val[2]);
        }
        return $query->first();
    }

    public function createPivot($model, array $payload = [], string $relation = ''){
        // dd($model, $payload);
        return $model->{$relation}()->attach($model->id, $payload);
    }

    // updatePivot($post, $payloadLanguage, 'languages')

    // public function createRelationPivot($model, array $payload = [])
    // {
    //     // dd($payload, $model);

    //     return $model->languages()->attach($model->id, $payload);
    // }
}
