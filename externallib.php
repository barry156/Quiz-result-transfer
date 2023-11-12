<?php

use core_completion\progress;


/*use mod_h5pactivity\local\attempt;
use mod_h5pactivity\local\manager;
use mod_h5pactivity\event\statement_received;
use core_xapi\local\statement;
use core_xapi\handler as handler_base;
use core\event\base as event_base;
use core_xapi\local\state;
use moodle_exception;*/


require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

defined('MOODLE_INTERNAL') || die();


class local_h5p_results_transfer_external extends external_api
{

    public static function update_h5p_results_parameters()
    {
        return new external_function_parameters(
            array(
                'statement' => new external_value(PARAM_RAW, 'xAPI Statement in JSON format')
            )
        );
    }

    public static function update_h5p_results($statement)
    {
        global $DB, $CFG;

        $data = json_decode($statement);
        $xapiobject = null;

        if (isset($data->object) && isset($data->object->id)) {
            $xapiobject = $data->object->id;
        }

        $parts = explode('?', $xapiobject, 2);
        $contextid = array_shift($parts);
        $filename = $contextid . '.h5p';

        $courseId = null;

        $files = $DB->get_records('files', ['filename' => $filename]);
        $contextIds = array();

        foreach ($files as $file) {
            if ($file->component === "mod_h5pactivity") {
                $contextIds[] = $file->contextid;
            }
        }
        $contextIdsString = implode(',', $contextIds);
        $activityContext = context::instance_by_id($contextIdsString);

        if ($activityContext) {
            $courseContext = $activityContext->get_parent_context();

            if ($courseContext && $courseContext->contextlevel == CONTEXT_COURSE) {

                $courseId = $courseContext->instanceid;
            }
        }

        if (empty($courseId)) {
            return ['message' => 'Failed to find a contextid'];
        } else {
            $response = [
                'message' => 'Success',
                'context_ids' => $contextIdsString,
                'course_id' => $courseId,

            ];
        }

        return $response;
    }

    public static function update_h5p_results_returns()
    {
        return new external_single_structure(
            array(
                'message' => new external_value(PARAM_TEXT, 'Message'),
                'context_ids' => new external_value(PARAM_RAW, 'Context IDs'),
                'course_id' => new external_value(PARAM_RAW, 'Course ID'),
            )
        );
    }
}
