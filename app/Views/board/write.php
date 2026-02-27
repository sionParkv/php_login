<?php $title = '글쓰기'; include __DIR__.'/_layout_top.php'; ?>

    <div class="topbar">
        <div class="title">글쓰기</div>
        <div class="d-flex gap-2">
            <a class="btn btn-sm btn-outline-secondary" href="/board">목록</a>
            <a class="btn btn-sm btn-outline-secondary" href="/auth/logout">로그아웃</a>
        </div>
    </div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mt-3"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

    <form method="post" action="/board/write_post" class="mt-3">
        <div class="mb-3">
            <label class="form-label">제목</label>
            <input name="title" class="form-control" placeholder="제목을 입력하세요">
        </div>

        <div class="mb-3">
            <label class="form-label">내용</label>
            <textarea name="content" class="form-control" rows="10" placeholder="내용을 입력하세요"></textarea>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="/board" class="btn btn-outline-secondary">취소</a>
            <button class="btn btn-pink">등록</button>
        </div>
    </form>

<?php include __DIR__.'/_layout_bottom.php'; ?>