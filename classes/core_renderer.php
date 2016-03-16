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
 * Theme Firefly core renderer file.
 *
 * @package    theme_cle
 * @copyright  2014 Mary Evans (bylazydaisy.co.uk)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

include_once ($CFG->dirroot. '/theme/bootstrapbase/renderers.php');

class theme_firefly_core_renderer extends theme_bootstrapbase_core_renderer {

    /**
     * The standard tags (typically performance information and validation links,
     * if we are in developer debug mode) that should be output in the footer area
     * of the page. Designed to be called in theme layout.php files.
     *
     * @return string HTML fragment.
     */
    public function standard_footer_html() {
        global $CFG, $SCRIPT;

        // This function is normally called from a layout.php file in {@link core_renderer::header()}
        // but some of the content will not be known until later, so we return a placeholder for now.
        // This will be replaced with the real content in {@link core_renderer::footer()}.
        $output = $this->unique_performance_info_token;
        if ($this->page->devicetypeinuse == 'legacy') {
            // The legacy theme is in use print the notification
            $output .= html_writer::tag('div', get_string('legacythemeinuse'), array('class'=>'legacythemeinuse'));
        }
        // Get links to switch device types (only shown for users not on a default device)
        $output .= $this->theme_switch_links();

        if (!empty($CFG->debugpageinfo)) {
            $icon = 'fa fa-cogs';
            $text = 'This page is ';
            $itag = html_writer::tag('i', '', array('class' => $icon));
            $output .= html_writer::tag('div', $itag . $text . $this->page->debug_summary(), array('class' => 'performanceinfo pageinfo well'));
        }
        if (debugging(null, DEBUG_DEVELOPER) and has_capability('moodle/site:config', context_system::instance())) {  // Only in developer mode
            // Add link to profiling report if necessary
            if (function_exists('profiling_is_running') && profiling_is_running()) {
                $txt = get_string('profiledscript', 'admin');
                $title = get_string('profiledscriptview', 'admin');
                $url = $CFG->wwwroot . '/admin/tool/profiling/index.php?script=' . urlencode($SCRIPT);
                $link = html_writer::link($url, $txt, array('title' => $title));
                $output .= html_writer::tag('div', $link, array('class' => 'profilingfooter'));
            }
            $output .= html_writer::start_tag('div', array('class' => 'purgecaches'));
            $output .= html_writer::link(new moodle_url('/'.$CFG->admin.'/purgecaches.php?confirm=1&amp;sesskey='.sesskey()), html_writer::tag('i', '', array('class' => 'fa fa-trash-o')). '&nbsp;&nbsp;'.get_string('purgecaches', 'admin'), array('class' => 'btn btn-small'));
            $output .= html_writer::end_tag('div');


        }
        if (!empty($CFG->debugvalidators)) {
            // NOTE: this is not a nice hack, $PAGE->url is not always accurate and $FULLME neither, it is not a bug if it fails. --skodak
            $output .= '<div class="validators"><ul>
              <li><a class="btn btn-small btn-info" href="http://validator.w3.org/check?verbose=1&amp;ss=1&amp;uri=' . urlencode(qualified_me()) . '"><i class="fa fa-cogs"></i>&nbsp;&nbsp;Validate HTML</a></li>
              <li><a class="btn btn-small btn-info" href="http://www.contentquality.com/mynewtester/cynthia.exe?rptmode=-1&amp;url1=' . urlencode(qualified_me()) . '"><i class="fa fa-cogs"></i>&nbsp;&nbsp;Section 508 Check</a></li>
              <li><a class="btn btn-small btn-info" href="http://www.contentquality.com/mynewtester/cynthia.exe?rptmode=0&amp;warnp2n3e=1&amp;url1=' . urlencode(qualified_me()) . '"><i class="fa fa-cogs"></i>&nbsp;&nbsp;WCAG 1 (2,3) Check</a></li>
            </ul><br /></div>';
        }
        if (!empty($CFG->additionalhtmlfooter)) {
            $output .= "\n".$CFG->additionalhtmlfooter;
        }
        return $output;
    }

    /**
     * Return the 'back' link that normally appears in the footer.
     *
     * @return string HTML fragment.
     */
    public function home_link() {
        global $CFG, $SITE;

        if ($this->page->pagetype == 'site-index') {
            // Special case for site home page - please do not remove
            return '<div class="sitelink">' .
                   '<a title="Moodle" href="http://moodle.org/">' .
                   '<img style="width:100px;height:30px" src="' . $this->pix_url('moodlelogo') . '" alt="moodlelogo" /></a></div>';

        } else if (!empty($CFG->target_release) && $CFG->target_release != $CFG->release) {
            // Special case for during install/upgrade.
            return '<div class="sitelink">'.
                   '<a title="Moodle" href="http://docs.moodle.org/en/Administrator_documentation" onclick="this.target=\'_blank\'">' .
                   '<img style="width:100px;height:30px" src="' . $this->pix_url('moodlelogo') . '" alt="moodlelogo" /></a></div>';

        } else if ($this->page->course->id == $SITE->id || strpos($this->page->pagetype, 'course-view') === 0) {
            return '<div class="homelink"><a class="btn btn-small" href="' . $CFG->wwwroot . '/"><i class="icon-home"></i>&nbsp;&nbsp;' .
                    get_string('home') . '</a></div>';

        } else {
            return '<div class="homelink"><a class="btn btn-small" href="' . $CFG->wwwroot . '/course/view.php?id=' . $this->page->course->id . '"><i class="icon-home"></i>&nbsp;&nbsp;' .
                    format_string($this->page->course->shortname, true, array('context' => $this->page->context)) . '</a></div>';
        }
    }


    /**
     * Returns HTML to display a "Turn editing on/off" button in a form.
     *
     * @param moodle_url $url The URL + params to send through when clicking the button
     * @return string HTML the button
     */
    public function edit_button(moodle_url $url) {

        $content = '';
        $url->param('sesskey', sesskey());
        if ($this->page->user_is_editing()) {
            $url->param('edit', 'off');
            $icon = 'fa-power-off';
            $btn = 'btn-danger';
            $title = get_string('turneditingoff');
        } else {
            $url->param('edit', 'on');
            $icon = 'fa-edit';
            $btn = 'btn-success';
            $title = get_string('turneditingon');
        }
        $itag = html_writer::tag('i', '', array('class' => 'fa ' . $icon));
        $content = html_writer::link($url, $itag . ' ' . $title, array('class' => 'btn ' . $btn, 'title' => $title));

        return $content;
    }

    /**
     * Renders a primary action_menu_filler item.
     *
     * @param action_menu_link_filler $action
     * @return string HTML fragment
     */

    protected function render_action_menu_filler(action_menu_filler $action) {
        return html_writer::tag('li', '', array('class' => 'divider'));
    }
}