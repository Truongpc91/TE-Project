<?php

namespace App\Rules\Promotion;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OrderAmountRangeRule implements ValidationRule
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
        if (
            !isset($this->data['amountFrom']) 
            || !isset($this->data['amountTo']) 
            || !isset($this->data['amountValue']) 
            || count($this->data['amountFrom']) == 0
            || $this->data['amountFrom'][0] == ''
        ){
            $fail('Bạn phải khởi tạo khoảng giá cho khuyến mãi');
        }

        if (in_array(0, $this->data['amountValue']) || in_array('', $this->data['amountValue'])) {
            $fail('Cấu hình giá trị khuyến mãi không hợp lệ');
        }
        $temp = [];

        $conflig = false;

        for ($i=0; $i < count($this->data['amountFrom']); $i++) { 
           $amountFrom_1 = $this->data['amountFrom'][$i];
           $amountTo1 = $this->data['amountTo'][$i];

           if($amountFrom_1 >= $amountTo1){
                $conflig = true;
                break;
           }

           for ($j=0; $j < count($this->data['amountFrom']) ; $j++) { 
            if($i !== $j){
                $amountFrom_2 = $this->data['amountFrom'][$j];
                $amountTo2 = $this->data['amountTo'][$j];
                if($amountFrom_1 <= $amountTo2 && $amountTo1 >= $amountFrom_2){
                    $conflig = true;
                    break 2;
                }
            }
           }
        }

        if($conflig){
            $fail('Có xung đột giữa các khoảng giá trị khuyến mãi ! Hãy kiểm tra lại ');
        }
    }
}
