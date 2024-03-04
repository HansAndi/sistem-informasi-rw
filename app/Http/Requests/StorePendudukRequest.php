<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePendudukRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'no_kk' => 'required|max:16',
            'nik' => 'required|max:16',
            'nama' => 'required|max:128',
            'tempat_lahir' => 'required|max:128',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'golongan_darah' => 'required|max:3',
            'agama' => 'required|max:16',
            'status_perkawinan' => 'required|max:32',
            'status_keluarga' => 'required',
            'pekerjaan' => 'required|max:128',
            'alamat' => 'required|max:256',
            'rt' => 'required|integer',
            'keterangan' => 'required|max:128',
            'id_sosial' => 'required',
        ];
    }
}
