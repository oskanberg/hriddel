<?php

class EditArticleView extends View
{
    private $template = 'edit_article.tpl';
    private $title = 'Edit Article';

    public function display()
    {
        $data = array(
            'title' => $this->title,
            'view_specific_template' => $this->template,
            'show_form' => true,
            'show_error' => false,
            'show_result' => false,
            'show_review_score' => false,
            'show_column_name' => false,

        );
        $data['article'] = $this->_model->get_article_by_id($_GET['a_id']);
        if ($data['article'] instanceof Review) {
            $data['show_review_score'] = true;
        } else if ($data['article'] instanceof ColumnArticle) {
            $data['show_column_name'] = true;
        }
        $data['comments'] = $this->_model->get_article_comments($_GET['a_id']);
        $data['author_possibilities'] = array();
        $current_article_author_usernames = array();
        foreach ($data['article']->authors as $author) {
            $current_article_author_usernames[] = $author->username;
        }
        foreach ($this->_model->get_all_possible_authors() as $author) {
            if (!in_array($author->username, $current_article_author_usernames)) {
                $data['author_possibilities'][] = $author;
            }
        }
        if ($this->_model->has_submit_been_attempted()) {
            if ($this->_model->error_exists()) {
                $data['show_error'] = true;
                $data['error_string'] = '<p class="error">' . $this->_model->get_error_string() . '</p>';
            } else {
                // successful submit
                $data['show_form'] = false;
                $data['show_result'] = true;
                $data['submit_result_text'] = '<p>Article successfully committed.</p>';
            }
        }
        include_once TEMPLATE_PATH . 'base_template.tpl';
    }
}
