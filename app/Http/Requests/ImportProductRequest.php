<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'csv' => 'required|file|mimes:csv,txt',
        ];
    }

    public function messages()
    {
        return [
            'csv.required' => 'Please select a CSV file to import.',
            'csv.file' => 'The uploaded file must be a valid file.',
            'csv.mimes' => 'Only CSV or TXT files are allowed.',
        ];
    }
}
