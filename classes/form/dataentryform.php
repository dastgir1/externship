<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace mod_dataentry\form;

/**
 * Class dataentryform
 *
 * @package    mod_dataentry
 * @copyright  2024 dastgirmoodledeveloper@gmail.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dataentryform  extends \moodleform
{
    public function definition()
    {
        $mform = $this->_form;
        global $CFG, $DB, $dataentry_data, $dataentryid;
        // Header
        $mform->addElement('header', 'dataentryform', get_string('dataentryform', 'dataentry'));

        $dataid = $this->_customdata['dataid'];
        $dataentryid = $this->_customdata['dataentryid'];

        $mform->addElement('hidden', 'dataid', $dataid);
        $mform->setType('dataid', PARAM_INT);
        $mform->addElement('hidden', 'dataentryid', $dataentryid);
        $mform->setType('dataentryid', PARAM_INT);
        $now = getdate();
        $curryear = (int) $now['year'];
        for ($i = 1; $i <= 31; $days["$i"] = $i++);
        for ($i = 1; $i <= 12; $months["$i"] = $i++);
        for ($i = $curryear - 5; $i <= $curryear + 5; $years["$i"] = $i++);
        for ($i = 0; $i <= 23; $hours["$i"] = $i++);
        for ($i = 0; $i < 60; $i += 5) $minutes["$i"] = sprintf("%02d", $i);

        // $cmids["0"] = get_string('selectanactivity', 'dataentry');
        // foreach ($modinfo->cms as $cm) $cmids["$cm->id"] = $cm->name;

        if ($dataid) {
            global $dataentry_data;
            $starttime_obj = getdate($dataentry_data->starttime);
            $endtime_obj = getdate($dataentry_data->endtime);


            $default_day   =  $starttime_obj['mday'];
            $default_month =  $starttime_obj['mon'];
            $default_year  =  $starttime_obj['year'];
            $default_starthour        = $starttime_obj['hours'];
            $default_startminute      = $starttime_obj['minutes'];
            $default_endhour        = $endtime_obj['hours'];
            $default_endminute      = $endtime_obj['minutes'];
            $default_durationhour     = intval(intval($dataentry_data->duration) / 3600);
            $default_durationminute   = intval((intval($dataentry_data->duration) - $default_durationhour * 3600) / 60);
            $default_description      = $dataentry_data->description;
            if ($dataentry_data->cmid) $default_cmid = $dataentry_data->cmid;
            else $default_cmid = '0';
        } else {
            $default_day   =  $now['mday'];
            $default_month =  $now['mon'];
            $default_year  =  $curryear;
            $default_starthour      = '0';
            $default_startminute    = '00';
            $default_endhour      = '0';
            $default_endminute    = '00';
            $default_durationhour   = '0';
            $default_durationminute = '00';
            $default_description    = '';
            $default_cmid = '0';
        }

        //-------------------------------------------------------------------------------
        $mform->addElement('text', 'clinicname', get_string('clinicname', 'dataentry'));
        $mform->setType('clinicname', PARAM_TEXT);

        $mform->addElement('text', 'preceptorname', get_string('preceptorname', 'dataentry'));
        $mform->setType('preceptorname', PARAM_TEXT);


        $stimearray = array();
        $stimearray[] = &$mform->createElement('select', 'day', '', $days);
        $mform->setDefault('day', $default_day);
        $mform->setType('day', PARAM_INT);
        $stimearray[] = &$mform->createElement('select', 'month', '', $months);
        $mform->setType('month', PARAM_INT);
        $mform->setDefault('month', $default_month);
        $stimearray[] = &$mform->createElement('select', 'year', '', $years);
        $mform->setType('year', PARAM_INT);
        $mform->setDefault('year', $default_year);
        $mform->addGroup($stimearray, 'timearr', get_string('date', 'dataentry'), ' ', false);

        // Start time group
        $stimearray = array();
        $stimearray[] = $mform->createElement('select', 'starthour', '', $hours);
        $mform->setDefault('starthour', $default_starthour);
        $mform->setType('starthour', PARAM_INT);

        $stimearray[] = $mform->createElement('select', 'startminute', '', $minutes);
        $mform->setDefault('startminute', $default_startminute);
        $mform->setType('startminute', PARAM_INT);

        $mform->addGroup($stimearray, 'starttimearr', get_string('starttime', 'dataentry'), ' ', false);
        // $mform->addRule('starttime', get_string('required', 'dataentry'), 'required', null, 'client');

        // End time group
        $etimearray = array();
        $etimearray[] = $mform->createElement('select', 'endhour', '', $hours);
        $mform->setDefault('endhour', $default_endhour);
        $mform->setType('endhour', PARAM_INT);

        $etimearray[] = $mform->createElement('select', 'endminute', '', $minutes);
        $mform->setDefault('endminute', $default_endminute);
        $mform->setType('endminute', PARAM_INT);

        $mform->addGroup($etimearray, 'endtimearr', get_string('endtime', 'dataentry'), ' ', false);
        $html = '<p id="custom-div-id"></p>';
        $mform->addElement('html', $html);



        $mform->addElement('text', 'duration', get_string('duration', 'dataentry'));
        $mform->setType('duration', PARAM_RAW);

        $mform->addElement('textarea', 'description', get_string('description', 'dataentry'));
        $mform->setType('description', PARAM_RAW);

        $mform->addElement(
            'filemanager',
            'file',
            get_string('file', 'dataentry'),
            null,
            [
                'subdirs' => 0,
                'maxbytes' => 1048576,
                'areamaxbytes' => 1048576,
                'maxfiles' => 1,
                'accepted_types' => ['.doc', '.docx', '.pdf', '.jpg', '.png'],
                // 'return_types' => FILE_INTERNAL | FILE_EXTERNAL,
            ]
        );
        $mform->addRule('file', get_string('required', 'dataentry'), 'required', null, 'client');

        $this->add_action_buttons();
    }

    // public function validation($data, $files)
    // {
    //     $errors = parent::validation($data, $files);

    //     // Check that start time and end time are present in the form data
    //     if (isset($data['starthour']) && isset($data['startminute']) && isset($data['endhour']) && isset($data['endminute'])) {
    //         // Retrieve start and end time data
    //         $starthour = $data['starthour'];
    //         $startminute = $data['startminute'];
    //         $endhour = $data['endhour'];
    //         $endminute = $data['endminute'];

    //         // Convert start and end times to minutes for easier comparison
    //         $start_time_in_minutes = ($starthour * 60) + $startminute;
    //         $end_time_in_minutes = ($endhour * 60) + $endminute;

    //         // Validate that end time is greater than start time
    //         if ($end_time_in_minutes <= $start_time_in_minutes) {
    //             $errors['endtimearr'] = get_string('error_endtime_greater', 'dataentry'); // Custom error message
    //         } else {
    //             $errors['endtimearr'] = '';
    //         }
    //     } else {
    //         // Handle missing time fields if necessary
    //         $errors['endtimearr'] = get_string('error_missing_time_fields', 'dataentry');
    //     }

    //     return $errors;
    // }
}
