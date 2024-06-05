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

defined('MOODLE_INTERNAL') || die();

use core\session\manager;

/**
 * Abstract local analytics class.
 * @copyright  Bas Brands, Sonsbeekmedia 2017
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class analytics {
    /**
     * Encode a substring if required.
     *
     * @param string  $input  The string that might be encoded.
     * @param boolean $encode Whether to encode the URL.
     * @return string
     */
    private static function might_encode($input, $encode) {
        if (!$encode) {
            return str_replace("'", "\'", $input);
        }

        return urlencode($input);
    }

    /**
     * Get the Tracking URL for the request.
     *
     * @param bool|int $urlencode    Whether to encode URLs.
     * @param bool|int $leadingslash Whether to add a leading slash to the URL.
     * @return string A URL to use for tracking.
     */
    public static function trackurl($urlencode = false, $leadingslash = false) {
        global $DB, $PAGE, $_REQUEST, $USER;
        $pageinfo = get_context_info_array($PAGE->context->id);

        if($pageinfo[1] == null) {
            return false;
        }

        $trackurl = "";

        if ($leadingslash) {
            $trackurl .= "/";
        }

        // Adds course category name.
        if (isset($pageinfo[1]->category)) {
            if ($category = $DB->get_record('course_categories', ['id' => $pageinfo[1]->category])
            ) {
                $cats = explode("/", $category->path);
                foreach (array_filter($cats) as $cat) {
                    if ($categorydepth = $DB->get_record("course_categories", ["id" => $cat])) {
                        $trackurl .= self::might_encode($categorydepth->name, $urlencode).'/';
                    }
                }
            }
        }

        // Adds course full name.
        if (isset($pageinfo[1]->fullname)) {
            if (isset($pageinfo[2]->name)) {
                $trackurl .= self::might_encode($pageinfo[1]->fullname, $urlencode).'/';
            } else {
                $trackurl .= self::might_encode($pageinfo[1]->fullname, $urlencode);
                $trackurl .= '/';
                if ($PAGE->user_is_editing()) {
                    $trackurl .= get_string('edit', 'local_analytics');
                } else {
                    $trackurl .= get_string('view', 'local_analytics');
                }
            }
        }

        // Adds activity name.
        if (isset($pageinfo[2]->name)) {
            $trackurl .= self::might_encode($pageinfo[2]->modname, $urlencode);
            $trackurl .= '/';
            $trackurl .= self::might_encode($pageinfo[2]->name, $urlencode);
        }

        // If in the library add the entry category or name
        if(isset($_REQUEST["eid"]) || isset($_REQUEST["mode"]) || isset($_REQUEST["rid"])) {
            // If request is in a glossary activity and in category mode, add the category to the url
            if(isset($_REQUEST["mode"]) && $_REQUEST["mode"] == "cat") { 
                $category = get_entry_category($_REQUEST["hook"]);
                $trackurl .= '/category/';
                $trackurl .= self::might_encode($category->name, $urlencode);
            }
            // If request is in a glossary activity and viewing single entry, add the entry title
            if(isset($_REQUEST["eid"])) {
                $entry = get_entry($_REQUEST["eid"]);
                $trackurl .= '/entry/';
                $trackurl .= self::might_encode($entry->concept, $urlencode);
            }
            // If request is in a database activity and viewing single entry, add the entry title
            if(isset($_REQUEST["rid"])) {
                $entry = get_20_entry($_REQUEST["rid"]);
                $trackurl .= '/entry/';
                $trackurl .= self::might_encode($entry->content, $urlencode);
            }
        }

        // Add the user_id
        //if($USER->id > 0) {
        //    $trackurl .= '?u=';
        //    $trackurl .= self::might_encode($USER->id, $urlencode);
        //}

        // If searching the library add the search params
        if(isset($_REQUEST["hook"]) && isset($_REQUEST["mode"])) {
            if($_REQUEST["mode"] == "search") { 
                //$trackurl .= '/search/';
                $trackurl .= (strpos($trackurl, "?") ? '&q=' : '?q=');
                $trackurl .= self::might_encode($_REQUEST["hook"], $urlencode);
            }
        }

        return $trackurl;
    }

    /**
     * Whether to track this request.
     *
     * @return boolean
     *   The outcome of our deliberations.
     */
    public static function should_track() {
        if (!is_siteadmin()) {
            return true;
        }

        $trackadmin = get_config('local_analytics', 'trackadmin');
        return ($trackadmin == 1);
    }

    /**
     * Get the user full name to record in tracking, taking account of masquerading if necessary.
     *
     * @return string
     *   The full name to log for the user.
     */
    public static function user_full_name() {
        global $USER;
        $user = $USER;
        $ismasquerading = manager::is_loggedinas();

        if ($ismasquerading) {
            $usereal = get_config('local_analytics', 'masquerade_handling');
            if ($usereal) {
                $user = manager::get_realuser();
            }
        }

        $realname = fullname($user);
        return $realname;
    }
}
