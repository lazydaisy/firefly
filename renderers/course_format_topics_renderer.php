<?php

defined('MOODLE_INTERNAL') || die();

include_once($CFG->dirroot.'/course/format/topics/renderer.php');

class theme_firefly_format_topics_renderer extends format_topics_renderer {

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
                $controls[] = html_writer::link($url,
                    html_writer::tag('i', '', array('class' => 'fa fa-eye')),
                    array('class' => 'editing_showhide', 'title' => $strhidefromothers));
            } else {
                $strshowfromothers = get_string('showfromothers', 'format_'.$course->format);
                $url->param('show',  $section->section);
                $controls[] = html_writer::link($url,
                    html_writer::tag('i', '', array('class' => 'fa fa-eye-slash')),
                    array('class' => 'editing_showhide', 'title' => $strshowfromothers));
            }
        }

        if (!$onsectionpage && has_capability('moodle/course:movesections', $coursecontext)) {
            $url = clone($baseurl);
            if ($section->section > 1) { // Add a arrow to move section up.
                $url->param('section', $section->section);
                $url->param('move', -1);
                $strmoveup = get_string('moveup');

                $controls[] = html_writer::link($url,
                    html_writer::tag('i', '', array('class' => 'fa fa-arrow-up')),
                    array('class' => 'moveup', 'title' => $strmoveup));
            }

            $url = clone($baseurl);
            if ($section->section < $course->numsections) { // Add a arrow to move section down.
                $url->param('section', $section->section);
                $url->param('move', 1);
                $strmovedown = get_string('movedown');

                $controls[] = html_writer::link($url,
                    html_writer::tag('i', '', array('class' => 'fa fa-arrow-down')),
                    array('class' => 'movedown', 'title' => $strmovedown));
            }
        }

        if (has_capability('moodle/course:setcurrentsection', $coursecontext)) {
            if ($course->marker == $section->section) {  // Show the "light globe" on/off.
                $url->param('marker', 0);
                $controls[] = html_writer::link($url,
                    html_writer::tag('i', '', array('class' => 'fa fa-moon')),
                    array('title' => get_string('markedthistopic'), 'class' => 'editing_highlight'));
            } else {
                $url->param('marker', $section->section);
                $controls[] = html_writer::link($url,
                    html_writer::tag('i', '', array('class' => 'fa fa-sun-o')),
                    array('title' => get_string('markthistopic'), 'class' => 'editing_highlight'));
            }
        }

        return $controls;;
    }
}