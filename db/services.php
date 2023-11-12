<?php
defined('MOODLE_INTERNAL') || die();
$functions = array(
    'local_h5p_results_transfer_update_results' => array(
        'classname' => 'local_h5p_results_transfer_external',
        'methodname' => 'update_h5p_results',
        'classpath' => 'local/h5p_results_transfer/externallib.php',
        'description' => 'Transfer H5P quiz results',
        'type' => 'write',
        'ajax' => true,
    )
);

$services = array(
    'Eurokey H5P Plugin' => array(
        'functions' => array(
            'local_h5p_results_transfer_update_results'
        ),
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'h5p_results_transfer'
    )
);
