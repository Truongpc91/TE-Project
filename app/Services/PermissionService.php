<?php

namespace App\Services;

use App\Models\Permission;
use App\Repositories\Interfaces\PermissionReponsitoryInterface as PermissionReponsitory;
use App\Services\Interfaces\PermissionServiceInterface;
use Illuminate\Support\Facades\DB;


/**
 * Class UserService
 * @package App\Services
 */
class PermissionService implements PermissionServiceInterface
{
    protected $permissionReponsitory;

    public function __construct(PermissionReponsitory $permissionReponsitory)
    {
        $this->permissionReponsitory = $permissionReponsitory;
    }

    public function paginate($request)
    {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
       
        $perPage = addslashes($request->integer('per_page'));

        $permissions = $this->permissionReponsitory->pagination(
            ['*'],
            $condition,
            $perPage,
            ['path' => 'admin/permissions/index'],
            ['id','ASC',],
            [],
            [],
        );
           
        return $permissions;
    }

    public function create($data)
    {
        DB::beginTransaction();
        try {
            $permission = $this->permissionReponsitory->create($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function update($data, $permission)
    {
        DB::beginTransaction();
        try {

            $updateUserCatalogue = $this->permissionReponsitory->update($permission, $data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function destroy($permission)
    {
        DB::beginTransaction();
        try {
            $destroyPermission = $this->permissionReponsitory->destroy($permission);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function updateStatus($post = [])
    {
        DB::beginTransaction();
        try {
            // dd($post);   
            $payload[$post['field']] = (($post['value'] == 1) ? 2 : 1);
            $permission = Permission::find($post['modelId']);
            $updateLanguage = $this->permissionReponsitory->update($permission, $payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function updateStatusAll($post = [])
    {
        DB::beginTransaction();
        try {
            $payload[$post['field']] = $post['value'];
            $flag = $this->permissionReponsitory->updateByWhereIn('id', $post['id'], $payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

}
