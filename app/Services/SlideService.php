<?php

namespace App\Services;

use App\Models\User;
use App\Services\Interfaces\SlideServiceInterface;
use App\Repositories\Interfaces\SlideRepositoryInterface as slideRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserService
 * @package App\Services
 */
class SlideService implements SlideServiceInterface
{
    protected $slideRepository;

    public function __construct(slideRepository $slideRepository){
        $this->slideRepository = $slideRepository;
    }

    public function paginate($request){
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perPage = addslashes($request->integer('per_page'));

        $users = $this->slideRepository->pagination(
            ['*'],
            $condition,
            $perPage,
            ['path' => 'admin/slide/index'],
            ['id', 'DESC'],
            [
                
            ],
            [],
            ); 
            // dd($users);
        return $users;
    }

    public function create($data){
        DB::beginTransaction();
        try {
            $data['birthday'] = $this->convertBirthdayDate($data['birthday']);
            // dd($data);
            $user = $this->slideRepository->create($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function update($data, $user){
        DB::beginTransaction();
        try {

            $data['password'] = Hash::make($data['password']);
            $data['birthday'] = $this->convertBirthdayDate($data['birthday']);
            $updateUser = $this->slideRepository->update($user, $data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function destroy( $user){
        DB::beginTransaction();
        try {
            $destroyUser = $this->slideRepository->destroy($user);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function updateStatus($post = []){
        DB::beginTransaction();
        try {
            // dd($post);

            $payload[$post['field']] = (($post['value'] == 1) ? 2 : 1);

            // dd($payload);
            $user = User::find($post['modelId']);

            $updateUser = $this->slideRepository->update($user, $payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function updateStatusAll($post = []){
        DB::beginTransaction();
        try {
            $payload[$post['field']] = $post['value'];

            $flag = $this->slideRepository->updateByWhereIn('id', $post['id'], $payload);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

    private function convertBirthdayDate($birthday = ''){
        $carbonDate = Carbon::createFromFormat('Y-m-d', $birthday);
        $birthday = $carbonDate->format('Y-m-d H:i:s');

        return $birthday;
    }
}
