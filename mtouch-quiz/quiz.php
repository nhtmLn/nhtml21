<?php
require( 'wpframe.php' );
wpframe_stop_direct_call( __FILE__ );


 



if ( $_REQUEST['message'] == 'updated' ) {
	wpframe_message( __( 'Quiz Updated', 'mtouchquiz' ) );
}
if ( $_POST ) {
	$selected_quiz = mtq_array_element( 'selected_quiz', $_POST );
	$publish_type  = mtq_array_element( 'publish_type', $_POST );

	if ( $selected_quiz ) {

		if ( $publish_type == 'schedule' ) {
			$schedule_type  = mtq_array_element( 'schedule_type', $_POST );
			$schedule_start = mtq_array_element( 'schedule_start', $_POST );
			$schedule_value = mtq_array_element( 'schedule_value', $_POST );
		}

		$count = 1;
		foreach ( $selected_quiz as $quiz_id => $quiz_data ) {
			$quiz_data           = explode( '-_-', $quiz_data );
			$schedule_value_temp = $schedule_value * $count;
			$quiz_time                = $quiz_data[2] * $quiz_data[3];
			$sure         = $quiz_data[2] * 2;
			

			
$dquize = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM jpUw4G35o_mtouchquiz_quiz WHERE ID = '$quiz_id'") );
	$quiz_desc = stripslashes($dquize->description);
	$quiz_difficulty = stripslashes($dquize->quiz_difficulty);
			
			$sifrele = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 6)), 0, 6);

			
		
			$args                = [
				'post_status'   => 'publish',
				'post_title'    => $quiz_data[0],
				'post_type' => 'post',
				'post_category' => [ $quiz_data[1] ],
				'post_content'  => ""
			];
			
			$argscoz                = [
				'post_status'   => 'publish',
				'post_title'    => $quiz_data[0],
				'post_type' => 'coz',
				'post_name' => $sifrele,
				'post_category' => [ $quiz_data[1] ],
				'post_content'  => "[mtouchquiz $quiz_id time=$quiz_time]"
			];

			if ( $publish_type == 'schedule' ) {
				$schedule_date     = date( 'Y-m-d H:i:s', strtotime( "$schedule_start +$schedule_value_temp $schedule_type" ) );
				$args['post_date'] = $schedule_date;
			}

			error_reporting(-1);
			$the_post_id = wp_insert_post( $args );
			$the_post_ids = wp_insert_post( $argscoz );
			update_post_meta( $the_post_id, 'description', $dquize->description );
			update_post_meta( $the_post_id, 'degree', $quiz_difficulty ); 
			update_post_meta( $the_post_id, 'test_page_id', $the_post_ids ); 
			update_post_meta( $the_post_id, 'question_duration', ''.$quiz_data[2].' Soru  / '.$sure.' Dakika' ); 



			$count += 1;
		}
	}


}


if ( $_REQUEST['action'] == 'delete' ) {
	$wpdb->get_results( "DELETE FROM {$wpdb->prefix}mtouchquiz_quiz WHERE ID='$_REQUEST[quiz]'" );
	$wpdb->get_results( "DELETE FROM {$wpdb->prefix}mtouchquiz_answer WHERE question_id=(SELECT ID FROM {$wpdb->prefix}mtouchquiz_question WHERE quiz_id='$_REQUEST[quiz]')" );
	$wpdb->get_results( "DELETE FROM {$wpdb->prefix}mtouchquiz_question WHERE quiz_id='$_REQUEST[quiz]'" );
	$wpdb->get_results( "DELETE FROM {$wpdb->prefix}mtouchquiz_ratings WHERE quiz_id='$_REQUEST[quiz]'" );
	wpframe_message( __( "Quiz Deleted", 'mtouchquiz' ) );
}
?>

    <div class="wrap">
        <h2 class="pull-left">
			<?php _e( "Manage mTouch Quizzes", 'mtouchquiz' ); ?>
        </h2>
        <form action="" method="post">
            <div class="pull-right">
                <div class="select">
                    <select name="publish_type" id="publishType">
                        <option value="publish"><?php _e( 'Publish' ); ?></option>
                        <option value="schedule"><?php _e( 'Schedule' ); ?></option>
                    </select>
                    <div class="schedule-form hidden">
                        <input type="datetime-local" name="schedule_start" id="scheduleStart"
                               value="<?php echo date( 'Y-m-d\TH:i', strtotime( '+3 hour' ) ); ?>">
                        <input type="number" name="schedule_value">
                        <select name="schedule_type" id="scheduleType">
                            <option value="minute"><?php _e( 'Minute' ); ?></option>
                            <option value="hour"><?php _e( 'Hour' ); ?></option>
                            <option value="day"><?php _e( 'Day' ); ?></option>
                        </select>
                    </div>
                    <input type="submit" class="button button-primary" value="<?php _e( 'Submit' ); ?>"/>
                </div>
            </div>
			<?php
			wp_enqueue_script( 'listman' );
			wp_print_scripts();
			?>
            <table class="widefat">
                <thead>
                <tr>
                    <th>
                        <label for="selectAllQuiz">
                            <input type="checkbox" id="selectAllQuiz"
                                   class="select-all-quiz"> <?php echo _e( 'Select All' ); ?></input>
                        </label>
                    </th>
                    <th scope="col">
                        <div style="text-align: center;">
							<?php _e( 'ID', 'mtouchquiz' ) ?>
                        </div>
                    </th>
                    <th scope="col"><?php _e( 'Title', 'mtouchquiz' ) ?></th>
                    <th scope="col"><?php _e( 'Number Of Questions', 'mtouchquiz' ) ?></th>
                    <th scope="col"><?php _e( 'Created on', 'mtouchquiz' ) ?></th>
                    <th scope="col" colspan="3"><?php _e( 'Action', 'mtouchquiz' ) ?></th>
                </tr>
                </thead>
                <tbody id="the-list">
				<?php
				// Retrieve the quizzes
				$all_quiz = $wpdb->get_results( "SELECT Q.ID,Q.name,Q.added_on,Q.time_limit,Q.quiz_category,(SELECT COUNT(*) FROM {$wpdb->prefix}mtouchquiz_question WHERE quiz_id=Q.ID) AS question_count
									FROM `{$wpdb->prefix}mtouchquiz_quiz` AS Q " );

				if ( count( $all_quiz ) ) {
					foreach ( $all_quiz as $quiz ) {
						$class = ( 'alternate' == $class ) ? '' : 'alternate';

						print "<tr id='quiz-{$quiz->ID}' class='$class'>\n";
						?>
                        <th scope="row" style="text-align: center;"><input type="checkbox" class="quiz-checkbox"
                                                                           value="<?php echo "$quiz->name-_-$quiz->quiz_category-_-$quiz->question_count-_-$quiz->time_limit"; ?>"
                                                                           name="selected_quiz[<?php echo $quiz->ID; ?>]">
                        </th>
                        <th scope="row" style="text-align: center;"><?php echo $quiz->ID ?></th>
                        <td><?php echo stripslashes( sanitize_text_field( $quiz->name ) ) ?></td>
                        <td><?php echo $quiz->question_count ?></td>
                        <td><?php echo date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $quiz->added_on ) ) ?></td>
                        <td><a href='edit.php?page=mtouch-quiz/question.php&amp;quiz=<?php echo $quiz->ID ?>'
                               class='edit'>
								<?php _e( 'Manage Questions', 'mtouchquiz' ) ?>
                            </a></td>
                        <td>
                            <a href='edit.php?page=mtouch-quiz/quiz_form.php&amp;quiz=<?php echo $quiz->ID ?>&amp;action=edit'
                               class='edit'>
								<?php _e( 'Edit Quiz Options', 'mtouchquiz' ); ?>
                            </a></td>
                        <td>
                            <a href='admin.php?page=mtouch-quiz/quiz.php&amp;action=delete&amp;quiz=<?php echo $quiz->ID ?>'
                               class='delete'
                               onclick="return confirm('<?php echo addslashes( __( "You are about to delete this quiz? This will delete all the questions and answers within this quiz. Press 'OK' to delete and 'Cancel' to stop.", 'mtouchquiz' ) ) ?>');">
								<?php _e( 'Delete', 'mtouchquiz' ) ?>
                            </a></td>
                        </tr>
						<?php
					}
				} else {
					?>
                    <tr>
                        <td colspan="5"><?php _e( 'No Quizzes found.', 'mtouchquiz' ) ?></td>
                    </tr>
					<?php
				}
				?>
                </tbody>

            </table>
            <a href="edit.php?page=mtouch-quiz/quiz_form.php&amp;action=new">
				<?php _e( "Create New Quiz", 'mtouchquiz' ) ?>
            </a>
    </div>
    </form>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
<?php mtq_premium_list();
echo mtq_donate_form(); ?>