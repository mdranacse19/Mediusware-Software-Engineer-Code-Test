@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="{{ url('product_filter') }}" method="get" class="card-header">

            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" value="{{ isset($data['title']) ? $data['title'] : '' }}" placeholder="Product Title" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control">
                        <option value="">ALL</option>
                        @if($data['variant'])
                            @foreach($data['variant'] as $variant)
                                <option disabled>{{ $variant->title }}</option>
                                @foreach($variant->product_variants as $product_variants)
                                    <option
                                        {{ (isset($data['variant_id']) and $product_variants->variant == $data['variant_id']) ? 'selected' : '' }}
                                        value="{{ $product_variants->variant }}">{{ $product_variants->variant }}</option>
                                @endforeach
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" value="{{ isset($data['price_from']) ? $data['price_from'] : '' }}" name="price_from" aria-label="First name" placeholder="From" class="form-control">
                        <input type="text" value="{{ isset($data['price_to']) ? $data['price_to'] : '' }}" name="price_to" aria-label="Last name" placeholder="To" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" value="{{ isset($data['date']) ? $data['date'] : '' }}" name="date" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    <tbody>

                        @foreach($data['products'] as $key => $list)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $list->title ? $list->title : 'N/A' }} <br> Created at : {{ $list->created_at ? $list->created_at->diffForHumans() : 'N/A' }}</td>
                                <td>{{ $list->description ? $list->description : 'N/A' }}</td>
                                <td>
                                    <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">

                                        <dt class="col-sm-3 pb-0">
                                            <dl class="row">
                                            @foreach($list->variants as $variant)
                                                <dt class="col-sm-12  pb-2">
                                                {{ $variant->variant ? $variant->variant : 'N/A' }}</dt>
                                            @endforeach
                                            </dl>
                                        </dt>
                                        <dd class="col-sm-9">
                                            <dl class="row mb-0">
                                                @foreach($list->variant_prices as $variant_price)
                                                    <dt class="col-sm-4 pb-0">Price : {{ $variant_price->price ? number_format($variant_price->price,2) : 'N/A' }}</dt>
                                                    <dd class="col-sm-8 pb-0">InStock : {{ $variant_price->stock ? number_format($variant_price->stock,2) : 'N/A' }}</dd>
                                                @endforeach
                                            </dl>
                                        </dd>
                                    </dl>
                                    <button onclick="$('#variant').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('product.edit', 1) }}" class="btn btn-success">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    @if($data['products'])
                        <p> Displaying {{$data['products']->count()}} of {{ $data['products']->total() }} product(s). </p>
                    @endif
                </div>
                <div class="col-md-2">
                    @if(isset($data['products']))
                        {{ $data['products']->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
