<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>
<script type="text/javascript">
$(function(){
	document.getElementById('quantity_input_items').oninput = function () {
	  if (this.value.length > 6) this.value = this.value.substr(0, 6); // в поле можно ввести только 6 символов
	}
	document.getElementById('quantity_input_places').oninput = function () {
	  if (this.value.length > 5) this.value = this.value.substr(0, 5); // в поле можно ввести только 5 символов
	}
	document.getElementById('quantity_input_weight').oninput = function () {
	  if (this.value.length > 10) this.value = this.value.substr(0, 10); // в поле можно ввести только 5 символов
	}
	$.expr[':'].Contains = function(a,i,m){
		return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())==0;
	};
	function filterList(list, input){//фильтрация по вводу

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
			if ($(this).attr("id")=='officialname_input')
			{
				$('#jobname_input').attr("value","123");
			}
		});
	}
	filterList($("#nameauthbody_list"), $('#nameauthbody_input'));//вызов фильтрации
	filterList($("#cityauthbody_list"), $('#cityauthbody_input'));
	filterList($("#nameissuedregnum_list"), $('#nameissuedregnum_input'));
	filterList($("#posissuedregnum_list"), $('#posissuedregnum_input'));
	filterList($("#issued_list"), $('#issued_input'));
	filterList($("#transport_list"), $('#transport_input'));
	filterList($("#manufactname_list"), $('#manufactname_input'));
	filterList($("#productionadress_list"), $('#productionadress_input'));
	filterList($("#quantity_list"), $('#quantity_input'));
	filterList($("#recipientname_list"), $('#recipientname_input'));
	filterList($("#specialsnotes_list"), $('#specialsnotes_input'));
	filterList($("#officialname_list"), $('#officialname_input'));
	filterList($("#placeload_list"), $('#placeload_input'));
	filterList($("#unloadpoint_list"), $('#unloadpoint_input'));
	filterList($("#entities_list"), $('#entities_input'));
	filterList($("#country_list"), $('#country_input'));
	filterList($("#country2_list"), $('#country2_input'));
	if ($("#foundfitfor_id").val()=='WITH_LIMITS')//если подгрузилось с ограничениями
	{
		$("#reasons").show();
	}
	else 
	{
		$("#reasons").hide();
	}
	$(document).on('change','#foundfitfor_id', function()//если выбрано с ограничениями
	{
		if ($("#foundfitfor_id").val()=='WITH_LIMITS')
		{
			$("#reasons").show();
		}
		else 
		{
			$("#reasons").hide();
		}
	});
	$(document).on('click','#nameauthbody_list li', function()
	{
		if ($(this).text()!='Пустой справочник')
		{$("#nameauthbody_input").val($(this).text());}
		$(".nameauthbody_list_wrap").hide();
		$("#nameauthbody_input").focus();
	});

	$(document).on('click', function()
	{
		if ($(event.target).closest(".nameauthbody_list_wrap").length) return;
		if ($(event.target).closest("#nameauthbody_input").length)
		{
			$(".nameauthbody_list_wrap").show();
			return;
		}
		$(".nameauthbody_list_wrap").hide();
		event.stopPropagation();
	});
	$( "#inputNextDate" ).change(function() {
		var prevdate=$('input[name=productiondate]').val();
		var thisdate=$(this).val();
		var separator=', ';
		if (!$('input[name=productiondate]').val().length)
		{
			separator='';
		}
		$('input[name=productiondate]').val(prevdate+separator+thisdate);
	});
	$('a[href^="#"]').click(function()
	{
		var el = $(this).attr('href');
		$('body').animate({
			scrollTop: $(el).offset().top}, 1000);
			if ($('#nameauthbody_input').is(':hidden'))
				{$('#cityauthbody_input').focus();}
			else
				{$('#nameauthbody_input').focus();}
			return false; 
	});
		$( ".showcontent, .hidecontent" ).hover(
	  function() {
	    $( this ).addClass( "hover" );
	  }, function() {
	    $( this ).removeClass( "hover" );
	  }
		);
		$(document).on('click','.showcontent', function()
		{
			// $("#quantity_input").prop("disabled", true);
			$(this).hide();
			$(".hiddencontent").show();
			$(".hidecontent").show();
			$("#quantity_input_numbers").focus();
		});
		$(document).on('click','.hidecontent', function()
		{
			$(this).hide();
			$(".showcontent").show();
			$(".hiddencontent").hide();
			$("#quantity_input").focus();
		});
		$(document).on('keyup','#quantity_input_weight', function()
		{	
			var floatRegex = '[.]+[0-9]+[0-9]+[0-9]'; 
			var found = $(this).val().match(floatRegex);
			if ($("#quantity_input_weight").val().length==6 && found==null)
			{
				$("#quantity_input_weight").val($(this).val()+".000");
			}
		});
		$('#quantity_input_weight').blur(function() {
			var floatRegex = '[.]+[0-9]+[0-9]+[0-9]'; 
			var found = $(this).val().match(floatRegex);
			if ($("#quantity_input_weight").val().length<=6 && $("#quantity_input_weight").val().length>=1 && found==null)
			{
				$("#quantity_input_weight").val($(this).val()+".000");
			}

		});
		$(document).on('keyup',' #quantity_input_upakovka, #quantity_input_markirovka, #quantity_input_places, #quantity_input_items, #quantity_input_weight', function(key)
		{
			if(key.charCode == 44) return false;
			$("#quantity_input").val(
					"мест "
					+$("#quantity_input_places").val()
					+" штук "
					+$("#quantity_input_items").val()
					+" вес "
					+$("#quantity_input_weight").val()
					+","
					+$("#quantity_input_upakovka").val()
					+","
					+$("#quantity_input_markirovka").val()
				);
		});
		if (!$('input[name=regnum]').is(':disabled'))
		{
			$("input[name=regnum]").mask("№ 11-3/99999 от 99.99.9999 г.",
				{completed:function()
					{
						$("#posissuedregnum_input").focus();
					}});
		}
	$("input[name=formnum]").mask("BY № 99 99999999",
		{completed:function()
			{
				$("input[name=dateissuecert]").focus();
			}});

	$(document).on('focus','input', function()
		{
			if($(this).attr('id')!='nameauthbody_input')
			{
				$(".nameauthbody_list_wrap").hide();
			}
			if($(this).attr('id')!='cityauthbody_input')
			{
				$(".cityauthbody_list_wrap").hide();
			}
			if($(this).attr('id')!='nameissuedregnum_input')
			{
				$(".nameissuedregnum_list_wrap").hide();
			}
			if($(this).attr('id')!='issued_input')
			{
				$(".issued_list_wrap").hide();
			}
			if($(this).attr('id')!='transport_input')
			{
				$(".transport_list_wrap").hide();
			}
			if($(this).attr('id')!='posissuedregnum_input')
			{
				$(".posissuedregnum_list_wrap").hide();
			}
			if($(this).attr('id')!='manufactname_input')
			{
				$(".manufactname_list_wrap").hide();
			}
			if($(this).attr('id')!='productionadress_input')
			{
				$(".productionadress_list_wrap").hide();
			}
			if($(this).attr('id')!='quantity_input')
			{
				$(".quantity_list_wrap").hide();
			}
			if($(this).attr('id')!='recipientname_input')
			{
				$(".recipientname_list_wrap").hide();
			}
			if($(this).attr('id')!='specialsnotes_input')
			{
				$(".specialsnotes_list_wrap").hide();
			}
			if($(this).attr('id')!='officialname_input')
			{
				$(".officialname_list_wrap").hide();
			}
			if($(this).attr('id')!='placeload_input')
			{
				$(".placeload_list_wrap").hide();
			}
			if($(this).attr('id')!='unloadpoint_input')
			{
				$(".unloadpoint_list_wrap").hide();
			}
			if($(this).attr('id')!='entities_input')
			{
				$(".entities_list_wrap").hide();
			}
			if($(this).attr('id')!='country_input')
			{
				$(".country_list_wrap").hide();
			}
			if($(this).attr('id')!='country2_input')
			{
				$(".country2_list_wrap").hide();
			}
	});
$(document).on('click', function()
	{
		if ($(event.target).closest(".cityauthbody_list_wrap").length) return;
		if ($(event.target).closest("#cityauthbody_input").length)
		{
			$(".cityauthbody_list_wrap").show();
			return;
		}
		if ($(event.target).closest(".nameissuedregnum_list_wrap").length) return;
		if ($(event.target).closest("#nameissuedregnum_input").length)
		{
			$(".nameissuedregnum_list_wrap").show();
			return;
		}
		$(".nameissuedregnum_list_wrap").hide();
		$(".cityauthbody_list_wrap").hide();
		if ($(event.target).closest(".transport_list_wrap").length) return;
		if ($(event.target).closest("#transport_input").length)
		{
			$(".transport_list_wrap").show();
			return;
		}
		if ($(event.target).closest(".posissuedregnum_list_wrap").length) return;
		if ($(event.target).closest("#posissuedregnum_input").length)
		{
			$(".posissuedregnum_list_wrap").show();
			return;
		}
		if ($(event.target).closest(".manufactname_list_wrap").length) return;
		if ($(event.target).closest("#manufactname_input").length)
		{
			$(".manufactname_list_wrap").show();
			return;
		}
		event.stopPropagation();
		if ($(event.target).closest(".productionadress_list_wrap").length) return;
		if ($(event.target).closest("#productionadress_input").length)
		{
			$(".productionadress_list_wrap").show();
			return;
		}
		if ($(event.target).closest(".quantity_list_wrap").length) return;
		if ($(event.target).closest("#quantity_input").length)
		{
			$(".quantity_list_wrap").show();
			return;
		}
		if ($(event.target).closest(".recipientname_list_wrap").length) return;
		if ($(event.target).closest("#recipientname_input").length)
		{
			$(".recipientname_list_wrap").show();
			return;
		}
		if ($(event.target).closest(".specialsnotes_list_wrap").length)return;
		if ($(event.target).closest("#specialsnotes_input").length)
		{
			$(".specialsnotes_list_wrap").show();
			return;
		}
		if ($(event.target).closest(".officialname_list_wrap").length) return;
		if ($(event.target).closest("#officialname_input").length)
		{
			$(".officialname_list_wrap").show();
			return;
		}
		if ($(event.target).closest(".placeload_list_wrap").length) return;
		if ($(event.target).closest("#placeload_input").length)
		{
			$(".placeload_list_wrap").show();
			return;
		}
		if ($(event.target).closest(".unloadpoint_list_wrap").length) return;
		if ($(event.target).closest("#unloadpoint_input").length)
		{
			$(".unloadpoint_list_wrap").show();
			return;
		}
		if ($(event.target).closest(".entities_list_wrap").length) return;
		if ($(event.target).closest("#entities_input").length)
		{
			$(".entities_list_wrap").show();
			return;
		}
		if ($(event.target).closest(".country_list_wrap").length) return;
		if ($(event.target).closest("#country_input").length)
		{
			$(".country_list_wrap").show();
			return;
		}
		if ($(event.target).closest(".country2_list_wrap").length) return;
		if ($(event.target).closest("#country2_input").length)
		{
			$(".country2_list_wrap").show();
			return;
		}
		if ($(event.target).closest(".issued_list_wrap").length) return;
		if ($(event.target).closest("#issued_input").length)
		{
			$(".issued_list_wrap").show();
			return;
		}
		$(".issued_list_wrap").hide();
		$(".transport_list_wrap").hide();
		$(".posissuedregnum_list_wrap").hide();
		$(".manufactname_list_wrap").hide();
		$(".productionadress_list_wrap").hide();
		$(".quantity_list_wrap").hide();
		$(".recipientname_list_wrap").hide();
		$(".specialsnotes_list_wrap").hide();
		$(".officialname_list_wrap").hide();
		$(".placeload_list_wrap").hide();
		$(".unloadpoint_list_wrap").hide();
		$(".entities_list_wrap").hide();
		$(".country_list_wrap").hide();
		$(".country2_list_wrap").hide();
		event.stopPropagation();
	});
/*---------------*/
$(document).on('click','#cityauthbody_list li', function()
	{
		if ($(this).text()!='Пустой справочник')
		{$("#cityauthbody_input").val($(this).text());}
		$(".cityauthbody_list_wrap").hide();
		$("#cityauthbody_input").focus();
	});
/*---------------*/
$(document).on('click','#nameissuedregnum_list li', function()
	{
		if ($(this).text()!='Пустой справочник')
		{$("#nameissuedregnum_input").val($(this).text());}
		$(".nameissuedregnum_list_wrap").hide();
		$("#nameissuedregnum_input").focus();
	});

/*---------------*/
$(document).on('click','#issued_list li', function()
	{
		if ($(this).text()!='Пустой справочник')
		{$("#issued_input").val($(this).text());}
		$(".issued_list_wrap").hide();
		$("#issued_input").focus();
	});

/*---------------*/
$(document).on('click','#transport_list li', function()
	{
		if ($(this).text()!='Пустой справочник')
		{$("#transport_input").val($(this).text());}
		$(".transport_list_wrap").hide();
		$("#transport_input").focus();
	});
/*---------------*/
$(document).on('click','#posissuedregnum_list li', function()
	{
		if ($(this).text()!='Пустой справочник')
		{$("#posissuedregnum_input").val($(this).text());}
		$(".posissuedregnum_list_wrap").hide();
		$("#posissuedregnum_input").focus();
	});

/*---------------*/
$(document).on('click','#manufactname_list li', function()
	{
		if ($(this).text()!='Пустой справочник')
		{$("#manufactname_input").val($(this).text());}
		$(".manufactname_list_wrap").hide();
		$("#manufactname_input").focus();
	});

/*---------------*/
$(document).on('click','#productionadress_list li', function()
	{
		if ($(this).text()!='Пустой справочник')
		{$("#productionadress_input").val($(this).text());}
		$(".productionadress_list_wrap").hide();
		$("#productionadress_input").focus();
	});

/*---------------*/
$(document).on('click','#quantity_list li', function()
	{
		if ($(this).text()!='Пустой справочник')
		{$("#quantity_input").val($(this).text());}
		$(".quantity_list_wrap").hide();
		$("#quantity_input").focus();
	});

/*---------------*/
$(document).on('click','#recipientname_list li', function()
	{
		if ($(this).text()!='Пустой справочник')
		{$("#recipientname_input").val($(this).text());}
		$(".recipientname_list_wrap").hide();
		$("#recipientname_input").focus();
	});

/*---------------*/
$(document).on('click','#specialsnotes_list li', function()
	{
		if ($(this).text()!='Пустой справочник')
		{$("#specialsnotes_input").val($(this).text());}
		$(".specialsnotes_list_wrap").hide();
		$("#specialsnotes_input").focus();
	});

$(document).on('click','#officialname_list li', function()
	{
		if ($(this).text()!='Пустой справочник')
		{$("#officialname_input").val($(this).text());
		$("#jobname_input").val($(this).attr("value"));}
		$(".officialname_list_wrap").hide();
		$("#officialname_input").focus();
	});

/*---------------*/
$(document).on('click','#placeload_list li', function()
	{
		if ($(this).text()!='Пустой справочник')
		{$("#placeload_input").val($(this).text());}
		$(".placeload_list_wrap").hide();
		$("#placeload_input").focus();
	});

/*---------------*/
$(document).on('click','#unloadpoint_list li', function()
	{
		if ($(this).text()!='Пустой справочник')
		{$("#unloadpoint_input").val($(this).text());}
		$(".unloadpoint_list_wrap").hide();
		$("#unloadpoint_input").focus();
	});

/*---------------*/
$(document).on('click','#entities_list li', function()
	{
		if ($(this).text()!='Пустой справочник')
		{$("#entities_input").val($(this).text());}
		$(".entities_list_wrap").hide();
		$("#entities_input").focus();
	});

/*---------------*/
$(document).on('click','#country_list li', function()
	{
		if ($(this).text()!='Пустой справочник')
		{$("#country_input").val($(this).text());}
		$(".country_list_wrap").hide();
		$("#country_input").focus();
	});

/*---------------*/
$(document).on('click','#country2_list li', function()
	{
		if ($(this).text()!='Пустой справочник')
		{$("#country2_input").val($(this).text());}
		$(".country2_list_wrap").hide();
		$("#country2_input").focus();
	});

});
</script>
<?
global $APPLICATION;
$dir = $APPLICATION->GetCurDir();
//country choose
function getcountry($id1,$array1)
{	
	foreach ($array1 as $key => $value) 
	{	
		if ($value["UF_XML_ID"]==$id1)
		{
			return $value["UF_NAME"];
			break;
		}
	}
}
//значения по умолчанию
if (!strlen($arResult['DATEISSUECERT'])>0)
{
	 $arResult['DATEISSUECERT']=date("d.m.Y"); 
}
if (!strlen($arResult['PRODUCTIONDATE'])>0)
{
	 $arResult['DATEISSUECERT']=date("d.m.Y"); 
}
$EmptyDictionary='Пустой справочник';
?>
<div class="vet_sert_2">
		<?if(count($arResult["PRELOAD_FORM2"])>0)
		{?>
		<div class="anchor">
			<a href="#form2_anchor">Перейти к заполнению</a>
		</div></br>
		<?}?>
		<div id="container_preload">
		<?foreach ($arResult["PRELOAD_FORM2"] as $key => $value) 
			{?>
				<div class="info">
					<div class="rightside">
						[<a href="<?=$dir?>print.php?form_2_ID=<?=$value["ID"]?>" target="_blank">Печать</a>]
					</div>
					<a href="<?=$dir?>?form_2_ID=<?=$value["ID"]?>">
					<?=$value["REG_NUM"]["NAME"]?>: <?=$value["REG_NUM"]["VALUE"]?>
					от: <?=$value["DATE_ACTIVE_FROM"]?></br>
					<?=$value["FORM_NUM"]["NAME"]?>: <?=$value["FORM_NUM"]["VALUE"]?></br>
					<?=$value["PRODUCT_NAME"]["NAME"]?>: <?=$value["PRODUCT_NAME"]["VALUE"]?></br>
					<?//getcountry($value["COUNTRY_ORIGIN"]["VALUE"],$arResult["COUNTRIES"])
					// getcountry($value["COUNTRY_DEST"]["VALUE"],$arResult["COUNTRIES"])?>
					<?=$value["ENTITIES"]["NAME"]?>: <?=$value["ENTITIES"]["VALUE"];?>
					</a></br>
				</div>
			<?}?>
		</div>
</div>
</br>
<div class="mfeedback" id="form2_anchor">
	<form action="/form2/" method="GET">
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("MF_OWNPRODUCT");?>
			</div></br>
			<input type="radio" name="ownproduct"  class="rg" value="Y" id="YES" 
			<?if($arParams["YESNO"]=="Y"){echo 'checked="checked"';}?>">
			<label for="YES"><?=GetMessage("MF_OWNPRODUCT_YES");?></label></br>
		 	<input type="radio" name="ownproduct"  class="rg" value="N" id="NO"  
		 	<?if($arParams["YESNO"]=="N"){echo 'checked="checked"';}?>">
		 	<label for="NO"><?=GetMessage("MF_OWNPRODUCT_NO");?></label></br></br>
			<button type="submit"><?=GetMessage("MF_OWNPRODUCT_CONFIRM");?></button></br></br>
		</div>
		</form>
	<?if (strlen($arResult["PRINT_ID"])>0)
		{?>
			<input type="button" name="print" onclick="window.open('<?=$dir?>print.php?form_2_ID=<?=$arResult["PRINT_ID"]?>'); return false;" value="Печать" />
		<?}?>
		<?if(strlen($arResult["OK_MESSAGE"]) > 0)
		{?>
			<div class="mf-ok-text"><?=$arResult["OK_MESSAGE"]?></div>
		<?}?>
	<?if(strlen($arResult["NOT_GOOD_MESSAGE"]) > 0)
	{?>
		<div class="mf-notok-text" id="mf-notok-text-id"><?=$arResult["NOT_GOOD_MESSAGE"]?></div>
	<?}?>
		<?if(!empty($arResult["ERROR_MESSAGE"]))
			{
				foreach($arResult["ERROR_MESSAGE"] as $v)
					ShowError($v);
			}?>
	<form action="<?=POST_FORM_ACTION_URI?>" method="POST">
		<?=bitrix_sessid_post();?>
		<?if($arParams["YESNO"]=="N"/*||(strlen($_GET["form_2_ID"])>0)*/)
		{?>
			<div class="mf-name">
				<div class="mf-text">
					<?=GetMessage("NAMEAUTHBODY")?>
				</div>
				<input type="text" id="nameauthbody_input" name="nameauthbody" value="<?=$arResult['NAMEAUTHBODY'];?>" autocomplete="off">
				<div class="nameauthbody_list_wrap">
					<ul id="nameauthbody_list">
						<?if (!empty($arResult['nameauthbody']))
						{?>
							<?foreach($arResult['nameauthbody'] as $arItem)
							{
								?><li data-id="<?=$arItem['UF_XML_ID']?>"><?=$arItem['UF_NAME']?></li><?
							}
						}
						else
						{
							/*?><li data-id="0"><?=$EmptyDictionary?></li><?*/
						}?>
					</ul>
				</div>
			</div>
		<?}
			else
			{
				?>
				<div class="mf-name">
					<input type="text" id="nameauthbody_input" name="nameauthbody" value="<?=$arResult['NAMEAUTHBODY'];?>" hidden>
				</div>
			<?}
		?>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("CITYAUTHBODY")?>
			</div>
			<input type="text" id="cityauthbody_input" name="cityauthbody" value="<?=$arResult['CITYAUTHBODY'];?>" autocomplete="off">
			<div class="cityauthbody_list_wrap">
				<ul id="cityauthbody_list">
					<?if (!empty($arResult['cityauthbody']))
					{?>
						<?foreach($arResult['cityauthbody'] as $arItem)
						{
							?><li data-id="<?=$arItem['UF_XML_ID']?>"><?=$arItem['UF_NAME']?></li><?
						}
					}
					else
					{
						/*?><li data-id="0"><?=$EmptyDictionary?></li><?*/
					}?>
				</ul>
			</div>
		</div>
			<div class="mf-name">
				<div class="mf-text">
					<?=GetMessage("REGNUM")?>
				</div>
				<input type="text" name="regnum" value="<?=$arResult['REGNUM'];?>" <?if ($arParams["YESNO"]=="Y"){echo " disabled";};?>>
			</div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("POSISSUEDREGNUM")?>
			</div>
			<input type="text" id="posissuedregnum_input" name="posissuedregnum" value="<?=$arResult['POSISSUEDREGNUM'];?>" autocomplete="off">
			<div class="posissuedregnum_list_wrap">
				<ul id="posissuedregnum_list">
					<?if (!empty($arResult['posissuedregnum']))
					{?>
						<?foreach($arResult['posissuedregnum'] as $arItem)
						{
							?><li data-id="<?=$arItem['UF_XML_ID']?>"><?=$arItem['UF_NAME']?></li><?
						}
					}
					else
					{
						/*?><li data-id="0"><?=$EmptyDictionary?></li><?*/
					}?>
				</ul>
			</div>
		</div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("NAMEISSUEDREGNUM")?>
			</div>
			<input type="text" id="nameissuedregnum_input" name="nameissuedregnum" value="<?=$arResult['NAMEISSUEDREGNUM'];?>" autocomplete="off">
			<div class="nameissuedregnum_list_wrap">
				<ul id="nameissuedregnum_list">
					<?if (!empty($arResult['nameissuedregnum']))
					{?>
						<?foreach($arResult['nameissuedregnum'] as $arItem)
						{
							?><li data-id="<?=$arItem['UF_XML_ID']?>"><?=$arItem['UF_NAME']?></li><?
						}
					}
					else
					{
						/*?><li data-id="0"><?=$EmptyDictionary?></li><?*/
					}?>
				</ul>
			</div>
		</div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("FORMNUM")?>
			</div>
			<input type="text" name="formnum" value="<?=$arResult['FORMNUM'];?>">
		</div>
			<script type="text/javascript">
				$(function() {
				    $.datepicker.setDefaults($.datepicker.regional['ru']);
				    $('input[name="dateissuecert"]').datepicker({
					    showOn: "focus",
					    changeMonth: true,
						changeYear: true,
					});
							
				});
			</script>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("DATEISSUECERT")?>
			</div>
			<input type="text" name="dateissuecert" value="<?=$arResult['DATEISSUECERT'];?>">
		</div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("ISSUED")?>
			</div>
			<input type="text" id="issued_input" name="issued" value="<?=$arResult['ISSUED'];?>" autocomplete="off">
			<div class="issued_list_wrap">
				<ul id="issued_list">
					<?if (!empty($arResult['issued']))
					{?>
						<?foreach($arResult['issued'] as $arItem)
						{
							?><li data-id="<?=$arItem['UF_XML_ID']?>"><?=$arItem['UF_NAME']?></li><?
						}
					}
					else
					{
						/*?><li data-id="0"><?=$EmptyDictionary?></li><?*/
					}?>
				</ul>
			</div>
		</div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("QUANTITY")?>
			</div>
			<input type="text" id="quantity_input" name="quantity" value="<?=$arResult['QUANTITY'];?>" autocomplete="off">
			<div class="quantity_list_wrap">
				<ul id="quantity_list">
					<?if (!empty($arResult['quantity']))
					{?>
						<?foreach($arResult['quantity'] as $arItem)
						{
							?><li data-id="<?=$arItem['UF_XML_ID']?>"><?=$arItem['UF_NAME']?></li><?
						}
					}
					else
					{
						/*?><li data-id="0"><?=$EmptyDictionary?></li><?*/
					}?>
				</ul>
			</div>
			<div class="showcontent">▼заполнить "в количестве"▼</div>
			<div class="hiddencontent" hidden>
				<div class="mf-text">
					<!-- <?=GetMessage("QUANTITY")?> -->
					Мест
				</div>
				<input type="number" id="quantity_input_places" autocomplete="off" pattern="^[ 0-9]+$">
				<div class="mf-text">
					<!-- <?=GetMessage("QUANTITY")?> -->
					Штук
				</div>
				<input type="number" id="quantity_input_items" autocomplete="off" pattern="^[ 0-9]+$">
				<div class="mf-text">
					<!-- <?=GetMessage("QUANTITY")?> -->
					Вес
				</div>
				<input type="number" id="quantity_input_weight" autocomplete="off" step="0.001" >
				<div class="mf-text">
					<!-- <?=GetMessage("QUANTITY")?> -->
					Упаковка
				</div>
				<input type="text" id="quantity_input_upakovka" autocomplete="off" >
				<div class="mf-text">
					<!-- <?=GetMessage("QUANTITY")?> -->
					Маркировка
				</div>
				<input type="text" id="quantity_input_markirovka" autocomplete="off">
			</div>
			<div class="hidecontent" hidden>▲готово▲</div>
		</div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("MANUFACTNAME")?>
			</div>
			<input type="text" id="manufactname_input" name="manufactname" value="<?=$arResult['MANUFACTNAME'];?>" autocomplete="off">
			<div class="manufactname_list_wrap">
				<ul id="manufactname_list">
					<?if (!empty($arResult['manufactname']))
					{?>
						<?foreach($arResult['manufactname'] as $arItem)
						{
							?><li data-id="<?=$arItem['UF_XML_ID']?>"><?=$arItem['UF_NAME']?></li><?
						}
					}
					else
					{
						/*?><li data-id="0"><?=$EmptyDictionary?></li><?*/
					}?>
				</ul>
			</div>
		</div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("PRODUCTIONDATE")?>
			</div>
			<input type="text" name="productiondate" value="<?=$arResult['PRODUCTIONDATE'];?>">
			<script type="text/javascript">
				$(function() {
					
				    $.datepicker.setDefaults($.datepicker.regional['ru']);
				    $('#inputNextDate').datepicker({
					    showOn: "button",
					    buttonImage: "/bitrix/templates/corp_services_gray/images/calendar.gif",
					    changeMonth: true,
						changeYear: true,
					    buttonImageOnly: true
					});
							
				});
			</script>
			<input type="text" id="inputNextDate" hidden="hidden">
		</div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("FOUNDFITFOR")?>
			</div>
				<select name="foundfitfor" value="<?=$arResult['FOUNDFITFOR'];?>" id="foundfitfor_id">
				  <option value="WITHOUT_LIMITS" <?if ($arResult['FOUNDFITFOR']=="WITHOUT_LIMITS") echo 'selected="selected"';?>>Реализация без ограничений.</option>
				  <option value="WITH_LIMITS" <?if ($arResult['FOUNDFITFOR']=="WITH_LIMITS") echo 'selected="selected"';?>>Реализация с ограничениями</option>
				  <option value="WITH_RULES" <?if ($arResult['FOUNDFITFOR']=="WITH_RULES") echo 'selected="selected"';?>>Переработка согласно правилам ветсанэкспертизы</option>
				</select>
		</div>
		<div class="mf-name" id="reasons">
			<div class="mf-text">
				<?=GetMessage("REASONS")?>
			</div>
			<input type="text" name="reasons" value="<?=$arResult['REASONS'];?>">
		</div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("TRANSPORT")?>
			</div>
			<input type="text" id="transport_input" name="transport" value="<?=$arResult['TRANSPORT'];?>" autocomplete="off">
			<div class="transport_list_wrap">
				<ul id="transport_list">
					<?if (!empty($arResult['transport']))
					{?>
						<?foreach($arResult['transport'] as $arItem)
						{
							?><li data-id="<?=$arItem['UF_XML_ID']?>"><?=$arItem['UF_NAME']?></li><?
						}
					}
					else
					{
						/*?><li data-id="0"><?=$EmptyDictionary?></li><?*/
					}?>
				</ul>
			</div>
		</div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("TTD")?>
			</div>
			<input type="text" name="ttd" value="<?=$arResult['TTD'];?>">
		</div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("RECIPIENTNAME")?>
			</div>
			<input type="text" id="recipientname_input" name="recipientname" value="<?=$arResult['RECIPIENTNAME'];?>" autocomplete="off">
			<div class="recipientname_list_wrap">
				<ul id="recipientname_list">
					<?if (!empty($arResult['recipientname']))
					{?>
						<?foreach($arResult['recipientname'] as $arItem)
						{
							?><li data-id="<?=$arItem['UF_XML_ID']?>"><?=$arItem['UF_NAME']?></li><?
						}
					}
					else
					{
						/*?><li data-id="0"><?=$EmptyDictionary?></li><?*/
					}?>
				</ul>
			</div>
		</div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("LABNAME")?>
			</div>
			<input type="text" name="labname" value="<?=$arResult['LABNAME'];?>">
		</div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("PRODUCTNAME")?>
			</div>
			<input type="text" name="productname" value="<?=$arResult['PRODUCTNAME'];?>">
		</div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("PRODUCTIONADRESS")?>
			</div>
			<input type="text" id="productionadress_input" name="productionadress" value="<?=$arResult['PRODUCTIONADRESS'];?>" autocomplete="off">
			<div class="productionadress_list_wrap">
				<ul id="productionadress_list">
					<?if (!empty($arResult['productionadress']))
					{?>
						<?foreach($arResult['productionadress'] as $arItem)
						{
							?><li data-id="<?=$arItem['UF_XML_ID']?>"><?=$arItem['UF_NAME']?></li><?
						}
					}
					else
					{
						/*?><li data-id="0"><?=$EmptyDictionary?></li><?*/
					}?>
				</ul>
			</div>
		</div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("SPECIALSNOTES")?>
			</div>
			<input type="text" id="specialsnotes_input" name="specialsnotes" value="<?=$arResult['SPECIALSNOTES'];?>" autocomplete="off">
			<div class="specialsnotes_list_wrap">
				<ul id="specialsnotes_list">
					<?if (!empty($arResult['specialsnotes']))
					{?>
						<?foreach($arResult['specialsnotes'] as $arItem)
						{
							?><li data-id="<?=$arItem['UF_XML_ID']?>"><?=$arItem['UF_NAME']?></li><?
						}
					}
					else
					{
						/*?><li data-id="0"><?=$EmptyDictionary?></li><?*/
					}?>
				</ul>
			</div>
		</div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("OFFICIALNAME")?>
			</div>
			<input type="text" id="officialname_input" name="officialname" value="<?=$arResult['OFFICIALNAME'];?>" autocomplete="off">
			<div class="officialname_list_wrap">
				<ul id="officialname_list">
					<?if (!empty($arResult['officialname']))
					{?>
						<?foreach($arResult['officialname'] as $arItem)
						{
							?><li data-id="<?=$arItem['UF_XML_ID']?>" value="<?=$arItem['UF_JOB_NAME'];?>"><?=$arItem['UF_NAME']?></li><?
						}
					}
					else
					{
						/*?><li data-id="0"><?=$EmptyDictionary?></li><?*/
					}?>
				</ul>
			</div>
		</div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("JOBNAME")?></br> (осторожно! поле заполняется при выборе ФИО должностного лица)
			</div>
			<input type="text" id="jobname_input" name="jobname" value="<?=$arResult['JOBNAME'];?>">
		</div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("PLACELOAD")?>
			</div>
			<input type="text" id="placeload_input" name="placeload" value="<?=$arResult['PLACELOAD'];?>" autocomplete="off">
			<div class="placeload_list_wrap">
				<ul id="placeload_list">
					<?if (!empty($arResult['placeload']))
					{?>
						<?foreach($arResult['placeload'] as $arItem)
						{
							?><li data-id="<?=$arItem['UF_XML_ID']?>"><?=$arItem['UF_NAME']?></li><?
						}
					}
					else
					{
						/*?><li data-id="0"><?=$EmptyDictionary?></li><?*/
					}?>
				</ul>
			</div>
		</div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("UNLOADPOINT")?>
			</div>
			<input type="text" id="unloadpoint_input" name="unloadpoint" value="<?=$arResult['UNLOADPOINT'];?>" autocomplete="off">
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
						/*?><li data-id="0"><?=$EmptyDictionary?></li><?*/
					}?>
				</ul>
			</div>
		</div>
		<div class="mf-name">
			<div class="mf-text">
				<?=GetMessage("ENTITIES")?>
			</div>
			<input type="text" id="entities_input" name="entities" value="<?=$arResult['ENTITIES'];?>" autocomplete="off">
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
						/*?><li data-id="0"><?=$EmptyDictionary?></li><?*/
					}?>
				</ul>
			</div>
		</div>
			<?/*<div class="mf-name">
				<div class="mf-text">
					<?=GetMessage("AUTHOR")?> (*)
				</div>
				<input type="text" name="author" value="<?=$arResult["AUTHOR"]?>">
			</div>*/?>
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
				<input type="text" id="country2_input" name="country2" value="<?=$arResult["COUNTRY2"]?>" autocomplete="off">
				<div class="country2_list_wrap">
					<ul id="country2_list">
						<?foreach($arResult['COUNTRIES'] as $arItem2){
							?><li data-id="<?=$arItem2['UF_NAME']?>"><?=$arItem2['UF_NAME']?></li><?
						}?>
					</ul>
				</div>
			</div>
			<?/*<div class="mf-name">
				<div class="mf-text">
					<?=GetMessage("DEL")?> (*)
				</div>
				<input type="text" name="del" value="<?=$arResult["DEL"]?>">
			</div>
			<div class="mf-name">
				<div class="mf-text">
					<?=GetMessage("DEL_P")?> (*)
				</div>
				<input type="text" name="del_p" value="<?=$arResult["DEL_P"]?>">
			</div>
			<div class="mf-name">
				<div class="mf-text">
					<?=GetMessage("AUTHOR_ANNUL")?> (*)
				</div>
				<input type="text" name="author_annul" value="<?=$arResult["AUTHOR_ANNUL"]?>">
			</div>*/?>
		<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
		<input type="submit" name="submit" value="<?=GetMessage("MF_MFT_SUBMIT")?>">
		<input type="button" style="float:right;" name="to_main" onclick="location.href = 'http://<?=$_SERVER['SERVER_NAME']?>'" value="<?=GetMessage("MF_TO_MAIN");?>">
	</form>
</div>
</br>
