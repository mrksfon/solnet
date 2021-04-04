<?php

namespace App\Http\Requests;

use App\Models\Reply;
use App\Rules\SpamFree;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class CreatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Gate::allows('create', new Reply);
    }

    protected function failedAuthorization()
    {
        throw new ThrottleRequestsException('You are replying too frequently. Please take a break.');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */


    public function rules()
    {
        return [
            'body' => ['required',new SpamFree]
        ];
    }
}
