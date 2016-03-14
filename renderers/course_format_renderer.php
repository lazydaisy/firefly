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
 * Theme Firefly coure format renderer file.
 *
 * @package    theme_firefly
 * @copyright  2014 Mary Evans (bylazydaisy.co.uk)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

include_once ($CFG->dirroot. '/course/format/renderer.php');

abstract class theme_firefly_format_section_renderer extends format_section_renderer_base {


    /**
     * Generate the edit controls of a section
     *
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return array of links with edit controls
     */
    protected function section_edit_controls($course, $section, $onsectionpage = false) {
        global $PAGE;

        if (!$PAGE->user_is_editing()) {
            return array();
        }

        $coursecontext = context_course::instance($course->id);

        if ($onsectionpage) {
            $baseurl = course_get_url($course, $section->section);
        } else {
            $baseurl = course_get_url($course);
        }
        $baseurl->param('sesskey', sesskey());

        $controls = array();
        $url = clone($baseurl);
        if (has_capability('moodle/course:sectionvisibility', $coursecontext)) {
            if ($section->visible) { // Show the hide/show eye.
                $strhidefromothers = get_string('hidefromothers', 'format_'.$course->format);
                $url->param('hide', $section->section);
                $controls[] = html_writer::link($url, html_writer::tag('i', '', array('class' => 'fa fa-eye editing_showhide', 'title' => $strhidefromothers)));
            } else {
                $strshowfromothers = get_string('showfromothers', 'format_'.$course->format);
                $url->param('show',  $section->section);
                $controls[] = html_writer::link($url, html_writer::tag('i', '', array('class' => 'fa fa-eye-slash editing_showhide', 'title' => $strshowfromothers)));
            }
            return $controls;
        }
    }
}