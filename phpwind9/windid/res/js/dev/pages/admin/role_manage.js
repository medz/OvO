/*!
 * PHPWind PAGE JS
 * 后台-管理角色
 * Author: linhao87@gmail.com
 */

Wind.use('dialog', 'ajaxForm', function () {
	
	//编辑_配置当前权限
	if (ROLE_AUTH_CONFIG) {
		$.each(ROLE_AUTH_CONFIG, function (i, o) {
			$('ul.J_ul_check input:checkbox[value = "' + o + '"]').attr('checked', true);
		});
		countCheckbox();
	}
	
	//已有角色权限复制
	var checkbox_list = $('ul.J_ul_check input:checkbox');
	
	$('#J_role_select').change(function () {
		var $this = $(this),
		select_item = ROLE_LIST_CONFIG[$this.children('option:selected').text()];
		if (select_item) {
			checkbox_list.removeAttr('checked');
			$.each(select_item, function (i, o) {
				$('ul.J_ul_check input:checkbox[value = "' + o + '"]').attr('checked', true);
			});
			countCheckbox();
		}
	});
	
	//复选框全选统计
	function countCheckbox() {
		var th_check, //全选框
		checkbox_num, //列表复选框总数
		checked_checkbox_num; //列表选中的复选框总数
		
		//从已有角色复制权限
		$.each($('ul.J_ul_check'), function (i, o) {
			var name = $(this).data('name');
			th_check = $('input#J_role_' + name); //获取对应全选框
			checkbox_num = $(this).find('input:checkbox').length; //复选框总数
			checked_checkbox_num = $(this).find('input:checkbox:checked').length; //选中的复选框总数
			if (checkbox_num !== checked_checkbox_num) {
				th_check.removeAttr('checked');
			} else {
				th_check.attr('checked', true);
			}
		});
	}
});
