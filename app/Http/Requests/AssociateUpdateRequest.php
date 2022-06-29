<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssociateUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [ 'max:100'],
            'area_id' => ['nullable'],
            'subarea_id' => ['nullable'],
            'shift_id' => ['nullable'],
            'associate_type_id' => ['nullable'],
            'employee_number' => ['nullable'],
            'entry_date' => ['nullable', 'date'],
            'status_id' => ['nullable'],
            'picture' => ['nullable'],
            'elegible' => ['nullable'],
            'user_saalma' => ['nullable'],
            'wamas_user' => ['nullable'],
            'unionized' => ['nullable'],
        ];
    }
}
