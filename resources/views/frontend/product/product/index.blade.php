@extends('frontend.homepage.layout')

@section('content')
    <div class="product-container" style="padding-left: 20px">
        <div class="uk-container uk-container-center">
            @include('frontend.components.breadcrumb', [
                'model' => $productCatalogue,
                'breadcrumb' => $breadcrumb,
            ])
            <div class="panel-body">
                @include('frontend.product.product.components.product-detail', ['product' => $product, 'productCatalogue' => $productCatalogue])
            </div>
        </div>
    </div>
@endsection
