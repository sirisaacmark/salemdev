<h3>id</h3>
<p><?php echo esc_html( __( 'Fetch a single event using the ID of that event', 'the-events-calendar-shortcode' ) ); ?></p>
<blockquote>[ecs-list-events id='123']</blockquote>

<h3>exclude_id</h3>
<p><?php echo sprintf( esc_html( __( 'Exclude a single event from the listing.  Use "%s" when using the shortcode on an event page to exclude the current event.', 'the-events-calendar-shortcode' ) ), 'current' ); ?></p>
<blockquote>[ecs-list-events exclude_id='123']</blockquote>
<blockquote>[ecs-list-events exclude_id='current']</blockquote>

<h3>days</h3>
<p><?php echo esc_html( sprintf( __( 'Specifies the number of days from today to fetch events.  If specified, overrides the "%s" option', 'the-events-calendar-shortcode' ), 'month' ) ); ?></p>
<p><?php echo esc_html( __( 'For example to get events for the next week:', 'the-events-calendar-shortcode' ) ) ?></p>
<blockquote>[ecs-list-events days='7']</blockquote>
<p><?php echo esc_html( __( 'Or to get events for the next day:', 'the-events-calendar-shortcode' ) ); ?></p>
<blockquote>[ecs-list-events days='1']</blockquote>

<h3>day</h3>
<p><?php echo esc_html( sprintf( __( 'Specifies the day of events to fetch.  If specified, overrides the "%s" option', 'the-events-calendar-shortcode' ), 'month' ) ); ?></p>
<blockquote>[ecs-list-events day='2017-04-15']</blockquote>
<blockquote>[ecs-list-events day='current']</blockquote>
<p><?php echo esc_html( __( 'or for the current day but in future only:', 'the-events-calendar-shortcode' ) ) ?></p>
<blockquote>[ecs-list-events day='current' futureonly='true']</blockquote>

<h3>year</h3>
<p><?php echo esc_html( __( 'Show events for a certain year', 'the-events-calendar-shortcode' ) ); ?></p>
<blockquote>[ecs-list-events year='2017']</blockquote>
<blockquote>[ecs-list-events year='current']</blockquote>
<p><?php echo esc_html( __( 'or to only show events from a certain year but in the future from today:', 'the-events-calendar-shortcode' ) ); ?></p>
<blockquote>[ecs-list-events year='2017' futureonly='true']</blockquote>

<h3>date range</h3>
<p><?php echo esc_html( __( 'Show events between certain dates', 'the-events-calendar-shortcode' ) ); ?></p>
<blockquote>[ecs-list-events fromdate='2017-05-31' todate='2017-06-15']</blockquote>
<p><?php echo sprintf( esc_html( __( 'or you can specify either the from or the to date, and optionally add %s to only show future events', 'the-events-calendar-shortcode' ) ), 'futureonly' ); ?></p>
<blockquote>[ecs-list-events fromdate='2017-07-25']</blockquote>
<blockquote>[ecs-list-events todate='2018-06-15']</blockquote>
<blockquote>[ecs-list-events todate='2018-07-25' futureonly='true']</blockquote>
<?php echo sprintf( esc_html( __( 'or optionally add %s to only show events that have not finished', 'the-events-calendar-shortcode' ) ), 'hide_finished' ); ?>
<blockquote>[ecs-list-events fromdate='-30 days' todate='today' key='start date' hide_finished='true']</blockquote>

<h3>include in progress</h3>
<p><?php echo esc_html( __( 'For use with fromdate/todate, days, day and year to also include events that are "in progress" (ie. start before and end after the current day).', 'the-events-calendar-shortcode' ) ); ?></p>
<blockquote>[ecs-list-events fromdate='2018-05-31' todate='2018-06-15' include_in_progress='true']</blockquote>
<blockquote>[ecs-list-events day='current' include_in_progress='true']</blockquote>
<blockquote>[ecs-list-events year='current' include_in_progress='true']</blockquote>
<blockquote>[ecs-list-events days='7' include_in_progress='true']</blockquote>

<h3>exclude categories</h3>
<p><?php echo esc_html( __( 'Include events that are not in the specified categories.  Useful for excluding any community submitted events.', 'the-events-calendar-shortcode' ) ); ?></p>
<blockquote>[ecs-list-events exclude_cat='community']</blockquote>
<blockquote>[ecs-list-events exclude_cat='meeting, gala']</blockquote>

<h3>tag</h3>
<p><?php echo esc_html( __( 'Represents single event tag.  Use commas when you want multiple tags.', 'the-events-calendar-shortcode' ) ); ?>
<blockquote>[ecs-list-events tag='special']</blockquote>
<blockquote>[ecs-list-events tag='special, workshops']</blockquote>

<h3>exclude tags</h3>
<p><?php echo esc_html( __( 'Include events that are not in the specified tags.', 'the-events-calendar-shortcode' ) ); ?></p>
<blockquote>[ecs-list-events exclude_tag='regular-event']</blockquote>
<blockquote>[ecs-list-events exclude_tag='large-event, tag2']</blockquote>

<h3>tag_cat_operator</h3>
<p><?php echo esc_html( __( 'Used to require events to have ALL specified categories or tags, instead of just one of them.', 'the-events-calendar-shortcode' ) ); ?>
<blockquote>[ecs-list-events cat='special, workshops' tag_cat_operator='AND']</blockquote>
<blockquote>[ecs-list-events tag='nature, meeting' tag_cat_operator='AND']</blockquote>

<h3>hiderecurring</h3>
<p><?php echo sprintf( esc_html( __( 'Option to only show the first event from a recurring event (%s).', 'the-events-calendar-shortcode' ) ), 'The Events Calendar PRO' ); ?></p>
<blockquote>[ecs-list-events hiderecurring='true']</blockquote>

<h3>raw_excerpt</h3>
<p><?php echo esc_html( __( 'Uses the raw excerpt rather than stripping any HTML from it or modifying the result in any way.', 'the-events-calendar-shortcode' ) ); ?></p>
<blockquote>[ecs-list-events excerpt="true" raw_excerpt="true"]</blockquote>

<h3>description</h3>
<p><?php echo sprintf( esc_html( __( 'Use the full description for the excerpt rather than the WordPress excerpt or short description. Can also use "%s" with it to avoid removing any formatting and links.', 'the-events-calendar-shortcode' ) ), 'raw_description' ); ?></p>
<blockquote>[ecs-list-events excerpt="true" description="true"]</blockquote>
<blockquote>[ecs-list-events excerpt="100" description="true"]</blockquote>
<blockquote>[ecs-list-events excerpt="true" description="true" raw_description="true"]</blockquote>

<h3>city</h3>
<h3>state</h3>
<h3>country</h3>

<p><?php echo esc_html( __( 'Filter events by a location city, state or province, or country', 'the-events-calendar-shortcode' ) ); ?></p>
<blockquote>[ecs-list-events city='Chicago']</blockquote>
<blockquote>[ecs-list-events state='IL']</blockquote>
<blockquote>[ecs-list-events country='United States, Canada']</blockquote>
<p><?php echo esc_html( __( 'If the location itself has a comma, use -- instead:', 'the-events-calendar-shortcode' ) ); ?></p>
<blockquote>[ecs-list-events country='Korea-- Republic of']</blockquote>

<h3>featured only</h3>
<p><?php echo esc_html( __( 'Only show events that are marked "featured"', 'the-events-calendar-shortcode' ) ); ?></p>
<blockquote>[ecs-list-events featured_only='true']</blockquote>

<h3>exclude featured</h3>
<p><?php echo esc_html( __( 'Only show events that are not marked "featured"', 'the-events-calendar-shortcode' ) ); ?></p>
<blockquote>[ecs-list-events exclude_featured='true']</blockquote>

<h3>venue_id</h3>
<p><?php echo esc_html( __( 'Filter events by one or more venue ids', 'the-events-calendar-shortcode' ) ); ?></p>
<blockquote>[ecs-list-events venue_id='52']</blockquote>

<h3>organizer_id</h3>
<p><?php echo esc_html( __( 'Filter events by one or more organizer ids', 'the-events-calendar-shortcode' ) ); ?></p>
<blockquote>[ecs-list-events organizer_id='72']</blockquote>

<h3>offset</h3>
<p><?php echo esc_html( __( 'Number of events to skip.  Useful if you want to split events into columns without CSS or have the first event be in a different design.', 'the-events-calendar-shortcode' ) ); ?></p>
<blockquote>[ecs-list-events offset="2"]</blockquote>

<h3><?php echo esc_html__( 'Custom Fields', 'the-events-calendar-shortcode' ); ?></h3>

<p><?php echo esc_html( __( 'When using The Events Calendar PRO "Additional Fields", Advanced Custom Fields, or Toolset Types, you can use that field when filtering. For example if you have a custom field with slug my_field you can do the following to filter for all events with a value of "Test":' ) ); ?></p>
<blockquote>[ecs-list-events my_field="Test"]</blockquote>
<p><?php echo esc_html( sprintf( __( 'More information available %son the blog post%s' ), '<a href="https://eventcalendarnewsletter.com/support-custom-fields-added-events-calendar-shortcode/">', '</a>' ) ); ?></p>
