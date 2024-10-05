@include('backend.dashboard.components.breadcrumb',['title' => $config['seo']['index']['title']])

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row mt20">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{ $config['seo']['index']['table'] }}</h5>
                   @include('backend.dashboard.components.toolbox', ['model' => 'UserCatalogue']) 
                </div>
                <div class="ibox-content">
                   @include('backend.customer.catalogue.components.filter')
                   @include('backend.customer.catalogue.components.table', ['table' => $config['seo']['index']['table']])
                </div>
            </div>
        </div>
    </div>
</div>


