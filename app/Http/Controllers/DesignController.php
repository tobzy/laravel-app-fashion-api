<?php

namespace App\Http\Controllers;

use App\Design;
use Illuminate\Http\Request;

use App\Http\Requests;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;
use Validator;

class DesignController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = $request->designer_id;
        $designs = Design::where('designer_id',$id)->get();
        if(count($designs)>0){
            foreach ($designs as $design) {
                $design->view_design = [
                    'href' => '/v1/designer/design/' . $design->id,
                    'method' => 'GET'
                ];
            }

            $response = [
                'message' => 'List of all designs.. from '. $design->designer->full_name,
                'designs' => $designs
            ];
            return $this->respondWithoutError($response);
        }
        return $this->respondWithError(404, 'request_error', 'No Designs From this user');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request['designer_id'] = $request->designer_id;

        //validate the post request
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'location' => 'required',
            'designer_id' => 'required',
        ]);

        //if validator fails return json error responce
        if ($validator->fails()) {
            $error = [
                'hasError' => true,
                'message' => $validator->errors(),
            ];
            return response()->json(['errors' => $error]);
        }


        $design = new Design();
        $time = new Carbon();
        $time = $time->timestamp;

        $design->title = $request->input('title');
        $design->uuid = Uuid::uuid1() . '_' . $time;
        $design->description = $request->input('description');
        $design->location = $request->input('location');
        $design->original_name = $request->input('original_name');
        $design->designer_id = $request->input('designer_id');

        if ($design->save()) {
            $design->view_meeting = [
                'href' => '/v1/designer/design/' . $design->id,
                'method' => 'GET'
            ];
            $response = [
                'message' => 'Design Created',
                'design' => $design
            ];
            return $this->respondWithoutError($response);
        }

        return $this->respondWithError(404, 'request_error', 'Error, Design not Uploaded. Try again');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $design = Design::where('id', $id)->first();

        if ($design) {
            if($design->designer_id == $request->designer_id){

                $design->view_meetings = [
                    'href' => '/v1/designer/design',
                    'method' => 'GET'
                ];

                $response = [
                    'message' => 'Design Information',
                    'design' => $design
                ];

                return $this->respondWithoutError($response);
            }
            return $this->respondWithError(401, 'request_error', 'This designer can\'t view this design');

        }

        return $this->respondWithError(404, 'request_error', 'No such Design!');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request['designer_id'] = $request->designer_id;

        //validate the post request
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'location' => 'required',
            'designer_id' => 'required',
        ]);

        //if validator fails return json error responce
        if ($validator->fails()) {
            $error = [
                'hasError' => true,
                'message' => $validator->errors(),
            ];
            return response()->json(['errors' => $error]);
        }


        $designer_id = $request->input('designer_id');

        $design = Design::where('id',$id)->first();

        if(empty($design)){
            return $this->respondWithError(401, 'request_error', 'No such Design!');
        }
        if (!$design->designer()->where('designers.id',$designer_id)->first()) {
            return $this->respondWithError(401, 'request_error', 'Designer didn\'t make this design, Update not successful');
        }
        
        $design->title = $request->input('title');
        $design->description = $request->input('description');
        $design->location = $request->input('location');
        $design->original_name = $request->input('original_name');
        $design->designer_id = $request->input('designer_id');

        if ($design->update()) {
            $design->view_meeting = [
                'href' => '/v1/designer/design/' . $design->id,
                'method' => 'GET'
            ];
            $response = [
                'message' => 'Design Updated',
                'design' => $design
            ];
            return $this->respondWithoutError($response);
        }
        
        return $this->respondWithError(404, 'request_error', 'Error during Updating. Try again');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //
        $designer_id = $request->designer_id;
        $design = Design::where('id',$id)->first();
        if(empty($design)){
            return $this->respondWithError(404, 'request_error', 'No such Design');
        }
        if($designer_id == $design->designer_id){
            if ($design->delete()) {
                $design->create = [
                    'href' => '/v1/designer/design',
                    'method' => 'POST',
                    'params' => 'title, description, location'
                ];
                $response = [
                    'message' => 'Design Deleted',
                    'design' => $design
                ];
                return $this->respondWithoutError($response);
            }
            return $this->respondWithError(404, 'request_error', 'Error during Deleting. Try again');
        }
        
        return $this->respondWithError(404, 'request_error', 'Designer not allowed to delete. Try again');
        

    }
}
