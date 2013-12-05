
<!-- begin articles.tpl -->
<section class="content_box restrict_and_center pad">
    <h3 style="margin: 2px;">All Articles</h3>
    <?php 
    foreach ($data['articles'] as $article)
    {
        echo '<a href="/?view_article&a_id=' . $article->get_id() . '">';
        echo '<div class="fiveply-content-box"';
        echo 'style="background-image:url(' . $article->cover_image . ')">';
        echo '<p>' . $article->title . '</p>';
        echo '</div>';
        echo '</a>';
    }
    ?>
</section>
<!-- end articles.tpl -->
