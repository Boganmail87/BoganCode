<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");?>
<?
$APPLICATION->SetTitle("Отчет по Ветеринарным сертификатам по форме № 4");?>
<?//style block?>
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
<?//end style block?>
<?
//init values of variables
$arParamForRequest="";
$param="";
$arCancel=false;
$cntNumOrder=1;
$strEmptyFileld='--//--';
//end init
function FormatValues($a)//форматирование значений к читабельному виду
{
  switch ($a) 
  {
  	case 'Y': return 'Разрешено';
  		break;
  	case 'N': return 'Запрещено';
  		break;
  	case '': return $strEmptyFileld;
  		break;
  	default: return $a;
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
foreach($_GET as $param => $value){
		if (($param=="CANCEL")&&($value=="YES"))://отмена фильтра = делаем запрос к всем полям формы
		unset($arResult["GET_FOR_REQUEST"]);
		unset($arResult["GET"]);
		$arCancel=true;
		$arResult["QUANTITY_SUMM"]=0;
		$arResult["WEIGHT_SUMM"]=0;
		$arResult["COL_COUNT"]=0;
		$arResult["ROW_COUNT"]=0;
		$arUnique=array();
		break;
   elseif ($value && $arCancel==false)://нету отмены фильтра
			switch ($param) {
				case 'NAME':
				case 'DATE_ACTIVE_FROM':
				case 'ACTIVE':
				$arParamForRequest=$param;
				break;
				default:
			$arParamForRequest="PROPERTY_".$param;
					break;
			}//switch
   $arResult["GET_FOR_REQUEST"][$arParamForRequest]=$value;
   $arResult["GET"][$param]=$value;
   endif;
	}//foreach
if(CModule::IncludeModule("iblock")){
	$arSelect =array(/*"PROPERTY_COUNTER", "PROPERTY_NAME", "PROPERTY_DATE_ACTIVE_FROM", "PROPERTY_ACTIVE", "PROPERTY_NUMBER", "PROPERTY_TTN", "PROPERTY_QUALITY", "PROPERTY_CODE", "PROPERTY_TYPE", "PROPERTY_QUANTITY", "PROPERTY_WEIGHT", "PROPERTY_POINT", "PROPERTY_ADRESS", "PROPERTY_COUNTRY", "PROPERTY_COUNTRY2", "PROPERTY_AUTHOR", "PROPERTY_DEL", "PROPERTY_DEL_P", "PROPERTY_DEL_A", */"NAME", "DATE_ACTIVE_FROM","ACTIVE", "ID","IBLOCK_ID");
	$arFilter = Array('IBLOCK_ID'=>3, "ACTIVE_DATE"=>"Y", $arResult["GET_FOR_REQUEST"]["ACTIVE"], $arResult["GET_FOR_REQUEST"]);
	$res = CIBlockElement::GetList(Array(), $arFilter,false,false, $arSelect);?>
	<?while($ob = $res->GetNextElement()){ 
		$arFields = $ob->GetFields();  
		$arResult["ITEMS"][]=$arFields;
		$arPropsFirst = $ob->GetProperties();
		unset($arPropsFirst["LAST_ID"]);
		$arPropsSecond["COUNTER"]=$arPropsFirst["COUNTER"];
    $arPropsSecond["NAME"]=array("CODE"=>"NAME","NAME"=>"Название организации","VALUE"=>$arFields["NAME"]);
    $arPropsSecond["DATE_ACTIVE_FROM"]=array("CODE"=>"DATE_ACTIVE_FROM","NAME"=>"Дата выдачи документа","VALUE"=>$arFields["DATE_ACTIVE_FROM"]);
   	$arPropsSecond["ACTIVE"]=array("CODE"=>"ACTIVE","NAME"=>"Разрешено/Запрещено","VALUE"=>$arFields["ACTIVE"]);
   	$arProps=$arPropsSecond+$arPropsFirst;
		$arResult["PROPS"][]=$arProps;
		foreach ($arProps as $key => $value) {
				if(!in_array($value["VALUE"], $arUnique[$key])):
					$arUnique[$key][]=$value["VALUE"];
				endif;
		}//foreach
	}//while
	$arResult["UNIQUE"]=$arUnique;
  foreach ($arResult["PROPS"] as $key => $value) 
  {
  	$arResult["COL_COUNT"]++;
		foreach ($value as $key1 => $value1):
			$arResult["ROW_COUNT"]++;
			if($key1=="QUANTITY") {$arResult["QUANTITY_SUMM"]+=Getfloat($value1["VALUE"]);}
			if($key1=="WEIGHT") {$arResult["WEIGHT_SUMM"]+=Getfloat($value1["VALUE"]);}
		endforeach;
	}

} //if(CModule::IncludeModule("iblock"))?>
<?//Представлення?>
<?$arCSV=array();?>
<?if (!empty($arResult["ITEMS"])):?>
	<form method="get" id="filter_form_4_id">
		<button type="submit">Фильтровать</button>
		<button type="reset">Снять текущий фильтр</button>
		<button name="CANCEL" value="YES">Отменить фильтр</button>
		</br>
		</br>
		<table id="table_4_print" class="tableclass">
			<thead align="center" class="classtbody">
				<tr class="classtr">
					<?foreach ($arResult["PROPS"][0] as $key => $value) {?>
						<td class="classth">
						<?$arCSV["HEAD"][]=iconv('utf-8', 'windows-1251', $value["NAME"]);?>
						<?=$value["NAME"];?>
						<select name="<?=$value["CODE"]?>" <? if($value["CODE"]=="CODE"){echo 'class="selector"';}?>>
							<option value="">Ничего</option>
							<?foreach ($arUnique[$key] as $key2 => $value1) {?>
							<?// если надо выводить номер по порядку как в инфоблоке то условие COUNTER удалить?>
							<?if($key=="COUNTER"){continue;}?>
			        <option value="<?=$value1?>" <?if ($_GET[$key]==htmlspecialchars_decode($value1)) {echo 'selected="selected"';}?>>
								<?=FormatValues($value1);?></option>
							<?}//foreach?>
			      </select>
						</td>
					<?}//foreach?>
				</tr>
			</thead>
			<tbody align="center" class="classtbody">
			  <?foreach ($arResult["PROPS"] as $PropKey1 => $value) 
			  {?>
				  <tr class="classtr">
						 <?foreach ($value as $key1 => $value1): 
						 	// если надо выводить номер по порядку как в инфоблоке то условие CASE'COUNTER' удалить
								switch ($key1) 
								{
									case 'COUNTER':?>
											<?$arCSV["BODY"][$PropKey1][]=iconv('utf-8', 'windows-1251',$cntNumOrder);?>
											<td  class="classtd"><?= $cntNumOrder++;?></td>
										<?break;
									default:?>
						 					<?$arCSV["BODY"][$PropKey1][]=iconv('utf-8', 'windows-1251', FormatValues(htmlspecialchars_decode($value1["VALUE"])));?>
						 					<td  class="classtd"><div class="container"><?=FormatValues($value1["VALUE"]);?></div></td>
										<?break;
								}
						 	endforeach ?>
					</tr>
				<?}?>
					 <tr class="classtr">
							<td  class="classtd" align="left" colspan="<?=$arResult["ROW_COUNT"];?>"><STRONG>ИТОГО:</STRONG></td>
					</tr>
			</tbody>
			<tfoot class="classtbody">
						 <?php 
						 $PropKey1++;
						 foreach ($arResult["PROPS"][0] as $key1 => $value1): ?>
							<?switch ($key1) 
							{
								case 'NAME':?>
									<?$arCSV["BODY"][$PropKey1][$key1]=iconv('utf-8', 'windows-1251', "ИТОГО:");?>
									<td  class="classtd"><?=$strEmptyFileld?></td>
									<?break;
								case 'QUANTITY':?>
									<?$arCSV["BODY"][$PropKey1][$key1]=iconv('utf-8', 'windows-1251', FormatValues($arResult["QUANTITY_SUMM"]));?>
									<td  class="classtd"><?=FormatValues($arResult["QUANTITY_SUMM"]);?></td>
									<?break;
								case 'WEIGHT':?>
									<?$arCSV["BODY"][$PropKey1][$key1]=iconv('utf-8', 'windows-1251', FormatValues($arResult["WEIGHT_SUMM"]));?>
									<td class="classtd"><?=FormatValues($arResult["WEIGHT_SUMM"]);?></td>
									<?break;
								default:?>
								<?$arCSV["BODY"][$PropKey1][$key1]=iconv('utf-8', 'windows-1251', $strEmptyFileld);?>
								<td class="classtd"><?=$strEmptyFileld?></td>
									<?break;
							}?>
							<?php endforeach ?>
			</tfoot>
		</table>
	</form>
  <?$file = $_SERVER['DOCUMENT_ROOT']."/upload/form4.csv";
  unlink($file);
  $fp = fopen($file, 'w');
  fputcsv($fp, $arCSV["HEAD"], ';');
  foreach($arCSV["BODY"] as $arItem){
  fputcsv($fp, $arItem, ';');
  }
  fclose($fp);?>
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
	</br>
	<INPUT TYPE="button" VALUE=" Печать результата" ONCLICK="printtext()">
	<INPUT TYPE="button" VALUE=" Експорт в CSV" onClick="window.location.href='/upload/form4.csv'">
<?else:?>
	<form method="get">
				<H1>Ничего не найдено!</H1>
				<button name="CANCEL" value="YES">Снять активный фильтр</button>
	</form>
<?endif;?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
