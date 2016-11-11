--安装或更新时需要注册的sql写在这里--
ALTER TABLE `pw_bbs_threads_content`
ADD COLUMN `use_reply_for_lz`  tinyint(1) NOT NULL DEFAULT 0;

