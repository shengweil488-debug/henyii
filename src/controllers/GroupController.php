<?php
require_once __DIR__ . '/../models/Group.php';
class GroupController {
    public static function list() {
        return Group::all();
    }
    public static function detail($id) {
        return Group::find($id);
    }
    public static function create($name) {
        return Group::create($name);
    }
    public static function update($id, $name) {
        return Group::update($id, $name);
    }
    public static function delete($id) {
        return Group::delete($id);
    }
    public static function members($group_id) {
        return Group::members($group_id);
    }
    public static function addMember($group_id, $user_id) {
        return Group::addMember($group_id, $user_id);
    }
    public static function removeMember($group_id, $user_id) {
        return Group::removeMember($group_id, $user_id);
    }
} 