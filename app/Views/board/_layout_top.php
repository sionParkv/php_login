<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?= esc($title ?? 'BoardController') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background:#192A3E; margin:0; overflow:hidden; }

        .page-wrap{
            max-width:1080px;
            width:calc(100% - 60px);
            margin:30px auto;
            background:#fff;
            border-radius:10px;
            padding:18px;
            height:calc(100vh - 60px);
            display:flex;
            flex-direction:column;
            position:relative;
            box-shadow:0 10px 30px rgba(0,0,0,0.15);
        }

        .post-content-scroll {
            max-height: 45vh;          /* 화면 높이 기준 */
            overflow-y: auto;
            overflow-x: hidden;
            white-space: pre-wrap;
            overflow-wrap: anywhere;   /* 긴 단어/URL도 줄바꿈 */
            color: #192A3E;
            padding-right: 6px;        /* 스크롤바 겹침 방지(선택) */
        }

        /* 댓글 리스트 스크롤 */
        .comment-list-scroll {
            max-height: 33vh;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 6px;
        }

        .btn-like {
            border: 1px solid #ced4da;
            background: #fff;
            color: #6c757d;
            transition: all 0.2s ease;
        }

        .btn-like.btn-liked {
            border-color: #FF10B4;
            color: #FF10B4;
            background: #fff;
        }

        .btn-like:hover {
            border-color: #FF10B4;
            color: #FF10B4;
        }

        .topbar{ display:flex; justify-content:space-between; align-items:center; padding:6px 2px 14px; border-bottom:1px solid #e9ecef; }
        .title{ font-weight:800; color:#192A3E; }
        .title small{ font-weight:600; color:#6c757d; margin-left:8px; }

        .searchbox{ width:320px; }
        .btn-pink{ background:#FF10B4; color:#fff; border:none; }
        .btn-pink:hover{ background:#e6009e; color:#fff; }

        .table-area{ flex:1 1 auto; padding-top:10px; overflow:hidden; }
        .footerbar{ flex:0 0 auto; display:flex; justify-content:center; padding-top:14px; }

        .title-link{ text-decoration:none; color:#192A3E; font-weight:600; }
        .title-link:hover{ color:#FF10B4; }
        .muted{ color:#6c757d; }

        /* ✅ 글쓰기 버튼을 "박스 우측 하단" */
        .write-fab{
            position:absolute;
            right:18px;
            bottom:18px;
            border-radius:10px;
            padding:10px 16px;
            z-index:5;
        }
    </style>
</head>
<body>
<div class="page-wrap">