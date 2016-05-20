<?php
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
/**
 * Bitrix vars
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */
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
function getKv()
{
	$kv = intval((date('m') + 2)/3);
	$kv1["kvartal"]=$kv;
	$kv1["year"]=intval(date('Y'));
	return $kv1;
}
function getQuarterInterval($quarter, $year = NULL)
{
	if (!$year)
	{
		$year = date('Y');
	}
	$start = array();
	$end = array();
	$start['year'] = $year;
	$start['month'] = ($quarter-1)*3 + 1;
	$start['day'] = 1;
	$end['year'] = $year;
	$end['month'] = ($quarter)*3;
	$end['day'] = cal_days_in_month(CAL_GREGORIAN, ($quarter)*3, $year);
	return array
	(
		date_format(date_create(implode('-', $start)),'d.m.Y'),
		date_format(date_create(implode('-', $end)),'d.m.Y'),
	);
}
$arResult["PARAMS_HASH"] = md5(serialize($arParams).$this->GetTemplateName());
//подключаем модули
CModule::IncludeModule("iblock");
CModule::IncludeModule('highloadblock');
//пользователь
global $USER;
$USER_ID=$USER->GetID();
$rsUser = CUser::GetList(($by="ID"), ($order="desc"), array("ID"=>$USER_ID),array("SELECT"=>array("UF_*")));
$arUser = $rsUser->Fetch();
$numREGION=$arUser["UF_USER_REGION"];

// номер инфоблока формы
$FORM_IBLOCK_ID = 7;
//список "Регионы" для выпадающего списков формы
$rsData1 = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Madnessregions')));
if ($arData1 = $rsData1->fetch())
{
	$Entity1 = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData1);
	$Query1 = new \Bitrix\Main\Entity\Query($Entity1); 
	$Query1->setSelect(array('*'));
	$Query1->setFilter(array("ID"=> intval($numREGION)));
	// $Query1->setOrder(array('UF_NAME' => 'ASC'));
	$result1 = $Query1->exec();
	$result1 = new CDBResult($result1);
	$arLang1 = array();
	while ($row1 = $result1->Fetch())
	{
		$UserField = CUserFieldEnum::GetList(array(), array("ID" => $row1["UF_USER"]));
		unset($row1["UF_USER"]);
		while($UserFieldAr = $UserField->Fetch())
		{
			$row1["UF_USER"]=$UserFieldAr;
		}
		$UserField = CUserFieldEnum::GetList(array(), array("ID" => $row1["UF_ORGANIZATIONS"]));
		unset($row1["UF_ORGANIZATIONS"]);
		while($UserFieldAr = $UserField->Fetch())
		{
			$row1["UF_ORGANIZATIONS"][$UserFieldAr["ID"]]=$UserFieldAr;
		}
		$arResult['madnessregions'] = $row1;
	}
}
//задаем период выборки
if($_SERVER["REQUEST_METHOD"] == "GET")
{
	switch ($_GET["form_madness_period"]) 
	{
		case 'lastmonth': 
			$begin=date(/*$DB->DateFormatToPHP(CLang::GetDateFormat("SHORT"))*/'d.m.Y', strtotime('-31 day'));
			$end=date('d.m.Y');
		break;
		case 'lastqarter': 
			$lastKvartal=getKv();
			list($begin,$end) = getQuarterInterval(intval($lastKvartal["kvartal"]), intval($lastKvartal["year"]));
		break;
		case 'lastyear':
			$begin=date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), strtotime('-365 day'));
			$end=date('d.m.Y');
		break;
		default:
			$lastKvartal=getKv();
			$begin=date(/*$DB->DateFormatToPHP(CLang::GetDateFormat("SHORT"))*/'d.m.Y', strtotime('-31 day'));
			$end=date('d.m.Y');
			break;
	}
}
// загружаем ранее созданые отчеты
$arSelect =array("NAME", "DATE_ACTIVE_FROM","ACTIVE", "ID","IBLOCK_ID","CREATED_USER_ID");
//задаем условия выборки
$arFilter = Array
	(
		'IBLOCK_ID'=>$FORM_IBLOCK_ID, 
		"=CREATED_USER_ID"=>$USER_ID,
		">=DATE_ACTIVE_FROM"=>$begin,
		"<=DATE_ACTIVE_FROM"=>$end
	);
$res = CIBlockElement::GetList(Array(), $arFilter,false,false, $arSelect);
while($ob = $res->GetNextElement())
{ 
	$arFields = $ob->GetFields();
	$arPropsFirst = $ob->GetProperties();
	$arResult["ITEMS"][]=$arFields+$arPropsFirst;
}//while
//если сабмит формы
if(
	$_SERVER["REQUEST_METHOD"] == "POST" 
	&& $_POST["submit"] <> '' 
	&& (isset($_POST["PARAMS_HASH"]) 
	|| $arResult["PARAMS_HASH"] === $_POST["PARAMS_HASH"])
	)
{
	if(check_bitrix_sessid())//если сесия
	{
		//errors check
		foreach ($_POST as $key => $value) 
		{ 
			if (strlen($value)<=0)
			{
				$arResult["ERROR_MESSAGE"][] = "Поле не заполнено ".$key;
			}
			else
			{
				switch ($key) 
				{
					case 'bxajaxid':
					case 'AJAX_CALL':
					case 'sessid':
					case 'submit':
					case 'PARAMS_HASH':
					break;
					case 'MONTHS':
						if (strlen($value)<=0)//empty value FIO
						{
							$arResult["ERROR_MESSAGE"][] = ' значение FOR_MONTHS не введно ';
						}
					break;
					case 'FIO':
						if (strlen($value)<=0)//empty value FIO
						{
							$arResult["ERROR_MESSAGE"][] = ' значение не введно ';
						}
						else //check case in array
						{
							$boolFIO=false;
							if($arResult['madnessregions']["UF_NAME"]==$value)
							{
								$boolFIO=true;
							}
							if (!$boolFIO)
							{
								$arResult["ERROR_MESSAGE"][] = 'значение не из списка UF_NAME';
							}
						}
					break;
					case 'COMPANY':
						if (strlen($value)<=0)//empty value COMPANY
						{
							$arResult["ERROR_MESSAGE"][] = ' значение не введно ';
						}
						else //check case in array
						{
							$boolCOMPANY=false;
							if($arResult['madnessregions'] ["UF_OFFICE"]==$value)
							{
								$boolCOMPANY=true;
							}
							if (!$boolCOMPANY)
							{
								$arResult["ERROR_MESSAGE"][] = 'значение не из списка UF_OFFICE';
							}
						}
					break;
					case 'REGION':
						if (strlen($value)<=0)//empty value USER_REGION
						{
							$arResult["ERROR_MESSAGE"][] = ' значение не введно ';
						}
						else
						{

						}
					break;
					case 'ORGANIZATIONS':
						if (strlen($value)<=0)//empty value USER_REGION
						{
							$arResult["ERROR_MESSAGE"][] = ' значение не введно ';
						}
						else //check case in array
						{
							if(!$arResult["madnessregions"]['UF_USER']['VALUE']==$value)
							{
								$arResult["ERROR_MESSAGE"][] = ' значение не match ';
							}
						}
					break;
					case 'AMOUNT':
					case 'LOCAL_BUDGET_FUNDING':
						if(is_numeric($value))
						{
							if(floatval($value)<=0)
							{
								$arResult["ERROR_MESSAGE"][]=$key."меньше нуля/ноль";
							}
						}
						else
						{
							$arResult["ERROR_MESSAGE"][]=$key."не число";
						}
					break;
					default:
						if (!intval($value))
						{
							$arResult["ERROR_MESSAGE"][] = GetMessage("MF_NOT_INT")." ".$key;
						}
					break;
				}
			}
		}
		//если нету ошибок
		if(empty($arResult["ERROR_MESSAGE"]))
		{
			//сюда свтавить проверку для определения активности елемента (если надо)
			$ACTIVE=true;
			$el = new CIBlockElement;//формируем елемент к записи
			$PROP = array();
			foreach ($_POST as $key => $value) 
			{
				switch ($key) 
				{
					case 'bxajaxid':
					case 'AJAX_CALL':
					case 'sessid':
					case 'submit':
					case 'PARAMS_HASH':
					case 'MONTHS':
					case 'FIO':
					break;
					case 'COMPANY':
					case 'ORGANIZATIONS':
						$PROP[$key]=$value;
					break;
					default:
						$PROP[$key]=intval($value);
					break;
				}
			}
			$PROP['KVARTAL']=array("VALUE" =>$lastKvartal["kvartal"]);
			$arLoadProductArray = Array
			(
				"MODIFIED_BY"    => $USER_ID,
				"NAME"           => $_POST['COMPANY'],
				"IBLOCK_ID"      => $FORM_IBLOCK_ID,
				"PROPERTY_VALUES"=> $PROP,
				"ACTIVE"         => $ACTIVE,  
				"DATE_ACTIVE_FROM" => ConvertTimeStamp(MakeTimeStamp($_POST["MONTHS"], "DD.MM.YYYY"), "SHORT"),
			);
			if($USER->IsAdmin()):
			?><pre><?print_r($arLoadProductArray);?></pre><?
			endif;
			/*------------------------------------------------------------*/
			// if($PRODUCT_ID = $el->Add($arLoadProductArray))//если запись прошла успешно
			/*------------------------------------------------------------*/
			// {
			// 	echo "запись прошла успешно";
			// }
		}//if(empty($arResult["ERROR_MESSAGE"]))
		else
		{
			//відправити якщо томилки
			foreach ($_POST as $key => $value) 
			{
				switch ($key) 
				{
					case 'bxajaxid':
					case 'AJAX_CALL':
					case 'sessid':
					case 'submit':
						break;
					default:
						$arResult["RETURN"][$key] = htmlspecialcharsbx($_POST[$key]);
						break;
				}
			}
		}
	}//if(check_bitrix_sessid())
	else //сессия истекла
	{
		$arResult["ERROR_MESSAGE"][] = GetMessage("MF_SESS_EXP");
	}
}//if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["submit"] <> '' && (!isset($_POST["PARAMS_HASH"]) || $arResult["PARAMS_HASH"] === $_POST["PARAMS_HASH"]))

//подгоняем значения под требуемый формат
foreach ($arResult["ITEMS"] as $key => $value) 
{
	foreach ($value as $key1 => $value1) 
	{ 
		switch ($key1) 
		{
			case 'NAME'://unset not neded values
			case '~NAME':
			case 'DATE_ACTIVE_FROM':
			case '~DATE_ACTIVE_FROM':
			case 'ACTIVE':
			case '~ACTIVE':
			case 'ID':
			case '~ID':
			case 'IBLOCK_ID':
			case '~IBLOCK_ID':
			case 'MONTH':
			case '~MONTH':
			case 'DATE_REPORT':
			case '~DATE_REPORT':
			case '~COMPANY':
			unset($arResult["ITEMS"][$key][$key1]);
				break;
			case 'THEREIS_DOGS'://local total count
			case 'THEREIS_CATS':
				$arResult["TOTAL"]["THEREIS_TOTAL"]=0;
				$arResult["ITEMS"][$key][$key1]["TOTAL".$arResult["ITEMS"][$key]["THEREIS_CATS"]["ID"].$arResult["ITEMS"][$key]["THEREIS_DOGS"]["ID"]]=
				intval($arResult["ITEMS"][$key]["THEREIS_CATS"]["VALUE"])
				+intval($arResult["ITEMS"][$key]["THEREIS_DOGS"]["VALUE"]);
				// записую сумму всіх значень по стовбчику
				$arResult["TOTAL"][$key1]=intval($arResult["TOTAL"][$key1])+intval($value1["VALUE"]);
				$arResult["TOTAL"]["THEREIS_TOTAL"]=intval($arResult["TOTAL"]["THEREIS_DOGS"])+intval($arResult["TOTAL"]["THEREIS_CATS"]);
			break;
			case 'VACCINATED_DOGS':
			case 'VACCINATED_CATS'://local total count
			//записую в ячейку значення муммы
				$arResult["TOTAL"]["VACCINATED_TOTAL"]=0;
				$arResult["ITEMS"][$key][$key1]["TOTAL".$arResult["ITEMS"][$key]["VACCINATED_CATS"]["ID"].$arResult["ITEMS"][$key]["VACCINATED_DOGS"]["ID"]]=
					intval($arResult["ITEMS"][$key]["VACCINATED_CATS"]["VALUE"])
					+intval($arResult["ITEMS"][$key]["VACCINATED_DOGS"]["VALUE"]);
			// записую сумму всіх значень по стовбчику
				$arResult["TOTAL"][$key1]=intval($arResult["TOTAL"][$key1])+intval($value1["VALUE"]);
			// запис сумми сумм двох стовбчиків
				$arResult["TOTAL"]["VACCINATED_TOTAL"]=intval($arResult["TOTAL"]["VACCINATED_DOGS"])+intval($arResult["TOTAL"]["VACCINATED_CATS"]);
			break;
			case'AMOUNT':
			case'LOCAL_BUDGET_FUNDING':
				$arResult["ITEMS"][$key][$key1]["VALUE"]=roundproperway($arResult["ITEMS"][$key][$key1]["VALUE"]);
				$arResult["TOTAL"][$key1]=roundproperway(floatval($arResult["TOTAL"][$key1])+floatval($value1["VALUE"]));
			break;
			case 'COMPANY'://do nothing
			break;
			default://total count
			$arResult["TOTAL"][$key1]=intval($arResult["TOTAL"][$key1])+intval($value1["VALUE"]);
			break;
		}
	}
}
$this->IncludeComponentTemplate();
?>