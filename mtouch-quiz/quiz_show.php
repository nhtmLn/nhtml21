<?php if($quiz->is_classic): ?>
    <div class="mtq-classic-results">
        <h2><?php _e('Sınav Sonuçları', 'mtouchquiz'); ?></h2>
        
        <?php foreach($questions as $question): ?>
            <?php 
            $user_answer = $wpdb->get_var($wpdb->prepare(
                "SELECT user_answer FROM {$wpdb->prefix}mtouchquiz_results 
                WHERE quiz_id = %d AND question_id = %d AND user_id = %d 
                ORDER BY timestamp DESC LIMIT 1",
                $quiz->ID, $question->ID, get_current_user_id()
            ));
            ?>
            
            <div class="result-item">
                <h3><?php echo __('Soru', 'mtouchquiz') . ' ' . $question->sort_order; ?></h3>
                
                <div class="question-content">
                    <?php echo apply_filters('the_content', $question->question); ?>
                </div>
                
                <div class="answer-comparison">
                    <div class="user-answer">
                        <h4><?php _e('Sizin Cevabınız:', 'mtouchquiz'); ?></h4>
                        <?php echo wpautop($user_answer); ?>
                    </div>
                    
                    <div class="correct-answer">
                        <h4><?php _e('Doğru Cevap:', 'mtouchquiz'); ?></h4>
                        <?php echo apply_filters('the_content', $question->classic_answer); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <style>
    .mtq-classic-results {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .result-item {
        margin-bottom: 40px;
        padding: 20px;
        background: #f9f9f9;
        border-radius: 5px;
    }
    
    .answer-comparison {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-top: 20px;
    }
    
    .user-answer, .correct-answer {
        padding: 15px;
        background: #fff;
        border-radius: 3px;
        border: 1px solid #ddd;
    }
    
    .user-answer h4, .correct-answer h4 {
        margin-top: 0;
        color: #333;
    }
    </style>
<?php else: ?>
    <!-- Mevcut çoktan seçmeli sonuç kodu buraya -->
<?php endif; ?>