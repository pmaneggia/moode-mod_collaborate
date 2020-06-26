/**
 * Timing for the Developers Level 2 scheduled task
 *
 * @package    mod_collaborate
 * @since      Moodle 2.7
 * @copyright  2015 Flash Gordon http://www.flashgordon.com
 *
    @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$tasks = array(
  // The cron will run the specified task.
  array('classname' => 'mod_collaborate\task\collaborate_scheduled',
       
    'blocking' => 0,
        'minute' => '*/1',
        'hour' => '*',
        'day' => '*',
       
    'dayofweek' => '*',
        'month' => '*'
    )
);