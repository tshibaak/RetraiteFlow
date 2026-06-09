<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EncadreurSotreRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
           'name'      => 'required|string|max:255',
           'sexe'      => 'required',
           'age'       => 'required|integer|min:15|max:50',
           'groupe'    => 'required',
           'commission'=> 'required|string|max:100',
           'phone'     => 'required|string|max:20',
           'amount'    => 'required|numeric|min:0',
           'delai'     => 'required|date',
        ];
    }

    /**
     * Get the validated data with renamed 'groupe' to 'group'
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        if (is_array($validated) && isset($validated['groupe'])) {
            $validated['group'] = $validated['groupe'];
            unset($validated['groupe']);
        }
        return $validated;
    }
}
