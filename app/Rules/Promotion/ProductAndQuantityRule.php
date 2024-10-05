<?php

namespace App\Rules\Promotion;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProductAndQuantityRule implements ValidationRule
{

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        if($this->data['product_and_quantity']['quantity'] == 0){
            $fail('Bạn phải nhập số lượng tối thiểu để hưởng chiết khấu !');
        }
        if($this->data['product_and_quantity']['maxDiscountValue'] == 0){
            $fail('Bạn phải nhập vào giá trị của chiết khấu !');
        }

        if(!isset($this->data['object']) || count($this->data['object']['id']) == 0){
            $fail('Bạn chưa chọn đối tượng áp dụng chiết khấu!');
        }
    }
}
