$(document).ready(function () {

    /* ==========================
       좋아요 토글
    ========================== */

    $(document).on("click", ".like-btn", function () {

        const btn = $(this);
        const postId = btn.data("post-id");

        $.ajax({
            url: "/api/like/toggle",
            method: "POST",
            data: { post_id: postId },
            dataType: "json",
            success: function (res) {

                if (res.status !== "success") {
                    alert("처리 실패");
                    return;
                }

                btn.find(".like-count").text(res.likeCount);

                if (res.liked) {
                    btn.addClass("text-danger");
                } else {
                    btn.removeClass("text-danger");
                }
            },
            error: function () {
                alert("서버 오류");
            }
        });
    });


    /* ==========================
       댓글 작성 AJAX
    ========================== */

    $(document).on("submit", "#commentForm", function (e) {

        e.preventDefault();

        const form = $(this);
        const postId = form.find("input[name='post_id']").val();
        const content = form.find("textarea[name='content']").val();

        if (!content.trim()) return;

        $.ajax({
            url: "/api/comment/write",
            method: "POST",
            data: {
                post_id: postId,
                content: content
            },
            dataType: "json",
            success: function (res) {

                if (res.status !== "success") {
                    alert("댓글 등록 실패");
                    return;
                }

                // 댓글 목록에 바로 추가
                $("#commentList").append(res.html);

                form.find("textarea").val("");
            }
        });
    });

});