<?php

use Illuminate\Database\Seeder;
use Medz\Wind\Models\PwAdminRole;

class PwAdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PwAdminRole::create([
            'id' => 1,
            'name' => '管理员',
            'auths' => 'custom_set,config_site,config_nav,config_register,config_mobile,config_credit,config_editor,config_emotion,config_attachment,config_watermark,config_verifycode,config_seo,config_rewrite,config_domain,config_email,config_pay,config_area,config_school,u_groups,u_upgrade,u_manage,u_forbidden,u_check,bbs_article,contents_tag,contents_message,contents_report,bbs_contentcheck_forum,contentcheck_word,contents_user_tag,bbs_recycle,bbs_configbbs,bbs_setforum,bbs_setbbs,design_page,design_component,design_module,design_push,design_permissions,database_backup,cache_m,data_hook,cron_operations,log_manage,app_album,app_vote,app_medal,app_task,app_punch,app_link,app_message,app_announce,platform_server,platform_appList,platform_server_check,platform_index,platform_siteStyle,platform_upgrade',
            'created_time' => '1340275489',
            'modified_time' => '1347092145',
        ]);
    }
}
