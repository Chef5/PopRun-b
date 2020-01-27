<?php

namespace App\Http\Controllers;

use App\RUsers;
use Illuminate\Http\Request;

class RUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->has('openid')) {
            RUsers::insert($request->all());
        }
        return RUsers::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RUsers  $rUsers
     * @return \Illuminate\Http\Response
     */
    public function show(RUsers $rUsers)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RUsers  $rUsers
     * @return \Illuminate\Http\Response
     */
    public function edit(RUsers $rUsers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RUsers  $rUsers
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RUsers $rUsers)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RUsers  $rUsers
     * @return \Illuminate\Http\Response
     */
    public function destroy(RUsers $rUsers)
    {
        //
    }
}
