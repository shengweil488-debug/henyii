<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
$lang = $_SESSION['lang'] ?? 'en';
$langArr = require __DIR__ . '/../lang/' . $lang . '.php';
?>
<!-- 引入 Bootstrap 和 FontAwesome 之前加上 jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- 引入 Bootstrap 和 FontAwesome -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<div class="container my-5">
  <div class="card shadow rounded-4 p-4" style="max-width:900px;margin:auto;">
    <h2 class="fw-bold mb-4 text-center" style="color:#1976d2;letter-spacing:2px;"><?php echo $langArr['create_activity'] ?? '创建活动'; ?></h2>
    <form method="post" enctype="multipart/form-data" action="activity_create_handler.php">
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['title'] ?? 'Title'; ?></label>
            <input type="text" name="title" class="form-control rounded-pill" required>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['event_type'] ?? 'Event Type'; ?></label>
            <input type="text" name="event_type" class="form-control rounded-pill">
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['location'] ?? 'Location'; ?></label>
            <input type="text" name="location" class="form-control rounded-pill">
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['date'] ?? 'Date'; ?></label>
            <input type="date" name="date" class="form-control rounded-pill">
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['organizer'] ?? 'Organizer'; ?></label><br>
            <input type="checkbox" name="organizer[]" value="PPD"> PPD
            <input type="checkbox" name="organizer[]" value="JPN"> JPN
            <input type="checkbox" name="organizer[]" value="KPM"> KPM
            <input type="checkbox" name="organizer[]" value="Others"> <?php echo $langArr['others'] ?? 'Others'; ?>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['level'] ?? 'Level'; ?></label><br>
            <input type="radio" name="level" value="School"> <?php echo $langArr['school'] ?? 'School'; ?>
            <input type="radio" name="level" value="Zone/Area"> <?php echo $langArr['zone_area'] ?? 'Zone/Area'; ?>
            <input type="radio" name="level" value="State"> <?php echo $langArr['state'] ?? 'State'; ?>
            <input type="radio" name="level" value="National"> <?php echo $langArr['national'] ?? 'National'; ?>
            <input type="radio" name="level" value="International"> <?php echo $langArr['international'] ?? 'International'; ?>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['achievement'] ?? 'Achievement'; ?></label><br>
            <input type="radio" name="achievement" value="Champion"> <?php echo $langArr['champion'] ?? 'Champion'; ?>
            <input type="radio" name="achievement" value="Runner-up"> <?php echo $langArr['runner_up'] ?? 'Runner-up'; ?>
            <input type="radio" name="achievement" value="Third Place"> <?php echo $langArr['third_place'] ?? 'Third Place'; ?>
            <input type="radio" name="achievement" value="Fourth Place"> <?php echo $langArr['fourth_place'] ?? 'Fourth Place'; ?>
            <input type="radio" name="achievement" value="Fifth Place"> <?php echo $langArr['fifth_place'] ?? 'Fifth Place'; ?>
            <input type="radio" name="achievement" value="Other"> <?php echo $langArr['other'] ?? 'Other'; ?>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['teacher'] ?? '负责人教师'; ?></label>
            <div class="input-group" style="max-width:400px;">
                <input type="text" name="teacher" class="form-control rounded-pill" placeholder="<?php echo $langArr['search_teacher'] ?? '搜索教师'; ?>">
                <button type="button" class="btn btn-outline-secondary" id="show-all-teachers" tabindex="-1" style="border-radius:20px;"><?php echo $langArr['browse_all'] ?? '浏览全部'; ?></button>
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
                        <td><input type="number" name="stat_malay_m" class="form-control table-input rounded-pill" value="0"></td>
                        <td><input type="number" name="stat_chinese_m" class="form-control table-input rounded-pill" value="0"></td>
                        <td><input type="number" name="stat_indian_m" class="form-control table-input rounded-pill" value="0"></td>
                        <td><input type="number" name="stat_others_m" class="form-control table-input rounded-pill" value="0"></td>
                    </tr>
                    <tr>
                        <td><?php echo $langArr['female'] ?? 'Female'; ?></td>
                        <td><input type="number" name="stat_malay_f" class="form-control table-input rounded-pill" value="0"></td>
                        <td><input type="number" name="stat_chinese_f" class="form-control table-input rounded-pill" value="0"></td>
                        <td><input type="number" name="stat_indian_f" class="form-control table-input rounded-pill" value="0"></td>
                        <td><input type="number" name="stat_others_f" class="form-control table-input rounded-pill" value="0"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['objectives'] ?? 'Objectives'; ?></label>
            <div id="objectives-list">
                <input type="text" name="objectives[]" class="form-control mb-2 rounded-pill" placeholder="<?php echo addslashes($langArr['objective'] ?? 'Objective'); ?>">
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" id="add-objective"><i class="fas fa-plus"></i> <?php echo $langArr['add_objective'] ?? '添加目标'; ?></button>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['activity_content'] ?? 'Activity Content'; ?></label>
            <div id="content-list">
                <input type="text" name="content[]" class="form-control mb-2 rounded-pill" placeholder="<?php echo addslashes($langArr['content'] ?? 'Content'); ?>">
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" id="add-content"><i class="fas fa-plus"></i> <?php echo $langArr['add_content'] ?? '添加内容'; ?></button>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['followup_action'] ?? 'Follow-up Action'; ?></label>
            <div id="followup-list">
                <input type="text" name="followup[]" class="form-control mb-2 rounded-pill" placeholder="<?php echo addslashes($langArr['followup_action'] ?? 'Follow-up Action'); ?>">
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" id="add-followup"><i class="fas fa-plus"></i> <?php echo $langArr['add_followup'] ?? '添加后续行动'; ?></button>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['list_of_participants'] ?? 'List of Participants'; ?></label>
            <button type="button" class="btn btn-outline-info btn-sm mb-2 rounded-pill" id="import-participants"><i class="fas fa-file-excel"></i> <?php echo $langArr['import_excel'] ?? '导入Excel'; ?></button>
            <table class="table table-bordered" id="participants-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $langArr['name'] ?? 'Name'; ?></th>
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
                    <tr>
                        <td>1</td>
                        <td><input type="text" name="participant_name[]" class="form-control participant-name rounded-pill" placeholder="Search..." autocomplete="off"></td>
                        <td><input type="text" name="participant_chinese_name[]" class="form-control participant-chinese-name rounded-pill"></td>
                        <td><input type="text" name="participant_id[]" class="form-control participant-id rounded-pill"></td>
                        <td><input type="text" name="participant_class[]" class="form-control participant-class rounded-pill"></td>
                        <td><select name="participant_gender[]" class="form-select table-select rounded-pill"><option value="M">M</option><option value="F">F</option></select></td>
                        <td><input type="text" name="participant_race[]" class="form-control rounded-pill"></td>
                        <td><input type="text" name="participant_religion[]" class="form-control rounded-pill"></td>
                        <td><input type="text" name="participant_achievement[]" class="form-control rounded-pill"></td>
                        <td><button type="button" class="btn btn-danger btn-sm rounded-pill remove-row"><i class="fas fa-trash-alt"></i></button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-outline-success btn-sm rounded-pill" id="add-participant"><i class="fas fa-plus"></i> <?php echo $langArr['add_participant'] ?? '添加参与者'; ?></button>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['upload_certificates'] ?? 'Upload Certificates/Photos'; ?></label>
            <input type="file" name="activity_files[]" class="form-control rounded-pill" multiple accept="image/*,application/pdf">
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $langArr['visibility_setting'] ?? 'Visibility Setting'; ?></label><br>
            <input type="radio" name="visibility" value="public" checked> <?php echo $langArr['public'] ?? 'Public'; ?>
            <input type="radio" name="visibility" value="private"> <?php echo $langArr['private'] ?? 'Private'; ?>
        </div>
        <div class="d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-primary px-4 rounded-pill"><i class="fa fa-plus"></i> <?php echo $langArr['create'] ?? '创建'; ?></button>
            <a href="dashboard.php" class="btn btn-secondary px-4 rounded-pill"><?php echo $langArr['return'] ?? '返回'; ?></a>
        </div>
    </form>
  </div>
</div>
<div class="night-toggle" id="nightToggleBtn" title="夜间/白天模式" style="position:fixed;top:32px;right:40px;z-index:1000;">
    <i class="fas fa-moon"></i>
</div>
<script>
console.log('JS loaded');
// 动态添加目标、内容、后续行动
$('#add-objective').click(function(){
    $('#objectives-list').append('<input type="text" name="objectives[]" class="form-control mb-2 rounded-pill" placeholder="<?php echo addslashes($langArr['objective'] ?? 'Objective'); ?>">');
});
$('#add-content').click(function(){
    $('#content-list').append('<input type="text" name="content[]" class="form-control mb-2 rounded-pill" placeholder="<?php echo addslashes($langArr['content'] ?? 'Content'); ?>">');
});
$('#add-followup').click(function(){
    $('#followup-list').append('<input type="text" name="followup[]" class="form-control mb-2 rounded-pill" placeholder="<?php echo addslashes($langArr['followup_action'] ?? 'Follow-up Action'); ?>">');
});
// 动态添加/删除参与者
$('#add-participant').click(function(){
    var idx = $('#participants-table tbody tr').length + 1;
    var row = `<tr>
        <td>`+idx+`</td>
        <td><input type="text" name="participant_name[]" class="form-control participant-name rounded-pill" placeholder="<?php echo addslashes($langArr['search'] ?? 'Search...'); ?>" autocomplete="off"></td>
        <td><input type="text" name="participant_chinese_name[]" class="form-control participant-chinese-name rounded-pill"></td>
        <td><input type="text" name="participant_id[]" class="form-control participant-id rounded-pill"></td>
        <td><input type="text" name="participant_class[]" class="form-control participant-class rounded-pill"></td>
        <td><select name="participant_gender[]" class="form-select table-select rounded-pill"><option value="M">M</option><option value="F">F</option></select></td>
        <td><input type="text" name="participant_race[]" class="form-control rounded-pill"></td>
        <td><input type="text" name="participant_religion[]" class="form-control rounded-pill"></td>
        <td><input type="text" name="participant_achievement[]" class="form-control rounded-pill"></td>
        <td><button type="button" class="btn btn-danger btn-sm rounded-pill remove-row"><i class="fas fa-trash-alt"></i></button></td>
    </tr>`;
    $('#participants-table tbody').append(row);
});
$(document).on('click', '.remove-row', function(){
    $(this).closest('tr').remove();
    // 重新编号
    $('#participants-table tbody tr').each(function(i){
        $(this).find('td:first').text(i+1);
    });
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
            var msg = "<?php echo addslashes($langArr['student_not_found'] ?? '未找到学生，请先在学生管理中添加'); ?>";
            var list = $('<ul class="list-group position-absolute autocomplete-list" style="z-index:1000;"><li class="list-group-item text-danger">'+msg+'</li></ul>');
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
// 分页下拉
function renderTeacherDropdown(list, page, total, pageSize) {
    var ul = $('<ul class="list-group position-absolute autocomplete-list" style="z-index:1000;width:100%;max-height:260px;overflow:auto;"></ul>');
    list.forEach(function(t){
        var item = $('<li class="list-group-item list-group-item-action" style="cursor:pointer">'+t.name+'（'+t.username+'）</li>');
        item.data('teacher', t);
        ul.append(item);
    });
    // 分页
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
// 输入时自动补全
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
// 浏览全部
// 移除自动插入a标签的逻辑，只保留事件绑定
$(document).on('click', '#show-all-teachers', function(e){
    e.preventDefault();
    var input = $(this).closest('.input-group').find('input[name="teacher"]');
    input.siblings('.autocomplete-list').remove();
    $.get('api_teacher_search.php', {q: '', page: 1, pageSize: 10}, function(res){
        // alert('接口返回内容：'+JSON.stringify(res)); // 移除调试弹窗
        var data = res.data || res;
        var total = res.total || data.length;
        var page = res.page || 1;
        var pageSize = res.pageSize || 10;
        if(data.length > 0){
            var list = renderTeacherDropdown(data, page, total, pageSize);
            input.after(list);
        }
    }).fail(function(xhr, status, error){
        alert('接口请求失败：'+error+'\n'+xhr.responseText);
    });
});
// 分页点击
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
// 选择老师
$(document).on('click', '.autocomplete-list .list-group-item', function(){
    var t = $(this).data('teacher');
    if (!t) return;
    var input = $(this).closest('.mb-3').find('input[name="teacher"]');
    input.val(t.name);
    input.siblings('input[name="teacher_id"]').val(t.id);
    $(this).parent().remove();
});
$(document).on('blur', 'input[name="teacher"]', function(){
    var input = $(this);
    setTimeout(function(){ input.siblings('.autocomplete-list').remove(); }, 200);
});
// 在负责老师输入框后面加一个隐藏域
$(function(){
  if ($('input[name="teacher_id"]').length === 0) {
    $('input[name="teacher"]').after('<input type="hidden" name="teacher_id">');
  }
});
// 清空时也清空teacher_id
$('input[name="teacher"]').on('input', function(){
    if (!$(this).val()) $(this).siblings('input[name="teacher_id"]').val('');
});
function setDarkMode(on) {
    if(on) {
        document.body.classList.add('dark-mode');
        localStorage.setItem('henyii_dark', '1');
    } else {
        document.body.classList.remove('dark-mode');
        localStorage.setItem('henyii_dark', '0');
    }
}
document.getElementById('nightToggleBtn').onclick = function() {
    setDarkMode(!document.body.classList.contains('dark-mode'));
};
if(localStorage.getItem('henyii_dark')==='1') setDarkMode(true);
</script>
<style>
body {
    background: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%);
    min-height: 100vh;
    font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
    color: #232946;
    transition: background 0.5s, color 0.5s;
}
.card {
    border: none;
    border-radius: 22px;
    box-shadow: 0 4px 24px rgba(160,132,238,0.10), 0 1.5px 8px #7f7fd544;
    background: #fff;
    transition: box-shadow 0.32s, background 0.5s;
}
.card:hover {
    box-shadow: 0 8px 32px 0 #a1c4fd99, 0 24px 64px 0 #7f7fd544, 0 0 24px 4px #a1c4fd, 0 0 0 2.5px #a1c4fd88 inset, 0 1.5px 1.5px 0 #fff8 inset;
    transform: translateY(-8px) scale(1.025);
}
.form-control, .form-select {
    border-radius: 20px;
    box-shadow: 0 1.5px 8px #7f7fd522 inset;
    border: 1.5px solid #e3eaf2;
    background: rgba(255,255,255,0.95);
    transition: box-shadow 0.22s, background 0.5s;
}
.form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 2px #7f7fd5cc, 0 1.5px 8px #7f7fd522 inset;
    background: #f3f6fa;
}
.btn {
    border-radius: 22px;
    font-weight: 600;
    box-shadow: 0 2px 12px #7f7fd522, 0 0 0 2px #a1c4fd22 inset;
    transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
}
.btn-primary, .btn-outline-primary {
    background: linear-gradient(90deg, #7f7fd5 0%, #86a8e7 100%);
    color: #fff;
    border: none;
}
.btn-primary:hover, .btn-outline-primary:hover {
    background: linear-gradient(90deg, #86a8e7 0%, #7f7fd5 100%);
    color: #fff;
    box-shadow: 0 4px 24px #7f7fd544;
    transform: translateY(-2px) scale(1.04);
}
.btn-outline-secondary {
    background: #fff;
    color: #1976d2;
    border: 1.5px solid #b0c4de;
}
.btn-outline-secondary:hover {
    background: #e3eaf2;
    color: #1976d2;
}
.btn-outline-success {
    background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
    color: #fff;
    border: none;
}
.btn-outline-success:hover {
    background: linear-gradient(90deg, #38f9d7 0%, #43e97b 100%);
    color: #fff;
}
.btn-danger {
    background: linear-gradient(90deg, #ff4d4f 0%, #ff6a6a 100%);
    color: #fff;
    border: none;
}
.btn-danger:hover {
    background: linear-gradient(90deg, #ff6a6a 0%, #ff4d4f 100%);
    color: #fff;
}
/* 表格美化 */
.table {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(127,127,213,0.10), 0 1.5px 8px #7f7fd544;
    background: rgba(255,255,255,0.92);
    margin-bottom: 1.5rem;
}
.table th {
    background: linear-gradient(90deg, #7f7fd5 0%, #86a8e7 100%);
    color: #fff;
    font-weight: 700;
    font-size: 1.08rem;
    border: none;
    border-radius: 0 !important;
    box-shadow: 0 2px 12px #7f7fd544, 0 0 0 1.5px #a1c4fd44 inset;
    text-shadow: 0 2px 8px #7f7fd544, 0 1px 2px #fff8;
    letter-spacing: 1px;
}
.table thead th:first-child {
    border-top-left-radius: 18px !important;
}
.table thead th:last-child {
    border-top-right-radius: 18px !important;
}
.table td, .table th {
    vertical-align: middle;
    text-align: center;
    padding: 13px 8px;
    border: none;
}
.table-hover tbody tr {
    transition: background 0.22s, box-shadow 0.18s;
}
.table-hover tbody tr:hover {
    background: linear-gradient(90deg, #e3eaf2 0%, #f3f0ff 100%);
    box-shadow: 0 2px 12px #7f7fd544;
}
/* 玻璃拟态输入框/下拉框 */
.table-input, .table-select, .participant-name, .participant-chinese-name, .participant-id, .participant-class, .participant-race, .participant-religion, .participant-achievement {
    background: rgba(40,50,80,0.13);
    border: 1.5px solid #b0c4de;
    box-shadow: 0 2px 12px #7f7fd522, 0 0 0 2px #a1c4fd22 inset, 0 1.5px 8px #fff4 inset;
    color: #232946;
    border-radius: 22px !important;
    font-size: 1.08rem;
    font-weight: 500;
    padding: 8px 18px;
    transition: box-shadow 0.22s, background 0.4s, color 0.4s;
    backdrop-filter: blur(6px);
}
.table-input:focus, .table-select:focus, .participant-name:focus, .participant-chinese-name:focus, .participant-id:focus, .participant-class:focus, .participant-race:focus, .participant-religion:focus, .participant-achievement:focus {
    background: rgba(127,127,213,0.18);
    border: 1.5px solid #7f7fd5;
    box-shadow: 0 0 0 2.5px #7f7fd5cc, 0 2px 12px #7f7fd544, 0 1.5px 8px #fff4 inset;
    color: #232946;
}
.table-input::placeholder, .participant-name::placeholder, .participant-chinese-name::placeholder, .participant-id::placeholder, .participant-class::placeholder, .participant-race::placeholder, .participant-religion::placeholder, .participant-achievement::placeholder {
    color: #b0b8d0;
    opacity: 1;
    font-weight: 400;
}
.table-select {
    background: rgba(40,50,80,0.13);
    color: #232946;
    border-radius: 22px !important;
    border: 1.5px solid #b0c4de;
    box-shadow: 0 2px 12px #7f7fd522, 0 0 0 2px #a1c4fd22 inset, 0 1.5px 8px #fff4 inset;
    font-size: 1.08rem;
    font-weight: 500;
    padding: 8px 18px;
    transition: box-shadow 0.22s, background 0.4s, color 0.4s;
    backdrop-filter: blur(6px);
}
.table-select:focus {
    background: rgba(127,127,213,0.18);
    border: 1.5px solid #7f7fd5;
    box-shadow: 0 0 0 2.5px #7f7fd5cc, 0 2px 12px #7f7fd544, 0 1.5px 8px #fff4 inset;
    color: #232946;
}
/* 夜间模式适配 */
body.dark-mode .table {
    background: rgba(35,41,70,0.98) !important;
    color: #f3f6fa !important;
    box-shadow: 0 2px 12px #7f7fd544, 0 0 0 1.5px #7f7fd5 inset;
    border-radius: 20px;
}
body.dark-mode .table th {
    background: linear-gradient(90deg, #232946 0%, #7f7fd5 100%);
    color: #fff;
    text-shadow: 0 2px 8px #7f7fd5, 0 1px 2px #fff2;
    box-shadow: 0 2px 12px #7f7fd544, 0 0 0 1.5px #7f7fd5 inset;
}
body.dark-mode .table-hover tbody tr:hover {
    background: linear-gradient(90deg, #232946 0%, #393a5a 100%) !important;
    box-shadow: 0 2px 12px #7f7fd544;
}
body.dark-mode .table-input, body.dark-mode .table-select, body.dark-mode .participant-name, body.dark-mode .participant-chinese-name, body.dark-mode .participant-id, body.dark-mode .participant-class, body.dark-mode .participant-race, body.dark-mode .participant-religion, body.dark-mode .participant-achievement {
    background: rgba(60,70,110,0.38) !important;
    color: #f3f6fa !important;
    border: 1.5px solid #7f7fd544;
    box-shadow: 0 2px 12px #7f7fd544, 0 0 0 2px #7f7fd5 inset, 0 1.5px 8px #fff2 inset;
}
body.dark-mode .table-input:focus, body.dark-mode .table-select:focus, body.dark-mode .participant-name:focus, body.dark-mode .participant-chinese-name:focus, body.dark-mode .participant-id:focus, body.dark-mode .participant-class:focus, body.dark-mode .participant-race:focus, body.dark-mode .participant-religion:focus, body.dark-mode .participant-achievement:focus {
    background: rgba(127,127,213,0.22) !important;
    border: 1.5px solid #a1c4fd;
    box-shadow: 0 0 0 2.5px #a1c4fdcc, 0 2px 12px #7f7fd544, 0 1.5px 8px #fff2 inset;
    color: #fff !important;
}
body.dark-mode .table-input::placeholder, body.dark-mode .participant-name::placeholder, body.dark-mode .participant-chinese-name::placeholder, body.dark-mode .participant-id::placeholder, body.dark-mode .participant-class::placeholder, body.dark-mode .participant-race::placeholder, body.dark-mode .participant-religion::placeholder, body.dark-mode .participant-achievement::placeholder {
    color: #b0b8d0;
    opacity: 1;
    font-weight: 400;
}
.night-toggle {
    border: none;
    background: linear-gradient(90deg, #7f7fd5 0%, #86a8e7 100%);
    color: #fff;
    border-radius: 22px;
    font-weight: 700;
    font-size: 1.08rem;
    padding: 8px 22px;
    box-shadow: 0 2px 8px rgba(120,120,255,0.10);
    transition: filter 0.2s, transform 0.2s, background 0.5s;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}
.night-toggle:hover {
    filter: brightness(1.10);
    transform: scale(1.04);
}
body.dark-mode {
    background: linear-gradient(120deg, #232946 0%, #1a1a2e 100%);
    color: #f3f6fa;
}
body.dark-mode .card {
    background: rgba(35,41,70,0.92) !important;
    color: #f3f6fa !important;
    box-shadow:
        0 2.5px 12px 0 #7f7fd544,
        0 8px 32px 0 #7f7fd588,
        0 0 8px 2px #7f7fd5,
        0 0 0 1.5px #7f7fd544 inset,
        0 0.5px 0.5px 0 #fff2 inset;
    border-radius: 22px;
    border: 1.5px solid #7f7fd544;
    background-image: linear-gradient(180deg,rgba(120,120,255,0.10) 0%,transparent 60%);
}
body.dark-mode .card:hover {
    box-shadow:
        0 8px 32px 0 #a1c4fdcc,
        0 24px 64px 0 #23294699,
        0 0 32px 8px #7f7fd5,
        0 0 0 2.5px #a1c4fd inset,
        0 1.5px 1.5px 0 #fff2 inset;
}
body.dark-mode .form-control, body.dark-mode .form-select {
    background: rgba(35,41,70,0.92) !important;
    color: #f3f6fa !important;
    border: 1.5px solid #7f7fd544;
    box-shadow: 0 1.5px 8px #7f7fd544 inset;
}
body.dark-mode .form-control:focus, body.dark-mode .form-select:focus {
    background: #232946 !important;
    color: #fff !important;
    box-shadow: 0 0 0 2px #7f7fd5cc, 0 1.5px 8px #7f7fd544 inset;
}
body.dark-mode .btn, body.dark-mode .btn-feature {
    color: #fff !important;
    box-shadow: 0 2px 12px #7f7fd544, 0 0 0 2px #a1c4fd55 inset;
    border-radius: 22px;
}
body.dark-mode .btn-primary, body.dark-mode .btn-outline-primary {
    background: linear-gradient(90deg, #232946 0%, #7f7fd5 100%);
    box-shadow:
        0 2px 12px #7f7fd5cc,
        0 0 0 2px #7f7fd5 inset,
        0 0.5px 0.5px #fff2 inset;
}
body.dark-mode .btn-primary:hover, body.dark-mode .btn-outline-primary:hover {
    background: linear-gradient(90deg, #393a5a 0%, #a1c4fd 100%);
    box-shadow:
        0 6px 24px #7f7fd5cc,
        0 0 0 4px #a1c4fd inset,
        0 2px 8px #fff2 inset;
}
body.dark-mode .btn-outline-secondary {
    background: #232946;
    color: #7f7fd5;
    border: 1.5px solid #7f7fd544;
}
body.dark-mode .btn-outline-secondary:hover {
    background: #393a5a;
    color: #7f7fd5;
}
body.dark-mode .btn-outline-success {
    background: linear-gradient(90deg, #232946 0%, #43e97b 100%);
    color: #fff;
}
body.dark-mode .btn-outline-success:hover {
    background: linear-gradient(90deg, #393a5a 0%, #43e97b 100%);
    color: #fff;
}
body.dark-mode .btn-danger {
    background: linear-gradient(90deg, #232946 0%, #ff4d4f 100%);
    color: #fff;
}
body.dark-mode .btn-danger:hover {
    background: linear-gradient(90deg, #393a5a 0%, #ff4d4f 100%);
    color: #fff;
}
body.dark-mode .table {
    background: #232946 !important;
    color: #f3f6fa !important;
    box-shadow: 0 2px 12px #7f7fd544, 0 0 0 1.5px #7f7fd5 inset;
    border-radius: 16px;
}
body.dark-mode th, body.dark-mode td {
    color: #f3f6fa !important;
}
body.dark-mode .table-hover tbody tr:hover {
    background: linear-gradient(90deg, #232946 0%, #393a5a 100%) !important;
    box-shadow: 0 2px 12px #7f7fd544;
}
.page-container {
    padding: 30px 0;
    min-height: 100vh;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}
.content-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    margin-bottom: 30px;
}
.page-header {
    text-align: center;
    margin-bottom: 40px;
}
.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1rem;
    background: linear-gradient(45deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.page-subtitle {
    font-size: 1.1rem;
    color: #6c757d;
    font-weight: 400;
}
.form-group {
    margin-bottom: 25px;
}
.form-label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 1rem;
}
.form-control, .form-select, .form-textarea {
    border-radius: 12px;
    border: 1.5px solid #e3f0fc;
    padding: 14px 16px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.9);
    width: 100%;
    box-sizing: border-box;
}
.form-control:focus, .form-select:focus, .form-textarea:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    background: white;
    outline: none;
}
.form-textarea {
    min-height: 120px;
    resize: vertical;
}
.btn-submit {
    background: linear-gradient(45deg, #667eea, #764ba2);
    border: none;
    color: white;
    padding: 15px 40px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    display: inline-flex;
    align-items: center;
    gap: 10px;
}
.btn-submit:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    color: white;
}
.btn-cancel {
    background: linear-gradient(45deg, #95a5a6, #7f8c8d);
    border: none;
    color: white;
    padding: 15px 30px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(149, 165, 166, 0.3);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-cancel:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(149, 165, 166, 0.4);
    color: white;
    text-decoration: none;
}
.form-actions {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 40px;
    flex-wrap: wrap;
}
.file-upload-area {
    border: 2px dashed #e3f0fc;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.5);
    cursor: pointer;
}
.file-upload-area:hover {
    border-color: #667eea;
    background: rgba(102, 126, 234, 0.05);
}
.file-upload-icon {
    font-size: 3rem;
    color: #667eea;
    margin-bottom: 15px;
}
.file-upload-text {
    color: #6c757d;
    font-size: 1rem;
    margin-bottom: 10px;
}
.file-upload-hint {
    color: #95a5a6;
    font-size: 0.9rem;
}
.navbar {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 2px 20px rgba(31, 38, 135, 0.1);
}
.navbar-brand {
    font-weight: 700;
    font-size: 1.5rem;
    background: linear-gradient(45deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.nav-link {
    font-weight: 500;
    color: #2c3e50;
    transition: all 0.3s ease;
    border-radius: 8px;
    padding: 8px 16px;
}
.nav-link:hover {
    color: #667eea;
    background: rgba(102, 126, 234, 0.1);
}
.night-mode-toggle {
    background: rgba(102, 126, 234, 0.1);
    border: 1px solid rgba(102, 126, 234, 0.2);
    color: #667eea;
    padding: 8px 12px;
    border-radius: 20px;
    transition: all 0.3s ease;
}
.night-mode-toggle:hover {
    background: rgba(102, 126, 234, 0.2);
    transform: scale(1.05);
}
body.dark-mode {
    background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
    color: #e3f2fd;
}
body.dark-mode .page-container {
    background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
}
body.dark-mode .content-card {
    background: rgba(26, 26, 46, 0.9);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #e3f2fd;
}
body.dark-mode .page-title {
    color: #ffffff;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}
body.dark-mode .page-subtitle {
    color: #e3f2fd;
    font-weight: 400;
}
body.dark-mode .form-label {
    color: #ffffff;
    font-weight: 600;
}
body.dark-mode .form-control,
body.dark-mode .form-select,
body.dark-mode .form-textarea {
    background: rgba(26, 26, 46, 0.8);
    border-color: rgba(255, 255, 255, 0.2);
    color: #ffffff;
    font-weight: 500;
}
body.dark-mode .form-control:focus,
body.dark-mode .form-select:focus,
body.dark-mode .form-textarea:focus {
    background: rgba(26, 26, 46, 0.95);
    border-color: #667eea;
    color: #ffffff;
}
body.dark-mode .file-upload-area {
    border-color: rgba(255, 255, 255, 0.2);
    background: rgba(26, 26, 46, 0.5);
}
body.dark-mode .file-upload-area:hover {
    border-color: #667eea;
    background: rgba(102, 126, 234, 0.1);
}
body.dark-mode .file-upload-text {
    color: #e3f2fd;
    font-weight: 500;
}
body.dark-mode .file-upload-hint {
    color: #b0bec5;
    font-weight: 400;
}
body.dark-mode .navbar {
    background: rgba(26, 26, 46, 0.95);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
body.dark-mode .nav-link {
    color: #ffffff;
    font-weight: 500;
}
body.dark-mode .nav-link:hover {
    color: #667eea;
    background: rgba(102, 126, 234, 0.1);
}
body.dark-mode .navbar-brand {
    color: #ffffff;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}
</style> 