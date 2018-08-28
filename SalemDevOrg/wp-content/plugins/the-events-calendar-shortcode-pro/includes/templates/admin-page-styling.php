<p><?php echo sprintf( esc_html( "Can't find the option you're looking for? %sSubmit a support request%s and we'll do our best to help!" ), '<a href="https://eventcalendarnewsletter.com/contact">', '</a>' ); ?></p>
<p><?php echo esc_html( __( "By default the plugin shows a listing of events in a style similar to The Events Calendar list view.", 'the-events-calendar-shortcode' ) ); ?></p>

<h2><?php echo esc_html( __( 'Default Design', 'the-events-calendar-shortcode' ) ) ?></h2>

<p><?php echo esc_html__( 'The default design includes event details, photo, excerpt and more.', 'the-events-calendar-shortcode' ); ?></p>

<blockquote>[ecs-list-events]</blockquote>

<p><a target="_blank" href="https://demo.eventcalendarnewsletter.com/the-events-calendar-shortcode/default-design/"><?= esc_html__( 'View Examples', 'the-events-calendar-shortcode' ) ?></a></p>

<h2><?php echo esc_html( __( 'Standard Design', 'the-events-calendar-shortcode' ) ); ?></h2>

<p><?php echo esc_html( sprintf( __( 'If you would like to use the styling/html from the free version, just add %s to the shortcode:', 'the-events-calendar-shortcode' ), 'design="standard"' ) ); ?></p>

<blockquote>[ecs-list-events design="standard"]</blockquote>

<h2><?php echo esc_html( __( 'Compact Design', 'the-events-calendar-shortcode' ) ) ?></h2>

<p><?php echo sprintf( esc_html( __( 'If you would like to use a more compact view, just add %s to the shortcode:', 'the-events-calendar-shortcode' ) ), 'design="compact"' ); ?></p>

<blockquote>[ecs-list-events design="compact"]</blockquote>

<p><?php echo sprintf( esc_html( __( 'You can customize the colors using %s (text color) and %s (background color), for example:', 'the-events-calendar-shortcode' ) ), 'fgthumb', 'bgthumb' ); ?></p>

<blockquote>[ecs-list-events design="compact" fgthumb="#efefef" bgthumb="#000000"]</blockquote>
<blockquote>[ecs-list-events design="compact" fgthumb="black" bgthumb="red"]</blockquote>

<p><a target="_blank" href="https://demo.eventcalendarnewsletter.com/the-events-calendar-shortcode/compact-design/"><?= esc_html__( 'View Examples', 'the-events-calendar-shortcode' ) ?></a></p>

<h2><?php echo esc_html( __( 'Calendar Design', 'the-events-calendar-shortcode' ) ) ?></h2>

<p><?php echo esc_html( __( 'Creates a full calendar view:', 'the-events-calendar-shortcode' ) ); ?></p>

<blockquote>[ecs-list-events design="calendar"]</blockquote>
<blockquote>[ecs-list-events design="calendar" height="500"]</blockquote>
<blockquote>[ecs-list-events design="calendar" eventbg="#f77530" eventfg="black" eventborder="blue"]</blockquote>
<blockquote>[ecs-list-events design="calendar" defaultview="listMonth"]</blockquote>
<blockquote>[ecs-list-events design="calendar" hide_extra_days="true"]</blockquote>

<p><?php echo esc_html( __( 'Change the first day of the week, ie. 0=Sunday, 1=Monday etc:', 'the-events-calendar-shortcode' ) ); ?></p>

<blockquote>[ecs-list-events design="calendar" first_day_of_week="1"]</blockquote>

<p><a target="_blank" href="https://demo.eventcalendarnewsletter.com/the-events-calendar-shortcode/calendar-design/"><?= esc_html__( 'View Examples', 'the-events-calendar-shortcode' ) ?></a></p>

<h2><?php echo esc_html( __( 'Horizontal/Columns/Photo Design', 'the-events-calendar-shortcode' ) ) ?></h2>

<p><?php echo esc_html( __( 'Creates a listing of your events split into columns:', 'the-events-calendar-shortcode' ) ); ?></p>

<blockquote>[ecs-list-events design="columns"]</blockquote>
<blockquote>[ecs-list-events design="columns" limit="6"]</blockquote>

<p><?php echo sprintf( esc_html( __( 'The design defaults to three columns but you can change that with the %s property:', 'the-events-calendar-shortcode' ) ), 'columns' ); ?></p>

<blockquote>[ecs-list-events design="columns" columns="4"]</blockquote>

<p><a target="_blank" href="https://demo.eventcalendarnewsletter.com/the-events-calendar-shortcode/columns-photos-horizontal-design/"><?= esc_html__( 'View Examples', 'the-events-calendar-shortcode' ) ?></a></p>

<h2><?php echo esc_html( __( 'Grouped Design', 'the-events-calendar-shortcode' ) ) ?></h2>

<p><?php echo sprintf( esc_html( __( 'If you would like to group the events by day with just the time under each date, just add %s to the shortcode:', 'the-events-calendar-shortcode' ) ), 'design="grouped"' ); ?></p>

<blockquote>[ecs-list-events design="grouped"]</blockquote>
<blockquote>[ecs-list-events design="grouped" groupby="month"]</blockquote>

<p><a target="_blank" href="https://demo.eventcalendarnewsletter.com/the-events-calendar-shortcode/grouped-design/"><?= esc_html__( 'View Examples', 'the-events-calendar-shortcode' ) ?></a></p>

<h2><?php echo esc_html( __( 'Additional Design Options', 'the-events-calendar-shortcode' ) ) ?></h2>

<p><?php echo esc_html( __( 'You can also customize the output of most of the designs with things like:', 'the-events-calendar-shortcode' ) ); ?></p>

<ul>
	<li><strong>titlesize</strong> <?php echo esc_html( __( 'to customize the size of the title, ie.', 'the-events-calendar-shortcode' ) ) ?> titlesize="18px"</li>
	<li><strong>thumb="true"</strong> <?php echo esc_html( __( 'to show the event thumbnail image (if any)', 'the-events-calendar-shortcode' ) ); ?></li>
	<li><strong>excerpt="true"</strong> <?php echo esc_html( __( 'to show the event summary description under the title', 'the-events-calendar-shortcode' ) ); ?></li>
	<li><strong>venue="true"</strong> <?php echo esc_html( __( 'to show the venue name under the title', 'the-events-calendar-shortcode' ) ); ?></li>
	<li><strong>timeonly="true"</strong> <?php echo esc_html( __( 'to show the start time without the date', 'the-events-calendar-shortcode' ) ); ?></li>
	<li><strong>button="View Details"</strong> <?php echo sprintf( esc_html( __( 'to show a button with a link to the event (change "%s" to whatever you want)', 'the-events-calendar-shortcode' ) ), 'View Details' ); ?></li>
	<li><strong>buttonlink="website"</strong> <?php echo esc_html( __( 'to use the event website URL for the button', 'the-events-calendar-shortcode' ) ); ?></li>
	<li><strong>buttonbg</strong> <?php echo esc_html( __( 'and', 'the-events-calendar-shortcode' ) ); ?> <strong>buttonfg</strong> <?php echo esc_html( __( 'to customize button color', 'the-events-calendar-shortcode' ) ); ?></li>
</ul>

<h2><?php echo esc_html( __( 'Custom Design', 'the-events-calendar-shortcode' ) ); ?></h2>

<p><?php echo wp_kses( sprintf( __( "If you'd like to completely customize the output you can create a folder called <strong>%s</strong> in your theme directory, and in it create a file called <strong>%s</strong>", 'the-events-calendar-shortcode' ), 'tecshortcode', 'custom.php' ), array( 'strong' => array() ) ); ?></p>
<p><?php echo esc_html( sprintf( __( 'Then add your shortcode with "%s" as the design name (which matches %s):', 'the-events-calendar-shortcode' ), 'custom', 'custom.php' ) ); ?></p>
<blockquote>[ecs-list-events design="custom"]</blockquote>
<p><?php echo esc_html( sprintf( __( 'You can rename %s to another name and even create multiple design templates to use elsewhere on your site.  Note that you should place this folder within a child theme so any updates to your main theme do not override your templates.', 'the-events-calendar-shortcode' ), 'custom.php' ) ); ?></p>