<?php $title = '게시글'; require __DIR__.'/_layout_top.php'; ?>

    <div class="topbar">
        <div class="title">
            게시글
            <small><?= esc($totalCount ?? 0) ?></small>
        </div>

        <div class="d-flex gap-2 align-items-center">
            <form method="get" action="/board" class="d-flex searchbox">
                <input name="q" value="<?= esc($q ?? '') ?>" class="form-control form-control-sm" placeholder="Search">
                <button class="btn btn-sm btn-outline-secondary ms-2 w-50" type="submit">검색</button>
            </form>

            <a class="btn btn-sm btn-outline-secondary" href="/auth/logout">로그아웃</a>
        </div>
    </div>

    <div class="table-area">
        <table class="table table-hover mb-0">
            <thead>
            <tr>
                <th style="width:80px;">No</th>
                <th>제목</th>
                <th style="width:140px;">글쓴이</th>
                <th style="width:140px;">작성일</th>
                <th style="width:90px;" class="text-end">조회수</th>
                <th style="width:90px;" class="text-end">좋아요</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($posts as $p): ?>
                <tr>
                    <td><?= esc($p['id']) ?></td>
                    <td>
                        <a class="title-link" href="/board/view/<?= esc($p['id']) ?>">
                            <?= esc($p['title']) ?>
                        </a>
                    </td>
                    <td><?= esc($p['name']) ?></td>
                    <td class="muted"><?= esc(date('Y-m-d', strtotime($p['created_at'] ?? 'now'))) ?></td>
                    <td class="text-end"><?= esc($p['views'] ?? 0) ?></td>
                    <td class="text-end"><?= esc($p['like_count'] ?? 0) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="footerbar">
        <?= $pager->links() ?>
    </div>

    <a href="/board/write" class="btn btn-pink write-fab">글쓰기</a>

<?php require __DIR__.'/_layout_bottom.php'; ?>