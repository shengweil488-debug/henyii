<?php
require_once __DIR__ . '/../models/Activity.php';
class ActivityController {
    public static function list() {
        return Activity::all();
    }
    public static function detail($id) {
        return Activity::find($id);
    }
    public static function create($title, $date, $location, $organizer_id, $description) {
        return Activity::create($title, $date, $location, $organizer_id, $description);
    }
    public static function update($id, $title, $date, $location, $description) {
        return Activity::update($id, $title, $date, $location, $description);
    }
    public static function delete($id) {
        return Activity::delete($id);
    }
    public static function searchAll($search = '') {
        return Activity::searchAll($search);
    }
    public static function approve($id) {
        return Activity::approve($id);
    }
    public static function reject($id) {
        return Activity::reject($id);
    }
} 