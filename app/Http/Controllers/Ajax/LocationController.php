<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\DistrictReponsitoryInterface as DistrictRepository;
use App\Repositories\Interfaces\ProvinceReponsitoryInterface as ProvinceReponsitory;


class LocationController extends Controller
{
    protected $districtRepository;
    protected $provinceReponsitory;

    public function __construct(DistrictRepository $districtRepository, ProvinceReponsitory $ProvinceReponsitory)
    {
        $this->districtRepository = $districtRepository;
        $this->provinceReponsitory = $ProvinceReponsitory;
    }

    public function getLocation(Request $request)
    {
        // $province_id = $request->input('province_id');
        $html = '';

        $get = $request->input();
        if ($get['target'] == 'districts') {
            $provinces = $this->provinceReponsitory->findById($get['data']['location_id'], ['code', 'name'], ['districts']);
            $html = $this->renderHtml($provinces->districts);
        } else if ($get['target'] == 'wards') {
           $district = $this->districtRepository->findById($get['data']['location_id'], ['code', 'name'], ['wards']);
           $html = $this->renderHtml($district->wards, '[Chọn Phường/Xã]');

        }

        // $districts = $this->districtRepository->findDistrictByProvinceId($province_id);

        $response = [
            'html' => $html
        ];

        return response()->json($response);
    }

    public function renderHtml($districts, $root = '[Chọn Quận/Huyện]')
    {
        $html = '<option value="0">'.$root.'</option>';
        foreach ($districts as $district) {
            $html .= '<option value="' . $district->code . '">' . $district->name . '</option>';
        }

        return $html;
    }
}
