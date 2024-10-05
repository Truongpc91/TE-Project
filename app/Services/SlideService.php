<?php

namespace App\Services;

use App\Models\User;
use App\Services\Interfaces\SlideServiceInterface;
use App\Repositories\Interfaces\SlideRepositoryInterface as slideRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Class UserService
 * @package App\Services
 */
class SlideService extends BaseService implements SlideServiceInterface
{
    const PATH_UPLOAD = 'slides';
    protected $slideRepository;

    public function __construct(slideRepository $slideRepository)
    {
        $this->slideRepository = $slideRepository;
    }

    public function paginate($request)
    {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perPage = addslashes($request->integer('per_page'));

        $users = $this->slideRepository->pagination(
            ['*'],
            $condition,
            $perPage,
            ['path' => 'admin/slide/index'],
            ['id', 'DESC'],
            [],
            [],
        );
        // dd($users);
        return $users;
    }

    public function create($request, $languageId)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only('name', 'keyword', 'setting', 'short_code');
            $payload['item'] = $this->handleSlideItem($request, $languageId);

            $slide = $this->slideRepository->create($payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function update($request, $id, $languageId)
    {
        DB::beginTransaction();
        try {
           
            $payload = $request->only('name', 'keyword', 'setting', 'short_code');
            $payload['item'] = $this->handleSlideItem($request, $languageId);
            if(!empty($request->file('slide'))){
                $removeImage = $this->removeImage($id, $languageId);
            }
            $slide = $this->slideRepository->findById($id);
            $updateUser = $this->slideRepository->update($slide, $payload);
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
            $destroyUser = $this->slideRepository->destroy($user);
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

            $updateUser = $this->slideRepository->update($user, $payload);
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

            $flag = $this->slideRepository->updateByWhereIn('id', $post['id'], $payload);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function convertSlideArray(array $slide = []): array{
        $temp = [];
        $fields = ['image', 'description', 'window', 'canonical', 'name', 'alt'];

        foreach ($slide as $key => $val) {
            foreach ($fields as $field) {
                $temp[$field][] = $val[$field];
            }
        }
        return $temp;
    }

    private function removeImage($id,$languageId) {
        $slide = $this->slideRepository->findById($id);

        foreach ($slide->item[$languageId] as $key => $value) {
            if(Storage::exists($value['image'])){
                Storage::delete($value['image']);
            }
        }

        return true;
    }

    private function handleSlideItem($request, $languageId)
    {
        $slide1 = $request->file('slide');
        $slide2 = $request->input('slide');
        $slide = array_merge($slide1, $slide2);

        $temp = [];

        foreach ($slide['image'] as $key => $val) {
            $temp[$languageId][] = [
                'image' =>  Storage::put(self::PATH_UPLOAD, $val),
                'name' => $slide['name'][$key],
                'description' => $slide['description'][$key],
                'canonical' => $slide['canonical'][$key],
                'alt' => $slide['alt'][$key],
                'window' => (isset($slide['window'][$key])) ? $slide['window'][$key] : '',
            ];
        }

       return $temp;
    }
}
