@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.components.formError')
<form action="{{ route('admin.attribute.destroy', $attribute) }}" method="post" class="box">
   @include('backend.dashboard.components.destroy', ['model' => $attribute])
</form>
