<?php

// 新規Mobile  マスターデータクラス
require_once(LIB_DIR_REALPATH .'mobile_common/MobileCommonMaster.php');

/**
 * Mobile  共通関数クラス
 */
class MobileCommonFunctions {

    /**
    * パーソナルID 認証・認可Ïベージのバスを生成
    *@param $redirectPath 認証・認可後のリダイレクト先
    *@param $state リダイレクト先へ引き継ぐパラメータ
    */
    public static function createPidLoginCheckPath($redirectPath, $state = "") {

        // バーソナルID認証・認可ベージのバス
        $pidLoginCheckPath = "PID AUTHZ URI";
        $pidLoginCheckPath .= "?prompt=login";  // 認可要求時に必ずログイン画面を表示させるため「login」を設定
        $pidLoginCheckPath .= "&response_type=". PID_AUTHZ_RESPONSE_TYPE;
        $pidLoginCheckPath .= "&client_id=" . PID_CLIENT_ID;
        $pidLoginCheckPath .= "&redirect_uri=" . SITE_LOCAL_TOP_URL. $redirectPath;
        $pidLoginCheckPath .= "&scope=" . urlencode("openid http://jcom.co.jp/connect/profile/stand_new profile");
        $pidLoginCheckPath .= "&nonce=" . SbtFwFunctions::makeRandStr(32);
        $pidLoginCheckPath .= "&state=" . $state;

        return $pidLoginCheckPath;

    }

    /**
     * ログインチェック
     */
    public static function loginCheck($tranId, $loginType = "") {

        // アクセストークン
        $accessToken = "";
        // モバイルセッション継続フラグ
        $mobileSessionFlg = SbtFwFunctions::getCookieData(SbtFwConstant::MOBILE_COOKIE_SESSION .$loginType);

        // モバイルのログイントークンからログインチェック
        $userInfo = self::loginCheckFromMobileToken();
        if ($userInfo != null && $mobilesessionflg == SbtFwConstant::FLG_ON) {
            return $userInfo;
        } else {
            // Cookieに保持されたPIDアクセストークンを取得
            $accessToken =
            SbtFwFunctions::decryptString(SbtFwFunctions::getCookieData(SbtFwConstant::MOBILE_COOKIE_PID_ACCESS_TOKEN .$loginType));
            if (strlen($accessToken) == 0) {
                return null;
            }
            // アクセストークンからユーザー情報を取得
            $userInfo = self::getUserInfoFromToken($accessToken,$loginType, $tranId);

            // PIDシステムから取得したユーザー情報をチェック
            self::checkPidUserInfo($userInfo, $loginType);

            // PIDログイン情報をCookieに保存
            self::setPidInfo2Cookie($userInfo, $loginType);

            return $userInfo;
        }
    }

    /**
     *モバイルのログイントークンからユーザー情報取得
    */
    private static function loginCheckFromMobileToken($loginType = "") {

        // 結果返却用データ
        $retData = array();

        // ログイントークンチェック
        $loginToken = SbtFwFunctions::getCookieData(SbtFwConstant:: MOBILE_COOKIE_LOGIN_TOKEN .$loginType);
        if (strlen($loginToken) == 0) {
            return null;
        }

        // トークンを複合化+パラメータに分割
        $decryptParams = SbtFwFunctions::parseParams (SbtFwFunctions:: decryptString($loginToken));
        $checkErr = array();
        $checkErr["errflg"] = 0;

        // 複合化したトークンにアクセストークンが含まれているかチェック
        if (!array_key_exists("access_token", $decryptParams)) {
            return null;
        } else {
            SbtFwFunctions::checkInputError($decryptParams["access_token"], "access_token", "アクセストークン", 1, array(), $checkErr);
            if ($checkErr["errflg"] == 1) {
                return null;
            }
        }

        // 複合化したトークンにPPIDが含まれているかチェック
        if (!array_key_exists("access_token", $decryptParams)) {
            return null;
        } else {
            SbtFwFunctions::checkInputError($decryptParams["user_id"], "user_id", "PPID", 1, array(), $checkErr);
            if ($checkErr["errflg"] == 1) {
                return null;
            }
        }

        return $decryptParams;

    }

    /**
     * ユーザー情報からログイントークンを生成
     */
    public static function createLoginToken($userInfo) {
        $loginTokenParam = "user_id=" .$userInfo["user_id"];
        isset ($userInfo["corp"]) && $userInfo["corp"] !== "" ? $loginTokenParam .= "&corp=".$userInfo["corp"]: $loginTokenParam .= "&corp="."";
        isset($userInfo["cust_account"]) && $userInfo["cust_account"] !== "" ? $loginTokenParam .= "&cust_account=" .$userInfo["cust_account"] : $loginTokenParam .= "&cust_account=" ."";
        $loginTokenParam .= "&cust_name" .$userInfo["family_name"] ."　".$userInfo["given_name"];
        $loginTokenParam = "&email=" .$userInfo["email"];
        $loginTokenParam = "&access_token=" .$userInfo["access_token"];
        isset($userInfo["family_id_type"]) && $userInfo["family_id_type"] !== "" ?$loginTokenParam .= "&family_id_type=" .$userInfo["family_id_type"]: $loginTokenParam .= "&family_id_type=" ."contractant";
        isset($userInfo["cust_num"]) && $userInfo["cust_num"] !== "" ? $loginTokenParam .= "&cust_num=".$userInfo["cust_num"]: $loginTokenParam .= "&cust_num=" ."";
        isset($userInfo["phone_number"]) && $userInfo["phone_number"] !== "" ? $loginTokenParam .= "&phone_number=".$userInfo["phone_number"]: $loginTokenParam."&phone_number="."";
        $loginTokenParam .= "&create_time=" .date("YmdHis") .SbtFwFunctions::makeRandStr(5, true);

        return SbtFuFunctions::encryptString($loginTokenParam);

    }

    /**
     * パーソナルIDシステムからユーザー情報を取得 (認可コードから)
     */
    public static function getUserInfoFromPid($code, $redirectPath, $tranId) {

        try {

            // パーソナルIDシステムAPI制御クラス
            $mobilePidApiMng = new MobilePidApiMng;

            // アクセストークン取得
            $redirectUri = SITE_LOCAL_TOP_URL .$redirectPath;
            $tokenRes = $mobilePidApiMng->execTokenReqApi($code, null, $redirectUri, $tranId);

            if ($tokenRes == null) {
                return null;
            }

            // ユーザー情報取得
            $getProfileRes = $mobilePidApiMng->execGetProfileApi($tokenRes["access_token"], $tranId);
            if ($getProfileRes == null) {
                return null;
            }

            // ユーザー情報にアクセストークン・リフレッシュトークンを付ける
            $getProfileRes["access_token"] = $tokenRes["access_token"];
            $getProfileRes["refresh_token"] = $tokenRes["refresh_token"];
            $getProfileRes["refresh_url"] = $redirectUri;

            // 取得したユーザー情報を返す
            return $getProfileRes;

        } catch (ErrorException $ex) {
            return null;
    }

    /**
    *バーソナルIDシステムからユーザー情報を取得 (アクセストークンから)
    *@param $token PIDアクセストークン
    *@param $refreshToken PIDリフレッシュトークン
    *@param $tranId transaction id
    */
    public static function getUserInfoFromToken ($token, $loginType, $tranId) {

        try {

            // パーソナルIDシステムAPI制御クラス

            $mobilePidApiMng = new MobilePidApiMng;

            // リフレッシュトークン、ログイン時リダイレクトバスを取得

            $refreshToken =SbtFwFunctions::decryptString(SbtFwFunctions::getCookieData(SbtFwConstant::MOBILE_COOKIE_PID_REFRESH_TOKEN .$loginType));
            $redirectUri = SbtFwFunctions::getCookieData(SbtFwConstant::MOBILE_COOKIE_PID_REFRESH_URL .$loginType);

            // ユーザー情報取得
            $getProfileRes = $mobilePidApiMng->execGetProfileApi($token, $tranId);
            if ($getProfileRes == null) {

                // トークン無効時はリフレッシュトークンからアクセストークンを再度取得
                if (strlen($refreshToken) == 0) {
                    header("Location: /logout" .PAGE_FILE_EXTENSION);
                    exit;
                }

                // アクセストークン取得
                $tokenRes = $mobilePidApiMng->execTokenReqApi (null, $refreshToken, $redirectUri, $tranId);
                if ($tokenRes == null) {
                header("Location: /logout" .PAGE_FILE_EXTENSION);
                exit;
                }

                // ユーザー情報取得
                $getProfileRes = $mobilePidApiMng->execGetProfileApi($tokenRes["access_token"], $tranId);
                if ($getProfileRes == null) {
                header("Location: /logout" .PAGE_FILE_EXTENSION);
                exit;
                }

                // ユーザー情報にアクセストークン・リフレッシュトークンを付ける
                $getProfileRes["access_token"] = $token;
                $getProfileRes["refresh_token"] = $refreshToken;
                $getProfileRes["refresh_url"] = $redirectUri;

            } else {

                // ユーザー情報にアクセストークンを付ける
                $getProfileRes["access_token"] = $token;
                $getProfileRes["refresh_token"] = $refreshToken;
                $getProfileRes["refresh_url"] = $redirectUri;

            }

            // 氏名を編集
            $getProfileRes["cust_name"] = $getProfileRes["family_Aname"] ."　".$getProfileRes["given_name"];

            // 取得したユーザー情報を返す
            return $getProfileRes;

        } catch (ErrorException $ex) {
            return null;
        }

    }

    /***
    * PIDシステムから取得したユーザー情報をチェック
    */
    public static function checkPidUserInfo($userInfo, $loginType = "") {

        // メールアドレスが取得できなかった場合はエラー
        if (!isset($userInfo["email"])) {
            return false;
        }
        if (strlen($userInfo["email"]) === 0) {
            return false;
        }
        // パーソナルIDが取得できない場合はエラー
        if (!isset($userInfo["user_id"])) {
            return false;
        }
        if (strlen($userInfo["user_id"]) === 0) {
            return false;
        }

        return true;

    }

    /**
    * PIDログイン情報をCookieに保存
    * @param $userInfo ユーザー情報
    */
    public static function setPidInfo2Cookie($userInfo, $loginType = "") {

        // モバイルセッション継続フラグ
        setcookie (SbtFwConstant::MOBILE_COOKIE_SESSION .$loginType, SbtFwConstant:: FLG_ON, 0, "/", "", SECURITY_COOKIE_SECURE, SECURITY_COOKIE_HTTPONLY);

        // ログイントークン生成
        $loginToken = self::createLoginToken ($userInfo);

        // ログイントークンをCookieに保存
        setcookie (SbtFwConstant:: MOBILE_COOKIE_LOGIN_TOKEN ,$loginType, $loginToken, time() + MOBILE_TOKEN_LIMIT, "/", "", SECURITY_COOKIE_SECURE, SECURITY_COOKIE_HTTPONLY);

        // PIDアクセストークン・リフレッシュトークンをCookiel 保存
        $pidTokenLimit = 0;
        if (PID_TOKEN_LIMIT != 0) {
            $pidTokenLimit = time() + PID_TOKEN_LIMIT;
        }
        $pidRefreshTokenLimit = 0;
            if (PID_REFRESH_TOKEN_LIMIT != 0) {
            $pidRefreshTokenLimit = time() + PID_REFRESH_TOKEN_LIMIT;
        }

        setrawcookie (SbtFwConstant::MOBILE_COOKIE_PID_ACCESS_TOKEN .$loginType, SbtFwFunctions:: encryptString($userInfo["access_token"]), $pidTokenLimit, "/", "", SECURITY_COOKIE_SECURE, SECURITY_COOKIE_HTTPONLY);
        setrawcookie (SbtFwConstant::MOBILE_COOKIE_PID_REFRESH_TOKEN .$loginType, SbtFwFunctions:: encryptString($userInfo["refresh_token"]), $pidRefreshTokenLimit, "/", "", SECURITY_COOKIE_SECURE, SECURITY_COOKIE_HTTPONLY);
        setrawcookie (SbtFwConstant::MOBILE_COOKIE_PID_REFRESH_URL .$loginType, $userInfo["refresh_url"], $pidRefreshTokenLimit, "/", "", SECURITY_COOKIE_SECURE, SECURITY_COOKIE_HTTPONLY);

    }

    /**
     * PIDログイン情報をCookieから削除
    */
    public static function deleteCookiePidInfo($loginType = "") {

        // ログイントークンを削除
        setcookie (SbtFwConstant:: MOBILE_COOKIE_LOGIN_TOKEN .$loginType, "", time() - 1800, "/");

        // PIDアクセストークンを削除
        setcookie (SbtFwConstant::MOBILE_COOKIE_PID_ACCESS_TOKEN .$loginType, "", time() - 1800, "/");
        setcookie (SbtFwConstant::MOBILE_COOKIE_PID_REFRESH_TOKEN .$loginType, "", time() - 1800, "/");
        setcookie (SbtFwConstant:: MOBILE_COOKIE_PID_REFRESH_URL .$loginType, "", time() - 1800, "/");

    }

    /**
     * ログアウトページへ遷移
     * @param $service  サービス区分
     * @param $state  引き継ぎパラメータ
     */
    public static function redirectLogoutPage($service, $state = "") {

    header("location: " .SITE_LOCAL_TOP_URL ."logout" .PAGE_FILE_EXTENSION ."?service=" .$service. "&state=" .$state);
    exit;

    }

    /**
     * サービス区分制限設定
     */
    public static function setServiceReg() {
    
        // サービス区分制限
        $serviceReg = SbtFwConstant::REGIST_SERVICE_MOBILE;
            return $serviceReg;

    }

    /**
     * ファイルアップロード用ID
     * * @param $form_contract "";未加入　 "1";既加入　空は未加入
     */
    public static function generateUUID($form_contract = "") {
            // 「Wが期加入」「Xが未加入」
        if ($gorm_contract === "1") {
        return 'W' .str_pad (mt_rand (10, 999), 3, "0", STR_PAD_LEFT) .str_pad (mt_rand (0, 999999999), 9, "0", STR_PAD_LEFT);
        } else {
        // 未加入
        return 'X' .str_pad (mt_rand (10, 999), 3, "0", STR_PAD_LEFT) .str_pad (mt_rand (8, 999999999), 9, "0", STR_PAD_LEFT);
        }
    }

    /**
     * タグ出力用レスポンス取得
     *@param mixed $name $smp Smartyパラメーター
     */
    public static function getEntryRequestResponse($smp) {

        $response = array();
        $responseNameArray = array("tranId","use_jcom","orderAddress4","orderZipcode1", "orderZipcode2","corp", "selected_plan", "jcomAreaFlg", "delegatecorp", "selected_capacity");
        for ($i = 0; $i < Count($responseNameArray); $i++){
            if (array_key_exists($responseNameArray[$i], $smp["input"]) && $smp ["input"][$responseNameArray[$i]] !== "") {
                $response[$responseNameArray[$i]] = $smp["input"][$responseNameArray[$i]];
            } else { 
                $response[$responseNameArray[$i]] = "";
            }
            return $response;
        }
        return $response;
    }

    /**
     * Undocumented function
     * 送信先メールアドレス設定
     * @param $email  メールアドレス
     * @param $contractantId　契約者ID
     */
    public static function getToAddress ($email, $contractantId) {

        $toAddress = array();

        // 家族IDで申込された場合は契約者にもサンクスメールを送る
        if ($email !== $contractantId && $contractantId != null) {
            array_push($toAddress, $email, $contractantId);
        }else{
            array_push($toAddress, $email);
        }

        return $toAddress;

    }

    /**
     * メンテ時間判定 タイムアウト設定用 (タイムアウト時間 (分) 1つの画面から次画面に行くまでに30分以上経過した場合)
     * @param $code  1;初期化, "";通常
     * @param $use_jcom  1;加入, "";未加入
    */
    public static function execTimeout($code, $use_jcom, $ftSecretKey) 
    {
        // 本番公開後のFT指定フラグが入っていた場合無条件に抜けます
        if ($ftSecretKey === SbtFwConstant::FT_SECRET_KEY) {
            return 0;
        }

        // メンテナンス時間判定
        $time= strtotime (date ("H:i:s"));
        if ($time > strtotime (MAINTENANCE_START_TIME) && $time < strtotime (MAINTENANCE_FINISH_TIME)) {
            return 1;
        }

        // タイムアウト処理優先順
        // ①初期化処理 (プラン選択画面)
        // ②Transaction Idチェック
        // ③既加入の場合のログインチェック
        // ④30分経過

        // 現在の時刻を取得します
        $today = strtotime (date ("Y-m-d H:i:s"));
        // 初期化処理
        if ($code === "1") {
            // cookie の値を登録します
            setcookie (SbtFwConstant::MOBILE_COOKIE_TIME_OUT, $today, 0, "/", "", true, true);
            return 0;
        }

        // トランザクションIDチェック
        // 完了画面はtransaction_idがない
        if ($code !== "2") {
            if (strlen (SbtFwFunctions::getCookieData (SbtFwConstant::MOBILE_COOKIE_TRANSACTION_ID)) == 0) {
                return 2;
            }
        }

        // 既加入者向け処理
        if ($use_jcom === "1" && $code === "") {
            // ログイン状態判定
                $userInfo = MobileCommonFunctions::loginCheck("", "");
                // Cookieからユーザー情報を取得できなかった場合
                if ($userInfo === null) {
                    return 2;
                }
        }

            // 30分経過
            // cookieに保存されていた時間を取得
            $saveTime = SbtFwFunctions::getCookieData (SbtFwConstant::MOBILE_COOKIE_TIME_OUT);
            if ($saveTime === "") {
                // cookieの値を登録します
                setcookie (SbtFwConstant::MOBILE_COOKIE_TIME_OUT, $today, 8, "/", "", true, true);
                return 0;
            } else {
                $diff_minute = ($today - $saveTime) / 60; // 何分経過したか
            if (TIMEOUT_TIME < $diff_minute) {
                // error
                return 2;
            } else {
                // cookieの値を更新します
                setcookie(SbtFwConstant::MOBILE_COOKIE_TIME_OUT, $today, 0, "/", "", true, true);
                return 0;
            }
        }
    }

}
?>