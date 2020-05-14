<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\CastMember;
use App\Http\Controllers\Controller;

class CastMemberController extends Controller
{


    private $rules = [
        'name' => 'required|max:255',
        'type' => 'required|min:1|max:1',
        'is_active' => 'boolean'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return CastMember::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->rules);
        $castMember = CastMember::create($request->all());
        $castMember->refresh();
        return $castMember;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CastMember  $castMember
     * @return \Illuminate\Http\Response
     */
    public function show(CastMember $castMember)
    {
        return $castMember;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CastMember  $castMember
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CastMember $castMember)
    {
        $this->validate($request, $this->rules);
        $castMember->update($request->all());
        return $castMember;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CastMember  $castMember
     * @return \Illuminate\Http\Response
     */
    public function destroy(CastMember $castMember)
    {
        $castMember->delete();
        return response()->noContent(); // 204 No content
    }
}
