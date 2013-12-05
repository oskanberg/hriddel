<?php

class ViewArticleView extends View
{
    private $template = 'view_article.tpl';
    
    public function display()
    {
        $article = $this->_model->get_article_by_id($_GET['a_id']);
        $this->title = $article->title;
        $data = array(
            'title' => $this->title,
            'view_specific_template' => $this->template,
            'article' => $article
        );
        include_once(TEMPLATE_PATH . 'base_template.tpl');
    }
}

?>