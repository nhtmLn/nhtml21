<?php if($quiz->is_classic): ?>
  <div class="mtq-classic-results">
    <h3><?php _e('Soru', 'mtouchquiz'); ?></h3>
    <div class="question"><?php echo $question->question; ?></div>
    
    <h3><?php _e('Sizin Cevabınız', 'mtouchquiz'); ?></h3>
    <div class="user-answer"><?php echo $user_answer; ?></div>
    
    <h3><?php _e('Doğru Cevap', 'mtouchquiz'); ?></h3>
    <div class="correct-answer"><?php echo $question->classic_answer; ?></div>
  </div>
<?php endif; ?>