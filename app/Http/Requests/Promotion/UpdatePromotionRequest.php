<?php

namespace App\Http\Requests\Promotion;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Promotion\OrderAmountRangeRule;
use App\Rules\Promotion\ProductAndQuantityRule;
use App\Enums\PromotionEnums;
class UpdatePromotionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules =  [
            'name'      => 'required',
            'code'      => 'required|unique:promotions,id,'.$this->id.'',
            'startDate' => 'required|custom_date_format',
        ];

        $date = $this->only('startDate', 'endDate');

        if (!$this->input('neverEndDate')) {
            $rules['endDate'] = 'required|custom_date_format|custom_after:startDate';
        }

        $method = $this->only('method')['method'];
        switch ($method) {
            case PromotionEnums::ORDER_AMOUNT_RANGE:
                $rules['method'] = [new OrderAmountRangeRule($this->input('promotion_order_amount_range'))];
                break;
            case PromotionEnums::PRODUCT_AND_QUANTITY:
                $rules['method'] = [new ProductAndQuantityRule($this->only('product_and_quantity', 'object'))];
                break;
            default:
                $rules['method'] = 'required|not_in:none';
                break;
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [
            'name.required'                 =>  'Bạn chưa nhập tên của khuyến mãi !',
            'code.required'                    =>  'Bạn chưa nhập từ khóa của khuyến mãi !',
            'code.unique'                   =>  'Mã khuyễn mãi đã tồn tại!',
            'startDate.required'            =>  'Bạn chưa nhập ngày bắt đầu khuyến mãi !',
            'startDate.custom_date_format'  =>  'Ngày bắt đầu không đúng định dạng !',
            'endDate.required'              =>  'Bạn chưa nhập ngày kết thúc khuyến mãi !',
            'endDate.custom_date_format'    =>  'Ngày kết thúc không đúng định dạng !',

        ];

        $method = $this->only('method')['method'];
        if ($method === 'none') {
            $messages['method.not_in']           = 'Bạn chưa chọn hình thức khuyến mãi !';
        }


        if (!$this->input('neverEndDate')) {
            $messages['endDate.required']           = 'Bạn chưa nhập ngày kết thúc khuyến mãi';
            $messages['endDate.custom_after']       = 'Ngày kết thúc phải lớn hơn ngày bắt đầu khuyến mãi';
        }

        return $messages;
    }
}
