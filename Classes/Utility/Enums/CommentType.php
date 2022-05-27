<?php namespace JournalTransporterPlugin\Utility\Enums;

import('classes.article.ArticleComment');

class CommentType
{
    /**
     * @var string[]
     */
    static $mapping = [
        COMMENT_TYPE_PEER_REVIEW => 'peer_review',
        COMMENT_TYPE_EDITOR_DECISION => 'editor_decision',
        COMMENT_TYPE_COPYEDIT => 'copyedit',
        COMMENT_TYPE_LAYOUT => 'layout',
        COMMENT_TYPE_PROOFREAD => 'proofread',
    ];

    /**
     * Turn a comment type integer into a label
     * @param $route
     * @return string
     */
    static public function getCommentTypeName($commentTypeId) {
        return @self::$mapping[$commentTypeId] ?: null;
    }
}