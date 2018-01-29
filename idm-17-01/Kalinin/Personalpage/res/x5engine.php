<?php

/**
 * This file contains all the classes used by the PHP code created by WebSite X5
 *
 * @category  X5engine
 * @package   X5engine
 * @copyright 2013 - Incomedia Srl
 * @license   Copyright by Incomedia Srl http://incomedia.eu
 * @version   WebSite X5 Professional 10.0.0
 * @link      http://websitex5.com
 */

@session_start();



/**
 * Blog class
 * @access public
 */
class imBlog
{

    var $comments;
    var $postsPerPage = 5;
    var $comPerPage = 10;

    /**
     * Set the default number of posts to show in the home page
     * @param integer $n
     */
    function setPostsPerPage($n) {
        $this->postsPerPage = $n;
    }

    /**
     * Set the number of comments to show in each page
     * @param integer $n
     */
    function setCommentsPerPage($n) {
        $this->comPerPage = $n;
    }

    /**
     * Format a timestamp
     * 
     * @param string $ts The timestamp
     * 
     * @return string
     */
    function formatTimestamp($ts)
    {
        return date("d/m/Y H:i:s", strtotime($ts));
    }

    /**
     * Show the pagination links
     * 
     * @param string  $baseurl  The base url of the pagination
     * @param integer $start    Start from this page
     * @param integer $length   For this length
     * @param integer $count    Count of the current objects to show
     * 
     * @return void
     */
    function paginate($baseurl = "", $start, $length, $count)
    {
        echo "<div style=\"text-align: center;\">";
        if ($start > 0)
            echo "<a href=\"" . $baseurl . "start=" . ($start - $length) . "&length=" . $length . "\" class=\"imCssLink\">" . l10n("blog_pagination_prev", "&lt;&lt; Newer posts") . "</a>";
        if ($start > 0 && $count > $start + $length)
            echo " | ";
        if ($count > $start + $length)
            echo "<a href=\"" . $baseurl . "start=" . ($start + $length) . "&length=" . $length . "\" class=\"imCssLink\">" . l10n("blog_pagination_next", "Older posts &gt;&gt;") . "</a>";
        echo "</div>";
    }

    /**
     * Provide the page title tag to be shown in the header
     * Keep track of the page using the $_GET vars provided:
     *     - id
     *     - category
     *     - tag
     *     - month
     *     - search
     *
     * @param string $basetitle The base title of the blog, to be appended after the specific page title
     * @param string $separator The separator char, default "-"
     * 
     * @return string The page title
     */
    function pageTitle($basetitle, $separator = "-") {
        global $imSettings;

        if (isset($_GET['id']) && isset($imSettings['blog']['posts'][$_GET['id']])) {
            // Post
            return htmlspecialchars($imSettings['blog']['posts'][$_GET['id']]['title'] . $separator . $basetitle);
        } else if (isset($_GET['category']) && isset($imSettings['blog']['posts_cat'][$_GET['category']])) {
            // Category
            return htmlspecialchars(str_replace("_", " ", $_GET['category']) . $separator . $basetitle);
        } else if (isset($_GET['tag'])) {
            // Tag
            return htmlspecialchars(strip_tags($_GET['tag']) . $separator . $basetitle);
        } else if (isset($_GET['month']) && is_numeric($_GET['month']) && strlen($_GET['month']) == 6) {
            // Month
            return htmlspecialchars(substr($_GET['month'], 4, 2) . "/" . substr($_GET['month'], 0, 4) . $separator . $basetitle);
        } else if (isset($_GET['search'])) {
            // Search
            return htmlspecialchars(strip_tags(urldecode($_GET['search'])) . $separator . $basetitle);
        }
        
        // Default (Home page): Show the blog description
        return htmlspecialchars($basetitle);
    }

    /**
     * Get the open graph tags for a post
     * @param  $id   The post id
     * @param  $tabs The tabs (String) to prepend to each tag
     * @return string      The HTML tags
     */
    function getOpengraphTags($id, $tabs = "") {
        global $imSettings;
        $html = "";

        if (!isset($imSettings['blog']['posts'][$id]) || !isset($imSettings['blog']['posts'][$id]['opengraph'])) {
            return $html;
        }
        $og = $imSettings['blog']['posts'][$id]['opengraph'];
        if (isset($og['url'])) $html .= $tabs . '<meta property="og:url" content="' . htmlspecialchars($og['url']) . '" />' . "\n";
        if (isset($og['type'])) $html .= $tabs . '<meta property="og:type" content="' . $og['type'] . '" />' . "\n";
        if (isset($og['title'])) $html .= $tabs . '<meta property="og:title" content="' . htmlspecialchars($og['title']) . '" />' . "\n";
        if (isset($og['description'])) $html .= $tabs . '<meta property="og:description" content="' . htmlspecialchars($og['description']) . '" />' . "\n";
        if (isset($og['updated_time'])) $html .= $tabs . '<meta property="og:updated_time" content="' . htmlspecialchars($og['updated_time']) . '" />' . "\n";
        if (isset($og['video'])) $html .= $tabs . '<meta property="og:video" content="' . htmlspecialchars($og['video']) . '" />' . "\n";
        if (isset($og['video:type'])) $html .= $tabs . '<meta property="og:video:type" content="' . htmlspecialchars($og['video:type']) . '" />' . "\n";
        if (isset($og['audio'])) $html .= $tabs . '<meta property="og:audio" content="' . htmlspecialchars($og['audio']) . '" />' . "\n";
        if (isset($og['images']) && is_array($og['images'])) {
            foreach ($og['images'] as $image) {
                $html .= $tabs . '<meta property="og:image" content="' . htmlspecialchars($image) . '" />' . "\n";
            }
        }
        return $html;
    }

    /**
     * Get the count of valid posts
     * @return integer
     */
    function getPostsCount() {
        global $imSettings;
        $count = 0;
        $utcTime = time();
        foreach ($imSettings['blog']['posts'] as $id => $post) {
            if ($post['utc_time'] <= $utcTime) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get the posts enabled for visualization
     * @return array
     */
    function getPosts() {
        global $imSettings;
        $posts = array();
        $utcTime = time();
        foreach ($imSettings['blog']['posts'] as $id => $post) {
            if ($post['utc_time'] <= $utcTime) {
                $posts[$id] = $post;
            }
        }
        return $posts;
    }

    /**
     * Get the count of valid posts in a category
     * @return integer
     */
    function getCategoryPostCount($category) {
        global $imSettings;
        $bps = isset($imSettings['blog']['posts_cat'][$category]) ? $imSettings['blog']['posts_cat'][$category] : false;
        if (!is_array($bps))
            return 0;
        $count = 0;
        $utcTime = time();
        foreach ($bps as $id) {
            if (!isset($imSettings['blog']['posts'][$id])) continue;
            if ($imSettings['blog']['posts'][$id]['utc_time'] <= $utcTime) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get the posts enabled for visualization in a category
     * @return array
     */
    function getCategoryPosts($category) {
        global $imSettings;
        $bps = isset($imSettings['blog']['posts_cat'][$category]) ? $imSettings['blog']['posts_cat'][$category] : false;
        if (!is_array($bps))
            return 0;
        $posts = array();
        $utcTime = time();
        foreach ($bps as $id) {
            if (!isset($imSettings['blog']['posts'][$id])) continue;
            if ($imSettings['blog']['posts'][$id]['utc_time'] <= $utcTime) {
                $posts[$id] = $imSettings['blog']['posts'][$id];
            }
        }
        return $posts;
    }

    /**
     * Get the count of valid posts in a Tag
     * @return integer
     */
    function getTagPostCount($tag) {
        global $imSettings;
        $count = 0;
        $utcTime = time();
        foreach ($imSettings['blog']['posts'] as $id => $post) {
            if ($post['utc_time'] <= $utcTime && in_array($tag, $post['tag'])) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get the posts enabled for visualization in a tag
     * @return array
     */
    function getTagPosts($tag) {
        global $imSettings;
        $posts = array();
        $utcTime = time();
        foreach ($imSettings['blog']['posts'] as $id => $post) {
            if ($post['utc_time'] <= $utcTime && in_array($tag, $post['tag'])) {
                $posts[$id] = $post;
            }
        }
        return $posts;
    }

    /**
     * Get the posts of a month
     * @param  string $month
     * @return integer
     */
    function getMonthPostsCount($month) {
        global $imSettings;
        $count = 0;
        $utcTime = time();
        if (!isset($imSettings['blog']['posts_month'][$month])) {
            return 0;
        }
        foreach ($imSettings['blog']['posts_month'][$month] as $id) {
            if (isset($imSettings['blog']['posts'][$id]) && $imSettings['blog']['posts'][$id]['utc_time'] < $utcTime) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get the posts of a month
     * @param  string $month
     * @return array
     */
    function getMonthPosts($month) {
        global $imSettings;
        $posts = array();
        $utcTime = time();
        if (!isset($imSettings['blog']['posts_month'][$month])) {
            return array();
        }
        foreach ($imSettings['blog']['posts_month'][$month] as $id) {
            if (isset($imSettings['blog']['posts'][$id]) && $imSettings['blog']['posts'][$id]['utc_time'] < $utcTime) {
                $posts[$id] = $imSettings['blog']['posts'][$id];
            }
        }
        return $posts;
    }

    /**
     * Show the page description to be echoed in the metatag description tag.
     * Keep track of the page using the $_GET vars provided:
     *     - id
     *     - category
     *     - tag
     *     - month
     *     - search
     *
     * @return string The required description
     */
    function pageDescription()
    {
        global $imSettings;
        
        if (isset($_GET['id']) && isset($imSettings['blog']['posts'][$_GET['id']])) {
            // Post
            return htmlspecialchars(str_replace("\n", " ", $imSettings['blog']['posts'][$_GET['id']]['summary']));
        } else if (isset($_GET['category'])) {
            // Category
            return htmlspecialchars(strip_tags($_GET['category']));
        } else if (isset($_GET['tag'])) {
            // Tag
            return htmlspecialchars(strip_tags($_GET['tag']));
        } else if (isset($_GET['month'])) {
            // Month
            return htmlspecialchars(substr($_GET['month'], 4, 2) . "/" . substr($_GET['month'], 0, 4));
        } else if (isset($_GET['search'])) {
            // Search
            return htmlspecialchars(strip_tags(urldecode($_GET['search'])));
        }
        
        // Default (Home page): Show the blog description
        return htmlspecialchars(str_replace("\n", " ", $imSettings['blog']['description']));
    }

    /**
     * Get the last update date
     *
     * @return string
     */
    function getLastModified()
    {
        global $imSettings;
        $c = $this->comments->getComments($_GET['id']);
        if ($_GET['id'] != "" && $c != -1) {
            return $this->formatTimestamp($c[count($c)-1]['timestamp']);
        } else {
            $utcTime = time();
            foreach ($imSettings['blog']['posts'] as $id => $post) {
                if ($post['utc_time'] < $utcTime) {

                }
            }
            $last_post = $imSettings['blog']['posts'];
            $last_post = array_shift($last_post);
            return $last_post['timestamp'];
        }
    }

    /**
     * Show a post
     * 
     * @param string  $id    the post id
     * @param intger  $ext   Set 1 to show as extended
     * @param integer $first Set 1 if this is the first post in the list
     *
     * @return void
     */
    function showPost($id, $ext=0, $first=0)
    {
        global $imSettings;
        
        $bs = $imSettings['blog'];
        $bp = isset($bs['posts'][$id]) ? $bs['posts'][$id] : false;
        $utcTime = time();

        if (is_bool($bp) || $bp['utc_time'] > $utcTime)
            return;


        $title = !$ext ? "<a href=\"?id=" . $id . "\">" . $bp['title'] . "</a>" : $bp['title'];
        echo "<h2 class=\"imPgTitle\" style=\"display: block;\" itemprop=\"headline\">" . $title . "</h2>\n";
        echo "<div class=\"imBreadcrumb\" style=\"display: block;\">";
        echo "<span>" . l10n('blog_published_by') . " <span itemprop=\"author\"><span itemscope itemtype=\"http://schema.org/Person\"><strong itemprop=\"name\">" . $bp['author'] . "</strong></span></span></span> ";
        echo l10n('blog_in') . " <a href=\"?category=" . urlencode($bp['category']) . "\" target=\"_blank\" rel=\"nofollow\"><span itemprop=\"about\">" . $bp['category'] . "</span></a> &middot; <span itemprop=\"datePublished\" content=\"" . $bp['timestamp'] . "\">" . $bp['timestamp'] . "</span>";

        // Media audio/video
        if (isset($bp['media'])) {
            echo " &middot; <a href=\"" . $bp['media'] . "\">Download " . basename($bp['media']) . "</a>";
        }

        if (count($bp['tag']) > 0) {
            echo "<br />Tags: ";
            for ($i = 0; $i < count($bp['tag']); $i++) {
                echo "<a href=\"?tag=" . $bp['tag'][$i] . "\">" . $bp['tag'][$i] . "</a>";
                if ($i < count($bp['tag']) - 1)
                    echo ",&nbsp;";
            }
        }
        echo "</div>\n";

        if ($ext || $first && $imSettings['blog']['post_type'] == 'firstshown' || $imSettings['blog']['post_type'] == 'allshown') {
            echo "<div class=\"imBlogPostBody\" itemprop=\"articleBody\">\n";

            if (isset($bp['mediahtml']) || isset($bp['slideshow'])) {
                // Audio/video
                if (isset($bp['mediahtml'])) {
                    echo $bp['mediahtml'] . "\n";
                }
                // Slideshow
                if (isset($bp['slideshow'])) {
                    echo $bp['slideshow'];
                }
                echo "<div style=\"clear: both; margin-bottom: 10px;\"></div>";
            }
            echo $bp['body'];

            if (count($bp['sources']) > 0) {
                echo "\t<div class=\"imBlogSources\">\n";
                echo "\t\t<b>" . l10n('blog_sources') . "</b>:<br />\n";
                echo "\t\t<ul>\n";

                foreach ($bp['sources'] as $source) {
                    echo "\t\t\t<li>" . $source . "</li>\n";
                }

                echo "\t\t</ul>\n\t</div>\n";
            }
            echo (isset($imSettings['blog']['addThis']) ? "<br />" . $imSettings['blog']['addThis'] : "") . "<br /><br /></div>\n";
        } else {
            echo "<div class=\"imBlogPostSummary\">" . $bp['summary'] . "</div>\n";
        }
        if ($ext == 0) {
            echo "<div class=\"imBlogPostRead\"><a class=\"imCssLink\" href=\"?id=" . $id . "\">" . l10n('blog_read_all') ." &raquo;</a></div>\n";
        } else if (isset($bp['foo_html'])) {
            echo "<div class=\"imBlogPostFooHTML\">" . $bp['foo_html'] . "</div>\n";
        }

        // Schema.org Image
        if (isset($bp['opengraph']['postimage'])) {
            echo "<img src=\"" . $bp['opengraph']['postimage'] . "\" itemprop=\"image\" style=\"display: none\" alt=\"\">";
        }

        if ($ext != 0 && $bp['comments']) {
            if ($imSettings['blog']['comments_source'] == 'wsx5') {
                echo "<div id=\"blog-topic\">\n";
                $this->comments = new ImTopic($imSettings['blog']['file_prefix'] . 'pc' . $id, "../", "index.php?id=" . $id);
                $this->comments->setCommentsPerPage($this->comPerPage);
                // Show the comments
                if ($bs['sendmode'] == "db")
                    $this->comments->loadDb($bs['dbhost'], $bs['dbuser'], $bs['dbpassword'], $bs['dbname'], $bs['dbtable']);
                else
                    $this->comments->loadXML($bs['folder']);
                $this->comments->setPostUrl("index.php?id=" . $id);
                if ($imSettings['blog']['comment_type'] != "stars") {
                    $this->comments->showSummary($bs['comment_type'] != "comment");
                    $this->comments->showComments($bs['comment_type'] != "comment", $bs["comments_order"], $bs["abuse"]);
                    $this->comments->showForm($bs['comment_type'] != "comment", $bs['captcha'], $bs['moderate'], $bs['email'], "blog", $imSettings['general']['url'] . "/admin/blog.php?category=" . str_replace(" ", "_", $imSettings['blog']['posts'][$id]['category']) . "&post=" . $id);
                } else {
                    $this->comments->showRating();
                }
                echo "</div>";
                echo "<script type=\"text/javascript\">x5engine.boot.push('x5engine.topic({ target: \\'#blog-topic\\', scrollbar: false})', false, 6);</script>\n";
            } else {
                echo $imSettings['blog']['comments_code'];
            }
        }
    }

    /**
     * Find the posts tagged with tag
     * 
     * @param string $tag The searched tag
     *
     * @return void
     */
    function showTag($tag)
    {
        global $imSettings;
        $start = isset($_GET['start']) ? max(0, (int)$_GET['start']) : 0;
        $length = isset($_GET['length']) ? (int)$_GET['length'] : $this->postsPerPage;
        $count = $this->getTagPostCount($tag);

        if ($count == 0)
            return;
        $bps = array_values($this->getTagPosts($tag));
        for($i = $start; $i < ($count < $start + $length ? $count : $start + $length); $i++) {
            if ($i > $start)
                echo "<div class=\"imBlogSeparator\"></div>";
            $this->showPost($bps[$i]['id'], 0, ($i == $start ? 1 : 0));
        }
        $this->paginate("?tag=" . $tag . "&", $start, $length, $count);
    }

    /**
     * Find the post in a category
     * 
     * @param strmg $category the category ID
     *
     * @return void
     */
    function showCategory($category)
    {
        global $imSettings;
        $start = isset($_GET['start']) ? max(0, (int)$_GET['start']) : 0;
        $length = isset($_GET['length']) ? (int)$_GET['length'] : $this->postsPerPage;
        $count = $this->getCategoryPostCount($category);

        $bps = array_values($this->getCategoryPosts($category));
        for($i = $start; $i < ($count < $start + $length ? $count : $start + $length); $i++) {
            if ($i > $start)
                echo "<div class=\"imBlogSeparator\"></div>";
            $this->showPost($bps[$i]['id'], 0, ($i == 0 ? 1 : 0));
        }
        $this->paginate("?category=" . $category . "&", $start, $length, $count);
    }

    /**
     * Find the posts of the month
     * 
     * @param string $month The mont
     *
     * @return void
     */
    function showMonth($month)
    {
        global $imSettings;
        $start = isset($_GET['start']) ? max(0, (int)$_GET['start']) : 0;
        $length = isset($_GET['length']) ? (int)$_GET['length'] : $this->postsPerPage;
        $count = $this->getMonthPostsCount($month);

        $bps = array_values($this->getMonthPosts($month));
        for($i = $start; $i < ($count < $start + $length ? $count : $start + $length); $i++) {
            if ($i > $start) {
                echo "<div class=\"imBlogSeparator\"></div>";
            }
            $this->showPost($bps[$i]['id'], 0, ($i == $start ? 1 : 0));
        }
        $this->paginate("?month=" . $month . "&", $start, $length, $count);
    }

    /**
     * Show the last n posts
     * 
     * @param integer $count the number of posts to show
     *
     * @return void
     */
    function showLast($count)
    {
        global $imSettings;
        $start = isset($_GET['start']) ? max(0, (int)$_GET['start']) : 0;
        $length = isset($_GET['length']) ? (int)$_GET['length'] : $this->postsPerPage;

        $bps = array_values($this->getPosts());
        $bpsc = $this->getPostsCount();
        for($i = $start; $i < ($bpsc < $start + $length ? $bpsc : $start + $length); $i++) {
            if ($i > $start) {
                echo "<div class=\"imBlogSeparator\"></div>";
            }
            $this->showPost($bps[$i]['id'], 0, ($i == $start ? 1 : 0));
        }
        $this->paginate("?", $start, $length, $bpsc);
    }

    /**
     * Show the search results
     * 
     * @param string  $search the search query
     *
     * @return void
     */
    function showSearch($search)
    {
        global $imSettings;
        $start = isset($_GET['start']) ? max(0, (int)$_GET['start']) : 0;
        $length = isset($_GET['length']) ? (int)$_GET['length'] : $this->postsPerPage;

        $bps = $this->getPosts();
        $j = 0;
        if (is_array($bps)) {
            $bpsc = count($bps);
            $results = array();
            for ($i = $start; $i < $bpsc; $i++) {
                if (stristr($imSettings['blog']['posts'][$bps[$i]]['title'], $search) || stristr($imSettings['blog']['posts'][$bps[$i]]['summary'], $search) || stristr($imSettings['blog']['posts'][$bps[$i]]['body'], $search)) {
                    $results[] = $bps[$i];
                    $j++;
                }
            }
            for ($i = $start; $i < ($j < $start + $length ? $j : $start + $length); $i++) {
                if ($i > $start)
                    echo "<div class=\"imBlogSeparator\"></div>";
                $this->showPost($bps[$i], 0, ($i == $start ? 1 : 0));
            }
            $this->paginate("?search=" . $search . "&", $start, $length, $j);
            if ($j == 0) {
                echo "<div class=\"imBlogEmpty\">Empty search</div>";
            }
        } else {
            echo "<div class=\"imBlogEmpty\">Empty blog</div>";
        }
    }

    /**
     * Show the categories sideblock
     * 
     * @param integer $n The number of categories to show
     *
     * @return void
     */
    function showBlockCategories($n)
    {
        global $imSettings;

        if (is_array($imSettings['blog']['posts_cat'])) {
            $categories = array();
            foreach ($this->getPosts() as $id => $post) {
                if (!in_array($post['category'], $categories)) {
                    $categories[] = $post['category'];
                }
            }
            sort($categories);
            echo "<ul>";
            for ($i = 0; $i < count($categories) && $i < $n; $i++) {
                echo "<li><a href=\"?category=" . urlencode($categories[$i]) . "\">" . $categories[$i] . "</a></li>";
            }
            echo "</ul>";
        }
    }

    /**
     * Show the cloud sideblock
     * 
     * @param string $type TAGS or CATEGORY
     *
     * @return void;
     */
    function showBlockCloud($type)
    {
        global $imSettings;

        $max = 0;
        $min_em = 0.95;
        $max_em = 1.25;
        if ($type == "tags") {
            $tags = array();
            foreach ($this->getPosts() as $id => $post) {
                foreach ($post['tag'] as $tag) {
                    if (!isset($tags[$tag]))
                        $tags[$tag] = 1;
                    else
                        $tags[$tag] = $tags[$tag] + 1;
                    if ($tags[$tag] > $max)
                        $max = $tags[$tag];
                }
            }
            if (count($tags) == 0)
                return;

            $tags = shuffleAssoc($tags);
            
            foreach ($tags as $name => $number) {
                $size = number_format(($number/$max * ($max_em - $min_em)) + $min_em, 2, '.', '');
                echo "\t\t\t<span class=\"imBlogCloudItem\" style=\"font-size: " . $size . "em;\">\n";
                echo "\t\t\t\t<a href=\"?tag=" . urlencode($name) . "\" style=\"font-size: " . $size . "em;\">" . $name . "</a>\n";
                echo "\t\t\t</span>\n";
            }
        } else if ($type == "categories") {
            $categories = array();
            foreach ($this->getPosts() as $id => $post) {
                if (!isset($categories[$post['category']]))
                    $categories[$post['category']] = 1;
                else
                    $categories[$post['category']] = $categories[$post['category']] + 1;
                if ($categories[$post['category']] > $max)
                    $max = $categories[$post['category']];
            }
            if (count($categories) == 0)
                return;

            $categories = shuffleAssoc($categories);

            foreach ($categories as $name => $number) {
                $size = number_format(($number/$max * ($max_em - $min_em)) + $min_em, 2, '.', '');
                echo "\t\t\t<span class=\"imBlogCloudItem\" style=\"font-size: " . $size . "em;\">\n";
                echo "\t\t\t\t<a href=\"?category=" . urlencode($name) . "\" style=\"font-size: " . $size . "em;\">" . $name . "</a>\n";
                echo "\t\t\t</span>\n";
            }
        }
    }

    /**
     * Show the month sideblock
     * 
     * @param integer $n Number of entries
     *
     * @return void
     */
    function showBlockMonths($n)
    {
        global $imSettings;

        if (is_array($imSettings['blog']['posts_month'])) {
            $months = array();
            foreach ($this->getPosts() as $id => $post) {
                if (!in_array($post['month'], $months)) {
                    $months[] = $post['month'];
                }
            }
            rsort($months);
            echo "<ul>";
            for ($i = 0; $i < count($months) && $i < $n; $i++) {
                echo "<li><a href=\"?month=" . urlencode($months[$i]) . "\">" . (strlen($months[$i]) == 6 ? substr($months[$i], 4, 2) . "/" . substr($months[$i], 0, 4) : $months[$i]) . "</a></li>";
            }
            echo "</ul>";
        }
    }

    /**
     * Show the last posts block
     * 
     * @param integer $n The number of post to show
     *
     * @return void
     */
    function showBlockLast($n)
    {
        global $imSettings;

        $posts = array_values($this->getPosts());
        if (is_array($posts)) {
            echo "<ul>";
            for ($i = 0; $i < count($posts) && $i < $n; $i++) {
                echo "<li><a href=\"?id=" . $posts[$i]['id'] . "\">" . $posts[$i]['title'] . "</a></li>";
            }
            echo "</ul>";
        }
    }
}



/**
 * x5Captcha handling class
 * @access public
 */
class X5Captcha {

    private $nameList;
    private $charList;

    /**
     * Build a new captcha class
     * @param {Array} $nameList
     * @param {Array} $charList
     */
    function __construct($nameList, $charList) {
        $this->nameList = $nameList;
        $this->charList = $charList;
    }

    /**
     * Show the captcha chars
     */
    function show($sCode)
    {
        $text = "<!DOCTYPE HTML>
            <html>
          <head>
          <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">
          <meta http-equiv=\"pragma\" content=\"no-cache\">
          <meta http-equiv=\"cache-control\" content=\"no-cache, must-revalidate\">
          <meta http-equiv=\"expires\" content=\"0\">
          <meta http-equiv=\"last-modified\" content=\"\">
          </head>
          <body style=\"margin: 0; padding: 0; border-collapse: collapse;\">";

        for ($i = 0; $i < strlen($sCode); $i++) {
            $text .= "<img style=\"margin:0; padding:0; border: 0; border-collapse: collapse; width: 24px; height: 24px; position: absolute; top: 0; left: " . (24 * $i) . "px;\" src=\"imcpa_".$this->nameList[substr($sCode, $i, 1)].".gif\" width=\"24\" height=\"24\">";
        }

        $text .= "</body></html>";

        return $text;
    }

    /**
     * Check the sent data
     * @param {String} code The correct code
     * @param {String} ans  The user's answer
     */
    function check($code, $ans)
    {
        if ($ans == "") {
            return '-1';
        }
        for ($i = 0; $i < strlen($code); $i++) {
            if ($this->charList[substr(strtoupper($code), $i, 1)] != substr(strtoupper($ans), $i, 1)) {
                return '-1';
            }
        }
        return '0';
    }
}


/**
 * reCaptcha handling class
 * @access public
 */
class ReCaptcha {

    private $secretKey;

    /**
     * Build a new captcha class
     * @param {String} $secretKey
     */
    function __construct($secretKey) {
        $this->secretKey = $secretKey;
    }

    /**
     * Check the response
     * @param $response The response to be checked
     */
    function check($response)
    {
        // Create the POST data
        $post = "secret=" . urlencode($this->secretKey) . "&response=" . urlencode($response);
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $post .= "&remoteip=" . urldecode($_SERVER['HTTP_X_FORWARDED_FOR']);
        }
        else if (isset($_SERVER['REMOTE_ADDR'])) {
            $post .= "&remoteip=" . urldecode($_SERVER['REMOTE_ADDR']);   
        }

        // Use curl instead of file_get_contents (which can be blocked)
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}



/**
 * Provide support for sending and saving a cart order as well as checking the coupon codes
 */
class ImCart {

    /**
     * Contains the coupon data structured as follows:
     * "cart" => array(
     *        "coupon" => "CARTCOUPON",
     *        "amount" => 10
     *    ),
     *    "products" => array(
     *        "prod1" => array("coupon" => COUPONFORPROD1", "start_time" => 20493437492837, "end_time" => 304923840938),
     *        "prod2" => array("coupon" => COUPONFORPROD2", "start_time" => 20493437492837, "end_time" => 304923840938),
     *        "prod3" => array("coupon" => COUPONFORPROD3", "start_time" => 20493437492837, "end_time" => 304923840938)
     *    )
     *    
     * @var Array
     */
    var $couponData;

    /**
     * Maximum coupon requests every 60 seconds. This is set to prevent the coupon spoofing.
     * Set to 0 to disable the requests limit.
     * 
     * @var integer
     */
    var $couponRequestsPerMinute = 0;

    /**
     * The public folder path used to store the anti-spoofing log to protect the coupon codes
     * 
     * @var string
     */
    var $publicFolder;

    /**
     * Save the order data with the following structure:
     *    "orderNo": "",
     *    "userInvoiceData": {},
     *    "userShippingData": {},
     *    "shipping": {
     *        "name": "",
     *        "description": "",
     *        "email_text": "",
     *        "price": "",
     *        "rawPrice":
     *        "vat":""
     *        "rawVat":
     *    },
     *    "payment": {
     *        "name": "",
     *        "description":"",
     *        "price": "",
     *        "rawPrice":""
     *        "email_text": "",
     *        "vat": "",
     *        "rawVat":""
     *        "html": ""
     *    },
     *    "products": [{
     *        "id" : "",
     *        "name": "",
     *        "description": "",
     *        "option": "",
     *        "suboption": "",
     *        "rawSinglePrice": "",
     *        "rawPrice": "",
     *        "rawFullPrice": "",
     *        "singlePrice": "",
     *        "singleFullPrice": "",
     *        "price": "",
     *        "fullPrice": "",
     *        "quantity": "",
     *        "vat": ""
     *    }],
     *    "rawTotalPrice": "",
     *    "rawTalVat": "",
     *    "totalPrice": "",
     *    "totalVat": "",
     *    "coupon": "",
     *    "currency": ""
     * @var array
     */
    var $orderData;

    /**
     * Database connection
     * 
     * @var boolean
     */
    var $db = false;
    var $table_prefix = "";
    /**
     * True to directly remove the bought dynamic items.
     * False to wait until the order is archived by the user.
     * @var boolean
     */
    var $availabilityDirectCount = true;

    /**
     * Contains the cart settings
     * 
     * @var array
     */
    var $settings = array(
        'orders_table' => 'orders',
        'shipping_addresses_table' => 'shipping_addresses',
        'invoice_addresses_table' => 'invoice_addresses',
        'products_table' => 'products',
        'dynamicproducts_table' => 'dynamicproducts',
        'force_sender' => false,
        'email_opening' => '',
        'email_closing' => '',
        'useCSV' => false,
        'header_bg_color' => '#FFD34F',
        'header_text_color' => '#404040',
        'cell_bg_color' => '#FFFFFF',
        'cell_text_color' => '#000000',
        'availability_reduction_type' => 1, // Default is reducing the availability when the order is set
        'border_color' => '#D3D3D3',
        'owner_email' => '',
        'vat_type' => 'none'
    );

    function setSettings($data)
    {
        // Copy the settings preserving the default data when a key is missing
        foreach ($data as $key => $value) {
            $this->settings[$key] = $value;
        }
    }

    function setOrderData($data)
    {
        // Sanitize the form data
        if (isset($data['userInvoiceData'])) {
            foreach ($data['userInvoiceData'] as $key => $value) {
                if (isset($value['value']) && isset($value['label'])) {
                    $data['userInvoiceData'][$key] = array(
                        "label" => strip_tags($value['label']),
                        "value" => strip_tags($value['value'])
                    );
                }
            }
        }
        if (isset($data['userShippingData'])) {
            foreach ($data['userShippingData'] as $key => $value) {
                if (isset($value['value']) && isset($value['label'])) {
                    $data['userShippingData'][$key] = array(
                        "label" => strip_tags($value['label']),
                        "value" => strip_tags($value['value'])
                    );
                }
            }
        }
        // As some servers escape the double quotes in the HTML code sent via POST,
        // let's check if we have to unescape the payment HTML code using a test HTML sent by JS
        // Do just 3 attempts to avoid unwanted loops
        $i = 3;
        while (isset($data['payment']['htmlCheck']) && $i-- > 0 && $data['payment']['htmlCheck'] != "<a href=\"http://google.it\">escapecheck</a>") {
            $data['payment']['htmlCheck'] = stripcslashes($data['payment']['htmlCheck']);
            $data['payment']['html'] = stripcslashes($data['payment']['html']);
        }
        $this->orderData = $data;
    }

    function setCouponData($data)
    {
        $this->couponData = $data;
    }

    function setPublicFolder($path)
    {
        $this->publicFolder = $path;
    }
   
    /**
     * Check if the current IP can use a coupon code without spoofing
     * 
     * @return boolean
     */
    function canCheckCoupon() {
        $path = "../" . $this->publicFolder . "/nospoof.txt";
        if (!isset($_SERVER['REMOTE_ADDR']) || !@file_exists($path))
            return true;
        foreach (explode("\r\n", @file_get_contents($path)) as $line) {
            $columns = explode("|", $line);
            if (count($columns) == 3 && $columns[0] == $_SERVER['REMOTE_ADDR'] && strtotime($columns[1]) > time() - 60 && $columns[2] * 1 >= $this->couponRequestsPerMinute)
                return false;
        }
        return true;
    }

    /**
     * Save the current user fingerprint. Keeps track of the requests number for each IP address.
     * 
     * @return void
     */
    function saveFingerPrint() {
        if (!isset($_SERVER['REMOTE_ADDR']))
            return;

        $path = "../" . $this->publicFolder . "/nospoof.txt";
        $content = "";
        $old_content = "";
        // Remove the old data from the file to avoid it to become too large
        if (@file_exists($path))
            $old_content = @file_get_contents($path);
        $found = false;
        foreach (explode("\r\n", $old_content) as $line) {
            if (strlen($line) !== 0) {
                $columns = explode("|", $line);
                // If the line contains the current IP, let's check the last request date
                if ($columns[0] == $_SERVER['REMOTE_ADDR']) {
                    if (strtotime($columns[1]) < time() - 60)
                        $columns[2] = 0;
                    $content .= $_SERVER['REMOTE_ADDR'] . "|" . date("Y-m-d H:i:s") . "|" . (($columns[2] * 1) + 1) . "\r\n";
                    $found = true;
                } else if (strtotime($columns[1]) > time() - 60) {
                    // Otherwise, let's save the entry only if it's recent
                    $content .= $columns . "\r\n";
                }
            }
        }
        if (!$found)
            $content .= $_SERVER['REMOTE_ADDR'] . "|" . date('Y-m-d H:i:s') . "|1" . "\r\n";
        @file_put_contents($path,  $content);
    }

    /**
     * Provide the discount data of a coupon code
     * 
     * @param  string $coupon The coupon code
     * 
     * @return string         The data in JSON format
     */
    function checkCoupon($coupon)
    {
        // Avoid spoofing by allowing only 6 tries in 1 minute
        if ($this->couponRequestsPerMinute !== 0) {
            if (!$this->canCheckCoupon())
                return "false";
            $this->saveFingerPrint();
        }

        $coupon = trim($coupon);

        if (!is_array($this->couponData))
            return "false";

        // Check the cart coupon
        if (isset($this->couponData['cart']) && $this->couponData['cart']['coupon'] == $coupon)
            return '{ "type": "cart", "amount": ' . number_format($this->couponData['cart']['amount'], 4, '.', '') . ' }';

        // Check the products coupon
        if (isset($this->couponData['products'])) {
            $products = array();
            foreach ($this->couponData['products'] as $productId => $couponData) {
                $utcTime = time() + date("Z",time());
                if (!isset($couponData['coupon']) || $couponData['coupon'] != $coupon) continue;
                if (isset($couponData['start_time']) && $couponData['start_time'] > $utcTime) continue;
                if (isset($couponData['end_time']) && $couponData['end_time'] < $utcTime) continue;
                $products[] = '"' . $productId . '"';
            }
            if (count($products))
                return '{ "type": "product", "ids": [' . implode(", ", $products) . '] }';    
        }

        // No coupon!
        return "false";
    }

    /**
     * Send the order email
     * 
     * @param boolean $isOwner true to send the owner's email
     * @param string $from from address
     * @param string $to to address
     * 
     * @return boolean
     */
    function sendEmail($isOwner, $from, $to)
    {
        
        global $ImMailer;

        $separationLine = "<tr><td colspan=\"2\" style=\"margin: 10px 0; height: 10px; font-size: 0.1px; border-bottom: 1px solid [email:emailBackground];\">&nbsp;</td></tr>\n";
        $opt = false;
        $vat = 0;
        $userDataTxt = "";
        $userDataHtml = "";
        $userDataCSVH = Array();
        $userDataCSV = Array();
        $shippingDataTxt = "";
        $shippingDataHtml = "";
        $shippingDataCSVH = Array();
        $shippingDataCSV = Array();
        $orderDataTxt = "";
        $orderDataHTML = "";
        $orderDataCSV = "";

        //
        //    Set the invoice data
        //
        if (isset($this->orderData['userInvoiceData']) && is_array($this->orderData['userInvoiceData'])) {
            $i = 0;
            foreach ($this->orderData['userInvoiceData'] as $key => $value) {
                if (trim($value['value']) != "") {
                    // Is it an email?
                    if (preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' . '(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', $value['value'])) {
                        $f = "\n\t\t\t\t<td style=\"[email:contentFontFamily]\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $value['label']) . ":</b></td>\n\t\t\t\t<td style=\"[email:contentFontFamily]\"><a href=\"mailto:" . $value['value'] . "\">". $value['value'] . "</a></td>";
                    } else if (preg_match('/^http[s]?:\/\/[a-zA-Z0-9\.\-]{2,}\.[a-zA-Z]{2,}/', $value['value'])) {
                        // Is it an URL?
                        $f = "\n\t\t\t\t<td style=\"[email:contentFontFamily]\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $value['label']) . ":</b></td>\n\t\t\t\t<td style=\"[email:contentFontFamily]\"><a href=\"" . $value['value'] . "\">". $value['value'] . "</a></td>";
                    } else {
                        $f = "\n\t\t\t\t<td style=\"[email:contentFontFamily]\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $value['label']) . ":</b></td>\n\t\t\t\t<td style=\"[email:contentFontFamily]\">" . str_replace(array("\\'", '\\"'), array("'", '"'), $value['value']) . "</td>";
                    }

                    $userDataTxt .= $value['label'] . ": " . $value['value'] . "\n";
                    $userDataHtml .= "\n\t\t\t<tr" . ($i%2 ? " bgcolor=\"[email:bodyBackgroundOdd]\"" : "") . ">" . $f . "\n\t\t\t</tr>";
                    $userDataCSVH[] = $value['label'];
                    $userDataCSV[] = $value['value'];
                    $i++;
                }
            }
            if ($userDataHtml != "")
                $userDataHtml = "\n\t\t<table width=\"100%\" style=\"[email:contentStyle]\">" . $userDataHtml . "\n\t\t</table>";
        }

        //
        //    Set the shipping data
        //
        if (isset($this->orderData['userShippingData']) && is_array($this->orderData['userShippingData'])) {
            $i = 0;
            foreach ($this->orderData['userShippingData'] as $key => $value) {
                if (trim($value['value']) != "") {
                    // Is it an email?
                    if (preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' . '(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', $value['value'])) {
                        $f = "\n\t\t\t\t<td style=\"[email:contentFontFamily]\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $value['label']) . ":</b></td>\n\t\t\t\t<td style=\"[email:contentFontFamily]\"><a href=\"mailto:" . $value['value'] . "\">". $value['value'] . "</a></td>";
                    } else if (preg_match('/^http[s]?:\/\/[a-zA-Z0-9\.\-]{2,}\.[a-zA-Z]{2,}/', $value['value'])) {
                        // Is it an URL?
                        $f = "\n\t\t\t\t<td style=\"[email:contentFontFamily]\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $value['label']) . ":</b></td>\n\t\t\t\t<td style=\"[email:contentFontFamily]\"><a href=\"" . $value['value'] . "\">". $value['value'] . "</a></td>";
                    } else {
                        $f = "\n\t\t\t\t<td style=\"[email:contentFontFamily]\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $value['label']) . ":</b></td>\n\t\t\t\t<td style=\"[email:contentFontFamily]\">" . str_replace(array("\\'", '\\"'), array("'", '"'), $value['value']) . "</td>";
                    }

                    $shippingDataTxt .= $value['label'] . ": " . $value['value'] . "\n";
                    $shippingDataHtml .= "\n\t\t\t<tr" . ($i%2 ? " bgcolor=\"[email:bodyBackgroundOdd]\"" : "") . ">" . $f . "\n\t\t\t</tr>";
                    $shippingDataCSVH[] = $value['label'];
                    $shippingDataCSV[] = $value['value'];
                    $i++;
                }
            }
            if ($shippingDataHtml != "")
                $shippingDataHtml = "\n\t\t<table width=\"100%\" style=\"[email:contentStyle]\">" . $shippingDataHtml . "\n\t\t</table>";
        }
        $userDataCSV = @implode(";", $userDataCSVH) . "\n" . @implode(";", $userDataCSV);
        $shippingDataCSV = @implode(";", $shippingDataCSVH) . "\n" . @implode(";", $shippingDataCSV);
        
        //
        //    Set the products data
        //
        if (isset($this->orderData['products']) && is_array($this->orderData['products'])) {
            $i = 0;
            foreach ($this->orderData['products'] as $key => $value)
                if (isset($value["option"]) && $value["option"] != "null" && strlen($value["option"]))
                    $opt = true;
            $vat = ($this->settings['vat_type'] != "none");
            $colspan = 3 + ($opt ? 1 : 0) + ($vat ? 1 : 0);
            $orderDataHTML = "<table cellpadding=\"5\" width=\"100%\" style=\"[email:contentStyle] border-collapse: collapse; \">
                                <tr bgcolor=\"[email:bodyBackgroundOdd]\">
                                    <td style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder];\">
                                        <b>" . l10n("cart_name") . "</b>
                                    </td>" .
                                    ($opt ?
                                        "<td style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; min-width: 80px;\">
                                            <b>" . l10n("product_option") . "</b>
                                        </td>" : "") .
                                    "<td style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder];\">
                                        <b>" . l10n("cart_qty") . "</b>
                                    </td>
                                    <td style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; min-width: 70px;\">
                                        <b>" . l10n("cart_price") . "</b>
                                    </td>" .
                                    ($vat ?
                                        "<td style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; min-width: 70px;\">
                                            <b>" . ($this->settings['vat_type'] == "included" ? l10n("cart_vat_included") : l10n("cart_vat")) ."</b>
                                        </td>" : "") .
                                    "<td  style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder];\">
                                        <b>" . l10n("cart_subtot") . "</b>
                                    </td>
                                </tr>\n";

            $orderDataCSV = l10n("cart_name") . ";" . l10n("cart_descr") . ";" . ($opt ? l10n("product_option") . ";" : "") . l10n("cart_qty") . ";" . l10n("cart_price") . ";" . ($vat ? l10n("cart_vat") .";" : "") . l10n("cart_subtot");
            foreach ($this->orderData['products'] as $key => $value) {
                // CSV
                $orderDataCSV .= "\n" 
                . strip_tags(str_replace(array("\n", "\r"), "", $value["name"])) . ";" 
                . strip_tags(str_replace(array("\n", "\r"), "", $value["description"])) . ";" 
                . (($opt && $value["option"] != "null") ? $value["option"] . ( $value["suboption"] != "null" ? " " . $value["suboption"] . ";" : "") : "")
                . $value["quantity"] . ";"
                . ($this->settings['vat_type'] == "excluded" ? $value["singlePrice"] : $value['singlePricePlusVat']) . ";"
                . ($vat ? $value["vat"] .";" : "")
                . ($this->settings['vat_type'] == "excluded" ? $value["price"] : $value['pricePlusVat']);

                // Txt
                $orderDataTxt .= 
                strip_tags(str_replace(array("\n", "\r"), "", $value["name"]))
                . (($opt && $value["option"] != "null") ? " " . $value["option"] . ( $value["suboption"] != "null" ? " " . $value["suboption"] : "") : "")
                . "- " . strip_tags(str_replace(array("\n", "\r"), "", $value["description"])) . "\n "
                . $value["quantity"] . " x " 
                . ( $this->settings['vat_type'] == "excluded" ?
                    "(" . $value["singlePrice"] . " + " . l10n("cart_vat") . " " . $value["vat"] . ")"
                :
                    $value["singlePricePlusVat"]
                )
                . " = " . ($this->settings['vat_type'] == "excluded" ? $value["price"] : $value['pricePlusVat']) . "\n\n";

                // HTML
                $orderDataHTML .= "\n\t\t\t\t<tr valign=\"top\" style=\"[email:contentFontFamily] vertical-align: top\"" . ($i%2 ? " bgcolor=\"#EEEEEE\"" : "") . ">
                                                <td style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder];\">" . $value["name"] . "<br />" . $value["description"] . "</td>" .
                                                ($opt ? "<td style=\"border: 1px solid [email:bodyBackgroundBorder];\">" . (($value["option"] != "null") ? $value["option"] . (($value["suboption"] != "null") ? " " . $value["suboption"] : "") : "") . "</td>" : "") .
                                                "<td style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . $value["quantity"] . "</td>" .
                                                ($this->settings['vat_type'] == "excluded" ? 
                                                    "<td style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . ($value["singlePrice"] == $value['singleFullPrice'] ? $value['singlePrice'] : $value['singlePrice'] . ' <span style="text-decoration: line-through;">' . $value['singleFullPrice'] . "</span>") . "</td>"
                                                :
                                                    "<td style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . ($value["singlePricePlusVat"] == $value['singleFullPricePlusVat'] ? $value['singlePricePlusVat'] : $value['singlePricePlusVat'] . ' <span style="text-decoration: line-through;">' . $value['singleFullPricePlusVat'] . "</span>") . "</td>"
                                                )
                                                 .
                                                ($vat ? "<td style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . $value["vat"] ."</td>" : "") .
                                                "<td style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . ($this->settings['vat_type'] == "excluded" ? $value["price"] : $value['pricePlusVat']) . "</td>
                                            </tr>\n";
                $i++;
            }
        }

        //
        //    Shipping
        //
        if (isset($this->orderData['shipping']) && is_array($this->orderData['shipping'])) {
            $orderDataHTML .= "\n\t\t\t<tr>" . 
                                        ($this->settings['vat_type'] != "none" ?
                                            "<td colspan=\"" . ($colspan - 1) . "\"  style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . l10n('cart_shipping') . ": " . $this->orderData['shipping']['name'] . "</td>" .
                                            "<td  style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . $this->orderData['shipping']['vat'] . "</td>"
                                        :
                                            "<td colspan=\"" . $colspan . "\"  style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . l10n('cart_shipping') . ": " . $this->orderData['shipping']['name'] . "</td>"
                                        )
                                        . "<td  style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . ($this->settings['vat_type'] == "excluded" ? $this->orderData['shipping']['price'] : $this->orderData['shipping']['pricePlusVat']) . "</td>
                                    </tr>";
            $orderDataTxt .= "\n" . l10n('cart_shipping') . " - " . $this->orderData['shipping']['name'] . ": " . ($this->settings['vat_type'] == "excluded" ? $this->orderData['shipping']['price'] : $this->orderData['shipping']['pricePlusVat']);
        }

        //
        //    Payment
        //
        if (isset($this->orderData['payment']) && is_array($this->orderData['payment'])) {
            $orderDataHTML .= "\n\t\t\t<tr>" . 
                                        ($this->settings['vat_type'] != "none" ?
                                            "<td colspan=\"" . ($colspan - 1) . "\"  style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . l10n('cart_payment') . ": " . $this->orderData['payment']['name'] . "</td>" .
                                            "<td  style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . $this->orderData['payment']['vat'] . "</td>"
                                        :
                                            "<td colspan=\"" . $colspan . "\"  style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . l10n('cart_payment') . ": " . $this->orderData['payment']['name'] . "</td>"
                                        )
                                        . "<td  style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . ($this->settings['vat_type'] == "excluded" ? $this->orderData['payment']['price'] : $this->orderData['payment']['pricePlusVat']) . "</td>
                                    </tr>";
            $orderDataTxt .= "\n" . l10n('cart_payment') . " - " . $this->orderData['payment']['name'] . ": " . ($this->settings['vat_type'] == "excluded" ? $this->orderData['payment']['price'] : $this->orderData['payment']['pricePlusVat']);
        }

        //
        //  Coupon
        //
        if (isset($this->orderData['coupon']) && $this->orderData['coupon'] !== "") {
            $orderDataHTML .= "\n\t\t\t<tr>
                                    <td colspan=\"" . $colspan . "\"  style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . l10n('cart_coupon', "Coupon Code") . "</td>
                                    <td style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . $this->orderData['coupon'] . "</td>
                                </tr>";
            $orderDataTxt .= "\n" . l10n('cart_coupon', "Coupon Code")  . ": " . $this->orderData['coupon'];      
        }

        //
        //  Total amounts
        //
        switch ($this->settings['vat_type']) {
            case "excluded":
                $orderDataHTML .= "\n\t\t\t<tr>
                                        <td colspan=\"" . $colspan . "\"  style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right; font-weight: bold;\">" . l10n('cart_total') . "</td>
                                        <td style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . $this->orderData['totalPrice'] . "</td>
                                    </tr>";
                $orderDataHTML .= "\n\t\t\t<tr>
                                        <td colspan=\"" . $colspan . "\"  style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right; font-weight: bold;\">" . l10n('cart_vat') . "</td>
                                        <td style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . $this->orderData['totalVat'] . "</td>
                                    </tr>";
                $orderDataHTML .= "\n\t\t\t<tr>
                                        <td colspan=\"" . $colspan . "\"  style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right; font-weight: bold;\">" . l10n('cart_total_vat') . "</td>
                                        <td style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . $this->orderData['totalPricePlusVat'] . "</td>
                                    </tr>";
                $orderDataTxt .= "\n" . l10n('cart_vat')  . ": " . $this->orderData['totalVat'];
                $orderDataTxt .= "\n" . l10n('cart_total_vat')  . ": " . $this->orderData['totalPricePlusVat'];
            break;
            case "included": 
                $orderDataHTML .= "\n\t\t\t<tr>
                                        <td colspan=\"" . $colspan . "\"  style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right; font-weight: bold;\">" . l10n('cart_total_vat') . "</td>
                                        <td style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . $this->orderData['totalPricePlusVat'] . "</td>
                                    </tr>";
                $orderDataHTML .= "\n\t\t\t<tr>
                                        <td colspan=\"" . $colspan . "\"  style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right; font-weight: bold;\">" . l10n('cart_vat_included') . "</td>
                                        <td style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . $this->orderData['totalVat'] . "</td>
                                    </tr>";
                $orderDataTxt .= "\n" . l10n('cart_total_vat')  . ": " . $this->orderData['totalPricePlusVat'];
                $orderDataTxt .= "\n" . l10n('cart_vat_included')  . ": " . $this->orderData['totalVat'];
            break;
            case "none":
                $orderDataHTML .= "\n\t\t\t<tr>
                                        <td colspan=\"" . $colspan . "\"  style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right; font-weight: bold;\">" . l10n('cart_total_price') . "</td>
                                        <td style=\"[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;\">" . $this->orderData['totalPricePlusVat'] . "</td>
                                    </tr>";
                $orderDataTxt .= "\n" . l10n('cart_total_price')  . ": " . $this->orderData['totalPricePlusVat'];
            break;
        }
        $orderDataHTML .= "</table>";

        $txtMsg = "";
        $htmlMsg = "<table border=0 width=\"100%\" style=\"[email:contentStyle]\">\n";

        //
        // Order number
        // 
        $htmlMsg .= "<tr><td colspan=\"2\" style=\"[email:contentFontFamily] text-align: center; font-weight: bold;font-size: 1.11em\">" . l10n('cart_order_no') . ": " . $this->orderData['orderNo'] . "</td></tr>";
        $txtMsg .= str_replace("<br>", "\n", l10n('cart_order_no') . ": " . $this->orderData['orderNo']);

        //
        //  Opening message
        //
        if (!$isOwner) {
            $htmlMsg .= "\n<tr><td colspan=\"2\">" . $this->settings['email_opening'] . "</td></tr>";
            $txtMsg .= "\n\n" . strip_tags(str_replace("<br />", "\n", $this->settings['email_opening']));
        }

        // Customer's data
        if ($shippingDataHtml != "") {
            $htmlMsg .= "\n<tr style=\"vertical-align: top\" valign=\"top\">\n\t<td style=\"[email:contentFontFamily] width: 50%; padding: 20px 0;\">\n\t\t<h3 style=\"font-size: 1.11em\">" . l10n('cart_vat_address') . "</h3>\n\t\t" . $userDataHtml . "\n\t</td>";
            $htmlMsg .= "\n\t<td style=\"[email:contentFontFamily] width: 50%; padding: 20px 0;\">\n\t\t<h3 style=\"font-size: 1.11em\">" . l10n('cart_shipping_address') . "</h3>\n\t\t" . $shippingDataHtml . "</td>\n\t</tr>";

            $txtMsg .= "\n" . str_replace("<br />", "\n", l10n('cart_vat_address') . "\n" . $userDataTxt);
            $txtMsg .= "\n" . str_replace("<br />", "\n", l10n('cart_shipping_address') . "\n" . $shippingDataTxt);
        } else {
            $htmlMsg .= "\n<tr>\n\t<td colspan=\"2\" style=\"[email:contentFontFamily] padding: 20px 0 0 0;\">\n\t\t<h3 style=\"font-size: 1.11em\">" . l10n('cart_vat_address') . "/" . l10n('cart_shipping_address') . "</h3>\n\t\t" . $userDataHtml . "</td>\n</tr>";
            $txtMsg .= "\n" . str_replace("<br />", "\n", l10n('cart_vat_address') . "/" . l10n('cart_shipping_address') . "\n" . $userDataTxt);
        }

        $htmlMsg .= $separationLine;

        // Products
        $htmlMsg .=  "<tr><td colspan=\"2\" style=\"padding: 5px 0 0 0;\"><h3 style=\"font-size: 1.11em\">" . l10n('cart_product_list') . "</h3>" . $orderDataHTML . "</td></tr>";
        $txtMsg .= "\n\n" . str_replace("<br />", "\n", l10n('cart_product_list') . "\n" . $orderDataTxt);

        $htmlMsg .= $separationLine;

        // Payment
        if (isset($this->orderData['payment']) && is_array($this->orderData['payment'])) {
            $htmlMsg .= "<tr>
                            <td colspan=\"2\" style=\"padding: 5px 0 0 0;\">
                                <h3 style=\"font-size: 1.11em\">" . l10n('cart_payment') . "</h3>" .
                                nl2br($this->orderData['payment']['name']) .( !$isOwner ? '<br />' . nl2br($this->orderData['payment']['email_text']) . ($this->orderData['payment']['html'] != "" ? '<div style="text-align: center; margin-top: 20px;">' . $this->orderData['payment']['html'] . '</div>' : "") : "" ) .
                            "</td>
                        </tr>";
            $txtMsg .= "\n\n" . str_replace("<br />", "\n", l10n('cart_payment') . "\n" . $this->orderData['payment']['name'] . ( !$isOwner ? "\n" . $this->orderData['payment']['email_text'] : "" ));

            $htmlMsg .= $separationLine;
        }

        // Shipping
        if (isset($this->orderData['shipping']) && is_array($this->orderData['shipping'])) {
            $htmlMsg .= "<tr>
                            <td colspan=\"2\" style=\"padding: 5px 0 0 0;\">
                                <h3 style=\"font-size: 1.11em\">" . l10n('cart_shipping') . "</h3>" .
                                nl2br($this->orderData['shipping']['name']) . ( !$isOwner ? '<br />' . nl2br($this->orderData['shipping']['email_text']) : "" ) .
                            "</td>
                        </tr>";
            $txtMsg .= "\n\n" . str_replace("<br />", "\n", l10n('cart_shipping') . "\n" . $this->orderData['shipping']['name'] . ( !$isOwner ? "\n" . $this->orderData['shipping']['email_text'] : "" ));
        }

        //
        //  Closing message
        //
        if (!$isOwner) {
            $htmlMsg .= $separationLine;
            $htmlMsg .= "\n<tr><td colspan=\"2\" style=\"padding: 5px 0 0 0;\">" . $this->settings['email_closing'] . "</td></tr>";
            $txtMsg .= "\n\n" . strip_tags(str_replace("<br />", "\n", $this->settings['email_closing']));
        }
        
        // Close the html table
        $htmlMsg .= "</table>\n";

        $attachments = array();
        if ($this->settings['useCSV'] && $isOwner) {
            $txtMsg .= $userDataCSV . "\n" . $orderDataCSV;
            $attachments[] = array("name" => "user_data.csv", "content" => $userDataCSV, "mime" => "text/csv");
            $attachments[] = array("name" => "order_data.csv", "content" => $orderDataCSV, "mime" => "text/csv");
        }
        return $ImMailer->send($from, $to, l10n('cart_order_no') . " " . $this->orderData['orderNo'], $txtMsg, $htmlMsg, $attachments);
    }

    /**
     * Send the order email to the owner
     * 
     * @return boolean
     */
    function sendOwnerEmail()
    {
        global $imSettings;
        return $this->sendEmail(true, $imSettings['general']['use_common_email_sender_address'] ? $imSettings['general']['common_email_sender_addres'] : $this->settings['owner_email'], $this->settings['owner_email']);
    }

    /**
     * Send the order email to the customer
     * 
     * @return boolean
     */
    function sendCustomerEmail()
    {
        global $imSettings;
        return $this->sendEmail(false, $imSettings['general']['use_common_email_sender_address'] ? $imSettings['general']['common_email_sender_addres'] : $this->settings['owner_email'], $this->orderData['userInvoiceData']['Email']['value']);
    }

    
    /**
     * Set the database connection data
     * 
     * @param String $host        
     * @param String $user        
     * @param String $pwd         
     * @param String $db          
     * @param String $table_prefix
     *
     * @return Boolean
     */
    function setDatabaseConnection($host, $user, $pwd, $db, $table_prefix)
    {
        $this->table_prefix = $table_prefix;
        $this->db = new ImDb($host, $user, $pwd, $db);
        if ($this->db->testConnection())
            return true;
        $this->db = false;
        return false;
    }

    /**
     * Close the database connection
     * 
     * @return void
     */
    function closeDatabaseConnection()
    {
        if ($this->db) {
            $this->db->closeConnection();
        }
    }

    /**
     * Get the ordered products that are not available because of the requested quantity
     * @return Array The products that are not available
     */
    function getOrderUnavailableProducts()
    {
        $return = array();
        if (!$this->db || !$this->db->tableExists($this->table_prefix . $this->settings['dynamicproducts_table'])) {
            return $return;
        }
        $where = array();
        foreach ($this->orderData['products'] as $hash => $product) {
            $where[] = "(`id`='" . $this->db->escapeString($product['id']) . "' AND `quantity`<" . $product['quantity'] . ")";
        }
        $query = "SELECT `id`, `quantity` FROM `" . $this->table_prefix . $this->settings['dynamicproducts_table'] . "` WHERE (" . implode(" OR ", $where) . ")";
        $results = $this->db->query($query);
        if (count($results) > 0) {
            foreach ($results as $dbProduct) {
                foreach ($this->orderData['products'] as $hash => $cartProduct) {
                    if ($cartProduct['id'] == $dbProduct['id'] && $cartProduct['quantity'] > $dbProduct['quantity']) {
                        $return[$cartProduct['id']] = array(
                            "id" => $cartProduct['id'],
                            "name" => $cartProduct['name'],
                            "quantity" => $cartProduct['quantity'],
                            "availableQuantity" => $dbProduct['quantity']
                        );
                    }
                }
            }
        }
        return $return;
    }

    /**
     * Returns true if the order can be set
     * @return Boolean
     */
    function canSetOrder()
    {
        $res = $this->getInvalidProductQuantities();
        return count($res) == 0;
    }

    /**
     * Save the order to DB
     * 
     * @return array
     */
    function saveOrderToDB()
    {
        if (!$this->db)
            return false;
        $this->db->createTable(
            $this->table_prefix . $this->settings['orders_table'],
            array(
                "id" => array('type' => 'VARCHAR(16)', 'primary' => true),
                "ts" => array('type' => 'TIMESTAMP'),
                "ip" => array('type' => 'VARCHAR(16)'),
                "price" => array('type' => 'FLOAT'),
                "vat" => array('type' => 'FLOAT'),
                "price_plus_vat" => array('type' => 'FLOAT'),
                "currency" => array('type' => 'VARCHAR(4)'),
                "shipping_name" => array('type' => 'VARCHAR(32)'),
                "shipping_icon" => array('type' => 'VARCHAR(128)'),
                "shipping_price" => array('type' => 'FLOAT'),
                "shipping_vat" => array('type' => 'FLOAT'),
                "shipping_price_plus_vat" => array('type' => 'FLOAT'),
                "payment_name" => array('type' => 'VARCHAR(32)'),
                "payment_icon" => array('type' => 'VARCHAR(128)'),
                "payment_price" => array('type' => 'FLOAT'),
                "payment_vat" => array('type' => 'FLOAT'),
                "payment_price_plus_vat" => array('type' => 'FLOAT'),
                "coupon" => array("type" => "VARCHAR(32)"),
                "vat_type" => array("type" => "VARCHAR(8)"),
                "availability_reduction_type" => array("type" => "INT(11)"),
                "status" => array("type" => "VARCHAR(16)", "more" => "DEFAULT 'inbox'")
            )
        );
        $this->db->createTable(
            $this->table_prefix . $this->settings['products_table'],
            array(
                "order_id" => array('type' => 'VARCHAR(16)', 'primary' => true),
                "product_id" => array('type' => 'VARCHAR(16)', 'primary' => true),
                "option" => array('type' => 'VARCHAR(32)', 'primary' => true),
                "suboption" => array('type' => 'VARCHAR(32)', 'primary' => true),
                "price" => array('type' => 'FLOAT'),
                "vat" => array('type' => 'FLOAT'),
                "price_plus_vat" => array('type' => 'FLOAT'),
                "quantity" => array('type' => 'INT(11)'),
                "name" => array('type' => 'TEXT')
            )
        );
        $this->db->createTable(
            $this->table_prefix . $this->settings['invoice_addresses_table'],
            array(
                "order_id" => array('type' => 'VARCHAR(16)', 'primary' => true),
                "field_id" => array('type' => 'VARCHAR(64)', 'primary' => true),
                "label" => array('type' => 'VARCHAR(32)'),
                "index" => array('type' => 'INT(11)'),
                "value" => array('type' => 'TEXT')
            )
        );
        $this->db->createTable(
            $this->table_prefix . $this->settings['shipping_addresses_table'],
            array(
                "order_id" => array('type' => 'VARCHAR(16)', 'primary' => true),
                "field_id" => array('type' => 'VARCHAR(64)', 'primary' => true),
                "label" => array('type' => 'VARCHAR(32)'),
                "index" => array('type' => 'INT(11)'),
                "value" => array('type' => 'TEXT')
            )
        );

        // If the dynamic products are set, check their quantity and make sure the order can be set
        $prods = $this->getOrderUnavailableProducts();
        if (count($prods) > 0) {
            return array(
                "status" => "error",
                "errorType" => "invalid_product_quantity",
                "productsData" => $prods
            );
        }

        // Check if the current order number already exists
        do {
            $res = $this->db->query("SELECT id FROM `" . $this->table_prefix . $this->settings['orders_table'] . "` WHERE id='" . $this->db->escapeString($this->orderData['orderNo']) ."'");
            if (count($res)) {
                $this->orderData['orderNo'] .= rand(0, 9);
            }
        } while (count($res));

        // Save the order data
        $this->db->query(
            "INSERT INTO `" . $this->table_prefix . $this->settings['orders_table'] . "` "
            . "(
                `id`,
                `ts`,
                `ip`,
                `vat_type`,
                `price`,
                `vat`,
                `price_plus_vat`,
                `currency`,
                `shipping_name`,
                `shipping_icon`,
                `shipping_price`,
                `shipping_vat`,
                `shipping_price_plus_vat`,
                `payment_name`,
                `payment_icon`,
                `payment_price`,
                `payment_vat`,
                `payment_price_plus_vat`,
                `coupon`,
                `availability_reduction_type`
            ) VALUES("
            . "'" . $this->db->escapeString($this->orderData['orderNo']) . "',
            '" . date("Y-m-d H:i:s") . "',
            '" . $_SERVER['REMOTE_ADDR'] . "',
            '" . $this->settings['vat_type'] . "',
            " . $this->orderData['rawTotalPrice'] . ",
            " . $this->orderData['rawTotalVat'] . ",
            " . $this->orderData['rawTotalPricePlusVat'] . ",
            '" . $this->orderData['currency'] . "',
            '" . (isset($this->orderData['shipping']) ? $this->db->escapeString($this->orderData['shipping']['name']) : ''). "',
            '" . (isset($this->orderData['shipping']) ? $this->db->escapeString($this->orderData['shipping']['icon']) : ''). "',
            " . (isset($this->orderData['shipping']) ? $this->orderData['shipping']['rawPrice'] : 0) . ",
            " . (isset($this->orderData['shipping']) ? $this->orderData['shipping']['rawVat'] : 0) . ",
            " . (isset($this->orderData['shipping']) ? $this->orderData['shipping']['rawPricePlusVat'] : 0) . ",
            '" . (isset($this->orderData['payment']) ? $this->db->escapeString($this->orderData['payment']['name']) : '') . "',
            '" . (isset($this->orderData['payment']) ? $this->db->escapeString($this->orderData['payment']['icon']) : '') . "',
            " . (isset($this->orderData['payment']) ? $this->orderData['payment']['rawPrice'] : 0) . ",
            " . (isset($this->orderData['payment']) ? $this->orderData['payment']['rawVat'] : 0). ",
            " . (isset($this->orderData['payment']) ? $this->orderData['payment']['rawPricePlusVat'] : 0) . ",            
            '" . (isset($this->orderData['coupon']) ? $this->db->escapeString($this->orderData['coupon']) : '') . "',
            " . $this->settings['availability_reduction_type'] . "
            )"
        );

        // Save the products
        if (isset($this->orderData['products']) && is_array($this->orderData['products'])) {
            $qdata = array();
            $query = "INSERT INTO `" . $this->table_prefix . $this->settings['products_table']. "` (
                    `order_id`,
                    `product_id`,
                    `option`,
                    `suboption`,
                    `price`,
                    `vat`,
                    `price_plus_vat`,
                    `quantity`,
                    `name`
                ) VALUES";
            foreach ($this->orderData['products'] as $key => $product) {
                $qdata[] = "(
                    '" . $this->db->escapeString($this->orderData['orderNo']) . "',
                    '" . $this->db->escapeString($product['id']) . "',
                    '" . $this->db->escapeString(isset($product['option']) ? $product['option'] : '') . "',
                    '" . $this->db->escapeString(isset($product['suboption']) ? $product['suboption'] : '') . "',
                    " . $product['rawPrice'] . ",
                    " . $product['rawVat'] . ",
                    " . $product['rawPricePlusVat'] . ",
                    " . $product['quantity'] . ",
                    '" . $this->db->escapeString($product['name']) . "'
                )";
            }
            $query .= implode(",", $qdata);
            $this->db->query($query);
        }

        // Save the invoice data
        if (isset($this->orderData['userInvoiceData']) && is_array($this->orderData['userInvoiceData'])) {
            $qdata = array();
            $index = 0;
            $query = "INSERT INTO `" . $this->table_prefix . $this->settings['invoice_addresses_table'] . "` (
                `order_id`,
                `field_id`,
                `label`,
                `index`,
                `value`
            ) VALUES";
            foreach ($this->orderData['userInvoiceData'] as $key => $field) {
                if ($field['value'] != "") {
                    $qdata[] = "(
                        '" . $this->db->escapeString($this->orderData['orderNo']) . "',
                        '" . $this->db->escapeString($key) . "',
                        '" . $this->db->escapeString($field['label']) . "',
                        '" . $index++ . "',
                        '" . $this->db->escapeString($field['value']) . "'
                    )";
                }
            }
            $query .= implode(",", $qdata);
            $this->db->query($query);
        }

        // Save the shipping data
        if (isset($this->orderData['userShippingData']) && is_array($this->orderData['userShippingData'])) {
            $qdata = array();
            $index = 0;
            $query = "INSERT INTO `" . $this->table_prefix . $this->settings['shipping_addresses_table'] . "` (
                `order_id`,
                `field_id`,
                `label`,
                `index`,
                `value`
            ) VALUES";
            foreach ($this->orderData['userShippingData'] as $key => $field) {
                if ($field['value'] != "") {
                    $qdata[] = "(
                        '" . $this->db->escapeString($this->orderData['orderNo']) . "',
                        '" . $this->db->escapeString($key) . "',
                        '" . $this->db->escapeString($field['label']) . "',
                        '" . $index++ . "',
                        '" . $this->db->escapeString($field['value']) . "'
                    )";
                }
            }
            $query .= implode(",", $qdata);
            $this->db->query($query);
        }

        // If the dynamic products availability reduction must be done now, search for dynamic products
        if ($this->settings['availability_reduction_type'] == 1) {
            foreach ($this->orderData['products'] as $key => $product) {
                if ($this->isDynamicProduct($product['id'])) {
                    $this->addDynamicProductItems($product['id'], -$product['quantity']);
                }
            }
        }

        return array(
            "status" => "ok",
            "orderNumber" => $this->orderData['orderNo']
        );
    }

    /**
     * Delete an order from the DB
     * 
     * @param  String $id
     * 
     * @return void              
     */
    function deleteOrderFromDb($id)
    {
        if (!$this->db) {
            return;
        }
        $id = $this->db->escapeString($id);
        $order = $this->db->query("SELECT * FROM `" . $this->table_prefix . $this->settings['orders_table'] . "` WHERE `id`='" . $id . "'");
        // If the order is not evaded and not int waiting and the quantity was not reduced, restore it!
        if ($order && count($order) && $order[0]['status'] != 'waiting' && $order[0]['availability_reduction_type'] == 1) {
            $products = $this->db->query("SELECT * FROM `" . $this->table_prefix . $this->settings['products_table'] . "` WHERE order_id='" . $id . "'");
            for ($i = 0; $products && $i < count($products); $i++) { 
                if ($this->isDynamicProduct($products[$i]['product_id'])) {
                    $this->addDynamicProductItems($products[$i]['product_id'], $products[$i]['quantity']);
                }
            }
        }
        // Now delete all the data about this order
        $this->db->query("DELETE FROM `" . $this->table_prefix . $this->settings['orders_table'] . "` WHERE `id`='" . $id . "'" );
        $this->db->query("DELETE FROM `" . $this->table_prefix . $this->settings['invoice_addresses_table'] . "` WHERE `order_id`='" . $id . "'" );
        $this->db->query("DELETE FROM `" . $this->table_prefix . $this->settings['shipping_addresses_table'] . "` WHERE `order_id`='" . $id . "'" );
        $this->db->query("DELETE FROM `" . $this->table_prefix . $this->settings['products_table'] . "` WHERE `order_id`='" . $id . "'" );
    }

    /**
     * Get a list of the orders in the DB
     *
     * @param Number $pagination_start
     * @param Number $pagination_length
     * @param String $filter Filter the result matching this string
     * @param String $status If defined, get only the orders with the given status
     * 
     * @return array
     */
    function getOrders($pagination_start, $pagination_length, $filter = "", $status = "")
    {
        $result = array(
            "orders" => array(),
            "paginationCount" => 0
        );
        if (!$this->db) {
            return $result;
        }
        // Search for specific orders
        $ids = array();
        if (strlen($filter)) {
            // Search in the customer's data
            $results = $this->db->query(
                "SELECT order_id FROM `" . $this->table_prefix . $this->settings['invoice_addresses_table'] . "`" .
                " WHERE value LIKE '%" . $this->db->escapeString($filter) . "%'"
            );
            if ($results) {
                foreach ($results as $order) {
                    $ids[] = $order['order_id'];
                }
            }
            // Search in the orders's data
            $results = $this->db->query(
                "SELECT id FROM `" . $this->table_prefix . $this->settings['orders_table'] . "`" .
                " WHERE `id` LIKE '%" . $this->db->escapeString($filter) . "%'"
            );
            if ($results) {
                foreach ($results as $order) {
                    $ids[] = $order['id'];
                }
            }
            $ids = array_unique($ids);
            if (count($ids) > 0) {
                $results = $this->db->query(
                    "SELECT * FROM `" . $this->table_prefix . $this->settings['orders_table'] .
                    "` WHERE id IN ('" . implode("','", $ids) . "') " .
                    ($status != "" ? "AND `status`='" . $this->db->escapeString($status) . "' " : "") .
                    "ORDER BY `ts` DESC LIMIT " . $pagination_start . ", " . $pagination_length
                );
                if (!is_bool($results)) {
                    $result['orders'] = $results;
                    // Set the pagination maximum length
                    $ordersCount = $this->db->query("SELECT COUNT(*) AS c FROM `" . $this->table_prefix . $this->settings['orders_table'] . "` WHERE id IN ('" . implode("','", $ids) . "')");
                    $result['paginationCount'] = $ordersCount[0]['c'];
                }
            }
        } else {
            $results = $this->db->query(
                "SELECT * FROM `" . $this->table_prefix . $this->settings['orders_table'] . "` " .
                ($status != "" ? "WHERE `status`='" . $this->db->escapeString($status) . "' " : "") .
                "ORDER BY `ts` DESC LIMIT " . $pagination_start . ", " . $pagination_length
            );
            if (!is_bool($results)) {
                $result['orders'] = $results;
                // Set the pagination maximum length
                $ordersCount = $this->db->query("SELECT COUNT(*) AS c FROM `" . $this->table_prefix . $this->settings['orders_table'] . "`");
                $result['paginationCount'] = $ordersCount[0]['c'];
                for ($i=0; $i < count($results); $i++) { 
                    $ids[] = $results[$i]['id'];
                }
            }
        }
        // Populate the orders with the invoice addresses
        $fields = array();
        $fieldresults = $this->db->query("SELECT * FROM `" . $this->table_prefix . $this->settings['invoice_addresses_table'] . "` WHERE `order_id` IN ('" . implode("','", $ids) . "') ORDER BY `order_id`, `index`");
        for ($i = 0; $i < count($fieldresults); $i++) {
            if (!isset($fields[$fieldresults[$i]['order_id']])) {
                $fields[$fieldresults[$i]['order_id']] = array();
            }
            $fields[$fieldresults[$i]['order_id']][] = $fieldresults[$i];
        }
        for ($i=0; $i < count($result['orders']); $i++) { 
            $result['orders'][$i]['invoice'] = array();
            foreach ($fields as $key => $value) {
                if ($key == $result['orders'][$i]['id']) {
                    $result['orders'][$i]['invoice'] = $value;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * Get an order
     * 
     * @param  String $id The order ID
     * 
     * @return Array      The order data
     */
    function getOrder($id)
    {
        $result = array();
        $order = $this->db->query("SELECT * FROM `" . $this->table_prefix . $this->settings['orders_table'] . "` WHERE id='" . $this->db->escapeString($id) . "'");
        if (count($order)) {
            $result['order'] = $order[0];
            $result['products'] = $this->db->query("SELECT * FROM `" . $this->table_prefix . $this->settings['products_table'] . "` WHERE order_id='" . $this->db->escapeString($id) . "'");
            $result['invoice']= $this->db->query("SELECT * FROM `" . $this->table_prefix . $this->settings['invoice_addresses_table'] . "` WHERE order_id='" . $this->db->escapeString($id) . "' ORDER BY `index`");
            $result['shipping'] = $this->db->query("SELECT * FROM `" . $this->table_prefix . $this->settings['shipping_addresses_table'] . "` WHERE order_id='" . $this->db->escapeString($id) . "' ORDER BY `index`");
        }
        return $result;
    }

    /**
     * Get the amount of sold items for each month, for each year
     * 
     * @return Array A structured container of the data
     */
    function getNonCumulativeSellings()
    {
        $results = array();
        if ($this->db) {
            $table = $this->table_prefix . $this->settings['orders_table'];
            $query = $this->db->query("SELECT YEAR(`ts`) as `year`, MONTH(`ts`) as `month`, SUM(`price_plus_vat`) as `amount` FROM `" . $table . "` WHERE `status`='evaded' GROUP BY YEAR(`ts`), MONTH(`ts`) ORDER BY `year` DESC, `month` ASC");
            if ($query) {
                // Set the data
                foreach ($query as $queryRow) {
                    if (!isset($results["" . $queryRow['year']])) {
                        $results["" . $queryRow['year']] = array();
                    }
                    $results["" . $queryRow['year']]["" . $queryRow['month']] = $queryRow['amount'];
                }
                // Fill the empty months
                foreach ($results as $year => $data) {
                    for ($i = 1; $i <= 12 && !($year == date("Y") && $i."" == date("n")); $i++) {
                        if (!isset($results[$year]["" . $i])) {
                            $results[$year]["" . $i] = 0;
                        }
                    }
                }
            }
        }
        return $results;
    }

    /**
     * Get the cumulative amount of sold items for each month, for each year
     * 
     * @return Array A structured container of the data
     */
    function getCumulativeSellings()
    {
        $results = array();
        if ($this->db) {
            $table = $this->table_prefix . $this->settings['orders_table'];
            $query = $this->db->query("SELECT YEAR(`ts`) as `year`, MONTH(`ts`) as `month`, SUM(`price_plus_vat`) as `amount` FROM `" . $table . "` WHERE `status`='evaded' GROUP BY YEAR(`ts`), MONTH(`ts`) ORDER BY `year` DESC, `month` ASC");
            if ($query) {
                $amountCounter = 0;
                // Set the data
                foreach ($query as $queryRow) {
                    if (!isset($results[$queryRow['year']])) {
                        $amountCounter = 0;
                    }
                    $amountCounter += $queryRow['amount'];
                    $results[$queryRow['year']]["" . $queryRow['month']] = $amountCounter;
                }
                // Fill the empty months
                foreach ($results as $year => $data) {
                    $lastValue = 0;
                    for ($i = 1; $i <= 12 && !($year == date("Y") && $i."" == date("n")); $i++) {
                        if (!isset($results[$year]["" . $i])) {
                            $results[$year]["" . $i] = $lastValue;
                        } else {
                            $lastValue = $results[$year]["" . $i];
                        }
                    }
                }
            }
        }
        return $results;
    }

    /**
     * Get the number of sold items for each item in the database, ordered by number of items 
     * sold, descending.
     * 
     * @param  Number $number   The number of items to show before falling into the "other" category
     * 
     * @return Array            An associative array with the required data
     */
    function getSoldItemsNumber($number)
    {
        $results = array();
        if ($this->db) {
            $ptable = $this->table_prefix . $this->settings['products_table'];
            $otable = $this->table_prefix . $this->settings['orders_table'];
            $query = $this->db->query("SELECT `name`, SUM(`quantity`) as `count` FROM `" . $ptable . "` as p JOIN `" . $otable . "` as o ON p.order_id=o.id WHERE o.status='evaded' GROUP BY `product_id`, `name` ORDER BY `count` DESC");
            if ($query) {
                $count = 0;
                foreach ($query as $queryRow) {
                    if ($count++ < $number) {
                        $results[$queryRow['name']] = $queryRow['count'];
                    } else if (!isset($results['other'])) {
                        $results['other'] = $queryRow['count'];
                    } else {
                        $results['other'] += $queryRow['count'];
                    }
                }
            }
        }
        return $results;
    }

    /**
     * Get the CSV data of the products
     * @param  String $id The order id
     * @return String     The CSV data
     */
    function getProductsCSV($id)
    {
        $products = $this->getOrder($id);
        $products = $products['products'];

        // Check if the options were set
        $opt = false;
        for ($i = 0; $i < count($products); $i++) {
            if (strlen($products[$i]["option"])) {
                $opt = true;
                break;
            }
        }
        $orderDataCSV = l10n("cart_name") . ";" . ($opt ? l10n("product_option") . ";" : "") . l10n("cart_qty") . ";" . l10n("cart_price") . ";" . l10n("cart_vat") . ";" . l10n("cart_subtot");
        for ($i = 0; $i < count($products); $i++) {
            // CSV
            $orderDataCSV .= "\n" 
            . strip_tags(str_replace(array("\n", "\r"), "", $products[$i]["name"])) . ";"
            . ($opt ? $products[$i]["option"] . $products[$i]["suboption"] . ";" : "")
            . $products[$i]["quantity"] . ";"
            . (($this->settings['vat_type'] == "excluded" ? $products[$i]["price"] : $products[$i]['price_plus_vat']) / $products[$i]['quantity']) . ";"
            . ($products[$i]["vat"] / $products[$i]['quantity']). ";"
            . ($this->settings['vat_type'] == "excluded" ? $products[$i]["price"] : $products[$i]['price_plus_vat']);
        }
        return $orderDataCSV;
    }

    /**
     * Get the CSV data of the invoice
     * 
     * @param  String $id The order id
     * 
     * @return String     The CSV data
     */
    function getInvoiceDataCSV($id)
    {
        $order = $this->getOrder($id);
        $invoice = $order['invoice'];

        for ($i=0; $i < count($invoice); $i++) { 
            if (trim($invoice[$i]['value']) != "") {
                $userDataCSVH[] = $invoice[$i]['label'];
                $userDataCSV[] = $invoice[$i]['value'];
            }
        }
        return implode(";", $userDataCSVH) . "\n" . implode(";", $userDataCSV);
    }

    /**
     * Get the CSV data of the shipping
     * 
     * @param  String $id The order id
     * 
     * @return String     The CSV data
     */
    function getShippingDataCSV($id)
    {
        $order = $this->getOrder($id);
        $shipping = $order['shipping'];
        if (!count($shipping)) {
            return "";
        }

        for ($i=0; $i < count($shipping); $i++) { 
            if (trim($shipping[$i]['value']) != "") {
                $userDataCSVH[] = $shipping[$i]['label'];
                $userDataCSV[] = $shipping[$i]['value'];
            }
        }
        return implode(";", $userDataCSVH) . "\n" . implode(";", $userDataCSV);
    }

    /**
     * Returns true if this server supports ZipArchive and so can export the zip files
     * 
     * @param  String $pathToRoot The path to root with a trailing slash
     * 
     * @return Boolean
     */
    function canExportZip($pathToRoot)
    {
        global $imSettings;
        $test = new ImTest();
        return class_exists("ZipArchive") && $test->writable_folder_test(pathCombine(array($pathToRoot, $imSettings['general']['public_folder'])));
    }

    /**
     * Zip the CSV files of an order into a file and get the zip path
     * 
     * @param  String $id           The order id
     * @param  String $pathToRoot   The path to root with a trailing slash
     * 
     * @return Mixed                The zip path or false on error
     */
    function zipOrder($id, $pathToRoot)
    {
        global $imSettings;
        if (!$this->canExportZip($pathToRoot)) {
            return false;
        }
        $path = pathCombine(array($pathToRoot, $imSettings['general']['public_folder'], str_replace(" ", "_", $id) . '.tmp'));
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::CREATE);
        $zip->addFromString("invoicedata.csv", $this->getInvoiceDataCSV($id));
        $shippingCsv =  $this->getShippingDataCSV($id);
        if (strlen($shippingCsv)) {
            $zip->addFromString("shippingdata.csv", $shippingCsv);
        }
        $zip->addFromString("products.csv", $this->getProductsCSV($id));
        $zip->close();
        return $path;
    }

    /**
     * Remove the temporary files created by exporting the zip order data
     * 
     * @return void
     */
    function deleteTemporaryFiles($pathToRoot)
    {
        global $imSettings;
        $path = pathCombine(array($pathToRoot, $imSettings['general']['public_folder']));
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                if ((time()-filectime($path.'/'.$file)) > 600) {  // Ten minutes cache
                    if (preg_match('/\.tmp$/i', $file)) {
                        unlink($path.'/'.$file);
                    }
                }
            }
        }
    }

    /**
     * Move the order to the inbox
     * 
     * @param  String $id The order id
     * 
     * @return Void
     */
    function moveOrderToInbox($id) {
        // Check the order status
        $result = $this->db->query("SELECT `status`, `availability_reduction_type` FROM `" . $this->table_prefix . $this->settings['orders_table'] . "` WHERE id='" . $this->db->escapeString($id) . "'");
        if (!$result || !count($result) || $result[0]['status'] != "waiting") { // You can move to the inbox only the waiting orders
            return;
        }
        // Update the order status
        $this->db->query("UPDATE `" . $this->table_prefix . $this->settings['orders_table'] . "` SET `status`='inbox', `ts`=`ts` WHERE `id`='" . $this->db->escapeString($id) . "'");
        // If the availability reduction type is immediate, update the products quantity
        if ($result[0]['availability_reduction_type'] == 1) {
            $products = $this->db->query("SELECT * FROM `" . $this->table_prefix . $this->settings['products_table'] . "` WHERE `order_id`='" . $this->db->escapeString($id) . "'");
            if (!$products || !count($products)) {
                return;
            }
            for ($i = 0; $i < count($products); $i++) { 
                if ($this->isDynamicProduct($products[$i]['product_id'])) {
                    $this->addDynamicProductItems($products[$i]['product_id'], -$products[$i]['quantity']);
                }
            }
        }
    }

    /**
     * Move the order to the waiting list
     * 
     * @param  String $id The order id
     * 
     * @return Void
     */
    function moveOrderToWaiting($id) {
        // Check the order status
        $result = $this->db->query("SELECT `status`, `availability_reduction_type` FROM `" . $this->table_prefix . $this->settings['orders_table'] . "` WHERE id='" . $this->db->escapeString($id) . "'");
        if (!$result || !count($result) || $result[0]['status'] != "inbox") { // You can put to wait only inbox orders
            return;
        }
        // Update the order status
        $this->db->query("UPDATE `" . $this->table_prefix . $this->settings['orders_table'] . "` SET `status`='waiting', `ts`=`ts` WHERE `id`='" . $this->db->escapeString($id) . "'");
        // If the availability reduction type is immediate, update the products quantity
        if ($result[0]['availability_reduction_type'] == 1) {
            $products = $this->db->query("SELECT * FROM `" . $this->table_prefix . $this->settings['products_table'] . "` WHERE `order_id`='" . $this->db->escapeString($id) . "'");
            if (!$products || !count($products)) {
                return;
            }
            for ($i = 0; $i < count($products); $i++) { 
                if ($this->isDynamicProduct($products[$i]['product_id'])) {
                    $this->addDynamicProductItems($products[$i]['product_id'], $products[$i]['quantity']);
                }
            }
        }
    }

    /**
     * Evade the order
     * 
     * @param  String $id The order id
     * 
     * @return Void
     */
    function evadeOrder($id) {
        // Check the order status
        $result = $this->db->query("SELECT `status`, `availability_reduction_type` FROM `" . $this->table_prefix . $this->settings['orders_table'] . "` WHERE id='" . $this->db->escapeString($id) . "'");
        if (!$result || !count($result) || $result[0]['status'] != "inbox") { // Allow only inbox orders to be evaded
            return;
        }
        // Update the order status
        $this->db->query("UPDATE `" . $this->table_prefix . $this->settings['orders_table'] . "` SET `status`='evaded', `ts`=`ts` WHERE `id`='" . $this->db->escapeString($id) . "'");
        // If the availability reduction type is postponed, update the products quantity
        if ($result[0]['availability_reduction_type'] == 2) {
            $products = $this->db->query("SELECT * FROM `" . $this->table_prefix . $this->settings['products_table'] . "` WHERE `order_id`='" . $this->db->escapeString($id) . "'");
            if (!$products || !count($products)) {
                return;
            }
            for ($i = 0; $i < count($products); $i++) { 
                if ($this->isDynamicProduct($products[$i]['product_id'])) {
                    $this->addDynamicProductItems($products[$i]['product_id'], -$products[$i]['quantity']);
                }
            }
        }
    }

    /**
     * Remove an order from the evaded ones and move it to the inbox
     * 
     * @param  String $id The order id
     * 
     * @return Void
     */
    function unevadeOrder($id) {
        // Check the order status
        $result = $this->db->query("SELECT `status`, `availability_reduction_type` FROM `" . $this->table_prefix . $this->settings['orders_table'] . "` WHERE id='" . $this->db->escapeString($id) . "'");
        if (!$result || !count($result) || $result[0]['status'] != "evaded") { // Allow only evaded orders to be unevaded
            return;
        }
        // Update the order status
        $this->db->query("UPDATE `" . $this->table_prefix . $this->settings['orders_table'] . "` SET `status`='inbox', `ts`=`ts` WHERE `id`='" . $this->db->escapeString($id) . "'");
        // If the availability reduction type is postponed, update the products quantity
        if ($result[0]['availability_reduction_type'] == 2) {
            $products = $this->db->query("SELECT * FROM `" . $this->table_prefix . $this->settings['products_table'] . "` WHERE `order_id`='" . $this->db->escapeString($id) . "'");
            if (!$products || !count($products)) {
                return;
            }
            for ($i = 0; $i < count($products); $i++) { 
                if ($this->isDynamicProduct($products[$i]['product_id'])) {
                    $this->addDynamicProductItems($products[$i]['product_id'], $products[$i]['quantity']);
                }
            }
        }
    }
    
    /*
    | ---------------------------
    | Dynamic products management
    | ---------------------------
     */ 
    
    /**
     * Remove a dynamic product from the availability table
     * 
     * @param  String $id The product id
     * 
     * @return Void
     */
    function removeDynamicProduct($id)
    {
        if ($this->db) {
            $table = $this->table_prefix . $this->settings['dynamicproducts_table'];
            $this->db->query("DELETE FROM `" . $table . "` WHERE id='" . $this->db->escapeString($id) . "'");
        }
    }

    /**
     * Add more elements to the specified product
     * 
     * @param String $id           The product id
     * @param Number $quantity     The number to add to the current availability
     *
     * @return Void
     */
    function addDynamicProductItems($id, $quantity)
    {
        if (!$this->db) {
            return;
        }

        $quantity *= 1;
        $table = $this->table_prefix . $this->settings['dynamicproducts_table'];

        // Create the table if it doesn't exist
        if (!$this->db->tableExists($table)) {
            $this->db->createTable($table, array(
                "id"           => array('type' => 'VARCHAR(16)', 'primary' => true),
                "quantity"     => array('type' => 'INT(11)'),
                "warninglimit" => array('type' => 'INT(11)')
            ));
        }

        // Add the items to the table
        $count = $this->db->query("SELECT * FROM `" . $table . "` WHERE `id`='" . $this->db->escapeString($id) . "'");
        if (count($count) > 0) {
            $newQuantity = $count[0]['quantity'] + $quantity; // Make sure that the minimum quantity is always 0
            $this->db->query("UPDATE `" . $table . "` SET `quantity`=" . ($newQuantity > 0 ? $newQuantity : 0) ." WHERE `id`='" . $this->db->escapeString($id) . "'");
        } else {
            // Do not allow negative quantities at first
            $this->db->query("INSERT INTO `" . $table . "` (id, quantity, warninglimit) VALUES ('" . $this->db->escapeString($id) . "', " . ($quantity > 0 ? $quantity : 0) . ", 0)");
        }
    }

    /**
     * Set the quantity limit at wich a warning is triggered
     * 
     * @param String $id    The product id
     * @param Number $limit The limit
     *
     * @return Void
     */
    function setDynamicProductWarningLimit($id, $limit)
    {
        if ($this->db) {
            $table = $this->table_prefix . $this->settings['dynamicproducts_table'];
            $limit *= 1;
            $this->db->query("UPDATE `" . $table . "` SET `warninglimit`=" . $limit ." WHERE `id`='" . $this->db->escapeString($id) . "'");
        }
    }

    /**
     * Get the quantity of a single dynamic product
     * 
     * @param  String $id       The product id
     * 
     * @return Number           The number of avilable elements
     */
    function getDynamicProductQuantity($id)
    {
        if ($this->db) {
            $table = $this->table_prefix . $this->settings['dynamicproducts_table'];
            $result = $this->db->query("SELECT `quantity` FROM `" . $table . "` WHERE `id`='" . $this->db->escapeString($id) . "'");
            if ($result && count($result) > 0) {
                return $result[0]['quantity'];
            }
        }
        return 0;
    }

    /**
     * Get the information about the status of all the dynamic products
     * 
     * @param Number $pagination_start Where to start the pagination
     * @param Number $pagination_length The pagination length
     * 
     * @return Array
     */
    function getDynamicProductsStatus($pagination_start = 0, $pagination_length = 0)
    {
        $result = array();
        if (!$this->db) {
            return $result;
        }
        $query = "SELECT * FROM `" . $this->table_prefix . $this->settings['dynamicproducts_table'] . "`";
        $pagination_start *= 1;
        $pagination_length *= 1;
        if ($pagination_length > 0) {
            $query .= " LIMIT " . $pagination_start . "," . $pagination_length;
        }
        $query = $this->db->query($query);
        if (!$query) {
            return $result;
        }
        foreach ($query as $product) {
            $result[] = array(
                "id" => $product['id'],
                "availableQuantity" => $product['quantity'],
                "quantityAlert" => $product['quantity'] < $product['warninglimit']
            );
        }
        return $result;
    }

    /**
     * Get the information about the status of all the dynamic products that are reporting an alert status
     * 
     * @param Number $pagination_start Where to start the pagination
     * @param Number $pagination_length The pagination length
     * 
     * @return Array
     */
    function getDynamicProductsAlertStatus($pagination_start = 0, $pagination_length = 0)
    {
        $result = array();
        if (!$this->db) {
            return $result;
        }
        $query = "SELECT * FROM `" . $this->table_prefix . $this->settings['dynamicproducts_table'] . "` WHERE `quantity`<`warninglimit` || `quantity`=0";
        $pagination_start *= 1;
        $pagination_length *= 1;
        if ($pagination_length > 0) {
            $query .= " LIMIT " . $pagination_start . "," . $pagination_length;
        }
        $query = $this->db->query($query);
        if (!$query) {
            return;
        }
        foreach ($query as $product) {
            $result[] = array(
                "id" => $product['id'],
                "availableQuantity" => $product['quantity'],
                "quantityAlert" => $product['quantity'] < $product['warninglimit']
            );
        }
        return $result;
    }

    /**
     * Get an array of dynamic products that are below the warning limit, grouped by category
     * @param  integer $pagination_start
     * @param  integer $pagination_length
     * @return Array
     */
    function getDynamicProductsAvailabilityTable($pagination_start = 0, $pagination_length = 0) {
        global $imSettings;
        $data = $this->getDynamicProductsAlertStatus($pagination_start, $pagination_length);
        if (!$data || !count($data)) {
            return array();
        }
        $results = array();
        for ($i = 0; $i < count($data); $i++) {
            $name = $data[$i]['id'];
            if (isset($imSettings['search']['products'][$name])) {
                $image = $imSettings['search']['products'][$name]['image'];
                // Extract the image path
                $index = strpos($image, "src=\"");
                if ($index !== false) {
                    $image = substr($image, $index + 5);
                    $index = strpos($image, "\" ");
                    if ($index !== false) {
                        $image = "../" . substr($image, 0, $index);
                    }
                }
                $category = $imSettings['search']['products'][$name]['category'];
                if (!isset($results[$category])) {
                    $results[$category] = array();
                }
                $results[$category][] = array(
                    "name" => $imSettings['search']['products'][$name]['name'],
                    "image" => $image,
                    "status" => $data[$i]['availableQuantity'] == 0 ? 'unavailable' : 'lack',
                    "availableQuantity" => $data[$i]['availableQuantity']
                );
            }
        }
        return $results;
    }

    /**
     * Get the total count of dynamic products records
     * 
     * @return Number The dynamic products record count
     */
    function getDynamicProductsCount()
    {
        if ($this->db) {
            $result = $this->db->query("SELECT COUNT(id) AS `count` FROM `" . $this->table_prefix . $this->settings['dynamicproducts_table'] . "`");
            return $result[0]['count'];
        }
        return 0;
    }

    /**
     * Check if the specified product id is a dynamic product
     * 
     * @param  String  $id The product id
     * 
     * @return boolean     True if the product is using the dynamic availability
     */
    function isDynamicProduct($id)
    {
        if ($this->db) {
            $table = $this->table_prefix . $this->settings['dynamicproducts_table'];
            $result = $this->db->query("SELECT `id` FROM `" . $table . "` WHERE `id`='" . $this->db->escapeString($id) . "'");
            return is_array($result) && count($result) > 0;
        }
        return false;
    }

    /**
     * Get the availability level of a product (available, lacking, notavailable)
     * 
     * @param  String $id The product id
     * 
     * @return String     The availability level as a string
     */
    function getDynamicProductAvailabilityLevel($id)
    {
        if ($this->db) {
            $table = $this->table_prefix . $this->settings['dynamicproducts_table'];
            $result = $this->db->query("SELECT `quantity`, `warninglimit` FROM `" . $table . "` WHERE `id`='" . $this->db->escapeString($id) . "'");
            if (count($result)) {
                if ($result[0]['quantity'] > 0 && $result[0]['quantity'] >= $result[0]['warninglimit']) {
                    return "available";
                }
                if ($result[0]['quantity'] > 0 && $result[0]['quantity'] < $result[0]['warninglimit']) {
                    return "lacking";
                }
            }
        }
        return "notavailable";
    }
    
}




/**
 * @summary
 * Manage the comment structure of a topic. It can load and save the comments from/to a file or a database.
 * To use it, you must include __x5engine.php__ in your code.
 * 
 * This class is available only in the **Professional**, **Evolution** and **Compact** editions.
 *
 * @description
 * Build a new ImComment object.
 * 
 * @constructor
 */
class ImComment
{

    var $comments = array();
    var $error = 0;

    /**
     * Load the comments from an xml file
     * 
     * @param {string} $file The source file path
     *
     * @return {Void}
     */
    function loadFromXML($file)
    {
        if (!file_exists($file)) {
            $this->comments = array();
            return;
        }

        $xmlstring = @file_get_contents($file); 
        if (strpos($xmlstring, "<?xml") !== false) {
            $xml = new imXML();
            $id = 0;

            // Remove the garbage (needed to avoid loosing comments when the xml string is malformed)
            $xmlstring = preg_replace('/<([0-9]+)>.*<\/\1>/i', '', $xmlstring);
            $xmlstring = preg_replace('/<comment>\s*<\/comment>/i', '', $xmlstring);
            
            $comments = $xml->parse_string($xmlstring);
            if ($comments !== false && is_array($comments)) {
                $tc = array();
                if (!isset($comments['comment'][0]) || !is_array($comments['comment'][0]))
                    $comments['comment'] = array($comments['comment']);
                for ($i = 0; $i < count($comments['comment']); $i++) {
                    foreach ($comments['comment'][$i] as $key => $value) {
                        if ($key == "timestamp" && strpos($value, "-") == 2) {
                            // The v8 and v9 timestamp was inverted. For compatibility, let's convert it to the correct format.
                            // The v10 format is yyyy-mm-dd hh:ii:ss
                            // The v8 and v9 format is dd-mm-yyyy hh:ii:ss
                            $value = preg_replace("/([0-9]{2})\-([0-9]{2})\-([0-9]{4}) ([0-9]{2})\:([0-9]{2})\:([0-9]{2})/", "$3-$2-$1 $4:$5:$6", $value);
                        }
                        $tc[$i][$key] = str_replace(array("\\'", '\\"'), array("'", '"'), htmlspecialchars_decode($value));
                        if ($key == "rating" && is_numeric($value) && intval($value) > 5) {
                            $tc[$i][$key] = "5";
                        }
                    }
                    $tc[$i]['id'] = $id++;
                }
                $this->comments = $tc;
            } else {
                // The comments cannot be retrieved. The XML is jammed.
                // Do a backup copy of the file and then reset the xml.
                // Hashed names ensure that a file is not copied more than once
                $n = $file . "_version_" . md5($xmlstring);
                if (!@file_exists($n))
                    @copy($file, $n);
                $this->comments = array();
            }
        } else {
            $this->loadFromOldFile($file);
        }
    }

    /**
     * Get the comments from a v8 comments file.
     * Use loadFromXML instead
     *
     * @see [loadFromXML](##imcommentloadfromxmlfile)
     * @deprecated
     * 
     * @param {string} $file The source file path
     *
     * @return {Void}
     */
    function loadFromOldFile($file)
    {
        if (!@file_exists($file)) {
            $this->comments = array();
            return;
        }
        $f = @file_get_contents($file);
        $f = explode("\n", $f);
        for ($i = 0;$i < count($f)-1; $i += 6) {
            $c[$i/6]['id'] = $i / 6;
            $c[$i/6]['name'] = stripslashes($f[$i]);
            $c[$i/6]['email'] = $f[$i+1];
            $c[$i/6]['url'] = $f[$i+2];
            $c[$i/6]['body'] = stripslashes($f[$i+3]);
            $c[$i/6]['timestamp'] = preg_replace("/([0-9]{2})\-([0-9]{2})\-([0-9]{4}) ([0-9]{2})\:([0-9]{2})\:([0-9]{2})/", "$3-$2-$1 $4:$5:$6", $f[$i+4]);
            $c[$i/6]['approved'] = $f[$i+5];
            $c[$i/6]['rating'] = 0;
        }
        $this->comments = $c;
    }

    /**
     * Save the comments in a xml file
     * 
     * @param {string} $file The destination file path
     *
     * @return {boolean} True if the file was saved correctly
     */
    function saveToXML($file)
    {
        // If the count is 0, delete the file and exit
        if (count($this->comments) === 0) {
            if (@file_exists($file))
                @unlink($file);
            return true;
        }

        // If the folder doesn't exists, try to create it
        $dir = @dirname($file);
        if ($dir != "" && $dir != "/" && $dir != "." && $dir != "./" && !file_exists($dir)) {
            @mkdir($dir, 0777, true);
        }

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<comments>\n";
        $i = 0;
        foreach ($this->comments as $comment) {
            $txml = "";
            foreach ($comment as $key => $value) {
                // Well formed content only
                if (!preg_match('/[0-9]+/', $key) && in_array(gettype($value), array('string', 'integer', 'double'))) {
                    $code = str_replace(array("\\'", '\\"', "\\\""), array("'", '"', "\""), preg_replace('/[\n\r\t]*/', '', nl2br($value)));
                    $txml .= "\t\t<" . $key . "><![CDATA[" . htmlspecialchars($code) . "]]></" . $key . ">\n";
                }
            }
            if ($txml != "")
                $xml .= "\t<comment>\n" . $txml . "\t</comment>\n";
        }
        $xml .= "</comments>";

        if ((is_writable($file) || !file_exists($file))) {
            if (!$f = @fopen($file, 'w+')) {
                $this->error = -3;
                return false;
            } else {
                if (flock($f, LOCK_EX)) {
                    $locked = 1;
                }

                if (fwrite($f, $xml) === false) {
                    $this->error = -4;
                    return false;
                } else {
                    if($locked)
                        flock($f, LOCK_UN);
                    fclose($f);
                    $this->error = 0;
                    return true;
                }
            }
        } else {
            $this->error = -2;
            return false;
        }
    }


    /**
     * Save the comments in a DB.
     * **Available only in the Professional Edition**
     * 
     * @param {string} $host     The host name
     * @param {string} $user     The user name
     * @param {string} $password The user password
     * @param {string} $db       The db name
     * @param {string} $table    The db table
     * @param {string} $postid   The post id
     * 
     * @return {boolean} True if the comment was saved correctly
     */
    function saveToDb($host, $user, $password, $db, $table, $postid)
    {    
        // TODO: Avoid collisions and simplify update/delete the changed rows instead of deleting and rebuilding the table
        
        $db = new ImDb($host, $user, $password, $db);
        if (!$db->testConnection()) {
            return false;
        }

        // Delete the comments
        $db->query("DELETE FROM `" . $table . "` WHERE postid='" . $db->escapeString($postid) . "'");

        if (count($this->comments) === 0) {
            return true;
        }

        // Create the fields definition
        $fields = array(
            "postid"    => array("type" => (is_string($postid) ? "VARCHAR(32)" : "INT(11)"), "primary" => true),
            "commentid" => array("type" => "INT(11)", "primary" => true),
            "email"     => array("type" => "TEXT"),
            "name"      => array("type" => "TEXT"),
            "url"       => array("type" => "TEXT"),
            "body"      => array("type" => "TEXT"),
            "ip"        => array("type" => "TEXT"),
            "timestamp" => array("type" => "TIMESTAMP"),
            "abuse"     => array("type" => "TEXT"), // Yep. These fields would be more efficient if saved as INT but we have to
            "approved"  => array("type" => "TEXT"), // support even the old structures and the conversion between XML and MySQL.
            "rating"    => array("type" => "TEXT")
        );
        $db->createTable($table, $fields);

        // Resave them
        $i = 0;
        // WSXTWE-1222/1224/1283: Save only the fields that we care about, using the correct names
        $query = "INSERT INTO `" . $table . "` (postid,commentid,email,name,url,body,ip,timestamp,abuse,approved,rating) VALUES ";
        foreach ($this->comments as $comment) {
            $query .= "(
                '" . $postid . "',
                '" . $i++ . "',
                '" . (isset($comment['email']) ? $db->escapeString($comment['email']) : "") . "',
                '" . (isset($comment['name']) ? $db->escapeString($comment['name']) : "") . "',
                '" . (isset($comment['url']) ? $db->escapeString($comment['url']) : "") . "',
                '" . (isset($comment['body']) ? $db->escapeString($comment['body']) : "") . "',
                '" . (isset($comment['ip']) ? $db->escapeString($comment['ip']) : "") . "',
                '" . (isset($comment['timestamp']) ? $comment['timestamp'] : date("Y-m-d H:i:s")) . "',
                '" . (isset($comment['abuse']) ? $db->escapeString($comment['abuse']) : "0") . "',
                '" . (isset($comment['approved']) ? $db->escapeString($comment['approved']) : "1") . "',
                '" . (isset($comment['rating']) ? $db->escapeString($comment['rating']) : "0") . "'),";
        }
        $r = $db->query(trim($query, ","));
        if (!$r) {
            echo $db->error() . PHP_EOL;
        }
        $db->closeConnection();
        return $r;
    }

    /**
     * Load the comments from a DB. This method is available only in the Professional edition.
     * 
     * @param {string} $host     The host name
     * @param {string} $user     The user name
     * @param {string} $password The user password
     * @param {string} $dbname   The db name
     * @param {string} $table    The db table
     * @param {string} $postid   The post id
     * 
     * @return {boolean} True if the data was loaded correctly. False instead.
     */
    function loadFromDb($host, $user, $password, $dbname, $table, $postid)
    {
        $db = new ImDb($host, $user, $password, $dbname);
        if (!$db->testConnection()) {
            return false;
        }
        // WSXTWE-1317: Detect the kind of available fields
        $columnsResult = $db->query("SHOW COLUMNS FROM `" . $dbname . "`.`" . $table . "`");
        if (is_bool($columnsResult) && !$columnsResult) {
            return false;
        }
        $columns = array();
        foreach ($columnsResult as $result) {
            $columns[] = $result['Field'];
        }

        // WSXTWE-1222/1224/1283: Only select the fields that we care about
        $rows = $db->query("SELECT `commentid` AS `id`" . 
                                    (in_array('email', $columns) ? ", `email`" : "") .
                                    (in_array('name', $columns) ? ", `name`" : "") .
                                    (in_array('url', $columns) ? ", `url`" : "") .
                                    (in_array('body', $columns) ? ", `body`" : "") .
                                    (in_array('ip', $columns) ? ", `ip`" : "") .
                                    (in_array('timestamp', $columns) ? ", `timestamp`" : "") .
                                    (in_array('abuse', $columns) ? ", `abuse`" : "") .
                                    (in_array('approved', $columns) ? ", `approved`" : "") .
                                    (in_array('rating', $columns) ? ", `rating`" : "") .
                                    " FROM `" . $table . "` WHERE postid='" . $postid . "'");

        if (is_bool($rows)) {
            $this->comments = array();
            return false;
        }

        foreach ($rows as $row) {
            $comment = array();
            foreach ($row as $key => $value) {
                // Filter some fields
                if (!is_numeric($key)) {
                    $comment[$key] = $value;
                }
                if ($key == "rating" && is_numeric($value) && intval($value) > 5) {
                    $comment[$key] = "5";
                }
            }
            $this->comments[] = $comment;
        }
        return true;
    }


    /**
     * Add a comment to a file
     * 
     * @param {array} $comment the array of data to store
     *
     * @return {Void}
     */
    function add($comment)
    {
        foreach ($comment as $key => $value) {
            $comment[$key] = $this->filterCode($value, true);
        }
        $this->comments[] = $comment;
    }

    /**
     * Sort the array
     * 
     * @param string $orderby Field to compare when ordering the array
     * @param string $sort    Sort by ascending (asc) or descending (desc) order
     * 
     * @return void         
     */
    function sort($orderby = "", $sort = "desc")
    {
        if (count($this->comments) === 0)
            return;

        // Find where the comments has this field
        // This is useful to order using a field which is not present in every comment (like the ts field, which is missing in the stars-only vote type)
        $comment = null;
        for ($i=0; $i < count($this->comments) && $comment == null; $i++) { 
            if (isset($this->comments[$i][$orderby]))
                $comment = $this->comments[$i];
        }
        if ($comment === null)
            return;
        
        // Order the array
        $symbol = (strtolower($sort) == "desc" ? '<' : '>');
        $compare = 'if (!isset($a["' . $orderby . '"]) || !isset($b["' . $orderby . '"])) return 0;';
        $compare .= 'if($a["' . $orderby . '"] == $b["' . $orderby . '"]) return 0; ';

        // The orderable field is a timestamp
        if (preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}/", $comment[$orderby]))
            $compare .= 'if (strtotime($a["' . $orderby . '"]) ' . $symbol . ' strtotime($b["' . $orderby . '"])) return -1; return 1;';

        // The orderable field is a number
        else if (is_numeric($comment[$orderby]))
            $compare .= 'if ($a["' . $orderby . '"] ' . $symbol . ' $b["' . $orderby . '"]) return -1; return 1;';

        // The orderable field is a string
        else if (is_string($comment[$orderby]))
            $compare .= 'return strcmp($a["' . $orderby . '"], $b["' . $orderby . '"]);';

        // Sort and return
        usort($this->comments, create_function('$a, $b', $compare));
    }

    /**
     * Get all the comments loaded in this class
     * 
     * @param {string}  $orderby      Field to compare when ordering the array
     * @param {string}  $sort         Sort by ascending (asc) or descending (desc) order
     * @param {boolean} $approvedOnly Show only approved comments
     * 
     * @return {array} An array of associative arrays containing the comments data
     */
    function getAll($orderby = "", $sort = "desc", $approvedOnly = true)
    {
        if ($orderby == "" || count($this->comments) === 0)
            return $this->comments;
        
        $this->sort($orderby, $sort);
        
        if (!$approvedOnly)
            return $this->comments;

        $comments = array();
        foreach ($this->comments as $comment) {
            if (isset($comment['approved']) && $comment['approved'] == "1") {
                $comments[] = $comment;
            }
        }
        return $comments;
    }

    /**
     * Get the comments in the specified page when there is the specified number of comments in every page.
     * This is useful for pagination.
     * 
     * @param  {integer} $pageNumber      The number of page to show (0 based)
     * @param  {integer} $commentsPerPage Number of comments shown in each page
     * @param  {string}  $orderby         Field to compare when ordering the array
     * @param  {string}  $sort            Sort by ascending (asc) or descending (desc) order
     * @param  {boolean} $approvedOnly    Show only approved comments
     * 
     * @return {array} The list of comments in the page
     */
    function getPage($pageNumber, $commentsPerPage, $orderby = "", $sort = "desc", $approvedOnly = true) {
        $all = $this->getAll($orderby, $sort, $approvedOnly);
        // If the page number is wrong, return an empty array
        if ($pageNumber < 0 || $pageNumber > $this->getPagesNumber($commentsPerPage))
            return array();
        return array_slice($all, $pageNumber * $commentsPerPage, $commentsPerPage, false);
    }

    /**
     * Get the comment number n
     * 
     * @param {integer} $n The comment's number
     * 
     * @return {array} The comment's data or an empty array if the comment is not found
     */
    function get($n)
    {
        if (isset($this->comments[$n]))
            return $this->comments[$n];
        return array();
    }

    /**
     * Get the pages number given the number of comments per page
     * 
     * @param  {integer} $commentsPerPage Number of comments in every page
     * @param  {boolean} $approvedOnly    Show only approved comments
     * 
     * @return {integer} The number of pages
     */
    function getPagesNumber($commentsPerPage, $approvedOnly = true) {
        if (!is_array($this->comments) || !count($this->comments))
            return 0;
        if (!$approvedOnly) {
            $count = count($this->comments);
        } else {
            $count = 0;
            foreach ($this->comments as $comment) {
                if ($comment['approved'] == "1") {
                    $count++;
                }
            }
        }
        return ceil($count / $commentsPerPage);
    }

    /**
     * Edit the comment number $n with the data contained in the parameter $comment
     * 
     * @param {integer} $n       Comment number
     * @param {array}   $comment Comment data
     * 
     * @return {boolean} True if the comment was correctly edited. False instead.
     */
    function edit($n, $comment)
    {
        if (isset($this->comments[$n])) {
            $this->comments[$n] = $comment;
            return true;
        }
        return false;
    }

    /**
     * Delete the comment at $n
     * 
     * @param {integer} $n The index of the comment
     *
     * @return {Void}
     */
    function delete($n)
    {
        // Delete an element from the array and reset the indexes
        if (isset($this->comments[$n])) {
            $comments = $this->comments;
            $this->comments = array();
            for ($i = 0; $i < count($comments); $i++)
                if ($i != $n)
                    $this->comments[] = $comments[$i];
            return true;
        } else {
            return false;
        }
    }

    /**
     * Clean the data from XSS
     * 
     * @param string  $str         The string to parse
     * @param boolean $allow_links true to allow links
     * 
     * @return string
     */
    function filterCode($str, $allow_links = false)
    {
        global $imSettings;

        if (gettype($str) != 'string')
            return "";

        // Remove javascript
        while (($start = imstripos($str, "<script")) !== false) {
            $end = imstripos($str, "</script>") + strlen("</script>");
            $str = substr($str, 0, $start) . substr($str, $end);
        }

        // Remove PHP Code
        while (($start = imstripos($str, "<?")) !== false) {
            $end = imstripos($str, "?>") + strlen("?>");
            $str = substr($str, 0, $start) . substr($str, $end);
        }

        // Remove ASP code
        while (($start = imstripos($str, "<%")) !== false) {
            $end = imstripos($str, "%>") + strlen("<%");
            $str = substr($str, 0, $start) . substr($str, $end);
        }

        // Allow only few tags
        $str = strip_tags($str, '<b><i><u>' . ($allow_links ? '<a>' : ''));
        
        // Remove XML injection code
        while (($start = imstripos($str, "<![CDATA[")) !== false) {    
            // Remove the entire XML block when possible
            if (imstripos($str, "]]>") !== false) {
                $end = imstripos($str, "]]>") + strlen("]]>");
                $str = substr($str, 0, $start) . substr($str, $end);
            } else {        
                $str = str_replace("<![CDATA[", "", str_replace("<![cdata[", "", $str));
            }
        }
        while (($start = imstripos($str, "]]>")) !== false) {
            $str = str_replace("]]>", "", $str);
        }

        // Remove all the onmouseover, onclick etc attributes
        while (($res = preg_replace("/(<[\\s\\S]+) on.*\\=(['\"\"])[\\s\\S]+\\2/i", "\\1", $str)) != $str) {
            // Exit in case of error
            if ($res == null)
                break;
            $str = $res;
        }

        $matches = array();
        preg_match_all('~<a.*>~isU', $str, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            if (imstripos($matches[0][$i], 'nofollow') === false && imstripos($matches[0][$i], $imSettings['general']['url']) === false) {
                $result = trim($matches[0][$i], ">") . ' rel="nofollow">';
                $str = str_replace(strtolower($matches[0][$i]), strtolower($result), $str);
            }
        }

        return $str;
    }

    /**
     * Provide the last error
     * 
     * @return int
     */
    function lastError()
    {
        return $this->error;
    }
}




/**
 * An interface that defines the common methods used to access the db
 */
interface DatabaseAccess
{	
    public function testConnection();
    public function closeConnection();
    public function createTable($name, $fields);
    public function deleteTable($table);
    public function tableExists($table);
    public function error();
    public function lastInsertId();
    public function query($query);
    public function escapeString($string);
    public function affectedRows();
}



/**
 * @summary
 * A database driver class which access to the DB using the "mysql_" functions
 * 
 * To use this class, you must include __x5engine.php__ in your code.
 * 
 * @description Create a new ImDb Object
 *
 * @ignore
 * @class
 * @constructor
 * 
 * @param {string} $host  The database host address
 * @param {string} $user  The database username
 * @param {string} $pwd   The database password
 * @param {string} $db    The database name
 */
class MySQLDriver implements DatabaseAccess
{

    var $conn;
    var $db;
    var $db_name;
    var $engine = "MYISAM";
    
    function __construct($host, $user, $pwd, $db)
    {
        $this->setUp($host, $user, $pwd, $db);
    }
    
    function ImDb($host, $user, $pwd, $db)
    {
        $this->setUp($host, $user, $pwd, $db);
    }

    function setUp($host, $user, $pwd, $db)
    {
        $this->db_name = $db;
        $this->conn = @mysql_connect($host, $user, $pwd);
        if ($this->conn === false)
            return;
        $this->db = @mysql_select_db($db, $this->conn);
        if ($this->db === false)
            return;
        if (function_exists('mysql_set_charset'))
            @mysql_set_charset("utf8", $this->conn);
        else
            @mysql_query('SET NAMES "utf8"', $this->conn);
    }

    /**
     * Check if the class is connected or not to a db
     *
     * @return {boolean} True if the class is connected to a DB. False otherwise.
     */
    function testConnection()
    {
        return ($this->conn !== false && $this->db !== false);
    }

    /**
     * Close the connection
     * 
     * @return void
     */
    function closeConnection()
    {
        @mysql_close($this->conn);
    }

    /**
     * Create a new table or update an existing one.
     * 
     * @param {string} $name   The table name
     * @param {array} $fields  The table fields list as array of associative arrays (one array item foreach table field). must be passed as stated in the example.
     *
     * @example
     * $db->createTable('tableName', array(
     *     "field1" => array(
     *         "type" => "INTEGER",
     *         "null" => false,
     *         "auto_increment" => true,
     *         "primary" => true
     *     ),
     *     "field2" => array(
     *         "type" => "TEXT",
     *         "null" => true,
     *         "auto_increment" => false,
     *         "more" => "CHARACTER SET UTF-8"
     *     ))
     * );
     * 
     * @return {boolean} True if the table was created succesfully.
     */
    function createTable( $name, $fields )
    {
        $qfields = array();
        $primaries = array();
        $createResult = false;

        // If the table does not exists, create it
        if (!$this->tableExists($name)) {
            $query = "CREATE TABLE `" . $this->db_name . "`.`" . $name . "` (";
            foreach ($fields as $key => $value) {
                $qfields[] = "`" . $key . "` " .
                            $value['type'] .
                            ($value['type'] == 'TEXT' ? " CHARACTER SET utf8 COLLATE utf8_unicode_ci" : "") .
                            (!isset($value['null']) || !$value['null'] ? " NOT NULL" : "") .
                            (isset($value['auto_increment']) ? " AUTO_INCREMENT" : "") .
                            (isset($value['more']) ? " " . $value['more'] : "");
                if (isset($value['primary']) && $value['primary']) {
                    $primaries[] = "`" . $key . "`";
                }
            }
            $query .= implode(",", $qfields);
            if (count($primaries))
                $query .= ", PRIMARY KEY (" . implode(",", $primaries) . ")";
            $query .= ") ENGINE = " . $this->engine . " ;";
            $createResult = mysql_query($query, $this->conn);
        } else {
            $result = mysql_query("SHOW COLUMNS FROM `" . $this->db_name . "`.`" . $name . "`", $this->conn);
            if ($result) {
                // Actual fields
                $query = "ALTER TABLE `" . $this->db_name. "`.`" . $name . "`";
                $act_fields = array();
                while ($row = mysql_fetch_array($result))
                    $act_fields[] = $row[0];
                // New fields
                $new_fields = array_diff(array_keys($fields), $act_fields);
                $new_fields = array_merge($new_fields); // Order the indexes
                if (count($new_fields) > 0) {
                    foreach ($new_fields as $key) {
                        $qfields[] = " ADD `" . $key . "` " . $fields[$key]['type'] . 
                        ($fields[$key]['type'] == 'TEXT' ? " CHARACTER SET utf8 COLLATE utf8_unicode_ci" : "") .
                        (!isset($fields[$key]['null']) || !$fields[$key]['null'] ? " NOT NULL" : "") .
                        (isset($fields[$key]['auto_increment']) && $fields[$key]['auto_increment'] ? " AUTO_INCREMENT" : "") .
                        // WSXTWE-1215: Manage the adding/removal of a primary key
                        (isset($fields[$key]['primary']) && $fields[$key]['primary'] ? " PRIMARY KEY" : "") .
                        (isset($fields[$key]['more']) ? " " . $fields[$key]['more'] : "");
                    }
                    $query .= implode(",", $qfields);
                    $createResult = mysql_query($query, $this->conn);
                }
            }
        }
        return $createResult;
    }

    /**
     * Delete a table from the database.
     * 
     * @param {string} $table The table name
     *
     * @return {Void}
     */
    function deleteTable($table)
    {
        mysql_query("DROP TABLE " . $this->db_name . "." . $table, $this->conn);
    }

    /**
     * Check if the table exists
     * 
     * @param {string} $table The table name
     * 
     * @return {boolean} True if the table exists. False otherwise.
     */
    function tableExists($table)
    {
        $result = mysql_query("SHOW FULL TABLES FROM `" . $this->db_name . "` LIKE '" . mysql_real_escape_string($table, $this->conn) . "'", $this->conn);
        // Check that the name is correct (usage of LIKE is not correct if there are wildcards in the table name. Unfortunately MySQL 4 doesn't allow another syntax..)
        while (!is_bool($result) && $tb = mysql_fetch_array($result)) {
            if ($tb[0] == $table)
                return true;
        }
        return false;
    }

    /**
     * Get the last MySQL error.
     * 
     * @return {array}
     */
    function error()
    {
        return mysql_error();
    }

    /**
     * Provide the last inserted ID of the AUTOINCREMENT column
     * 
     * @return {int} The id of the latest insert operation
     */
    function lastInsertId()
    {
        $res = $this->query("SELECT LAST_INSERT_ID() AS `id`");
        if (count($res) > 0 && isset($res[0]['id'])) {
            return $res[0]['id'];
        }
        return 0;
    }

    /**
     * Execute a MySQL query.
     * 
     * @param {string} $query
     * 
     * @return {array}        The query result or FALSE on error
     */
    function query($query)
    {
        $result = mysql_query($query, $this->conn);
        if (!is_bool($result)) {
            $rows = array();
            while($row = mysql_fetch_array($result)) {
                $rows[] = $row;
            }
            return $rows;
        }
        return $result;
    }

    /**
     * Escape a MySQL query string.
     * 
     * @param {string} $string The string to escape
     * 
     * @return {string} The escaped string
     */
    function escapeString($string)
    {
        if (!is_array($string)) {
            return mysql_real_escape_string($string, $this->conn);
        } else {
            for ($i = 0; $i < count($string); $i++)
                $string[$i] = $this->escapeString($string[$i]);
            return $string;
        }
    }

    /**
     * Return the number of affected rows in the last query.
     * 
     * @return {integer} The number of affected rows.
     */
    function affectedRows()
    {
        return mysql_affected_rows($this->conn);
    }
}




/**
 * @summary
 * A database driver class which access to the DB using MySQLi.
 * 
 * To use this class, you must include __x5engine.php__ in your code.
 * 
 * @description Create a new ImDb Object
 *
 * @ignore
 * @class
 * @constructor
 * 
 * @param {string} $host  The database host address
 * @param {string} $user  The database username
 * @param {string} $pwd   The database password
 * @param {string} $db    The database name
 */
class MySQLiDriver implements DatabaseAccess
{
    private $db;
    private $db_name;
    private $engine = "INNODB";
    
    function __construct($host, $user, $pwd, $db)
    {
        $this->db_name = $db;
        $this->db = new mysqli($host, $user, $pwd);
        if ($this->db->connect_errno) {
            return;
        }
        if (strlen($db)) {
            $this->db->select_db($db);
        }
        if (function_exists('mysqli_set_charset')) {
            $this->db->set_charset("utf8");
        }
        else {
            $this->db->query('SET NAMES "utf8"');
        }
    }

    /**
     * Check if the class is connected or not to a db
     *
     * @return {boolean} True if the class is connected to a DB. False otherwise.
     */
    function testConnection()
    {
        return ($this->db->connect_errno == 0);
    }

    /**
     * Close the connection
     * 
     * @return void
     */
    function closeConnection()
    {
        $this->db->close();
    }

    /**
     * Create a new table or update an existing one.
     * 
     * @param {string} $name   The table name
     * @param {array} $fields  The table fields list as array of associative arrays (one array item foreach table field). must be passed as stated in the example.
     *
     * @example
     * $db->createTable('tableName', array(
     *     "field1" => array(
     *         "type" => "INTEGER",
     *         "null" => false,
     *         "auto_increment" => true,
     *         "primary" => true
     *     ),
     *     "field2" => array(
     *         "type" => "TEXT",
     *         "null" => true,
     *         "auto_increment" => false,
     *         "more" => "CHARACTER SET UTF-8"
     *     ))
     * );
     * 
     * @return {boolean} True if the table was created succesfully.
     */
    function createTable( $name, $fields )
    {
        $qfields = array();
        $primaries = array();
        $createResult = false;

        // If the table does not exists, create it
        if (!$this->tableExists($name)) {
            $query = "CREATE TABLE `" . $this->db_name . "`.`" . $name . "` (";
            foreach ($fields as $key => $value) {
                $qfields[] = "`" . $key . "` " .
                            $value['type'] .
                            ($value['type'] == 'TEXT' ? " CHARACTER SET utf8 COLLATE utf8_unicode_ci" : "") .
                            (!isset($value['null']) || !$value['null'] ? " NOT NULL" : "") .
                            (isset($value['auto_increment']) ? " AUTO_INCREMENT" : "") .
                            (isset($value['more']) ? " " . $value['more'] : "");
                if (isset($value['primary']) && $value['primary']) {
                    $primaries[] = "`" . $key . "`";
                }
            }
            $query .= implode(",", $qfields);
            if (count($primaries))
                $query .= ", PRIMARY KEY (" . implode(",", $primaries) . ")";
            $query .= ") ENGINE = " . $this->engine . " ;";
            $createResult = $this->db->query($query);
        } else {
            $result = $this->db->query("SHOW COLUMNS FROM `" . $this->db_name . "`.`" . $name . "`");
            if ($result) {
                // Actual fields
                $query = "ALTER TABLE `" . $this->db_name. "`.`" . $name . "`";
                $act_fields = array();
                while ($row = $result->fetch_array())
                    $act_fields[] = $row[0];
                // New fields
                $new_fields = array_diff(array_keys($fields), $act_fields);
                $new_fields = array_merge($new_fields); // Order the indexes
                if (count($new_fields) > 0) {
                    foreach ($new_fields as $key) {
                        $qfields[] = " ADD `" . $key . "` " . $fields[$key]['type'] . 
                        ($fields[$key]['type'] == 'TEXT' ? " CHARACTER SET utf8 COLLATE utf8_unicode_ci" : "") .
                        (!isset($fields[$key]['null']) || !$fields[$key]['null'] ? " NOT NULL" : "") .
                        (isset($fields[$key]['auto_increment']) && $fields[$key]['auto_increment'] ? " AUTO_INCREMENT" : "") .
                        // WSXTWE-1215: Manage the adding/removal of a primary key
                        (isset($fields[$key]['primary']) && $fields[$key]['primary'] ? " PRIMARY KEY" : "") .
                        (isset($fields[$key]['more']) ? " " . $fields[$key]['more'] : "");
                    }
                    $query .= implode(",", $qfields);
                    $createResult = $this->db->query($query);
                }
            }
        }
        return $createResult;
    }

    /**
     * Delete a table from the database.
     * 
     * @param {string} $table The table name
     *
     * @return {Void}
     */
    function deleteTable($table)
    {
        $this->db->query("DROP TABLE " . $this->db_name . "." . $table);
    }

    /**
     * Check if the table exists
     * 
     * @param {string} $table The table name
     * 
     * @return {boolean} True if the table exists. False otherwise.
     */
    function tableExists($table)
    {
        $result = $this->db->query("SHOW FULL TABLES FROM `" . $this->db_name . "` LIKE '" . $this->db->real_escape_string($table) . "'");
        // Check that the name is correct (usage of LIKE is not correct if there are wildcards in the table name. Unfortunately MySQL 4 doesn't allow another syntax..)
        while (!is_bool($result) && $tb = $result->fetch_array()) {
            if ($tb[0] == $table)
                return true;
        }
        return false;
    }

    /**
     * Get the last MySQL error.
     * 
     * @return {array}
     */
    function error()
    {
        return $this->db->error;
    }

    /**
     * Provide the last inserted ID of the AUTOINCREMENT column
     * 
     * @return {int} The id of the latest insert operation
     */
    function lastInsertId()
    {
        $res = $this->query("SELECT LAST_INSERT_ID() AS `id`");
        if (count($res) > 0 && isset($res[0]['id'])) {
            return $res[0]['id'];
        }
        return 0;
    }

    /**
     * Execute a MySQL query.
     * 
     * @param {string} $query
     * 
     * @return {array}        The query result or FALSE on error
     */
    function query($query)
    {
        $result = $this->db->query($query);
        if (!is_bool($result)) {
            $rows = array();
            while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $rows[] = $row;
            }
            return $rows;
        }
        return $result;
    }

    /**
     * Escape a MySQL query string.
     * 
     * @param {string} $string The string to escape
     * 
     * @return {string} The escaped string
     */
    function escapeString($string)
    {
        if (!is_array($string)) {
            return $this->db->real_escape_string($string);
        } else {
            for ($i = 0; $i < count($string); $i++) {
                $string[$i] = $this->escapeString($string[$i]);
            }
            return $string;
        }
    }

    /**
     * Return the number of affected rows in the last query.
     * 
     * @return {integer} The number of affected rows.
     */
    function affectedRows()
    {
        return $this->db->affected_rows;
    }
}




/**
 * @summary
 * A utility class which provides an easy access to the databases defined by the WSX5 user.
 * Detect if MySQLi is supported, otherwise fallback on MySQL.
 * 
 * To use this class, you must include __x5engine.php__ in your code.
 * 
 * @description Create a new ImDb Object
 *
 * @class
 * @constructor
 * 
 * @param {string} $host  The database host address
 * @param {string} $user  The database username
 * @param {string} $pwd   The database password
 * @param {string} $db    The database name
 */
class ImDb implements DatabaseAccess
{

    private $driver;
    
    function __construct($host, $user, $pwd, $db)
    {
        // Detect the correct driver
        if (function_exists("mysqli_connect")) {
            $this->driver = new MySQLiDriver($host, $user, $pwd, $db);
        } else if (function_exists("mysql_connect")) {
            $this->driver = new MySQLDriver($host, $user, $pwd, $db);
        } else {
            die("No database support detected");
        }
    }

    /**
     * Check if the class is connected or not to a db
     *
     * @return {boolean} True if the class is connected to a DB. False otherwise.
     */
    function testConnection()
    {
        return $this->driver->testConnection();
    }

    /**
     * Close the connection
     * 
     * @return void
     */
    function closeConnection()
    {
        $this->driver->closeConnection();
    }

    /**
     * Create a new table or update an existing one.
     * 
     * @param {string} $name   The table name
     * @param {array} $fields  The table fields list as array of associative arrays (one array item foreach table field). must be passed as stated in the example.
     *
     * @example
     * $db->createTable('tableName', array(
     *     "field1" => array(
     *         "type" => "INTEGER",
     *         "null" => false,
     *         "auto_increment" => true,
     *         "primary" => true
     *     ),
     *     "field2" => array(
     *         "type" => "TEXT",
     *         "null" => true,
     *         "auto_increment" => false,
     *         "more" => "CHARACTER SET UTF-8"
     *     ))
     * );
     * 
     * @return {boolean} True if the table was created succesfully.
     */
    function createTable($name, $fields)
    {
        return $this->driver->createTable($name, $fields);
    }

    /**
     * Delete a table from the database.
     * 
     * @param {string} $table The table name
     *
     * @return {Void}
     */
    function deleteTable($table)
    {
        $this->driver->deleteTable($table);
    }

    /**
     * Check if the table exists
     * 
     * @param {string} $table The table name
     * 
     * @return {boolean} True if the table exists. False otherwise.
     */
    function tableExists($table)
    {
        return $this->driver->tableExists($table);
    }

    /**
     * Get the last MySQL error.
     * 
     * @return {array}
     */
    function error()
    {
        return $this->driver->error();
    }

    /**
     * Provide the last inserted ID of the AUTOINCREMENT column
     * 
     * @return {int} The id of the latest insert operation
     */
    function lastInsertId()
    {
        return $this->driver->lastInsertId();
    }

    /**
     * Execute a MySQL query.
     * 
     * @param {string} $query
     * 
     * @return {array}        The query result or FALSE on error
     */
    function query($query)
    {
        return $this->driver->query($query);
    }

    /**
     * Escape a MySQL query string.
     * 
     * @param {string} $string The string to escape
     * 
     * @return {string} The escaped string
     */
    function escapeString($string)
    {
        return $this->driver->escapeString($string);
    }

    /**
     * Return the number of affected rows in the last query.
     * 
     * @return {integer} The number of affected rows.
     */
    function affectedRows()
    {
        return $this->driver->affectedRows();
    }
}




class DynamicObject
{
	var $body = "";	
	var $id;
	var $defaultText = "";

	function __construct($id)
	{
		$this->id = $id;
	}

	function setDefaultText($text)
	{
		$this->defaultText = $text;
	}

	function setContent($content)
	{
		$this->body = $content;
	}

	function getContent()
	{
		if (strlen($this->body))
			return $this->body;
		return $this->defaultText;
	}

	/**
     * Setup the folder
     * 
     * @param string $folder The folder path to prepare
     * 
     * @return string
     */
    function prepFolder($folder)
    {
        if (strlen(trim($folder)) == 0)
            return "./";

        if (substr($folder, 0, -1) != "/")
            $folder .= "/";

        return $folder;
    }

	function loadFromFile($folder)
	{
		$folder = $this->prepFolder($folder);
		if (file_exists($folder . $this->id . ".txt"))
			$this->body = @file_get_contents($folder . $this->id . ".txt");
		else
			$this->body = "";
	}

	function saveToFile($folder)
	{
		$folder = $this->prepFolder($folder);
		if ($folder != "" && $folder != "/" && $folder != "." && $folder != "./" && !file_exists($folder)) {
            @mkdir($folder, 0777, true);
        }
		return @file_put_contents($folder . $this->id . ".txt", $this->body);
	}

	function loadFromDb($host, $user, $password, $db, $table)
	{
		$db = new ImDb($host, $user, $password, $db);
		if (!$db->testConnection())
			return false;
		$data = $db->query("SELECT * FROM `" . $table . "` WHERE id='" . $db->escapeString($this->id) . "'");
		if (is_bool($data))
			return false;
		if (!isset($data[0]['body']))
			return false;
		$this->body = $data[0]['body'];
		return true;
	}

	function saveToDb($host, $user, $password, $db, $table)
	{
		$db = new ImDb($host, $user, $password, $db);
		if (!$db->testConnection())
			return false;
		$db->createTable(
	        $table,
	        array(
	            "id" => array('type' => 'VARCHAR(32)', 'primary' => true),
	            "body" => array('type' => 'TEXT')
	        )
	    );
	    $exists = $db->query("SELECT * FROM `" . $table . "` WHERE id='" . $db->escapeString($this->id) . "'");
	    if ($exists)
	    	return $db->query("UPDATE `" . $table . "` SET body='" . $db->escapeString($this->body) . "' WHERE id='" . $db->escapeString($this->id) . "'");
	    return $db->query("INSERT INTO `" . $table . "` (id, body) VALUES ('" . $db->escapeString($this->id) . "', '" . $db->escapeString($this->body) . "')");
	}	
}




/**
 * Provide support for sending and saving the email form data
 */

class ImForm
{

    var $fields = array();
    var $files = array();
    var $answers = array();

    /**
     * Set the data of a field
     * 
     * @param string $label  The field label
     * @param strin  $value  The field value
     * @param string $dbname The name to use in the db
     * @param boolean $isSeparator True if this field must be used as separator in the email
     * 
     * @return boolean
     */
    function setField($label, $value, $dbname = "", $isSeparator = false)
    {
        $this->fields[] = array(
            "label"     => $label,
            "value"     => $value,
            "dbname"    => $dbname,
            "isSeparator" => $isSeparator
        );
        return true;
    }

    /**
     * Provide the currently set fields
     * 
     * @return array
     */
    function fields()
    {
        return $this->fields;
    }

    /**
     * Set a file field
     * 
     * @param string  $label      The field label
     * @param file    $value      The $_FILE[id] content
     * @param string  $folder     The folder in which the file must be saved
     * @param string  $dbname     The db column in which this filed must be saved
     * @param mixed   $extensions The extensions allowed for the file (string or array)
     * @param integer $maxsize    The max size (0 to not check this)
     * 
     * @param integer 1 = No file uploaded, 0 = success, -1 = generic error, -2 = extension not allowed, -3 = File too large
     */
    function setFile($label, $value, $folder = "", $dbname = "", $extensions = array(), $maxsize = 0)
    {
        if (is_string($extensions))
            $extensions = strlen($extensions) ? explode(",", trim(strtolower($extensions), ",")) : array();

        // WSXELE-738: Fix extensions separated by spaces
        for ($i = 0; $i < count($extensions); $i++) { 
            $extensions[$i] = trim($extensions[$i]);
        }

        $exists = file_exists($value['tmp_name']);
        if (!$exists)
            return 1; // If the file doesn't exists it means that it was not uploaded
        
        $fileExtension = strtolower(substr($value['name'], strpos($value['name'], ".") + 1));
        $extension = (count($extensions) == 0 || in_array($fileExtension, $extensions));
        $size = ($maxsize == 0 || $maxsize >= $value['size']);

        if (!$extension)
            return -2;
        if (!$size)
            return -3;
            
        if ($folder != "" && substr($folder, 0, -1) != "/")
            $folder .= "/";
        $this->files[] = array(
            "value" => $value,
            "label" => $label,
            "dbname" => $dbname,
            "folder" => $folder,
            // Save the file content to set is as available for every istance in the class
            // This because after calling move_uploaded_file the temp file is not available anymore
            "content" => @file_get_contents($value['tmp_name'])
        );
        return 0;
    }

    /**
     * Provides the currently set files
     *
     *  @return array
     */
    function files()
    {
        return $this->files;
    }

    /**
     * Set the answer to check
     * 
     * @param string $questionId The question id
     * @param string $answer     The correct answer
     *
     * @return void
     */
    function setAnswer($questionId, $answer)
    {
        $this->answers[$questionId] = $answer;
    }

    /**
     * Check if the answer $answer is correct for question $questionId
     * 
     * @param  string $questionId The question id
     * @param  string $answer     The answer
     * 
     * @return boolean
     */
    function checkAnswer($questionId, $answer)
    {
        $questionId += "";
        return (isset($this->answers[$questionId]) && trim(strtolower($this->answers[$questionId])) == trim(strtolower($answer)));
    }

    /**
     * Provides the currently set answers
     * 
     * @return array
     */
    function answers()
    {
        return $this->answers;
    }

    /**
     * Save the data in the database
     * 
     * @param  string $host
     * @param  string $user
     * @param  string $passwd
     * @param  string $database
     * @param  string $table
     * 
     * @return boolean
     */
    function saveToDb($host, $user, $passwd, $database, $table)
    {
        $db = new ImDb($host, $user, $passwd, $database);
        if (!$db->testConnection())
            return false;

        $fields = array();
        $names = array();
        $values = array();

        // WSXTWE-1215: Add an autoincrement primary key
        $fields["id"] = array(
            "type" => "INTEGER",
            "null" => false,
            "auto_increment" => true,
            "primary" => true
        );
        $i = 0;
        foreach ($this->fields as $field) {
            if (!$field['isSeparator']) {
                $name = isset($field['dbname']) && $field['dbname'] !== "" ? $field['dbname'] : "field_" . $i++;
                $fields[$name] = array(
                    "type" => "TEXT"
                );
                $names[] = "`" . $name . "`";
                $values[] = "'" . $db->escapeString(is_array($field['value']) ? implode(", ", $field['value']) : $field['value']) . "'";
            }
        }
        $i = 0;
        foreach ($this->files as $file) {
            $fieldname = isset($file['dbname']) && $file['dbname'] !== "" ? $file['dbname'] : "file_" . $i++;
            $filename = $this->findFileName($file['folder'], $file['value']['name']);
            $fields[$fieldname] = array(
                "type" => "TEXT"
            );
            $names[] = "`" . $fieldname . "`";
            $values[] = "'" . $db->escapeString($filename) . "'";
            // Create and check the folder
            $folder = "../";
            if (($pos = strpos($file['folder'], "/")) === 0)
                $file['folder'] = substr($file['folder'], 1);
            $folder .= $file['folder'];
            if ($folder != "../" && !file_exists($folder))
                @mkdir($folder, 0777, true);
            $folder = str_replace("//", "/", $folder .= $filename);
            // Save the file
            @move_uploaded_file($file['value']['tmp_name'], $folder);
        }

        // Create the table
        $db->createTable($table, $fields);

        // Save the fields data
        $query = "INSERT INTO `" . $table . "` (" . implode(",", $names) . ") VALUES (" . implode(",", $values) . ")";
        $db->query($query);
        $db->closeConnection();
        return true;
    }

    /**
     * Find a free filename
     * 
     * @param  string $folder   The folder in which the file is being saved
     * @param  string $tmp_name The filename
     * 
     * @return string           The new name
     */
    function findFileName($folder, $tmp_name)
    {
        $pos = strrpos($tmp_name, ".");
        $ext = ($pos !== false ? substr($tmp_name, $pos) : "");
        $fname = basename($tmp_name, $ext);            
        do {
            $rname = $fname . "_" . rand(0, 10000) .  $ext;
        } while (file_exists($folder . $rname));
        return $rname;
    }

    /**
     * Send the email to the site's owner
     * 
     * @param  string  $from
     * @param  string  $to
     * @param  string  $subject
     * @param  string  $text    The email body
     * @param  boolean $csv     Attach the CSV files?
     * 
     * @return boolean
     */
    function mailToOwner($from, $to, $subject, $text, $csv = false)
    {
        global $ImMailer;

        //Form Data
        $txtData = strip_tags($text);
        if (strlen($txtData))
             $txtData .= "\n\n";
        $htmData = nl2br($text);
        if (strlen($htmData))
            $htmData .= "\n<br><br>\n";
        $htmData .= "<table border=0 width=\"100%\" style=\"[email:contentStyle]\">\r\n";
        $csvHeader = "";
        $csvData = "";
        $firstField = true;
        
        foreach ($this->fields as $field) {
            if ($field['isSeparator']) {
                //
                // This field is a form separator
                // 
                $txtData .= (!$firstField ? "\r\n" : "") . $field['label'] . "\r\n" . str_repeat("=", strlen($field['label'])) . "\r\n";
                $htmData .= "<tr valign=\"top\"><td colspan=\"2\" style=\"" . (!$firstField ? "padding-top: 8px;" : "") . " border-bottom: 1px solid [email:bodySeparatorBorderColor];\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $field['label']) . "</b></td></tr>\r\n";
            } else {
                //
                // This field is a classic form field
                // 
                $label = ($field['label'] != "" ? $field['label'] . ": " : "");
                if (is_array($field['value'])) {
                    $txtData .= $label . implode(", ", $field['value']) . "\r\n";
                    $htmData .= "<tr valign=\"top\"><td width=\"25%\"><b>" . $label . "</b></td><td>" . implode(", ", $field['value']) . "</td></tr>\r\n";
                    if ($csv) {
                        $csvHeader .= $field['label'] . ";";
                        $csvData .= implode(", ", $field['value']) . ";";
                    }
                } else {        
                    $txtData .= $label . $field['value'] . "\r\n";
                    // Is it an email?
                    if (preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' . '(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', $field['value'])) {
                        $htmData .= "<tr valign=\"top\"><td width=\"25%\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $label) . "</b></td><td><a href=\"mailto:" . $field['value'] . "\">". $field['value'] . "</a></td></tr>\r\n";
                    } else if (preg_match('/^http[s]?:\/\/[a-zA-Z0-9\.\-]{2,}\.[a-zA-Z]{2,}/', $field['value'])) {
                        // Is it an URL?
                        $htmData .= "<tr valign=\"top\"><td width=\"25%\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $label) . "</b></td><td><a href=\"" . $field['value'] . "\">". $field['value'] . "</a></td></tr>\r\n";
                    } else {
                        $htmData .= "<tr valign=\"top\"><td width=\"25%\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $label) . "</b></td><td>" . str_replace(array("\\'", '\\"'), array("'", '"'), $field['value']) . "</td></tr>\r\n";
                    }
                    if ($csv) {
                        $csvHeader .= str_replace(array("\\'", '\\"'), array("'", '"'), $field['label']) . ";";
                        $csvData .= str_replace(array("\\'", '\\"'), array("'", '"'), $field['value']) . ";";
                    }
                }
            }
            $firstField = false;
        }

        $htmData .= "</table>";
        $attachments = array();
        if ($csv) {
            $attachments[] = array(
                "name" => "form_data.csv", 
                "content" => $csvHeader . "\n" . $csvData,
                "mime" => "text/csv"
            );
        }
        foreach ($this->files as $file) {
            $attachments[] = array(
                'name' => $file['value']['name'],
                'content' => $file['content'],
                'mime' => $file['value']['type']
            );
        }
        return $ImMailer->send($from, $to, $subject, $txtData, $htmData, $attachments);
    }

    /**
     * Send the email to the site's customer
     * 
     * @param  string  $from
     * @param  string  $to
     * @param  string  $subject
     * @param  string  $text    The email body
     * @param  boolean $summary Append the data to the email? (It's not an attachment)
     * 
     * @return boolean
     */
    function mailToCustomer($from, $to, $subject, $text, $csv = false)
    {
        global $ImMailer;

        //Form Data
        $txtData = strip_tags($text);
        if (strlen($txtData))
            $txtData .= "\n\n";
        $htmData = nl2br($text);
        if (strlen($htmData))
            $htmData .= "\n<br><br>\n";
        $csvHeader = "";
        $csvData = "";
        $firstField = true;
        
        if ($csv) {
            $htmData .= "<table border=0 width=\"100%\" style=\"[email:contentStyle]\">\r\n";
            foreach ($this->fields as $field) {
                if ($field['isSeparator']) {
                    //
                    // This field is a form separator
                    // 
                    $txtData .= (!$firstField ? "\r\n" : "") . $field['label'] . "\r\n" . str_repeat("=", strlen($field['label'])) . "\r\n";
                    $htmData .= "<tr valign=\"top\"><td colspan=\"2\" style=\"" . (!$firstField ? "padding-top: 8px;" : "") . " border-bottom: 1px solid [email:bodySeparatorBorderColor];\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $field['label']) . "</b></td></tr>\r\n";
                } else {
                    //
                    // This field is a classic form field
                    // 
                    $label = ($field['label'] != "" ? $field['label'] . ": " : "");
                    if (is_array($field['value'])) {
                        $txtData .= $label . implode(", ", $field['value']) . "\r\n";
                        $htmData .= "<tr valign=\"top\"><td width=\"25%\"><b>" . $label . "</b></td><td>" . implode(", ", $field['value']) . "</td></tr>\r\n";
                        if ($csv) {
                            $csvHeader .= $field['label'] . ";";
                            $csvData .= implode(", ", $field['value']) . ";";
                        }
                    } else {                        
                        $txtData .= $label . $field['value'] . "\r\n";
                        // Is it an email?
                        if (preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' . '(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', $field['value'])) {
                            $htmData .= "<tr valign=\"top\"><td width=\"25%\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $label) . "</b></td><td><a href=\"mailto:" . $field['value'] . "\">". $field['value'] . "</a></td></tr>\r\n";
                        } else if (preg_match('/^http[s]?:\/\/[a-zA-Z0-9\.\-]{2,}\.[a-zA-Z]{2,}/', $field['value'])) {
                            // Is it an URL?
                            $htmData .= "<tr valign=\"top\"><td width=\"25%\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $label) . "</b></td><td><a href=\"" . $field['value'] . "\">". $field['value'] . "</a></td></tr>\r\n";
                        } else {
                            $htmData .= "<tr valign=\"top\"><td width=\"25%\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $label) . "</b></td><td>" . str_replace(array("\\'", '\\"'), array("'", '"'), $field['value']) . "</td></tr>\r\n";
                        }
                        if ($csv) {
                            $csvHeader .= str_replace(array("\\'", '\\"'), array("'", '"'), $field['label']) . ";";
                            $csvData .= str_replace(array("\\'", '\\"'), array("'", '"'), $field['value']) . ";";
                        }
                    }
                }
                $firstField = false;
            }
            $htmData .= "</table>\n";
        }
        
        return $ImMailer->send($from, $to, $subject, $txtData, $htmData);
    }
}




/**
 * Manage the redirect to different pages basin gon the user's language
 */
class LanguageRedirect {

	/**
	 * The associative array of language => page
	 * @var array
	 */
	private $languages;
	private $defaultUrl;

	public function __construct()
	{
		$this->languages = array();
		$this->defaultUrl = "";
	}

	/**
	 * Add a redirect rule
	 * 
	 * @param String $langId      The language id
	 * @param String $redirectUrl The url to wich the user is redirect to
	 *
	 * @return void
	 */
	public function addRedirectRule($langId, $redirectUrl)
	{
		if (strlen($langId) > 0 && strlen($redirectUrl) > 0) {
			$parsedLang = $this->parseLanguage($langId);
			if ($parsedLang !== false) {
				$this->languages[] = array(
					"language" => $parsedLang,
					"url"      => $redirectUrl
				);
			}
		}
	}

	/**
	 * Set the default redirect URL used when the set languages are not enough.
	 * 
	 * @param String $url The default url
	 *
	 * @return void
	 */
	public function setDefaultUrl($url)
	{
		$this->defaultUrl = $url;
	}

	/**
	 * Get the redirect URL basing on the current language and the rules set
	 * 
	 * @return String The URL
	 */
	public function getRedirectUrl()
	{
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$detectedLangs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$matches = array();

			// Look for all the languages that match as primary
			foreach ($detectedLangs as $lang) {
				$lang = $this->parseLanguage($lang);
				// Look for a similar language
				foreach ($this->languages as $entry) {
					if (strtolower($lang["primary"]) == strtolower($entry["language"]["primary"])) {
						// If this is the first language that matches as primary, just keep it
						// Or if this language is not the first to match, make sure that matches with the other ones already in the list
						if (count($matches) == 0 || $matches[0]["language"]["primary"] == $entry["language"]["primary"]) {
							$matches[] = $entry;
						}
					}
				}
			}

			// Look if there are some specific matches
			if (count($matches) > 0) {
				foreach ($detectedLangs as $lang) {
					$lang = $this->parseLanguage($lang);
					// Look for the very same language
					foreach ($matches as $entry) {
						if (strtolower($lang["primary"]) == strtolower($entry["language"]["primary"]) && strtolower($lang["subtag"]) == strtolower($entry["language"]["subtag"])) {
							return $entry["url"];
						}
					}
				}
				return $matches[0]["url"];
			}
		}

		// Or return the default url
		return $this->defaultUrl;
	}

	/**
	 * Execute the redirect basing on the specified languages
	 * 
	 * @return bool True if the redirect is being made
	 */
	public function redirect()
	{
		$url = $this->getRedirectUrl();
		if (strlen($url)) {
			echo "<script type=\"text/javascript\">window.top.location.href='" . $url . "';</script>";
			return true;
		}
		return false;
	}

	/**
	 * Parse a string (RFC 2616) and convert it to a language array
	 * @param  String $languageString The string to be parsed
	 * @return array                  An associative array containing the language data or false on error
	 */
	private function parseLanguage($languageString)
	{
		$pattern = '/^(?P<primarytag>[a-zA-Z]{2,8})' . '(?:-(?P<subtag>[a-zA-Z]{2,8}))?(?:(?:;q=)' . '(?P<quantifier>\d\.\d))?$/';		
		if (preg_match($pattern, $languageString, $splits)) {
			return array(
				"primary"    => $splits["primarytag"],
				"subtag"     => isset($splits["subtag"]) ? $splits["subtag"] : "",
				"quantifier" => isset($splits["quantifier"]) ? $splits["quantifier"] : ""
			);
		}
		return false;
	}

}



/**
 * @summary
 * Provides a set of useful methods for managing the private area, the users and the accesses.
 * To use it, you must include __x5engine.php__ in your code.
 * 
 * @description Create a new ImDb Object
 *
 * @class
 * @constructor
 */
class imPrivateArea
{

    static $instance = false;

    /**
     * Get the instance of the private area
     *
     * @static
     * 
     * @return {imPrivateArea} The instance of a private area
     */
    static function getInstance() {
        if (!self::$instance) {
            self::$instance = new imPrivateArea();
        }
        return self::$instance;
    }

    var $session_type;
    var $session_uname;
    var $session_uid;
    var $session_gids;
    var $session_page;
    var $cookie_name;
    var $salt;
    var $admin_email;
    var $db = false;
    var $db_table;

    function __construct()
    {
        global $imSettings;

        $this->session_type      = "im_access_utype";
        $this->session_uname     = "im_access_uname";
        $this->session_real_name = "im_access_real_name";
        $this->session_page      = "im_access_request_page";
        $this->session_uid       = "im_access_uid";
        $this->session_gids      = "im_access_gids";
        $this->cookie_name       = "im_access_cookie_uid";
        $this->salt              = $imSettings['general']['salt'];
    }

    /**
     * Encode the string
     *
     * @ignore
     * 
     * @param  string $string The string to encode
     * @param  $key The encryption key
     * 
     * @return string    The encoded string
     */
    function _encode($s, $k)
    {
        $r = array();
        for($i = 0; $i < strlen($s); $i++)
            $r[] = ord($s[$i]) + ord($k[$i % strlen($k)]);

        // Try to encode it using base64
        if (function_exists("base64_encode") && function_exists("base64_decode"))
            return base64_encode(implode('.', $r));

        return implode('.', $r);
    }

    /**
     * Decode the string
     *
     * @ignore
     * 
     * @param  string $s The string to decode
     * @param  string $k The encryption key
     * 
     * @return string    The decoded string
     */
    function _decode($s, $k)
    {

        // Try to decode it using base64
        if (function_exists("base64_encode") && function_exists("base64_decode"))
            $s = base64_decode($s);

        $s = explode(".", $s);
        $r = array();
        for($i = 0; $i < count($s); $i++)
            $r[$i] = chr($s[$i] - ord($k[$i % strlen($k)]));
        return implode('', $r);
    }

    /**
     * Login a user with username and password
     * 
     * @param {string} $uname Username
     * @param {string} $pwd   Password
     *
     * @return {int} An error code:
     *                  -5 if the user email is not validated,
     *                  -2 if the username or password are invalid,
     *                  -1 if there's a db error,
     *                  0 if the process exits correctly
     */
    function login($uname, $pwd)
    {
        global $imSettings;

        if (!strlen($uname) || !strlen($pwd))
            return -2;

        // Check if the user exists in the hardcoded file
        if (isset($imSettings['access']['users'][$uname]) && $imSettings['access']['users'][$uname]['password'] == $pwd) {
            $this->_setSession(
                "1",
                $imSettings['access']['users'][$uname]['id'],
                $imSettings['access']['users'][$uname]['groups'],
                $uname,
                $imSettings['access']['users'][$uname]['name']
            );
            return 0;
        }
        // WSXELE-467
        // If the DB is "false" it means that the db was not set up in WSX5.
        // If the DB itself is not accessible, the user receives another error when the DB data is set in this class
        if (!$this->db)
            return -2;
        // Check if the user exists in the DB and it's validated
        $user = $this->db->query("SELECT * FROM `" . $this->db_table . "` WHERE BINARY `username`='" . $this->db->escapeString($uname) . "' AND BINARY `password`='" . $this->db->escapeString($pwd) . "'");
        if (!is_array($user) || !count($user))
            return -2;
        if (!$user[0]['validated'])
            return -5;
        $this->_setSession("0", $user[0]['id'], isset($imSettings['access']['webregistrations_gid']) ? array($imSettings['access']['webregistrations_gid']) : array(), $user[0]['username'], $user[0]['realname']);
        return 0;
    }

    /**
     * Set the session after the login
     *
     * @ignore
     *
     * @param {string} $type "0" or "1"
     * @param {string} $uid
     * @param {array}  $gids
     * @param {string} $uname   
     * @param {string} $realname
     *
     * @return {Void}
     */
    function _setSession($type, $uid, $gids, $uname, $realname)
    {
        @session_regenerate_id();
        $_SESSION[$this->session_type]      = $this->_encode($type, $this->salt);
        $_SESSION[$this->session_uid]       = $this->_encode($uid, $this->salt);
        $_SESSION[$this->session_uname]     = $this->_encode($uname, $this->salt);
        $_SESSION[$this->session_real_name] = $this->_encode($realname, $this->salt);
        $_SESSION[$this->session_gids]      = $gids;
        $_SESSION['HTTP_USER_AGENT']        = md5($_SERVER['HTTP_USER_AGENT'] . $this->salt);
        @setcookie($this->cookie_name, $this->_encode($uid, $this->salt), 0, "/"); // Expires when the browser is closed
    }

    /**
     * Logout a user
     *
     * @return {Void}
     */
    function logout()
    {
        $_SESSION[$this->session_type]  = "";
        $_SESSION[$this->session_uname] = "";
        $_SESSION[$this->session_uid]   = "";
        $_SESSION[$this->session_page]  = "";
        $_SESSION[$this->session_gids]  = array();
        $_SESSION['HTTP_USER_AGENT']    = "";
        @setcookie($this->cookie_name, "", time() - 3600, "/");
        $_COOKIE[$this->cookie_name]    = "";
    }

    /**
     * Save the current page as the referer
     *
     * @return {Void}
     */
    function savePage()
    {
        global $imSettings;
        $url = $_SERVER['REQUEST_URI'];
        $_SESSION[$this->session_page] = $this->_encode($url, $this->salt);
    }

    /**
     * Return the referer page name (the one which caused the user to land on the login page).
     * 
     * @method getSavedPage
     *
     * @return {mixed} The name of the page or false if no referer is available.
     */
    function getSavedPage()
    {
        global $imSettings;
        if (isset($_SESSION[$this->session_page]) && $_SESSION[$this->session_page] != "")
            return $this->_decode($_SESSION[$this->session_page], $this->salt);
        return false;
    }

    /**
     * Use whoIsLogged instead
     * @deprecated
     * @return {mixed}
     */
    function who_is_logged() {
        return $this->whoIsLogged();
    }

    /**
     * Get an array of data about the logged user
     *
     * @return {mixed} An array containing the data of the current logged user or false if no user is logged.
     */
    function whoIsLogged()
    {
        global $imSettings;
        if (isset($_SESSION[$this->session_uname]) && $_SESSION[$this->session_uname] != "" && isset($_SESSION[$this->session_uname])) {
            $uname = $this->_decode($_SESSION[$this->session_uname], $this->salt);
            return array(
                "username" => $uname,
                "uid"      => $this->_decode($_SESSION[$this->session_uid], $this->salt),
                "realname" => $this->_decode($_SESSION[$this->session_real_name], $this->salt),
                "groups"   => $_SESSION[$this->session_gids]
            );
        }
        return false;
    }

    /**
     * Check if the logged user can access to a specific page.
     * The page is provided using its page id.
     * 
     * @param {int} $page The page id. You can retrieve the page id from the file x5settings.php.
     *
     * @return {int} 0 if the current user can access the page, 
     *                 -2 if the XSS security checks are not met
     *                 -3 if the user is not logged
     *                 -4 if the user is still not validated
     *                 -8 if the user cannot access the page
     */
    function checkAccess($page)
    {
        global $imSettings;

        //
        // The session can live only in the same browser
        //

        if (!isset($_SESSION[$this->session_type]) || $_SESSION[$this->session_type] == "" || !isset($_SESSION[$this->session_uid]) || $_SESSION[$this->session_uid] == "")
            return -3;

        if (!isset($_SESSION['HTTP_USER_AGENT']) || $_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'] . $this->salt))
            return -2;

        if ($this->_decode($_SESSION[$this->session_type], $this->salt) == "0" && $_SESSION[$this->session_uid] != "") {
            if (!@in_array($page, $imSettings['access']['waitingpages']))
                return -4;
            return 0;
        }
        $uid = $this->_decode($_SESSION[$this->session_uid], $this->salt);
        if (!@in_array($uid, $imSettings['access']['pages'][$page]) && !@in_array($uid, $imSettings['access']['admins']))
            return -8; // The active user cannot access to this page
        return 0;
    }

    /**
     * Get the current user's landing page.
     *
     * @return {mixed} The filename of the user's landing page or false if the user is not logged.
     */
    function getLandingPage()
    {
        global $imSettings;
        if (!isset($_SESSION[$this->session_type]) || !isset($_SESSION[$this->session_uname]) || $_SESSION[$this->session_uname] === '' || !isset($_SESSION[$this->session_uid]) || $_SESSION[$this->session_uid] === '')
            return false;

        if ($this->_decode($_SESSION[$this->session_type], $this->salt) == "0")
            return $imSettings['access']['entrancepage'];

        return $imSettings['access']['users'][$this->_decode($_SESSION[$this->session_uname], $this->salt)]['page'];
    }

    /**
     * Convert a status code to a text message
     *
     * @param {int} $code The error code
     * 
     * @return {string} The text message related to the provided error code
     */
    function messageFromStatusCode($code)
    {
        switch ($code) {

            // Error
            case -8 : return l10n("private_area_account_not_allowed", "Your account is not allowed to access the selected page");
            case -7 : return l10n("private_area_lostpassword_error", "We cannot find your data.");
            case -6 : return l10n("private_area_user_already_exists", "The user already exists.");
            case -5 : return l10n("private_area_not_validated", "Your account is not yet validated.");
            case -4 : return l10n("private_area_waiting", "Your account is not yet active.");
            //case -3 : return l10n("private_area_not_allowed", "A login is required to access this page.");
            case -2 : return l10n("private_area_login_error", "Wrong username or password.");
            case -1 : return l10n("private_area_generic_error", "Generic error.");

            // Success
            case 2  : return l10n('private_area_validation_sent', 'We sent you a validation email.');
            case 3  : return l10n('private_area_registration_success', 'You are now registered.');
            case 4  : return l10n('private_area_lostpassword_success', 'We sent you an email with your password.');

            default : return "";
        }
    }

    /**
     * Redirect in a session safe mode. IIS requires this.
     *
     * @ignore
     * 
     * @param  {string} $to The redirect URL.
     * 
     * @return {void}
     */
    function sessionSafeRedirect($to)
    {
        exit('<!DOCTYPE html><html lang="it" dir="ltr"><head><title>Loading...</title><meta http-equiv="refresh" content="1; url=' . $to . '"></head><body><p style="text-align: center;">Loading...</p></body></html>');
    }

    /**
     * Get the data about a user.
     * 
     * @param  {string} $id The username
     * 
     * @return {mixed} The user's data (As associative array) or null if the user is not found.
     * The associative array contains the following keys: id, ts, ip, username, password, realname, email, key, validated, groups
     */
    function getUserByUsername($username)
    {
        global $imSettings;
        
        // Search in the file
        if (isset($imSettings['access']['users'][$username])) {
            $user = $imSettings['access']['users'][$username];
            return array(
                "id"        => $user['id'],
                "ts"        => "",
                "ip"        => "",
                "username"  => $username,
                "password"  => $user['password'],
                "realname"  => $user['name'],
                "email"     => $user['email'],
                "key"       => "",
                "validated" => true,
                "groups"    => $user['groups']
            );
        }
        // Search in the DB
        $res = $this->db->query("SELECT * FROM `" . $this->db_table . "` WHERE `username`='" . $this->db->escapeString($username) . "'");
        if (is_array($res) && count($res) > 0) {
            $user = $res[0];
            return array(
                "id"        => $user['id'],
                "ts"        => $user['ts'],
                "ip"        => $user['ip'],
                "username"  => $user['username'],
                "password"  => $user['password'],
                "realname"  => $user['realname'],
                "email"     => $user['email'],
                "key"       => $user['key'],
                "validated" => $user['validated'],
                "groups"    => array($imSettings['access']['webregistrations_gid'])
            );
        }
        return null;
    }

    
    /**
     * Get the user data relative to a set of user ids.
     * This method is available only in the **Professional** edition.
     * 
     * @param  {array}  $ids The array of user ids.
     * 
     * @return {array}  An array of associative arrays containing the users' data.
     * The array keys are: id, ts, ip, username, password, realname, email, key, validated
     */
    function getUsersById($ids = array())
    {
        if (is_string($ids)) {
            if (strlen($ids))
                $ids = array($ids);
            else
                $ids = array();
        }

        $res = $this->db->query("SELECT * FROM `" . $this->db_table . "`" . (count($ids) ? " WHERE `id` IN (" . implode(",", $ids) . ")" : ""));
        $users = array();
        if (is_array($res)) {
            foreach ($res as $user) {
                $users[] = array(
                    "id"        => $user['id'],
                    "ts"        => $user['ts'],
                    "ip"        => $user['ip'],
                    "username"  => $user['username'],
                    "password"  => $user['password'],
                    "realname"  => $user['realname'],
                    "email"     => $user['email'],
                    "key"       => $user['key'],
                    "validated" => $user['validated']
                );
            }
        }
        return $users;
    }
    
    /**
     * Setup the db connection.
     * This method is only available in the **Professional edition**.
     * 
     * @param {string} $host
     * @param {string} $username
     * @param {string} $password
     * @param {string} $dbname
     * @param {string} $dbtable
     *
     * @return {Void}
     */
    function setDBData($host, $username, $password, $dbname, $dbtable)
    {
        $this->db = new ImDb($host, $username, $password, $dbname);
        $this->db_table = $dbtable;
        if (!$this->db->testConnection())
            die("Unable to connect to DB");
    }
    
    /**
     * Get an encoded JSON list of the waiting users' data
     *
     * @ignore
     *
     * @param  string $encKey The encryption key used to encode the json data
     * 
     * @return string
     */
    function getWaitingUsers($encKey = "")
    {
        if (!$this->db || !$this->db->tableExists($this->db_table))
            return "";
        $users = array(
            "extra" => array("timestamp" => date("Y-m-d H:i:s")),
            "users" => $this->getUsersById()
        );
        return json_encode($users);
    }

    /**
     * Validate the waiting users listed in $ids. It must be an array of DB ids.
     * This method is only available in the **Professional edition**.
     * 
     * @param  array  $dbid
     * 
     * @return bool
     */
    function validateWaitingUserById($dbids = array())
    {
        if (!is_array($dbids))
            $dbids = array($dbids);
        if (!count($dbids))
            return false;
        $this->db->query("UPDATE `" . $this->db_table . "` SET `validated`=1, ts=ts WHERE `validated`=0 AND `id` IN (" . implode(",", $this->db->escapeString($dbids)) . ")");
        return $this->db->affectedRows() > 0;
    }

    /**
     * Validate the waiting users listed in $keys. It must be an array of DB keys.
     * This method is only available in the **Professional edition**.
     * 
     * @param  array $keys
     * @param  boolean $login Automatically login the user if validation is succesful
     * 
     * @return booleal
     */
    function validateWaitingUserByKey($keys = array(), $login = false)
    {
        $user = false;
        if (!is_array($keys))
            $keys = array($keys);

        if ($login && count($keys) == 1) {
            $user = $this->db->query("SELECT `username`, `password` FROM `" . $this->db_table . "` WHERE `key`='" . $this->db->escapeString($keys[0]) . "'");
            if (is_bool($user))
                return false;
            $user = $user[0];
        }
        $this->db->query("UPDATE `" . $this->db_table . "` SET `validated`=1, `ts`=NOW(), `ip`='" . $this->db->escapeString($_SERVER['REMOTE_ADDR']) . "' WHERE `validated`=0 AND `key` IN ('" . implode("','", $this->db->escapeString($keys)) . "')");
        if ($user && $this->db->affectedRows())
            return $this->login($user['username'], $user['password']) == 0;
        return $this->db->affectedRows() > 0;
    }

    /**
     * Remove the remaining waiting users.
     * This method is only available in the **Professional edition**.
     *
     * @param  string $ts          Remove only the users registered before this timestamp.
     * @param  array  $usersToKeep Remove all the users but keep the ones listed in this array
     * 
     * @return void
     */
    function removeWaitingUsers($ts, $usersToKeep = array())
    {
        if (!$this->db || !$this->db->tableExists($this->db_table))
            return;
        $query = "DELETE FROM `" . $this->db_table . "` WHERE `ts`<='" . $this->db->escapeString($ts) . "'";
        if (!is_array($usersToKeep))
            $usersToKeep = array($usersToKeep);
        if (count($usersToKeep))
            $query .= " AND id NOT IN (" . implode(",", $this->db->escapeString($usersToKeep)) . ")";
        $this->db->query($query);
    }

    /**
     * Get the validation key of user $dbid.
     * This method is only available in the **Professional edition**.
     * 
     * @param  string $dbid
     * 
     * @return string The validation key
     */
    function getKeyFromId($dbid)
    {
        $key = $this->db->query("SELECT `key` FROM `" . $this->db_table . "` WHERE `id`=" . (int)$dbid);
        if (!is_bool($key) && count($key))
            return $key[0]['key'];
        return false;
    }

    /**
     * Create the users table if it doesn't exist
     * This method is only available in the **Professional edition**.
     * 
     * @return void
     */
    function createUsersTable()
    {
        if (!$this->db || $this->db->tableExists($this->db_table))
            return;
        $this->db->createTable(
            $this->db_table,
            array(
                "id"        => array('type' => 'INT(11)', 'primary' => true, 'auto_increment' => true),
                "ts"        => array('type' => 'TIMESTAMP'),
                "ip"        => array('type' => 'VARCHAR(16)'),
                "username"  => array('type' => 'TEXT'),
                "password"  => array('type' => 'TEXT'),
                "realname"  => array('type' => 'TEXT'),
                "email"     => array('type' => 'TEXT'),
                "key"       => array('type' => 'VARCHAR(32)'),
                "validated" => array('type' => 'INT(1)')
            )
        );
    }

    /**
     * Register a new user in the database.
     * This method is only available in the **Professional edition**.
     * 
     * @param string $username
     * @param string $password
     * @param string $email   
     * @param string $validated
     * 
     * @return {int} the user's ID or the error number (-1: user already exists, -2: generic error)
     */
    function registerNewUser($username, $password, $realname, $email, $validated)
    {
        global $imSettings;

        if (!$this->db)
            return -1;
        if (!strlen($username) || !strlen($password) || !strlen($email))
            return -1;

        $this->createUsersTable();

        // Check if the user already exists in the hardcoded file
        if (isset($imSettings['access']['users'][$username]))
            return -6;

        // Check if the user already exists in the DB as validated user
        if (count($this->db->query("SELECT  `username` FROM `" . $this->db_table . "` WHERE `username`='" . $this->db->escapeString($username) . "' AND `validated`='1'")))
            return -6;

        // Check if the user already exists in the DB as not validated user
        if (count($res = $this->db->query("SELECT  `id`, `username` FROM `" . $this->db_table . "` WHERE `username`='" . $this->db->escapeString($username) . "' AND `validated`='0'")))
            return $res[0]['id'];

        // Create the user's record
        $this->db->query("INSERT INTO `" . $this->db_table . "` (`ts`, `ip`, `username`, `password`, `realname`, `email`, `key`, `validated`) VALUES (" .
            "'" . date("Y-m-d H:i:s") . "'," .
            "'" . $this->db->escapeString($_SERVER['REMOTE_ADDR']) . "'," .
            "'" . $this->db->escapeString($username) . "'," .
            "'" . $this->db->escapeString($password) . "'," .
            "'" . $this->db->escapeString($realname) . "'," .
            "'" . $this->db->escapeString($email) . "'," .
            "'" . md5($username . $password . date("U") . rand(1000, 9999)) . "'," .
            "'" . $this->db->escapeString($validated) . "'" .
        ")");
        return $this->db->lastInsertId();
    }

    /**
     * Notify the registration of a new user to the site's owner.
     * This method is only available in the **Professional edition**.
     * 
     * @param  {int}    $id      The user id
     *
     * @return {Void}
     */
    function sendNotificationEmail($id)
    {
        global $ImMailer;
        global $imSettings;
        $html = "";

        $user = $this->getUsersById($id);
        $user = $user[0];
        if (is_bool($user) || !count($user))
            return;

        // ---------------------------------------------------
        //  WSXELE-898: Find the correct email sender address
        $from = $this->admin_email;
        if ($imSettings['general']['use_common_email_sender_address']) {
            $from = $imSettings['general']['common_email_sender_addres'];
        } else if (strlen($user['email'])) {
            $from = $user['email'];
        }
        // ---------------------------------------------------

        $subject = str_replace("[FIELD]", $imSettings['general']['url'], l10n("private_area_newregistration_subject", "A new user registered to your private area at [FIELD]"));
        $html .= nl2br(str_replace(
                array("[FIELD]", "\n"),
                array($imSettings['general']['url'], "<br />\n"),
                l10n("private_area_newregistration_body", "Here's his data.")
            )) . "<br /><br />\n\n";
        $html .= "<b>" . l10n("private_area_realname", "Name") . ":</b> " . $user['realname'] . "<br />\n";
        $html .= "<b>" . l10n("private_area_username", "Username") . ":</b> " . $user['username'] . "<br />\n";
        $html .= "<b>" . l10n("private_area_email", "Email") . ":</b> " . $user['email'] . "<br />\n";
        $html .= "<b>" . l10n("private_area_ip", "IP") . ":</b> " . $user['ip'] . "<br />\n";
        $html .= "<b>" . l10n("private_area_ts", "Time") . ":</b> " . $user['ts'] . "<br />\n";

        $ImMailer->send($from, $this->admin_email, $subject, strip_tags($html), $html);
    }

    /**
     * Send the validation email for the user indentified by $id.
     * This method is only available in the **Professional edition**.
     *
     * @param  {string} $id
     *
     * @return {Void}
     */
    function sendValidationEmail($dbid)
    {
        global $ImMailer;
        global $imSettings;

        $html = "";

        $user = $this->getUsersById($dbid);
        $user = $user[0];
        if (is_bool($user) || !count($user))
            return;

        // ---------------------------------------------------
        //  WSXELE-898: Find the correct email sender address
        $from = $this->admin_email;
        if ($imSettings['general']['use_common_email_sender_address']) {
            $from = $imSettings['general']['common_email_sender_addres'];
        }
        // ---------------------------------------------------

        $subject = str_replace("[FIELD]", $imSettings['general']['url'], l10n("private_area_validation_subject", "Validate your account on [FIELD]"));
        $html .= l10n("private_area_validation_body", "Click here to validate your account:") . " ";
        $html .= "<a href=\"" . $imSettings['general']['url'] . "/imlogin.php?validate=" . $user['key'] . "\">";
        $html .= $imSettings['general']['url'] . "/imlogin.php?validate=" . $user['key'];
        $html .= "</a>";

        $ImMailer->send($from, $user['email'], $subject, strip_tags($html), $html);
    }

    /**
     * If the user has provided an email address, he receives a message with his password.
     * If the user's email is not available, the request is notified to the site's admin.
     * This method is only available in the **Professional edition**.
     * 
     * @param  {string} $data The user's email or username
     * 
     * @return {boolean} True if the email is sent correctly.
     */
    function sendLostPasswordEmail($data)
    {
        global $ImMailer;
        global $imSettings;

        $username = false;
        $password = false;
        $emailTo = false;

        $query = "SELECT `email`, `username`, `password` FROM `" . $this->db_table . "` ";
        $query .= "WHERE username='" . $this->db->escapeString($data) ."' OR email='" . $this->db->escapeString($data) ."'";
        $user = $this->db->query($query);
        if (!is_bool($user) && count($user)) {
            $emailTo = $user[0]['email'];
            $username = $user[0]['username'];
            $password = $user[0]['password'];
        } else {
            foreach ($imSettings['access']['users'] as $uname => $user) {
                // If the email identify a user, send the message to him, otherwise send a message to the admin
                if ($uname == $data || $user['email'] == $data) {
                    $emailTo = strlen($user['email']) ? $user['email'] : false;
                    $username = $uname;
                    $password = $user['password'];
                    break;
                }
            }
        }

        if (!$username || !$password)
            return false;

        // ---------------------------------------------------
        //  WSXELE-898: Find the correct email sender address
        $from = $this->admin_email;
        if ($imSettings['general']['use_common_email_sender_address']) {
            $from = $imSettings['general']['common_email_sender_addres'];
        }
        // ---------------------------------------------------

        if (!$emailTo) {
            // Send an email to the admin
            $emailTo = $from;
            $subject = str_replace("[FIELD]", $imSettings['general']['url'], l10n("private_area_password_recovery_subject_admin", "Password recovery request from [FIELD]"));
            $html = nl2br(str_replace(
                array("[FIELD]", "[URL]", "\n"),
                array($username, $imSettings['general']['url'], "<br />\n"),
                l10n("private_area_password_recovery_body_admin", "The user [FIELD] on [URL] wants to recover his own username and password.")
            ));
        } else {
            // Send an email to the user
            $subject = str_replace("[FIELD]", $imSettings['general']['url'], l10n("private_area_password_recovery_subject_user", "Password recovered for [FIELD]"));
            $html = l10n("private_area_password_recovery_body_user", "Here is your account data: ") . "<br /><br />\n\n";
            $html .= "<b>" . l10n("private_area_username", "Username") . ":</b> " . $username . "<br />\n";
            $html .= "<b>" . l10n("private_area_password", "Password") . ":</b> " . $password . "<br />\n";
        }

        $ImMailer->send($from, $emailTo, $subject, strip_tags($html), $html);
        return true;
    }

}

/**
 * Contains the methods used by the search engine
 * @access public
 */
class imSearch {

    var $scope;
    var $page;
    var $results_per_page;

    function __construct()
    {
        $this->setScope();
        $this->results_per_page = 10;
    }

    /**
     * Loads the pages defined in search.inc.php  to the search scope
     * @access public
     */
    function setScope()
    {
        global $imSettings;
        $scope = $imSettings['search']['general']['defaultScope'];

        // Logged users can search in their private pages
        $pa = new imPrivateArea();
        if ($user = $pa->who_is_logged()) {
            foreach ($imSettings['search']['general']['extendedScope'] as $key => $value) {
                if (in_array($user['uid'], $imSettings['access']['pages'][$key]))
                    $scope[] = $value;
            }
        }

        $this->scope = $scope;
    }

    /**
     * Do the pages search
     * @access public
     * @param queries The search query (array)
     */
    function searchPages($queries)
    {
        
        global $imSettings;
        $html = "";
        $found_content = array();
        $found_count = array();

        if (is_array($this->scope)) {
            foreach ($this->scope as $filename) {
                $count = 0;
                $weight = 0;
                $file_content = @implode("\n", file($filename));
                // Replace the nonbreaking space with a white space
                // to avoid that is converted to a 196+160 UTF8 char
                $file_content = str_replace("&nbsp;", " ", $file_content);
                if (function_exists("html_entity_decode"))
                    $file_content = html_entity_decode($file_content, ENT_COMPAT, 'UTF-8');

                // Remove the page menu
                while (stristr($file_content, "<div id=\"imPgMn\"") !== false) {
                    $style_start = imstripos($file_content, "<div id=\"imPgMn\"");
                    $style_end = imstripos($file_content, "</div", $style_start);
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }

                // Remove the breadcrumbs
                while (stristr($file_content, "<div id=\"imBreadcrumb\"") !== false) {
                    $style_start = imstripos($file_content, "<div id=\"imBreadcrumb\"");
                    $style_end = imstripos($file_content, "</div", $style_start);
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }

                // Remove CSS
                while (stristr($file_content, "<style") !== false) {
                    $style_start = imstripos($file_content, "<style");
                    $style_end = imstripos($file_content, "</style", $style_start);
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }

                // Remove JS
                while (stristr($file_content, "<script") !== false) {
                    $style_start = imstripos($file_content, "<script");
                    $style_end = imstripos($file_content, "</script", $style_start);
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }

                // Remove PHP
                while (stristr($file_content, "<?php") !== false) {
                    $style_start = imstripos($file_content, "<?php");
                    $style_end = imstripos($file_content, "?>", $style_start) !== false ? imstripos($file_content, "?>", $style_start) + 2 : strlen($file_content);
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }
                $file_title = "";

                // Replace the dynamic objects with their content
                if (is_array($imSettings['search']['dynamicobjects'])) {
                    foreach ($imSettings['search']['dynamicobjects'] as $id => $object) {
                        // Only if the object is in the current scope
                        if ($object['Page'] != $filename)
                            continue;
                        // Load the object's content
                        $dynobj = new DynamicObject($object['ObjectId']);
                        $dynobj->setDefaultText($object['DefaultText']);
                        if (isset($object['Folder'])) {
                            // Load from file
                            $dynobj->loadFromFile(pathCombine(array($imSettings['general']['public_folder'], $object['Folder'])));
                        } else if (isset($object['Database']) && isset($object['Table'])) {
                            // Load from db
                            $db = getDbData($object['Database']);
                            $dynobj->loadFromDb($db['host'], $db['user'], $db['password'], $db['database'], $object['Table']);
                        }
                        // Replace the content
                        $needle_start = "<!-- search-tag " . $object['ObjectId'] . " start -->";
                        $needle_end = "<!-- search-tag " . $object['ObjectId'] . " end -->";
                        $find_start = strpos($file_content, $needle_start);
                        $find_end = strpos($file_content, $needle_end) + strlen($needle_end);
                        $file_content = substr($file_content, 0, $find_start) . $dynobj->getContent() . substr($file_content, $find_end);
                    }
                }
                // Get the title of the page
                preg_match('/\<title\>([^\<]*)\<\/title\>/', $file_content, $matches);
                if (count($matches) > 1)
                    $file_title = $matches[1];
                else {
                    preg_match('/\<h2\>([^\<]*)\<\/h2\>/', $file_content, $matches);
                    if (count($matches) > 1)
                        $file_title = $matches[1];
                }

                if ($file_title != "") {
                    foreach ($queries as $query) {
                        $title = imstrtolower($file_title);
                        while (($title = stristr($title, $query)) !== false) {
                            $weight += 5;
                            $count++;
                            $title = substr($title, strlen($query));
                        }
                    }
                }

                // Get the keywords
                preg_match('/\<meta name\=\"keywords\" content\=\"([^\"]*)\" \/>/', $file_content, $matches);
                if (count($matches) > 1) {
                    $keywords = $matches[1];
                    foreach ($queries as $query) {
                        $tkeywords = imstrtolower($keywords);
                        while (($tkeywords = stristr($tkeywords, $query)) !== false) {
                            $weight += 4;
                            $count++;
                            $tkeywords = substr($tkeywords, strlen($query));
                        }
                    }
                }

                // Get the description
                preg_match('/\<meta name\=\"description\" content\=\"([^\"]*)\" \/>/', $file_content, $matches);
                if (count($matches) > 1) {
                    $keywords = $matches[1];
                    foreach ($queries as $query) {
                        $tkeywords = imstrtolower($keywords);
                        while (($tkeywords = stristr($tkeywords, $query)) !== false) {
                            $weight += 3;
                            $count++;
                            $tkeywords = substr($tkeywords, strlen($query));
                        }
                    }
                }

                // Remove the page title from the result
                while (stristr($file_content, "<h2") !== false) {
                    $style_start = imstripos($file_content, "<h2");
                    $style_end = imstripos($file_content, "</h2", $style_start);
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }

                $page_pos = strpos($file_content, "<div id=\"imContent\">") + strlen("<div id=\"imContent\">");
                $page_end = strpos($file_content, "<div id=\"imBtMn\">");
                if ($page_end == false)
                    $page_end = strpos($file_content, "</body>");
                $file_content = strip_tags(substr($file_content, $page_pos, $page_end-$page_pos));
                $t_file_content = imstrtolower($file_content);

                foreach ($queries as $query) {
                    $file = $t_file_content;
                    while (($file = stristr($file, $query)) !== false) {
                        $count++;
                        $weight++;
                        $file = substr($file, strlen($query));
                    }
                }

                if ($count > 0) {
                    $found_count[$filename] = $count;
                    $found_weight[$filename] = $weight;
                    $found_content[$filename] = $file_content;
                    if ($file_title == "")
                        $found_title[$filename] = $filename;
                    else
                        $found_title[$filename] = $file_title;
                }
            }
        }

        if (count($found_count)) {
            arsort($found_weight);
            $i = 0;
            foreach ($found_weight as $name => $weight) {
                $count = $found_count[$name];
                $i++;
                if (($i > $this->page*$this->results_per_page) && ($i <= ($this->page+1)*$this->results_per_page)) {
                    $title = strip_tags($found_title[$name]);
                    $file = $found_content[$name];
                    $file = strip_tags($file);
                    $ap = 0;
                    $filelen = strlen($file);
                    $text = "";
                    for ($j=0; $j<($count > 6 ? 6 : $count); $j++) {
                        $minpos = $filelen;
                        $word = "";
                        foreach ($queries as $query) {
                            if ($ap < $filelen && ($pos = strpos(strtoupper($file), strtoupper($query), $ap)) !== false) {
                                if ($pos < $minpos) {
                                    $minpos = $pos;
                                    $word = $query;
                                }
                            }
                        }
                        $prev = explode(" ", substr($file, $ap, $minpos-$ap));
                        if (count($prev) > ($ap > 0 ? 9 : 8))
                            $prev = ($ap > 0 ? implode(" ", array_slice($prev, 0, 8)) : "") . " ... " . implode(" ", array_slice($prev, -8));
                        else
                            $prev = implode(" ", $prev);
                        if (strlen($word)) {
                            $text .= $prev . "<strong>" . substr($file, $minpos, strlen($word)) . "</strong>";
                            $ap = $minpos + strlen($word);
                        }
                    }
                    $next = explode(" ", substr($file, $ap));
                    if (count($next) > 9)
                        $text .= implode(" ", array_slice($next, 0, 8)) . "...";
                    else
                        $text .= implode(" ", $next);
                    $text = str_replace("|", "", $text);
                    $text = str_replace("<br />", " ", $text);
                    $text = str_replace("<br>", " ", $text);
                    $text = str_replace("\n", " ", $text);
                    $text = str_replace("\t", " ", $text);
                    $text = trim($text);
                    $html .= "<div class=\"imSearchPageResult\"><h3><a class=\"imCssLink\" href=\"" . $name . "\">" . strip_tags($title, "<b><strong>") . "</a></h3>" . strip_tags($text, "<b><strong>") . "<div class=\"imSearchLink\"><a class=\"imCssLink\" href=\"" . $name . "\">" . $imSettings['general']['url'] . "/" . $name . "</a></div></div>\n";
                }
            }
            $html = preg_replace_callback('/\\s+/', create_function('$matches', 'return implode(\' \', $matches);'), $html);
            $html .= "<div class=\"imSLabel\">&nbsp;</div>\n";
        }

        return array("content" => $html, "count" => count($found_content));
    }

    function searchBlog($queries)
    {
        
        global $imSettings;
        $html = "";
        $found_content = array();
        $found_count = array();

        if (isset($imSettings['blog']) && is_array($imSettings['blog']['posts'])) {
            foreach ($imSettings['blog']['posts'] as $key => $value) {
                // WSXELE-799: Skip the post that are published in the future
                if ($value['utc_time'] > time()) {
                    continue;
                }
                $count = 0;
                $weight = 0;
                $filename = 'blog/index.php?id=' . $key;
                $file_content = $value['body'];
                // Rimuovo le briciole dal contenuto
                while (stristr($file_content, "<div id=\"imBreadcrumb\"") !== false) {
                    $style_start = imstripos($file_content, "<div id=\"imBreadcrumb\"");
                    $style_end = imstripos($file_content, "</div", $style_start);
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }

                // Rimuovo gli stili dal contenuto
                while (stristr($file_content, "<style") !== false) {
                    $style_start = imstripos($file_content, "<style");
                    $style_end = imstripos($file_content, "</style", $style_start);
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }
                // Rimuovo i JS dal contenuto
                while (stristr($file_content, "<script") !== false) {
                    $style_start = imstripos($file_content, "<script");
                    $style_end = imstripos($file_content, "</script", $style_start);
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }
                $file_title = "";

                // Rimuovo il titolo dal risultato
                while (stristr($file_content, "<h2") !== false) {
                    $style_start = imstripos($file_content, "<h2");
                    $style_end = imstripos($file_content, "</h2", $style_start);
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }

                // Conto il numero di match nel titolo
                foreach ($queries as $query) {
                    $t_count = @preg_match_all('/' . preg_quote($query, '/') . '/', imstrtolower($value['title']), $matches);
                    if ($t_count !== false) {
                        $weight += ($t_count * 4);
                        $count += $t_count;
                    }
                }

                // Conto il numero di match nei tag
                foreach ($queries as $query) {
                    if (in_array($query, $value['tag'])) {
                        $count++;
                        $weight += 4;
                    }
                }

                $title = "Blog &gt;&gt; " . $value['title'];

                // Cerco nel contenuto
                foreach ($queries as $query) {
                    $file = imstrtolower($file_content);
                    while (($file = stristr($file, $query)) !== false) {
                        $count++;
                        $weight++;
                        $file = substr($file, strlen($query));
                    }
                }

                if ($count > 0) {
                    $found_count[$filename] = $count;
                    $found_weight[$filename] = $weight;
                    $found_content[$filename] = $file_content;
                    $found_breadcrumbs[$filename] = "<div class=\"imBreadcrumb\" style=\"display: block; padding-bottom: 3px;\">" . l10n('blog_published_by') . "<strong> " . $value['author'] . " </strong>" . l10n('blog_in') . " <a href=\"blog/index.php?category=" . $value['category'] . "\" target=\"_blank\" rel=\"nofollow\">" . $value['category'] . "</a> &middot; " . $value['timestamp'] . "</div>";
                    if ($title == "")
                        $found_title[$filename] = $filename;
                    else
                        $found_title[$filename] = $title;
                }
            }
        }

        if (count($found_count)) {
            arsort($found_weight);
            $i = 0;
            foreach ($found_weight as $name => $weight) {
                $count = $found_count[$name];
                $i++;
                if (($i > $this->page*$this->results_per_page) && ($i <= ($this->page+1)*$this->results_per_page)) {
                    $title = strip_tags($found_title[$name]);
                    $file = $found_content[$name];
                    $file = strip_tags($file);
                    $ap = 0;
                    $filelen = strlen($file);
                    $text = "";
                    for ($j=0;$j<($count > 6 ? 6 : $count);$j++) {
                        $minpos = $filelen;
                        $word = "";
                        foreach ($queries as $query) {
                            if ($ap < $filelen && ($pos = strpos(strtoupper($file), strtoupper($query), $ap)) !== false) {
                                if ($pos < $minpos) {
                                    $minpos = $pos;
                                    $word = $query;
                                }
                            }
                        }
                        $prev = explode(" ", substr($file, $ap, $minpos-$ap));
                        if(count($prev) > ($ap > 0 ? 9 : 8))
                            $prev = ($ap > 0 ? implode(" ", array_slice($prev, 0, 8)) : "") . " ... " . implode(" ", array_slice($prev, -8));
                        else
                            $prev = implode(" ", $prev);
                        $text .= $prev . "<strong>" . substr($file, $minpos, strlen($word)) . "</strong> ";
                        $ap = $minpos + strlen($word);
                    }
                    $next = explode(" ", substr($file, $ap));
                    if(count($next) > 9)
                        $text .= implode(" ", array_slice($next, 0, 8)) . "...";
                    else
                        $text .= implode(" ", $next);
                    $text = str_replace("|", "", $text);
                    $html .= "<div class=\"imSearchBlogResult\"><h3><a class=\"imCssLink\" href=\"" . $name . "\">" . strip_tags($title, "<b><strong>") . "</a></h3>" . strip_tags($found_breadcrumbs[$name], "<b><strong>") . "\n" . strip_tags($text, "<b><strong>") . "<div class=\"imSearchLink\"><a class=\"imCssLink\" href=\"" . $name . "\">" . $imSettings['general']['url'] . "/" . $name . "</a></div></div>\n";
                }
            }
            echo "  <div class=\"imSLabel\">&nbsp;</div>\n";
        }

        $html = preg_replace_callback('/\\s+/', create_function('$matches', 'return implode(\' \', $matches);'), $html);
        return array("content" => $html, "count" => count($found_content));
    }

    // Di questa funzione manca la paginazione!
    function searchProducts($queries)
    {
        
        global $imSettings;
        $html = "";
        $found_products = array();
        $found_count = array();

        foreach ($imSettings['search']['products'] as $id => $product) {
            $count = 0;
            $weight = 0;
            $t_title = strip_tags(imstrtolower($product['name']));
            $t_description = strip_tags(imstrtolower($product['description']));

            // Conto il numero di match nel titolo
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', $t_title, $matches);
                if ($t_count !== false) {
                    $weight += ($t_count * 4);
                    $count += $t_count;
                }
            }

            // Conto il numero di match nella descrizione
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', $t_description, $matches);
                if ($t_count !== false) {
                    $weight++;
                    $count += $t_count;
                }
            }

            if ($count > 0) {
                $found_products[$id] = $product;
                $found_weight[$id] = $weight;
                $found_count[$id] = $count;
            }
        }

        if (count($found_count)) {
            arsort($found_weight);
            $i = 0;
            foreach ($found_products as $id => $product) {
                $i++;
                if (($i > $this->page*$this->results_per_page) && ($i <= ($this->page+1)*$this->results_per_page)) {
                    $count = $found_count[$id];
                    $html .= "<div class=\"imSearchProductResult\">";
                    // Top row
                    $html .= "<div class=\"imProductImage\">";
                    $html .= $product['image'];
                    $html .= "</div>";
                    $html .= "<div class=\"imProductDescription\">";
                    $html .= "<div class=\"imProductTitle\">";
                    $html .= "<h3>" . $product['name'] . "</h3>";
                    $html .= "<span>" . $product['price'] . "<img src=\"cart/images/cart-add.png\" onclick=\"x5engine.cart.ui.addToCart('" . $id . "', 1);\" style=\"cursor: pointer;\" /></span>";
                    $html .= "</div>";
                    $html .= "<p>" . strip_tags($product['description']) . "</p>";
                    $html .= "</div>";
                    // Close the container
                    $html .= "</div>";
                }
            }
        }

        return array("content" => $html, "count" => count($found_products));
    }

    // Di questa funzione manca la paginazione!
    function searchImages($queries)
    {
        
        global $imSettings;
        $id = 0;
        $html = "";
        $found_images = array();
        $found_count = array();

        foreach ($imSettings['search']['images'] as $image) {
            $count = 0;
            $weight = 0;
            $t_title = strip_tags(imstrtolower($image['title']));
            $t_description = strip_tags(imstrtolower($image['description']));

            // Conto il numero di match nel titolo
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', $t_title, $matches);
                if ($t_count !== false) {
                    $weight += ($t_count * 4);
                    $count += $t_count;
                }
            }

            // Conto il numero di match nella location
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', imstrtolower($image['location']), $matches);
                if ($t_count !== false) {
                    $weight += ($t_count * 2);
                    $count += $t_count;
                }
            }

            // Conto il numero di match nella descrizione
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', $t_description, $matches);
                if ($t_count !== false) {
                    $weight++;
                    $count += $t_count;
                }
            }

            if ($count > 0) {
                $found_images[$id] = $image;
                $found_weight[$id] = $weight;
                $found_count[$id] = $count;
            }

            $id++;
        }

        if (count($found_count)) {
            arsort($found_weight);
            $i = 0;
            foreach ($found_images as $id => $image) {
                $i++;
                if (($i > $this->page*$this->results_per_page) && ($i <= ($this->page+1)*$this->results_per_page)) {
                    $count = $found_count[$id];
                    $html .= "<div class=\"imSearchImageResult\">";
                    $html .= "<div class=\"imSearchImageResultContent\"><a href=\"" . $image['page'] . "\"><img src=\"" . $image['src'] . "\" /></a></div>";
                    $html .= "<div class=\"imSearchImageResultContent\">";
                    $html .= "<h3>" . $image['title'];
                    if ($image['location'] != "")
                        $html .= "&nbsp;(" . $image['location'] . ")";
                    $html .= "</h3>";
                    $html .= strip_tags($image['description']);
                    $html .= "</div>";
                    $html .= "</div>";
                }
            }
        }

        return array("content" => $html, "count" => count($found_images));
    }

    // Di questa funzione manca la paginazione!
    function searchVideos($queries)
    {
        
        global $imSettings;
        $id = 0;
        $found_count = array();
        $found_videos = array();
        $html = "";
        $month = 7776000;

        foreach ($imSettings['search']['videos'] as $video) {
            $count = 0;
            $weight = 0;
            $t_title = strip_tags(imstrtolower($video['title']));
            $t_description = strip_tags(imstrtolower($video['description']));

            // Conto il numero di match nei tag
            foreach ($queries as $query) {
                $t_count = preg_match_all('/\\s*' . preg_quote($query, '/') . '\\s*/', imstrtolower($video['tags']), $matches);
                if ($t_count !== false) {
                    $weight += ($t_count * 10);
                    $count += $t_count;
                }
            }

            // I video più recenti hanno maggiore peso in proporzione
            $time = strtotime($video['date']);
            $ago = strtotime("-3 months");
            if ($time - $ago > 0)
                $weight += 5 * max(0, ($time - $ago)/$month);

            // Conto il numero di match nel titolo
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', $t_title, $matches);
                if ($t_count !== false) {
                    $weight += ($t_count * 4);
                    $count += $t_count;
                }
            }

            // Conto il numero di match nella categoria
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', imstrtolower($video['category']), $matches);
                if ($t_count !== false) {
                    $weight += ($t_count * 2);
                    $count += $t_count;
                }
            }

            // Conto il numero di match nella descrizione
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query) . '/', $t_description, $matches);
                if ($t_count !== false) {
                    $weight++;
                    $count += $t_count;
                }
            }

            if ($count > 0) {
                $found_videos[$id] = $video;
                $found_weight[$id] = $weight;
                $found_count[$id] = $count;
            }

            $id++;
        }

        if ($found_count) {
            arsort($found_weight);
            $i = 0;
            foreach ($found_videos as $id => $video) {
                $i++;
                if (($i > $this->page*$this->results_per_page) && ($i <= ($this->page+1)*$this->results_per_page)) {
                    $count = $found_count[$id];
                    $html .= "<div class=\"imSearchVideoResult\">";
                    $html .= "<div class=\"imSearchVideoResultContent\"><a href=\"" . $video['page'] . "\"><img src=\"" . $video['src'] . "\" /></a></div>";
                    $html .= "<div class=\"imSearchVideoResultContent\">";
                    $html .= "<h3>" . $video['title'];
                    if (!$video['familyfriendly'])
                        $html .= "&nbsp;<span style=\"color: red; text-decoration: none;\">[18+]</span>";
                    $html .= "</h3>";
                    $html .= strip_tags($video['description']);
                    if ($video['duration'] > 0) {
                        if (function_exists('date_default_timezone_set'))
                            date_default_timezone_set('UTC');
                        $html .= "<span class=\"imSearchVideoDuration\">" . l10n('search_duration') . ": " . date("H:i:s", $video['duration']) . "</span>";
                    }
                    $html .= "</div>";
                    $html .= "</div>";
                }
            }
        }

        return array("content" => $html, "count" => count($found_videos));
    }

    /**
     * Start the site search
     * 
     * @param array  $keys The search keys as string (string)
     * @param string $page Page to show (integer)
     * @param string $type The content type to show
     *
     * @return void
     */
    function search($keys, $page, $type)
    {
        global $imSettings;

        $html = "";
        $content = "";
        $emptyResultsHtml = "<div style=\"margin-top: 15px; text-align: center; font-weight: bold;\">" . l10n('search_empty') . "</div>\n";

        $html .= "<div class=\"imPageSearchField\"><form method=\"get\" action=\"imsearch.php\">";
        $html .= "<input style=\"width: 200px; font: 8pt Tahoma; color: rgb(0, 0, 0); background-color: rgb(255, 255, 255); padding: 3px; border: 1px solid rgb(0, 0, 0); vertical-align: middle;\" class=\"search_field\" value=\"" . htmlspecialchars($keys, ENT_COMPAT, 'UTF-8') . "\" type=\"text\" name=\"search\" />";
        $html .= "<input style=\"height: 21px; font: 8pt Tahoma; color: rgb(0, 0, 0); background-color: rgb(211, 211, 211); margin-left: 6px; padding: 3px 3px; border: 1px solid rgb(0, 0, 0); vertical-align: middle; cursor: pointer;\" type=\"submit\" value=\"" . l10n('search_search') . "\">";
        $html .= "</form></div>\n";

        // Exit if no search query was given
        if (trim($keys) == "" || $keys == null) {
            $html .= $emptyResultsHtml;
            return $html;
        }

        $search = trim(imstrtolower($keys));
        $this->page = $page;

        $queries = explode(" ", $search);

        // Search everywhere to populate the results numbers shown in the sidebar menu
        // Pages
        $pages = $this->searchPages($queries);
        // Fallback on the selection if there are no pages
        if ($pages['count'] == 0 && $type == "pages")
            $type = "blog";

        // Blog
        if (isset($imSettings['blog']) && is_array($imSettings['blog']['posts']) && count($imSettings['blog']['posts']) > 0)
            $blog = $this->searchBlog($queries);
        else
            $blog = array("count" => 0);
        // Fallback on the selection if there is no blog
        if ($blog['count'] == 0 && $type == "blog")
            $type = "products";

        // Products
        if (is_array($imSettings['search']['products']) && count($imSettings['search']['products']) > 0)
            $products = $this->searchProducts($queries);
        else
            $products = array("count" => 0);
        // Fallback on the selection if there are no products
        if ($products['count'] == 0 && $type == "products")
            $type = "images";

        // Images
        if (is_array($imSettings['search']['images']) && count($imSettings['search']['images']) > 0)
            $images = $this->searchImages($queries);
        else
            $images = array("count" => 0);
        // Fallback on the selection if there are no images
        if ($images['count'] == 0 && $type == "images")
            $type = "videos";

        // Videos
        if (is_array($imSettings['search']['videos']) && count($imSettings['search']['videos']) > 0)
            $videos = $this->searchVideos($queries);
        else
            $videos = array("count" => 0);
        // Fallback on the selection if there are no videos
        if ($videos['count'] == 0 && $type == "videos")
            $type = "pages";            

        // Show only the requested content type
        switch ($type) {
            case "pages":
                if ($pages['count'] > 0)
                    $content .= "<div>" . $pages['content'] . "</div>\n";
                $results_count = $pages['count'];
                break;
            case "blog":
                if ($blog['count'] > 0)
                    $content .= "<div>" . $blog['content'] . "</div>\n";
                $results_count = $blog['count'];
                break;
            case "products":
                if ($products['count'] > 0)
                    $content .= "<div>" . $products['content'] . "</div>\n";
                $results_count = $products['count'];
                break;
            case "images":
                if ($images['count'] > 0)
                    $content .= "<div>" . $images['content'] . "</div>\n";
                $results_count = $images['count'];
                break;
            case "videos":
                if ($videos['count'] > 0)
                    $content .= "<div>" . $videos['content'] . "</div>\n";
                $results_count = $videos['count'];
                break;
        }

        // Exit if there are no results
        if (!$results_count) {
            $html .= $emptyResultsHtml;
            return $html;
        }

        $sidebar = "<ul>\n";
        if ($pages['count'] > 0)
            $sidebar .= "\t<li><span class=\"imScMnTxt\"><a href=\"imsearch.php?search=" . urlencode($keys) . "&type=pages\">" . l10n('search_pages') . " (" . $pages['count'] . ")</a></span></li>\n";
        if ($blog['count'] > 0)
            $sidebar .= "\t<li><span class=\"imScMnTxt\"><a href=\"imsearch.php?search=" . urlencode($keys) . "&type=blog\">" . l10n('search_blog') . " (" . $blog['count'] . ")</a></span></li>\n";
        if ($products['count'] > 0)
            $sidebar .= "\t<li><span class=\"imScMnTxt\"><a href=\"imsearch.php?search=" . urlencode($keys) . "&type=products\">" . l10n('search_products') . " (" . $products['count'] . ")</a></span></li>\n";
        if ($images['count'] > 0)
            $sidebar .= "\t<li><span class=\"imScMnTxt\"><a href=\"imsearch.php?search=" . urlencode($keys) . "&type=images\">" . l10n('search_images') . " (" . $images['count'] . ")</a></span></li>\n";
        if ($videos['count'] > 0)
            $sidebar .= "\t<li><span class=\"imScMnTxt\"><a href=\"imsearch.php?search=" . urlencode($keys) . "&type=videos\">" . l10n('search_videos') . " (" . $videos['count'] . ")</a></span></li>\n";
        $sidebar .= "</ul>\n";

        $html .= "<div id=\"imSearchResults\">\n";
        if ($imSettings['search']['general']['menu_position'] == "left") {
            $html .= "\t<div id=\"imSearchSideBar\" style=\"float: left;\">" . $sidebar . "</div>\n";
            $html .= "\t<div id=\"imSearchContent\" style=\"float: right;\">" . $content . "</div>\n";
        } else {
            $html .= "\t<div id=\"imSearchContent\" style=\"float: left;\">" . $content . "</div>\n";
            $html .= "\t<div id=\"imSearchSideBar\" style=\"float: right;\">" . $sidebar . "</div>\n";
        }
        $html .= "</div>\n";

        // Pagination
        if ($results_count > $this->results_per_page) {
            $html .= "<div style=\"text-align: center; clear: both;\">";
            // Back
            if ($page > 0) {
                $html .= "<a href=\"imsearch.php?search=" . implode("+", $queries) . "&amp;page=" . ($page - 1) . "&type=" . $type . "\">&lt;&lt;</a>&nbsp;";
            }

            // Central pages
            $start = max($page - 5, 0);
            $end = min($page + 10 - $start, ceil($results_count/$this->results_per_page));

            for ($i = $start; $i < $end; $i++) {
                if ($i != $this->page)
                    $html .= "<a href=\"imsearch.php?search=" . implode("+", $queries) . "&amp;page=" . $i . "&type=" . $type . "\">" . ($i + 1) . "</a>&nbsp;";
                else
                    $html .= ($i + 1) . "&nbsp;";
            }

            // Next
            if ($results_count > ($page + 1) * $this->results_per_page) {
                $html .= "<a href=\"imsearch.php?search=" . implode("+", $queries) . "&amp;page=" . ($page + 1) . "&type=" . $type . "\">&gt;&gt;</a>";
            }
            $html .= "</div>";
        }

        return $html;
    }
}


/**
 * Contains the methods used to style and send emails
 * @access public
 */
class ImSendEmail
{

    var $header;
    var $footer;
    var $bodyBackground;
    var $bodyBackgroundEven;
    var $bodyBackgroundOdd;
    var $bodyBackgroundBorder;
    var $bodySeparatorBorderColor;
    var $emailBackground;
    var $emailContentStyle;
    var $emailContentFontFamily;
    var $emailType = "html";
    var $pathToRoot = "../";
    var $use_smtp = false;
    var $use_smtp_auth = false;
    var $smtp_host;
    var $smtp_port;
    var $smtp_username;
    var $smtp_password;
    var $smtp_encryption = 'none';
    var $exposeWsx5 = true;

    /**
     * Apply the CSS style to the HTML code
     * @param  string $html The HTML code
     * @return string       The styled HTML code
     */
    function styleHTML($html)
    {
        $html = str_replace("[email:contentStyle]", $this->emailContentStyle, $html);
        $html = str_replace("[email:contentFontFamily]", $this->emailContentFontFamily, $html);
        $html = str_replace("[email:bodyBackground]", $this->bodyBackground, $html);
        $html = str_replace("[email:bodyBackgroundBorder]", $this->bodyBackgroundBorder, $html);
        $html = str_replace("[email:bodyBackgroundOdd]", $this->bodyBackgroundOdd, $html);
        $html = str_replace("[email:bodyBackgroundEven]", $this->bodyBackgroundEven, $html);
        $html = str_replace("[email:bodySeparatorBorderColor]", $this->bodySeparatorBorderColor, $html);
        $html = str_replace("[email:emailBackground]", $this->emailBackground, $html);
        return $html;
    }

    /**
     * Send an email
     * 
     * @param string $from        Self explanatory
     * @param string $to          Self explanatory
     * @param string $subject     Self explanatory
     * @param string $text        Self explanatory
     * @param string $html        Self explanatory
     * @param array  $attachments Self explanatory
     * 
     * @return boolean
     */
    function send($from = "", $to = "", $subject = "", $text = "", $html = "", $attachments = array())
    {
        /*
        |--------------
        |  PHPMailer
        |--------------
         */
        if ($this->emailType == 'phpmailer') {
            require_once("PHPMailerAutoload.php");
            $email = new PHPMailer;
            // SMTP support
            if ($this->use_smtp) {
                $email->isSMTP();
                $email->Host = $this->smtp_host;
                $email->Port = $this->smtp_port;
                if ($this->smtp_encryption != 'none') {
                    $email->SMTPSecure = $this->smtp_encryption;
                }
                $email->SMTPAuth = $this->use_smtp_auth;
                if ($this->use_smtp_auth) {
                    $email->Username = $this->smtp_username;
                    $email->Password = $this->smtp_password;
                }
            }
            // Meta
            $email->CharSet = 'UTF-8'; // WSXELE-1067: Force UTF-8
            $email->Subject = $subject;
            $email->From = addressFromEmail($from);
            $email->FromName = nameFromEmail($from);
            // WSXELE-1120: Split the email addresses if necessary
            $to = str_replace(";", ",", $to); // Make sure it works for both "," and ";" separators
            foreach (explode(",", $to) as $addr) {
                // WSXELE-1157: Provide support for the format John Doe <johndoe@email.com>
                $email->addAddress(addressFromEmail($addr), nameFromEmail($addr));
            }
            // Content
            $email->isHTML(true);
            $email->Body = $this->header . $this->styleHTML($html) . $this->footer;
            $email->AltBody = $text;
            // Attachments
            foreach ($attachments as $file) {                
                if (isset($file['name']) && isset($file['content']) && isset($file['mime'])) {
                    $email->addStringAttachment($file['content'], $file['name'], 'base64', $file['mime'], 'attachment');
                }
            }
            if (!$email->send()) {
                $this->registerLog($email->ErrorInfo);
                return false;
            }
            return true;
        }

        /*
        |--------------
        |  WSX5 class
        |--------------
         */

        $email = new imEMail($from, $to, $subject, "utf-8");
        $email->setExpose($this->exposeWsx5);
        $email->setText($text);
        $email->setHTML($this->header . $this->styleHTML($html) . $this->footer);
        $email->setStandardType($this->emailType);
        foreach ($attachments as $a) {
            if (isset($a['name']) && isset($a['content']) && isset($a['mime'])) {
                $email->attachFile($a['name'], $a['content'], $a['mime']);
            }
        }
        if (!$email->send()) {
            $this->registerLog("Cannot send email with internal script");
            return false;
        }
        return true;
    }

    /**
     * Restore some special chars escaped previously in WSX5
     * 
     * @param string $str The string to be restored
     *
     * @return string
     */
    function restoreSpecialChars($str)
    {
        $str = str_replace("{1}", "'", $str);
        $str = str_replace("{2}", "\"", $str);
        $str = str_replace("{3}", "\\", $str);
        $str = str_replace("{4}", "<", $str);
        $str = str_replace("{5}", ">", $str);
        return $str;
    }

    /**
     * Decode the Unicode escaped chars like %u1239
     * 
     * @param string $str The string to be decoded
     *
     * @return string
     */
    function decodeUnicodeString($str)
    {
        $res = '';

        $i = 0;
        $max = strlen($str) - 6;
        while ($i <= $max) {
            $character = $str[$i];
            if ($character == '%' && $str[$i + 1] == 'u') {
                $value = hexdec(substr($str, $i + 2, 4));
                $i += 6;

                if ($value < 0x0080) // 1 byte: 0xxxxxxx
                    $character = chr($value);
                else if ($value < 0x0800) // 2 bytes: 110xxxxx 10xxxxxx
                    $character = chr((($value & 0x07c0) >> 6) | 0xc0) . chr(($value & 0x3f) | 0x80);
                else // 3 bytes: 1110xxxx 10xxxxxx 10xxxxxx
                $character = chr((($value & 0xf000) >> 12) | 0xe0) . chr((($value & 0x0fc0) >> 6) | 0x80) . chr(($value & 0x3f) | 0x80);
            } else
                $i++;

            $res .= $character;
        }
        return $res . substr($str, $i);
    }

    /**
     * Get the email log path (relative to the sites' root)
     * @return String
     */
    function getLogPath()
    {
        global $imSettings;
        return pathCombine(array($imSettings['general']['public_folder'], "email_log.txt"));
    }

    /**
     * Register a message in the email log
     * @param  String $message The message to be saved in the log
     * @return void
     */
    function registerLog($message)
    {
        if (function_exists("file_get_contents") && function_exists("file_put_contents")) {
            $data = "";
            $file = pathCombine(array($this->pathToRoot, $this->getLogPath()));
            if (file_exists($file)) {
                $data = @file_get_contents($file);
            }
            $data = "[" . date("Y-m-d H:i:s") . "] " . $message . PHP_EOL . $data;
            @file_put_contents($file, $data);
        }
    }
}

/**
 * Server Test Class
 * @access public
 */
class imTest {

    /*
     * Session check
     */
    function session_test()
    {
        
        if (!isset($_SESSION))
            return false;
        $_SESSION['imAdmin_test'] = "test_message";
        return ($_SESSION['imAdmin_test'] == "test_message");
    }

    /*
     * Writable files check
     */
    function writable_folder_test($dir)
    {
        if (!file_exists($dir) && $dir != "" && $dir != "./.")
            @mkdir($dir, 0777, true);

        $fp = @fopen(pathCombine(array($dir, "imAdmin_test_file")), "w");
        if (!$fp)
            return false;
        if (@fwrite($fp, "test") === false)
            return false;
        @fclose($fp);
        if (!@file_exists(pathCombine(array($dir, "imAdmin_test_file"))))
            return false;
        @unlink(pathCombine(array($dir, "imAdmin_test_file")));
        return true;
    }

    /*
     * PHP Version check
     */
    function php_version_test()
    {   
        if (!function_exists("version_compare") || version_compare(PHP_VERSION, '4.0.0') < 0)
            return false;
        return true;
    }

    /*
     * MySQL Connection check
     */
    function mysql_test($host, $user, $pwd, $name)
    {
        $db = new ImDb($host, $user, $pwd, $name);
        if (!$db->testConnection())
            return false;
        $db->closeConnection();
        return true;
    }

    /*
     * Do the test
     */
    function doTest($expected, $value, $title, $message)
    {
        if ($expected == $value)
            echo "<div class=\"test\"><span>" . $title . "</span><span class=\"green\">PASS</span></div>";
        else
            echo "<div class=\"test\"><span>" . $title . "</span><span class=\"red\">FAIL</span><p class=\"text-small\">" . $message . "</p></div>";
    }
}



/**
 * @summary
 * Manage the user messages in a topic or discussion.
 * To use it, you must include __x5engine.php__ in your code.
 *
 * @description Create a new instance of ImTopic class
 *
 * @class
 * @constructor
 * 
 * @param {string} $id       The topic id
 * @param {string} $basepath The base path
 * @param {string} $postUrl  The URL to post to
 */
class ImTopic
{
    /**
     * The captcha code used inside this class
     * @var string
     */
    static $captcha_code       = "";

    private $id;
    private $comments          = null;
    private $table             = "";
    private $folder            = "";
    private $host              = "";
    private $user              = "";
    private $pwd               = "";
    private $database          = "";
    private $ratingImage       = "";
    private $storageType       = "xml";
    private $basepath          = "";
    private $posturl           = "";
    private $title             = "";
    private $comPerPage        = 10;
    private $paginationNumbers = 10;

    /**
     * Create a new instance of ImTopic class
     *
     * @ignore
     * 
     * @param {string} $id       The topic id
     * @param {string} $basepath The base path
     * @param {string} $postUrl  The URL to post to
     */
    function __construct($id, $basepath = "", $postUrl = "")
    {
        $this->id = $id;
        if (strlen($postUrl)) {
            $this->posturl = trim($postUrl, "?&");
            $this->posturl .=(strpos($this->posturl, "?") === false ? "?" : "&");
        } else {
            $this->posturl = basename($_SERVER['PHP_SELF']) . "?";
        }
        $this->basepath = $this->prepFolder($basepath);
        // Create the comments array
        $this->comments = new ImComment();
    }

    /**
     * Set the number of comments to show in each page
     * 
     * @param {integer} $n
     *
     * @return {Void}
     */
    function setCommentsPerPage($n) {
        $this->comPerPage = $n;
    }

    /**
     * Set the path to wich the data is posted to when a message is submitted
     * 
     * @param {string} $posturl
     *
     * @return {Void}
     */
    function setPostUrl($posturl)
    {
        $this->posturl = $posturl . (strpos($posturl, "?") === 0 ? "?" : "&");
    }

    /**
     * Set the title of this topic
     * 
     * @param {string} $title
     *
     * @return {Void}
     */
    function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Return the encrypted filename of a string
     *
     * @ignore
     * 
     * @param  string $str
     * 
     * @return string
     */
    function encFileName($str)
    {
        return substr(md5($str), 0, 8) . substr($str, -4);
    }

    /**
     * Load the data from the xml file contained in the specified folder.
     * The filename of this topic is automatically calculated basing on the topic's id to provide a major lever of security.
     * 
     * @param {string} $folder The file's folder
     *
     * @return {Void}
     */
    function loadXML($folder = "")
    {
        if ($this->comments == null)
            return;

        $this->folder = $this->prepFolder($folder);
        $encName = $this->encFileName($this->id);
        // Check if the encrypted filename exists
        if (file_exists($this->basepath . $this->folder . $encName))
            $this->comments->loadFromXML($this->basepath . $this->folder . $encName);
        // If the encrypted filename doesn't exist, try the normal filename
        else
            $this->comments->loadFromXML($this->basepath . $this->folder . $this->id);
        $this->storageType = "xml";
    }

    /**
     * Save the data to an xml file in the provided folder.
     * The filename of this topic is automatically calculated basing on the topic's id to provide a major lever of security.
     * 
     * @param {string} $folder The folder where is saved the file
     * 
     * @return {boolean} True if the file is saved correctly
     */
    function saveXML($folder = "")
    {
        if ($this->comments == null)
            return;

        $encName = $this->encFileName($this->id);
        $folder = $folder != "" ? $this->prepFolder($folder) : $this->folder;
        if ($this->comments->saveToXML($this->basepath . $folder . $encName)) {
            // If the comments can be saved, check if the non-encrypted file exists. If so, delete it.
            if (file_exists($this->basepath . $this->folder . $this->id))
                unlink($this->basepath . $this->folder . $this->id);
            return true;
        }
        return false;
    }

    /**
     * Setup the folder
     *
     * @ignore
     * 
     * @param string $folder The folder path to prepare
     * 
     * @return string
     */
    function prepFolder($folder)
    {
        if (strlen(trim($folder)) == 0)
            return "./";

        if (substr($folder, 0, -1) != "/")
            $folder .= "/";

        return $folder;
    }


    /**
     * Load the data from the database.
     * This method is available only in the **Professional** edition.
     * 
     * @param {string} $host  The dbname
     * @param {string} $user  The db user name
     * @param {string} $pwd   The db user password
     * @param {string} $db    The db name
     * @param {string} $table The db table
     *
     * @return {Void}
     */
    function loadDb($host, $user, $pwd, $db, $table)
    {
        if ($this->comments == null)
            return;

        $this->host  = $host;
        $this->user  = $user;
        $this->pwd   = $pwd;
        $this->db    = $db;
        $this->table = $table;
        $this->storageType = "database";

        $this->comments->loadFromDb($this->host, $this->user, $this->pwd, $this->db, $this->table, $this->id);
    }

    /**
     * Save the comments to a database.
     * This method is available only in the **Professional** edition.
     * 
     * @param {string} $host  The dbname
     * @param {string} $user  The db user name
     * @param {string} $pwd   The db user password
     * @param {string} $db    The db name
     * @param {string} $table The db table
     * 
     * @return {boolean} True if the comments are saved correctly
     */
    function saveDb($host = "", $user = "", $pwd = "", $db = "", $table = "")
    {
        if ($this->comments == null)
            return false;

        $host  = $host != "" ? $host : $this->host;
        $user  = $user != "" ? $user : $this->user;
        $pwd   = $pwd != "" ? $pwd : $this->pwd;
        $db    = $db != "" ? $db : $this->db;
        $table = $table != "" ? $table : $this->table;
        return $this->comments->saveToDb($host, $user, $pwd, $db, $table, $this->id);
    }


    /**
     * Checks the $_POST array for new messages
     *
     * @ignore
     * 
     * @param boolean $moderate    TRUE to show only approved comments
     * @param string  $to          The email to notify the new comment
     * @param string  $type        The topic type (guestbook|blog)
     * @param string  $moderateurl The url where the user can moderate the comments
     * 
     * @return boolean
     */
    function checkNewMessages($moderate = true, $to = "", $type = "guestbook", $moderateurl = "")
    {
        global $ImMailer;
        global $imSettings;

        /*
        |-------------------------------------------
        |    Check for new messages
        |-------------------------------------------
         */

        if (!isset($_POST['x5topicid']) || $_POST['x5topicid'] != $this->id)
            return false;
        if (!checkJsAndSpam())
            return false;

        $comment = array(
            "email"     => $_POST['email'],
            "name"      => $_POST['name'],
            "url"       => $_POST['url'],
            "body"      => $_POST['body'],
            "ip"        => $_SERVER['REMOTE_ADDR'],
            "timestamp" => date("Y-m-d H:i:s"),
            "abuse"     => "0",
            "approved"  => $moderate ? "0" : "1"
        );
        if (isset($_POST['rating']))
            $comment['rating'] = $_POST['rating'];
        $this->comments->add($comment);
        $saved = ($this->storageType == "xml" ? $this->saveXML() : $this->saveDb());
        if (!$saved) {
            echo "<script type=\"text/javascript\">window.top.location.href='" . $this->posturl . $this->id . "error';</script>";
            return false;
        }
        // Send the notification email
        if ($to != "") {
            // ---------------------------------------------------
            //  WSXELE-898: Find the correct email sender address
            $from = "";
            if ($imSettings['general']['use_common_email_sender_address']) {
                $from = $imSettings['general']['common_email_sender_addres'];
            } else if (strlen($comment['email'])) {
                $from = $comment['email'];
            } else {
                $from = $to;
            }
            // ---------------------------------------------------

            if ($type == "guestbook")
                $html = str_replace(array("Blog", "blog"), array("Guestbook", "guestbook"), l10n('blog_new_comment_text')) . " \"" . $this->title . "\":<br /><br />\n\n";
            else
                $html = l10n('blog_new_comment_text') . ":<br /><br />\n\n";
            $html .= "<b>" . l10n('blog_name') . "</b> " . stripslashes($_POST['name']) . "<br />\n";
            $html .= "<b>" . l10n('blog_email') . "</b> " . $_POST['email'] . "<br />\n";
            $html .= "<b>" . l10n('blog_website') . "</b> " . $_POST['url'] . "<br />\n";
            if (isset($_POST['rating']))
                $html .= "<b>" . l10n('blog_rating', "Vote:") . "</b> " . $_POST['rating'] . "/5<br />\n";
            $html .= "<b>" . l10n('blog_message') . "</b> " . stripslashes($_POST['body']) . "<br /><br />\n\n";
            // Set the proper link
            if ($moderateurl != "") {
                $html .= ($moderate ? l10n('blog_unapprove_link') : l10n('blog_approve_link')) . ":<br />\n";
                $html .= "<a href=\"" . $moderateurl . "\">" . $moderateurl . "</a>";
            }
            if ($type == "guestbook")
                $subject = str_replace(array("Blog", "blog"), array("Guestbook", "guestbook"), l10n('blog_new_comment_object'));
            else
                $subject = l10n('blog_new_comment_object');
            $ImMailer->send($from, $to, $subject, strip_tags($html), $html);
        }

        // Redirect
        echo "<script type=\"text/javascript\">window.top.location.href='" . $this->posturl . ($moderate ? $this->id . "success" : "") . "';</script>";
        return true;
    }

    /**
     * Check for new abuses
     *
     * @ignore
     * 
     * @return void
     */
    function checkNewAbuses()
    {
        if (isset($_GET['x5topicid']) && $_GET['x5topicid'] == $this->id) {
            if (isset($_GET['abuse'])) {
                $n = intval($_GET['abuse']);
                $c = $this->comments->get($n);
                $c['abuse'] = "1";
                $this->comments->edit($n, $c);
                $this->storageType == "xml" ? $this->saveXML() : $this->saveDb();
                echo "<script type=\"text/javascript\">window.top.location.href='" . $this->posturl . "';</script>";
            }
        }
    }

    /**
     * Show the comments form
     * 
     * @param {boolean} $rating      true to show the rating
     * @param {boolean} $captcha     true to enable captcha
     * @param {boolean} $moderate    true to enable the moderation
     * @param {string}  $email       the email address to notificate
     * @param {string}  $type        guestbook or blog
     * @param {string}  $moderateurl The url at wich is possible to moderate the new comments
     * 
     * @return void
     */
    function showForm($rating = true, $captcha = true, $moderate = true, $email = "", $type = "guestbook", $moderateurl = "")
    {
        global $imSettings;
        $id = $this->id . "-topic-form";

        $this->checkNewMessages($moderate, $email, $type, $moderateurl);
        $this->checkNewAbuses();

        /*
        |-------------------------------------------
        |    Show the form
        |-------------------------------------------
         */
        
        if (isset($_GET[$this->id . 'success'])) {
            echo "<div class=\"alert alert-green\">" . l10n('blog_send_confirmation') . "</div>";
        } else if (isset($_GET[$this->id . 'error'])) {
            echo "<div class=\"alert alert-red\">" . l10n('blog_send_error') . "</div>";
        }

        echo "<div class=\"topic-form\">
              <form id=\"" . $id ."\" action=\"" . $this->posturl . "\" method=\"post\">
                <input type=\"hidden\" name=\"post_id\" value=\"" . $this->id . "\"/>
                <div class=\"topic-form-row\">
                    <label for=\"" . $id . "-name\" style=\"float: left; width: 100px;\">" . l10n('blog_name') . "*</label> <input type=\"text\" id=\"" . $id . "-name\" name=\"name\" class=\"imfield mandatory\" />
                </div>
                <div class=\"topic-form-row\">
                    <label for=\"" . $id . "-email\" style=\"float: left; width: 100px;\">" . l10n('blog_email') . "*</label> <input type=\"text\" id=\"" . $id . "-email\" name=\"email\" class=\"imfield mandatory valEmail\"/>
                </div>
                <div class=\"topic-form-row\">
                    <label for=\"" . $id . "-url\" style=\"float: left; width: 100px;\">" . l10n('blog_website') . "</label> <input type=\"text\" id=\"" . $id . "-url\" name=\"url\" />
                </div>";
        if ($rating) {
            echo "<div class=\"topic-form-row\">
                <label style=\"float: left; width: 100px;vertical-align: middle;\">" . l10n('blog_rating', "Vote") . "</label>
                <span class=\"topic-star-container-big variable-star-rating\">
                    <span class=\"topic-star-fixer-big\" style=\"width: 0;\"></span>
                </span>
                </div>";
        }
                echo "<div class=\"topic-form-row\">
                    <br /><label for=\"" . $id . "-body\" style=\"clear: both; width: 100px;\">" . l10n('blog_message') . "*</label><textarea id=\"" . $id . "-body\" name=\"body\" class=\"imfield mandatory\" style=\"width: 95%; height: 100px;\"></textarea>
                </div>";
        if ($captcha) {
            echo ImTopic::$captcha_code;
        }
        echo "<input type=\"hidden\" value=\"" . $this->id . "\" name=\"x5topicid\">";
        echo "<input type=\"text\" value=\"\" name=\"prt\" class=\"prt_field\">";
        echo "<div class=\"topic-form-row\" style=\"text-align: center\">
                    <input type=\"submit\" value=\"" . l10n('blog_send') . "\" />
                    <input type=\"reset\" value=\"" . l10n('form_reset') . "\" />
                </div>
                </form>
                <script type=\"text/javascript\">x5engine.boot.push( function () { x5engine.imForm.initForm('#" . $id . "', false, { showAll: true }); });</script>
            </div>\n";
    }

    /**
     * Show the topic summary
     * 
     * @param {boolean} $rating      TRUE to show the ratings
     * @param {boolean} $admin       TRUE to show approved and unapproved comments
     * @param {boolean} $hideifempty true to hide the summary if there are no comments
     *
     * @return {Void}
     */
    function showSummary($rating = true, $admin = false, $hideifempty = true)
    {
        $c = $this->comments->getAll();
        $comments = array();
        $votes = 0;
        $votescount = 0;
        foreach ($c as $comment) {
            if ($comment['approved'] == "1" || $admin) {
                if (isset($comment['body'])) {
                    $comments[] = $comment;
                }
                if (isset($comment['rating'])) {
                    $votes += $comment['rating'];
                    $votescount++;
                }
            }
        }
        $count = count($comments);
        $vote = $votescount > 0 ? $votes/$votescount : 0;

        if ($count == 0 && $hideifempty)
            return;

        echo "<div class=\"topic-summary\">\n";
        echo "<div>" . ($count > 0 ? "<span itemprop=\"commentCount\">" . $count . "</span> " . ($count > 1 ? l10n('blog_comments') : l10n('blog_comment')) : l10n('blog_no_comment') . "<span itemprop=\"commentCount\" style=\"display: none;\">0</span>") . "</div>";
        if ($rating) {
            echo "<div style=\"margin-bottom: 5px;\" itemprop=\"aggregateRating\" itemscope itemtype=\"http://schema.org/AggregateRating\">";
            echo l10n("blog_average_rating", "Average Vote") . ": ";
            echo "<span itemprop=\"worstRating\" style=\"display: none;\">1</span>";
            echo "<span itemprop=\"ratingCount\" style=\"display: none;\">" . $count . "</span>";
            echo "<span itemprop=\"ratingValue\">" . number_format($vote, 1) . "</span>/<span itemprop=\"bestRating\">5</span>";
            echo "</div>";
            echo "<span class=\"topic-star-container-big\" title=\"" . number_format($vote, 1) . "/5\">
                    <span class=\"topic-star-fixer-big\" style=\"width: " . round($vote/5 * 100) . "%;\"></span>
            </span>\n";
        }
        echo "</div>\n";
    }

    /**
     * Show the topic summary
     *
     * @ignore
     * 
     * @param {boolean} $rating      TRUE to show the ratings
     * @param {boolean} $hideifempty true to hide the summary if there are no comments
     */
    function showAdminSummary($rating = true, $hideifempty = true)
    {
        $c = $this->comments->getAll();
        $comments = array();
        $votes = 0;
        $votescount = 0;
        foreach ($c as $comment) {
            if (isset($comment['body'])) {
                $comments[] = $comment;
            }
            if (isset($comment['rating'])) {
                $votes += $comment['rating'];
                $votescount++;
            }
        }
        $count = count($comments);
        $vote = $votescount > 0 ? $votes/$votescount : 0;

        if ($count == 0 && $hideifempty)
            return;

        echo "<div class=\"topic-summary page-navbar-item\">\n";
        echo "<div>" . ($count > 0 ? "<span class=\"comments-count\">" .  $count . "</span> " . strtoupper($count > 1 ? l10n('blog_comments') : l10n('blog_comment')) : l10n('blog_no_comment')) . "</div>";
        if ($rating) {
            echo "<div class=\"separator\">&nbsp;</div>";
            echo "<div>" . l10n("blog_average_rating", "Average Vote") . ": " . number_format($vote, 1) . "/5</div>";
            echo "<span class=\"topic-star-container-small\" title=\"" . number_format($vote, 1) . "/5\">
                    <span class=\"topic-star-fixer-small\" style=\"width: " . round($vote/5 * 100) . "%;\"></span>
            </span>\n";
        }
        echo "</div>\n";
    }

    /**
     * Returns true if this topic has comments
     * @return boolean
     */
    function hasComments() 
    {
        return $this->comments && count($this->comments->getAll()) > 0;
    }

    /**
     * Show the comments list
     *
     * @param {integer} $page            The page to show
     * @param {integer} $commentsPerPage The number of comments to show for each page
     * @param {boolean} $rating          True to show the ratings
     * @param {string}  $order           desc or asc
     * @param {boolean} $showabuse       True to show the "Abuse" button
     * @param {boolean} $hideifempty     True to hide the summary if there are no comments
     *
     * @return {Void}
     */
    function showComments($rating = true, $order = "desc", $showabuse = true, $hideifempty = false)
    {
        
        global $imSettings;

        $page = @$_GET[$this->id . "page"];

        $c = $this->comments->getPage($page, $this->comPerPage, "timestamp", $order, true);

        if (count($c) == 0 && $hideifempty)
            return;

        // Show the pagination
        $this->showPagination($page);
        echo "<div class=\"topic-comments\">\n";
        if (count($c) > 0) {
            // Show the comments
            $i = 0;
            foreach ($c as $comment) {
                if (isset($comment['body']) && $comment['approved'] == "1") {
                    echo "<div class=\"topic-comment\" itemscope itemtype=\"http://schema.org/UserComments\">\n";
                    echo "<div class=\"topic-comments-user\" itemprop=\"creator\" itemscope itemtype=\"http://schema.org/Person\">" . (stristr($comment['url'], "http") ? "<a href=\"" . $comment['url'] . "\" target=\"_blank\" " . (strpos($comment['url'], $imSettings['general']['url']) === false ? 'rel="nofollow"' : '') . "><span itemprop=\"name\">" . $comment['name'] . "</span></a>" : "<span itemprop=\"name\">" . $comment['name'] . "</span>");
                    if ($rating && isset($comment['rating']) && $comment['rating'] > 0) {
                        echo "<span class=\"topic-star-container-small\" title=\"" . $comment['rating'] . "/5\" style=\"margin-left: 5px; vertical-align: middle;\">
                                    <span class=\"topic-star-fixer-small\" style=\"width: " . round($comment['rating']/5 * 100) . "%;\"></span>
                            </span>\n";
                    }
                    echo "</div>\n";
                    echo "<div class=\"topic-comments-date imBreadcrumb\" itemprop=\"commentTime\" datetime=" . $comment['timestamp'] . ">" . $comment['timestamp'] . "</div>\n";
                    echo "<div class=\"topic-comments-body\" itemprop=\"commentText\">" . $comment['body'] . "</div>\n";
                    if ($showabuse) {
                        echo "<div class=\"topic-comments-abuse\"><a href=\"" . $this->posturl . "x5topicid=" . $this->id . "&amp;abuse=" . $comment['id'] . "\">" . l10n('blog_abuse') . "<img src=\"" . $this->basepath . "res/exclamation.png\" alt=\"" . l10n('blog_abuse') . "\" title=\"" . l10n('blog_abuse') . "\" /></a></div>\n";
                    }
                    echo "</div>\n";
                }
                $i++;
            }
        } else {
            echo "<div>" . l10n('blog_no_comment') . "</div>\n";
        }
        echo "</div>\n";
        // Show the pagination
        $this->showPagination($page);
    }

    /**
     * Show the pagination links for this topic
     * 
     * @param  {int} $page Page number
     *
     * @return {Void}
     */
    function showPagination($page) {
        $pages = $this->comments->getPagesNumber($this->comPerPage, true);
        if ($pages > 1) {
            echo "<div class=\"topic-pagination\">\n";
            $interval = floor($this->paginationNumbers / 2);
            if ($page > $interval && $pages > 2) {
                echo "\t<a href=\"?" . updateQueryStringVar($this->id . "page", 0) . "\" class=\"imCssLink\">&lt;&lt;</a>\n";
            }
            for ($i = max(0, $page - $interval); $i < min($pages, $i + $interval); $i++) {
                echo "\t<a href=\"?" . updateQueryStringVar($this->id . "page", $i) . "\" class=\"imCssLink\" " . ($i == $page ? " style=\"font-weight: bold;\"" : "") . ">" . ($i + 1) . "</a>\n";
            }
            if ($page < $pages - $interval && $pages > 2) {
                echo "\t<a href=\"?" . updateQueryStringVar($this->id . "page", $pages - 1) . "\" class=\"imCssLink\">&gt;&gt;</a>\n";
            }
            echo "</div>\n";
        }
    }

    /**
     * Show the comments list in a administration section
     *
     * @ignore
     * 
     * @param boolean $rating true to show the ratings
     * @param string  $order  desc or asc
     * 
     * @return void
     */
    function showAdminComments($rating = true, $order = "desc")
    {
        
        global $imSettings;
        $this->comments->sort("ts", $order);

        if (isset($_GET['disable'])) {
            $n = (int)$_GET['disable'];
            $c = $this->comments->get($n);
            if (count($c) != 0) {
                $c['approved'] = "0";
                $this->comments->edit($n, $c);
                $this->storageType == "xml" ? $this->saveXML() : $this->saveDb();
            }
        }

        if (isset($_GET['enable'])) {
            $n = (int)$_GET['enable'];
            $c = $this->comments->get($n);
            if (count($c) != 0) {
                $c['approved'] = "1";
                $this->comments->edit($n, $c);
                $this->storageType == "xml" ? $this->saveXML() : $this->saveDb();
            }
        }

        if (isset($_GET['delete'])) {
            $this->comments->delete((int)$_GET['delete']);
            $this->storageType == "xml" ? $this->saveXML() : $this->saveDb();
        }

        if (isset($_GET['unabuse'])) {
            $n = (int)$_GET['unabuse'];
            $c = $this->comments->get($n);
            if (count($c)) {
                $c['abuse'] = "0";
                $this->comments->edit($n, $c);
                $this->storageType == "xml" ? $this->saveXML() : $this->saveDb();
            }
        }

        if (isset($_GET['disable']) || isset($_GET['enable']) || isset($_GET['delete']) || isset($_GET['unabuse'])) {
            echo "<script type=\"text/javascript\">window.top.location.href='" . $this->posturl . "';</script>\n";
            exit();
        }

        echo "<div class=\"topic-comments\">\n";
        $c = $this->comments->getAll();
        if (count($c) > 0) {
            // Show the comments
            for ($i = 0; $i < count($c); $i++) {
                $comment = $c[$i];
                if (isset($comment['body'])) {
                    echo "<div class=\"topic-comment " . ($comment['approved'] == "1" ? "enabled" : "disabled") . ($comment['abuse'] == "1" ? " abused" : "") . "\">\n";
                    echo "<div class=\"topic-comments-user-wrapper\">\n";
                    echo "\t\t<div class=\"topic-comments-user\">";
                    // Abuse sign
                    if ($comment['abuse'] == "1") {
                        echo "<img src=\"" . $this->basepath . "res/exclamation.png\" alt=\"Abuse\" title=\"" . l10n('admin_comment_abuse') . "\" style=\"vertical-align: middle;\">\n";
                    }
                    // User name (with link to its url if available)
                    // Prepare the url
                    if (isset($comment['url']) && strlen($comment['url']) > 0) {
                        if (strpos($comment['url'], "http://") !== 0 && strpos($comment['url'], "https://") !== 0) {
                            $comment['url'] = "http://" . $comment['url'];
                        } 
                        echo "<a href=\"" . $comment['url'] . "\" target=\"_blank\" " . (strpos($comment['url'], $imSettings['general']['url']) === false ? 'rel="nofollow"' : '') . ">" . $comment['name'] . "</a>";
                    } else {
                        echo $comment['name'];
                    }
                    // Email
                    if (isset($comment['email'])) {
                        echo " (<a href=\"mailto:" . $comment['email'] . "\">" . $comment['email'] . "</a>)";
                    }
                    echo "\t\t<div class=\"topic-comments-date\">" . $comment['timestamp'] . "</div>\n";
                    // Rating
                    if ($rating && isset($comment['rating']) && $comment['rating'] > 0) {
                        echo "\t\t<div class=\"topic-star-container-small\" title=\"" . $comment['rating'] . "/5\">
                                    <span class=\"topic-star-fixer-small\" style=\"width: " . round($comment['rating']/5 * 100) . "%;\"></span>
                            </div>\n";
                    }
                    echo "\t\t</div>\n";
                    echo "\t\t<div class=\"topic-comments-body\">" . $comment['body'] . "</div>\n";
                    echo "\t</div>\n";
                    echo "\t<div class=\"topic-comments-controls\">\n";
                    echo "\t\t<span class=\"left\">IP: " . $comment['ip'] . "</span>\n";
                    if ($comment['abuse'] == "1")
                        echo "\t\t<a class=\"green\" href=\"" . $this->posturl . "unabuse=" . $comment['id'] . "\">" . l10n("blog_abuse_remove", "Remove abuse") . "</a> |\n";
                    if ($comment['approved'] == "1")
                        echo "\t\t<a class=\"black\" onclick=\"return confirm('" . str_replace("'", "\\'", l10n('blog_unapprove_question')) . "')\" href=\"" . $this->posturl . "disable=" . $comment['id'] . "\">" . l10n('blog_unapprove') . "</a> |\n";
                    else
                        echo "\t\t<a class=\"black\" onclick=\"return confirm('" . str_replace("'", "\\'", l10n('blog_approve_question')) . "')\" href=\"" . $this->posturl . "enable=" . $comment['id'] . "\">" . l10n('blog_approve') . "</a> |\n";
                    echo "\t\t<a class=\"red\" onclick=\"return confirm('" . str_replace("'", "\\'", l10n('blog_delete_question')) . "')\" href=\"" . $this->posturl . "delete=" . $comment['id'] . "\">" . l10n('blog_delete') . "</a>\n";
                    echo "</div>\n";
                    echo "</div>\n";
                }
            }
        } else {
            echo "<div style=\"text-align: center; margin: 15px; 0\">" . l10n('blog_no_comment') . "</div>\n";
        }
        echo "</div>\n";        
    }

    /**
     * Show a single rating form
     * 
     * @return void
     */
    function showRating()
    {
        
        global $imSettings;

        if (isset($_POST['x5topicid']) && $_POST['x5topicid'] == $this->id && !isset($_COOKIE['vtd' . $this->id]) && isset($_POST['imJsCheck']) && $_POST['imJsCheck'] == 'jsactive') {
            $this->comments->add(
                array(
                    "rating" => $_POST['rating'],
                    "approved" => "1"
                )
            );
            $this->storageType == "xml" ? $this->saveXML() : $this->saveDb();
        }

        $c = $this->comments->getAll();
        $count = 0;
        $votes = 0;
        $vote = 0;
        if (count($c) > 0) {
            // Check aproved comments count
            $ca = array();
            foreach ($c as $comment) {
                if ($comment['approved'] == "1" && isset($comment['rating'])) {
                    $count++;
                    $votes += $comment['rating'];
                }
            }
            $vote = ($count > 0 ? $votes/$count : 0);
        }
        echo "
            <div style=\"text-align: center\">
                <div style=\"margin-bottom: 5px;\">" . l10n("blog_rating", "Vote:") . " " . number_format($vote, 1) . "/5</div>
                <div class=\"topic-star-container-big" . (!isset($_COOKIE['vtd' . $this->id]) ? " variable-star-rating" : "") . "\" data-url=\"" . $this->posturl . "\" data-id=\"" . $this->id . "\">
                    <span class=\"topic-star-fixer-big\" style=\"width: " . round($vote/5 * 100) . "%;\"></span>
                </div>
            </div>\n";
    }
}




/**
 * XML Handling class
 * @access public
 */
class imXML 
{
    var $tree = array();
    var $force_to_array = array();
    var $error = null;
    var $parser;
    var $inside = false;

    function __construct($encoding = 'UTF-8')
    {
        $this->parser = xml_parser_create($encoding);
        xml_set_object($this->parser, $this); // $this was passed as reference &$this
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);
        xml_set_element_handler($this->parser, "startEl", "stopEl");
        xml_set_character_data_handler($this->parser, "charData");
        xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
    }

    function parse_file($file)
    {
        $fp = @fopen($file, "r");
        if (!$fp)
            return false;
        while ($data = fread($fp, 4096)) {
            if (!xml_parse($this->parser, $data, feof($fp))) {
                return false;
            }
        }
        fclose($fp);
        return $this->tree[0]["content"];
    }

    function parse_string($str)
    {
        if (!xml_parse($this->parser, $str))
            return false;
        if (isset($this->tree[0]["content"]))
            return $this->tree[0]["content"];
        return false;
    }

    function startEl($parser, $name, $attrs)
    {
        array_unshift($this->tree, array("name" => $name));
        $this->inside = false;
    }

    function stopEl($parser, $name)
    {
        if ($name != $this->tree[0]["name"])
            return false;
        if (count($this->tree) > 1) {
            $elem = array_shift($this->tree);
            if (isset($this->tree[0]["content"][$elem["name"]])) {
                if (is_array($this->tree[0]["content"][$elem["name"]]) && isset($this->tree[0]["content"][$elem["name"]][0])) {
                    array_push($this->tree[0]["content"][$elem["name"]], $elem["content"]);
                } else {
                    $this->tree[0]["content"][$elem["name"]] = array($this->tree[0]["content"][$elem["name"]],$elem["content"]);
                }
            } else {
                if (in_array($elem["name"], $this->force_to_array)) {
                    $this->tree[0]["content"][$elem["name"]] = array($elem["content"]);
                } else {
                    if (!isset($elem["content"])) $elem["content"] = "";
                    $this->tree[0]["content"][$elem["name"]] = $elem["content"];
                }
            }
        }
        $this->inside = false;
    }

    function charData($parser, $data)
    {
        if (!preg_match("/\\S/", $data))
                return false;
        if ($this->inside) {
            $this->tree[0]["content"] .= $data;
        } else {
            $this->tree[0]["content"] = $data;
        }
        $this->inside_data = true; 
    }
}


/**
 * Prints an error that warns the user that JavaScript is not enabled, then redirects to the previous page after 5 seconds.
 *
 * @param {boolean} $docType True to use the meta redirect with a complete document. False to use a javascript code.
 *
 * @return {Void}
 */
function imPrintJsError($docType = true)
{
    return imPrintError(l10n('form_js_error'), $docType);
}

/**
 * Prints a custom error message then redirects to the previous page after 5 seconds.
 *
 * @param {string} $message The message to show
 * @param {boolean} $docType True to use the meta redirect with a complete document. False to use a javascript code.
 *
 * @return {Void}
 */
function imPrintError($message, $docType = true)
{
    if ($docType) {
        $html = "<DOCTYPE><html><head><meta charset=\"UTF-8\"> <meta http-equiv=\"Refresh\" content=\"5;URL=" . $_SERVER['HTTP_REFERER'] . "\"></head><body>";
        $html .= $message;
        $html .= "</body></html>";
    } else {
        $html = "<meta charset=\"UTF-8\"> <meta http-equiv=\"Refresh\" content=\"5;URL=" . $_SERVER['HTTP_REFERER'] . "\">";
        $html .= $message;
    }
    return $html;
}

/**
 * Check if the current user can access to the provided page id.
 * If not, the user is redirected to the login page.
 *
 * @method imCheckAccess
 * 
 * @param {string} $page The id of the page to check
 * @param {string} $pathToRoot The path to reach the root from the current folder
 *
 * @return {Void}
 */
function imCheckAccess($page, $pathToRoot = "")
{
    $pa = imPrivateArea::getInstance();
    $stat = $pa->checkAccess($page);
    if ($stat !== 0) {
        $pa->savePage();
        header("Location: " . $pathToRoot . "imlogin.php?loginstatus=" . $stat );
        exit;
    }
}


/**
 * Show the guestbook
 * This function is provided as compatibility for v9 guestbook widget
 * 
 * @param string  $id              The guestbook id
 * @param string  $filepath        The folder where the comments must be stored
 * @param string  $email           The email to notify the new comments
 * @param boolean $captcha         true to show the captcha
 * @param boolean $direct_approval true to directly approve comments
 * 
 * @return void
 */
function showGuestBook($id, $filepath, $email, $captcha = true, $direct_approval = true)
{
    global $imSettings;

    $gb = new ImTopic("gb" . $id);
    $gb->loadXML($filepath);
    $gb->showSummary(false);
    $gb->showForm(false, $captcha, !$direct_approval, $email, "guestbook", $imSettings['general']['url'] . "/admin/guestbook.php?id=" . $id);
    $gb->showComments(false);
}

/**
 * Provide the database connection data of given id
 *
 * @method getDbData
 * 
 * @param  {string} $dbid The database id
 * 
 * @return {array}        an array like array('description' => '', 'host' => '', 'database' => '', 'user' => '', 'password' => '')
 */
function getDbData($dbid) {
    global $imSettings;
    if (!isset($imSettings['databases'][$dbid]))
        return false;
    return $imSettings['databases'][$dbid];
}


/**
 * Shuffle an associative array
 *
 * @method shuffleAssoc
 * 
 * @param {array} $list The array to shuffle
 * 
 * @return {array}       The shuffled array
 */
function shuffleAssoc($list)
{
    if (!is_array($list))
        return $list;
    $keys = array_keys($list);
    shuffle($keys);
    $random = array();
    foreach ($keys as $key)
        $random[$key] = $list[$key];
    return $random;
}

/**
 * If you want to support PHP 4 code, use this function instead of stripos.
 *
 * @method imstripos
 * 
 * @param {string}  $haystack Where to search
 * @param {string}  $needle   What to replace
 * @param {integer} $offset   Start searching from here
 * 
 * @return {integer}          The position of the searched string
 */
function imstripos($haystack, $needle , $offset = 0)
{
    if (function_exists('stripos')) // Is PHP5+
        return stripos($haystack, $needle, $offset);

    // PHP4 fallback
    return strpos(strtolower($haystack), strtolower($needle), $offset);
}

/**
 * Get a localization string.
 * The string is taken from the ones specified at step 1 of WSX5
 *
 * @method l10n
 * 
 * @param {string} $id      The localization key
 * @param {string} $default The default string
 * 
 * @return {string}         The localization
 */
function l10n($id, $default = "")
{
    global $l10n;

    if (!isset($l10n[$id]))
        return $default;

    return $l10n[$id];
}

/**
 * Combine a series of paths
 *
 * @method pathCombine
 * 
 * @param  {array}  $paths The array with the elements of the path
 * 
 * @return {string} The path created combining the elements of the array
 */
function pathCombine($paths = array())
{
    $s = array();
    foreach ($paths as $path) {
        if (strlen($path)) {
            $s[] = trim($path, "/\\ ");
        }
    }
    return implode("/", $s);
}

/**
 * Try to convert a string to lowercase using multibyte encoding
 * 
 * @param  string $str
 * 
 * @return string
 */
function imstrtolower($str)
{
    return (function_exists("mb_convert_case") ? mb_convert_case($str, MB_CASE_LOWER, "UTF-8") : strtolower($str));
}

if (!function_exists('htmlspecialchars_decode')) {
    /**
     * Fallback for htmlspecialchars_decode in PHP4
     * @ignore
     * @param  string  $text
     * @param  integer $quote_style
     * @return string
     */
    function htmlspecialchars_decode($text, $quote_style = ENT_COMPAT)
    {
        return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS, $quote_style)));
    }
}

if (!function_exists('json_encode')) {
    /**
     * Fallback for json_encode before PHP 5.2
     * @ignore
     */
    function json_encode($data)
    {
        switch ($type = gettype($data)) {
            case 'NULL':
                return 'null';
            case 'boolean':
                return ($data ? 'true' : 'false');
            case 'integer':
            case 'double':
            case 'float':
                return $data;
            case 'string':
                return '"' . addslashes($data) . '"';
            case 'object':
                $data = get_object_vars($data);
            case 'array':
                $output_index_count = 0;
                $output_indexed = array();
                $output_associative = array();
                foreach ($data as $key => $value) {
                    $output_indexed[] = json_encode($value);
                    $output_associative[] = json_encode($key) . ':' . json_encode($value);
                    if ($output_index_count !== NULL && $output_index_count++ !== $key) {
                        $output_index_count = NULL;
                    }
                }
                if ($output_index_count !== NULL) {
                    return '[' . implode(',', $output_indexed) . ']';
                } else {
                    return '{' . implode(',', $output_associative) . '}';
                }
            default:
                return ''; // Not supported
        }
    }
}

/**
 * Check for the valid data about spam and js
 * 
 * @param  string $prt The spam post field name
 * @param  string $js  The js post file name
 * 
 * @return bool
 */
function checkJsAndSpam($prt = 'prt', $js = 'imJsCheck')
{
    // Spam!
    if ($_POST[$prt] != "") {
        return false;
    }

    // Javascript disabled
    if (!isset($_POST[$js]) || $_POST[$js] != 'jsactive') {
        echo imPrintJsError(false);
        return false;
    }

    return true;
}

/**
 * Search if at least one element of $needle is in $haystack.
 * @param  Array   $needle   Non-associative array
 * @param  Array   $haystack Non-associative array
 * @param  boolean $all      Set to true to ensure that all the elements in $needle are in $haystack
 * @return boolean
 */
function in_array_field($needle, $haystack, $all = false)
{
    if ($all) {
        foreach ($needle as $key)
            if (!in_array($key, $haystack))
                return false;
        return true;
    } else {
        foreach ($needle as $key)
            if (in_array($key, $haystack))
                return true;
        return false;
    }
}

/**
 * Filter the var from unwanted input chars.
 * Basically remove the quotes added by magic_quotes
 * @param  mixed $var The var to filter
 * @return mixed      The filtered var
 */
function imFilterInput($var) {
    // Remove the magic quotes
    if (get_magic_quotes_gpc()) {        
        // String
        if (is_string($var))
            $var = stripslashes($var);
        // Array
        else if (is_array($var)) {
            for ($i = 0; $i < count($var); $i++)
                $var[$i] = imFilterInput($var[$i]);
        }
    }
    return $var;
}

/**
 * Get the most recent date in the provided array of PHP times that do not define a future time.
 * The date is provided in the RSS format Sun, 15 Jun 2008 21:15:07 GMT
 * @param  array    $timeArr
 * @return string
 */
function getLastAvailableDate($timeArr) {
    if (count($timeArr) > 0) {
        sort($timeArr, SORT_DESC);
        $utcTime = time() + date("Z", time());
        foreach ($timeArr as $time) {
            if ($time <= $utcTime) {
                return date("r", $time);
            }
        }
    }
    return date("r", time());
}

/**
 * Update the query string adding or updating a variable with a specified value
 *
 * @param  string $name  The variable to be replaced
 * @param  string $value The value to set
 * 
 * @return string        The updated query string
 */
function updateQueryStringVar($name, $value) {
    if (!isset($_SERVER['QUERY_STRING'])) return "";
    $qs = array();
    parse_str($_SERVER['QUERY_STRING'], $qs);
    $qs[$name] = $value;
    return http_build_query($qs);
}

/**
 * Get the email address from a string formatted like "John Doe <johndoe@email.com>"
 * 
 * @param  String $email The email string
 * 
 * @return String        The email address
 */
function addressFromEmail($email) {
    $start = strpos($email, "<");
    $end = strpos($email, ">");
    if ($start > 0 && $end !== false) {
        return trim(substr($email, $start + 1, $end - $start - 1));
    }
    return $email;
}

/**
 * Get the user name from a string formatted like "John Doe <johndoe@email.com>"
 * 
 * @param  String $email The email string
 * 
 * @return String        The user name
 */
function nameFromEmail($email) {
    $end = strpos($email, "<");
    if ($end > 0) {
        return trim(substr($email, 0, $end));
    }
    return $email;
}

/**
 * If you want to get the request headers and still support PHP 4, use this function.
 * 
 * @return array The request headers array or FALSE on failure
 */
function imRequestHeaders()
{
    $headers = array();
    // If apache supports apache_request_headers, use it!
    if (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        if (!is_array($headers))
            return false;
    } else {
        // Build the array manually
        foreach ($_SERVER as $key => $val) {
            if (strncmp($key, 'HTTP_', 5) === 0) {
                $headers[substr($key, 5)] = $_SERVER[$key];
            }
        }
        if (count($headers) === 0)
            return false;
        $headers['Content-Type'] = (isset($_SERVER['CONTENT_TYPE'])) ? $_SERVER['CONTENT_TYPE'] : @getenv('CONTENT_TYPE');
    }

    // Format the headers name correctly. For example, turn CONTENT_TYPE into Content-Type.
    foreach ($headers as $key => $val) {
        $key = str_replace('_', ' ', strtolower($key));
        $key = str_replace(' ', '-', ucwords($key));
        $headers[$key] = $val;
    }
    
    return $headers;
}

$imSettings = Array();
$l10n = Array();
$phpError = false;
$ImMailer = new ImSendEmail();

@include_once "imemail.inc.php";        // Email class - Static
@include_once "x5settings.php";         // Basic settings - Dynamically created by WebSite X5
@include_once "blog.inc.php";           // Blog data - Dynamically created by WebSite X5
@include_once "access.inc.php";         // Private area data - Dynamically created by WebSite X5
@include_once "l10n.php";               // Localizations - Dynamically created by WebSite X5
@include_once "search.inc.php" ;        // Search engine data - Dynamically created by WebSite X5


// End of file