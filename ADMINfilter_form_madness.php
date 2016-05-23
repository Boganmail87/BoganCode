<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<?$APPLICATION->SetTitle("Отчет ");?>
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
$(function()
{
$('#lmh').attr("href",
	"<?=$dir?>filter_form_madness.php?form_madness_period=lastmonth&region_name="
	+$('#company_select').val());
$('#lkh').attr("href",
	"<?=$dir?>filter_form_madness.php?form_madness_period=lastqarter&region_name="
	+$('#company_select').val());
$('#lyh').attr("href",
	"<?=$dir?>filter_form_madness.php?form_madness_period=lastyear&region_name="
	+$('#company_select').val());
$('#company_select').change(
	function ()
	{
		$('#lmh').attr("href","<?=$dir?>filter_form_madness.php?form_madness_period=lastmonth&region_name="+$(this).val());
		$('#lkh').attr("href","<?=$dir?>filter_form_madness.php?form_madness_period=lastqarter&region_name="+$(this).val());
		$('#lyh').attr("href","<?=$dir?>filter_form_madness.php?form_madness_period=lastyear&region_name="+$(this).val());
	}
);
function printtext(text)
	{
		var tableToPrint=document.getElementById("table_4_print");
		newWin= window.open("");
		newWin.document.write('<style type="text/css">@media print {.selector{display: none !important;}}</style>');
		newWin.document.write(tableToPrint.outerHTML);
		newWin.print();
		newWin.close();
	}
});
</SCRIPT>
<?//---------------------------PHP FUNCTIONS-------------------------------------------------?>
<?
function	roundproperway($total)//проверка на три знака после точки
{ 
	preg_match("/[^0-9^.,]/", $total, $output_array);
	if(empty($output_array))
	{
		preg_match("/[,]/", $total, $output_array);
		if (!empty($output_array))
		{
			$total = str_replace(",", ".", $total);
		}
		preg_match("/[.]/", $total, $output_array);
		if (!empty($output_array))
		{
			preg_match("/[.]\d{4,}/", $total, $output_array);
			if(empty($output_array))
			{   
				preg_match("/[.]\d{3}/", $total, $output_array);
				if(!empty($output_array))
				{
					return($total);
				}
				else
				{
					preg_match("/[.]\d{2}/", $total, $output_array);
					if (!empty($output_array))
					{
						return $total.="0";
					}
					else
					{
						preg_match("/[.]\d{1}/", $total, $output_array);
						if (!empty($output_array))
						{
							return $total.="00";
						}
					}
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			if (intval($total))
			{
				return $total.=".000";
			}
			else
			{
				return false;
			}
		}
	}
	else
	{
		return false;
	}
}
function get_dates_of_quarter($quarter = 'current', $year = null, $format = null)
{
    if ( !is_int($year) ) {        
       $year = (new DateTime)->format('Y');
    }
    $current_quarter = ceil((new DateTime)->format('n') / 3);
    switch (  strtolower($quarter) ) {
    case 'this':
    case 'current':
       $quarter = ceil((new DateTime)->format('n') / 3);
       break;

    case 'previous':
       $year = (new DateTime)->format('Y');
       if ($current_quarter == 1) {
          $quarter = 4;
          $year--;
        } else {
          $quarter =  $current_quarter - 1;
        }
        break;

    case 'first':
        $quarter = 1;
        break;

    case 'last':
        $quarter = 4;
        break;

    default:
        $quarter = (!is_int($quarter) || $quarter < 1 || $quarter > 4) ? $current_quarter : $quarter;
        break;
    }
    if ( $quarter === 'this' ) {
        $quarter = ceil((new DateTime)->format('n') / 3);
    }
    $start = new DateTime($year.'-'.(3*$quarter-2).'-1 00:00:00');
    $end = new DateTime($year.'-'.(3*$quarter).'-'.($quarter == 1 || $quarter == 4 ? 31 : 30) .' 23:59:59');

    return array(
        'start' => $format ? $start->format($format) : $start,
        'end' => $format ? $end->format($format) : $end,
    );
}
?>
<?//---------------------------init values of variables--------------------------------------?>
<?
$MES["REGION_NAME"]="Наименование района";
$MES["REGION_COUNT"]="Количество населенных пунктов";
$MES["COMP_NAME"]="Наименование компании";
$MES["PETS_COUNT"]="Имеется животных";
$MES["VACZ_PRTS"]="Провакцинировано животных";
$MES["MEASURES"]="Приняты меры";
$MES["FOUNDATION"]="Выделено денежных средств из местного бюджета, руб.";
$MES["SMI"]="Опубликовано информации в СМИ";
$MES["PROVEDENO"]="Проведено";
$MES["GOS_RABOTA"]="Госветинспекторсая работа";
$MES["TOTAL"]="Всего";
$MES["V_TOM_CHISLE"]="в том числе";
$MES["PO_OTLOVU"]="по отлову";
$MES["PO_OTSTRELU"]="по отстрелу";
$MES["RADIO_TV"]="По радио/ теле-видение";
$MES["LISTOVOK"]="листо-вок,памяток";
$MES["IN_PRINT"]="В печати";
$MES["REJDOV"]="Совместных рейдов";
$MES["BESED"]="бесед";
$MES["PREDPISANIJ"]="составлено предписаний";
$MES["PROTOKOLOV"]="составлено протоколов";
$MES["OSHTRAF"]="оштрафовано лиц";
$MES["NA_SUMMU"]="на сумму  руб.";
$MES["CATS"]="Кошек";
$MES["DOGS"]="Собак";
$MES["LAST_M"]="за прошлый месяц ";
$MES["LAST_Q"]="за прошлый квартал ";
$MES["LAST_Y"]="за прошлый год ";
$MES["SEL_PERIOD"]="выбраный период";
$MES["FROM"]="от: ";
$MES["TO"]=" до:";
$MES["CHOOSE_REGION"]="Выберите район";
$MES["ALL_REGIONS"]="Все районы";
$MES["CANCEL_FILTRATION"]="отменить фильтрицию";
$MES["ZAGOLOVOK"]="о проводимых мероприятиях по профилактике и ликвидации бешенства животных (собак,кошек) в населённых пунктах";
$MES["INFO"]="Информация";
$monthesRUS = array(
          1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
          5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
          9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь');
global $USER;
$USER_ID=$USER->GetID();
$rsUser = CUser::GetList(($by="ID"), ($order="desc"), array("ID"=>$USER_ID),array("SELECT"=>array("UF_*")));
$arUser = $rsUser->Fetch();
$numREGION=$arUser["UF_USER_REGION"];
// номер инфоблока формы
$FORM_IBLOCK_ID = 7;
global $APPLICATION;
$dir = $APPLICATION->GetCurDir();
$arTOTAL=array();
//end init
?>
<?//подключаем модули
CModule::IncludeModule("iblock");
CModule::IncludeModule('highloadblock');?>
<?//задаем период выборки
if($_SERVER["REQUEST_METHOD"] == "GET")
{
	if(strlen("form_cancel")!="YES")
	{
		switch ($_GET["form_madness_period"]) 
		{
			case 'lastqarter': 
				$temp=get_dates_of_quarter('previous',null,'U');
				$begin=$temp["start"];
				$end=$temp["end"];
			break;
			case 'lastyear':
				$begin = strtotime('1/1 previous year');
				$end = strtotime('12/31 previous year');
			break;
			default:
				$begin = strtotime('first day of previous month');
				$end = strtotime('last day of previous month');
				break;
		}
		if($_GET["region_name"]=="select_all_regions"||$_GET["region_name"]=='')
		{
			$regionfilter='';
			$regiontriger=true;
		}
		else
		{
			$regionfilter=$_GET["region_name"];
			$regiontriger=false;
		}
	}
	else
	{
		$begin='';
		$end='';
		$_GET["region_name"]='';
	}
}?>
<?// загружаем  список всех районов
$rsData1 = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Madnessregions')));
if ($arData1 = $rsData1->fetch())
{
	$Entity1 = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData1);
	$Query1 = new \Bitrix\Main\Entity\Query($Entity1); 
	$Query1->setSelect(array('*'));
	// $Query1->setFilter(array("ID"=> intval($numREGION)));
	// $Query1->setOrder(array('UF_NAME' => 'ASC'));
	$result1 = $Query1->exec();
	$result1 = new CDBResult($result1);
	$arLang1 = array();
	while ($row1 = $result1->Fetch())
	{
		$UserField = CUserFieldEnum::GetList(array(), array("ID" => $row1["UF_ORGANIZATIONS"]));
		unset($row1["UF_ORGANIZATIONS"]);
		while($UserFieldAr = $UserField->Fetch())
		{
			$row1["UF_ORGANIZATIONS"][$UserFieldAr["ID"]]=$UserFieldAr;
		}
		$arResult['madnessregions'][$row1["UF_XML_ID"]] = $row1;
	}
}
?>
<?// загружаем ранее созданые отчеты
$arSelect =array("NAME", "ACTIVE", "ID","IBLOCK_ID","DATE_ACTIVE_FROM","CREATED_BY");
//задаем условия выборки
$arrFilter=Array
(
		"IBLOCK_ID"=>$FORM_IBLOCK_ID,
		"PROPERTY_REGION"=>$regionfilter,
		array
		(
		"LOGIC" => "AND",
        array( ">=DATE_ACTIVE_FROM"=>date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), $begin)),
        array( "<=DATE_ACTIVE_FROM"=>date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), $end)),
    )
);
//задаем условия групировки
$arGroupBy=false;
$res = CIBlockElement::GetList(Array("DATE_ACTIVE_FROM"=>'DESC'), $arrFilter,$arGroupBy,false, $arSelect);
while($ob = $res->GetNextElement())
{ 
	$arFields = $ob->GetFields();
	if(strlen($arFields["CREATED_BY"])>0&&!in_array($arFields["CREATED_BY"], $arResult["CREATED_BY"]))
	{
		$arResult["CREATED_BY"][]=$arFields["CREATED_BY"];
	}
	unset($arFields["CREATED_BY"]);
	unset($arFields["~CREATED_BY"]);
	$arPropsFirst = $ob->GetProperties();
	$arResult["ITEMS"][]=$arFields+$arPropsFirst;
}//while
//подгоняем значения под требуемый формат
foreach ($arResult["ITEMS"] as $key => $value) 
{ 
	// $arResult["ROWS_COUNT"]=count($arResult["ITEMS"]);
	foreach ($value as $key1 => $value1) 
	{	
		$tempmonth=explode('.', $value["DATE_ACTIVE_FROM"]);
		$region=$value["REGION"]["VALUE"].$tempmonth[1];
		switch ($key1) 
		{
			case 'NAME'://unset not neded values
			case '~NAME':
			case 'ACTIVE_FROM':
			case '~DATE_ACTIVE_FROM':
			case '~ACTIVE_FROM':
			case 'ACTIVE':
			case '~ACTIVE':
			case 'ID':
			case '~ID':
			case 'IBLOCK_ID':
			case '~IBLOCK_ID':
			case 'DATE_REPORT':
			case '~DATE_REPORT':
			case '~COMPANY':
			// unset($arResult["ITEMS"][$key][$key1]);
				break;
			case 'DATE_ACTIVE_FROM':
				if ($regiontriger)
				{
					$arTOTAL[$region][$key1]=$value1;
				}
				else
				{
					$arTOTAL[$key][$key1]=$value1;
				};
			break;
			case 'REGION':
				if ($regiontriger)
				{
					$arUniqueRegion[$value1["VALUE"]]=$arResult['madnessregions'][$value1["VALUE"]]["UF_NAME"];
					$arTOTAL[$region][$key1]=$arResult['madnessregions'][$value1["VALUE"]]["UF_NAME"];
				}
				else
				{
					$arTOTAL[$key][$key1]=$arResult['madnessregions'][$value1["VALUE"]]["UF_NAME"];
				};
			break;
			case 'THEREIS_DOGS'://local total count
			case 'THEREIS_CATS':
				if ($regiontriger)
				{
					$arTOTAL[$region]["THEREIS_CATS+THEREIS_DOGS"]=0;
					$arTOTAL[$region][$key1]=$arTOTAL[$region][$key1]+$value1["VALUE"];
					$arTOTAL[$region]["THEREIS_CATS+THEREIS_DOGS"]=$arTOTAL[$region]["THEREIS_CATS"]+$arTOTAL[$region]["THEREIS_DOGS"];
				}
				else
				{
					$arTOTAL[$key]["THEREIS_TOTAL"]=0;
					$arTOTAL[$key][$key1]=$value1["VALUE"];
					$arTOTAL[$key]["THEREIS_TOTAL"]=intval($arTOTAL[$key]["THEREIS_CATS"])+intval($arTOTAL[$key]["THEREIS_DOGS"]);
				};
			break;
			case 'VACCINATED_DOGS':
			case 'VACCINATED_CATS'://local total count
				if ($regiontriger)
				{
					$arTOTAL[$region]["VACCINATED_CATS+VACCINATED_DOGS"]=0;
					$arTOTAL[$region][$key1]=$arTOTAL[$region][$key1]+$value1["VALUE"];
					$arTOTAL[$region]["VACCINATED_CATS+VACCINATED_DOGS"]=$arTOTAL[$region]["VACCINATED_CATS"]+$arTOTAL[$region]["VACCINATED_DOGS"];
				}
				else
				{
					$arTOTAL[$key]["VACCINATED_TOTAL"]=0;
					$arTOTAL[$key][$key1]=$value1["VALUE"];
					$arTOTAL[$key]["VACCINATED_TOTAL"]=intval($arTOTAL[$key]["VACCINATED_CATS"])+intval($arTOTAL[$key]["VACCINATED_DOGS"]);
				};
			break;
			default://total count
				if ($regiontriger)
				{
					if (is_numeric($value1["VALUE"]))
					{
						$arTOTAL[$region][$key1]=$arTOTAL[$region][$key1]+$value1["VALUE"];
					};
				}
				else
				{
					$arTOTAL[$key][$key1]=$value1["VALUE"];
				};
			break;
		};
	};
};
foreach ($arTOTAL as $key => $value) 
{
	foreach ($value as $key1 => $value1) 
	{
		if($key1=="COMPANY"||$key1=="REGION"||$key1=="SETTLEMENTS"||$key1=="DATE_ACTIVE_FROM")
		{
			$arTOTAL["ALL_REGIONS"][$key1]='';
		}
		else
		{
			$arTOTAL["ALL_REGIONS"][$key1]=$arTOTAL["ALL_REGIONS"][$key1]+$value1;
		}
	}
}
foreach ($arResult['madnessregions'] as $key => $value) 
{
	$arUniqueRegion[$value["UF_XML_ID"]]=$value["UF_NAME"];
}
$arUniqueRegion=array_unique($arUniqueRegion);
$arUniqueCompany=array_unique($arUniqueCompany);
//готовим файл к експорту
//шапка
$arCSV=array();
$arCSV['UF_OFFICE'][1]=iconv('utf-8', 'windows-1251',$arResult['madnessregions'][$regionfilter]["UF_OFFICE"]);
$arCSV['UF_USER'][1]=iconv('utf-8', 'windows-1251',$arResult['madnessregions'][$regionfilter]["UF_USER"]);
$arCSV['INFO'][1]=iconv('utf-8', 'windows-1251',$MES["INFO"]);
$arCSV['ZAGOLOVOK'][1]=iconv('utf-8', 'windows-1251',$MES["ZAGOLOVOK"]);
$prevMonth='';
$prevYear='';
$temp=array('firstrow','secondrow','thirdrow');
foreach ($temp as $key => $row) 
{
	for ($col=0; $col < 22; $col++) 
	{ 
	 $arCSV[$row][$col]='';
	}
}
//shift
$shift=0;
if (!$regiontriger)
{
	$shift=1;
	$arCSV["firstrow"][2]=iconv('utf-8', 'windows-1251',$MES["COMP_NAME"]);
}
$arCSV['firstrow'][0]=iconv('utf-8', 'windows-1251',$MES["REGION_NAME"]);
$arCSV['firstrow'][1]=iconv('utf-8', 'windows-1251',$MES["REGION_COUNT"]);
$arCSV['firstrow'][2+$shift]=iconv('utf-8', 'windows-1251',$MES["PETS_COUNT"]);
$arCSV['firstrow'][5+$shift]=iconv('utf-8', 'windows-1251',$MES["VACZ_PRTS"]);
$arCSV['firstrow'][8+$shift]=iconv('utf-8', 'windows-1251',$MES["MEASURES"]);
$arCSV['firstrow'][12+$shift]=iconv('utf-8', 'windows-1251',$MES["FOUNDATION"]);
$arCSV['firstrow'][13+$shift]=iconv('utf-8', 'windows-1251',$MES["SMI"]);
$arCSV['firstrow'][16+$shift]=iconv('utf-8', 'windows-1251',$MES["PROVEDENO"]);
$arCSV['firstrow'][18+$shift]=iconv('utf-8', 'windows-1251',$MES["GOS_RABOTA"]);
$arCSV['secondrow'][2+$shift]=iconv('utf-8', 'windows-1251',$MES["TOTAL"]);
$arCSV['secondrow'][3+$shift]=iconv('utf-8', 'windows-1251',$MES["V_TOM_CHISLE"]);
$arCSV['secondrow'][5+$shift]=iconv('utf-8', 'windows-1251',$MES["TOTAL"]);
$arCSV['secondrow'][6+$shift]=iconv('utf-8', 'windows-1251',$MES["V_TOM_CHISLE"]);
$arCSV['secondrow'][8+$shift]=iconv('utf-8', 'windows-1251',$MES["PO_OTLOVU"]);
$arCSV['secondrow'][10+$shift]=iconv('utf-8', 'windows-1251',$MES["PO_OTSTRELU"]);
$arCSV['secondrow'][13+$shift]=iconv('utf-8', 'windows-1251',$MES["RADIO_TV"]);
$arCSV['secondrow'][14+$shift]=iconv('utf-8', 'windows-1251',$MES["LISTOVOK"]);
$arCSV['secondrow'][15+$shift]=iconv('utf-8', 'windows-1251',$MES["IN_PRINT"]);
$arCSV['secondrow'][16+$shift]=iconv('utf-8', 'windows-1251',$MES["REJDOV"]);
$arCSV['secondrow'][17+$shift]=iconv('utf-8', 'windows-1251',$MES["BESED"]);
$arCSV['secondrow'][18+$shift]=iconv('utf-8', 'windows-1251',$MES["PREDPISANIJ"]);
$arCSV['secondrow'][19+$shift]=iconv('utf-8', 'windows-1251',$MES["PROTOKOLOV"]);
$arCSV['secondrow'][20+$shift]=iconv('utf-8', 'windows-1251',$MES["OSHTRAF"]);
$arCSV['secondrow'][21+$shift]=iconv('utf-8', 'windows-1251',$MES["NA_SUMMU"]);
$arCSV['thirdrow'][3+$shift]=iconv('utf-8', 'windows-1251',$MES["CATS"]);
$arCSV['thirdrow'][4+$shift]=iconv('utf-8', 'windows-1251',$MES["DOGS"]);
$arCSV['thirdrow'][6+$shift]=iconv('utf-8', 'windows-1251',$MES["CATS"]);
$arCSV['thirdrow'][7+$shift]=iconv('utf-8', 'windows-1251',$MES["DOGS"]);
$arCSV['thirdrow'][8+$shift]=iconv('utf-8', 'windows-1251',$MES["CATS"]);
$arCSV['thirdrow'][9+$shift]=iconv('utf-8', 'windows-1251',$MES["DOGS"]);
$arCSV['thirdrow'][10+$shift]=iconv('utf-8', 'windows-1251',$MES["CATS"]);
$arCSV['thirdrow'][11+$shift]=iconv('utf-8', 'windows-1251',$MES["DOGS"]);
//тело
foreach ($arTOTAL as $key => $value)
{
	foreach ($value as $key1 => $value1) 
	{
		if($key=="ALL_REGIONS"&&$key!="0")
		{
			$arCSV["TOTAL"]['1']=iconv('utf-8', 'windows-1251','ИТОГО:' );
			if($key1!='DATE_ACTIVE_FROM')
			{
				$arCSV[$key][$key1]=iconv('utf-8', 'windows-1251', $value1);
			}
		}
		else
		{
			$prevYear=$currYear;
			$prevMonth=$currMonth;
			$currMonth=date("m",strtotime($value["DATE_ACTIVE_FROM"]));
			$currYear=date("Y",strtotime($value["DATE_ACTIVE_FROM"]));
			if (intval($prevMonth)!=intval($currMonth)
				||intval($prevYear)!=intval($currYear))
			{
			$arCSV[$value["DATE_ACTIVE_FROM"]]["1"]=iconv('utf-8', 'windows-1251',$monthesRUS[intval($currMonth)].' '.$currYear.' г.');
			}
			if($key1!='DATE_ACTIVE_FROM')
			{
				$arCSV[$key][$key1]=iconv('utf-8', 'windows-1251', $value1);
			}
		}
	}
}
foreach ($arResult["CREATED_BY"] as $key => $value) 
{
	$rsUser = CUser::GetByID(intval($value));
	$arUser = $rsUser->Fetch();
	$arCSV["FOOTER"][]=iconv('utf-8', 'windows-1251',$arUser["NAME"]);
	$arCSV["FOOTER2"][]=iconv('utf-8', 'windows-1251',$arUser["LAST_NAME"]);
}
//footer
//записываем файл
$file = $_SERVER['DOCUMENT_ROOT']."/upload/form_madness.csv";
unlink($file);
$fp = fopen($file, 'w');
foreach($arCSV as $arItem)
{
	fputcsv($fp, $arItem, ';');
}
fclose($fp);
?>
<?//---------------------------VISIBLE PART--------------------------------------?>
<a href="<?=$dir?>filter_form_madness.php?form_cancel=YES" id="CANCEL"><?=$MES["CANCEL_FILTRATION"]?></a>
		<br>
		<br>
<div class="vet_sert_2">
		<div class="info">
			<center>
			<label for="company_select"><?=$MES["CHOOSE_REGION"]?></label>
				<select id="company_select">
					<option value="select_all_regions" <?if($regiontriger){echo "selected";}?>><?=$MES["ALL_REGIONS"]?></option>
					<?foreach ($arUniqueRegion as $key => $value) 
					{?>
						<option value="<?=$key;?>" <? if ($key==$_GET["region_name"]){echo "selected";}?>><?=$value;?></option>
					<?}?>
				</select></br>
				<?if ($_GET["form_madness_period"]=='lastmonth'||strlen($_GET["form_madness_period"])<=0){echo '<strong>';}?>
				<a href="<?=$dir?>filter_form_madness.php?form_madness_period=lastmonth&amp;region_name=<?=$_GET["region_name"]?>" id="lmh"><?=$MES["LAST_M"]?></a>
				<?if ($_GET["form_madness_period"]=='lastmonth'||strlen($_GET["form_madness_period"])<=0){echo '</strong>';}?>
				<?if ($_GET["form_madness_period"]=='lastqarter'){echo '<strong>';}?>
				<a href="<?=$dir?>filter_form_madness.php?form_madness_period=lastqarter&amp;region_name=<?=$_GET["region_name"]?>" id="lkh"><?=$MES["LAST_Q"]?></a>
				<?if ($_GET["form_madness_period"]=='lastqarter'){echo '</strong>';}?>
				<?if ($_GET["form_madness_period"]=='lastyear'){echo '<strong>';}?>
				<a href="<?=$dir?>filter_form_madness.php?form_madness_period=lastyear&amp;region_name=<?=$_GET["region_name"]?>" id="lyh"><?=$MES["LAST_Y"]?></a></br>
				<?if ($_GET["form_madness_period"]=='lastyear'){echo '</strong>';}?>
				<?=$MES["SEL_PERIOD"]?></br>
				<?=$MES["FROM"]?><strong><?=date('d-m-Y',$begin);?></strong> <?=$MES["TO"]?><strong><?=date('d-m-Y',$end);?></strong>
			</center>
		</div>
<div id="container_table">
	<table class="tableclass">
		<thead class="classtbody">
		  <tr class="classtr"> 
		    <th class="classth" rowspan="3"><?=$MES["REGION_NAME"];?></th>
		    <th class="classth" rowspan="3"><?=$MES["REGION_COUNT"];?></th>
		    <?if (!$regiontriger)
				{?>
		    	<th class="classth" rowspan="3"><?=$MES["COMP_NAME"];?></th>
		    <?}?>
		    <th class="classth" colspan="3"><?=$MES["PETS_COUNT"]?></th>
		    <th class="classth" colspan="3"><?=$MES["VACZ_PRTS"];?></th>
		    <th class="classth" colspan="4"><?=$MES["MEASURES"];?></th>
		    <th class="classth" rowspan="3"><?=$MES["FOUNDATION"];?></th>
		    <th class="classth" colspan="3"><?=$MES["SMI"];?></th>
		    <th class="classth" colspan="2"><?=$MES["PROVEDENO"];?></th>
		    <th class="classth" colspan="4"><?=$MES["GOS_RABOTA"];?></th>
		  </tr>
		  <tr class="classtr">
		    <td class="classth" class="classth" rowspan="2"><?=$MES["TOTAL"];?></td>
		    <td class="classth" colspan="2"><?=$MES["V_TOM_CHISLE"];?></td>
		    <td class="classth" rowspan="2"><?=$MES["TOTAL"];?></td>
		    <td class="classth" colspan="2"><?=$MES["V_TOM_CHISLE"];?></td>
		    <td class="classth" colspan="2"><?=$MES["PO_OTLOVU"];?></td>
		    <td class="classth" colspan="2"><?=$MES["PO_OTSTRELU"];?></td>
		    <td class="classth" rowspan="2"><?=$MES["RADIO_TV"];?></td>
		    <td class="classth" rowspan="2"><?=$MES["LISTOVOK"];?></td>
		    <td class="classth" rowspan="2"><?=$MES["IN_PRINT"];?></td>
		    <td class="classth" rowspan="2"><?=$MES["REJDOV"];?></td>
		    <td class="classth" rowspan="2"><?=$MES["BESED"];?></td>
		    <td class="classth" rowspan="2"><?=$MES["PREDPISANIJ"];?></td>
		    <td class="classth" rowspan="2"><?=$MES["PROTOKOLOV"];?></td>
		    <td class="classth" rowspan="2"><?=$MES["OSHTRAF"];?></td>
		    <td class="classth" rowspan="2"><?=$MES["NA_SUMMU"];?></td>
		  </tr>
		  <tr class="classtr">
		    <td class="classth"><?=$MES["CATS"];?></td>
		    <td class="classth"><?=$MES["DOGS"];?></td>
		    <td class="classth"><?=$MES["CATS"];?></td>
		    <td class="classth"><?=$MES["DOGS"];?></td>
		    <td class="classth"><?=$MES["CATS"];?></td>
		    <td class="classth"><?=$MES["DOGS"];?></td>
		    <td class="classth"><?=$MES["CATS"];?></td>
		    <td class="classth"><?=$MES["DOGS"];?></td>
		  </tr>
		</thead>
		<tbody class="classtbody">
			<?$prevMonth='';
			$prevYear='';
			foreach ($arTOTAL as $key => $value)
			{
				if($key=="ALL_REGIONS"&&$key!="0")
				{?>
					<tr class="classtr">
						<td class="classtd" colspan="23">
							 <?echo "ИТОГО: ";?>
						</td>
					</tr>
				<?}
				else
				{
					$prevYear=$currYear;
					$prevMonth=$currMonth;
					$currMonth=date("m",strtotime($value["DATE_ACTIVE_FROM"]));
					$currYear=date("Y",strtotime($value["DATE_ACTIVE_FROM"]));
					if (intval($prevMonth)!=intval($currMonth)
						||intval($prevYear)!=intval($currYear))
					{?>
						<tr class="classtr">
							<td class="classtd" colspan="23">
								<?echo $monthesRUS[intval($currMonth)].' '.$currYear.' г.';?>
							</td>
						</tr>
					<?};
				}?>
				<tr class="classtr">
					<?foreach ($value as $key2 => $value2) 
					{
						if($key2!='DATE_ACTIVE_FROM')
						{?>
							<td class="classtd"><?=$value2;?></td>
						<?}
					}?>
				</tr>
			<?}?>
		</tbody>
	</table>
</div>
</br>
	<INPUT TYPE="button" VALUE=" Печать результата" ONCLICK="printtext()">
	<INPUT TYPE="button" VALUE=" Експорт в CSV" onClick="window.location.href='/upload/form_madness.csv'">
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>