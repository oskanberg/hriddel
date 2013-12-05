<?php

class ReviewsView extends View
{
    private $template = 'reviews.tpl';
    private $title = 'Reviews';
    
    public function display()
    {
        $data = array(
            'title' => $this->title,
            'view_specific_template' => $this->template,
            'reviews' => $this->_model->get_recent_reviews(500)
        );
        include_once(TEMPLATE_PATH . 'base_template.tpl');
    }
}

?>