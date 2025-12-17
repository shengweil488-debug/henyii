<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../src/controllers/ParticipantController.php';
$lang = $_SESSION['lang'] ?? 'en';
$langArr = require __DIR__ . '/../lang/' . $lang . '.php';
$activity_id = $_GET['activity_id'] ?? null;
if (!$activity_id) { header('Location: dashboard.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    if (ParticipantController::add($activity_id, null, $student_id)) {
        header('Location: participant_list.php?activity_id=' . $activity_id);
        exit;
    } else {
        $error = 'Failed to add participant!';
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $langArr['add_participant']; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2><?php echo $langArr['add_participant']; ?></h2>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
    <form method="post" id="addForm">
        <div class="mb-3">
            <label class="form-label">Search Student</label>
            <input type="text" id="student_search" class="form-control" autocomplete="off" placeholder="Enter student no or name">
            <input type="hidden" name="student_id" id="student_id">
        </div>
        <div id="student_info" style="display:none;" class="mb-3 border p-2"></div>
        <div id="student_fields" style="display:none;">
            <div class="mb-2"><label>学号</label><input type="text" class="form-control" name="student_no" id="student_no"></div>
            <div class="mb-2"><label>姓名</label><input type="text" class="form-control" name="name" id="name"></div>
            <div class="mb-2"><label>班级</label><input type="text" class="form-control" name="class" id="class"></div>
            <div class="mb-2"><label>性别</label><input type="text" class="form-control" name="gender" id="gender"></div>
            <div class="mb-2"><label>民族</label><input type="text" class="form-control" name="race" id="race"></div>
            <div class="mb-2"><label>宗教</label><input type="text" class="form-control" name="religion" id="religion"></div>
            <div class="mb-2"><label>邮箱</label><input type="text" class="form-control" name="email" id="email"></div>
        </div>
        <button type="submit" class="btn btn-success">Add</button>
        <a href="participant_list.php?activity_id=<?php echo $activity_id; ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<script>
$(function() {
    $('#student_search').on('input', function() {
        var q = $(this).val();
        if (q.length < 1) { $('#student_info').hide(); $('#student_fields').hide(); return; }
        $.get('api_student_search.php', {q: q}, function(data) {
            if (data.length > 0) {
                var html = '<ul class="list-group">';
                data.forEach(function(s) {
                    html += '<li class="list-group-item list-group-item-action" style="cursor:pointer" data-id="'+s.id+'" data-name="'+s.name+'" data-no="'+s.student_no+'" data-class="'+s.class+'" data-gender="'+s.gender+'" data-race="'+s.race+'" data-religion="'+s.religion+'" data-email="'+s.email+'">'+s.student_no+' - '+s.name+'</li>';
                });
                html += '</ul>';
                $('#student_info').html(html).show();
            } else {
                $('#student_info').hide(); $('#student_fields').hide();
            }
        });
    });
    $(document).on('click', '.list-group-item', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var no = $(this).data('no');
        var sclass = $(this).data('class');
        var gender = $(this).data('gender');
        var race = $(this).data('race');
        var religion = $(this).data('religion');
        var email = $(this).data('email');
        $('#student_id').val(id);
        $('#student_search').val(no + ' - ' + name);
        $('#student_no').val(no);
        $('#name').val(name);
        $('#class').val(sclass);
        $('#gender').val(gender);
        $('#race').val(race);
        $('#religion').val(religion);
        $('#email').val(email);
        $('#student_info').hide();
        $('#student_fields').show();
    });
});
</script>
</body>
</html> 