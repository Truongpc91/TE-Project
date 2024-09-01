<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserCatalogue;
use App\Repositories\Interfaces\UserCatalogueRepositoryInterface as userCatalogueRepository;
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

    public function __construct(userCatalogueRepository $userCatalogueRepository)
    {
        $this->userCatalogueRepository = $userCatalogueRepository;
    }

    public function paginate($request)
    {
        // dd(123);

        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perPage = addslashes($request->integer('per_page'));

        $userCatalogues = $this->userCatalogueRepository->pagination(
            ['*'], $condition, [], ['path' => 'admin/catalogue/index'], $perPage, ['users']);
        
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

            // dd($payload);
            $user = UserCatalogue::find($post['modelId']);

            $updateUserCatalogue = $this->userCatalogueRepository->update($user, $payload);
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
}
