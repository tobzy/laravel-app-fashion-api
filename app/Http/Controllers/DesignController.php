<?php

namespace App\Http\Controllers;

use App\AdditionalDesigns;
use App\Design;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
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
        $designs = Design::with('additional_designs')->where('designer_id', $id)->orderBy('id', 'desc')->paginate(8);
        if (count($designs) > 0) {
            foreach ($designs as $design) {
                $design->view_design = [
                    'href' => '/v1/designer/design/' . $design->id,
                    'method' => 'GET'
                ];
            }

            $response = [
                'message' => 'List of all designs.. from ' . $design->designer->full_name,
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
        //validate the post request
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
//            'designer_id' => 'required',
        ]);

        //if validator fails return json error responce
        if ($validator->fails()) {
            $error = [
                'hasError' => true,
                'details' => $validator->errors(),
            ];
            return $this->respondWithError(404, 'validation_error', $validator->errors()->toJson());
        }


        $request['designer_id'] = $request->designer_id;
        $original_name = $request->file("file1")->getClientOriginalName();
        $time = new Carbon();
        $time = $time->timestamp;
        $uuid = Uuid::uuid1();
        $name = $uuid . "_" . $time;
        Storage::disk('uploads')->put($name, file_get_contents($request->file("file1")->getRealPath()));

        $design = new Design();
        $design->title = $request->input('title');
        $design->uuid = $uuid . '_' . $time;
        $design->description = $request->input('description');
        $design->location = $name;
        $design->original_name = $original_name;
        $design->designer_id = $request->input('designer_id');
        $saved = $design->save();

        for($i = 2;$i<5;$i++){
            $original_name = $request->file("file$i")->getClientOriginalName();
            $time = new Carbon();
            $time = $time->timestamp;
            $uuid = Uuid::uuid1();
            $name = $uuid . "_" . $time;
            Storage::disk('uploads')->put($name, file_get_contents($request->file("file$i")->getRealPath()));

            $add_design = new AdditionalDesigns();
            $add_design->uuid = $uuid.'_'.$time;
            $add_design->location = $name;
            $add_design->original_name = $original_name;
            $design->additional_designs()->save($add_design);
        }



//
        if ($saved) {
            $design->view_design = [
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
            if ($design->designer_id == $request->designer_id) {

                $design->view_designs = [
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
        ]);

        //if validator fails return json error responce
        if ($validator->fails()) {
            $error = [
                'hasError' => true,
                'details' => $validator->errors(),
            ];
            return $this->respondWithError(404, 'validation_error', $validator->errors()->toJson());
        }

        $designer_id = $request->input('designer_id');

        $design = Design::where('id', $id)->first();
        $add_design = $design->additional_designs;

        if (empty($design)) {
            return $this->respondWithError(401, 'request_error', 'No such Design!');
        }
        if (!$design->designer()->where('designers.id', $designer_id)->first()) {
            return $this->respondWithError(401, 'request_error', 'Designer didn\'t make this design, Update not successful');
        }

        if ($request->file('file1')->getClientOriginalName()) {
            $original_name = $request->file('file1')->getClientOriginalName();

            if($original_name){
                $time = new Carbon();
                $time = $time->timestamp;
                $uuid = Uuid::uuid1();
                $name = $uuid . "_" . $time;

                Storage::disk('uploads')->delete($design->location);
                Storage::disk('uploads')->put($name, file_get_contents($request->file('file1')->getRealPath()));
                $design->location = $name;
                $design->original_name = $original_name;
            }

        }
        if ($request->file('file2')->getClientOriginalName()) {
            $original_name = $request->file('file2')->getClientOriginalName();

            if($original_name){
                $time = new Carbon();
                $time = $time->timestamp;
                $uuid = Uuid::uuid1();
                $name = $uuid . "_" . $time;

                Storage::disk('uploads')->delete($add_design[0]->location);
                Storage::disk('uploads')->put($name, file_get_contents($request->file('file2')->getRealPath()));
                $add_design[0]->location = $name;
                $add_design[0]->original_name = $original_name;
                $add_design[0]->save();
            }

        }
        if ($request->file('file3')->getClientOriginalName()) {
            $original_name = $request->file('file3')->getClientOriginalName();

            if($original_name){
                $time = new Carbon();
                $time = $time->timestamp;
                $uuid = Uuid::uuid1();
                $name = $uuid . "_" . $time;

                Storage::disk('uploads')->delete($add_design[1]->location);
                Storage::disk('uploads')->put($name, file_get_contents($request->file('file3')->getRealPath()));
                $add_design[1]->location = $name;
                $add_design[1]->original_name = $original_name;
                $add_design[1]->save();
            }

        }
        if ($request->file('file4')->getClientOriginalName()) {
            $original_name = $request->file('file4')->getClientOriginalName();

            if($original_name){
                $time = new Carbon();
                $time = $time->timestamp;
                $uuid = Uuid::uuid1();
                $name = $uuid . "_" . $time;

                Storage::disk('uploads')->delete($add_design[2]->location);
                Storage::disk('uploads')->put($name, file_get_contents($request->file('file4')->getRealPath()));
                $add_design[2]->location = $name;
                $add_design[2]->original_name = $original_name;
                $add_design[2]->save();
            }

        }



        $design->title = $request->input('title');
        $design->description = $request->input('description');

        $design->designer_id = $request->input('designer_id');

//        $add_design->update();
        if ($design->update()) {
            $design->view_design = [
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
        $design = Design::where('id', $id)->first();
        $name = $design->location;
        $name1 = $design->additional_designs[0]->location;
        $name2 = $design->additional_designs[1]->location;
        $name3 = $design->additional_designs[2]->location;
        if (empty($design)) {
            return $this->respondWithError(404, 'request_error', 'No such Design');
        }
        if ($designer_id == $design->designer_id) {
            $design->additional_designs()->delete();
            if ($design->delete()) {
                Storage::disk('uploads')->delete($name);
                Storage::disk('uploads')->delete($name1);
                Storage::disk('uploads')->delete($name2);
                Storage::disk('uploads')->delete($name3);
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
