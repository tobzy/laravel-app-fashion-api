<?php

namespace App\Http\Controllers;

use App\Design;
use Illuminate\Http\Request;

use App\Http\Requests;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;

class DesignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $designs = Design::all();

        foreach ($designs as $design) {
            $design->view_design = [
                'href' => '/v1/designer/design/' . $design->id,
                'method' => 'GET'
            ];
        }

        $response = [
            'message' => 'List of all designs..',
            'designs' => $designs
        ];
        return response()->json($response, 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'location' => 'required',
            'designer_id' => 'required'
        ]);

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
            return response()->json($response, 201);
        }

        $response = [
            'message' => 'Error, Design not Uploaded. Try again',
        ];
        return response()->json($response, 404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $design = Design::where('id', $id)->first();
        if ($design) {
            $design->view_meetings = [
                'href' => '/v1/designer/design',
                'method' => 'GET'
            ];

            $response = [
                'message' => 'Design Information',
                'design' => $design
            ];

            return response()->json($response, 200);
        }
        $response = [
            'message' => 'NO SUCH DESIGN!',
        ];
        return response()->json($response, 404);

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
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'location' => 'required',
            'designer_id' => 'required'
        ]);
        $designer_id = $request->input('designer_id');

        $design = Design::where('id',$id)->first();

        if (!$design->designer()->where('designers.id',$designer_id)->first()) {
            return response()->json([
                'message' => 'Designer Not Registered for meeting, Update not successful'
            ],401);
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
            return response()->json($response, 200);
        }

        $response = [
            'message' => 'Error during Updating. Try again',
        ];
        return response()->json($response, 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $design = Design::where('id',$id)->first();
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
            return response()->json($response, 200);
        }

        $response = [
            'message' => 'Error during Deleting. Try again',
        ];
        return response()->json($response, 404);


    }
}
