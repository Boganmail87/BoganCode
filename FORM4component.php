<?php
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */

// номер инфоблока формы
$FORM_IBLOCK_ID = 3;
$CONDITIONS_IBLOCK_ID = 4;
/*
//

*/
//пользветель
global $USER;
$USER_ID=$USER->GetID();
//загрузка одной формы
$rsUser = CUser::GetList(($by="ID"), ($order="desc"), array("ID"=>$USER_ID),array("SELECT"=>array("UF_*")));
$arUser = $rsUser->Fetch();
if(CModule::IncludeModule("iblock")){
	$arSelect =array("NAME", "DATE_ACTIVE_FROM","ACTIVE", "ID","IBLOCK_ID","CREATED_USER_ID","DATE_CREATE");
	$arFilter = Array('IBLOCK_ID'=>$FORM_IBLOCK_ID, "CREATED_USER_ID"=>$USER_ID);
	$res = CIBlockElement::GetList(Array("DATE_CREATE"=>"ASC"), $arFilter,false,false, $arSelect);?>
	<?while($ob = $res->GetNextElement()){ 
		$arFields = $ob->GetFields();  
		$arPropsFirst = $ob->GetProperties();
		$arResult["PRELOAD_FORM4"][]=$arFields+$arPropsFirst;
	}//while
}
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
$arResult["PARAMS_HASH"] = md5(serialize($arParams).$this->GetTemplateName());
$arParams["EVENT_NAME_GOOD"] = "FORM4_GOOD";
$arParams["EVENT_NAME_BAD"] = "FORM4_BAD";
$arParams["EMAIL_TO"] = trim($arParams["EMAIL_TO"]);
if($arParams["EMAIL_TO"] == '')
	$arParams["EMAIL_TO"] = COption::GetOptionString("main", "email_from");
$arParams["OK_TEXT"] = trim($arParams["OK_TEXT"]);
if($arParams["OK_TEXT"] == '')
	$arParams["OK_TEXT"] = GetMessage("MF_OK_MESSAGE");
//подключаем блоки
CModule::IncludeModule("iblock");
CModule::IncludeModule('highloadblock');
//список стран для выпадающего списка формы
$rsData = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Countries')));
if ($arData = $rsData->fetch()){
    $Entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData);
	$Query = new \Bitrix\Main\Entity\Query($Entity); 
	$Query->setSelect(array('*'));
	$Query->setOrder(array('UF_NAME' => 'ASC'));
	$result = $Query->exec();
	$result = new CDBResult($result);
	$arLang = array();
	while ($row = $result->Fetch()){
		$arResult['COUNTRIES'][] = $row;
	}
}
//список ТН ВЭД для выпадающего списка формы
$rsData = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Tnved')));
if ($arData = $rsData->fetch()){
    $Entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData);
	$Query = new \Bitrix\Main\Entity\Query($Entity); 
	$Query->setSelect(array('*'));
	$Query->setOrder(array('UF_NAME' => 'ASC'));
	$result = $Query->exec();
	$result = new CDBResult($result);
	$arLang = array();
	while ($row = $result->Fetch()){
		$arResult['TNVEDS'][] = $row;
	}
}
//список  для выпадающего списка формы
$rsData = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Unloadpointform4')));
if ($arData = $rsData->fetch()){
    $Entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData);
	$Query = new \Bitrix\Main\Entity\Query($Entity); 
	$Query->setSelect(array('*'));
	$Query->setFilter(array('UF_USER'=> intval($USER_ID)));
	$Query->setOrder(array('UF_NAME' => 'ASC'));
	$result = $Query->exec();
	$result = new CDBResult($result);
	$arLang = array();
	while ($row = $result->Fetch()){
		$arResult['unloadpoint'][] = $row;
	}
}
//список ТН ВЭД для выпадающего списка формы
$rsData = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Entitiesform4')));
if ($arData = $rsData->fetch()){
    $Entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData);
	$Query = new \Bitrix\Main\Entity\Query($Entity); 
	$Query->setSelect(array('*'));
	$Query->setFilter(array('UF_USER'=> intval($USER_ID)));
	$Query->setOrder(array('UF_NAME' => 'ASC'));
	$result = $Query->exec();
	$result = new CDBResult($result);
	$arLang = array();
	while ($row = $result->Fetch()){
		$arResult['entities'][] = $row;
	}
}
//поля которые можно сохранять в справочники при заполнении форм
$arNeedToWrite=array(
	"unloadpoint"=> array(
		"NEEDTOWRITE"=>true,
		"ID"=>21
		),
	"entities"=> array(
		"NEEDTOWRITE"=>true,
		"ID"=>22
		)
);

if($_SERVER["REQUEST_METHOD"] == "GET" && strlen($_GET["form_4_ID"])>0)
{	global $USER;
	$USER_ID=$USER->GetID();
	$IBLOCKID=3;
	$arSelect =array("NAME", "DATE_ACTIVE_FROM","ACTIVE", "ID","IBLOCK_ID","CREATED_USER_ID");
	$arFilter = Array('IBLOCK_ID'=>$IBLOCKID, "ID"=>$_GET["form_4_ID"],"CREATED_USER_ID"=>$USER_ID);
	$res = CIBlockElement::GetList(Array(), $arFilter,false,false, $arSelect);
	while($ob = $res->GetNextElement()){ 
		$arFields = $ob->GetFields();  
		$arResult["FORM4"]["FIELDS"]=$arFields;
		$arPropsFirst = $ob->GetProperties();
		$arResult["FORM4"]["PROPS"]=$arPropsFirst;
	}//while
		$arResult["TTN"] = $arResult["FORM4"]["PROPS"]["TTN"]["VALUE"];
		$arResult["QUALITY"] = $arResult["FORM4"]["PROPS"]["QUALITY"]["VALUE"];
		$arResult["CODE"] = $arResult["FORM4"]["PROPS"]["CODE"]["VALUE"];
		$arResult["TYPE"] = $arResult["FORM4"]["PROPS"]["TYPE"]["VALUE"];
		$arResult["QUANTITY"] = $arResult["FORM4"]["PROPS"]["QUANTITY"]["VALUE"];
		$arResult["WEIGHT"] = $arResult["FORM4"]["PROPS"]["WEIGHT"]["VALUE"];
		$arResult["POINT"] = $arResult["FORM4"]["PROPS"]["POINT"]["VALUE"];
		$arResult["ADRESS"] = $arResult["FORM4"]["PROPS"]["ADRESS"]["VALUE"];
		$arResult["COUNTRY"] = $arResult["FORM4"]["PROPS"]["COUNTRY"]["VALUE"];
		$arResult["COUNTRY2"] = $arResult["FORM4"]["PROPS"]["COUNTRY2"]["VALUE"];
		$arResult["AUTHOR"] = $arResult["FORM4"]["PROPS"]["AUTHOR"]["VALUE"];
		$arResult["LAST_ID"] = $arResult["FORM4"]["PROPS"]["LAST_ID"]["VALUE"];
		$arResult["UNLOADPOINT"] = $arResult["FORM4"]["PROPS"]["UNLOADPOINT"]["VALUE"];
		$arResult["ENTITIES"] = $arResult["FORM4"]["PROPS"]["ENTITIES"]["VALUE"];
}


//обработка формы
if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["submit"] <> '' && (!isset($_POST["PARAMS_HASH"]) || $arResult["PARAMS_HASH"] === $_POST["PARAMS_HASH"]))
{
	$arResult["ERROR_MESSAGE"] = array();
	if(check_bitrix_sessid())
	{
//проверка ошибок	
		if(strlen($_POST["ttn"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_TTN");
		if(strlen($_POST["quality"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_QUALITY"); 
		if(strlen($_POST["code"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_CODE"); //проверка тнвэд
		else 
		{
			$tncode_arr = explode("-",$_POST["code"]); 
			$tncode = trim($tncode_arr[0]);
			$TN_OK = false;
			foreach($arResult['TNVEDS'] as $tnved)
			{
				if($tnved["UF_KOD"]==$tncode) $TN_OK = 1;
			}
			if(!$TN_OK) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_CODE1"); 
		}
		if(strlen($_POST["type"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_TYPE");
		if(strlen($_POST["quantity"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_QUANTITY"); 
		if((int)$_POST["quantity"]<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_QUANTITY1"); 
		if(!(roundproperway($_POST["weight"]))) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_WEIGHT1");
		if(strlen($_POST["weight"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_WEIGHT");
		if(strlen($_POST["point"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_POINT"); 
		if(strlen($_POST["adress"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_ADRESS");
		if(strlen($_POST["country"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_COUNTRY");//проверка стран
		else {
			$Co_OK = false;
			foreach($arResult['COUNTRIES'] as $country){
				if($country["UF_NAME"]==$_POST["country"]) $Co_OK = 1;
			}
			if(!$Co_OK) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_COUNTRY1");
		}
		if(strlen($_POST["country2"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_COUNTRY2");
		else {
			$Co_OK2 = false;
			foreach($arResult['COUNTRIES'] as $country2){
				if($country2["UF_NAME"]==$_POST["country2"]) $Co_OK2 = 1;
			}
			if(!$Co_OK2) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_COUNTRY21");
		}
		if(strlen($_POST["entities"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_ENTITIES");
		if(strlen($_POST["author"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_AUTHOR");
		
//если нет ошибок		
		if(empty($arResult["ERROR_MESSAGE"]))
		{
			//выбираем информацию о компании по привязке к пользователю
				$rsUser = CUser::GetList(($by="ID"), ($order="desc"), array("ID"=>$USER_ID),array("SELECT"=>array("UF_*")));
				$arUser = $rsUser->Fetch();
				$HBLOCK_NAMEOrganization = 'Organization';
				
				$rsDataOrganization = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>$HBLOCK_NAMEOrganization)));
				$arDataOrganization = $rsDataOrganization->fetch();
				$EntityOrganization = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arDataOrganization);
				
				$QueryOrganization = new \Bitrix\Main\Entity\Query($EntityOrganization);
				$QueryOrganization->setSelect(array("*"));
				$QueryOrganization->setFilter(array("ID"=> $arUser["UF_ORG"]));
				
				$resultOrganization = $QueryOrganization->exec();
				$resultOrganization = new CDBResult($resultOrganization);
				
				$MaxNumber = '';
				$WriteMaxNumber = '';
				$YearNumber = '';
				
				if ($rowOrganization = $resultOrganization->Fetch()){
					$ORG_ID = $rowOrganization["ID"];
					$ORG_CODE = $rowOrganization["UF_CODE"];
					$ORG_NAME = $rowOrganization["UF_NAME"];
					$ORG_XML_ID = $rowOrganization["UF_XML_ID"];
					$MaxNumber = $rowOrganization["UF_COUNTER"]+1;
					$WriteMaxNumber = $MaxNumber;
					$YearNumber = $rowOrganization["UF_YEAR"];
					//подгоняем вид значение номера под формат
						switch (strlen($MaxNumber)) {
							case 0:
								$MaxNumber =  "0000".$MaxNumber;
								break;
							case 1:
								$MaxNumber =  "000".$MaxNumber;
								break;
							case 2:
								$MaxNumber =  "00".$MaxNumber;
								break;
							case 3:
								$MaxNumber =  "0".$MaxNumber;
								break;
						}
					
				}
				$NumberString = '';
			//выбираем все элементы разрешений для сравнения
				$allow_number = 1;
				
				$resConditions = CIBlockElement::GetList(Array("ID"=>"DESC"), Array("IBLOCK_ID"=>$CONDITIONS_IBLOCK_ID, "ACTIVE"=>"Y"), false, false, Array("ID", "ACTIVE_FROM", "ACTIVE_TO", "PROPERTY_*"));
				while($obConditions = $resConditions->GetNext())
				{	
					$allow_number17 = 1;$allow_number19 = 1;$allow_number20 = 1;$allow_number21 = 1;$allow_number22 = 1;$allow_number23 = 1;$allow_number_date = 1;
					if(count($obConditions["PROPERTY_17"])>0){
						if(in_array($ORG_XML_ID, $obConditions["PROPERTY_17"])) $allow_number17 = false; //echo '17-'.$allow_number17.'<br>';
					}  else $allow_number17 = false;
					if(count($obConditions["PROPERTY_19"])>0){
						foreach($arResult['TNVEDS'] as $tnved){
							$tncode_arr = explode("-",$_POST["code"]); 
							$tncode = trim($tncode_arr[0]);						
							if($tnved["UF_KOD"]==$tncode) $tnved_xml_id = $tnved["UF_XML_ID"];
						}
						if(in_array($tnved_xml_id, $obConditions["PROPERTY_19"])) $allow_number19 = false;//echo '19-'.$allow_number19.'<br>';
					} else $allow_number19 = false;
					if(strlen($obConditions["PROPERTY_20"])>0){
						if($_POST["type"] == $obConditions["PROPERTY_20"]) $allow_number20 = false;//echo '20-'.$allow_number20.'<br>';
					} else $allow_number20 = false;
					if(strlen($obConditions["PROPERTY_21"])>0){
						if($_POST["point"] == $obConditions["PROPERTY_21"]) $allow_number21 = false;//echo '21-'.$allow_number21.'<br>';
					} else $allow_number21 = false; 
					if(count($obConditions["PROPERTY_22"])>0){
						foreach($arResult['COUNTRIES'] as $country){if($country["UF_NAME"]==$_POST["country"]) $country_xml_id = $country["UF_XML_ID"];}
						if(in_array($country_xml_id, $obConditions["PROPERTY_22"])) $allow_number22 = false;//echo '22-'.$allow_number22.'<br>';
					} else $allow_number22 = false; 
					if(count($obConditions["PROPERTY_23"])>0){
						foreach($arResult['COUNTRIES'] as $country){if($country["UF_NAME"]==$_POST["country2"]) $country_xml_id = $country["UF_XML_ID"];}
						if(in_array($country_xml_id, $obConditions["PROPERTY_23"])) $allow_number23 = false;//echo '23-'.$allow_number23.'<br>';
					} else $allow_number23 = false; 
					if(strlen($obConditions["ACTIVE_FROM"])>0 && strlen($obConditions["ACTIVE_TO"])>0){
						$arr = ParseDateTime($obConditions["ACTIVE_FROM"]);
						$arr1 = ParseDateTime($obConditions["ACTIVE_TO"]);
						if((time()>mktime(0, 0, 0, $arr["MM"], $arr["DD"], $arr["YYYY"])) || (time()<mktime(23, 59, 59, $arr1["MM"], $arr1["DD"], $arr1["YYYY"]))) $allow_number_date = false;//echo 'date-'.$allow_number_date.'<br>';
					} else $allow_number_date = false; 
					
					if(!$allow_number17 && !$allow_number19 && !$allow_number20 && !$allow_number21 && !$allow_number22 && !$allow_number23 && !$allow_number_date) {
						$allow_number = false;
						break;
					}
				}
//определяем максимальное значение номера записи формы
				$HBLOCK_NAME = 'Counterform4';
				$rsData = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>$HBLOCK_NAME)));
				if ($arData = $rsData->fetch()){
					$Entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData);
					$Query = new \Bitrix\Main\Entity\Query($Entity);
					$Query->setSelect(array("UF_COUNTER"));
					$Query->setFilter(array("ID"=> 1));
					$result = $Query->exec();
					$result = new CDBResult($result);
					if ($row = $result->Fetch()){
						$MaxCounter = $row["UF_COUNTER"]+1;
					}
				}	
//если заполняется еще одна позиция формы
				if(strlen($_POST["last_id"])>0 && $_POST["next"]=="Y"){
//определяем порядковый номер прошлой позиции
					$res1 = CIBlockElement::GetList(Array("ID"=>"DESC"), Array("IBLOCK_ID"=>$FORM_IBLOCK_ID), false, Array("nPageSize"=>1), Array("ID", "PROPERTY_LAST_ID"));
					while($ob1 = $res1->GetNextElement())
					{
					 	$arFields1 = $ob1->GetFields();
					 	$LAST_ID = $arFields1["PROPERTY_LAST_ID_VALUE"];
					}
//определем номер разрешения позиций с таким же порядком
					if($allow_number){
						$res1 = CIBlockElement::GetList(Array("ID"=>"DESC"), Array("IBLOCK_ID"=>$FORM_IBLOCK_ID, "property_LAST_ID"=>$LAST_ID), false, false, Array("ID", "PROPERTY_LAST_ID", "PROPERTY_NUMBER"));
						while($ob1 = $res1->GetNextElement())
						{
						 	$arFields1 = $ob1->GetFields();
						 	if(strlen($arFields1["PROPERTY_NUMBER_VALUE"])>0) $NumberString = $arFields1["PROPERTY_NUMBER_VALUE"];
						}
					}
				} 
				else $LAST_ID = $MaxCounter; 
				
//формируем окончательный номер разрешения если еще не сформирован
				if($allow_number){
					if(strlen($NumberString)<=0){
						$NumberString = $ORG_CODE.'-'.$MaxNumber;
					}
					$ACTIVE = "Y";
				}else{
					$NumberString = '';
						$ACTIVE = "N";
				}

				foreach ($arNeedToWrite as $key => $value) //проверяем надо ли добавлять в справочники
				{
					foreach ($arResult[$key] as $key1 => $value2) 
					{	
						if($value2["UF_NAME"]==$_POST[$key]&&$value2["UF_USER"]==intval($USER_ID))
						{
							$arNeedToWrite[$key]["NEEDTOWRITE"]=false;
						}
					}
				}
					//добавляем информацию в справочники
					foreach ($arNeedToWrite as $key => $value) 
					{
						if ($value["NEEDTOWRITE"]==true)
				  	{		//Подготовка:
							if (CModule::IncludeModule('highloadblock')) 
							{
							   $arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById($value["ID"])->fetch();
							   $obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
							   $strEntityDataClass = $obEntity->getDataClass();
							//Добавление:
								   $arElementFields = array(
									"UF_NAME" => $_POST[$key],
									"UF_XML_ID"=>md5($_POST[$key]),
									"UF_USER"=>$USER_ID,
							   );
							   $obResult = $strEntityDataClass::add($arElementFields);
							   $ID = $obResult->getID();
							   $bSuccess = $obResult->isSuccess();
							}
							if(!$bSuccess)
							{
								echo implode(', ', $result->getErrorMessages()); //выведем ошибки
							}
						}
					}
//добавляем форму в инфоблок				
				$el = new CIBlockElement;

				$PROP = array();
				$PROP["COUNTER"] = $MaxCounter;
				$PROP["NUMBER"] = $NumberString; 
				$PROP["TTN"] = $_POST["ttn"];
				$PROP["QUALITY"] = $_POST["quality"]; 
				$PROP["CODE"] = $_POST["code"]; 
				$PROP["TYPE"] = $_POST["type"]; 
				$PROP["QUANTITY"] = $_POST["quantity"]; 
				$PROP["WEIGHT"] = $_POST["weight"]; 
				$PROP["POINT"] = $_POST["point"]; 
				$PROP["ADRESS"] = $_POST["adress"]; 
				$PROP["COUNTRY"] = $_POST["country"];
				$PROP["COUNTRY2"] = $_POST["country2"]; 
				$PROP["AUTHOR"] = $_POST["author"];
				$PROP["UNLOADPOINT"] = $_POST["unloadpoint"];
				$PROP["ENTITIES"] = $_POST["entities"];
				$PROP["LAST_ID"] = $LAST_ID;				

				$arLoadProductArray = Array(
				  "MODIFIED_BY"    => $arUser["ID"], 
				  "IBLOCK_ID"      => $FORM_IBLOCK_ID,
				  "PROPERTY_VALUES"=> $PROP,
				  "NAME"           => $ORG_NAME,
				  "ACTIVE"         => $ACTIVE,  
				  "DATE_ACTIVE_FROM" => ConvertTimeStamp(time(), "SHORT")
				  );


				if($PRODUCT_ID = $el->Add($arLoadProductArray))
				{
//пишем последний номер и год его записи в хайлоад организаций	
					if($allow_number && $_POST["next"] != "Y"){
						$DataClassOrganization = $EntityOrganization->getDataClass();
						if($YearNumber==date('Y')){	
							$arBxDataOrganization = array('UF_COUNTER' => $WriteMaxNumber,'UF_YEAR' => $YearNumber,);
						}else{
							$arBxDataOrganization = array('UF_COUNTER' => 1,'UF_YEAR' => date('Y'),);
						}
						$resultOrganization = $DataClassOrganization::update($ORG_ID, $arBxDataOrganization);
					}
//пишем номер счетчика записи					
					$DataClass = $Entity->getDataClass();	
					$arBxData = array('UF_COUNTER' => $MaxCounter);
					$result = $DataClass::update(1, $arBxData);
//отправляем письма
					$LetterFields = Array(
						"ORG" => $ORG_NAME,
						"DATE" => ConvertTimeStamp(time(), "SHORT"),
						"NUMBER" => $NumberString,
						"TNVED" => $_POST["code"],
						"VID" => $_POST["type"],
						"QUANTITY" => $_POST["quantity"],
						"WEIGHT" => $_POST["weight"],
						"POINT" => $_POST["point"],
						"ADRESS" => $_POST["adress"],
						"UNLOADPOINT" => $_POST["unloadpoint"],
						"ENTITIES" => $_POST["entities"],
						"EMAIL_TO" => $arParams["EMAIL_TO"]
					);
					if($ACTIVE == "Y"){
						CEvent::Send($arParams["EVENT_NAME_GOOD"], SITE_ID, $LetterFields);
					}elseif($ACTIVE == "N"){
						CEvent::Send($arParams["EVENT_NAME_BAD"], SITE_ID, $LetterFields);
					}
					LocalRedirect($APPLICATION->GetCurPageParam("success=".$arResult["PARAMS_HASH"]."&last=".$PRODUCT_ID, Array("success","last","form_4_ID")));
				}
				else
				  $arResult["ERROR_MESSAGE"][] = $el->LAST_ERROR;
			} 
		
		$arResult["TTN"] = htmlspecialcharsbx($_POST["ttn"]);
		$arResult["QUALITY"] = htmlspecialcharsbx($_POST["quality"]); 
		$arResult["CODE"] = htmlspecialcharsbx($_POST["code"]); 
		$arResult["TYPE"] = htmlspecialcharsbx($_POST["type"]); 
		$arResult["QUANTITY"] = htmlspecialcharsbx($_POST["quantity"]); 
		$arResult["WEIGHT"] = htmlspecialcharsbx($_POST["weight"]); 
		$arResult["POINT"] = htmlspecialcharsbx($_POST["point"]); 
		$arResult["ADRESS"] = htmlspecialcharsbx($_POST["adress"]); 
		$arResult["COUNTRY"] = htmlspecialcharsbx($_POST["country"]);
		$arResult["COUNTRY2"] = htmlspecialcharsbx($_POST["country2"]); 
		$arResult["AUTHOR"] = htmlspecialcharsbx($_POST["author"]); 
		$arResult["LAST_ID"] = htmlspecialcharsbx($_POST["last_id"]); 
		$arResult["UNLOADPOINT"] = htmlspecialcharsbx($_POST["unloadpoint"]); 
		$arResult["ENTITIES"] = htmlspecialcharsbx($_POST["entities"]); 
		
	}
	else
		$arResult["ERROR_MESSAGE"][] = GetMessage("MF_SESS_EXP");
}
elseif($_REQUEST["success"] == $arResult["PARAMS_HASH"])
{
	$arResult["OK_MESSAGE"] = $arParams["OK_TEXT"];
	$arResult["LAST_ID"] = $_REQUEST["last"];
	$arResult["NOT_GOOD_MESSAGE"] = '';
	if(CModule::IncludeModule("iblock"))
	{	
		$ElementID=$arResult["LAST_ID"];
		$res = CIBlockElement::GetProperty($FORM_IBLOCK_ID, $ElementID, false, false, array("CODE" => "NUMBER"));
    if ($ob = $res->GetNext())
    { 
      if(strlen($ob['VALUE'])<=0)
      {
      	$arResult["NOT_GOOD_MESSAGE"] = GetMessage("MF_NOT_GOOD");
      	$arResult["OK_MESSAGE"] = GetMessage("OK_MESSAGE_SAVE");
    	}
    	else
    	{
    		$arResult["OK_MESSAGE"] = GetMessage("OK_MESSAGE_NUMBER").$ob['VALUE'];
    	}
		}
	}
}//elseif($_REQUEST["success"] == $arResult["PARAMS_HASH"])
$this->IncludeComponentTemplate();
