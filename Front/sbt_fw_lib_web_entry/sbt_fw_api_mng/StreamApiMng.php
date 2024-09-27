<?php

require_once(LIB_DIR_REALPATH."sbt_fw_api_mng/sbt_fw_api_mng.php");

/**
*JCOMストリーム 個人課金申込用各種API制御クラス
*/
class StreamApiMng {

    /**リクエストヘッダ設定
    * @param stranId トランザクションID (省略時はCookieから取得)
    * @param $jsonMode JSONリクエストモード 1:リクエストバラメータをJSON形式で実行
    */
    private function setRequestHeader($tranId, $jsonMode = 0) {
        
        $apiTranId = "";
        if ($tranId == null) {
            $apiTranId =
            SbtFwFunctions::decryptString(SbtFwFunctions::getCookieData(SbtFwConstant::STREAM_COOKIE_TRANSACTION_ID));
        } else {
            $apiTranId = $tranId;
        }

        $header = [
            ITVAPI_APP_ID_HEADER .": " .ITVAPI_API_APP_ID,
            ITVAPI_ACCESS_KEY_HEADER.": ".ITVAPI_API_ACCESS_KEY,
            ITVAPI_TRXID_KEY .": " .$apiTranId,
        ];

        if ($jsonMode == SbtFwConstant::API_REQUEST_PARAM_MODE_JSON) {
            $header[] = 'Content-Type: application/json';
        } else {
            $header[] = 'Content-Type: application/x-www-form-urlencoded';
        }

        return $header;

    }

    /**
     * 個人課金リクエストヘッダ設定
     * @param $tranId トランザクションID (省略時はCookieから取得)
     * @param $jsonMode JSON リクエストモード 1:リクエストパラメータをJSON形式で実行
     */
    private function setRequestPersonalSystemHeader($tranId) {

        $apiTranId = "";
        if ($tranId == null) {
            $apiTranId = SbtFwFunctions::decryptString(SbtFwFunctions::getCookieData(SbtFwConstant::STREAM_COOKIE_TRANSACTION_ID));
        } else {
            $apiTranId = $tranId;
        }

        $header = [
            PSAPI_APP_ID_HEADER. ": ".PSAPI_API_APP_ID,
            PSAPI_ACCESS_KEY_HEADER. ":".PSAPI_API_ACCESS_KEY,
            PSAPI_TRXID_KEY. ": ". $apiTranId,
        ];

        if ($jsonMode == SbtFwConstant::API_REQUEST_PARAM_MODE_JSON) {
            $header[]='Content-Type: application/json';
        } else {
            $header[]='Content-Type: application/x-www-form-urlencoded';
        }

            return $header;

    }

    /**
     * エラー情報編集
     *@param $body レスポンスボディ
     */
    public function editErrorInfo($body) {

        if (isset($body) && strlen($body) > 0) {
        $result = array();
        $result["resultFlag"] = SbtFwConstant::STATUS_NG;
        $result["errorInfo"] = json_decode($body, true);
        return $result;
        } else {
        return null;
        }

    }

    /**
     * JCOMストリーム情報取得
     *@param $tranId トランザクションID
     */
    public function execGetStreamServiceApi($tranId) {

        try {

            // リクエストヘッダ設定
            $header = $this->setRequestPersonalSystemHeader($tranId, SbtFwConstant::API_REQUEST_PARAM_MODE_JSON);
            // 外部API実行
            $clsSbtFwApiMng = new SbtFwApiMng($tranId);
            $response = $clsSbtFwApiMng->apiSendRequest(JST_GET_STREAM_INFO, "POST", "{}", $header, 3);
            // 結果判定 true: 正常終了、false: 異常終了
            if (!$response["curl_result"]) {
                return null;
            }
            return json_decode($response["body"], true);
        
        } catch (ErrorException $ex) {

            return null;

        }
    }

    /**
     * 顧客情報取得API实行 (JCOM STREAM汎用)
     * @param $corp コープ
     * @param $custAcct カストアカウント
     * @param $custNum 個人課金お客様番号
     * @param $excludesvcsts 除外サービス状態
     * @param $tranId トランザクションID
     */
    public function execGetStreamCustInfoApi ($corp, $custAcct, $custNum, $excludeSvcSts, $tranId) {

        try {

            // リクエストパラメータ設定
            $jsonParamItem = array();
            $jsonParamItem["corp"] = $corp;
            $jsonParamItem["custAcct"] = $custAcct;
            $jsonParamItem["custNum"] = $custNum;
            $jsonParamItem["excludeSvcSts"] = $excludeSvcSts;
            $reqParams = json_encode($jsonParamItem, JSON_UNESCAPED_UNICODE);
            // リクエストヘッダ設定
            $header = $this->setRequestPersonalSystemHeader($tranId, SbtFwConstant::API_REQUEST_PARAM_MODE_JSON);
            // 外部API实行
            $clsSbtFwApiMng = new SbtFwApiMng($tranId);
            $response = $clsSbtFwApiMng->apiSendRequest(CMN_STREAM_GET_CUST_INFO_URL, "POST", $reqParams, $header, 3);
            // 結果判定 true: 正常終了、false: 異常終了
            if (!$response["curl_result"]) {
                return null;
            }

            // 結果を返却
        return json_decode($response["body"], true);

        } catch (ErrorException $ex) {

            return null;

        }
    }

    /**
     * JCOM ストリーム 新規申込オーダー登録API実行å
     * @param SinputArray 入力パラメータ
     * @param serviceKbn サービス区分 (FOCUS連携 )
     * @param StranId トランザクションID
     */
    public function execNewRegistrationStreamApi ($inputArray, $servicekbn, $tranId) {

        try {
            // リクエストパラメータ設定
            $jsonParamItem LO array();
            
            // 既加入
            if ($inputArray["use_jcom"] ===  SbtFwConstant::USE_JCOM) {
                $userInfo = json_decode($inputArray["userInfo"], true);
            } else {
            // 未加入
                $userInfo["user_id"] = "";
                $userInfo["cust_num"] = "";
                $userInfo["email"] = "";
            }

            $jsonParamItem["orderDateTime"] = date("YmdHis"); // 申込日時

            $custNum = isset($userInfo["cust_num"]) && $userInfo["cust_num"] !== "" ? $jsonParamItem["custNum"] : null;
            $jsonParamItem["custnum"] = $custNum; // お客様番号

            $jsonParamItem["ppid"] = ($userInfo["user_id"] !== "") ? $userInfo["user_id"] : ""; //PPID
            // PID更新フラグメールアドレスの変更が行われていた場合フラグを立てます
            // メールドレス変更有無 0: 変更なし (新規登録) 1:変更あり
            if ($inputArray["orderEmail Change"] !== "") {
                $jsonParamItem["changeMailFlg"] = "1";
                $jsonParamItem["mailAddress"] = $userInfo["email"];
                $jsonParamItem["mailAddressAfter"] = $userInfo["oderemail"];
            } else {
                $jsonParamItem["changeMailFlg"] = "0";
                if ($inputArray["use_jcom"] === SbtFwConstant::USE_JCOM) {
                    $jsonParamItem["mailAddress"] = $userInfo["email"];
                } else {
                    $jsonParamItem["mailAddress"] = $inputArray["orderEmail"]; 
                }
                $jsonParamItem["mailAddressAfter"] = "";
            }
            // PIDパスワード (新規の場合は必須 新規ではない場合は仮固定値)
            $jsonParamItem["password"] = ($inputArray["orderPidPassword"] !== "") ? $inputArray["orderPidPassword"]: "asdf1234";
            $jsonParamItem["ruleVersion"] = $inputArray["ruleVersion"];  //PID規約バージョン
            $jsonParamItem["lastName"] = $inputArray["order LastName"];
            $jsonParamItem["firstName"] = $inputArray["orderFirstName"];
            $jsonParamItem["lastNameKana"] = $inputArray["orderLastNameKana"];
            $jsonParamItem["firstNameKana"] = $inputArray["orderFirstNameKana"];
            $jsonParamItem["birthdate"] = $inputArray["orderBirthday"]; // 生年月日
            $jsonParamItem["phoneNumber"] = $inputArray["orderScheduleTel"];
            $jsonParamItem["payMethod"] = "3"; // 支払方法 (3: クレジット、 4:窓口 (請求書払)
            $jsonParamItem["memCd"] = $inputArray["symphony_member_id"]; // 会員ID (DGFT会員ID 支払情報がクレジットの場合必須)
            $jsonParamItem["colAgId"] = "10"; // 決済代行会社コード (決済代行会社 01: ソニーペイメント、 02:GMOペイメント、 03:SBペイメント、 10: ベリトランス)
            $jsonParamItem["invoIssueDiv"] = "3"; // 請求書発行区分 (3:クレジットのみ)
            // 契約サービスリスト
            $jsonParamItem["contSrvList"] = array();
            $serviceIdArray = explode(", ", $inputArray["serviceId"]);
                for ($isi = 0; $isi <count($serviceIdArray); $iSi++) {
                if ($isi < 1) {
                    $jsonParamItem["contSrvList"][$isi]["srvId"] = $serviceIdArray[$iSi];// サービスID (無料: キャンペーンあり)
                    $jsonParamItem["contSrvList"][$isi]["relativePrice"] = "0";// 相对金額
                } else {
                    $jsonParamItem["contSrvList"][$isi]["srvId"] = $serviceIdArray[$isi];// サービスID (有料: キャンペーンなし)
                    $jsonParamItem["contSrvList"][$isi]["relativePrice"] = $inputArray["serviceAmt"];// 相对金額
                    // キャンペーンありの場合のみ課金開始日をセット//
                    if ($inputArray["cmpgnValidFlg"] === SbtFwConstant::STRFLG_ON) {
                        $serviceBillingDate = date("Ymd", strtotime($inputArray["pmtWeek"]. "week"));
                        $jsonParamItem["contSrvList"][$isi]["chrgStDate"] = $serviceBillingDate;// 課金開始日
                    }
                }
            }

            $jsonParamItem["planknd"] = SbtFwConstant::PS_ORDER_PLAN_KBN_PAID;// ブラン区分
            $jsonParamItem["focusAppId"] = WCFAPI_ID_KEY_TBL[$serviceKbn]["appId"];// FOCUS ACCSESS_KEY
            $jsonParamItem["focusAccessKey"] = WCFAPI_ID_KEY_TBL [$serviceKbn]["accessKey"]; // FOCUS連携区分
            // FOCUSE連携メモ
            $jsonParamItem["focusMemo"] = $inputArray["memo"];

            $reqParams = json_encode($jsonParamItem, JSON_UNESCAPED_UNICODE);

            // リクエストヘッダ設定
            $header = $this->setRequestPersonalSystemHeader($tranId, SbtFwConstant::API_REQUEST_PARAM_MODE_JSON);

            // 外部API实行
            $clsSbtFwApiMng = new SbtFwApiMng($tranId);
            $response = $clsSbtFwApiMng->apiSendRequest(JST_REGST_NEW_JCOM_STREAM, "POST", $reqParams, $header, 0);

            // 結果判定
            if (!$response["curl_result"]) {
                return null;
            }

            // ステータスコード判定
            if ($response["header"]["http_code"] < SbtFwConstant::HTTP_STATUS_OK || $response["header"]["http_code"] >= SbtFwConstant::HTTP_STATUS_MULTIPLE) {
                return $this->editErrorInfo($response["body"]);
            }
            // 結果を返却
            $result =json_decode($response["body"], true);
            return $result;

        } catch (ErrorException $ex) {
            
            return null;

        }

    }

    /**
     *会員ID発行
     * 個人課金システムへDGFT会員番号発行をリクエストし、 DGFT会員番号を取得する。
     * @param $tranId トランザクションID
     */
    public function execIssueMemberIdApi ($tranId) {

        try {

            // リクエストヘッダ設定
            $header = $this->setRequestPersonalSystemHeader($tranId, SbtFwConstant::API_REQUEST_PARAM_MODE_JSON);
            // 外部API実行
            $clsSbtFwApiMng = new SbtFwApiMng($tranId);
            $response = $clsSbtFwApiMng->apiSendRequest(CMN_IS_MOBILE_ISSUE_MEMBERID_URL, "POST", "{}", $header, 3);
            // 結果判定 true: 正常終了、false: 異常終了
            if (!$response["curl_result"]) {
                return null;
            }
            return json_decode($response["body"], true);

        }catch (ErrorException $ex) {

            return null;

        }
    }

    /**
    *ユーザ情報チェックAPI実行
    * @param $email - FUZ
    * @param $tranId トランザクションID
    */
    public function execUserInfoCheckApi ($email, $tranId) {

        try {
    
            // リクエストパラメータ設定
    
            $jsonParamItem = array();
            $jsonParamItem["email"] = $email; // メールアドレス
            $reqParams = json_encode($jsonParamItem, JSON_UNESCAPED_UNICODE);

            // リクエストヘッダ設定
            $header = $this->setRequestHeader($tranId, SbtFwConstant::API_REQUEST_PARAM_MODE_JSON);

            // 外部API实行
            $clsSbtFwApiMng = new SbtFwApiMng($tranId);
            $response = $clsSbtFwApiMng->apiSendRequest (USER_CHECK_URI, "POST", $reqParams, $header, 3);

            // 結果判定
            if (!$response["curl_result"]){
                return null;
            }

            // ステータスコード判定
            if ($response["header"]["http_code"] < SbtFwConstant::HTTP_STATUS_OK || $response["header"]["http_code"] >= SbtFwConstant::HTTP_STATUS_MULTIPLE) {
                return $this->editErrorInfo($response["body"]);
            }
            // 結果を返却
            $result = json_decode($response["body"], true);
            $result["resultFlag"] = SbtFwConstant::STATUS_OK;
            return $result;
            
        }catch (ErrorException $ex) {
        
            return null;

        }

    }

}
?>