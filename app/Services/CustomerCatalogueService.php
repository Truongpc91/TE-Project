<?php

namespace App\Services;

use App\Models\UserCatalogue;
use App\Repositories\Interfaces\CustomerCatalogueReponsitoryInterface as CustomerCatalogueReponsitory;
use App\Repositories\Interfaces\CustomerReponsitoryInterface as CustomerRepository;
use App\Services\Interfaces\CustomerCatalogueServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


/**
 * Class UserService
 * @package App\Services
 */
class CustomerCatalogueService implements CustomerCatalogueServiceInterface
{
    protected $CustomerCatalogueReponsitory;
    protected $customerRepository;

    public function __construct(
        CustomerCatalogueReponsitory $CustomerCatalogueReponsitory,
        CustomerRepository $customerRepository
    ){
        $this->CustomerCatalogueReponsitory = $CustomerCatalogueReponsitory;
        $this->customerRepository = $customerRepository;
    }

    public function paginate($request)
    {
        // dd(123);

        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perPage = addslashes($request->integer('per_page'));
        
        $userCatalogues = $this->CustomerCatalogueReponsitory->pagination(
            ['*'],
            $condition,
            $perPage,
            ['path' => 'admin/customer_catalogues/index'],
            ['id', 'DESC'],
            [],
            ['customers']
        );
        

        return $userCatalogues;
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only('name','description');

            $customerCatalogue = $this->CustomerCatalogueReponsitory->create($payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only('name','description');
            $updateUserCatalogue = $this->CustomerCatalogueReponsitory->update($id,$payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $customer_catalogue = $this->CustomerCatalogueReponsitory->findById($id);
            $destroyUser = $this->CustomerCatalogueReponsitory->destroy($customer_catalogue);
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
            $updateUserCatalogue = $this->CustomerCatalogueReponsitory->update($user, $payload);
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
            $flag = $this->CustomerCatalogueReponsitory->updateByWhereIn('id', $post['id'], $payload);
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

            $this->customerRepository->updateByWhereIn('user_catalogue_id', $array, $payload);
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
                    $userCatalogue = $this->CustomerCatalogueReponsitory->findById($key);
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
