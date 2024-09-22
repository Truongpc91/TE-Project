@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.components.formError')
<form action="{{ route('admin.{module}.destroy', ${module}) }}" method="post" class="box">
   @include('backend.dashboard.components.destroy', ['model' => ${module}])
</form>
