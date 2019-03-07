<?php

namespace App\Http\Requests;

class ReplyRequest extends Request
{
    public function rules()
    {
        return [
            'contents' => 'required|min:2',
        ];
    }

    public function messages()
    {
        return [
            'contents.required' => '回复内容不能为空',
            'contents.min'      => '回复内容太短'
        ];
    }
}
