<?php

class IndexView extends View
{
    private $template = 'index.tpl';
    private $title = 'Home';
    
    public function display()
    {
        $data = array(
            'title' => $this->title,
            'view_specific_template' => $this->template,
            'highlighted' => $this->_model->get_highlighted_articles(5),
            'liked' => $this->_model->get_most_liked(5),
            'recent_articles' => $this->_model->get_recent_articles(5),
            'recent_reviews' => $this->_model->get_recent_reviews(5),
            'recent_column_articles' => $this->_model->get_recent_column_articles(5)
        );
        include_once(TEMPLATE_PATH . 'base_template.tpl');
    }
}

?>