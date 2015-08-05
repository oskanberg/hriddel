<?php

class ArticleManagementView extends View
{
    private $template = 'article_management.tpl';
    private $title = 'Manage Articles';

    public function display()
    {
        $data = array(
            'title' => $this->title,
            'view_specific_template' => $this->template,
        );

        $data['articles'] = $this->_model->get_articles_array_restricted();
        include_once TEMPLATE_PATH . 'base_template.tpl';
    }
}
