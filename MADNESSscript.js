$(function(){
	$.expr[':'].Contains = function(a,i,m){
		return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())==0;
	};
	function filterList(list, input){

		$(input).change(function(){
			var filter = $(this).val();
			if(filter){
				$matches = $(list).find('li:Contains(' + filter + ')');
				
				$('li', list).not($matches).slideUp();
				$matches.slideDown();
			}else{
				$(list).find("li").slideDown();
			}
			return false;
		}).keyup( function () {
			$(this).change();
		});
	}
	$(function(){
		filterList($("#fio_list"), $('#fio_input'));
	});
$(document).ready(function() { // вся мaгия пoсле зaгрузки стрaницы
});
	$(document).on('click','#fio_list li', function()
	{
		$("#fio_input").val($(this).text());
		$(".fio_list_wrap").hide();
		$("#fio_input").focus();
	});
	$(document).on('focus','#fio_input', function()
	{
		$(".fio_list_wrap").show();
	});
$(document).on('focus','input', function()
	{
		if($(this).attr('id')!='fio_input')
		{
			$(".fio_list_wrap").hide();
		}
	});
$(document).on('click', function()
	{
		if ($(event.target).closest(".fio_list_wrap").length) return;
		if ($(event.target).closest("#fio_input").length)
		{
			$(".fio_list_wrap").show();
			return;
		}
		$(".fio_list_wrap").hide();
		event.stopPropagation();
	});
// ---------------------------------
	$(document).on('click','#office_list li', function()
	{
		$("#office_input").val($(this).text());
		$(".office_list_wrap").hide();
		$("#office_input").focus();
	});
	$(document).on('focus','#office_input', function()
	{
		$(".office_list_wrap").show();
	});
$(document).on('focus','input', function()
	{
		if($(this).attr('id')!='office_input')
		{
			$(".office_list_wrap").hide();
		}
	});
$(document).on('click', function()
	{
		if ($(event.target).closest(".office_list_wrap").length) return;
		if ($(event.target).closest("#office_input").length)
		{
			$(".office_list_wrap").show();
			return;
		}
		$(".office_list_wrap").hide();
		event.stopPropagation();
	});
// ---------------------------------
	$(document).on('click','#organizations_list li', function()
	{
		$("#organizations_input").val($(this).text());
		$(".organizations_list_wrap").hide();
		$("#organizations_input").focus();
	});
	$(document).on('focus','#organizations_input', function()
	{
		$(".organizations_list_wrap").show();
	});
$(document).on('focus','input', function()
	{
		if($(this).attr('id')!='organizations_input')
		{
			$(".organizations_list_wrap").hide();
		}
	});
$(document).on('click', function()
	{
		if ($(event.target).closest(".organizations_list_wrap").length) return;
		if ($(event.target).closest("#organizations_input").length)
		{
			$(".organizations_list_wrap").show();
			return;
		}
		$(".organizations_list_wrap").hide();
		event.stopPropagation();
	});
//--------------------
	$(document).on('click','#user_region_list li', function()
	{
		$("#user_region_input").val($(this).text());
		$(".user_region_list_wrap").hide();
		$("#user_region_input").focus();
	});
	$(document).on('focus','#user_region_input', function()
	{
		$(".user_region_list_wrap").show();
	});
$(document).on('focus','input', function()
	{
		if($(this).attr('id')!='user_region_input')
		{
			$(".user_region_list_wrap").hide();
		}
	});
$(document).on('click', function()
	{
		if ($(event.target).closest(".user_region_list_wrap").length) return;
		if ($(event.target).closest("#user_region_input").length)
		{
			$(".user_region_list_wrap").show();
			return;
		}
		$(".user_region_list_wrap").hide();
		event.stopPropagation();
	});
//--------------------
});
