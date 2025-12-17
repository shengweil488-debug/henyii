<?php
require_once __DIR__ . '/../models/Participant.php';
class ParticipantController {
    public static function list($activity_id) {
        return Participant::all($activity_id);
    }
    public static function add($activity_id, $user_id) {
        return Participant::add($activity_id, $user_id);
    }
    public static function remove($id) {
        return Participant::remove($id);
    }
} 