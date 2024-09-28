<div class="modal fade bd-example-modal-lg" id="createMenuCatalogue" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <form action="" class="create-menu-catalogue" method="">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Thêm mới vị trí hiển thị của Menu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="error form-error"></div>
                    <div class="form-group">
                        <label for="name" class="col-form-label">Tên vị trí hiển thị</label>
                        <input type="text" class="form-control" id="name" name="name">
                        <div class="error name" style="font-style: italic; color:Red;padding-top:5px;"></div>
                    </div>
                    <div class="form-group">
                        <label for="keyword" class="col-form-label">Từ khóa</label>
                        <input type="text" class="form-control" id="keyword" name="keyword">
                        <div class="error keyword" style="font-style: italic; color:Red;padding-top:5px;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="create" value="create" class="btn btn-primary">Send message</button>
                </div>
            </div>
        </div>
    </form>
</div>
