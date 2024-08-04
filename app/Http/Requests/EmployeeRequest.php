<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Employee;

class EmployeeRequest extends FormRequest
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
                'first_name' => ['required', 'string', 'max:255',
                Rule::unique('employees')->where(function ($query) {
                    return $query->where('first_name', $this->first_name)
                    ->where('last_name', $this->last_name)
                    ->where('id', '<>', $this->id);
                }),],
                'last_name' => ['required', 'string', 'max:255',
                Rule::unique('employees')->where(function ($query) {
                    return $query->where('first_name', $this->first_name)
                    ->where('last_name', $this->last_name)
                    ->where('id', '<>', $this->id);
                }),],
                'company_id' => 'required|integer|gt:0',
                'email' => 'nullable|email|max:255|unique:employees,email,'.$this->id,
                'phone' => 'nullable|regex:/^[\+]?[0-9 ]{0,1}[ ]{0,1}[.(-]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/|max:255|unique:employees,phone,'.$this->id
            ];
        }
        return [
            'first_name' => ['required', 'string', 'max:255',
            Rule::unique('employees')->where(function ($query) {
                return $query->where('first_name', $this->first_name)
                ->where('last_name', $this->last_name)
                ->where('id', '<>', $this->id);
            }),],
            'last_name' => ['required', 'string', 'max:255',
            Rule::unique('employees')->where(function ($query) {
                return $query->where('first_name', $this->first_name)
                ->where('last_name', $this->last_name)
                ->where('id', '<>', $this->id);
            }),],
            'company_id' => 'required|integer|gt:0',
            'email' => 'nullable|email|max:255|unique:' . Employee::class,
            'phone' => 'nullable|regex:/^[\+]?[0-9 ]{0,1}[ ]{0,1}[.(-]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/|max:255|unique:' . Employee::class
        ];
    }
}
