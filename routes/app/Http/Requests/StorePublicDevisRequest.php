<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePublicDevisRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nom' => trim(strip_tags((string) $this->input('nom'))),
            'telephone' => preg_replace('/\s+/', ' ', trim((string) $this->input('telephone'))),
            'ville' => trim(strip_tags((string) $this->input('ville'))),
            'pays' => trim(strip_tags((string) $this->input('pays', 'Togo'))),
            'description_besoin' => trim(strip_tags((string) $this->input('description_besoin'))),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => ['bail', 'required', 'string', 'min:2', 'max:255', 'regex:/^[\pL\pM\s\'\-]+$/u'],
            'telephone' => ['bail', 'required', 'string', 'max:30', 'regex:/^(?:\+?[0-9\s\.\-\(\)]{8,20})$/'],
            'ville' => ['nullable', 'string', 'min:2', 'max:255', 'regex:/^[\pL\pM\s\'\-]+$/u'],
            'pays' => ['required', 'string', 'min:2', 'max:80', 'regex:/^[\pL\pM\s\'\-]+$/u'],
            'description_besoin' => ['bail', 'required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est requis.',
            'nom.regex' => 'Le nom contient des caractères non autorisés.',
            'telephone.required' => 'Le numéro de téléphone est requis.',
            'telephone.regex' => 'Le numéro de téléphone est invalide.',
            'ville.regex' => 'La ville contient des caractères non autorisés.',
            'pays.required' => 'Le pays est requis.',
            'pays.regex' => 'Le pays contient des caractères non autorisés.',
            'description_besoin.required' => 'La description du besoin est requise.',
            'description_besoin.min' => 'La description du besoin doit contenir au moins 10 caractères.',
        ];
    }
}
