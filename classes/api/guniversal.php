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

/**
 * Analytics
 *
 * This module provides extensive analytics on a platform of choice
 * Currently support Google Analytics and Piwik
 *
 * @package    local_analytics
 * @copyright  Bas Brands, Sonsbeekmedia 2017
 * @author     Bas Brands <bas@sonsbeekmedia.nl>, David Bezemer <info@davidbezemer.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_analytics\api;

use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Guniversal analytics class.
 * @copyright  Bas Brands, Sonsbeekmedia 2017
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class guniversal extends analytics {
    /**
     * Insert the actual tracking code.
     *
     * @return void As the insertion is done through the {js} template API.
     */
    public static function insert_tracking() {
        global $PAGE, $OUTPUT, $USER;

        $template = new stdClass();

        $template->analyticsid = get_config('local_analytics', 'analyticsid');
        $template->analyticsgtmid = get_config('local_analytics', 'analyticsgtmid');
        $userid = $USER->id;

        if(substr($template->analyticsid, 0, 1) == "G") {
            $template->usega4 = true;
            $template->useua = false;
        }
        else if (substr($template->analyticsid, 0, 1) == "U") {
            $template->useua = true;
            $template->usega4 = false;
        }
        else {
            $template->useua = false;
            $template->usega4 = false;
        }

        if($USER->id > 0) {
            $template->userid = $userid;
            $template->trackuser = get_config('local_analytics', 'trackuser');
            $sa = get_user_service_area($USER->id);
            $template->usersa = $sa;
        }

        if (get_config('local_analytics', 'anonymizeip')) {
            $template->anonymizeip = true;
            $anonymizeip = "true";
        } else {
            $anonymizeip = "false";
        }
        
        $cleanurl = get_config('local_analytics', 'cleanurl');

        if ($cleanurl) {
            $trackurl = self::trackurl(true, true);
            if($template->usega4) {
                $template->ga4options = "{
                    ".($trackurl ? "'page_path' : '".$trackurl."'," : '')."
                    'page_title' : '".addslashes(format_string($PAGE->heading))."',
                    'SA' : '".$sa."',
                    'content_group' : '".$sa."',
                    ".($template->trackuser ? "'user_id' : '".$template->userid."'," : '')."
                    'anonymize_ip': ".$anonymizeip."
                    }";
            }
            else if ($template->useua) {
                if($trackurl) {
                    $template->addition = "{'hitType' : 'pageview',
                        'page' : '".self::trackurl(true, true)."',
                        'title' : '".addslashes(format_string($PAGE->heading))."'
                        }";
                } else {
                    $template->addition = "{'hitType' : 'pageview',
                        'title' : '".addslashes(format_string($PAGE->heading))."'
                        }";
                }
            }
        } else {
            $template->addition = "'pageview'";
        }
        if (self::should_track() && !empty($template->analyticsid)) {
            // The templates only contains a "{js}" block; so we don't care about
            // the output; only that the $PAGE->requires are filled.
            $OUTPUT->render_from_template('local_analytics/guniversal', $template);
        }
    }
}
