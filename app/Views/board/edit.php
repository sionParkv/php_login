<?php $title = '수정'; include __DIR__.'/_layout_top.php'; ?>

    <div class="topbar">
        <div class="title">글 수정</div>
        <div class="d-flex gap-2">
            <a class="btn btn-sm btn-outline-secondary" href="/board/view/<?= esc($post['id']) ?>">상세</a>
            <a class="btn btn-sm btn-outline-secondary" href="/board">목록</a>
        </div>
    </div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mt-3"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

    <form method="post" action="/board/edit_post/<?= esc($post['id']) ?>" class="mt-3">
        <div class="mb-3">
            <label class="form-label">제목</label>
            <input name="title" class="form-control" value="<?= esc($post['title']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">내용</label>
            <textarea name="content" class="form-control" rows="10"><?= esc($post['content']) ?></textarea>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="/board/view/<?= esc($post['id']) ?>" class="btn btn-outline-secondary">취소</a>
            <button class="btn btn-pink">저장</button>
        </div>
    </form>

<?php include __DIR__.'/_layout_bottom.php'; ?>