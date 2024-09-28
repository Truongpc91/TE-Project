<?php

namespace App\Services;

use App\Models\User;
use App\Services\Interfaces\SystemServiceInterface;
use App\Repositories\Interfaces\SystemReponsitoryInterface as systemRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Class UserService
 * @package App\Services
 */
class SystemService implements SystemServiceInterface
{
    const PATH_UPLOAD = 'systems';

    protected $systemRepository;

    public function __construct(systemRepository $systemRepository)
    {
        $this->systemRepository = $systemRepository;
    }

    public function save($request, $languageId)
    {
        DB::beginTransaction();
        try {
            $config1 = $request->input('config');
            $config2 = $request->file('config');
            $data = [];
            if ($config2 != null) {
                foreach ($config2 as $key => $value) {
                    $data[] = [
                        $key => Storage::put(self::PATH_UPLOAD, $value),
                    ];
                }
            }
            $config3 = array_merge(...$data);
            $config = array_merge($config1, $config3);
            $payload = [];
            if (count($config)) {
                foreach ($config as $key => $val) {
                    $payload[] = [
                        'keyword' => $key,
                        'content' => $val,
                        'language_id' => $languageId,
                        'user_id' => Auth::user()->id
                    ];
                }
            }
            $condition = ['keyword' => $key, 'language_id' => $languageId];
            $this->systemRepository->updateOrInsert($payload, $condition);
            // dd($payload);
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
