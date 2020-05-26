<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Genre;

class GenreController extends BasicCrudController
{

    private $rules = [
        'name' => 'required|max:255',
        'categories_id' => 'required|array|exists:categories,id',
        'is_active' => 'boolean'
    ];

    public function store(Request $request)
    {
        $validateData = $this->validate($request, $this->rulesStore());
        $self = $this;
        $obj = \DB::transaction(function() use ($request, $validateData, $self){
            $obj = $this->model()::create($validateData);
            $self-> handleRelations($obj, $request);
            return $obj;
        });
        $obj->refresh();
        return $obj;
    }

    public function update(Request $request, $id)
    {
        $obj = $this->findOrFail($id);
        $validatedData = $this->validate($request, $this->rulesUpdate());
        $self = $this;
        $obj = \DB::transaction(function() use ($obj, $request, $validatedData, $self){
            $obj->update($validatedData);
            $self-> handleRelations($obj, $request);
            return $obj;
        });        
        return $obj;
    }

    protected function handleRelations($genre, Request $request){
        $genre->categories()->sync($request->get('categories_id'));
    }

    protected function model()
    {
        return Genre::class;
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
