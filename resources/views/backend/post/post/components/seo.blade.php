<div class="ibox">
    <div class="ibox-title">
        <h5>Cấu Hình SEO</h5>
    </div>
    <div class="ibox-content">
        <div class="seo-container">
            <div class="h3 meta-title">{{ old('meta_title', (isset($post) ? $post->meta_title ?? '': 'Bạn chưa nhập tiêu đề SEO')) }}</div>
            <div class="canonical">
                {{ old('canonical', (isset($post) ? ($post->canonical) ? env('APP_URL').$post->canonical.config('apps.general.suffix') :  'http://shopprojectt.test/duong-dan-cua-ban.html' ?? '' : 'http://shopprojectt.test/duong-dan-cua-ban.html'))  }}
            </div>
            <div class="meta-description">
                {{ old('meta_description', (isset($post) ? $post->meta_description ?? '': 'Bạn chưa nhập mô tả SEO')) }}
                {{-- {{ old('meta_description') ?? 'Bạn chưa có mô tả SEO' }} --}}
            </div>
            <div class="seo-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-row">
                            <label for="" class="control-label text-right">
                                <div class="">
                                    <span>Mô tả SEO | </span>
                                    <span class="count_meta_title">0 ký tự</span>
                                </div>
                            </label>
                            <input type="text" class="form-control" name="meta_title" placeholder=""
                                autocomplete="off"
                                value="{{ old('meta_title', $post->meta_title ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-row">
                            <label for="" class="control-label text-right">
                                <div class="">
                                    <span>Từ Khóa SEO | </span>
                                    <span class="count_meta_keyword">0 ký tự</span>
                                </div>
                            </label>
                            <input type="text" class="form-control" name="meta_keyword" placeholder=""
                                autocomplete="off"
                                value="{{ old('meta_keyword', $post->meta_keyword ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-row">
                            <label for="" class="control-label text-right">
                                <div class="">
                                    <span>Mô tả SEO | </span>
                                    <span class="count_meta_description">0 ký tự</span>
                                </div>
                            </label>
                            <textarea type="text" class="form-control" name="meta_description" placeholder="" autocomplete="off"
                                >{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-row">
                            <label for="" class="control-label text-right">
                                <span>Đường Dẫn <span class="text-danger">*</span></span>
                            </label>
                            <div class="input-wrapper">
                                <input type="text" class="form-control seo-canonical" name="canonical" placeholder=""
                                    autocomplete="off" value="{{ old('canonical', $post->canonical ?? '') }}">
                                <span class="baseUrl">{{ env('APP_URL') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
