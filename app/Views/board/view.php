<?php $title = '상세보기'; include __DIR__.'/_layout_top.php'; ?>

    <div class="topbar">
        <div class="title">상세보기</div>
        <div class="d-flex gap-2">
            <a class="btn btn-sm btn-outline-secondary" href="/board">목록</a>
            <a class="btn btn-sm btn-outline-secondary" href="/auth/logout">로그아웃</a>
        </div>
    </div>

    <div class="mt-3">
        <h5 style="color:#192A3E; font-weight:800;"><?= esc($post['title']) ?></h5>

        <div class="meta-row mt-2 d-flex gap-2">
            <div><b>글쓴이</b> <?= esc($post['name']) ?></div>
            <div><b>작성시간</b> <?= esc($post['created_at']) ?></div>
            <div><b>조회수</b> <?= esc($post['views']) ?></div>
            <div><b>좋아요</b> <?= esc($likeCount) ?></div>
        </div>

        <hr>

        <div class="post-content-scroll" style="min-height: 100px">
            <?= esc($post['content']) ?>
        </div>

        <div class="d-flex justify-content-center align-items-center mt-4">
            <button type="button"
                    class="btn btn-sm btn-like <?= $liked ? 'btn-liked' : 'btn-unliked' ?>"
                    data-post-id="<?= esc($post['id']) ?>">
                좋아요 (<span class="like-count"><?= esc($likeCount) ?></span>)
            </button>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <?php if ((int)$post['user_id'] === (int)session()->get('user_id')): ?>
                <a class="btn btn-sm btn-outline-secondary" href="/board/edit/<?= esc($post['id']) ?>">수정</a>
                <a class="btn btn-sm btn-outline-danger" href="/board/delete/<?= esc($post['id']) ?>" onclick="return confirm('삭제할까요?');">삭제</a>
            <?php endif; ?>
        </div>
    </div>

    <hr class="mt-4">

    <div class="mt-3">
        <h6 style="color:#192A3E; font-weight:800;">댓글</h6>

        <div id="commentList" class="mt-3 comment-list-scroll">
            <?php foreach ($comments as $c): ?>
                <div id="comment-<?= esc($c['id']) ?>" class="py-2" style="border-bottom:1px solid #eee;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div style="font-weight:700; color:#192A3E;"><?= esc($c['name']) ?></div>

                        <div class="d-flex gap-2 align-items-center">
                            <div class="muted" style="font-size:12px;"><?= esc($c['created_at'] ?? '') ?></div>

                            <?php if ((int)$c['user_id'] === (int)session()->get('user_id')): ?>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger btn-comment-delete"
                                        data-comment-id="<?= esc($c['id']) ?>">
                                    삭제
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="color:#192A3E; margin-top:4px;">
                        <?= esc($c['content']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <form id="commentForm" class="mt-3">
            <input type="hidden" name="post_id" value="<?= esc($post['id']) ?>">
            <textarea name="content" class="form-control" rows="3" placeholder="댓글을 입력하세요"></textarea>
            <div class="d-flex justify-content-end mt-2">
                <button class="btn btn-pink btn-sm">댓글 등록</button>
            </div>
        </form>
    </div>

    <script type="text/template" class="comment-template">
        <div id="comment-<%=id%>" class="py-2" style="border-bottom:1px solid #eee;">
            <div class="d-flex justify-content-between align-items-center">
                <div style="font-weight:700; color:#192A3E;"><%=name%></div>

                <div class="d-flex gap-2 align-items-center">
                    <div class="muted" style="font-size:12px;"><%=created_at || ''%></div>

                    <% if (canDelete) { %>
                    <button type="button"
                            class="btn btn-sm btn-outline-danger btn-comment-delete"
                            data-comment-id="<%=id%>">
                        삭제
                    </button>
                    <% } %>
                </div>
            </div>

            <div style="white-space:pre-wrap; color:#192A3E; margin-top:4px;"><%=content%></div>
        </div>
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/underscore@1.13.6/underscore-umd-min.js"></script>
    <script src="/assets/js/board.js"></script>

<?php include __DIR__.'/_layout_bottom.php'; ?>