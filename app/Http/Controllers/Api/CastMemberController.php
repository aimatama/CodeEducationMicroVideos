<?php

namespace App\Http\Controllers\Api;

use App\Models\CastMember;
use App\Http\Controllers\Controller;

class CastMemberController extends BasicCrudController
{


    private $rules = [
        'name' => 'required|max:255',
        'type' => 'required|min:1|max:1',
        'is_active' => 'boolean'
    ];

    protected function model()
    {
        return CastMember::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }

}
