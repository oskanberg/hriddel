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
            'article' => $article,
            'like_suffix' => 'style="background: rgba(25, 25, 60, .7); width: 200px;"',
            'dislike_suffix' => 'style="background: rgba(25, 25, 60, .7); width: 200px;"'
        );
        if ($this->_model->has_current_user_liked_article($data['article']))
        {
            $data['like_suffix'] = 'style="background: rgba(25, 25, 60, .2); width: 200px;" disabled';
        }
        if ($this->_model->has_current_user_disliked_article($data['article']))
        {
            $data['dislike_suffix'] = 'style="background: rgba(25, 25, 60, .2); width: 200px;" disabled';
        }
        include_once(TEMPLATE_PATH . 'base_template.tpl');
    }
}

?>