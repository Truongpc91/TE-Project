@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo']['delete']['title']])
@include('backend.dashboard.components.formError')
<form action="{{ route('admin.product_catalogue.destroy', $productCatalogue) }}" method="post" class="box">
    @include('backend.dashboard.components.destroy', ['model' => ($productCatalogue) ?? null])
</form>
