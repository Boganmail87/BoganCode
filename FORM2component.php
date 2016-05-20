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
function is_valid_date($value, $format = 'dd.mm.yyyy'){ 
    if(strlen($value) >= 6 && strlen($format) == 10){ 
        
        // find separator. Remove all other characters from $format 
        $separator_only = str_replace(array('m','d','y'),'', $format); 
        $separator = $separator_only[0]; // separator is first character 
        
        if($separator && strlen($separator_only) == 2){ 
            // make regex 
            $regexp = str_replace('mm', '(0?[1-9]|1[0-2])', $format); 
            $regexp = str_replace('dd', '(0?[1-9]|[1-2][0-9]|3[0-1])', $regexp); 
            $regexp = str_replace('yyyy', '(19|20)?[0-9][0-9]', $regexp); 
            $regexp = str_replace($separator, "\\" . $separator, $regexp); 
            if($regexp != $value && preg_match('/'.$regexp.'\z/', $value)){ 

                // check date 
                $arr=explode($separator,$value); 
                $day=$arr[0]; 
                $month=$arr[1]; 
                $year=$arr[2]; 
                if(@checkdate($month, $day, $year)) 
                    return true; 
            } 
        } 
    } 
    return false; 
} 

//подключаем модули
CModule::IncludeModule("iblock");
CModule::IncludeModule('highloadblock');
//пользователь
global $USER;
$USER_ID=$USER->GetID();
$USER_MAIL=trim($USER->GetEmail());
$ADMINUSER = CUser::GetByID(1);
$arADMINUSER = $ADMINUSER->Fetch();
$ADMINEMAIL=trim($arADMINUSER["EMAIL"]);
if ($USER_MAIL==$ADMINEMAIL)
{
	$EMAILTO=$ADMINEMAIL.','.$arParams["EMAIL_TO"];
}
else
{
	$EMAILTO=$USER_MAIL.','.$ADMINEMAIL.','.$arParams["EMAIL_TO"];
}
// номер инфоблока формы
$FORM_IBLOCK_ID = 5;
// номер инфоблока для проверки формы
$CONDITIONS_IBLOCK_ID= 6;
//если поле заполнено то выведется ссылка на распечатку
$arResult["PRINT_ID"]='';
//масив значений ошибок
$arResult["ERROR_MESSAGE"] = array();
//для формирования порядкового номера
$NumberString='';
$arResult["PARAMS_HASH"] = md5(serialize($arParams).$this->GetTemplateName());
$arParams["EVENT_NAME_GOOD"] = "FORM2_GOOD";
$arParams["EVENT_NAME_BAD"] = "FORM2_BAD";
$arParams["EMAIL_TO"] = trim($arParams["EMAIL_TO"]);
if($arParams["EMAIL_TO"] == '')
	$arParams["EMAIL_TO"] = COption::GetOptionString("main", "email_from");
$arParams["OK_TEXT"] = trim($arParams["OK_TEXT"]);
if($arParams["OK_TEXT"] == '')
	$arParams["OK_TEXT"] = GetMessage("MF_OK_MESSAGE");

//поля которые можно сохранять в справочники при заполнении форм
$arNeedToWrite=array(
	"organization"=> array(
		"NEEDTOWRITE"=>true,
		"ID"=>5
		),
	"unloadpoint"=> array(
		"NEEDTOWRITE"=>true,
		"ID"=>6
		),
	"cityauthbody"=> array(
		"NEEDTOWRITE"=>true,
		"ID"=>7
		),
	"issued"=> array(
		"NEEDTOWRITE"=>true,
		"ID"=>10
		),
	"quantity"=> array(
		"NEEDTOWRITE"=>true,
		"ID"=>11
		),
	"productionadress"=> array(
		"NEEDTOWRITE"=>true,
		"ID"=>13
		),
	"transport"=> array(
		"NEEDTOWRITE"=>true,
		"ID"=>14
		),
	"recipientname"=> array(
		"NEEDTOWRITE"=>true,
		"ID"=>15
		),
	"specialsnotes"=> array(
		"NEEDTOWRITE"=>true,
		"ID"=>16
		),
	"officialname"=> array(
		"NEEDTOWRITE"=>true,
		"ID"=>17
		),
	"placeload"=> array(
		"NEEDTOWRITE"=>true,
		"ID"=>18
		),
	"entities"=> array(
		"NEEDTOWRITE"=>true,
		"ID"=>20
		)
);
$BoganVar=0;
// загружаем ранее созданые формы
$arSelect =array("NAME", "DATE_ACTIVE_FROM","ACTIVE", "ID","IBLOCK_ID","CREATED_USER_ID");
$arFilter = Array('IBLOCK_ID'=>$FORM_IBLOCK_ID, "CREATED_USER_ID"=>$USER_ID);
$res = CIBlockElement::GetList(Array(), $arFilter,false,false, $arSelect);?>
<?while($ob = $res->GetNextElement()){ 
	$arFields = $ob->GetFields();  
	$arPropsFirst = $ob->GetProperties();
	$arResult["PRELOAD_FORM2"][]=$arFields+$arPropsFirst;
	if($BoganVar<intval($arPropsFirst["COUNTER"]["VALUE"])) 
	{
		$BoganVar=intval($arPropsFirst["COUNTER"]["VALUE"]);
	}
}//while
  $property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$FORM_IBLOCK_ID, "CODE"=>"FOUND_FIT_FOR"));
  while($enum_fields = $property_enums->GetNext())
  {
  	$arResult["FOUNFIT_FOR_VALUES"][$enum_fields["VALUE"]]=$enum_fields;
  }
//список "Наименование уполномоченного органа" для выпадающего списка формы
$rsData1 = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Organization')));
if ($arData1 = $rsData1->fetch())
{
	$Entity1 = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData1);
	$Query1 = new \Bitrix\Main\Entity\Query($Entity1); 
	$Query1->setSelect(array('*'));
	$Query1->setOrder(array('UF_NAME' => 'ASC'));
	$result1 = $Query1->exec();
	$result1 = new CDBResult($result1);
	$arLang1 = array();
	while ($row1 = $result1->Fetch())
	{
		$arResult['nameauthbody'][] = $row1;
	}
}
//список "Город уполномоченного органа" для выпадающего списка формы
$rsData2 = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Cityauthbodyform2')));
if ($arData2 = $rsData2->fetch())
{
	$Entity2 = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData2);
	$Query2 = new \Bitrix\Main\Entity\Query($Entity2); 
	$Query2->setSelect(array('*'));
	$Query2->setFilter(array('UF_USER'=> intval($USER_ID)));
	$Query2->setOrder(array('UF_NAME' => 'ASC'));
	$result2 = $Query2->exec();
	$result2 = new CDBResult($result2);
	$arLang2 = array();
	while ($row2 = $result2->Fetch())
	{
		$arResult['cityauthbody'][] = $row2;
	}
}
//список "Должность выдавшего рег. №" для выпадающего списка формы
$rsData3 = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Posissuedregnumform2')));
if ($arData3 = $rsData3->fetch())
	{
	$Entity3 = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData3);
	$Query3 = new \Bitrix\Main\Entity\Query($Entity3); 
	$Query3->setSelect(array('*'));
	//$Query3->setFilter(array('UF_USER'=> intval($USER_ID)));
	$Query3->setOrder(array('UF_NAME' => 'ASC'));
	$result3 = $Query3->exec();
	$result3 = new CDBResult($result3);
	$arLang3 = array();
	while ($row3 = $result3->Fetch())
	{
		$arResult['posissuedregnum'][] = $row3;
	}
}
//список "ФИО  выдавшего рег. №" для выпадающего списка формы
$rsData4 = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Nameissuedregnumform2')));
if ($arData4 = $rsData4->fetch())
{
	$Entity4 = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData4);
	$Query4 = new \Bitrix\Main\Entity\Query($Entity4); 
	$Query4->setSelect(array('*'));
	//$Query4->setFilter(array('UF_USER'=> intval($USER_ID)));
	$Query4->setOrder(array('UF_NAME' => 'ASC'));
	$result4 = $Query4->exec();
	$result4 = new CDBResult($result4);
	$arLang4 = array();
	while ($row4 = $result4->Fetch())
	{
		$arResult['nameissuedregnum'][] = $row4;
	}
}
//список "Кому выдан" для выпадающего списка формы
$rsData5 = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Issuedform2')));
if ($arData5 = $rsData5->fetch())
{
	$Entity5 = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData5);
	$Query5 = new \Bitrix\Main\Entity\Query($Entity5); 
	$Query5->setSelect(array('*'));
	$Query5->setFilter(array('UF_USER'=> intval($USER_ID)));
	$Query5->setOrder(array('UF_NAME' => 'ASC'));
	$result5 = $Query5->exec();
	$result5 = new CDBResult($result5);
	$arLang5 = array();
	while ($row5 = $result5->Fetch())
	{
		$arResult['issued'][] = $row5;
	}
}
//список "Наименование предприятия изготовителя" для выпадающего списка формы
$rsData6 = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Quantityform2')));
if ($arData6 = $rsData6->fetch())
{
	$Entity6 = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData6);
	$Query6 = new \Bitrix\Main\Entity\Query($Entity6); 
	$Query6->setSelect(array('*'));
	$Query6->setFilter(array('UF_USER'=> intval($USER_ID)));
	$Query6->setOrder(array('UF_NAME' => 'ASC'));
	$result6 = $Query6->exec();
	$result6 = new CDBResult($result6);
	$arLang6 = array();
	while ($row6 = $result6->Fetch())
	{
		$arResult['quantity'][] = $row6;
	}
}
//список "Наименование предприятия изготовителя" для выпадающего списка формы
$rsData7 = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Manufactnameform2')));
if ($arData7= $rsData7->fetch())
{
	$Entity7 = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData7);
	$Query7 = new \Bitrix\Main\Entity\Query($Entity7); 
	$Query7->setSelect(array('*'));
	//$Query7->setFilter(array('UF_USER'=> intval($USER_ID)));
	$Query7->setOrder(array('UF_NAME' => 'ASC'));
	$result7 = $Query7->exec();
	$result7 = new CDBResult($result7);
	$arLang7 = array();
	while ($row7 = $result7->Fetch())
	{
		$arResult['manufactname'][] = $row7;
	}
}

//список "Адрес и место нахождения продукции" для выпадающего списка формы
$rsData8 = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Productionadressform2')));
if ($arData8 = $rsData8->fetch())
{
	$Entity8 = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData8);
	$Query8 = new \Bitrix\Main\Entity\Query($Entity8); 
	$Query8->setSelect(array('*'));
	$Query8->setFilter(array('UF_USER'=> intval($USER_ID)));
	$Query8->setOrder(array('UF_NAME' => 'ASC'));
	$result8 = $Query8->exec();
	$result8 = new CDBResult($result8);
	$arLang8 = array();
	while ($row8 = $result8->Fetch())
	{
		$arResult['productionadress'][] = $row8;
	}
}

//список "Вид транспорта, маршрут следования, условия перевозки и адрес получателя, а также наименование, номер и дата выдачи товаротранспортного документа" для выпадающего списка формы
$rsData9 = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Transportform2')));
if ($arData9 = $rsData9->fetch())
{
	$Entity9 = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData9);
	$Query9 = new \Bitrix\Main\Entity\Query($Entity9); 
	$Query9->setSelect(array('*'));
	$Query9->setFilter(array('UF_USER'=> intval($USER_ID)));
	$Query9->setOrder(array('UF_NAME' => 'ASC'));
	$result9 = $Query9->exec();
	$result9 = new CDBResult($result9);
	$arLang9 = array();
	while ($row9 = $result9->Fetch())
	{
		$arResult['transport'][] = $row9;
	}
}
//список "Наименование и адрес получателя" для выпадающего списка формы
$rsData10 = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Recipientnameform2')));
if ($arData10 = $rsData10->fetch())
{
	$Entity10 = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData10);
	$Query10 = new \Bitrix\Main\Entity\Query($Entity10); 
	$Query10->setSelect(array('*'));
	$Query10->setFilter(array('UF_USER'=> intval($USER_ID)));
	$Query10->setOrder(array('UF_NAME' => 'ASC'));
	$result10 = $Query10->exec();
	$result10 = new CDBResult($result10);
	$arLang10 = array();
	while ($row10= $result10->Fetch())
	{
		$arResult['recipientname'][] = $row10;
	}
}
//список "Особые отметки" для выпадающего списка формы
$rsData11 = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Specialsnotesform2')));
if ($arData11 = $rsData11->fetch())
{
	$Entity11 = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData11);
	$Query11 = new \Bitrix\Main\Entity\Query($Entity11); 
	$Query11->setSelect(array('*'));
	$Query11->setFilter(array('UF_USER'=> intval($USER_ID)));
	$Query11->setOrder(array('UF_NAME' => 'ASC'));
	$result11 = $Query11->exec();
	$result11 = new CDBResult($result11);
	$arLang11 = array();
	while ($row11 = $result11->Fetch())
	{
		$arResult['specialsnotes'][] = $row11;
	}
}
//список "ФИО должностного лица" для выпадающего списка формы
$rsData12 = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Officialnameform2')));
if ($arData12 = $rsData12->fetch())
{
	$Entity12 = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData12);
	$Query12 = new \Bitrix\Main\Entity\Query($Entity12); 
	$Query12->setSelect(array('*'));
	$Query12->setFilter(array('UF_USER'=> intval($USER_ID)));
	$Query12->setOrder(array('UF_NAME' => 'ASC'));
	$result12 = $Query12->exec();
	$result12 = new CDBResult($result12);
	$arLang12 = array();
	while ($row12= $result12->Fetch())
	{
		$arResult['officialname'][] = $row12;
	}
}
//список "Пункт погрузки" для выпадающего списка формы
$rsData13 = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Placeloadform2')));
if ($arData13 = $rsData13->fetch())
{
	$Entity13 = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData13);
	$Query13 = new \Bitrix\Main\Entity\Query($Entity13); 
	$Query13->setSelect(array('*'));
	$Query13->setFilter(array('UF_USER'=> intval($USER_ID)));
	$Query13->setOrder(array('UF_NAME' => 'ASC'));
	$result13 = $Query13->exec();
	$result13 = new CDBResult($result13);
	$arLang13 = array();
	while ($row13 = $result13->Fetch())
	{
		$arResult['placeload'][] = $row13;
	}
}
$rsData14 = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Unloadpointform2')));
if ($arData14 = $rsData14->fetch())
{
	$Entity14 = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData14);
	$Query14 = new \Bitrix\Main\Entity\Query($Entity14); 
	$Query14->setSelect(array('*'));
	$Query14->setFilter(array('UF_USER'=> intval($USER_ID)));
	$Query14->setOrder(array('UF_NAME' => 'ASC'));
	$result14 = $Query14->exec();
	$result14 = new CDBResult($result14);
	$arLang14 = array();
	while ($row14 = $result14->Fetch())
	{
		$arResult['unloadpoint'][] = $row14;
	}
}
$rsData15 = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>'Entitiesform2')));
if ($arData15 = $rsData15->fetch())
{
	$Entity15 = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData15);
	$Query15 = new \Bitrix\Main\Entity\Query($Entity15); 
	$Query15->setSelect(array('*'));
	$Query15->setFilter(array('UF_USER'=> intval($USER_ID)));
	$Query15->setOrder(array('UF_NAME' => 'ASC'));
	$result15 = $Query15->exec();
	$result15 = new CDBResult($result15);
	$arLang15 = array();
	while ($row15 = $result15->Fetch())
	{
		$arResult['entities'][] = $row15;
	}
}
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


if(($_SERVER["REQUEST_METHOD"] == "GET") && (strlen($_GET["ownproduct"])>0))
{
	$arParams["YESNO"]=$_GET["ownproduct"];
}
if(($_SERVER["REQUEST_METHOD"] == "GET") && (strlen($_GET["form_2_ID"])>0))
{
	$arSelect =array("NAME", "DATE_ACTIVE_FROM","ACTIVE", "ID","IBLOCK_ID","CREATED_USER_ID");
	$arFilter = Array('IBLOCK_ID'=>$FORM_IBLOCK_ID, "ID"=>$_GET["form_2_ID"],"CREATED_USER_ID"=>$USER_ID);
	$res = CIBlockElement::GetList(Array(), $arFilter,false,false, $arSelect);
	while($ob = $res->GetNextElement())
		{
			$arFields = $ob->GetFields();  
			$arResult["FORM2"]["FIELDS"]=$arFields;
			$arPropsFirst = $ob->GetProperties();
			$arResult["FORM2"]["PROPS"]=$arPropsFirst;
		}//while
	//передаем значения на вывод
	$arResult["NAMEAUTHBODY"] = $arResult["FORM2"]["PROPS"]["NAME_AUTH_BODY"]["VALUE"];
	$arResult["CITYAUTHBODY"] = $arResult["FORM2"]["PROPS"]["CITY_AUTH_BODY"]["VALUE"];
	$arResult["REGNUM"] = $arResult["FORM2"]["PROPS"]["REG_NUM"]["VALUE"];
	$arResult["POSISSUEDREGNUM"] = $arResult["FORM2"]["PROPS"]["POS_ISSUED_REG_NUM"]["VALUE"];
	$arResult["NAMEISSUEDREGNUM"] = $arResult["FORM2"]["PROPS"]["NAME_ISSUED_REG_NUM"]["VALUE"];
	$arResult["FORMNUM"] = $arResult["FORM2"]["PROPS"]["FORM_NUM"]["VALUE"];
	$arResult["DATEISSUECERT"] = $arResult["FORM2"]["PROPS"]["DATE_ISSUE_CERT"]["VALUE"];
	$arResult["ISSUED"] = $arResult["FORM2"]["PROPS"]["ISSUED"]["VALUE"];
	$arResult["PRODUCTNAME"] = $arResult["FORM2"]["PROPS"]["PRODUCT_NAME"]["VALUE"];
	$arResult["TRANSPORT"] = $arResult["FORM2"]["PROPS"]["TRANSPORT"]["VALUE"];
	$arResult["MANUFACTNAME"] = $arResult["FORM2"]["PROPS"]["MANUFACT_NAME"]["VALUE"];
	foreach ($arResult["FORM2"]["PROPS"]["PRODUCTION_DATE"]["VALUE"] as $key => $value) 
	{
		$arResult["PRODUCTIONDATE"] .= $value.", ";
	}
	$arResult["FOUNDFITFOR"] = $arResult["FORM2"]["PROPS"]["FOUND_FIT_FOR"]["VALUE"];
	$arResult["PRODUCTIONADRESS"] = $arResult["FORM2"]["PROPS"]["PRODUCTION_ADRESS"]["VALUE"];
	$arResult["QUANTITY"] = $arResult["FORM2"]["PROPS"]["QUANTITY"]["VALUE"];
	$arResult["TTD"] = $arResult["FORM2"]["PROPS"]["TTD"]["VALUE"];
	$arResult["RECIPIENTNAME"] = $arResult["FORM2"]["PROPS"]["RECIPIENT_NAME"]["VALUE"];
	$arResult["LABNAME"] = $arResult["FORM2"]["PROPS"]["LAB_NAME"]["VALUE"];
	$arResult["SPECIALSNOTES"] = $arResult["FORM2"]["PROPS"]["SPECIALS_NOTES"]["VALUE"];
	$arResult["JOBNAME"] = $arResult["FORM2"]["PROPS"]["JOB_NAME"]["VALUE"];
	$arResult["OFFICIALNAME"] = $arResult["FORM2"]["PROPS"]["OFFICIAL_NAME"]["VALUE"];
	$arResult["PLACELOAD"] = $arResult["FORM2"]["PROPS"]["PLACE_LOAD"]["VALUE"];
	$arResult["REASONS"] = $arResult["FORM2"]["PROPS"]["REASONS"]["VALUE"];
	$arResult["UNLOADPOINT"] = $arResult["FORM2"]["PROPS"]["UNLOADPOINT"]["VALUE"];
	$arResult["ENTITIES"] = $arResult["FORM2"]["PROPS"]["ENTITIES"]["VALUE"];
	$arResult["AUTHOR"] = $arResult["FORM2"]["PROPS"]["AUTHOR"]["VALUE"];
	foreach ($arResult['COUNTRIES'] as $key => $value) 
	{
		if($value["UF_XML_ID"]==$arResult["FORM2"]["PROPS"]["COUNTRY_ORIGIN"]["VALUE"])
		{
			$arResult["COUNTRY"]=$value["UF_NAME"];
		}
		if($value["UF_XML_ID"]==$arResult["FORM2"]["PROPS"]["COUNTRY_DEST"]["VALUE"])
		{
			$arResult["COUNTRY2"]=$value["UF_NAME"];
		}
	}
	$arResult["DEL"] = $arResult["FORM2"]["PROPS"]["DEL"]["VALUE"];
	$arResult["DEL_P"] = $arResult["FORM2"]["PROPS"]["DEL_P"]["VALUE"];
	$arResult["AUTHOR_ANNUL"] = $arResult["FORM2"]["PROPS"]["AUTHOR_ANNUL"]["VALUE"];
	$input_line=$arResult["REGNUM"];
	preg_match("/№\s11-3\/\d{2,5}\sот\s\d{2}[.]\d{2}[.]\d{4}\sг./", $input_line, $output_array);
	if (empty($output_array))
	{
		$arParams["YESNO"]="Y";
	}
	else
	{
		$arParams["YESNO"]="N";
	}
}
if (strlen($arParams["YESNO"])<=0)
{
	$arParams["YESNO"]="Y";
}
//нужно для определения организации
	$rsUser = CUser::GetList(($by="ID"), ($order="desc"), array("ID"=>$USER_ID),array("SELECT"=>array("UF_*")));
	$arUser = $rsUser->Fetch();
$BoganCode='';
foreach ($arResult['nameauthbody'] as $key => $value) 
{
	if ($arParams["YESNO"]=="Y")
	{
				if($value["ID"]==$arUser["UF_ORG"])
				{
					$arResult["USER_UF_ID"]=$value["ID"];
					$arResult["USER_UF_ORG_CODE"]= $value["UF_CODE"];
					$arResult["USER_UF_ORG_NAME"]= $value["UF_NAME"];
					$arResult["USER_UF_ORG_XML_ID"] = $value["UF_XML_ID"];
					$arResult["USER_UF_MAXNUMBER"] = $value["UF_COUNTER_FORM2"]+1;
					$arResult["USER_UF_WRITEMAXNUMBER"] = $arResult["USER_UF_MAXNUMBER"];
					$arResult["USER_UF_YEARNUMBER"] = $value["UF_YEAR_FORM2"];
					$arResult['NAMEAUTHBODY']=$arResult["USER_UF_ORG_NAME"];
					break;
				}
	}
	else
	{
			if($_POST["nameauthbody"]==$value["UF_NAME"])
			{
					$arResult["USER_UF_ID"]=$value["ID"];
					$arResult["USER_UF_ORG_CODE"]= $value["UF_CODE"];
					$arResult["USER_UF_ORG_NAME"]= $value["UF_NAME"];
					$arResult["USER_UF_ORG_XML_ID"] = $value["UF_XML_ID"];
					$arResult["USER_UF_MAXNUMBER"] = $value["UF_COUNTER_FORM2"]+1;
					$arResult["USER_UF_WRITEMAXNUMBER"] = $arResult["USER_UF_MAXNUMBER"];
					$arResult["USER_UF_YEARNUMBER"] = $value["UF_YEAR_FORM2"];
					break;
			}
	}
}
function normalizenimber($var)
{
	switch (strlen($var)) 
	{
		case 0:
			$var =  "0000".$var;
			break;
		case 1:
			$var =  "000".$var;
			break;
		case 2:
			$var =  "00".$var;
			break;
		case 3:
			$var =  "0".$var;
			break;
	} //switch (strlen($arResult["USER_UF_MAXNUMBER"])) 
	return $var;
}
// fake reg num
if ($arParams["YESNO"]=="Y")
{
	$arResult['REGNUM']=$arResult["USER_UF_ORG_CODE"]."-".normalizenimber($BoganVar);
}
//обработка формы
if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["submit"] <> '' && (!isset($_POST["PARAMS_HASH"]) || $arResult["PARAMS_HASH"] === $_POST["PARAMS_HASH"]))
{
	if(check_bitrix_sessid())//если сесия
	{
		//проверка ошибок
		if ($arParams["YESNO"]=="N")
		{
			if(strlen($_POST["nameauthbody"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_NAMEAUTHBODY");
		}
		if(strlen($_POST["cityauthbody"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_CITYAUTHBODY");
		/*			Если ответ «ДА», то номер формируется автоматически, формат как в форме 4: ByпробелNпробел05пробел(номер района 2цифры)пробел(номер организации2цифры)пробел(номер разрешения 4цифры, с нового года должен обнуляться)
		Если ответ «НЕТ», то номер вноситься вручную: формат № 11-3/389 от 23.02.2016 г.(«№пробел11-3/»-константа; «00389»-ручной ввод, число, 5 цифр максимум, обнуляется с нового года; «пробелОТпробел»-константа, от строчными буквами; «23.02.2016»-дата выдачи сертификата, по умолчанию текущая дата с возможностью корректировки; «пробелГ.»-константа, г.строчными буквами.*/
		if($arParams["YESNO"]=="N")
		{	 /*№_11-3/000000_от_23.02.2016_г.*/
			if(strlen($_POST["regnum"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_REGNUM");
			$input_line=$_POST["regnum"];
			preg_match("/№\s11-3\/\d{2,5}\sот\s\d{2}[.]\d{2}[.]\d{4}\sг./", $input_line, $output_array);
			$temp=explode(" ", $output_array[0]);
			if (!is_valid_date($temp[3]))
			{
				$arResult["ERROR_MESSAGE"][] = "В поле регистрационный номер некорректная дата";
			}
			if (empty($output_array))
				$arResult["ERROR_MESSAGE"][] = GetMessage("MF_REGNUM");
		}
		if(strlen($_POST["posissuedregnum"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_POSISSUEDREGNUM");
		if(strlen($_POST["nameissuedregnum"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_NAMEISSUEDREGNUM");
		if(strlen($_POST["formnum"])<=0) 
		{
			$arResult["ERROR_MESSAGE"][] = GetMessage("MF_FORMNUM");
		}
		else
		{
			$input_line=$_POST["formnum"];
			preg_match("/BY\s№\s\d{2}\s\d{8}/", $input_line, $output_array);
				if (empty($output_array))
				{
					$arResult["ERROR_MESSAGE"][] = GetMessage("MF_FORMNUM");
				}
		}
		if(strlen($_POST["dateissuecert"])<=0) 
		{
			$arResult["ERROR_MESSAGE"][] = GetMessage("MF_DATEISSUECERT");
		}
		else
		{
			$input_lines=$_POST["dateissuecert"];
			$input_lines = str_replace(",", ".", $input_lines);
			$input_lines = str_replace("/", ".", $input_lines);
			preg_match("/\d{2}[.]\d{2}[.]\d{4}/", $input_lines, $output_array);
			if (!empty($output_array))
				{
					if (is_valid_date($output_array[0]))
					{
						if ($stmp = MakeTimeStamp($output_array[0], "DD.MM.YYYY"))
						{
							$dateissuecert=ConvertTimeStamp($stmp, "SHORT");
						}
					}
					else 
					{
						$arResult["ERROR_MESSAGE"][] = GetMessage("MF_DATEISSUECERT_VRONG_DATE").$output_array[0];
					}
				}
				/*if($arParams["YESNO"]=="N")
					{	 //№_11-3/000000_от_23.02.2016_г.
						$input_line=$_POST["regnum"];
						preg_match("\s\d{2}[.]\d{2}[.]\d{4}\s/", $input_line, $output_array);
						if (empty($output_array)||($output_array[0]!=$arResultDateMas[0]))
							$arResult["ERROR_MESSAGE"][] = GetMessage("MF_REGNUM");
					}*/
		}
		/*10.В количестве(мест, штук, кг упаковка маркировка) - формат(«местПробел»-константа,5цифр,целое число; « пробелштукпробел» - константа, 6 цифр, целое; «пробелвеспробел» - константа, 6цифр целых и 3 после запятой; упаковка – выбор из справочника с возможностью сохранения; маркировка – ручной ввод.*/
		/*'мест_00000_штук_000000_вес_000000,000,упаковка,маркировка'		*/

		//достаем значения
		if(strlen($_POST["quantity"])<=0) 
		{
			$arResult["ERROR_MESSAGE"][] = GetMessage("MF_QUANTITY");
		}
		else
		{
			$input_line=$_POST["quantity"];
			preg_match("/мест\s\d{1,5}\s/", $input_line, $arQuantityPlaces);
			preg_match("/\sштук\s\d{1,6}/", $input_line, $arQuantityItems);
			preg_match("/\sвес\s\d{1,6}[.]\d{3}/", $input_line, $arQuantityWeight);
			$arQuantityREST=explode(",", $input_line);
			$upakovka=$arQuantityREST[1];
			$markirovka=$arQuantityREST[2];
			if(strlen($upakovka)<=0||strlen($markirovka)<=0)
			{
				$arResult["ERROR_MESSAGE"][] = GetMessage("MF_QUANTITY");
			}
			else
			{
				$temp=explode(" ", $arQuantityPlaces[0]);
				$arQantity["QUANTITY"]["PLACES"]=$temp[1];
				$temp=explode(" ", $arQuantityItems[0]);
				$arQantity["QUANTITY"]["ITEMS"]=$temp[2];
				$temp=explode(" ", $arQuantityWeight[0]);
				$arQantity["QUANTITY"]["WEIGHT"]=$temp[2];
				if(is_numeric($arQantity["QUANTITY"]["PLACES"])
						&&is_numeric($arQantity["QUANTITY"]["ITEMS"])
						&&is_numeric($arQantity["QUANTITY"]["WEIGHT"])
					)
				{
					if(intval($arQantity["QUANTITY"]["PLACES"])<=0
						||(intval($arQantity["QUANTITY"]["ITEMS"])<=0)
						||(floatval($arQantity["QUANTITY"]["WEIGHT"])<=0)
						)
						{
							$arResult["ERROR_MESSAGE"][] = GetMessage("MF_QUANTITY");
						}
				}
				else
				{
					$arResult["ERROR_MESSAGE"][] = GetMessage("MF_QUANTITY");
				}
			}
		}
		/*ввод(может быть как одна дата, так и несколько через тире или запятую.*/
		if(strlen($_POST["productiondate"])<=0) 
		{
			$arResult["ERROR_MESSAGE"][] = GetMessage("MF_PRODUCTIONDATE");
		}
		else
		{
			$input_lines=$_POST["productiondate"];
			$input_lines = str_replace(",", ".", $input_lines);
			$input_lines = str_replace("/", ".", $input_lines);
			preg_match_all("/\d{2}[.]\d{2}[.]\d{4}/", $input_lines, $output_array);
			if (!empty($output_array))
				{
					foreach ($output_array[0] as $key => $value) 
					{	
						if (is_valid_date($value))
						{
							if ($stmp = MakeTimeStamp($value, "DD.MM.YYYY"))
							{
								$arResultDateMas[]=ConvertTimeStamp($stmp, "SHORT");
							}
							else 
							{
								$arResult["ERROR_MESSAGE"][] = GetMessage("MF_PRODUCTIONDATE_VRONG_DATE")." : ".$stmp;
							}
						}
						else 
						{
							$arResult["ERROR_MESSAGE"][] = GetMessage("MF_PRODUCTIONDATE_VRONG_DATE")." : ".$value;
						}
					}
				}
			if (empty($arResultDateMas))
			$arResult["ERROR_MESSAGE"][] = GetMessage("MF_PRODUCTIONDATE");
		}
		if(strlen($_POST["foundfitfor"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_FOUNDFITFOR");
		switch ($_POST["foundfitfor"]) 
		{
			case "WITHOUT_LIMITS":
				$foundfitforId=2;
				break;
			case "WITH_LIMITS":
				$foundfitforId=3;
				break;
			case "WITH_RULES":
				$foundfitforId=4;
				break;
			default:
				$arResult["ERROR_MESSAGE"][] = GetMessage("MF_FOUNDFITFOR");
				break;
		}
		/*Реализация без ограничений.
		Реализация с ограничениями.
		Переработка согласно правилам ветсанэкспертизы.
		Если пункт 2,  то заполняем поле 13а(причины)-ручной ввод.*/
		if(($_POST["foundfitfor"]=="WITH_LIMITS")&&strlen($_POST["reasons"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_REASONS");
		if(strlen($_POST["officialname"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_OFFICIALNAME");
		if(strlen($_POST["jobname"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_JOBNAME");
		/*19.Полное наименование должности – заполняется автоматически при выборе п.20
		20.Фамилия и инициалы должностного лица – из справочника с возможностью сохранения и выбора из справочника.*/
		if(strlen($_POST["issued"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_ISSUED");
		if(strlen($_POST["productname"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_PRODUCTNAME");
		if(strlen($_POST["transport"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_TRANSPORT");
		if(strlen($_POST["productionadress"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_PRODUCTIONADRESS");
		if(strlen($_POST["manufactname"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_MANUFACTNAME");
		if(strlen($_POST["ttd"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_TTD");
		if(strlen($_POST["recipientname"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_RECIPIENTNAME");
		if(strlen($_POST["labname"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_LABNAME");
		if(strlen($_POST["specialsnotes"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_SPECIALSNOTES");
		if(strlen($_POST["placeload"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_PLACELOAD");
		if(strlen($_POST["unloadpoint"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_UNLOADPOINT");
		if(strlen($_POST["entities"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_ENTITIES");
		//проверка стран
		if(strlen($_POST["country"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_COUNTRY");
		else 
		{
			$Co_OK = false;
			foreach($arResult['COUNTRIES'] as $country)
			{
				if($country["UF_NAME"]==$_POST["country"]) 
				{
					$Co_OK = 1;
					$arResult["COUNTRY_ORIGIN_UF_XML_ID"]=$country["UF_XML_ID"];
					break;
				}
			}
			if(!$Co_OK) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_COUNTRY1");
		}
		if(strlen($_POST["country2"])<=0) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_COUNTRY2");
		else 
		{
			$Co_OK2 = false;
			foreach($arResult['COUNTRIES'] as $country2)
			{
				if($country2["UF_NAME"]==$_POST["country2"]) 
				{
					$Co_OK2 = 1;
					$arResult["COUNTRY_DEST_UF_XML_ID"]=$country2["UF_XML_ID"];
					break;
				}
			}
			if(!$Co_OK2) $arResult["ERROR_MESSAGE"][] = GetMessage("MF_COUNTRY21");
		}
/*---------------------------------------------------------------------------------------------------------*/
//если нет ошибок		

		if(empty($arResult["ERROR_MESSAGE"]))
		{	
				//определяем максимальное значение номера записи формы
				$HBLOCK_NAME16 = 'Counterform2';
				$rsData16 = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>$HBLOCK_NAME16)));
				if ($arData16 = $rsData16->fetch())
				{
					$Entity16 = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData16);
					$Query16 = new \Bitrix\Main\Entity\Query($Entity16);
					$Query16->setSelect(array("UF_COUNTER"));
					$Query16->setFilter(array("ID"=> 1));
					$result16 = $Query16->exec();
					$result16 = new CDBResult($result16);
					if ($row16 = $result16->Fetch())
					{
						$arResult["USER_UF_MAXCOUNTER"] = $row16["UF_COUNTER"]+1;
					}
				}
			if($arParams['YESNO']=="Y"):
				//выбираем информацию о компании по привязке к пользователю
						//подгоняем вид значение номера под формат
						normalizenimber($arResult["USER_UF_MAXNUMBER"]);
				//выбираем все элементы разрешений для сравнения
					//проверяем на разрешения
				$resConditions = CIBlockElement::GetList(Array("ID"=>"DESC"), Array("IBLOCK_ID"=>$CONDITIONS_IBLOCK_ID, "ACTIVE"=>"Y"), false, false, Array("ID", "ACTIVE_FROM", "ACTIVE_TO", "PROPERTY_*"));
				$allow_number=true;
				while($obConditions = $resConditions->GetNext())
				{	
					$an67=true;
					$an68=true;
					$an69=true;
					$an70=true;
					$an71=true;
					$an72=true;
					$allow_number_date=true;
					if (count($obConditions["PROPERTY_67"])>0)
					{
						if(in_array($arResult["USER_UF_ORG_XML_ID"],$obConditions["PROPERTY_67"])) 
						{
							$an67 = false;
							// echo "PROPERTY_67";
						}
					}
					else
					{
						$an67 = false;
					}
					if(strlen($obConditions["PROPERTY_68"])>0)
					{
						if($_POST["productname"]==$obConditions["PROPERTY_68"]) 
						{
							$an68 = false;
							// echo "PROPERTY_68";
						}
					}
					else
					{
						$an68 = false;
					}
					if(strlen($obConditions["PROPERTY_69"])>0)
					{
						if($_POST["productname"]==$obConditions["PROPERTY_69"]) 
						{
							$an69 = false;
							// echo "PROPERTY_69";
						}
					}
					else
					{
						$an69 = false;
					}
					if (count($obConditions["PROPERTY_70"])>0)
					{
						if(in_array($arResult["COUNTRY_ORIGIN_UF_XML_ID"],$obConditions["PROPERTY_70"])) 
						{
							$an70 = false;
							// echo "PROPERTY_70";
						}
					}
					else
					{
						$an70 = false;
					}
					if (count($obConditions["PROPERTY_71"])>0)
					{
						if(in_array($arResult["COUNTRY_DEST_UF_XML_ID"],$obConditions["PROPERTY_71"])) 
						{
							$an71 = false;
							// echo "PROPERTY_71";
						}
					}
					else
					{
						$an71 = false;
					}
					if(strlen($obConditions["PROPERTY_72"])>0)
					{
						if($_POST["productname"]==$obConditions["PROPERTY_72"]) 
						{
							$an72 = false;
							// echo "PROPERTY_72";
						}
					}
					else
					{
						$an72 = false;
					}
					if(strlen($obConditions["ACTIVE_FROM"])>0 && strlen($obConditions["ACTIVE_TO"])>0)
					{
						$arr = ParseDateTime($obConditions["ACTIVE_FROM"]);
						$arr1 = ParseDateTime($obConditions["ACTIVE_TO"]);
						if((time()>mktime(0, 0, 0, $arr["MM"], $arr["DD"], $arr["YYYY"])) 
							|| (time()<mktime(23, 59, 59, $arr1["MM"], $arr1["DD"], $arr1["YYYY"]))) 
							$allow_number_date = false;
						// echo "false from to date";
					}
					if(!$an67 && !$an68 && !$an69 && !$an70 && !$an71 && !$an72 && !$allow_number_date) {
						$allow_number = false;
						break;
					}
				}//while($obConditions = $resConditions->GetNext())
				//формируем окончательный номер разрешения если еще не сформирован
				if($allow_number)
				{
					if(strlen($NumberString)<=0)
					{
						$NumberString = $arResult["USER_UF_ORG_CODE"].'-'.$arResult["USER_UF_MAXNUMBER"];
					}
					$ACTIVE = "Y";
				}
				else
				{
					$NumberString = '';
						$ACTIVE = "N";
				}
			endif;//yesno=y
			// if($allow_number||$arParams["YESNO"]=="N")
			// {
				foreach ($arNeedToWrite as $key => $value) //проверяем надо ли добавлять в справочники
				{
					foreach ($arResult[$key] as $key1 => $value2) 
					{	
						if($value2["UF_NAME"]==$_POST[$key]&&$value2["UF_USER"]==$USER_ID)
						{
							$arNeedToWrite[$key]["NEEDTOWRITE"]=false;
						}
					}
				}
				//Теперь запишем все значения в справочники
				foreach ($arNeedToWrite as $key => $value) 
				{
					if ($value["NEEDTOWRITE"]==true)
					{		//Подготовка:
						$arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById($value["ID"])->fetch();
						$obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
						$strEntityDataClass = $obEntity->getDataClass();
								//Добавление:
						if("cityauthbody"==$key||"issued"==$key||"quantity"==$key||"productionadress"==$key||"transport"==$key||"recipientname"==$key||"specialsnotes"==$key||"entities"==$key||"placeload"==$key||"unloadpoint"==$key)
						{
							$arElementFields = array
							(
								"UF_NAME" => $_POST[$key],
								"UF_XML_ID"=>md5($_POST[$key]),
								"UF_USER"=>intval($USER_ID),
							);
						}//if
						elseif ("officialname"==$key) 
						{
							$arElementFields = array
							(
								"UF_NAME" => $_POST[$key],
								"UF_XML_ID"=>md5($_POST[$key]),
								"UF_USER"=>intval($USER_ID),
								"UF_JOB_NAME"=> $_POST["jobname"],
							);
						}
						else
						{
							$arElementFields = array
							(
								"UF_NAME" => $_POST[$key],
								"UF_XML_ID"=>md5($_POST[$key])
							);
						}//else
						$obResult = $strEntityDataClass::add($arElementFields);
						$ID = $obResult->getID();
						$bSuccess = $obResult->isSuccess();
						if(!$bSuccess)
						{
							echo implode(', ', $result->getErrorMessages()); //выведем ошибки
						}//if
					}	//если добавление успешно можно вывести сообщение
				}//foreach ($arNeedToWrite as $key => $value) 
				if ($arParams["YESNO"]=="N")
				{
					$ACTIVE=true;
					$NumberString=$_POST["regnum"];
				}
				$el = new CIBlockElement;//формируем елемент к записи
				$PROP = array();
				$PROP["COUNTER"] = $arResult["USER_UF_MAXCOUNTER"];
				$PROP["NAME_AUTH_BODY"] = $_POST["nameauthbody"];
				$PROP["CITY_AUTH_BODY"] = $_POST["cityauthbody"];
				$PROP["REG_NUM"] = $NumberString;
				$PROP["POS_ISSUED_REG_NUM"] = $_POST["posissuedregnum"];
				$PROP["NAME_ISSUED_REG_NUM"] = $_POST["nameissuedregnum"];
				$PROP["FORM_NUM"] = $_POST["formnum"];
				$PROP["DATE_ISSUE_CERT"] = $dateissuecert;
				$PROP["ISSUED"] = $_POST["issued"];
				$PROP["PRODUCT_NAME"] = $_POST["productname"];
				$PROP["TRANSPORT"] = $_POST["transport"];
				$PROP["PRODUCTION_ADRESS"] = $_POST["productionadress"];
				$PROP["FOUND_FIT_FOR"] = array("VALUE" =>$foundfitforId);
				$PROP["PRODUCTION_DATE"] = $arResultDateMas;
				$PROP["MANUFACT_NAME"] = $_POST["manufactname"];
				$PROP["QUANTITY"] = $_POST["quantity"];
				$PROP["TTD"] = $_POST["ttd"];
				$PROP["RECIPIENT_NAME"] = $_POST["recipientname"];
				$PROP["LAB_NAME"] = $_POST["labname"];
				$PROP["SPECIALS_NOTES"] = $_POST["specialsnotes"];
				$PROP["JOB_NAME"] = $_POST["jobname"];
				$PROP["OFFICIAL_NAME"] = $_POST["officialname"];
				$PROP["PLACE_LOAD"] = $_POST["placeload"];
				$PROP["UNLOADPOINT"] = $_POST["unloadpoint"];
				$PROP["ENTITIES"] = $_POST["entities"];
				$PROP["REASONS"] = $_POST["reasons"];
				$PROP["COUNTRY_ORIGIN"]=$arResult["COUNTRY_DEST_UF_XML_ID"];
				$PROP["COUNTRY_DEST"]=$arResult["COUNTRY_ORIGIN_UF_XML_ID"];
				$arLoadProductArray = Array
				(
					"MODIFIED_BY"    => $USER_ID,
					"NAME"           => $arResult["USER_UF_ORG_NAME"],
					"IBLOCK_ID"      => $FORM_IBLOCK_ID,
					"PROPERTY_VALUES"=> $PROP,
					"ACTIVE"         => $ACTIVE,  
					"DATE_ACTIVE_FROM" => ConvertTimeStamp(time(), "SHORT")
				);
				//пишем последний номер и год его записи в хайлоад организаций	
				$DataClassOrganization = $Entity1->getDataClass();
				if($arResult["USER_UF_YEARNUMBER"]==date('Y'))
				{	
					$arBxDataOrganization = array('UF_COUNTER_FORM2' => $arResult["USER_UF_WRITEMAXNUMBER"],'UF_YEAR_FORM2' => $arResult["USER_UF_YEARNUMBER"],);
				}
				else
				{
					$arBxDataOrganization = array('UF_COUNTER_FORM2' => 1,'UF_YEAR_FORM2' => date('Y'),);
				}
				$resultOrganization = $DataClassOrganization::update($arResult["USER_UF_ID"], $arBxDataOrganization);
			//пишем номер счетчика записи
				$HBLOCK_NAME17 = 'Counterform2';
				$rsData17 = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>$HBLOCK_NAME17)));
				if ($arData17 = $rsData17->fetch())
				{
					$Entity17 = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData17);
				}
				$DataClass = $Entity17->getDataClass();	
				$arBxData = array('UF_COUNTER' => $arResult["USER_UF_MAXCOUNTER"]);
				$resultcounter = $DataClass::update(1, $arBxData);

				/*------------------------------------------------------------*/
				if($PRODUCT_ID = $el->Add($arLoadProductArray))//если запись прошла успешно
				/*------------------------------------------------------------*/
				{
					$arResult["PRINT_ID"]=$PRODUCT_ID;
					//после добавления новой формы выводить сообщение?
					$arResult["OK_MESSAGE"] = GetMessage("MF_OF_MESS_2");
					$arResult["OK_MESSAGE"] .= $NumberString;
					//отправляем письма
					//выбираем пользователей - юзер + админ
					$rsCurUser=$USER->GetEmail();
					$rsUser = CUser::GetByID(1);
					$arAdminUser = $rsUser->Fetch();
					$LetterFields = Array
					(
						"ORG" => $arResult["USER_UF_ORG_NAME"],
						"DATE" => ConvertTimeStamp(time(), "SHORT"),
						"NUMBER" => $NumberString,
						"NAME_AUTH_BODY" => $_POST["nameauthbody"],
						"CITY_AUTH_BODY"=> $_POST["cityauthbody"],
						"REG_NUM"=> $NumberString,
						"POS_ISSUED_REG_NUM"=> $_POST["posissuedregnum"],
						"NAME_ISSUED_REG_NUM"=> $_POST["nameissuedregnum"],
						"FORM_NUM"=> $_POST["formnum"],
						"DATE_ISSUE_CERT"=> $dateissuecert,
						"ISSUED"=> $_POST["issued"],
						"PRODUCT_NAME"=> $_POST["productname"],
						"TRANSPORT"=> $_POST["transport"],
						"PRODUCTION_ADRESS"=> $_POST["productionadress"],
						"FOUND_FIT_FOR"=> $arResult["FOUNFIT_FOR_VALUES"][$_POST["foundfitfor"]]["PROPERTY_NAME"],
						"PRODUCTION_DATE"=> $_POST["productiondate"],
						"MANUFACT_NAME"=> $_POST["manufactname"],
						"QUANTITY"=> $_POST["quantity"],
						"TTD"=> $_POST["ttd"],
						"RECIPIENT_NAME"=> $_POST["recipientname"],
						"LAB_NAME"=> $_POST["labname"],
						"SPECIALS_NOTES"=> $_POST["specialsnotes"],
						"JOB_NAME"=> $_POST["jobname"],
						"OFFICIAL_NAME"=> $_POST["officialname"],
						"PLACE_LOAD"=> $_POST["placeload"],
						"REASONS"=> $_POST["reasons"],
						"UNLOADPOINT"=> $_POST["unloadpoint"],
						"ENTITIES"=> $_POST["entities"],
						"COUNTRY_ORIGIN"=>$_POST["country"],
						"COUNTRY_DEST"=>$_POST["country2"],
						"EMAIL_TO" => $EMAILTO,//почта куда отправлять письма
					);
					if($ACTIVE == "Y")
					{
						CEvent::Send($arParams["EVENT_NAME_GOOD"], SITE_ID, $LetterFields);
					}
					elseif($ACTIVE == "N")
					{
						CEvent::Send($arParams["EVENT_NAME_BAD"], SITE_ID, $LetterFields);
					}
					LocalRedirect($APPLICATION->GetCurPageParam("success=".$arResult["PARAMS_HASH"]."&last=".$PRODUCT_ID, Array("success","last")));
							//echo 'NewId:'.$ID;//Id нового элемента
				}
				else 
				{
					echo "Error: ".$el->LAST_ERROR; //$PRODUCT_ID = $el->Add //Покажет ошибки
				}
			// }//($allow_number)
		}//if errors
		//отправляем данные в форму если есть ошибки
		$arResult["NAMEAUTHBODY"] = htmlspecialcharsbx($_POST["nameauthbody"]);
		$arResult["CITYAUTHBODY"] = htmlspecialcharsbx($_POST["cityauthbody"]);
		$arResult["REGNUM"] = htmlspecialcharsbx($_POST["regnum"]);
		$arResult["POSISSUEDREGNUM"] = htmlspecialcharsbx($_POST["posissuedregnum"]);
		$arResult["NAMEISSUEDREGNUM"] = htmlspecialcharsbx($_POST["nameissuedregnum"]);
		$arResult["FORMNUM"] = htmlspecialcharsbx($_POST["formnum"]);
		$arResult["DATEISSUECERT"] = htmlspecialcharsbx($_POST["dateissuecert"]);
		$arResult["ISSUED"] = htmlspecialcharsbx($_POST["issued"]);
		$arResult["PRODUCTNAME"] = htmlspecialcharsbx($_POST["productname"]);
		$arResult["TRANSPORT"] = htmlspecialcharsbx($_POST["transport"]);
		$arResult["PRODUCTIONADRESS"] = htmlspecialcharsbx($_POST["productionadress"]);
		$arResult["FOUNDFITFOR"] = htmlspecialcharsbx($_POST["foundfitfor"]);
		$arResult["PRODUCTIONDATE"] = htmlspecialcharsbx($_POST["productiondate"]);
		$arResult["MANUFACTNAME"] = htmlspecialcharsbx($_POST["manufactname"]);
		$arResult["QUANTITY"] = htmlspecialcharsbx($_POST["quantity"]);
		$arResult["TTD"] = htmlspecialcharsbx($_POST["ttd"]);
		$arResult["RECIPIENTNAME"] = htmlspecialcharsbx($_POST["recipientname"]);
		$arResult["LABNAME"] = htmlspecialcharsbx($_POST["labname"]);
		$arResult["SPECIALSNOTES"] = htmlspecialcharsbx($_POST["specialsnotes"]);
		$arResult["JOBNAME"] = htmlspecialcharsbx($_POST["jobname"]);
		$arResult["OFFICIALNAME"] = htmlspecialcharsbx($_POST["officialname"]);
		$arResult["PLACELOAD"] = htmlspecialcharsbx($_POST["placeload"]);
		$arResult["REASONS"] = htmlspecialcharsbx($_POST["reasons"]);
		$arResult["UNLOADPOINT"] = htmlspecialcharsbx($_POST["unloadpoint"]);
		$arResult["ENTITIES"] = htmlspecialcharsbx($_POST["entities"]);
		$arResult["AUTHOR"] = htmlspecialcharsbx($_POST["author"]);
		$arResult["COUNTRY"] = htmlspecialcharsbx($_POST["country"]);
		$arResult["COUNTRY2"] = htmlspecialcharsbx($_POST["country2"]);
		$arResult["DEL"] = htmlspecialcharsbx($_POST["del"]);
		$arResult["DEL_P"] = htmlspecialcharsbx($_POST["del_p"]);
		$arResult["AUTHOR_ANNUL"] = htmlspecialcharsbx($_POST["author_annul"]);
		$arResult["COUNTRY"] = htmlspecialcharsbx($_POST["country"]);
		$arResult["COUNTRY2"] = htmlspecialcharsbx($_POST["country2"]);

	}//if sessid
	else //сессия истекла
		$arResult["ERROR_MESSAGE"][] = GetMessage("MF_SESS_EXP");
}//if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["submit"] <> '' && (!isset($_POST["PARAMS_HASH"]) || $arResult["PARAMS_HASH"] === $_POST["PARAMS_HASH"]))
//обработчики для печати ранее введеной формы
elseif($_REQUEST["success"] == $arResult["PARAMS_HASH"])
{
	$arResult["OK_MESSAGE"] = $arParams["OK_TEXT"];
	$arResult["LAST_ID"] = $_REQUEST["last"];;
	$arResult["NOT_GOOD_MESSAGE"] = '';
	if(CModule::IncludeModule("iblock"))
	{	
		$ElementID=$arResult["LAST_ID"];
		$res = CIBlockElement::GetProperty($FORM_IBLOCK_ID, $ElementID, false, false, array("CODE" => "REG_NUM"));
    if ($ob = $res->GetNext())
    { 
      if(strlen($ob['VALUE'])<=0)
      {
      	$arResult["OK_MESSAGE"] = GetMessage("MF_OK_MESSAGE").GetMessage("OK_MESSAGE_SAVE");
      	$arResult["NOT_GOOD_MESSAGE"] = GetMessage("MF_NOT_GOOD");
    	}
    	else
    	{
    		$arResult["OK_MESSAGE"] = GetMessage("OK_MESSAGE_NUMBER").$ob['VALUE'];
				$arResult["PRINT_ID"]=$arResult["LAST_ID"];
    	}
		}
	}
}
$this->IncludeComponentTemplate();