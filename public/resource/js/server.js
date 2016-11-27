//
jQuery.cachedScript = function(url, options) {
    options = $.extend(options || {}, {
        dataType: 'script',
        cache: true,
        url: url
    });
    return jQuery.ajax(options);
};
// Disable search and ordering by default
$.extend( $.fn.dataTable.defaults, {
	
    searching: false,
	"pagingType": "full_numbers",
	"lengthMenu": [[10, 15, 25, 50, 100, 200, 500, 1000], [10, 15, 25, 50, 100, 200, 500, 1000]],
	pageLength: 15,
	"dom": '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>'
} );

var cxpCountable = function(){
	return {
		init:function(){
			$('.autocount').each(function(){
				var obj = $(this);
				var id = obj.attr('id');
				var area = document.getElementById(id);
				Countable.live(area, function (counter) {
					var html = '\u5DF2\u8F93\u5165\u5B57\u7B26(\u4E0D\u5305\u62EC\u7A7A\u683C) ' + counter.characters + ' \u4E2A,\u5B57\u7B26(\u5305\u62EC\u7A7A\u683C) ' + counter.all + ' \u4E2A';
					var fieldname = obj.attr('data-fieldname');
					if(fieldname == 'seo_title'){
						// 75个字符
						html += '\uFF0C\u5EFA\u8BAE50-60\u4E2A\u5B57\u7B26';
					}
					if(fieldname == 'seo_keywords'){
						//
						html += '\uFF0C\u5EFA\u8BAE150-160\u4E2A\u5B57\u7B26';
					}
					if(fieldname == 'seo_description'){
						html += '\uFF0C\u5EFA\u8BAE150-160\u4E2A\u5B57\u7B26';
					}
				  $('#' + id + '_count').html(html);
				})
			});
		}
	}
}();

if (typeof toastr != 'undefined') {
	// 提示组件配置
	toastr.options = {
		"closeButton" : true,
		"debug" : false,
		"newestOnTop" : false,
		"progressBar" : true,
		"positionClass" : "toast-top-center",
		"preventDuplicates" : false,
		"onclick" : null,
		"showDuration" : "300",
		"hideDuration" : "1000",
		"timeOut" : "5000",
		"extendedTimeOut" : "1000",
		"showEasing" : "swing",
		"hideEasing" : "linear",
		"showMethod" : "fadeIn",
		"hideMethod" : "fadeOut"
	}
}
// 删除确认框
function del_confirm(title, msg, link) {
	var args1 = arguments;
	bootbox.dialog({
		message : msg,
		title : title,
		buttons : {
			main : {
				label : "\u786E\u5B9A",
				className : "btn-default",
				callback : function () {

					$.get(link, {
						/*csrf_test_name:$.cookie(CSRF_COOKIE_NAME)*/
					}, function (data) {
						var json = $.parseJSON(data);
						if (json.success) {
							if (args1.length > 3) {
								
								toastr.options.onShown = function () {
									if(typeof args1[3] === 'string'){
										var table = $('#' + args1[3]).DataTable();
										table.draw(false);
									}else if(typeof args1[3] === 'function'){
										// 如果有回调函数，调用回调函数
										var customcallback = args1[3];
										customcallback();
									}else{
										// nothing to do
									}
								}
							}else{
								// location.href = '';
								location.href = window.location.hash + '&after=del';
							}
							toastr.success(json.msg);
						} else {
							toastr.error(json.msg);
						}
					});
				}
			},
			success : {
				label : "\u53D6\u6D88",
				className : "btn-primary",
				callback : function () {
					// nothing to do
				}
			}
		}
	});
}

function changefieldvalue(rowid, tbname, tbfieldname) {
	$.post(BASE_URL + "?c=ajax&m=setfieldvalue", {
		tbname : tbname,
		tbfield : tbfieldname,
		tbfieldvalue : $("#" + tbfieldname + rowid).val(),
		id : rowid,
		csrf_test_name : $.cookie('csrf_cookie_name')
	},
		function (data) {});
}

function reset_dialog() {
	var dialog = top.dialog.get(window);
	// var dialog = top.dialog.get('dialog1');
	if (dialog) {
		dialog.width(768);
		dialog.height($('body').height() + 25);
		dialog.reset();
	}
}

function art_open(title, url, tableid) {
	var d = dialog({
			id : 'dialog2',
			title : title,
			url : url,
			width : 768,
			fixed : true,
			cancelValue:'\u5173\u95ED',
			cancel:function(){
				return true;
			},
			onremove : function () {
				if(typeof tableid === 'string'){
					var table = $('#' + tableid).DataTable();
					table.draw(false);
				}else if(typeof tableid === 'function'){
					// 回调函数
					tableid();
				}else{
					// nothing to do
				}
				
			}
		});
	// d.showModal();
	d.show();
}
function delrelaapp(obje) {
	var ool = $(obje).parent('li').parent('ol');
	$(obje).parent('li').remove();
	var obje_id = $(obje).attr('data-pk');
	var ool_prev = ool.prev();
	var arr = ool_prev.val().slice(1, ool_prev.val().length - 1).split(',');
	var arr1 = $.grep(arr, function (n, i) {
			return n != obje_id;
		}, false);
	if (arr1.length == 0) {
		ool_prev.val('');
	} else {
		ool_prev.val(',' + arr1.join(',') + ',');
	}
	//var id1 = ool_prev.attr('id').substring(ool_prev.attr('id').indexOf('_') + 1);
	var id1 = ool_prev.attr('id');
	// console.log(id1);
	var deloption = $('#del' + id1);
	var olddeloption = deloption.val();
	if (olddeloption == '') {
		deloption.val(',' + obje_id + ',');
	} else {
		deloption.val(olddeloption + obje_id + ',');
	}
}
$(function () {

	$.validator.setDefaults({
		ignore : "",
		highlight : function (element) {
			$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
			reset_dialog();
		},
		unhighlight : function (element) {
			$(element).closest('.form-group').removeClass('has-error');
			reset_dialog();
		}
	});

	$(document).on('click', '.cancel-form', function () {
		var dialog = top.dialog.get(window);
		if (typeof dialog != 'undefined') {
			dialog.remove();
		}
	});
});

$(function () {
	$(document).on('click', '.addpic', function () {
		var pk = $(this).attr('data-pk');
		var newarr = new Array();
		for (var ii = 0; ii < $('.img_item' + pk).length; ii++) {
			newarr.push($($('.img_item' + pk)[ii]).attr('id').substring(8));
		}
		var i = Math.max.apply(null, newarr) + 1;
		if (i == '-Infinity')
			i = 0;
		$('#' + pk + '_img').append('<li class="media img_item' + pk + '" id="img_item' + i + '">\
					<div class="media-left">\
					<img src="' + CDN_SERVER + 'resource/images/default.png" id="' + pk + '_input' + i + '_img" style="width:150px;" />\
					<input type="hidden" name="img_id' + pk + '[]" />\
					</div>\
					<div class="media-body"><input type="text" name="img_title' + pk + '[]" class="form-control" placeholder="\u56FE\u7247\u6807\u9898" />\
					<input type="text" name="' + pk + '[]" id="' + pk + '_input' + i + '" class="form-control" placeholder="\u9009\u62E9\u56FE\u7247" value="' + CDN_SERVER + 'resource/images/default.png?v=' + i + '" /> <a class="btn btn-default" href="javascript:;" onclick="BrowseServer(\'Images:/\', \'' + pk + '_input' + i + '\' );return false;"><i class="fa fa-folder-open icon-folder-open"></i> \u9009\u62E9\u6587\u4EF6</a> <i class="fa fa-trash icon-trash delpic" style="cursor:pointer;" data-pk="' + i + '"></i>\
					</div></li>');
	});
	$('body').on('click', '.delpic', function () {
		var pk = $(this).attr('data-pk');
		var optionid = $(this).attr('data-id');
		if($('.delpic').length == 1){
			// 已经是最后一个了，
			$('#' + optionid + '_img').after('<input type="hidden" name="' + optionid + '" value="">')
		}
		$('#img_item' + pk).remove();
		var old_val = $('#todelpic' + optionid).val();
		if (old_val == '') {
			var new_val = ',' + pk + ',';
		} else {
			var new_val = old_val + pk + ',';
		}
		$('#todelpic' + optionid).val(new_val);
	});

	$(document).on('click', '.sidebar-menu a', function () {
		$(this).parent('li').addClass('active');
		$(this).parent('li').siblings('li').removeClass('active');
	});
}); ;
(function ($) {
	/*
	 *  javascript复杂对象转url参数字符串
	 */
	var parseParam = function (param, key) {
		var paramStr = "";
		if (param instanceof String || param instanceof Number || param instanceof Boolean) {
			paramStr += "&" + key + "=" + encodeURIComponent(param);
		} else {
			$.each(param, function (i) {
				var k = key == null ? i : key + (param instanceof Array ? "[" + i + "]" : "." + i);
				paramStr += '&' + parseParam(this, k);
			});
		}
		return paramStr.substr(1);
	};
	var loadURL = function (uri) {
		$('.content-wrapper').load(BASE_URL + uri, function (response, status, xhr) {
			var source = '<section class="content-header">\
								      <h1>\
								        \u63D0\u793A\u4FE1\u606F\
								        <small></small>\
								      </h1>\
								      <ol class="breadcrumb">\
								        <li><a href="#/"><i class="fa fa-dashboard"></i> \u9996\u9875</a></li>\
								        <li class="active">\u63D0\u793A\u4FE1\u606F</li>\
								      </ol>\
								    </section>\
								    <section class="content">\
										<div class="alert alert-<%= status %> alert-dismissible">\
								            <h4><i class="icon fa fa-<%= icon %>"></i> \u63D0\u793A</h4>\
								            <%= msg %>\
								        </div>\
								    </section>';

			if (status == 'error') {
				var msg = "Sorry but there was an error: ";
				msg = msg + xhr.status + " " + xhr.statusText;
				var render = template.compile(source);
				var html = render({
						icon : 'ban',
						status : 'danger',
						msg : msg
					});
				$('.content-wrapper').html(html);
			} else {
				if (response.indexOf('{"success":') !== -1) {
					var json = $.parseJSON(response);
					if (json.success) {}
					else {
						var msg = json.msg;
						var render = template.compile(source);
						var html = render({
								icon : 'info',
								status : 'info',
								msg : msg
							});
						$('.content-wrapper').html(html);
					}
				}
			}
		});
	};
	var app = $.sammy(function () {
		var current_user = false;
		
		function checkLoggedIn(callback){
			// if(!current_user){
				$.getJSON(BASE_URL + 'login/status?callback=?', function(json){
					if(json.status){
						current_user = json;
						
						callback();
						// console.log('11');
					}else{
						current_user = false;
						
						top.location.href = BASE_URL + '?c=login';
					}
				});
			// }else{
				// callback();
				// console.log('22');
			// }
		}
		
		this.around(checkLoggedIn);
		
			this.disable_push_state = true;
			this.get('#/', function () {
				loadURL('index/dashboard');
			});
			this.get('#/(.*)', function () {
				var splat = this.params['splat'];
				var request_uri = parseParam(this.params);
				request_uri = request_uri.substring(0, request_uri.indexOf('&splat'));
				if (request_uri) {
					var request_url = '' + splat + '?' + request_uri;
				} else {
					var request_url = '' + splat;
				}
				loadURL(request_url);
			});
		});
	$(function () {
		app.run('#/');
	});
})(jQuery);