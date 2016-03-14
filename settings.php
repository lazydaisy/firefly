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
 * Theme Firefly settings file.
 *
 * @package    theme_firefly
 * @copyright  2014 Mary Evans (bylazydaisy.co.uk)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // @textColor setting.
    $name = 'theme_firefly/textcolor';
    $title = get_string('textcolor', 'theme_firefly');
    $description = get_string('textcolor_desc', 'theme_firefly');
    $default = '#DDDDDD';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, null, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // @linkColor setting.
    $name = 'theme_firefly/linkcolor';
    $title = get_string('linkcolor', 'theme_firefly');
    $description = get_string('linkcolor_desc', 'theme_firefly');
    $default = '#850F41';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, null, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // @bodyBackground setting.
    $name = 'theme_firefly/bodybackground';
    $title = get_string('bodybackground', 'theme_firefly');
    $description = get_string('bodybackground_desc', 'theme_firefly');
    $default = '';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, null, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Background image setting.
    $name = 'theme_firefly/backgroundimage';
    $title = get_string('backgroundimage', 'theme_firefly');
    $description = get_string('backgroundimage_desc', 'theme_firefly');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'backgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Background repeat setting.
    $name = 'theme_firefly/backgroundrepeat';
    $title = get_string('backgroundrepeat', 'theme_firefly');
    $description = get_string('backgroundrepeat_desc', 'theme_firefly');;
    $default = 'repeat';
    $choices = array(
        '0' => get_string('default'),
        'repeat' => get_string('backgroundrepeatrepeat', 'theme_firefly'),
        'repeat-x' => get_string('backgroundrepeatrepeatx', 'theme_firefly'),
        'repeat-y' => get_string('backgroundrepeatrepeaty', 'theme_firefly'),
        'no-repeat' => get_string('backgroundrepeatnorepeat', 'theme_firefly'),
    );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Background position setting.
    $name = 'theme_firefly/backgroundposition';
    $title = get_string('backgroundposition', 'theme_firefly');
    $description = get_string('backgroundposition_desc', 'theme_firefly');
    $default = '0';
    $choices = array(
        '0' => get_string('default'),
        'left_top' => get_string('backgroundpositionlefttop', 'theme_firefly'),
        'left_center' => get_string('backgroundpositionleftcenter', 'theme_firefly'),
        'left_bottom' => get_string('backgroundpositionleftbottom', 'theme_firefly'),
        'right_top' => get_string('backgroundpositionrighttop', 'theme_firefly'),
        'right_center' => get_string('backgroundpositionrightcenter', 'theme_firefly'),
        'right_bottom' => get_string('backgroundpositionrightbottom', 'theme_firefly'),
        'center_top' => get_string('backgroundpositioncentertop', 'theme_firefly'),
        'center_center' => get_string('backgroundpositioncentercenter', 'theme_firefly'),
        'center_bottom' => get_string('backgroundpositioncenterbottom', 'theme_firefly'),
    );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Background fixed setting.
    $name = 'theme_firefly/backgroundfixed';
    $title = get_string('backgroundfixed', 'theme_firefly');
    $description = get_string('backgroundfixed_desc', 'theme_firefly');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Invert Navbar to dark background.
    $name = 'theme_firefly/invert';
    $title = get_string('invert', 'theme_firefly');
    $description = get_string('invertdesc', 'theme_firefly');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Logo file setting.
    $name = 'theme_firefly/logo';
    $title = get_string('logo','theme_firefly');
    $description = get_string('logodesc', 'theme_firefly');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'logo');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Custom CSS file.
    $name = 'theme_firefly/customcss';
    $title = get_string('customcss', 'theme_firefly');
    $description = get_string('customcssdesc', 'theme_firefly');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Footnote setting.
    $name = 'theme_firefly/footnote';
    $title = get_string('footnote', 'theme_firefly');
    $description = get_string('footnotedesc', 'theme_firefly');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
}
