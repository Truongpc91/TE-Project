<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\PostCatalogue;
use App\Rules\CheckPostCatalogueChildrenRule;

class DeletePostCatalogueRequest extends FormRequest
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
        $postCatalogueId = $this->route('post_catalogue');

        return [
            'name' => [
               new CheckPostCatalogueChildrenRule($postCatalogueId)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name'         =>  'Có danh mục con không được xóa !',
        ];
    }
}
