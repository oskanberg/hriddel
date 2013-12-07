
<!-- begin index.tpl -->
<section class="content_box restrict_and_center pad">
    <h3 style="margin: 2px;">highlights</h3>
    <?php 
    foreach ($data['highlighted'] as $article)
    {
        echo '<a href="' . HTTP_ROOTPATH . '/?view_article&a_id=' . $article->get_id() . '">';
        echo '<div class="fiveply-content-box"';
        echo 'style="background-image:url(' . $article->cover_image . ')">';
        echo '<p>' . $article->title . '</p>';
        echo '</div>';
        echo '</a>';
    }
    ?>
</section>
<div class="spacer"></div>
<section class="content_box restrict_and_center pad">
    <h3 style="margin: 2px;">most liked</h3>
    <?php
    foreach ($data['liked'] as $article)
    {
        echo '<a href="' . HTTP_ROOTPATH . '/?view_article&a_id=' . $article->get_id() . '">';
        echo '<div class="fiveply-content-box"';
        echo 'style="background-image:url(' . $article->cover_image . ')">';
        echo '<p>' . $article->title . '</p>';
        echo '</div>';
        echo '</a>';
    }
    ?>
</section>
<div class="spacer"></div>
<section class="content_box restrict_and_center pad">
    <h3 style="margin: 2px;">most recent</h3>
    <div class="threeply-content-box">
        <h4>reviews</h4>
        <ul>
        <?php
            foreach ($data['recent_reviews'] as $article)
            {
                echo '<li>';
                echo '<a href="' . HTTP_ROOTPATH . '/?view_article&a_id=' . $article->get_id() . '">' ;
                echo $article->title . '</a></li>';
            }
        ?>
        </ul>
    </div>
    <div class="threeply-content-box">
        <h4>articles</h4>
        <ul>
        <?php
            foreach ($data['recent_articles'] as $article)
            {
                echo '<li>';
                echo '<a href="' . HTTP_ROOTPATH . '/?view_article&a_id=' . $article->get_id() . '">' ;
                echo $article->title . '</a></li>';
            }
        ?>
        </ul>
    </div>
    <div class="threeply-content-box">
        <h4>column articles</h4>
        <ul>
         <?php
            foreach ($data['recent_column_articles'] as $article)
            {
                echo '<li>';
                echo '<a href="' . HTTP_ROOTPATH . '/?view_article&a_id=' . $article->get_id() . '">' ;
                echo $article->title . '</a></li>';
            }
        ?>
        </ul>
    </div>
</section>

<!-- end index.tpl -->
