<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Company;
use App\Rules\CompanyLogoDimensionRule;

class CompanyRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            return [
                'name' => 'required|string|max:255|unique:companies,name,'.$this->id,
                'email' => 'nullable|email|max:255|unique:companies,email,'.$this->id,
                'logo' => 'nullable|string|max:255',
                'website' => 'nullable|string|regex:/^\S*$/|max:512|unique:companies,website,'.$this->id,
                'logoContent' => [new CompanyLogoDimensionRule],
            ];
        }
        return [
            'name' => 'required|string|max:255|unique:' . Company::class,
            'email' => 'nullable|email|max:255|unique:' . Company::class,
            'logo' => 'nullable|string|max:255',
            'website' => 'nullable|string|regex:/^\S*$/|max:512|unique:' . Company::class,
            'logoContent' => [new CompanyLogoDimensionRule],
        ];
    }
}
