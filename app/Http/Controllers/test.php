<?php

namespace App\Http\Controllers;

use App\Design;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Ramsey\Uuid\Uuid;

class DesignController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $designs = Design::whereUserId($this->user->id) ->orderBy('id', 'desc')->get();
        return $this->respondWithoutError(['designs' => $designs, ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $time = new Carbon();
        $time = $time->timestamp;
        $design = new Design();
        $design->title = $request->input('title');
        $design->uuid = Uuid::uuid1().'_'.$time;
        $design->description = $request->input('description');
        $design->user_id = $this->user->id;
        $design->location = $request->input('location');
        $design->original_name = $request->input('original_name');
        $design->save();

        $designs = Design::whereUserId($this->user->id)->get();
        return $this->respondWithoutError(['designs' => $designs]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
