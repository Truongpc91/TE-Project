@extends('frontend.homepage.layout')

@section('content')
    <div class="product-catalogue page-wrapper">
        <div class="uk-container uk-container-center">
            @include('frontend.components.breadcrumb', [
                'model' => $productCatalogue,
                'breadcrumb' => $breadcrumb,
            ])
        </div>
        <div class="panel-body" style="padding-left:20px">
           @include('frontend.product.catalogue.components.filter')
           @include('frontend.product.catalogue.components.filterContent')
            @if (!is_null($products))
                <div class="product-list">
                    <div class="uk-grid uk-grid-medium">
                        @foreach ($products as $key => $product)
                            <div class="uk-width-1-2 uk-width-small-1-2 uk-width-medium-1-3 uk-width-large-1-5 mb20">
                                @include('frontend.components.product-item', [
                                    'product' => $product,
                                ])
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="" style="display:flex; justify-content: space-around">
                    {{-- {{ $products->links('pagination: bootstrap-4') }} --}}
                    @include('frontend.components.pagination', ['model' => $products])
                </div>
            @endif
        </div>
    </div>
@endsection
