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

namespace mod_dataentry\event;

/**
 * Event edit_data
 *
 * @package    mod_dataentry
 * @copyright  2024 ghulam.dastgir@paktaleem.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

class edit_data extends \core\event\base
{

    protected function init()
    {
        $this->data['crud'] = 'u'; // 'u' for update
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'dataentry';
    }

    public static function get_name()
    {
        return get_string('eventeditdata', 'dataentry');
    }

    public function get_description()
    {
        return "The user with id '{$this->userid}' updated the dataentry data with id '{$this->objectid}' in the course with id '{$this->courseid}'.";
    }

    public function get_url()
    {
        return new \moodle_url('/mod/dataentry/dataentryform.php', array('dataid' => $this->objectid, 'dataentry_data' => $this->get_data()['other']['dataentryid']));
    }
}
