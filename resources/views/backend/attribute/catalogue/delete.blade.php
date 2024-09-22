@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo']['delete']['title']])
@include('backend.dashboard.components.formError')
<form action="{{ route('admin.attribute_catalogue.destroy', $attributeCatalogue) }}" method="post" class="box">
    @include('backend.dashboard.components.destroy', ['model' => ($attributeCatalogue) ?? null])
</form>
