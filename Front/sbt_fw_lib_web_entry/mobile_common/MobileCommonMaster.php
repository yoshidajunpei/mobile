<?php

/**
 * 対象外 (候補) コーブマスタ取得
 */
class MobileCommonMaster
{

    /**
     * 対象外 (候補) コーブマスタ取得
     */
    public static function getExcludeCorpMaster()
    {

        $excludeCorpMaster = array(
            array("corp" => "88", "corpName" => "YCV", "redirectUrl" => ""),
            array("corp" => "87", "corpName" => "OCT", "redirectUrl" => "")
        );

        return $excludeCorpMaster;
    }

    /**
     * お支払方法について取得
     */
    public static function getSettleMaster()
    {

        $entryAfterMaster = array(
            array("code" => "1", "name" => "credit", "value" => "F")
        );
        return $entryAfterMaster;
    }

    /**
     * 性別取得
     */
    public static function getGenderMaster()
    {

        $entryAfterMaster = array(
            array("code" => "1", "name" => "Man", "value" => "", "param" => "m"),
            array("code" => "2", "name" => "Woman", "value" => "", "param" => "w")
        );

        return $entryAfterMaster;
    }

    /**
     *  初期費用について取得
     */
    public static function getInitialMaster()
    {
        $initialMaster = array(
            array("code" => "1", "initialCampaign" => 3000, "initialCampaignTax" => 3300, "amt" => 3000, "amtIncTax" => 3300, "name" => "契約事務手数料（モバイル）", "serviceId" => "")
        );
        return $initialMaster;
    }

    /**
     * 規約情報表示用
     * corp=>tv_no(局ID)
     */
    public static function getCorpToTvNo()
    {
        $bureauMaster = array(
            array("tv_no" => "20", "corp" => "5"),
            array("tv_no" => "6", "corp" => "11"),
            array("tv_no" => "4", "corp" => "12"),
            array("tv_no" => "37", "corp" => "14"),
            array("tv_no" => "35", "corp" => "15"),
            array("tv_no" => "36", "corp" => "15"),
            array("tv_no" => "54", "corp" => "15"),
            array("tv_no" => "37", "corp" => "16"),
            array("tv_no" => "39", "corp" => "28"),
            array("tv_no" => "2", "corp" => "31"),
            array("tv_no" => "3", "corp" => "32"),
            array("tv_no" => "21", "corp" => "33"),
            array("tv_no" => "1", "corp" => "37"),
            array("tv_no" => "20", "corp" => "40"),
            array("tv_no" => "7", "corp" => "41"),
            array("tv_no" => "5", "corp" => "42"),
            array("tv_no" => "53", "corp" => "61"),
            array("tv_no" => "111", "corp" => "74"),
            array("tv_no" => "106", "corp" => "75"),
            array("tv_no" => "118", "corp" => "86"),
            array("tv_no" => "18", "corp" => "1"),
            array("tv_no" => "16", "corp" => "3"),
            array("tv_no" => "10", "corp" => "4"),
            array("tv_no" => "17", "corp" => "7"),
            array("tv_no" => "9", "corp" => "9"),
            array("tv_no" => "13", "corp" => "17"),
            array("tv_no" => "8", "corp" => "21"),
            array("tv_no" => "17", "corp" => "22"),
            array("tv_no" => "12", "corp" => "34"),
            array("tv_no" => "14", "corp" => "43"),
            array("tv_no" => "51", "corp" => "45"),
            array("tv_no" => "57", "corp" => "46"),
            array("tv_no" => "56", "corp" => "47"),
            array("tv_no" => "19", "corp" => "49"),
            array("tv_no" => "47", "corp" => "57"),
            array("tv_no" => "50", "corp" => "58"),
            array("tv_no" => "48", "corp" => "59"),
            array("tv_no" => "49", "corp" => "60"),
            array("tv_no" => "52", "corp" => "62"),
            array("tv_no" => "55", "corp" => "63"),
            array("tv_no" => "109", "corp" => "64"),
            array("tv_no" => "110", "corp" => "65"),
            array("tv_no" => "101", "corp" => "66"),
            array("tv_no" => "107", "corp" => "67"),
            array("tv_no" => "108", "corp" => "68"),
            array("tv_no" => "102", "corp" => "69"),
            array("tv_no" => "105", "corp" => "71"),
            array("tv_no" => "112", "corp" => "73"),
            array("tv_no" => "120", "corp" => "76"),
            array("tv_no" => "121", "corp" => "76"),
            array("tv_no" => "122", "corp" => "76"),
            array("tv_no" => "123", "corp" => "76"),
            array("tv_no" => "124", "corp" => "76"),
            array("tv_no" => "117", "corp" => "77"),
            array("tv_no" => "103", "corp" => "78"),
            array("tv_no" => "104", "corp" => "79"),
            array("tv_no" => "116", "corp" => "80"),
            array("tv_no" => "125", "corp" => "80"),
            array("tv_no" => "115", "corp" => "82"),
            array("tv_no" => "113", "corp" => "83"),
            array("tv_no" => "100", "corp" => "83"),
            array("tv_no" => "119", "corp" => "83"),
            array("tv_no" => "32", "corp" => "8"),
            array("tv_no" => "22", "corp" => "19"),
            array("tv_no" => "26", "corp" => "83"),
            array("tv_no" => "25", "corp" => "83"),
            array("tv_no" => "31", "corp" => "23"),
            array("tv_no" => "24", "corp" => "29"),
            array("tv_no" => "28", "corp" => "35"),
            array("tv_no" => "27", "corp" => "36"),
            array("tv_no" => "29", "corp" => "36"),
            array("tv_no" => "29", "corp" => "36"),
            array("tv_no" => "30", "corp" => "44"),
            array("tv_no" => "34", "corp" => "48"),
            array("tv_no" => "44", "corp" => "50"),
            array("tv_no" => "45", "corp" => "51"),
            array("tv_no" => "40", "corp" => "52"),
            array("tv_no" => "41", "corp" => "53"),
            array("tv_no" => "43", "corp" => "54"),
            array("tv_no" => "42", "corp" => "55"),
            array("tv_no" => "46", "corp" => "56"),
            array("tv_no" => "81", "corp" => "87"),
            array("tv_no" => "82", "corp" => "87"),
            array("tv_no" => "83", "corp" => "87"),
            array("tv_no" => "84", "corp" => "87"),
            array("tv_no" => "85", "corp" => "87"),
            array("tv_no" => "86", "corp" => "87")
            // 以下、検証用に追加設定
            ,
            array("tv_no" => "27", "corp" => "2")
        );
        return $bureauMaster;
    }

    /**
     * 規約情報表示 (コーブが存在しない場合に使用)
     * zipcode 2=>mobile_tv_no (ID)
     */

    public static function getCorpMaster()
    {
        $delegateCorpList = array(
            array("zipcode" => "00", "name" => "北海道", "delegateCorp" => "37", "mobileTvNo" => "1"),
            array("zipcode" => "01", "name" => "秋田県", "delegateCorp" => "62", "mobileTvNo" => "52"),
            array("zipcode" => "02", "name" => "岩手県", "delegateCorp" => "62", "mobileTvNo" => "52"),
            array("zipcode" => "03", "name" => "青森県", "delegateCorp" => "62", "mobileTvNo" => "52"),
            array("zipcode" => "04", "name" => "北海道", "delegateCorp" => "37", "mobileTvNo" => "1"),
            array("zipcode" => "04", "name" => "北海道", "delegateCorp" => "37", "mobileTvNo" => "1"),
            array("zipcode" => "06", "name" => "北海道", "delegateCorp" => "37", "mobileTvNo" => "1"),
            array("zipcode" => "07", "name" => "北海道", "delegateCorp" => "37", "mobileTvNo" => "1"),
            array("zipcode" => "08", "name" => "北海道", "delegateCorp" => "37", "mobileTvNo" => "1"),
            array("zipcode" => "09", "name" => "北海道", "delegateCorp" => "37", "mobileTvNo" => "1"),
            array("zipcode" => "10", "name" => "東京都", "delegateCorp" => "3", "mobileTvNo" => "1"),
            array("zipcode" => "11", "name" => "東京都", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "12", "name" => "東京都", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "13", "name" => "東京都", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "14", "name" => "東京都", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "15", "name" => "東京都", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "16", "name" => "東京都", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "17", "name" => "東京都", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "18", "name" => "東京都", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "19", "name" => "東京都", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "20", "name" => "東京都", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "21", "name" => "神奈川県", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "22", "name" => "神奈川県", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "23", "name" => "神奈川県", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "24", "name" => "神奈川県", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "25", "name" => "神奈川県", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "26", "name" => "千葉県", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "27", "name" => "千葉県", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "28", "name" => "千葉県", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "29", "name" => "千葉県", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "30", "name" => "茨城県", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "31", "name" => "茨城県", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "32", "name" => "茨城県", "delegateCorp" => "3", "mobileTvNo" => "16"),
            array("zipcode" => "33", "name" => "茨城県", "delegateCorp" => "3", "mobileTvNo" => "16")
        );
        return $delegateCorpList;
    }

    /**
     * コープマスタ取得
     */
    public static function getCorpMaster()
    {

        $ScorpMaster = array(
            array("corp" => "01", "ngt" => "0", "corpName" => "J:COM杉並·中野", "companyName" => "株式会社ジェイコム東京", "corpZipCd" => "177-0033", "corpAddress" => "東京都e馬区桜台1=1=6"),
            array("corp" => "02", "ngt" => "0", "corpName" => "J:COM 試験局", "companyName" => "株式会社ジェイコムテストコーブ", "corpZipCd" => "100-000s", "corpAddress" => "東京都干代田区丸の内1=8=1丸の内トラストタワーNeB"),
            array("corp" => "38", "ngt" => "0", "corpName" => "J:COM 38", "companyName" => "38 株式会社□ジェイコム東京", "corpZipCd" => "999-9938", "corpAddress" => "38県大分市松ヶ丘3-1-12"),
            array("corp" => "39", "ngt" => "0", "corpName" => "J:COM 39", "companyName" => "39 株式会社□ジェイコム東京", "corpZipCd" => "999-9939", "corpAddress" => "39県大分市松ヶ丘3-1-12")
        );

        return $ScorpMaster;
    }
}