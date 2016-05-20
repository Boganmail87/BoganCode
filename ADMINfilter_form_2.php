<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");?>
<?$APPLICATION->SetTitle("Отчет по Ветеринарным сертификатам по форме № 2");?>
<?//---------------------------CSS-------------------------------------------------?>
<style type="text/css">
	.selector
	{
		width:500px;
	}
	.container
	{
		overflow:auto; 
		width:auto;
		height:60px;
	}
	tfoot 
	{
		align:center;
		background:#fc0;
	}
	.tableclass{
	width:600; 
	border:1px; 
	cellspacing:0px;
	font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
	font-size: 14px;
	border-spacing: 0;
	text-align: center;
	}
	.classth {
	background: #BCEBDD;
	color: darkslategray;
	padding: 1px 2px;
	font-weight: bold;
	}
	.classth, .classtd {
	border-style: solid;
	border-width: 0 1px 1px 0;
	border-color: white;
	}
	.classth:first-child, .classtd:first-child {
	text-align: center;
	}
	.classth:last-child {
	border-right: none;
	}
	.classtd {
	padding: 1px 2px;
	background: #F8E391;
	}
	.classtr td:last-child {
	border-right: none;
	}
</style>
<?//---------------------------JS-------------------------------------------------?>
<SCRIPT LANGUAGE="JavaScript">
	function printtext(text)
	{
		var tableToPrint=document.getElementById("table_4_print");
		newWin= window.open("");
		newWin.document.write('<style type="text/css">@media print {.selector{display: none !important;}}</style>');
		newWin.document.write(tableToPrint.outerHTML);
		newWin.print();
		newWin.close();
	}
</SCRIPT>
<?//---------------------------PHP FUNCTIONS-------------------------------------------------?>
<?
function decodeDate($b)// преобразование строки в масив
{	
	preg_match("/\d{2}[.]\d{2}[.]\d{4}/", $b, $output_array);
	preg_match("/\d{2}[,]\d{2}[,]\d{4}/", $b, $output_array2);
	foreach($output_array as $key =>$value)
	$arResultDateMas[]=$value;
	foreach($output_array2 as $key  =>$value)
	$arResultDateMas[]=$value;
	return $arResultDateMas;
}
function FormatValues($a)//форматирование значений к читабельному виду
{$t='';
	if(is_array($a))
	{
		foreach ($a as $key => $value) {
			if ($value)
			{
			$t.=$value.", ";
			}
		}
		return $t;
	}
	else
	{
	  switch ($a) 
	  {
	  	case 'Y': return 'Разрешено';
	  		break;
	  	case 'N': return 'Запрещено';
	  		break;
	  	case '': return $strEmptyFileld;
	  		break;
	  	case 'WITHOUT_LIMITS': return 'Реализация без ограничений';
	  		break;
	  	case 'WITH_LIMITS': return 'Реализация с ограничениями';
	  		break;
	  	case 'WITH_RULES': return 'Переработка согласно правилам ветсанэкспертизы';
	  		break;
	  	default: return $a;
	  }
	}
}
function Getfloat($str) //преобразование в дробное число
{ 
  if(strstr($str, ",")) 
  { 
    $str = str_replace(".", "", $str); // replace dots (thousand seps) with blancs 
    $str = str_replace(",", ".", $str); // replace ',' with '.' 
  } 
  if(preg_match("#([0-9\.]+)#", $str, $match)) 
  { // search for number that may contain '.' 
    return floatval($match[0]); 
  } else 
  	{ 
    	return floatval($str); // take some last chances with floatval 
  	} 
} 
?>
<?//---------------------------init values of variables-------------------------------------------------?>
<?$arParamForRequest="";
$param="";
$arCancel=false;
$cntNumOrder=1;
$strEmptyFileld='--//--';
$arResult["COL_COUNT"]=0;
$arResult["ROW_COUNT"]=0;
$IBLOCKFORM2ID=5;
//end init?>
<?//---------------------------Формирование условий фильтра/отмена фильтра-------------------------------------------------
foreach($_GET as $param => $value)
{
	if (($param=="CANCEL")&&($value=="YES"))
	{//отмена фильтра = делаем запрос к всем полям формы
		$arResult["FILTER"]=array();
		$arCancel=true;
		$arResult["QUANTITY_SUMM"]=0;
		$arResult["WEIGHT_SUMM"]=0;
		$arResult["COL_COUNT"]=0;
		$arResult["ROW_COUNT"]=0;
		$arUnique=array();
		unset($_GET);
		break;
  }
  else
  {//формирование еще одного параметра фильтра
		if(strlen($value)>0)
		{
	 		if ($param=="PRODUCTION_DATE"||$param=="DATE_ISSUE_CERT")
	 		{
	 			 $value=ConvertDateTime($value, "YYYY-MM-DD");
					
	 		}
			$arParamForRequest="PROPERTY_".$param;
 			$arResult["FILTER"][$arParamForRequest]=$value;
 		}
  }
}//foreach
if(CModule::IncludeModule("iblock"))
{
	$arSelect =array("NAME", "DATE_ACTIVE_FROM","ACTIVE", "ID","IBLOCK_ID");
	$arFilter = Array('IBLOCK_ID'=>$IBLOCKFORM2ID, true, true, $arResult["FILTER"]);
	$res = CIBlockElement::GetList(Array(), $arFilter,false,false, $arSelect);
	while($ob = $res->GetNextElement())
	{ 
		$arFields = $ob->GetFields();  
		$arPropsFirst = $ob->GetProperties();
		$arResult["ITEMS"][]=$arPropsFirst;
		foreach ($arPropsFirst as $key => $value) 
		{
			if($key=="PRODUCTION_DATE")
			{
				foreach ($value["VALUE"]as $key2 => $value2) {
					if(!in_array($value2, $arUnique[$key])):
						$arUnique[$key][]=$value2;
					endif;
				}
			}
			else
			if(!in_array($value["VALUE"], $arUnique[$key])):
				$arUnique[$key][]=$value["VALUE"];
			endif;
		}//foreach
	}//while
	$arResult["UNIQUE"]=$arUnique;
  foreach ($arResult["ITEMS"] as $key => $value) //цикл для подщёта веса штук и т.д.
  {
  	$arResult["COL_COUNT"]++;
  	$arResult["ROW_COUNT"]=0;
		foreach ($value as $key1 => $value1):
			$arResult["ROW_COUNT"]++;
			/*if($key1=="QUANTITY") {$arResult["QUANTITY_SUMM"]+=Getfloat($value1["VALUE"]);}
			if($key1=="WEIGHT") {$arResult["WEIGHT_SUMM"]+=Getfloat($value1["VALUE"]);}*/
		endforeach;
	}
} //if(CModule::IncludeModule("iblock"))?>

<?//готовим файл к експорту
$arCSV=array();
//голова
$cntNumOrder=1;
foreach ($arResult["ITEMS"][0] as $key => $value) 
{
	$arCSV["HEAD"][]=iconv('utf-8', 'windows-1251', $value["NAME"]);
}
//тело
foreach ($arResult["ITEMS"] as $PropKey1 => $value) 
{
	foreach ($value as $key1 => $value1):
		// если надо выводить номер по порядку как в инфоблоке то условие CASE'COUNTER' удалить
			switch ($key1) 
			{
				case 'COUNTER':
						$arCSV["BODY"][$PropKey1][]=iconv('utf-8', 'windows-1251',$cntNumOrder);
						$cntNumOrder++;
					break;
				default:
	 					$arCSV["BODY"][$PropKey1][]=iconv('utf-8', 'windows-1251', FormatValues($value1["VALUE"]));
					break;
			}
	endforeach; 
}
//футер
	$PropKey1++;
	foreach ($arResult["ITEMS"][0] as $key1 => $value1): 
		switch ($key1) 
		{
			case 'NAME':
				$arCSV["BODY"][$PropKey1][$key1]=iconv('utf-8', 'windows-1251', "ИТОГО:");
				break;
			case 'QUANTITY':
				$arCSV["BODY"][$PropKey1][$key1]=iconv('utf-8', 'windows-1251', FormatValues($arResult["QUANTITY_SUMM"]));
				break;
			case 'WEIGHT':
				$arCSV["BODY"][$PropKey1][$key1]=iconv('utf-8', 'windows-1251', FormatValues($arResult["WEIGHT_SUMM"]));
				break;
			default:
			$arCSV["BODY"][$PropKey1][$key1]=iconv('utf-8', 'windows-1251', $strEmptyFileld);
				break;
		}
	endforeach;
	//записываем файл
	$file = $_SERVER['DOCUMENT_ROOT']."/upload/form2.csv";
  unlink($file);
  $fp = fopen($file, 'w');
  fputcsv($fp, $arCSV["HEAD"], ';');
  foreach($arCSV["BODY"] as $arItem)
  {
  	fputcsv($fp, $arItem, ';');
  }
  fclose($fp);

?>

<?//Представление?>
<div>
<?if (!empty($arResult["ITEMS"])):?>
	<form method="get" id="filter_form_2_id">
		<button type="submit">Фильтровать</button>
		<button type="reset">Снять текущий фильтр</button>
		<button name="CANCEL" value="YES">Отменить фильтр</button>
		<br>
		<br>
		<table class="tableclass" id="table_4_print">
			<thead align="center" class="classtbody"><?//формируем шапку?>
				<tr class="classtr">
					<?foreach ($arResult["ITEMS"][0] as $key => $value) 
					{?>
						<th class="classth">
							<div class="head_container">
								<?echo $value["NAME"];?>
							</div>
							<div class="head_container_select">
								<select name="<?=$value["CODE"]?>" >
									<option value="">Ничего</option>
									<?foreach ($arUnique[$key] as $key1 => $value1) 
									{?>
										<?// если надо выводить номер по порядку как в инфоблоке то условие COUNTER удалить?>
										<?if($key=="COUNTER"){continue;}?>
										<?if($key=="PRODUCTION_DATE")
											{?>
												<option value="<?=FormatValues($value1);?>"><?=FormatValues($value1);?></option>
											<?continue;}?>
						        <option value="<?=$value1?>" <?if (($_GET[$key]==htmlspecialchars_decode($value1))&&($value1!='')) {echo 'selected="selected"';}?>><?=FormatValues($value1);?></option>
									<?}//foreach?>
					      </select>
							</div>
						</th>
					<?}//foreach?>
				</tr>
			</thead>
	</form>
			<tbody align="center" >
			<?$cntNumOrder=1;?>
			  <?foreach ($arResult["ITEMS"] as $PropKey1 => $value) {?>
				  <tr class="classtr">
						 <?foreach ($value as $key1 => $value1): ?>
						 	<?// если надо выводить номер по порядку как в инфоблоке то условие CASE'COUNTER' удалить
								switch ($key1) 
								{
									case 'COUNTER':?>
											<td class="classtd" ><?= $cntNumOrder++;?></td>
										<?break;
									default:?>
						 					<td class="classtd" ><div class ="container"><?=FormatValues($value1["VALUE"]);?></div></td>
										<?break;
								}
						 	endforeach ?>
					</tr>
				<?}?>
					 <tr class="classtr" >
							<td class="classtd" " colspan="<?=$arResult["ROW_COUNT"];?>"><STRONG>ИТОГО:</STRONG></td>
					</tr>
			</tbody>
			<tfoot align="center">
						 <?php 
						 foreach ($arResult["ITEMS"][0] as $key1 => $value1): ?>
							<?switch ($key1) {
								/*case 'NAME':?>
									<td><?=$strEmptyFileld?></td>
									<?break;
								case 'QUANTITY':?>
									<td><?=FormatValues($arResult["QUANTITY_SUMM"]);?></td>
									<?break;
								case 'WEIGHT':?>
									<td><?=FormatValues($arResult["WEIGHT_SUMM"]);?></td>
									<?break;*/
								default:?>
								<td class="classtd"><?=$strEmptyFileld?></td>
									<?break;
							}?>
							<?php endforeach ?>
			</tfoot>
		</table>
	</br>
	<INPUT TYPE="button" VALUE=" Печать результата" ONCLICK="printtext()">
	<INPUT TYPE="button" VALUE=" Експорт в CSV" onClick="window.location.href='/upload/form2.csv'">
<?else:?>
	<form method="get">
				<H1>Ничего не найдено!</H1>
				<button name="CANCEL" value="YES">Снять активный фильтр</button>
	</form>
<?endif;?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>