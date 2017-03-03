<?php

 
/**
 * 搜索记录DM.
 */
class App_Search_Dm extends PwUserInfoDm
{
    public function setLastSearchTime($last_search_time)
    {
        $this->_data['last_search_time'] = intval($last_search_time);

        return $this;
    }
}
