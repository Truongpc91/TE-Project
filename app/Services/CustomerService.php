<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\CustomerReponsitoryInterface as customerRepository;
use App\Services\Interfaces\CustomerServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Class UserService
 * @package App\Services
 */
class CustomerService implements CustomerServiceInterface
{
    const PATH_UPLOAD = 'customers';
    protected $customerRepository;

    public function __construct(customerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function paginate($request)
    {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perPage = addslashes($request->integer('per_page'));


        $customers = $this->customerRepository->customerPagination(
            ['*'],
            $condition,
            $perPage,
            ['path' => 'admin/customer/index'],
            ['id', 'DESC'],
            [],
            ['customer_catalogues'],
        );
        return $customers;
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only($this->payload());
            $payload['birthday'] = $this->convertBirthdayDate($payload['birthday']);
            if ($request->hasFile('image')) {
                $payload['image'] = Storage::put(self::PATH_UPLOAD, $request->file('image'));
            }
            $customer = $this->customerRepository->create($payload);
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
            $payload = $request->only($this->payload());
            $customer = $this->customerRepository->findById($id);

            if ($request->hasFile('image')) {
                $payload['image'] = Storage::put(self::PATH_UPLOAD, $request->file('image'));
            }

            $currentImage = $customer->image;

            if ($request->hasFile('image') && $currentImage && Storage::exists($currentImage)) {
                Storage::delete($currentImage);
            }

            $payload['password'] = $customer->password;
            $payload['birthday'] = $this->convertBirthdayDate($payload['birthday']);
            $updateUser = $this->customerRepository->update($customer, $payload);
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
            $destroyUser = $this->customerRepository->destroy($user);
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

            // dd($payload);
            $user = User::find($post['modelId']);

            $updateUser = $this->customerRepository->update($user, $payload);
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

            $flag = $this->customerRepository->updateByWhereIn('id', $post['id'], $payload);

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

    public function customerStatistic()
    {
        return [
            'totalCustomers' => $this->customerRepository->totalCustomer(),
        ];
    }

    private function payload()
    {
        return [
            "email",
            "name",
            "customer_catalogue_id",
            "source_id",
            "birthday",
            "password",
            "province_id",
            "district_id",
            "ward_id",
            "address",
            "phone",
            "description",
        ];
    }
}
