<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;
use Storage;
use Ramsey\Uuid\Uuid;
use App\Order;
use App\OrderContent;

class StoreController extends ApiController
{

    /**
     * API Login, on success return JWT Auth token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts(Request $request)
    {

        $cat = $request->input('category');
        $filter = $request->input('filter');

        if (!(isset($cat) && ($cat == 'men' || $cat == 'women'))) {
            return $this->respondWithError('Error', 'category_error', 'The Selected Category doest exist');
        }

        $url_query = [
            'cat' => $cat,
        ];

        if (isset($filter) && $filter) {
            $min_price = $request->input('filter_min_price');
            $max_price = $request->input('filter_max_price');

            $products = App\Product::with('default_material')
                ->whereCategory($cat)
                ->where('price', '>=', $min_price)
                ->where('price', '<=', $max_price)
                ->paginate(9);


            $url_query['filter_min_price'] = $min_price;
            $url_query['filter_max_price'] = $max_price;
            $url_query['filter'] = true;

        } else {

            $products = App\Product::with('default_material')->whereCategory($cat)->paginate(9);
            $products->load('default_material');
        }

        $products->appends($url_query)->links();

        return $this->respondWithoutError([
            'products' => $products,
            'links' => (string)$products->links()
        ]);
    }

    public function getProductImage($uuid)
    {
        /* todo for production, each product has its
         * images in its own folder with id as name,
         * when, retrieving, retrieve from "images/products/{id}/image_name"
        */
        $product = App\Product::whereUuid($uuid)->first();

        if (!$product) {
            return $this->respondWithError([404, 'not_found', 'The product does not exist']);
        }
        return Storage::get('images/products/' . $product->image);
    }

    public function getImage($id)
    {
        $image = App\ProductImage::whereId($id)->first();
        if (!$image) {
            return $this->respondWithError([404, 'not_found', 'The image does not exist']);
        }
        return Storage::get('images/products/' . $image->image);
    }

    public function getNewProducts()
    {
        $products = App\Product::whereCategory('men')
            ->orWhere('category', 'women')
            ->orderBy('created_at', 'DESC')
            ->limit(3)
            ->get();

        return $this->respondWithoutError([
            'new_products' => $products,
        ]);
    }

    /**
     * API get single product item, on success returns a json data if the item
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSingleItem(Request $request)
    {
        $uuid = $request->input('uuid');

        $product = App\Product::with('images')->whereUuid($uuid)->first();
        if (!$product) {
            return $this->respondWithError('ERR_PRD_001', 'item_not_existing', 'Sorry the item you requested for doesn\'t exist, Try again!!');
        }

        $product->default_material = $product->default_material_with_cat($product->default_material);

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
    public function getMaterials(Request $request)
    {
        $filter = $request->input('filter');

        if (isset($filter) && $filter == true) {

            $min_price = $request->input('filter_min_price');
            $max_price = $request->input('filter_max_price');

            $materials = App\Material::with('category')
                ->where('price', '>=', $min_price)
                ->where('price', '<=', $max_price)
                ->paginate(9);

            $url_query = [];
            $url_query['filter_min_price'] = $min_price;
            $url_query['filter_max_price'] = $max_price;
            $url_query['filter'] = true;

            $materials->appends($url_query)->links();

        } else {
            $materials = App\Material::with('category')->paginate(9);
        }


        return $this->respondWithoutError([
            'materials' => $materials,
            'links' => (string)$materials->links()
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSingleMaterial(Request $request)
    {
        $id = $request->input('id');

        $material = App\Material::with('category')->whereId($id)->first();

        return $this->respondWithoutError([
            'material' => $material,
        ]);
    }

    public function getMaterialImage()
    {

    }

    public function getNewMaterials()
    {
        $new_materials = App\Material::with('category')
            ->orderBy('created_at', 'DESC')
            ->limit(3)
            ->get();

        return $this->respondWithoutError([
            'new_materials' => $new_materials
        ]);
    }

    public function addOrder(Request $request)
    {

        $content = null;
        $order = null;
        $product = App\Product::whereUuid($request->input('product_uuid'))->first();
        $material = App\Material::whereId($request->input('material_id'))->first();
        $qty = $request->input('quantity');

        if (!$request->has('order_uuid')) {

            $order = Order::where('user_id', $this->user->id)->first();
            if ($order) {
                $order_content = OrderContent::where('product_id', $product->id)->first();
                if ($order_content) {
                    $order_content->update([
                        'quantity' => $qty,
                        'product_price' => $product->price,
                        'material_id' => $material->id,
                        'material_price' => $material->price
                    ]);
                } else {
                    $content = new OrderContent([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'product_price' => $product->price,
                        'material_id' => $material->id,
                        'material_price' => $material->price
                    ]);
                    $order->content()->save($content);
                }
            }else{
                $order = Order::create([
                    'uuid' => Uuid::uuid4(),
                    'user_id' => $this->user->id,
                ]);
                $content = new OrderContent([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'product_price' => $product->price,
                    'material_id' => $material->id,
                    'material_price' => $material->price
                ]);
                $order->content()->save($content);
                $content->load('material','product');
            }

        } else {
            $order = Order::whereUuid($request->input('order_uuid'))->first();
        }

//        $order->load('content');
        $order->content = $content;

        return $this->respondWithoutError(['order' => $order]);
    }

    public function updateOrderContent(Request $request, $uuid)
    {
        $content = OrderContent::whereUuid($uuid)->first();

        $content->fill($request->all());

        return $this->respondWithoutError(['order_content' => $content]);
    }
}
