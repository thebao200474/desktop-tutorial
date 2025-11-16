<?php
use function htmlspecialchars as h;
?>
<section class="mb-4">
    <p class="text-uppercase text-primary fw-semibold mb-1">ChemLearn Q&A</p>
    <h1 class="display-6 fw-bold">Đặt câu hỏi mới</h1>
    <p class="text-muted">Trình bày vấn đề càng chi tiết, bạn càng dễ nhận được câu trả lời chính xác.</p>
</section>

<section class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="<?= app_url('hoi-dap/hoi'); ?>" method="post" enctype="multipart/form-data" class="vstack gap-4">
            <input type="hidden" name="csrf_token" value="<?= h($csrfToken); ?>">

            <div>
                <label class="form-label fw-semibold" for="questionTitle">Tiêu đề</label>
                <input id="questionTitle" name="tieu_de" type="text" class="form-control form-control-lg"
                       placeholder="Tiêu đề là một đoạn đầu của đề bài" required>
            </div>

            <div>
                <label class="form-label fw-semibold" for="questionContent">Mô tả chi tiết</label>
                <textarea id="questionContent" name="noi_dung_html"></textarea>
                <small class="text-muted">Bạn có thể chèn công thức, hình ảnh, liên kết, bảng…</small>
            </div>

            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <label class="form-label fw-semibold" for="attachments">Đính kèm file</label>
                    <input id="attachments" class="form-control" type="file" name="attachments[]" multiple>
                    <small class="text-muted">Hỗ trợ hình ảnh, PDF, Word… (tối đa 5MB mỗi file).</small>
                </div>
                <div class="col-12 col-lg-6">
                    <label class="form-label fw-semibold" for="externalLink">Thêm liên kết minh họa</label>
                    <input id="externalLink" name="external_link" type="url" class="form-control"
                           placeholder="https://ví_dụ.com/tai-lieu">
                </div>
            </div>

            <div class="text-end">
                <a class="btn btn-outline-secondary" href="<?= app_url('hoi-dap'); ?>">Hủy</a>
                <button class="btn btn-primary px-4" type="submit">Gửi câu hỏi</button>
            </div>
        </form>
    </div>
</section>

<script src="https://cdn.ckeditor.com/4.25.0-lts/standard-all/ckeditor.js"></script>
<script>
    CKEDITOR.config.versionCheck = false;
    CKEDITOR.replace('questionContent', {
        language: 'vi',
        height: 360,
        removePlugins: 'elementspath',
        extraPlugins: 'autogrow',
        autoGrow_onStartup: true,
        toolbar: [
            { name: 'clipboard', items: ['Undo', 'Redo'] },
            { name: 'styles', items: ['Format'] },
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'RemoveFormat'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight'] },
            { name: 'insert', items: ['Link', 'Unlink', 'Image', 'Table'] },
            { name: 'tools', items: ['Maximize', 'Source'] }
        ],
    });
</script>
