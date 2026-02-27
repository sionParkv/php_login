$(document).ready(function () {
    // 좋아요 토글
    $(document).on("click", ".like-btn", function () {

        const btn = $(this);
        const postId = btn.data("post-id");

        $.ajax({
            url: "/board/toggle_like",
            method: "POST",
            data: { post_id: postId },
            dataType: "json",
            success: function (res) {

                if (res.status !== "success") {
                    alert(res.message || "처리 실패");
                    return;
                }

                // 좋아요 숫자 변경
                btn.find(".like-count").text(res.likeCount);

                // 클래스 변경
                if (res.liked) {
                    btn.removeClass("btn-unliked").addClass("btn-liked");
                } else {
                    btn.removeClass("btn-liked").addClass("btn-unliked");
                }
            },
            error: function () {
                alert("서버 오류");
            }
        });
    });

    // 댓글 작성 AJAX
    $(document).on("submit", "#commentForm", function (e) {
        e.preventDefault();

        const form = $(this);
        const postId = form.find("input[name='post_id']").val();
        const content = form.find("textarea[name='content']").val();

        if (!content.trim()) return;

        $.ajax({
            url: "/board/comment_write",
            method: "POST",
            data: { post_id: postId, content: content },
            dataType: "json",
            success: function (res) {
                if (res.status !== "success") {
                    alert(res.message || "댓글 등록 실패");
                    return;
                }

                $("#commentList").append(res.html);
                form.find("textarea").val("");
            },
            error: function () {
                alert("서버 오류");
            }
        });
    });

    $(document).on("click", ".comment-delete-btn", function () {
        const commentId = $(this).data("comment-id");

        if (!confirm("댓글을 삭제할까요?")) return;

        $.ajax({
            url: "/board/comment_delete",
            method: "POST",
            data: { comment_id: commentId },
            dataType: "json",
            success: function (res) {
                if (res.status !== "success") {
                    alert(res.message || "삭제 실패");
                    return;
                }

                $("#comment-" + res.commentId).remove();
            },
            error: function () {
                alert("서버 오류");
            }
        });
    });
});