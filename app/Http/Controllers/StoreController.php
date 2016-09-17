<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;

use App\Http\Requests;

class StoreController extends ApiController
{

    /**
     * API Login, on success return JWT Auth token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts(Request $request){

        $cat = $request->input('category');

        if(!(isset($cat) && ($cat=='men' || $cat=='women'))){
           return $this->respondWithError('Error','category_error','The Selected Category doest exist');
        }

        $products = App\Product::whereCategory($cat)->paginate(9);
        $products->appends(['cat' => 'men'])->links();

        return $this->respondWithoutError([
            'products'=>$products,
            'links'=> (string)$products->links()
        ]);
    }

    /**
     * API get single product item, on success returns a json data if the item
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSingleItem(Request $request){
        $uuid = $request -> input('uuid');

        $product = App\Product::whereUuid($uuid)->first();
        if(!$product){
            return $this->respondWithError('ERR_PRD_001','item_not_existing','Sorry the item you requested for doesn\'t exist, Try again!!');
        }

        $product -> default_material = $product->default_material_with_cat($product->default_material);

        return $this->respondWithoutError([
            'product' => $product,
        ]);
    }

    /**
     * API get materials, on success return a paginated json data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMaterials(Request $request){
        $materials = App\Material::with('category')->paginate(9);

        return $this->respondWithoutError([
            'materials'=>$materials,
            'links'=>(string)$materials->links()
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSingleMaterial(Request $request){
        $id = $request->input('id');

        $material = App\Material::with('category')->whereId($id)->first();

        return $this->respondWithoutError([
            'material' => $material,
        ]);
    }
}
