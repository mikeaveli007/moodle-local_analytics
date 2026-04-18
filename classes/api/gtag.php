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

/**
 * gtag analytics class.
 * @copyright  Bas Brands, Sonsbeekmedia 2017
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gtag extends analytics {
    /**
     * Insert the actual tracking code.
     *
     * @return void As the insertion is done through the {js} template API.
     */
    public static function insert_tracking() {
        global $CFG, $PAGE, $OUTPUT, $USER;

        $template = new stdClass();

        $template->gtagtrackingid = get_config('local_analytics', 'gtagtrackingid');
        $cleanurl = get_config('local_analytics', 'cleanurl');
        $trackuser = get_config('local_analytics', 'trackuser');
        $anonymizeip = get_config('local_analytics', 'anonymizeip');

        $addition = [];

        if ($cleanurl) {
            $addition[] = "'page_path': '" . self::trackurl(true, true) . "'";
            $addition[] = "'page_title': '" . addslashes(format_string($PAGE->heading)) . "'";
        }

        if ($USER->id > 0) {
            $sa = get_user_service_area($USER->id);
            if ($sa) {
                $addition[] = "'SA': '" . $sa . "'";
                $addition[] = "'content_group': '" . $sa . "'";
            }
            if ($trackuser) {
                $addition[] = "'user_id': '" . $USER->id . "'";
            }
        }

        if ($anonymizeip) {
            $addition[] = "'anonymize_ip': true";
        }

        if (!empty($addition)) {
            $template->addition = implode(', ', $addition);
        }

        if (self::should_track() && !empty($template->gtagtrackingid)) {
            $CFG->additionalhtmlhead .= $OUTPUT->render_from_template('local_analytics/gtag', $template);
        }
    }
}
