<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('messages.seo') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="seo-container">
            <div class="h3 meta-title">{{ old('meta_title', (isset($postCatalogue) ? $postCatalogue->meta_title ?? '': __('messages.seoTitle'))) }}</div>
            <div class="canonical">
                {{ old('canonical', (isset($postCatalogue) ? ($postCatalogue->canonical) ? env('APP_URL').$postCatalogue->canonical.config('apps.general.suffix') :  __('messages.seoCanonical') ?? '' : __('messages.seoCanonical')))  }}
            </div>
            <div class="meta-description">
                {{ old('meta_description', (isset($postCatalogue) ? $postCatalogue->meta_description ?? '': __('messages.seoDescription'))) }}
                {{-- {{ old('meta_description') ?? 'Bạn chưa có mô tả SEO' }} --}}
            </div>
            <div class="seo-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-row">
                            <label for="" class="control-label text-right">
                                <div class="">
                                    <span>{{ __('messages.seoMetaTitle') }} | </span>
                                    <span class="count_meta_title">0 ký tự</span>
                                </div>
                            </label>
                            <input type="text" class="form-control" name="meta_title" placeholder=""
                                autocomplete="off"
                                value="{{ old('meta_title', $postCatalogue->meta_title ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-row">
                            <label for="" class="control-label text-right">
                                <div class="">
                                    <span>{{ __('messages.seoMetaKeyword') }} | </span>
                                    <span class="count_meta_keyword">0 ký tự</span>
                                </div>
                            </label>
                            <input type="text" class="form-control" name="meta_keyword" placeholder=""
                                autocomplete="off"
                                value="{{ old('meta_keyword', $postCatalogue->meta_keyword ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-row">
                            <label for="" class="control-label text-right">
                                <div class="">
                                    <span>{{ __('messages.seoMetaDescription') }} | </span>
                                    <span class="count_meta_description">0 ký tự</span>
                                </div>
                            </label>
                            <textarea type="text" class="form-control" name="meta_description" placeholder="" autocomplete="off"
                                >{{ old('meta_description', $postCatalogue->meta_description ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-row">
                            <label for="" class="control-label text-right">
                                <span>{{ __('messages.canonical') }} <span class="text-danger">*</span></span>
                            </label>
                            <div class="input-wrapper">
                                <input type="text" class="form-control seo-canonical" name="canonical" placeholder=""
                                    autocomplete="off" value="{{ old('canonical', $postCatalogue->canonical ?? '') }}">
                                <span class="baseUrl">{{ env('APP_URL') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
