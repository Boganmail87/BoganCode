<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>
<?
global $APPLICATION;
$dir = $APPLICATION->GetCurDir();
if (strlen($arResult["RETURN"]['FIO']<=0))
{
	$arResult["RETURN"]['FIO']=$arResult['madnessregions']["UF_NAME"];
}
if (strlen($arResult["RETURN"]['OFFICE']<=0))
{
	$arResult["RETURN"]['OFFICE']=$arResult['madnessregions']["UF_OFFICE"];
}
if (strlen($arResult["RETURN"]['SETTLEMENTS']<=0))
{
	$arResult["RETURN"]['SETTLEMENTS']=$arResult['madnessregions']["UF_REGION_COUNT"];
}
if (strlen($arResult["RETURN"]['MONTHS']<=0))
{
	$arResult["RETURN"]['MONTHS']=date('d.m.Y');
}
?>
<script> $(document).ready(function()
{ 
	
	$('.RESET').click(function()
		{
			document.getElementById("myForm").reset();
		});
	$('.container').mouseover(function()
		{
			var myTitle = $(this).attr('title');
			$("#hover").text(myTitle);
			$(this).text('*');
		});
}); 
</script>
<STYLE>
	#container_table 
	{
		width: 500px;
		border: 2px solid #E8C48F; /* рамка 2px */
		padding: 2px;              /* поля 2px */
		overflow-x: scroll; /* прокрутка по вертикали */
	}
	.container
	{
			overflow:auto;  width: 30px;
	}
	.tableclass{
	font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
	font-size: 14px;
	border-radius: 10px;
	border-spacing: 0;
	text-align: center;
	}
	.classth {
	background: #BCEBDD;
	color: white;
	text-shadow: 0 1px 1px #2D2020;
	padding: 1px 2px;
	}
	.classth, .classtd {
	border-style: solid;
	border-width: 0 1px 1px 0;
	border-color: white;
	}
	.classth:first-child, .classtd:first-child {
	text-align: center;
	}
	.classth:first-child {
	border-top-left-radius: 10px;
	}
	.classth:last-child {
	border-top-right-radius: 10px;
	border-right: none;
	}
	.classtd {
	padding: 1px 2px;
	background: #F8E391;
	}
	.classtr td:first-child {
		
	text-align: left;
	}
	.classtr:last-child td:first-child {
	border-radius: 0 0 0 10px;
	}
	.classtr:last-child td:last-child {
	border-radius: 0 0 10px 0;
	}
	.classtr td:last-child {
	border-right: none;
	}
</STYLE>

<div class="vet_sert_2">
		<div class="info">
			<center>
				<a href="<?=$dir?>?form_madness_period=lastmonth">за месяц </a>
				<a href="<?=$dir?>?form_madness_period=lastqarter">за квартал </a>
				<a href="<?=$dir?>?form_madness_period=lastyear">за год </a>
			</center>
		</div>

<div id="container_table">
	<table class="tableclass">
		<thead class="classtbody">
		  <tr class="classtr"> 
		    <th class="classth" rowspan="3">Наименование района</th>
		    <th class="classth" rowspan="3">Количество населенных пунктов</th>
		    <th class="classth" colspan="3">Имеется животных</th>
		    <th class="classth" colspan="3">Провакцинировано животных</th>
		    <th class="classth" colspan="4">Приняты меры</th>
		    <th class="classth" rowspan="3">Выделено денежных средств из местного бюджета, руб.</th>
		    <th class="classth" colspan="3">Опубликовано информации в СМИ</th>
		    <th class="classth" colspan="2">Проведено</th>
		    <th class="classth" colspan="4">Госветинспекторсая работа</th>
		  </tr>
		  <tr class="classtr">
		    <td class="classth" class="classth" rowspan="2">Всего</td>
		    <td class="classth" colspan="2">в том числе</td>
		    <td class="classth" rowspan="2">Всего</td>
		    <td class="classth" colspan="2">в том числе</td>
		    <td class="classth" colspan="2">по отлову</td>
		    <td class="classth" colspan="2">по отстрелу</td>
		    <td class="classth" rowspan="2">По радио/ теле-видение</td>
		    <td class="classth" rowspan="2">листо-вок,памяток</td>
		    <td class="classth" rowspan="2">В печати</td>
		    <td class="classth" rowspan="2">Совместных рейдов</td>
		    <td class="classth" rowspan="2">бесед</td>
		    <td class="classth" rowspan="2">составлено предписаний</td>
		    <td class="classth" rowspan="2">2составлено протоколов</td>
		    <td class="classth" rowspan="2">оштрафовано лиц</td>
		    <td class="classth" rowspan="2">на сумму  руб.</td>
		  </tr>
		  <tr class="classtr">
		    <td class="classth">Кошек</td>
		    <td class="classth">Собак</td>
		    <td class="classth">Кошек</td>
		    <td class="classth">Собак</td>
		    <td class="classth">Кошек</td>
		    <td class="classth">Собак</td>
		    <td class="classth">Кошек</td>
		    <td class="classth">Собак</td>
		  </tr>
		</thead>
		<tbody class="classtbody">
			<?foreach ($arResult["ITEMS"] as $key => $value)
				{?>
					<tr class="classtr">
							<?foreach ($value as $key2 => $value2) 
							{
								switch ($key2) 
								{
									case 'VACCINATED_CATS':
								?><td class="classtd"><?=$value2["TOTAL7677"];?></td><?
								?><td class="classtd"><?=$value2["VALUE"];?></td><?
										break;
									case 'THEREIS_CATS':
								?><td class="classtd"><?=$value2["TOTAL7475"];?></td><?
								?><td class="classtd"><?=$value2["VALUE"];?></td><?
										# code...
										break;
									
									default:
								?><td class="classtd"><?=$value2["VALUE"];?></td><?
										break;
								}
							}?>
					</tr>
				<?}?>



					<tr class="classtr">
						<td class="classtd">ВСЕГО: </td>
							<?foreach ($arResult["TOTAL"] as $key2 => $value2) 
							{
								?><td class="classtd"><?=$value2;?></td><?
							}?>
					</tr>



		</tbody>
	</table>
</div>
</br>
<?if(!empty($arResult["ERROR_MESSAGE"]))
{
	foreach($arResult["ERROR_MESSAGE"] as $v)
		ShowError($v);
}?>
<div class="mfeedback" id="madness_anchor">
	<form action="<?=POST_FORM_ACTION_URI?>" method="POST" id="myForm">
		<?=bitrix_sessid_post();?>
		<script type="text/javascript">
			$(function() {
			    $.datepicker.setDefaults($.datepicker.regional['ru']);
			    $('#FOR_MONTHS_INPUT').datepicker({
				    showOn: "focus",
				    maxDate: "+0M +0D",
				    showAnim: "blind",
				    changeMonth: true,
					changeYear: true,
				});
						
			});
		</script>
		<table class="tableclass">
			<thead>
				<tr class="classtr">
					<th class="classth">Название поля</th>
					<th class="classth">значение</th>
				</tr>
			</thead>
			<?foreach ($arResult["ITEMS"][0] as $key => $value) 
			{
				if ($key=="COMPANY"){continue;}
				?><tr class="classtr">
						<td class="classtd"><?=GetMessage($key)?></td>
						<td class="classtd">
							<input type="text" name="<?=$key?>" value="<?=$arResult["RETURN"][$key]?>" size="5">
						</td>
				</tr>
			<?}?>
		</table>
	<div class="mf-name">
		<div class="mf-text">
			<?=" дата заполнения: "?>
		</div>
		<input type="text" id="FOR_MONTHS_INPUT" name="MONTHS" value="<?=$arResult["RETURN"]['MONTHS'];?>" autocomplete="off">
	</div>
	<div class="mf-name">
		<div class="mf-text">
			<?=GetMessage("FIO_INPUT")?>
		</div>
		<input type="text" id="fio_input" name="FIO" value="<?=$arResult["RETURN"]['FIO'];?>" autocomplete="off">
	</div>
	<div class="mf-name">
		<div class="mf-text">
			<?=GetMessage("OFFICE_INPUT")?>
		</div>
		<input type="text" id="office_input" name="OFFICE" value="<?=$arResult['RETURN']['OFFICE'];?>" autocomplete="off">
	</div>
	<div class="mf-name">
		<div class="mf-text">
			<?=GetMessage("ORGANIZATIONS_INPUT")?>
		</div>
		<input type="text" id="organizations_input" name="ORGANIZATIONS" value="<?=$arResult['RETURN']['ORGANIZATIONS'];?>" autocomplete="off">
		<div class="organizations_list_wrap">
			<ul id="organizations_list">
				<?if (!empty($arResult['madnessregions']))
				{
					foreach ($arResult['madnessregions']['UF_ORGANIZATIONS'] as $key => $value) 
					{
					?><li data-id=""><?=$value["VALUE"];?></li><?
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
			<?=GetMessage("OFFICE_INPUT")?>
		</div>
		<input type="text" id="region_input" name="REGION" value="<?=$arResult["madnessregions"]['UF_USER']['VALUE'];?>" autocomplete="off">
	</div>
		<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
		<input type="submit" name="submit" value="<?=GetMessage("MF_MFT_SUBMIT")?>">
		<input type="button" value="Обнулить" class="RESET">
		<input type="button" style="float:right;" name="to_main" onclick="location.href = 'http://<?=$_SERVER['SERVER_NAME']?>'" value="<?=GetMessage("MF_TO_MAIN");?>">
	</form>
</div>
