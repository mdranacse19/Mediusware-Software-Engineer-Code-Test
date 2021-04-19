<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $data['variant'] = Variant::with('product_variants')->get();

        $products = Product::paginate(5);
        foreach ($products as $key => $product){
            $variant_prices = DB::table('product_variant_prices as pvp')
                ->select('price', 'stock', 'product_variant_one')
                ->where('pvp.product_id', $product->id)
                ->get();
            $product->variant_prices = $variant_prices;

            $variants = DB::table('product_variants as pv')->select('*')
                ->where('pv.product_id', $product->id)
                ->get();
            $product->variants = $variants;
        }
        $data['products'] = $products;
        //return $data['products']->toJson();

        return view('products.index', compact('data'));
    }


    public function filter(Request $request){
        //return $request;
        $title      = $request->title;
        $variant_id = $request->variant;
        $price_from = $request->price_from;
        $price_to   = $request->price_to;
        $date       = $request->date;

        $data['variant'] = Variant::with('product_variants')->get();

        $products = Product::when($date, function ($query) use ($date){
                        $query->whereRaw("substr(created_at, 1, 10) <= '$date'");
                            //->where('created_at', '<=', $date.' 00:00:00');
                    })
                    ->when($title, function ($query) use ($title){
                        $query->orWhere('title', 'LIKE', "%$title%");
                    })
                    ->paginate(10);

        if (!$products->isEmpty()){
            //return 'success';
            foreach ($products as $key => $product){
                $id = $product->id;
                $variant_prices = DB::table('product_variant_prices as pvp')
                    ->select('price', 'stock', 'product_variant_one')
                    ->where('pvp.product_id', $id)
                    ->when(($price_from and $price_to), function ($query) use ($price_from, $price_to){
                        $query->whereBetween('pvp.price', [$price_from, $price_to]);
                    })
                    ->get();
                $product->variant_prices = $variant_prices;

                $variants = DB::table('product_variants as pv')->select('*')
                    ->where('pv.product_id', $id)
                    ->when($variant_id, function ($query) use ($variant_id){
                        $query->where('pv.variant', 'LIKE', "%$variant_id%");
                    })
                    ->get();
                $product->variants = $variants;
            }
        }
        $data['products']       = $products;
        $data['title']          = $title;
        $data['variant_id']     = $variant_id;
        $data['price_from']     = $price_from;
        $data['price_to']       = $price_to;
        $data['date']           = $date;

        //return $data;

        return view('products.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'sku' => 'unique:products',
        ]);


        //return $request;
        $product = $request->only('title', 'sku', 'description');
        $store_product = Product::create($product);
        //product_variant
        foreach ($request->product_variant as $product_variant){
            $set_variant = '';
            foreach ($product_variant['tags'] as $key => $tag){
                $set_variant .= $tag.'/';
            }
            $pv = [
                'variant' => $set_variant,
                'variant_id' => $product_variant['option'],
                'product_id' => $store_product->id,
            ];
            //print_r($pv);
            ProductVariant::create($pv);
        }
        //product_variant_prices
        foreach ($request->product_variant_prices as $variant_price) {
            //print_r( $variant_price);
            $pvp = [
                'price' => $variant_price['price'],
                'stock' => $variant_price['stock'],
                'product_id' => $store_product->id,
            ];
            ProductVariantPrice::create($pvp);
        }

        return $request;


    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
