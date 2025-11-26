<?php 
    //additional functions for articles

    //change id ctgry => name
    function idCtgryToName($pdo, $articles) {
        if(!$articles) return false;

        //take all array of categories
        $db_catgries_name = $pdo->prepare("SELECT * FROM a_catgries");
        $db_catgries_name->execute();
        $catgries_name = $db_catgries_name->fetchAll(PDO::FETCH_ASSOC);

        //make associated array(to avoid misid)
        $ctgries = [];
        foreach($catgries_name as $cat) {
            $ctgries[$cat['id']] = $cat['name'];
        }

        //change id to name in all articles
        if(is_assoc_array($articles)) {
            $id_ctgry = (int)$articles['ctgry'];
            $articles['ctgry'] = $ctgries[$id_ctgry] ?? 'Unknown';
        } else {
            foreach($articles as &$article) {
                $id_ctgry = (int)$article['ctgry'];
                $article['ctgry'] = $ctgries[$id_ctgry] ?? 'Unknown'; 
            }
            unset($article);
        }

        return $articles;
    }

    //check "length" of array
    function is_assoc_array($arr) {
        if (!is_array($arr)) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    //change id to nick
    function setNicksCom($pdo, $comments) {
        if(!$comments) return false;

        $db_get_nick = $pdo->prepare("SELECT nick FROM users WHERE id = :id");

        if(is_assoc_array($comments)) {
            $db_get_nick->bindValue(':id', (int)$comments['id_user'], PDO::PARAM_INT);
            $db_get_nick->execute();

            $nick = $db_get_nick->fetchColumn();

            //change id to nick in comment
            $comments['id_user'] = $nick;
        } else {
            foreach($comments as &$comment) {
                $db_get_nick->bindValue(':id', (int)$comment['id_user'], PDO::PARAM_INT);
                $db_get_nick->execute();

                $nick = $db_get_nick->fetchColumn();

                //change id to nick in comment
                $comment['id_user'] = $nick;
            }
            unset($comment);
        }

        return $comments;
    }

    //change id_author to nick
    function setNicksAuthor($pdo, $articles) {
        if(!$articles) return false;

        $db_get_nick = $pdo->prepare("SELECT nick FROM users WHERE id = :id");

        if(is_assoc_array($articles)) {
            $db_get_nick->bindValue(':id', (int)$articles['id_author'], PDO::PARAM_INT);
            $db_get_nick->execute();

            $nick = $db_get_nick->fetchColumn();

            //change id to nick in comment
            $articles['id_author'] = $nick;
        } else {
            foreach($articles as &$article) {
                $db_get_nick->bindValue(':id', (int)$article['id_author'], PDO::PARAM_INT);
                $db_get_nick->execute();

                $nick = $db_get_nick->fetchColumn();

                //change id to nick in comment
                $article['id_author'] = $nick;
            }
            unset($comment);
        }

        return $articles;
    }

    //clean xss function from comments
    function removeXSSComments($array) {
        if(!$array) return false;

        if(is_assoc_array($array)) {
            $array['msg'] = strip_tags($array['msg'], '<p><b><strong><em><i><ul><li><a>');
        } else {
            foreach($array as &$comment) {
                $comment['msg'] = strip_tags($comment['msg'], '<p><b><strong><em><i><ul><li><a>');
            }
            unset($comment);
        }
        
        return $array;
    }

    //clean xss function from article
    function removeXSSArticles($articles) {
        if(!$articles) return false;

        if(is_assoc_array($articles)) {
            $articles['title'] = strip_tags($articles['title']);
            $articles['short_desc'] = strip_tags($articles['short_desc'], '<p><b><strong><em><i><ul><li><a>');
            $articles['full_desc'] = strip_tags($articles['full_desc'], '<p><b><strong><em><i><ul><li><a>');
        } else {
            foreach($articles as &$article) {
                $article['title'] = strip_tags($article['title']);
                $article['short_desc'] = strip_tags($article['short_desc'], '<p><b><strong><em><i><ul><li><a>');
                $article['full_desc'] = strip_tags($article['full_desc'], '<p><b><strong><em><i><ul><li><a>');
            }
            unset($article);
        }
        
        return $articles;
    }

    //change id rank to rank's name
    function setNameRank($pdo, $id_rank) {
        //make array of all ranks
        $db_all_ranks = $pdo->prepare("SELECT * FROM ranks");
        $db_all_ranks->execute();

        $all_ranks = $db_all_ranks->fetchAll(PDO::FETCH_ASSOC); //array of ranks

        //make array id -> rank (to avoid problem if rank was deleted)
        $ranks = [];

        //if not assoc array -> use foreach to make assoc array
        if(is_array($all_ranks) && array_keys($all_ranks) === range(0, count($all_ranks) - 1)) {
            foreach($all_ranks as $rank) {
                $ranks[$rank['id']] = $rank['name'];
            }
        } else {
            $ranks = $all_ranks;
        }
         
        //return rank's name
        return $ranks[$id_rank] ?? 'undefinied';
    }

    //check for user rank promotion
    function checkPromotion($userRank, $userArtclsAppr) {
        //global requirements for rank promotion depends on approved artls
        $mod_req = 3;
        $adm_req = 5;

        //check user and make answer
        //send number of rank for promotion. 2 => mod; 3 => adm; false => no promotion
        $answer = ($userRank < 3 && $userArtclsAppr >= $adm_req)
                    ? 3
                    : (($userRank < 2 && $userArtclsAppr >= $mod_req) 
                    ? 2
                    : false); 

        //send back rank for promotion
        return $answer;
    }
?>