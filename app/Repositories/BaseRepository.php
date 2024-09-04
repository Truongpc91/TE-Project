<?php

namespace App\Repositories;

use App\Models\User;
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
        array $join = [],
        array $extend = [],
        int $perPage = 5,
        array $relations = []
    ){
        $query = $this->model->select($column)->where(function($query) use ($condition){
            if(isset($condition['keyword']) && !empty($condition['keyword'])){
                $query->where('name', 'LIKE', '%'.$condition['keyword'].'%');
            }
        });

        if(isset($relations) && !empty($relations)){
            foreach($relations as $relation){
                $query->withCount($relation);
            }
        }

        if(isset($condition['publish']) && $condition['publish'] != 0){
            $query->where('publish', '=', $condition['publish']);
        }

        if(!empty($join)){
            $query->join(...$join);
        }

        return $query->paginate($perPage)->withQueryString()->withPath(env('APP_URL').$extend['path']);
    }

    public function create($data){
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

    public function findById(int $modelId, array $column = ['*'], array $relation = []){
        return $this->model->select($column)->with($relation)->findOrFail($modelId);  
    }

    public function createLanguagePivot($model, array $payload = [])
    {
        // dd($payload);

        return $model->languages()->attach($model->id, $payload);
    }
}
