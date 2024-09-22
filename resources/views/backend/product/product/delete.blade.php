@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.components.formError')
<form action="{{ route('admin.product.destroy', $product) }}" method="post" class="box">
   @include('backend.dashboard.components.destroy', ['model' => $product])
</form>
