<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>
<script type="text/javascript">
function isObjectfunc(obj) {
    for (var i in obj) {
        return true;
    }
    return false;
}
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
		filterList($("#country_list"), $('#country_input'));
		filterList($("#country_list2"), $('#country_input2'));
		filterList($("#tnved_list"), $('#tnved_input'));
		filterList($("#unloadpoint_list"), $('#unloadpoint_input'));
		filterList($("#entities_list"), $('#entities_input'));
	});
	$('a[href^="#"]').click(function(){
        var el = $(this).attr('href');
        $('body').animate({
            scrollTop: $(el).offset().top}, 1000);
        $('#TTN_INPUT').focus();
        return false; 
	});
	$(document).on('click', '[name="next"]', function () 
	{
		if ($(this).val()=='Y')
		{
			$.ajax({
								url: '/form4/ajax.php',
								dataType: 'json',
								type: 'POST',
								data: {ID: '<?=$arResult["LAST_ID"]?>'},
								success: function(jsondata)
								{	
									if(isObjectfunc(jsondata))
									{
										if(jsondata.TTN.length)
										{
											$('#TTN_INPUT').val(jsondata.TTN);
										}
										if(jsondata.ADRESS.length)
										{
											$('#ADRESS_INPUT').val(jsondata.ADRESS);
										}
										if(jsondata.AUTHOR.length)
										{
											$('#AUTHOR_INPUT').val(jsondata.AUTHOR);
										}
										if(jsondata.QUALITY.length)
										{
											$('#QUALITY_INPUT').val(jsondata.QUALITY);
										}
										if(jsondata.COUNTRY.length)
										{
											$('#country_input').val(jsondata.COUNTRY);
										}
										if(jsondata.COUNTRY2.length)
										{
											$('#country_input2').val(jsondata.COUNTRY2);
										}
										if(jsondata.ENTITIES.length)
										{
											$('#entities_input').val(jsondata.ENTITIES);
										}
										if(jsondata.UNLOADPOINT.length)
										{
											$('#unloadpoint_input').val(jsondata.UNLOADPOINT);
										}
										if(jsondata.POINT.length)
										{
											$('#POINT_INPUT').val(jsondata.POINT);
										}
									}
								}
						});
		}
		if ($(this).val()=='N')
		{
			$('#TTN_INPUT, #ADRESS_INPUT, #AUTHOR_INPUT, #QUALITY_INPUT, #country_input, #country_input2, #entities_input, #unloadpoint_input, #POINT_INPUT').val('');
		}
	});
	$(document).on('click','#tnved_list li', function(){
		$("#tnved_input").val($(this).text());
		$(".tnved_list_wrap").hide();
		$("#tnved_input").focus();
	});
	$(document).on('focus','input', function(){
		if($(this).attr('id')!='tnved_input'){
			$(".tnved_list_wrap").hide();
		}
		if($(this).attr('id')!='country_input'){
			$(".country_list_wrap").hide();
		}
		if($(this).attr('id')!='country_input2'){
			$(".country_list_wrap2").hide();
		}
	});
	$(document).on('click', function(){
		if ($(event.target).closest(".tnved_list_wrap").length) return;
		if ($(event.target).closest("#tnved_input").length){
			$(".tnved_list_wrap").show();
			return;
		}
		$(".tnved_list_wrap").hide();
		event.stopPropagation();
	});
	$(document).on('click','#country_list li', function(){
		$("#country_input").val($(this).text());
		$(".country_list_wrap").hide();
		$("#country_input").focus();
	});
	$(document).on('click', function(){
		if ($(event.target).closest(".country_list_wrap").length) return;
		if ($(event.target).closest("#country_input").length){
			$(".country_list_wrap").show();
			return;
		}
		$(".country_list_wrap").hide();
		event.stopPropagation();
	});
	$(document).on('click','#country_list2 li', function(){
		$("#country_input2").val($(this).text());
		$(".country_list_wrap2").hide();
		$("#country_input2").focus();
	});
	$(document).on('click', function(){
		if ($(event.target).closest(".country_list_wrap2").length) return;
		if ($(event.target).closest("#country_input2").length){
			$(".country_list_wrap2").show();
			return;
		}
		$(".country_list_wrap2").hide();
		event.stopPropagation();
	});
/*---------------*/
$(document).on('click','#entities_list li', function()
	{
		if ($(this).text()!='Пустой справочник')
		{$("#entities_input").val($(this).text());}
		$(".entities_list_wrap").hide();
		$("#entities_input").focus();
	});
$(document).on('focus','input', function()
	{
		if($(this).attr('id')!='entities_input')
		{
			$(".entities_list_wrap").hide();
		}
	});
$(document).on('click', function()
	{
		if ($(event.target).closest(".entities_list_wrap").length) return;
		if ($(event.target).closest("#entities_input").length)
		{
			$(".entities_list_wrap").show();
			return;
		}
		$(".entities_list_wrap").hide();
		event.stopPropagation();
	});
/*---------------*/
$(document).on('click','#unloadpoint_list li', function()
	{
		if ($(this).text()!='Пустой справочник')
		{$("#unloadpoint_input").val($(this).text());}
		$(".unloadpoint_list_wrap").hide();
		$("#unloadpoint_input").focus();
	});
$(document).on('focus','input', function()
	{
		if($(this).attr('id')!='unloadpoint_input')
		{
			$(".unloadpoint_list_wrap").hide();
		}
	});
$(document).on('click', function()
	{
		if ($(event.target).closest(".unloadpoint_list_wrap").length) return;
		if ($(event.target).closest("#unloadpoint_input").length)
		{
			$(".unloadpoint_list_wrap").show();
			return;
		}
		$(".unloadpoint_list_wrap").hide();
		event.stopPropagation();
	});
});
</script>
<?/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */
$EmptyDictionary='Пустой справочник';
global $APPLICATION;
$dir = $APPLICATION->GetCurDir();
?>
<div class="vet_sert_4">
	<?if (!empty($arResult["PRELOAD_FORM4"]))
	{?>
	<div class="anchor">
		<a href="#form4_anchor"><?=GetMessage("GOTOFILL")?></a>
	</div></br>
	<?}?>
	<div id="container_preload">
		<?foreach ($arResult["PRELOAD_FORM4"] as $key => $value) 
			{?><div class="info">
				<a href="<?=$dir?>?form_4_ID=<?=$value["ID"]?>">
				<?=$value["NUMBER"]["NAME"]?>: <?=$value["NUMBER"]["VALUE"]?>
				<?=GetMessage("ACTIVEFROM")?><?=$value["DATE_ACTIVE_FROM"]?></br>
				<?=$value["TYPE"]["NAME"]?>: <?=$value["TYPE"]["VALUE"]?></br>
				<?=GetMessage("FROM")?><?=$value["COUNTRY"]["VALUE"]?><?=GetMessage("TO")?><?=$value["COUNTRY2"]["VALUE"]?></br>
				<?=$value["CODE"]["NAME"]?>: <?=$value["CODE"]["VALUE"]?></a></br>
				</div>
			<?}?>
	</div>
</div>
</br>
</br>
<div class="mfeedback" id="form4_anchor">
<form action="<?=POST_FORM_ACTION_URI?>" method="POST">
	<?if(!empty($arResult["ERROR_MESSAGE"]))
	{
		foreach($arResult["ERROR_MESSAGE"] as $v)
			ShowError($v);
	}
	if(strlen($arResult["OK_MESSAGE"]) > 0)
	 {?>
		<div class="mf-ok-text" id="mf-ok-text-id"><?=$arResult["OK_MESSAGE"]?></div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("NEXT")?>
			</div>
			<label><input type="radio" name="next" value="Y" style="width: 20px;"><?=$arResult["YES"]?></label><Br>
			<label><input type="radio" name="next" value="N" checked="checked" style="width: 20px;"><?=$arResult["NO"]?></label><Br>
		</div>
	<?}
	if(strlen($arResult["NOT_GOOD_MESSAGE"]) > 0)
	{?>
		<div class="mf-notok-text" id="mf-notok-text-id"><?=$arResult["NOT_GOOD_MESSAGE"]?></div>
	<?}?>
	<input id="last_id_input" type="hidden" name="last_id" value="<?=$arResult["LAST_ID"]?>">
	<?=bitrix_sessid_post()?>
	<div class="mf-name">
		<div class="mf-text">
			<?=GetMessage("TTN")?>
		</div>
		<input type="text" id="TTN_INPUT" name="ttn" value="<?=$arResult["TTN"]?>">
	</div>
	<div class="mf-name">
		<div class="mf-text">
			<?=GetMessage("QUALITY")?>
		</div>
		<input type="text" id="QUALITY_INPUT" name="quality" value="<?=$arResult["QUALITY"]?>">
	</div>
	<div class="mf-name">
		<div class="mf-text">
			<?=GetMessage("CODE")?>
		</div>
		<input type="text" id="tnved_input" name="code" value="<?=$arResult["CODE"]?>" autocomplete="off">
		<div class="tnved_list_wrap">
			<ul id="tnved_list">
				<?foreach($arResult['TNVEDS'] as $arItem){
					?><li data-id="<?=$arItem['UF_KOD']?>"><?=$arItem['UF_KOD']?> - <?=$arItem['UF_NAME']?></li><?
				}?>
			</ul>
		</div>
	</div>
	<div class="mf-name">
		<div class="mf-text">
			<?=GetMessage("TYPE")?>
		</div>
		<input type="text" name="type" value="<?=$arResult["TYPE"]?>">
	</div>
	<div class="mf-name">
		<div class="mf-text">
			<?=GetMessage("QUANTITY")?>
		</div>
		<input type="text" name="quantity" value="<?=$arResult["QUANTITY"]?>">
	</div>
	<div class="mf-name">
		<div class="mf-text">
			<?=GetMessage("WEIGHT")?>
		</div>
		<input type="text" name="weight" value="<?=$arResult["WEIGHT"]?>">
	</div>
	<div class="mf-name">
		<div class="mf-text">
			<?=GetMessage("POINT")?>
		</div>
		<input type="text" id="POINT_INPUT" name="point" value="<?=$arResult["POINT"]?>">
	</div>
	<div class="mf-name">
		<div class="mf-text">
			<?=GetMessage("ADRESS")?>
		</div>
		<input type="text" id="ADRESS_INPUT" name="adress" value="<?=$arResult["ADRESS"]?>">
	</div>
	<div class="mf-name">
		<div class="mf-text">
			<?=GetMessage("COUNTRY")?>
		</div>
		<input type="text" id="country_input" name="country" value="<?=$arResult["COUNTRY"]?>" autocomplete="off">
		<div class="country_list_wrap">
			<ul id="country_list">
				<?foreach($arResult['COUNTRIES'] as $arItem){
					?><li data-id="<?=$arItem['UF_NAME']?>"><?=$arItem['UF_NAME']?></li><?
				}?>
			</ul>
		</div>
	</div>
	<div class="mf-name">
		<div class="mf-text">
			<?=GetMessage("COUNTRY2")?>
		</div>
		<input type="text" id="country_input2" name="country2" value="<?=$arResult["COUNTRY2"]?>" autocomplete="off">
		<div class="country_list_wrap2">
			<ul id="country_list2">
				<?foreach($arResult['COUNTRIES'] as $arItem2){
					?><li data-id="<?=$arItem2['UF_NAME']?>"><?=$arItem2['UF_NAME']?></li><?
				}?>
			</ul>
		</div>
	</div>

	<div class="mf-name">
		<div class="mf-text">
			<?=GetMessage("UNLOADPOINT")?>
		</div>
		<input type="text" id="unloadpoint_input" name="unloadpoint" value="<?=$arResult["UNLOADPOINT"]?>" autocomplete="off">
		<div class="unloadpoint_list_wrap">
			<ul id="unloadpoint_list">
					<?if (!empty($arResult['unloadpoint']))
					{?>
						<?foreach($arResult['unloadpoint'] as $arItem)
						{
							?><li data-id="<?=$arItem['UF_XML_ID']?>"><?=$arItem['UF_NAME']?></li><?
						}
					}
					else
					{
						?><li data-id="0"><?=$EmptyDictionary?></li><?
					}?>
			</ul>
		</div>
	</div>
	<div class="mf-name">
		<div class="mf-text">
			<?=GetMessage("ENTITIES")?>
		</div>
		<input type="text" id="entities_input" name="entities" value="<?=$arResult["ENTITIES"]?>" autocomplete="off">
		<div class="entities_list_wrap">
			<ul id="entities_list">
					<?if (!empty($arResult['entities']))
					{?>
						<?foreach($arResult['entities'] as $arItem)
						{
							?><li data-id="<?=$arItem['UF_XML_ID']?>"><?=$arItem['UF_NAME']?></li><?
						}
					}
					else
					{
						?><li data-id="0"><?=$EmptyDictionary?></li><?
					}?>
			</ul>
		</div>
	</div>
	<div class="mf-name">
		<div class="mf-text">
			<?=GetMessage("AUTHOR")?>
		</div>
		<input type="text" id="AUTHOR_INPUT" name="author" value="<?=$arResult["AUTHOR"]?>">
	</div>
	<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
	<input type="submit" name="submit" value="<?=GetMessage("MFT_SUBMIT")?>">
	<input type="button" style="float:right;" name="to_main" onclick="location.href = 'http://<?=$_SERVER['SERVER_NAME']?>'" value="<?=GetMessage("TO_MAIN")?>">
</form>
</div>
</br>