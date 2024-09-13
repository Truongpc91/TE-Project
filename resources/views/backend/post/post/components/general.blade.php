<div class="row mb15">
    <div class="col-lg-12">
        <div class="form-row">
            <label for="" class="control-label text-right">{{ __('messages.title') }}
                <span class="text-danger">(*)</span>
            </label>
            <input type="text" class="form-control" name="name" placeholder=""
                autocomplete="off" value="{{ old('name', $post->name ?? '') }}">
        </div>
    </div>
</div>
<div class="row mb15">
    <div class="col-lg-12">
        <div class="form-row">
            <label for="" class="control-label text-right">{{ __('messages.description') }}</label>
            <textarea type="text" class="ck-editor" name="description" placeholder="" autocomplete="off"
            id="ckDescription" data-height="100">{{ old('description', $post->description ?? '') }}</textarea>
        </div>
    </div>
</div>
<div class="row mb15">
    <div class="col-lg-12">
        <div class="form-row">
            <div class="">
                <label for="" class="control-label text-right">{{ __('messages.content') }}</label>
                <a href="" class="multipleUploadImageCkeditor text-left">{{ __('messages.upload') }}</a>
            </div>
            <textarea type="text" class="ck-editor" name="content" placeholder="" autocomplete="off"
             id="ckContent" data-height="500">{{ old('content', $post->content ?? '') }}</textarea>
        </div>
    </div>
</div>