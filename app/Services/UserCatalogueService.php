<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserCatalogue;
use App\Repositories\Interfaces\UserCatalogueRepositoryInterface as userCatalogueRepository;
use App\Repositories\Interfaces\UserRepositoryInterface as UserRepository;
use App\Services\Interfaces\UserCatalogueServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserService
 * @package App\Services
 */
class UserCatalogueService implements UserCatalogueServiceInterface
{
    protected $userCatalogueRepository;
    protected $userRepository;

    public function __construct(userCatalogueRepository $userCatalogueRepository, UserRepository $userRepository)
    {
        $this->userCatalogueRepository = $userCatalogueRepository;
        $this->userRepository = $userRepository;
    }

    public function paginate($request)
    {
        // dd(123);

        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perPage = addslashes($request->integer('per_page'));

        $userCatalogues = $this->userCatalogueRepository->pagination(
            ['*'],
            $condition,
            $perPage,
            ['path' => 'admin/catalogue/index'],
            ['id', 'DESC'],
            [],
            ['users']);
           
        // dd($userCatalogues);

        return $userCatalogues;
    }

    public function create($data)
    {
        DB::beginTransaction();
        try {
            $user_catalogue = $this->userCatalogueRepository->create($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function update($data, $user)
    {
        DB::beginTransaction();
        try {
            $updateUserCatalogue = $this->userCatalogueRepository->update($user, $data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function destroy($user)
    {
        DB::beginTransaction();
        try {
            $destroyUser = $this->userCatalogueRepository->destroy($user);
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
            $user = UserCatalogue::find($post['modelId']);
            $updateUserCatalogue = $this->userCatalogueRepository->update($user, $payload);
            $this->changeUserStatus($post, $payload[$post['field']]);
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
            $flag = $this->userCatalogueRepository->updateByWhereIn('id', $post['id'], $payload);
            $this->changeUserStatus($post, $post['value']);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function changeUserStatus($post,$value){
        DB::beginTransaction();
        try {
            $array = [];
            
            if(isset($post['modelId'])){
                $array[] = $post['modelId'];
            }else{
                $array = $post['id'];
            }
            $payload[$post['field']] = $value;
            // dd($array);

            $this->userRepository->updateByWhereIn('user_catalogue_id', $array, $payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function convertBirthdayDate($birthday = '')
    {
        $carbonDate = Carbon::createFromFormat('Y-m-d', $birthday);
        $birthday = $carbonDate->format('Y-m-d H:i:s');

        return $birthday;
    }

    public function setPermission($request){
        DB::beginTransaction();
        try{
           
            $permissions = $request->input('permission');
            // dd($permissions);
            if(count($permissions)){
                foreach($permissions as $key => $val){
                    $userCatalogue = $this->userCatalogueRepository->findById($key);
                    $userCatalogue->permissions()->detach();
                    $userCatalogue->permissions()->sync($val);        
                }
            }
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
        //Mục đích là đưa được dữ liệu vào bên trong bảng user_catalogue_permission
    }
}
