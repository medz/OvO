<?php

abstract class App_SearchAction
{
    abstract public function countSearch($so);

    abstract public function search($so, $limit = 20, $start = 0);

    abstract public function build($list, $keywords);

    public function _highlighting($subject, $pattern)
    {
        return str_ireplace($pattern, '<em>'.$pattern.'</em>', $subject);
    }

    /**
     * 检查关键字查询条件.
     *
     * @param string $keyword
     *
     * @return string 关键字
     */
    protected function _checkKeywordCondition($keyword)
    {
        if (strlen($keyword) < 3) {
            return [];
        }
        $keyword = trim(($keyword));
        $keyword = str_replace(['&#160;', '&#61;', '&nbsp;', '&#60;', '<', '>', '&gt;', '(', ')', '&#41;'], ' ', $keyword);
        $ks = explode(' ', $keyword);
        $keywords = [];
        foreach ($ks as $v) {
            $v = trim($v);
            ($v) && $keywords[] = $v;
        }
        if (!$keywords) {
            return [];
        }
        $keywords = implode(' ', $keywords);

        return $keywords;
    }
}
