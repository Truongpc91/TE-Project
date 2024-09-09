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

    public function update($user, $data){
        return $user->update($data);
    }

    public function updateByWhereIn(string $whereInField = '', array $whereIn = [], $data){
        return $this->model->whereIn($whereInField,$whereIn)->update($data);
    }

    public function destroy($user){
        return $user->delete();
    }

    public function all(){
        return $this->model->all();
    }

    public function findById(
        int $modelId,
        array $column = ['*'],
        array $relation = []
    ){
        return $this->model->select($column)->with($relation)->findOrFail($modelId);  
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
