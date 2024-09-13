@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo']['delete']['title']])
@include('backend.dashboard.components.formError')
<form action="{{ route('admin.{view}.destroy', ${module}->id) }}" method="post" class="box">
    @include('backend.dashboard.components.destroy', ['model' => (${module}) ?? null])
</form>
