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
 * Custom renderer for output of pages
 *
 * @package    mod_simplelesson
 * @copyright  2019 Richard Jones <richardnz@outlook.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_collaborate
 * @see https://github.com/justinhunt/moodle-mod_collaborate
 */
use \mod_collaborate\local\debugging;
defined('MOODLE_INTERNAL') || die();

/**
 * Renderer for collaborate mod.
 */
class mod_collaborate_renderer extends plugin_renderer_base {

    /**
     * Displays the main view page content.
     *
     * @param $collaborate the collaborate instance std Object
     * @param $cm the course module std Object
     * @param boolean true if the report tabs are to be shown.
     * @return none
     */
    public function render_view_page_content($collaborate, $cm, $reportstab = false) {

        $data = new stdClass();

        $data->heading = $collaborate->title;
        // Moodle handles processing of std intro field.
        $data->body = format_module_intro('collaborate',
                $collaborate, $cm->id);

        // Show reports tab?
        $data->reportstab = $reportstab;

        // Set up the user page URLs.
        $a = new \moodle_url('/mod/collaborate/showpage.php', ['cid' => $collaborate->id, 'page' => 'a']);
        $b = new \moodle_url('/mod/collaborate/showpage.php', ['cid' => $collaborate->id, 'page' => 'b']);
        $data->url_a = $a->out(false);
        $data->url_b = $b->out(false);

        // Add links to reports tabs, if enabled.
        if ($reportstab) {
            $r = new \moodle_url('/mod/collaborate/reports.php',
                    ['cid' => $collaborate->id]);
            $v = new \moodle_url('/mod/collaborate/view.php', ['id' => $cm->id]);
            $data->url_reports = $r->out(false);
            $data->url_view = $v->out(false);
        }

        // Display the view page content.
        echo $this->output->header();
        echo $this->render_from_template('mod_collaborate/view', $data);
        echo $this->output->footer();
    }

    /**
     * Displays the page content.
     *
     * @param $collaborate the collaborate instance std Object
     * @param $cm the course module std Object
     * @param $page the page ... ??? what a comment!!!
     * @param $form the editor form for submitting.
     * @return none
     */
    public function render_page_content($collaborate, $cm, $page, $form) {
        $data = new stdClass();
        $data->heading = $collaborate->title;
        $data->user = 'User: '. strtoupper($page);
        // Get the content from the database.
        $content = ($page == 'a') ? $collaborate->instructionsa : $collaborate->instructionsb;
        
        // $data->body = $content;  //this was before we got stuff from the editor
        // new in week 4 tast 2
        $filearea = 'instructions' . $page; 
        $context = context_module::instance($cm->id);
        $content = file_rewrite_pluginfile_urls($content, 'pluginfile.php', $context->id,
            'mod_collaborate', $filearea, $collaborate->id);

        // Run the content through format_text to enable streaming video etc.
        $formatoptions = new stdClass;
        $formatoptions->overflowdiv = true;
        $formatoptions->context = $context;
        $format = ($page == 'a') ? $collaborate->instructionsaformat : $collaborate->instructionsbformat;    

        // Now we can assign the content to the $data->body:
        $data->body = format_text($content, $format, $formatoptions);
        // and we can pass that content to the mustache template as html.

        // Get the form html.
        $data->form = $form->render();

        // Get a return url back to view page.
        $urlv = new \moodle_url('/mod/collaborate/view.php', ['id' => $cm->id]);
        $data->url_view = $urlv->out(false);
        // Display the show page content.
        echo $this->output->header();
        echo $this->render_from_template('mod_collaborate/show', $data);
        echo $this->output->footer();
    }

    /**
     * Displays the reports. (week 6) TODO
     *
     * @param object $collaborate the collaborate instance std Object.
     * @param object $cm the course module std Object.
     * @param array $submissions 2D array of submission records.
     * @param string $headers the strings for the column headers.
     * @return none
     */
    public function render_reports_page_content($collaborate, $cm, $submissions, $headers) {
        $data = new stdClass();
        
        $data->heading = get_string('submissions', 'mod_collaborate');
        $data->headers = $headers;
        $data->submissions = $submissions;
        
        // The tabs. // TODO extract to a function producing the tabs for both methods
        $r = new \moodle_url('/mod/collaborate/reports.php', ['cid' => $collaborate->id]);
        $v = new \moodle_url('/mod/collaborate/view.php', ['id' => $cm->id]);
        $data->url_reports = $r->out(false);
        $data->url_view = $v->out(false);

        // Display the page content.
        echo $this->output->header();
        echo $this->render_from_template('mod_collaborate/reports', $data);
        echo $this->output->footer();
    }

    public function render_submission_to_grade($submission, $context, $cid, $sid) {

    }
}