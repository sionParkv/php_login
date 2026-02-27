var JsBoardView = function () {
    this.commentTpl = null;
};

JsBoardView.prototype = {
    init: function () {
        this.compileTemplates();
        this.bindEvents();
    },

    compileTemplates: function () {
        if (typeof _ === "undefined" || !_.template) {
            console.error("underscore template is not loaded.");
            return;
        }

        var tpl = $("script.comment-template").html();
        this.commentTpl = _.template(tpl);
    },

    bindEvents: function () {
        var _this = this;

        // 좋아요
        $(document).on("click", ".btn-like", function () {
            _this.toggleLike($(this));
        });

        // 댓글 등록
        $(document).on("submit", "#commentForm", function (e) {
            e.preventDefault();
            _this.submitComment($(this));
        });

        // 댓글 삭제
        $(document).on("click", ".btn-comment-delete", function () {
            _this.deleteComment($(this));
        });
    },

    escapeHtml: function (str) {
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    },
    // 변수명 디테일
    toggleLike: function ($btn) {
        var postId = $btn.data("post-id");

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

                $btn.find(".like-count").text(res.likeCount);

                if (res.liked) {
                    $btn.removeClass("btn-unliked").addClass("btn-liked");
                } else {
                    $btn.removeClass("btn-liked").addClass("btn-unliked");
                }
            }
        });
    },

    submitComment: function ($form) {
        var _this = this;

        var postId = $form.find("input[name='post_id']").val();
        var content = $form.find("textarea[name='content']").val();

        if (!content || !content.trim()) return;

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

                var c = res.comment;

                // 템플릿에 넣기 전 escape
                var safe = {
                    id: c.id,
                    name: _this.escapeHtml(c.name),
                    content: _this.escapeHtml(c.content),
                    created_at: _this.escapeHtml(c.created_at || ""),
                    canDelete: !!c.canDelete
                };

                $("#commentList").append(_this.commentTpl(safe));
                $form.find("textarea").val("");
            }
        });
    },
    // 내부 함수는 _붙히기
    deleteComment: function ($btn) {
        var commentId = $btn.data("comment-id");

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
            }
        });
    }
};

$(function () {
    var boardView = new JsBoardView();
    boardView.init();
});