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
     * Redirects the user by any means possible given the current state
     *
     * This function should not be called directly, it should always be called using
     * the redirect function in lib/weblib.php
     *
     * The redirect function should really only be called before page output has started
     * however it will allow itself to be called during the state STATE_IN_BODY
     *
     * @param string $encodedurl The URL to send to encoded if required
     * @param string $message The message to display to the user if any
     * @param int $delay The delay before redirecting a user, if $message has been
     *         set this is a requirement and defaults to 3, set to 0 no delay
     * @param boolean $debugdisableredirect this redirect has been disabled for
     *         debugging purposes. Display a message that explains, and don't
     *         trigger the redirect.
     * @return string The HTML to display to the user before dying, may contain
     *         meta refresh, javascript refresh, and may have set header redirects
     */
    public function redirect_message($encodedurl, $message, $delay, $debugdisableredirect) {
        global $CFG;
        $url = str_replace('&amp;', '&', $encodedurl);

        switch ($this->page->state) {
            case moodle_page::STATE_BEFORE_HEADER :
                // No output yet it is safe to delivery the full arsenal of redirect methods
                if (!$debugdisableredirect) {
                    // Don't use exactly the same time here, it can cause problems when both redirects fire at the same time.
                    $this->metarefreshtag = '<meta http-equiv="refresh" content="'. $delay .'; url='. $encodedurl .'" />'."\n";
                    $this->page->requires->js_function_call('document.location.replace', array($url), false, ($delay + 3));
                }
                $output = $this->header();
                break;
            case moodle_page::STATE_PRINTING_HEADER :
                // We should hopefully never get here
                throw new coding_exception('You cannot redirect while printing the page header');
                break;
            case moodle_page::STATE_IN_BODY :
                // We really shouldn't be here but we can deal with this
                debugging("You should really redirect before you start page output");
                if (!$debugdisableredirect) {
                    $this->page->requires->js_function_call('document.location.replace', array($url), false, $delay);
                }
                $output = $this->opencontainers->pop_all_but_last();
                break;
            case moodle_page::STATE_DONE :
                // Too late to be calling redirect now
                throw new coding_exception('You cannot redirect after the entire page has been generated');
                break;
        }
        $output .= $this->notification($message, 'redirectmessage');
        $output .= '<div class="continuebutton"><a class="btn btn-small btn-info" href="'. $encodedurl .'">'. get_string('continue') .'&nbsp;&nbsp;<i class="fa fa-forward"></i></a></div>';
        if ($debugdisableredirect) {
            $output .= '<p><strong>Error output, so disabling automatic redirect.</strong></p>';
        }
        $output .= $this->footer();
        return $output;
    }

        /**
         * Returns HTML to display a "Turn editing on/off" button in a form.
         *
         * @param moodle_url $url The URL + params to send through when clicking the button
         * @return string HTML the button
         */
        public function edit_button(moodle_url $url) {

            $url->param('sesskey', sesskey());
            if ($this->page->user_is_editing()) {
                $url->param('edit', 'off');
                $text = '<a href="'.$url.'" class="btn btn-danger" title="'.get_string('turneditingoff').'"><i class="fa fa-power-off"></i></a>';
            } else {
                $url->param('edit', 'on');
                $text = '<a href="'.$url.'" class="btn btn-success"  title="'.get_string('turneditingon').'"><i class="fa fa-edit"></i></a>';
            }

            return ($text);
    }

   /*
    * This code replaces the standard moodle icons
    * with a icon sprite that is included in bootstrap
    * If the icon is not listed in the $icons array
    * the original Moodle icon will be shown
    */

    static $icons = array(
        'add' => 'plus',
        'book' => 'book',
        'chapter' => 'file',
        'docs' => 'question-sign',
        'generate' => 'gift',
        'spacer' => 'spacer',
        'a/logout' => 'sign-out',
        'i/assignroles' => 'user',
        'i/course' => 'home',
        'i/backup' => 'cogs',
        'i/dragdrop' => 'crosshairs',
        'i/edit' => 'edit',
        'i/enrolusers' => 'user',
        'i/filter' => 'filter',
        'i/grades' => 'bar-chart-o',
        'i/group' => 'user',
        'i/hide' => 'eye',
        'i/move_2d' => 'arrows',
        'i/navigationitem' => 'caret-right',
        'i/publish' => 'publish',
        'i/reload' => 'refresh',
        'i/report' => 'list-alt',
        'i/restore' => 'gears',
        'i/return' => 'repeat',
        'i/roles' => 'user',
        'i/settings' => 'list-alt',
        'i/show' => 'eye-slash',
        'i/user' => 'user',
        'i/users' => 'user',
        't/add' => 'plus',
        't/addcontact' => 'plus',
        't/assignroles' => 'user',
        't/award' => 'certificate',
        't/block_to_dock' => 'toggle-left',
        't/dock_to_block' => 'toggle-right',
        't/copy' => 'retweet',
        't/delete' => 'times',
        't/down' => 'arrow-down',
        't/download' => 'download',
        't/edit' => 'edit',
        't/editstring' => 'pencil',
        't/groupn' => 'group',
        't/groups' => 'group',
        't/groupv' => 'group',
        't/hide' => 'eye',
        't/left' => 'arrow-left',
        't/message' => 'retweet',
        't/move' => 'crosshairs',
        't/right' => 'arrow-right',
        't/show' => 'eye-slash',
        't/sort' => 'sort',
        't/switch_minus' => 'toggle-up',
        't/switch_plus' => 'toggle-down',
        't/up' => 'arrow-up',
        'mod_assign' => 'pencil',
        'nav_next' => 'chevron-right',
        'nav_prev' => 'chevron-left');

    public function block_controls($actions, $blockid = null) {
        global $CFG;
        if (empty($actions)) {
            return '';
        }
        $menu = new action_menu($actions);
        if ($blockid !== null) {
            $menu->set_owner_selector('#'.$blockid);
        }
        $menu->attributes['class'] .= ' block-control-actions commands icon';
        if (isset($CFG->blockeditingmenu) && !$CFG->blockeditingmenu) {
            $menu->do_not_enhance();
        }
        return $this->render($menu);
    }

    protected static function a($attributes, $content) {
        return html_writer::tag('a', $content, $attributes);
    }
    protected static function div($attributes, $content) {
        return html_writer::tag('div', $content, $attributes);
    }
    protected static function span($attributes, $content) {
        return html_writer::tag('span', $content, $attributes);
    }
    protected static function icon($name, $text=null) {
        if (!$text) {$text = $name;}
        return html_writer::tag('i', '', array('class' => 'fa fa-'.$name));
    }
    protected static function moodle_icon($name) {
        return self::icon(self::$icons[$name]);
    }
    public function icon_help() {
        return self::icon('question-sign');
    }
    public function action_icon($url, pix_icon $pixicon, component_action $action = null,
        array $attributes = null, $linktext=false) {

        if (!($url instanceof moodle_url)) {
            $url = new moodle_url($url);
        }
        $attributes = (array)$attributes;

        if (empty($attributes['class'])) {
            // let ppl override the class via $options
            $attributes['class'] = 'action-icon';
        }

        $icon = $this->render($pixicon);

        if ($linktext) {
            $text = $pixicon->attributes['alt'];
        } else {
            $text = '';
        }

        return $this->action_link($url, $text.$icon, $action, $attributes);
    }

    protected function render_pix_icon(pix_icon $icon) {

        if (isset(self::$icons[$icon->pix])) {
            return self::icon(self::$icons[$icon->pix]);
        } else {

            $attributes = $icon->attributes;
            $attributes['src'] = $this->pix_url($icon->pix, $icon->component);
            $component = $icon->component;

        if ($component == 'mod_assign') {
            return html_writer::tag('i', '', array('class' => 'fa fa-book fa-2x'));
        } else if ($component == 'mod_assignment') {
            return html_writer::tag('i', '', array('class' => 'fa fa-book fa-2x'));
        } else if ($component == 'mod_book') {
            return html_writer::tag('i', '', array('class' => 'fa fa-book fa-2x'));
        } else if ($component == 'mod_chat') {
            return html_writer::tag('i', '', array('class' => 'fa fa-comment-o fa-2x'));
        } else if ($component == 'mod_choice') {
            return html_writer::tag('i', '', array('class' => 'fa fa-question fa-2x'));
        } else if ($component == 'mod_data') {
            return html_writer::tag('i', '', array('class' => 'fa fa-database fa-2x'));
        } else if ($component == 'mod_feedback') {
            return html_writer::tag('i', '', array('class' => 'fa fa-bullhorn fa-2x'));
        } else if ($component == 'mod_folder') {
            return html_writer::tag('i', '', array('class' => 'fa fa-folder-open fa-2x'));
        } else if ($component == 'mod_forum') {
            return html_writer::tag('i', '', array('class' => 'fa fa-comments-o fa-2x'));
        } else if ($component == 'mod_glossary') {
            return html_writer::tag('i', '', array('class' => 'fa fa-sort-alpha-asc fa-2x'));
        } else if ($component == 'mod_imscp') {
            return html_writer::tag('i', '', array('class' => 'fa fa-stack-overflow fa-2x'));
        } else if ($component == 'mod_label') {
            return html_writer::tag('i', '', array('class' => 'fa fa-tag fa-2x'));
        } else if ($component == 'mod_lesson') {
            return html_writer::tag('i', '', array('class' => 'fa fa-mortar-board fa-2x'));
        } else if ($component == 'mod_lti') {
            return html_writer::tag('i', '', array('class' => 'fa fa-puzzle-piece fa-2x'));
        } else if ($component == 'mod_page') {
            return html_writer::tag('i', '', array('class' => 'fa fa-file-text-o fa-2x'));
        } else if ($component == 'mod_quiz') {
            return html_writer::tag('i', '', array('class' => 'fa fa-connectdevelop fa-2x'));
        } else if ($component == 'mod_resource') {
            return html_writer::tag('i', '', array('class' => 'fa fa-file-word-o fa-2x'));
        } else if ($component == 'mod_scorm') {
            return html_writer::tag('i', '', array('class' => 'fa fa-gift fa-2x'));
        } else if ($component == 'mod_survey') {
            return html_writer::tag('i', '', array('class' => 'fa fa-bar-chart fa-2x'));
        } else if ($component == 'mod_url') {
            return html_writer::tag('i', '', array('class' => 'fa fa-coffee fa-2x'));
        } else if ($component == 'mod_wiki') {
            return html_writer::tag('i', '', array('class' => 'fa fa-coffee fa-2x'));
        } else if ($component == 'mod_workshop') {
            return html_writer::tag('i', '', array('class' => 'fa fa-coffee fa-2x'));
        } else {
            //return parent::render_pix_icon($icon);
            return '<i class=icon-not-assigned data-debug-icon="'.$icon->pix.'"></i>';
            }
        }
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