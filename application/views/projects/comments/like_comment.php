<?php
$total_likes = "";
if ($comment->total_likes) {
    $data_placement = ($comment->file_id || $comment->task_id) ? "data-placement='right'" : "";
    $total_likes = "<span $data_placement data-toggle='tooltip' title='$comment->comment_likers'><i class='fa fa-thumbs-up text-warning'></i> $comment->total_likes</span>";
}

if ($comment->total_likes) {
    echo $total_likes;
}
?>

<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>