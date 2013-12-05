<?php

class ArticlesView extends View
{
    private $template = 'articles.tpl';
    private $title = 'Articles';
    
    public function display()
    {
        $data = array(
            'title' => $this->title,
            'view_specific_template' => $this->template,
            'articles' => $this->_model->get_recent_articles(500)
        );
        include_once(TEMPLATE_PATH . 'base_template.tpl');
    }
}

?>