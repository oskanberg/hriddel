<?php

/**
* Represents an editor's comment on an article (edit_article)
*
*/
class Comment extends AbstractObject
{
    /*
    * content of the comment
    */
    public $content;
    /*
    * username of the editor
    */
    public $username;

    /*
    * id of the article
    */
    public $a_id;

    /*
    * when the comment was made
    */
    public $time;
}

?>