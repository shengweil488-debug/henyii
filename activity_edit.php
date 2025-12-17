<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../src/controllers/ActivityController.php';
require_once __DIR__ . '/../src/controllers/ParticipantController.php';
require_once __DIR__ . '/config/database.php';
$lang = $_SESSION['lang'] ?? 'en';
$langArr = require __DIR__ . '/../lang/' . $lang . '.php';
$user = $_SESSION['user'];
$id = $_GET['id'] ?? null;
if (!$id) { header('Location: dashboard.php'); exit; }
$activity = ActivityController::detail($id);
if (!$activity) { header('Location: dashboard.php'); exit; }
// 权限检查：只有admin或teacher可编辑
if ($user['role'] !== 'admin' && $user['role'] !== 'teacher') {
    header('Location: dashboard.php');
    exit;
}
$participants = ParticipantController::list($id);
// 解析目标、内容、后续
$objectives = json_decode($activity['objectives'] ?? '[]', true);
$content = json_decode($activity['content'] ?? '[]', true);
$followup = json_decode($activity['followup'] ?? '[]', true);
// 查询图片
$stmt = $pdo->prepare('SELECT * FROM activity_files WHERE activity_id = ?');
$stmt->execute([$id]);
$files = $stmt->fetchAll();
$evidence = [];
$photos = [];
foreach ($files as $f) {
    if (stripos($f['file_type'], 'pdf') !== false) {
        $evidence[] = $f;
    } else {
        $photos[] = $f;
    }
}
// 获取老师姓名
$teacher_id = $activity['teacher'];
$teacher_name = '';
if ($teacher_id && is_numeric($teacher_id)) {
    $stmt_teacher = $pdo->prepare('SELECT name FROM users WHERE id = ?');
    $stmt_teacher->execute([$teacher_id]);
    $teacher_name = $stmt_teacher->fetchColumn() ?: '';
} else {
    $teacher_name = $activity['teacher'] ?? '';
}
?>
<!-- 只保留一次jQuery且在所有JS前加载 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- 引入 Bootstrap 和 FontAwesome -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<div class="container my-5">
  <div class="card shadow rounded-4 p-4" style="max-width:900px;margin:auto;">
    <h2 class="fw-bold mb-4 text-center" style="color:#1976d2;letter-spacing:2px;">
        <?php echo $langArr['edit_activity'] ?? '编辑活动'; ?>
    </h2>
    <form method="post" enctype="multipart/form-data" action="activity_edit_handler.php?id=<?php echo $id; ?>">
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['title'] ?? 'Title'; ?></label>
            <input type="text" name="title" class="form-control rounded-pill" value="<?php echo htmlspecialchars($activity['title']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['event_type'] ?? 'Event Type'; ?></label>
            <input type="text" name="event_type" class="form-control rounded-pill" value="<?php echo htmlspecialchars($activity['event_type']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['location'] ?? 'Location'; ?></label>
            <input type="text" name="location" class="form-control rounded-pill" value="<?php echo htmlspecialchars($activity['location']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['date'] ?? 'Date'; ?></label>
            <input type="date" name="date" class="form-control rounded-pill" value="<?php echo $activity['date']; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['organizer'] ?? 'Organizer'; ?></label><br>
            <?php $orgs = explode(',', $activity['organizer']); ?>
            <input type="checkbox" name="organizer[]" value="PPD" <?php if(in_array('PPD',$orgs))echo'checked';?>> <?php echo $langArr['ppd'] ?? 'PPD'; ?>
            <input type="checkbox" name="organizer[]" value="JPN" <?php if(in_array('JPN',$orgs))echo'checked';?>> <?php echo $langArr['jpn'] ?? 'JPN'; ?>
            <input type="checkbox" name="organizer[]" value="KPM" <?php if(in_array('KPM',$orgs))echo'checked';?>> <?php echo $langArr['kpm'] ?? 'KPM'; ?>
            <input type="checkbox" name="organizer[]" value="Others" <?php if(in_array('Others',$orgs))echo'checked';?>> <?php echo $langArr['others'] ?? 'Others'; ?>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['level'] ?? 'Level'; ?></label><br>
            <?php $levels = ["School","Zone/Area","State","National","International"]; foreach($levels as $lv): ?>
                <input type="radio" name="level" value="<?php echo $lv; ?>" <?php if($activity['level']==$lv)echo'checked';?>> <?php echo $langArr[strtolower(str_replace(['/', ' '], ['_', '_'], $lv)).'_level'] ?? $lv; ?>
            <?php endforeach; ?>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['achievement'] ?? 'Achievement'; ?></label><br>
            <?php $achvs = ["Champion","Runner-up","Third Place","Fourth Place","Fifth Place","Other"]; foreach($achvs as $ach): ?>
                <input type="radio" name="achievement" value="<?php echo $ach; ?>" <?php if($activity['achievement']==$ach)echo'checked';?>> <?php echo $langArr[strtolower(str_replace([' ', '-'], ['_', ''], $ach))] ?? $ach; ?>
            <?php endforeach; ?>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['teacher_in_charge'] ?? 'Teacher in Charge'; ?></label>
            <div class="input-group" style="max-width:400px;">
                <input type="text" name="teacher" class="form-control rounded-pill" placeholder="<?php echo $langArr['search_teacher'] ?? '搜索教师'; ?>" value="<?php echo htmlspecialchars($teacher_name); ?>">
                <input type="hidden" name="teacher_id" value="<?php echo is_numeric($activity['teacher']) ? $activity['teacher'] : '' ?>">
                <button type="button" class="btn btn-outline-secondary" id="show-all-teachers" tabindex="-1" style="border-radius:20px;">
                    <?php echo $langArr['browse_all'] ?? '浏览全部'; ?>
                </button>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['participants_stat'] ?? 'Participants (统计)'; ?></label>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th><?php echo $langArr['malay'] ?? 'Malay'; ?></th>
                        <th><?php echo $langArr['chinese'] ?? 'Chinese'; ?></th>
                        <th><?php echo $langArr['indian'] ?? 'Indian'; ?></th>
                        <th><?php echo $langArr['others'] ?? 'Others'; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $langArr['male'] ?? 'Male'; ?></td>
                        <td><input type="number" name="stat_malay_m" class="form-control table-input" value="<?php echo $activity['stat_malay_m']; ?>"></td>
                        <td><input type="number" name="stat_chinese_m" class="form-control table-input" value="<?php echo $activity['stat_chinese_m']; ?>"></td>
                        <td><input type="number" name="stat_indian_m" class="form-control table-input" value="<?php echo $activity['stat_indian_m']; ?>"></td>
                        <td><input type="number" name="stat_others_m" class="form-control table-input" value="<?php echo $activity['stat_others_m']; ?>"></td>
                    </tr>
                    <tr>
                        <td><?php echo $langArr['female'] ?? 'Female'; ?></td>
                        <td><input type="number" name="stat_malay_f" class="form-control table-input" value="<?php echo $activity['stat_malay_f']; ?>"></td>
                        <td><input type="number" name="stat_chinese_f" class="form-control table-input" value="<?php echo $activity['stat_chinese_f']; ?>"></td>
                        <td><input type="number" name="stat_indian_f" class="form-control table-input" value="<?php echo $activity['stat_indian_f']; ?>"></td>
                        <td><input type="number" name="stat_others_f" class="form-control table-input" value="<?php echo $activity['stat_others_f']; ?>"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['objectives'] ?? 'Objectives'; ?></label>
            <div id="objectives-list">
                <?php foreach($objectives as $obj): ?>
                    <input type="text" name="objectives[]" class="form-control mb-2" value="<?php echo htmlspecialchars($obj); ?>">
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" id="add-objective"><?php echo $langArr['add_objective'] ?? 'Add Objective'; ?></button>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['activity_content'] ?? 'Activity Content'; ?></label>
            <div id="content-list">
                <?php foreach($content as $c): ?>
                    <input type="text" name="content[]" class="form-control mb-2" value="<?php echo htmlspecialchars($c); ?>">
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" id="add-content"><?php echo $langArr['add_content'] ?? 'Add Content'; ?></button>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['followup_action'] ?? 'Follow-up Action'; ?></label>
            <div id="followup-list">
                <?php foreach($followup as $f): ?>
                    <input type="text" name="followup[]" class="form-control mb-2" value="<?php echo htmlspecialchars($f); ?>">
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" id="add-followup"><?php echo $langArr['add_followup'] ?? 'Add Follow-up Action'; ?></button>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['list_of_participants'] ?? 'List of Participants'; ?></label>
            <table class="table table-bordered" id="participants-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $langArr['student_name'] ?? 'Student Name'; ?></th>
                        <th><?php echo $langArr['chinese_name'] ?? 'Chinese Name'; ?></th>
                        <th><?php echo $langArr['student_id'] ?? 'Student ID'; ?></th>
                        <th><?php echo $langArr['class'] ?? 'Class'; ?></th>
                        <th><?php echo $langArr['gender'] ?? 'Gender'; ?></th>
                        <th><?php echo $langArr['race'] ?? 'Race'; ?></th>
                        <th><?php echo $langArr['religion'] ?? 'Religion'; ?></th>
                        <th><?php echo $langArr['achievement'] ?? 'Achievement'; ?></th>
                        <th><?php echo $langArr['remove'] ?? 'Remove'; ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php $i=1; foreach($participants as $p): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><input type="text" name="participant_name[]" class="form-control participant-name" value="<?php echo htmlspecialchars($p['student_name'] ?? $p['name'] ?? $p['username'] ?? ''); ?>" autocomplete="off"></td>
                        <td><input type="text" name="participant_chinese_name[]" class="form-control participant-chinese-name" value="<?php
                            $chinese_name = $p['chinese_name'] ?? '';
                            if (!$chinese_name && !empty($p['student_id'])) {
                                $stmt_cn = $pdo->prepare('SELECT chinese_name FROM students WHERE id = ?');
                                $stmt_cn->execute([$p['student_id']]);
                                $chinese_name = $stmt_cn->fetchColumn() ?: '';
                            }
                            echo htmlspecialchars($chinese_name);
                        ?>"></td>
                        <td><input type="text" name="participant_id[]" class="form-control participant-id" value="<?php echo htmlspecialchars($p['student_no'] ?? $p['participant_id'] ?? $p['id'] ?? ''); ?>"></td>
                        <td><input type="text" name="participant_class[]" class="form-control participant-class" value="<?php echo htmlspecialchars($p['class'] ?? ''); ?>"></td>
                        <td><select name="participant_gender[]" class="form-select table-select"><option value="M" <?php if(($p['gender'] ?? '')=='M')echo'selected';?>><?php echo $langArr['male'] ?? 'M'; ?></option><option value="F" <?php if(($p['gender'] ?? '')=='F')echo'selected';?>><?php echo $langArr['female'] ?? 'F'; ?></option></select></td>
                        <td><input type="text" name="participant_race[]" class="form-control" value="<?php echo htmlspecialchars($p['race'] ?? ''); ?>"></td>
                        <td><input type="text" name="participant_religion[]" class="form-control" value="<?php echo htmlspecialchars($p['religion'] ?? ''); ?>"></td>
                        <td><input type="text" name="participant_achievement[]" class="form-control" value="<?php echo htmlspecialchars($p['achievement'] ?? ''); ?>"></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-row"><?php echo $langArr['delete'] ?? 'Delete'; ?></button></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <button type="button" class="btn btn-outline-success btn-sm" id="add-participant"><?php echo $langArr['add_participant'] ?? 'Add Participant'; ?></button>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['upload_certificates'] ?? 'Upload Certificates/Photos'; ?></label>
            <input type="file" name="activity_files[]" class="form-control" multiple accept="image/*,application/pdf">
            <div class="mt-2">
                <?php foreach($evidence as $f): ?>
                    <div class="mb-1"><a href="../<?php echo $f['file_path']; ?>" target="_blank"><?php echo $langArr['view_pdf'] ?? 'View PDF'; ?></a></div>
                <?php endforeach; ?>
                <?php foreach($photos as $f): ?>
                    <div class="mb-1"><img src="../<?php echo $f['file_path']; ?>" alt="<?php echo $langArr['activity_photo'] ?? 'Activity photo'; ?>" style="height:60px;"></div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['visibility_setting'] ?? 'Visibility Setting'; ?></label><br>
            <input type="radio" name="visibility" value="public" <?php if(($activity['visibility']??'')=='public')echo'checked';?>> <?php echo $langArr['public'] ?? 'Public'; ?>
            <input type="radio" name="visibility" value="private" <?php if(($activity['visibility']??'')=='private')echo'checked';?>> <?php echo $langArr['private'] ?? 'Private'; ?>
        </div>
        <div class="d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-primary px-4 rounded-pill"><i class="fa fa-save"></i> <?php echo $langArr['save'] ?? '保存'; ?></button>
            <a href="dashboard.php" class="btn btn-secondary px-4 rounded-pill"><?php echo $langArr['back'] ?? '返回'; ?></a>
        </div>
    </form>
  </div>
</div>
<script>
$(function(){
  try {
    // 动态添加目标、内容、后续行动
    $('#add-objective').click(function(){
        $('#objectives-list').append('<input type="text" name="objectives[]" class="form-control mb-2" placeholder="<?php echo $langArr['objective'] ?? 'Objective'; ?>">');
        console.log('添加目标按钮已点击');
    });
    $('#add-content').click(function(){
        $('#content-list').append('<input type="text" name="content[]" class="form-control mb-2" placeholder="<?php echo $langArr['content'] ?? 'Content'; ?>">');
        console.log('添加内容按钮已点击');
    });
    $('#add-followup').click(function(){
        $('#followup-list').append('<input type="text" name="followup[]" class="form-control mb-2" placeholder="<?php echo $langArr['followup_action'] ?? 'Follow-up Action'; ?>">');
        console.log('添加后续行动按钮已点击');
    });
    // 动态添加/删除参与者
    $('#add-participant').click(function(){
        var idx = $('#participants-table tbody tr').length + 1;
        var row = `<tr>
            <td>`+idx+`</td>
            <td><input type="text" name="participant_name[]" class="form-control participant-name" placeholder="<?php echo $langArr['search_student'] ?? 'Search...'; ?>" autocomplete="off"></td>
            <td><input type="text" name="participant_chinese_name[]" class="form-control participant-chinese-name"></td>
            <td><input type="text" name="participant_id[]" class="form-control participant-id"></td>
            <td><input type="text" name="participant_class[]" class="form-control participant-class"></td>
            <td><select name="participant_gender[]" class="form-select table-select"><option value="M"><?php echo $langArr['male'] ?? 'M'; ?></option><option value="F"><?php echo $langArr['female'] ?? 'F'; ?></option></select></td>
            <td><input type="text" name="participant_race[]" class="form-control"></td>
            <td><input type="text" name="participant_religion[]" class="form-control"></td>
            <td><input type="text" name="participant_achievement[]" class="form-control"></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-row"><?php echo $langArr['delete'] ?? 'Delete'; ?></button></td>
        </tr>`;
        $('#participants-table tbody').append(row);
        console.log('添加参与者按钮已点击');
    });
    $(document).on('click', '.remove-row', function(){
        $(this).closest('tr').remove();
        // 重新编号
        $('#participants-table tbody tr').each(function(i){
            $(this).find('td:first').text(i+1);
        });
        console.log('删除参与者按钮已点击');
    });
    // 参与者自动补全（需配合api_student_search.php）
    $(document).on('input', '.participant-name', function(){
        var input = $(this);
        var q = input.val();
        if(q.length < 1) return;
        $.get('api_student_search.php', {q: q}, function(data){
            input.siblings('.autocomplete-list').remove();
            if(data.length > 0){
                var list = $('<ul class="list-group position-absolute autocomplete-list" style="z-index:1000;"></ul>');
                data.forEach(function(s){
                    var item = $('<li class="list-group-item list-group-item-action" style="cursor:pointer">'+s.student_no+' - '+s.name+'</li>');
                    item.data('student', s);
                    list.append(item);
                });
                input.after(list);
            } else {
                var list = $('<ul class="list-group position-absolute autocomplete-list" style="z-index:1000;"><li class="list-group-item text-danger">未找到学生，请先在学生管理中添加</li></ul>');
                input.after(list);
            }
        });
    });
    // 在自动补全JS中，选择学生时自动填写中文名字
    $(document).on('click', '.list-group-item', function(){
        var s = $(this).data('student');
        if (!s) return;
        var row = $(this).closest('tr');
        row.find('.participant-name').val(s.name);
        row.find('.participant-chinese-name').val(s.chinese_name || '');
        row.find('.participant-id').val(s.student_no || s.id || '');
        row.find('.participant-class').val(s.class || '');
        // 性别映射（更健壮，兼容大小写、空格、常见拼写）
        var gender = (s.gender || '').toString().trim().toLowerCase();
        if (gender === '男' || gender === 'male' || gender === 'm') gender = 'M';
        else if (gender === '女' || gender === 'female' || gender === 'f') gender = 'F';
        else gender = '';
        row.find('select[name^="participant_gender"]').val(gender);
        row.find('.participant-race, [name^="participant_race"]').val(s.race || '');
        row.find('.participant-religion, [name^="participant_religion"]').val(s.religion || '');
        row.find('ul.list-group').remove();
    });
    $(document).on('blur', '.participant-name', function(){
        setTimeout(()=>{$(this).siblings('ul.list-group').remove();}, 200);
    });
    // 监听学号输入，自动带出学生信息
    $(document).on('blur', '.participant-id', function(){
        var input = $(this);
        var student_no = input.val().trim();
        if (!student_no) return;
        $.get('api_student_search.php', {q: student_no}, function(data){
            if (data && data.length > 0) {
                var s = data[0];
                var row = input.closest('tr');
                row.find('.participant-name').val(s.name || '');
                row.find('.participant-chinese-name').val(s.chinese_name || '');
                row.find('.participant-class').val(s.class || '');
                // 性别映射
                var gender = s.gender || '';
                if (gender === '男' || gender === 'Male') gender = 'M';
                if (gender === '女' || gender === 'Female') gender = 'F';
                row.find('select[name^="participant_gender"]').val(gender);
                row.find('.participant-race, [name^="participant_race"]').val(s.race || '');
                row.find('.participant-religion, [name^="participant_religion"]').val(s.religion || '');
            }
        });
    });
  } catch(e) {
    console.error('JS事件绑定异常:', e);
  }
});
// 老师自动补全、分页、选择逻辑
$(function(){
  // 确保有隐藏域
  if ($('input[name="teacher_id"]').length === 0) {
    $('input[name="teacher"]').after('<input type="hidden" name="teacher_id">');
  }
});
function renderTeacherDropdown(list, page, total, pageSize) {
    var ul = $('<ul class="list-group position-absolute autocomplete-list" style="z-index:1000;width:100%;max-height:260px;overflow:auto;"></ul>');
    list.forEach(function(t){
        var item = $('<li class="list-group-item list-group-item-action" style="cursor:pointer">'+t.name+'（'+t.username+'）</li>');
        item.data('teacher', t);
        ul.append(item);
    });
    var totalPages = Math.ceil(total / pageSize);
    if (totalPages > 1) {
        var pager = $('<div class="d-flex justify-content-center align-items-center py-1" style="background:#f9f9f9;border-top:1px solid #eee;"></div>');
        if (page > 1) pager.append('<a href="#" class="teacher-page-link me-2" data-page="'+(page-1)+'">上一页</a>');
        for (var i = 1; i <= totalPages; i++) {
            var link = $('<a href="#" class="teacher-page-link mx-1" data-page="'+i+'">'+i+'</a>');
            if (i === page) link.css({'font-weight':'bold','color':'#1976d2'});
            pager.append(link);
        }
        if (page < totalPages) pager.append('<a href="#" class="teacher-page-link ms-2" data-page="'+(page+1)+'">下一页</a>');
        ul.append($('<li class="list-group-item p-1">').append(pager));
    }
    return ul;
}
$('input[name="teacher"]').on('input', function(){
    var input = $(this);
    var q = input.val();
    input.siblings('.autocomplete-list').remove();
    if(q.length < 1) return;
    $.get('api_teacher_search.php', {q: q, page: 1, pageSize: 10}, function(res){
        var data = res.data || res;
        var total = res.total || data.length;
        var page = res.page || 1;
        var pageSize = res.pageSize || 10;
        if(data.length > 0){
            var list = renderTeacherDropdown(data, page, total, pageSize);
            input.after(list);
        }
    });
});
$(document).on('click', '#show-all-teachers', function(e){
    e.preventDefault();
    var input = $(this).closest('.input-group').find('input[name="teacher"]');
    input.siblings('.autocomplete-list').remove();
    $.get('api_teacher_search.php', {q: '', page: 1, pageSize: 10}, function(res){
        var data = res.data || res;
        var total = res.total || data.length;
        var page = res.page || 1;
        var pageSize = res.pageSize || 10;
        if(data.length > 0){
            var list = renderTeacherDropdown(data, page, total, pageSize);
            input.after(list);
        }
    });
});
$(document).on('click', '.teacher-page-link', function(e){
    e.preventDefault();
    var page = $(this).data('page');
    var input = $('input[name="teacher"]');
    var q = input.val();
    input.siblings('.autocomplete-list').remove();
    $.get('api_teacher_search.php', {q: q, page: page, pageSize: 10}, function(res){
        var data = res.data || res;
        var total = res.total || data.length;
        var page = res.page || 1;
        var pageSize = res.pageSize || 10;
        if(data.length > 0){
            var list = renderTeacherDropdown(data, page, total, pageSize);
            input.after(list);
        }
    });
});
$(document).on('click', '.autocomplete-list .list-group-item', function(){
    var t = $(this).data('teacher');
    if (!t) return;
    var input = $(this).closest('.input-group').find('input[name="teacher"]');
    input.val(t.name);
    input.siblings('input[name="teacher_id"]').val(t.id);
    $(this).parent().remove();
});
$('input[name="teacher"]').on('input', function(){
    if (!$(this).val()) $(this).siblings('input[name="teacher_id"]').val('');
});
$(document).on('blur', 'input[name="teacher"]', function(){
    var input = $(this);
    setTimeout(function(){ input.siblings('.autocomplete-list').remove(); }, 200);
});
</script>
<style>
body { background: #f4f8fb; font-family: 'Montserrat', Arial, sans-serif; }
.card { border: none; }
.form-control, .form-select { border-radius: 20px; }
.btn { border-radius: 20px; font-weight: 600; }
</style> 