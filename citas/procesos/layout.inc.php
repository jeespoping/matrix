<?php
include_once("conex.php");
/* Color Section.  All colors must be in hexadecimal format */
      $plain_text_color = '000000';
      $background_color = 'FFFFFF';
      $calendar_bg_color = 'C4E2FF';
      $calendar_border_color = 'AAAAAA';
      $mouse_over_color = 'C0C0C0';
      $link_color = '000000';
      $calendar_link_color = '000000';
      $current_day_color = 'F0FFFF';
      
/* Text and image section */
      $calender_title = 'Calendario de citas';
      $site_title = '';        /* This is added to any new windows text for the calender.  ie:  MySite Administration */

      $calender_title_image = '';    /* Leave blank to use $calender_title
                                                                                 * Can point to either full url or use
                                                                                 * standard directory structure 
                                                                                 * ie: ../images/calendar.jpg
                                                                                 * Image size should be no bigger then
                                                                                 * 350X100
                                                                                 */

/* Misc Variables */
      $time_zone = 'auto';         /* This variable sets the displayed time zone in the schedule window.  The default
                                                                                 * setting is "auto" which pulls the
                                                                                 * Time Zone from the server.  A setting
                                                                                 * of "" or blank will omit the time zone
                                                                                 * entirely.
                                                                                 */
      $time_format = '24';	   /* This variable sets the format used for setting and displaying time.  There are
                                                                                 * two vairable settings, '24' which
                                                                                 * uses standard military time format
                                                                                 * and '12' which uses AM/PM format. 
                                                                                 * Any other setting will default to 24hr
                                                                                 * format.
                                                                                 */
      $start_day = 1;            /* This variable sets the beginning day of the week for the calendar display.  
                                                                                 * Numbers correspond from 0 to 6
                                                                                 * with the standard days of the week
                                                                                 * from Sunday to Saturday.  Any other
                                                                                 * setting will default to a standard
                                                                                 * week beginning with Sunday.
                                                                                 */
?>