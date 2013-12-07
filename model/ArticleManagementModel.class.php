<?php

class ArticleManagementModel extends Model
{
    /**
     * variable to remember whether a submit has been attempted
     * (for the view to query later)
     */
    private $submit_attempted = false;

    /**
     * an GenericArticleMapper object
     */
    private $generic_article_mapper;
    
    /**
     * a UserMapper object
     */
    private $user_mapper;

    /**
     * a comment_mapper object
     */
    private $comment_mapper;


    /**
     * construct ArticleManagementModel: call parent's constructor, create new mappers
     */
    public function __construct()
    {
        parent::__construct();
        $this->generic_article_mapper = new GenericArticleMapper($this->_database_connection);
        $this->user_mapper = new UserMapper($this->_database_connection);
        $this->comment_mapper = new CommentMapper($this->_database_connection);
    }
    

    /**
    * create and save a new article
    * @param string $title the title of the article
    * @param string $content the content of the article
    * @param string $type the type (article, column article, review) of the article
    * @param array(string) $additional_authors the usernames of any additional authors
    * @param string $cover_image a link to the cover image of the article
    * @param string $column_name either null, or the name of the column it belongs to
    * @param integer $review_score either null, or the review score
     */
    public function submit_article($title, $content, $type, $additional_authors, $cover_image, $column_name, $review_score)
    {
        // so we can query it from the view later
        $this->submit_attempted = true;
        
        /* $filename = $this->handle_file_upload();
        if (is_null($filename))
        {
            // image uploading failed.
            // the relevant error(s) will already have been recorded
            return null;
        } */

        $authors = array();
        $authors[] = $this->get_logged_in_user();
        foreach ($additional_authors as $username)
        {
            $authors[] = $this->user_mapper->find_by_id($username);
        }
        $data = array(
            'content' => $content,
            'authors' => $authors,
            'title' => $title,
            'type' => $type,
            'status' => 'submitted',
            'cover_image' => $cover_image,
            'column_name' => $column_name,
            'review_score' => $review_score
        );
        $article = $this->generic_article_mapper->create_new($data);
        $this->generic_article_mapper->save($article);
    }


    /**
    * handle a file upload
    *
    * I had to stop using this due to limitations of the assessment space.
    * Switched to just getting authors to submit links
    * @return null if failed, the new filename(hash) if success
    * 
     */
    private function handle_file_upload()
    {
        if ($_FILES['cover_image']['error'] > 0)
        {
            $this->_record_error($_FILES['cover_image']['error']);
            return null;
        }
        $permitted_types = array(
            'image/gif',
            'image/jpeg',
            'image/jpg',
            'image/pjpeg',
            'image/x-png',
            'image/png'
        );
        if (!in_array($_FILES['cover_image']['type'], $permitted_types))
        {
            $this->_record_error('The supplied image was an unpermitted type (' . $_FILES['cover_image']['type'] . '). Try another.');
            return null;
        }
        $permitted_extensions = array(
            'jpg',
            'jpeg',
            'png',
            'gif'
        );
        $split_name = explode('.', $_FILES['cover_image']['name']);
        $extension = end($split_name);
        if (!in_array($extension, $permitted_extensions))
        {
            $this->_record_error('The supplied image has an unpermitted file extension (' . $extension . ').');
            return null;
        }
        if ($_FILES['cover_image']['size'] > 50000)
        {
            $this->_record_error('The supplied image was too large. Please upload a smaller image.');
            return null;
        }
        $file_hash = hash_file('sha1', $_FILES['cover_image']['tmp_name']);
        $new_filename = $file_hash . '.' . $extension;
        move_uploaded_file($_FILES['cover_image']['tmp_name'], ROOTPATH . '/images/' . $new_filename);
        return $new_filename;
    }

    /**
    * check whether the controller has previously asked the model to do anything
    * during this request
    * @return boolean whether or not there has been an attempt
     */
    public function has_submit_been_attempted()
    {
        return $this->submit_attempted;
    }
    
    /**
    * return an array of article objects, filtered by what the current
    * user is allowed to see in the article management view
    * @return array(article) array of articles current user may manage
     */
    public function get_articles_array_restricted()
    {
        if ($this->is_user_logged_in())
        {
            $user_type = $this->get_logged_in_user()->type;
            if ($user_type == 'subscriber')
            {
                return array();
            } else if ($user_type == 'writer') {
                // only return articles by the writer
                $all = $this->get_articles_array();
                $mine = array();
                foreach ($all as $article)
                {
                    foreach ($article->authors as $author)
                    {
                        if ($author->username == $this->get_logged_in_username())
                        {
                            $mine[] = $article;
                        }
                    }
                }
                return $mine;
            } else {
                // publishers and editors can see all articles
                return $this->get_articles_array();
            }
        } else {
            return array();
        }
    }

    /**
    * get all articles (content pieces)
    * @return array(article) array of articles
     */
    public function get_articles_array()
    {
        return $this->generic_article_mapper->get_all();
    }

    /**
    * update and article's status
    * @param integer $a_id id of the article to update
    * @param string $new_status the status to update to
    */
    public function update_article_status($a_id, $new_status)
    {
        $article = $this->generic_article_mapper->find_by_id($a_id);
        $article->status = $new_status;
        $this->generic_article_mapper->update($article);
    }

    /**
    * get all the users that could be authors (i.e. writers+)
    * @return array of user objects
    */
    public function get_all_possible_authors()
    {
        $users = $this->user_mapper->get_all();
        $possible_authors = array();
        foreach ($users as $user)
        {
            if ($user->type != 'subscriber')
            {
                $possible_authors[] = $user;
            }
        }
        return $possible_authors;
    }

    /**
    * get an article of any type by its id
    * @param integer $article_id the id of the aricle to get
    * @return article object or null
    */
    public function get_article_by_id($article_id)
    {
        return $this->generic_article_mapper->find_by_id($article_id);
    }

    /**
    * get all the comments for a given article
    * @param integer $article_id the id of the aricle to get comments for
    * @return array of comment objects
    */
    public function get_article_comments($article_id)
    {
        return $this->comment_mapper->find_all_by_article_id($article_id);
    }

    /**
    * add a comment to a given article
    * @param integer $article_id the id of the article to add the comment to 
    * @param string $comment the content of the comment
    */
    public function add_comment($article_id, $comment)
    {
        $data = array(
            'content' => $comment,
            'username' => $this->get_logged_in_username(),
            'a_id' => $article_id
        );
        $comment = $this->comment_mapper->create_new($data);
        $this->comment_mapper->save($comment);
    }

    /**
    * update any of an article's attributes (bar type)
    * @param string $title the title of the article
    * @param string $content the content of the article
    * @param array(string) $authors the usernames of any authors
    * @param string $cover_image a link to the cover image of the article
    * @param string $column_name either null, or the name of the column it belongs to
    * @param integer $review_score either null, or the review score
    */
    public function update_article($title, $content, $authors, $cover_image, $article_id, $review_score, $column_name)
    {
        $this->submit_attempted = true;
        $article = $this->generic_article_mapper->find_by_id($article_id);
        $article->title = $title;
        $article->content = $content;
        $article->cover_image = $cover_image;
        if (!is_null($review_score))
        {
            $article->review_score = $review_score;
        } else if (!is_null($column_name)) {
            $article->column_name = $column_name;
        }
        $this->generic_article_mapper->update($article);
        $this->generic_article_mapper->add_editor($article->get_id(), $this->get_logged_in_user());

        $previous_authors = $article->authors;
        foreach ($authors as $author)
        {
            // check if this author is new or not
            $new = true;
            foreach ($previous_authors as $previous_author)
            {
                if ($previous_author->get_id() == $author)
                {
                    $new = false;
                }
            }
            if ($new)
            {
                $this->generic_article_mapper->add_author($author, $article->get_id());
            }
        }
    }

    /**
    * get the editors of an article
    * @param Article $article the article of which to find the editors
    */
    public function get_article_editors($article)
    {
        return $this->generic_article_mapper->get_article_editors($article->get_id());
    }

    /**
    * highlight an article
    * @param integer $article_id the id of the article to highlight
    */
    public function highlight_article($article_id)
    {
        $article = $this->generic_article_mapper->find_by_id($article_id);
        $this->generic_article_mapper->highlight($article);
    }

    /**
    * get highlighted articles, up to a limit
    * @param integer $limit max number of articles to return
    * @return array(Articles) an array of highlighted articles
    */
    public function get_highlighted_articles($limit)
    {
        return $this->generic_article_mapper->get_recent_highlighted_articles($limit);
    }
    

    /**
    * check whether the user can like and dislike. Was going to be
    * more complicated, but ended up just checking if they are logged in
    * @return boolean whether or not current user can like/dislike
    */
    public function can_user_like_dislike()
    {
        if ($this->is_user_logged_in())
        {
            return true;
        } else {
            return false;
        }
    }

    /**
    * like a given article by a given user
    * @param user the user who likes
    * @param article_id the id of the article the user likes
    */
    public function like_article($user, $article_id)
    {
        $liked_articles = $this->generic_article_mapper->get_all_articles_with_impression_by_user($user, 'like');
        foreach ($liked_articles as $liked_article)
        {
            if ($liked_article->get_id() == $article_id)
            {
                $this->_record_error('you have already liked this article');
                return null;
            }
        }
        $this->generic_article_mapper->store_impression($user, $article_id, 'like');
    }

    /**
    * dislike a given article by a given user
    * @param user the user who dislikes
    * @param article_id the id of the article the user dislikes
    */
    public function dislike_article($user, $article_id)
    {
        $disliked_articles = $this->generic_article_mapper->get_all_articles_with_impression_by_user($user, 'dislike');
        foreach ($disliked_articles as $disliked_article)
        {
            if ($disliked_article->get_id() == $article_id)
            {
                $this->_record_error('you have already disliked this article');
                return null;
            }
        }
        $this->generic_article_mapper->store_impression($user, $article_id, 'dislike');
    }

    /**
    * does what it says on the tin
    * @param article the article to check against
    * @return boolean whether the current user has liked the given article
    */
    public function has_current_user_liked_article($article)
    {
        $liked_articles = $this->generic_article_mapper->get_all_articles_with_impression_by_user($this->get_logged_in_user(), 'like');
        foreach ($liked_articles as $liked_article)
        {
            if ($liked_article->get_id() == $article->get_id())
            {
                return true;
            }
        }
        // none of the likes was on this article
        return false;
    }

    /**
    * does what it says on the tin
    * @param article the article to check against
    * @return boolean whether the current user has disliked the given article
    */
    public function has_current_user_disliked_article($article)
    {
        $disliked_articles = $this->generic_article_mapper->get_all_articles_with_impression_by_user($this->get_logged_in_user(), 'dislike');
        foreach ($disliked_articles as $disliked_article)
        {
            if ($disliked_article->get_id() == $article->get_id())
            {
                return true;
            }
        }
        // none of the dislikes was on this article
        return false;
    }

    /**
    * get an array of the most liked articles
    * @param $limit the max number to return
    * @return array(Article) the most liked articles
    */
    public function get_most_liked($limit)
    {
        return $this->generic_article_mapper->get_most_liked($limit);
    }

    /**
    * get an array of the most recent articles
    * @param $limit the max number to return
    * @return array(Article) the most recent articles
    */
    public function get_recent_articles($limit)
    {
        $article_mapper = new ArticleMapper($this->_database_connection);
        return $article_mapper->get_recent($limit);
    }

    /**
    * get an array of the most recent reviews
    * @param $limit the max number to return
    * @return array(Article) the most recent reviews
    */
    public function get_recent_reviews($limit)
    {
        $review_mapper = new ReviewMapper($this->_database_connection);
        return $review_mapper->get_recent($limit);
    }

    /**
    * get an array of the most recent column articles
    * @param $limit the max number to return
    * @return array(Article) the most recent column articles
    */
    public function get_recent_column_articles($limit)
    {
        $column_article_mapper = new ColumnArticleMapper($this->_database_connection);
        return $column_article_mapper->get_recent($limit);
    }
}

?>