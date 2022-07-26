<?php
// This file is part of the Local Analytics plugin for Moodle
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

$string['pluginname'] = 'Analytics';
$string['analytics'] = 'Analytics';
$string['analyticsdesc'] = 'Choose the type of Analytics you want to insert';
$string['analyticsid'] = 'Google Analytics id';
$string['analyticsid_desc'] = 'For example: UA-12345678-1 for Universal Analytics or G-ABC123DEF4 for GA4';
$string['analyticsgtmid'] = 'Google Tag Manager ID';
$string['analyticsgtmid_desc'] = 'For example: GTM-1234567';
$string['siteid'] = 'Site ID';
$string['siteid_desc'] = 'Enter your Piwik Site ID';
$string['siteurl'] = 'Analytics URL';
$string['siteurl_desc'] = 'Enter your Piwik Analytics URL without http(s) or a trailing slash (for both Google Analytics types leave empty)';
$string['enabled'] = 'Enabled';
$string['enabled_desc'] = 'Enable Analytics for Moodle';
$string['imagetrack'] = 'Image Tracking';
$string['cleanurl'] = 'Clean URLs';
$string['cleanurl_desc'] = 'Generate clean URL for in advanced tracking';
$string['imagetrack_desc'] = 'Enable Image Tracking for Moodle for browsers with JavaScript disabled (only for Piwik)';
$string['trackadmin'] = 'Tracking Admins';
$string['trackadmin_desc'] = 'Enable tracking of Admin users (not recommended)';
$string['view'] = 'View';
$string['edit'] = 'Edit';
$string['piwik'] = 'Piwik';
$string['piwikdesc'] = 'Use Piwik analytics, make sure you enter a site id when enabling this';
$string['guniversal'] = 'Google Universal Analytics';
$string['guniversaldesc'] = 'Google Universal Analytics, make sure you enter a google analytics id when enabling this';
$string['anonymizeip'] = 'Anonymize IPs';
$string['anonymizeip_desc'] = 'Google Universal Analytics: When present, the IP address of the sender will be anonymized.';
$string['trackuser'] = 'Track users with userId';
$string['trackuser_desc'] = 'Google Analytics: When present, will send userId to GA to track user';
$string['head'] = 'Header';
$string['topofbody'] = 'Top of body';
$string['footer'] = 'Footer';
/*
 * GDPR compliant
 */
$string['privacy:no_data_reason'] = "The local analytics plugin doesn't store any personal data.";
