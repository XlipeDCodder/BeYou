<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Garante que o usuário autenticado é o dono do canal
        // para o qual está tentando enviar o vídeo.
        // Por enquanto, vamos assumir que ele só pode enviar para o próprio canal.
        return $this->user()->channel()->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            // Validação do arquivo de vídeo
            'video' => [
                'required',
                'file',
                'mimetypes:video/mp4,video/quicktime,video/x-msvideo', // Aceita .mp4, .mov, .avi
                'max:204800' // Limite de 200MB (200 * 1024). Ajuste conforme necessário.
            ],
        ];
    }
}