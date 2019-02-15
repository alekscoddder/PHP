<?php
require_once("PayexpressValidate.php");

/**
 * <b>Connect to PAYEXPRESS Class</b>
 *
 * This class serve as an interface between merchant website and PAYEXPRESS web services.<br>
 *
 * Global Workflow is described in the documentation file that accompanies the API package.<br>
 *
 * This class does not do any sanitization on received data.
 * This must be done externally.<br>
 *
 * Every text must be encoded as UTF-8 when passed to this class.
 *
 * Every date passed to this class must be a valid date time format.
 *
 * PHP dependencies :
 * <ul>
 *  <li> PHP >= 5.2.0 </li>
 *  <li> PHP CURL module </li>
 * </ul>
 *
 * @version 1.2
 * @since 2015-02-21
 * @package Connect2PayexpressPos
 * @author Mehdi Atraimche < mehdi.atraimche@vpscorp.ma >
 * @copyright VPS - VANTAGE PAYMENT SYSTEMS < http://www.vantage-card.com/ >
 *
 */

class Connect2PayexpressPos{

    /**
     * CREATE RESERVATION MTI
     */
    const _RESA_REQUEST_MTI = '1100';
    /**
     * CREATE RESERVATION TC
     */
    const _RESA_REQUEST_TC = '00';

    /**
     * SIGN ON MTI
     */
    const _SIGN_ON_MTI = '1101';
    /**
     * SIGN ON TC
     */
    const _SIGN_ON_TC = '00';

    /**
     * SIGN OFF MTI
     */
    const _SIGN_OFF_MTI = '1101';
    /**
     * SIGN OFF TC
     */
    const _SIGN_OFF_TC = '01';

    /**
     * PING MTI
     */
    const _PING_MTI = '1101';
    /**
     * PING TC
     */
    const _PING_TC = '02';

    /**
     * CANCEL RESERVATION MTI
     */
    const _RESA_CANCEL_MTI = '1102';
    /**
     * CANCEL RESERVATION TC
     */
    const _RESA_CANCEL_TC = '00';

    /**
     * CHECK RESERVATION MTI
     */
    const _RESA_CHECKING_MTI = '1103';
    /**
     * CHECK RESERVATION TC
     */
    const _RESA_CHECKING_TC = '00';


    /**
     * CALLBACK RESERVATION CHECKING MTI
     */
    const _CALLBACK_RESA_CHECKING_MTI = '3100';
    /**
     * CALLBACK RESERVATION CHECKING TC
     */
    const _CALLBACK_RESA_CHECKING_TC = '00';

    /**
     * CALLBACK RESERVATION PAYMENT MTI
     */
    const _CALLBACK_TRX_PAYMENT_MTI = '3101';
    /**
     * CALLBACK RESERVATION PAYMENT TC
     */
    const _CALLBACK_TRX_PAYMENT_TC = '00';

    /**
     * CALLBACK TRANSACTION REVERSAL MTI
     */
    const _CALLBACK_TRX_REVERSAL_MTI = '3102';
    /**
     * CALLBACK TRANSACTION REVERSAL TC
     */
    const _CALLBACK_TRX_REVERSAL_TC = '00';

    /**
     * CALLBACK RESERVATION ADVICE MTI
     */
    const _CALLBACK_RESA_ADVICE_MTI = '3103';
    /**
     * CALLBACK RESERVATION ADVICE TC
     */
    const _CALLBACK_RESA_ADVICE_TC = '00';

    // RESPONSE CALLBACK MTI & TC

    /**
     * CALLBACK RESERVATION CHECKING RESPONSE MTI
     */
    const _CALLBACK_RESA_CHECKING_RESPONSE_MTI = '3100';
    /**
     * CALLBACK RESERVATION CHECKING RESPONSE TC
     */
    const _CALLBACK_RESA_CHECKING_RESPONSE_TC = '01';

    /**
     * CALLBACK RESERVATION PAYMENT RESPONSE MTI
     */
    const _CALLBACK_TRX_PAYMENT_RESPONSE_MTI = '3101';
    /**
     * CALLBACK RESERVATION PAYMENT RESPONSE TC
     */
    const _CALLBACK_TRX_PAYMENT_RESPONSE_TC = '01';

    /**
     * CALLBACK TRANSACTION REVERSAL RESPONSE MTI
     */
    const _CALLBACK_TRX_REVERSAL_RESPONSE_MTI = '3102';
    /**
     * CALLBACK TRANSACTION REVERSAL RESPONSE TC
     */
    const _CALLBACK_TRX_REVERSAL_RESPONSE_TC = '01';

    /**
     * CALLBACK RESERVATION ADVICE RESPONSE MTI
     */
    const _CALLBACK_RESA_ADVICE_RESPONSE_MTI = '3103';
    /**
     * CALLBACK RESERVATION ADVICE RESPONSE TC
     */
    const _CALLBACK_RESA_ADVICE_RESPONSE_TC = '01';


    /**
     * English Lang constants
     */
    const _LANG_EN = "en";
    /**
     * French Lang constants
     */
    const _LANG_FR = "fr";
    /**
     * Arabic Lang constants
     */
    const _LANG_ES = "ar";

    /**
     * Reservation id type : Token
     */
    const _RESERVATION_ID_AS_TOKEN = '1';
    /**
     * Reservation id type : Pos_book_id
     */
    const _RESERVATION_ID_AS_POS_BOOK_ID = '2';

    /**
     * Unknown title
     */
    const _TITLE_UNKNOWN = '0';

    /**
     * Miss title
     */
    const _TITLE_MISS = '1';

    /**
     * Mrs. Title
     */
    const _TITLE_MRS = '2';

    /**
     * Mr. title
     */
    const _TITLE_MR = '3';

    /**
     * Type document d'identification du Client
     *
     *    - '0' : CINE
     */
    const _CUSTOMER_ID_TYPE_CINE = '0';

    /**
     * Type document d'identification du Client
     *
     *    - '1' : Passeport
     */
    const _CUSTOMER_ID_TYPE_PASSPORT = '1';

    /**
     * Type document d'identification du Client
     *
     *    - '2' : Permis de conduire
     */
    const _CUSTOMER_ID_TYPE_DRIVING_LICENSE = '2';

    /**
     * Type document d'identification du Client
     *
     *    - '3' : Autre
     */
    const _CUSTOMER_ID_TYPE_OTHER = '3';

    /**
     * Date Format PAYEXPRESS Standard
     * @var string
     */
    const _DATE_FORMAT = 'YmdHis\TO';

    /**
     * All web services required fields
     * @var array
     */
    private $request_required_fields = array(
        'resa_request' => ['mti', 'tc', 'ws_version', 'pos_id', 'language', 'customer_name', 'customer_city_id',
            'send_customer_mail', 'transaction_amount', 'transmission_datetime', 'sender_mac_index', 'sender_mac'],
        'resa_cancel' => ['mti', 'tc', 'ws_version', 'acquirer_id', 'pos_id', 'provider_code', 'language',
            'transmission_datetime', 'sender_mac_index', 'sender_mac'],
        'resa_check' => ['mti', 'tc', 'ws_version', 'acquirer_id', 'pos_id', 'provider_code', 'language',
            'transmission_datetime', 'sender_mac_index', 'sender_mac'],
        'ping' => ['mti', 'tc', 'ws_version', 'acquirer_id', 'pos_id', 'provider_code', 'language',
            'transmission_datetime', 'sender_mac_index', 'sender_mac'],
        'signOn' => ['mti', 'tc', 'ws_version', 'acquirer_id', 'pos_id', 'provider_code', 'language',
            'transmission_datetime', 'sender_mac_index', 'sender_mac'],
        'signOff' => ['mti', 'tc', 'ws_version', 'acquirer_id', 'pos_id', 'provider_code', 'language',
            'transmission_datetime', 'sender_mac_index', 'sender_mac'],
    );

    /**
     * size of fields
     * @var array
     */
    private $fields_size = array(
        'ws_version' => '5', 'acquirer_id' => '10', 'pos_id' => '10', 'pos_name' => '32', 'provider_code' => '4',
        'network_code' => '2', 'pos_book_id' => '64', 'pos_book_date' => '20', 'language' => '2', 'customer_title' => '1',
        'customer_name' => '64', 'customer_id_type' => '1', 'customer_id' => '16', 'customer_phone' => '20',
        'customer_email' => '64', 'customer_birth_date' => '20', 'customer_zip_code' => '10', 'customer_address' => '128',
        'customer_country_code' => '3', 'customer_city_id' => '10', 'send_customer_mail' => '1', 'token' => '18',
        'good_service_desc' => '64', 'transaction_datetime' => '20', 'validity_reservation_datetime' => '20',
        'currency_code' => '3', 'url1' => '255', 'url2' => '255', 'url3' => '255', 'url4' => '255', 'url5' => '255',
        'pos_private_data' => '1024', 'sender_mac_index' => '1',
    );

    /**
     * type of fields
     * @var array
     */
    private $fields_validate = array(
        'ws_version' => 'isString', 'acquirer_id' => 'isNumericString', 'pos_id' => 'isNumericString',
        'pos_name' => 'isString', 'provider_code' => 'isNumericString', 'network_code' => 'isNumericString',
        'pos_book_id' => 'isString', 'pos_book_date' => 'isString', 'language' => 'isString',
        'customer_title' => 'isString', 'customer_name' => 'isName', 'customer_id_type' => 'isNumericString',
        'customer_id' => 'isNumericString', 'customer_phone' => 'isPhoneNumber', 'customer_email' => 'isEmail',
        'customer_birth_date' => 'isString', 'customer_zip_code' => 'isString', 'customer_address' => 'isAddress',
        'customer_country_code' => 'isCountryCode', 'customer_city_id' => 'isCityName', 'send_customer_mail' => 'isNumericString',
        'token' => 'isNumericString', 'good_service_desc' => 'isString', 'transaction_datetime' => 'isString',
        'validity_reservation_datetime' => 'isString', 'currency_code' => 'isCurrencyCode', 'transaction_amount' => 'isFloat',
        'url1' => 'isUrl', 'url2' => 'isUrl', 'url3' => 'isUrl', 'url4' => 'isUrl', 'url5' => 'isUrl',
        'pos_private_data' => 'isString', 'sender_mac_index' => 'isNumericString',
    );

    /**
     * All web service fields
     * @var array
     */
    private $webservice_fields = array(
        'mti', 'tc', 'ws_version', 'acquirer_id', 'pos_id', 'pos_name', 'provider_code', 'network_code', 'pos_book_id',
        'pos_book_date', 'language', 'customer_title', 'customer_name', 'customer_id_type', 'customer_id',
        'customer_phone', 'customer_email', 'customer_birth_date', 'customer_zip_code', 'customer_address',
        'customer_country_code','customer_city_id', 'send_customer_mail', 'token', 'good_service_desc',
        'transaction_datetime', 'validity_reservation_datetime', 'validity_reservation_status', 'currency_code',
        'transaction_amount', 'url1', 'url2', 'url3', 'url4', 'url5', 'pos_private_data', 'transmission_datetime',
        'response_code', 'response_message', 'sender_mac_index', 'sender_mac',
    );

    /**
     * Identifiant du type de message.
     * @var string(4)
     */
    private $mti;

    /**
     * Code Transaction
     * @var string(2)
     */
    private $tc;

    /**
     * Numéro de version du Web Service
     * @var string(5)
     */
    private $ws_version = '1.0';

    /**
     * Identifiant Acquéreur (Marchand) dont dépend le Point de Vente
     * @var string(10)
     */
    private $acquirer_id;

    /**
     * Identifiant du point de vente
     * @var string(10)
     */
    private $pos_id;

    /**
     * Enseigne du point de vente
     * @var string(32)
     */
    private $pos_name;

    /**
     * Code Apporteur d'affaire
     * @var string(4)
     */
    private $provider_code;

    /**
     * Code Réseau
     * @var string(2)
     */
    private $network_code;

    /**
     * Identifiant de la réservation émise par le point de vente
     * @var string(64)
     */
    private $pos_book_id;

    /**
     * Date de la réservation
     * @var string(20)
     */
    private $pos_book_date;

    /**
     * Code langue
     *
     *    - fr  : Français
     *    - en : English
     *    - ar  : Arabe
     * @var string(2)
     */
    private $language = self::_LANG_FR;

    /**
     * Titre du Client
     *
     *    - '0' : Unkhown
     *    - '1' : Madame
     *    - '2' : Mademoiselle
     *    - '3' : Monsieur
     *
     * @var string(1)
     */
    private $customer_title;

    /**
     * Nom du client
     * @var string(64)
     */
    private $customer_name;

    /**
     * Type document d'identification du Client
     *
     *    - '0' : CINE
     *    - '1' : Passeport
     *    - '2' : Permis de conduire
     *    - '3' : Autre
     *
     * @var string(1)
     */
    private $customer_id_type;

    /**
     * N° Identité du Client
     * @var string(16)
     */
    private $customer_id;

    /**
     * N° Téléphone du Client
     * @var string(20)
     */
    private $customer_phone;

    /**
     * Adresse émail du Client
     * @var string(64)
     */
    private $customer_email;

    /**
     * Date de naissance du Client
     * @var string(20)
     */
    private $customer_birth_date;

    /**
     * Code postale de l'adresse du Client
     * @var string(10)
     */
    private $customer_zip_code;

    /**
     * Adresse du Client
     * @var string(128)
     */
    private $customer_address;

    /**
     * Code Pays du client
     * @var string(3)
     */
    private $customer_country_code;

    /**
     * Code ou Nom de la ville
     * @var string(10)
     */
    private $customer_city_id;

    /**
     * Indicateur d'envoi d'émail au Client
     *
     *    - '0' : No
     *    - '1' : Yes
     *
     * @var string(1)
     */
    private $send_customer_mail;

    /**
     * Code Jeton
     * @var string(18)
     */
    private $token;

    /**
     * Description du bien ou du service payé
     * @var string(64)
     */
    private $good_service_desc;

    /**
     * Date & heure de la transaction
     * @var string(20)
     */
    private $transaction_datetime;

    /**
     * Date & heure de fin validité de la réservation
     * @var string(20)
     */
    private $validity_reservation_datetime;

    /**
     * Etat de la réservation
     * @var string(1)
     */
    private $validity_reservation_status;

    /**
     * Code monnaie de la transaction
     * @var string(3)
     */
    private $currency_code;

    /**
     * Montant de la transaction
     * @var float(9.2)
     */
    private $transaction_amount;

    /**
     * ResaRequest (Captcha) : Lien de Retour au site Marchant<br>
     * Response : Lien de génération doc HTML Resa
     * @var string(255)
     */
    private $url1;

    /**
     * ResaRequest(Captcha) : URL WS de confirmation Book Resa.<br>
     * Response : Lien de génération doc PDF Resa
     * @var string(255)
     */
    private $url2;

    /**
     * ResaRequest : URL WS Checking Resa.<br>
     * Response : Lien de génération doc HTML Trx
     * @var string(255)
     */
    private $url3;

    /**
     * ResaRequest : URL WS Payment Resa.<br>
     * Response : Lien de génération doc PDF Trx
     * @var string(255)
     */
    private $url4;

    /**
     * ResaRequest : URL WS Reversal Resa
     * @var string(255)
     */
    private $url5;

    /**
     * Données privées du POS qui sont alimentées par ce dernier au moment de la réservation
     * et reconduits dans la réponse et dans l’avis de paiement.
     * @var string(1024)
     */
    private $pos_private_data;

    /**
     * Code réponse
     * @var string(4)
     */
    private $response_code;

    /**
     * Message correspondant au code réponse
     * @var string(255)
     */
    private $response_message;

    /**
     * Indexe de la clé de cryptage des messages
     * @var string(1)
     */
    private $sender_mac_index;

    /**
     * Acquirer public key 1 for receiving request
     * @var string
     */
    private  $acquirer_public_output_key_1;

    /**
     * Acquirer public key 2 for receiving request
     * @var string
     */
    private  $acquirer_public_output_key_2;

    /**
     * Acquirer public key 1 for sending request
     * @var string
     */
    private  $acquirer_public_input_key_1;

    /**
     * Acquirer public key 2 for sending request
     * @var string
     */
    private  $acquirer_public_input_key_2;

    /**
     * @var string
     */
    private $webservice_url;

    /**
     * Date & heure de transmission
     * @var string(20)
     */
    private $transmission_datetime;

    /**
     * @var string
     */
    private $acquirer_public_key;

    /**
     * Message d'authentification
     * @var string(32)
     */
    private $sender_mac;

    /**
     * Request response result
     * @var array
     */
    private $result;

    /**
     * Callback request data
     * @var array
     */
    private $callback_request_data;

    /**
     * Request error message
     * @var string
     */
    private $error_message;
	
	// Path to the certificates file for SSL verification
	private $sslCAFile = null;

	private $extraCurlOptions = array();
	
    /**
     * Instantiate a new connect2payexpress pos client
     *
     * @param string $webservice_url The URL of the PAYEXPRESS webservice, captcha or server to server type.
     * @param array $data Data of transaction (optional)
     * @throws Exception
     */
    public function __construct($webservice_url, $data = null)
    {
        if(!EXPC2PValidate::isUrl($webservice_url))
            throw new Exception('Invalid webservice url');

        $this->webservice_url = $webservice_url;

        if($data != null)
        {
            if(!is_array($data))
                throw new Exception('Data must be an array');

            $this->initialData($data);
        }
    }
	
	/**
	 * Force the validation of the Connect2Pay SSL certificate.
	 *
	 * @param string $certFilePath The path to the PEM file containing the certification chain.
	 */
	public function forceSSLValidation($certFilePath) {
		$this->sslCAFile = $certFilePath;
	}

	/**
	 * Add extra curl options
	 */
	public function setExtraCurlOption($name, $value) {
		$this->extraCurlOptions[$name] = $value;
	}

    /**
     * Initialize the query data
     *
     * @param array $data data to be initialized
     * @return Connect2PayexpressPos|null
     */
    public function initialData($data)
    {
        if($data == null && !is_array($data)){
            return null;
        }

        foreach ($data as $var => $value) {
            if (property_exists($this, $var)) {
                $this->$var = $value;
            }
        }
        return ($this);
    }

    /**
     * Create a new reservation
     *
     * This function will calculate the senderMac, validating the necessary fields
     * and create a new Reservation.<br>
     *
     * @return bool true if the reservation is created, otherwise false
     * @example PACKAGE_DIR/Examples/createReservation.php
     */
    public function createReservation()
    {
        $this->mti = self::_RESA_REQUEST_MTI;
        $this->tc = self::_RESA_REQUEST_TC;

        $this->transmission_datetime = date(self::_DATE_FORMAT);
        $this->acquirer_public_key = ($this->sender_mac_index == '1') ? $this->acquirer_public_input_key_1 : $this->acquirer_public_input_key_2;

        $this->sender_mac = $this->getSenderMac(
            $this->pos_id,
            $this->transmission_datetime,
            $this->transaction_amount,
            $this->mti,
            $this->tc,
            $this->acquirer_public_key
        );

        return $this->initRequest($this->request_required_fields['resa_request']);
    }

    /**
     * Init captcha reservation fields
     *
     * This function will calculate the senderMac, validating the necessary fields
     * and init all necessary fields to send to captcha webservice url<br>
     *
     * @return bool|array false if validation failed, otherwise an array
     * @example PACKAGE_DIR/Examples/createReservation.php
     */
    public function initCaptchaReservation()
    {
        $this->mti = self::_RESA_REQUEST_MTI;
        $this->tc = self::_RESA_REQUEST_TC;

        $this->transmission_datetime = date(self::_DATE_FORMAT);
        $this->acquirer_public_key = ($this->sender_mac_index == '1') ? $this->acquirer_public_input_key_1 : $this->acquirer_public_input_key_2;

        $this->sender_mac = $this->getSenderMac(
            $this->pos_id,
            $this->transmission_datetime,
            $this->transaction_amount,
            $this->mti,
            $this->tc,
            $this->acquirer_public_key
        );

        if(!$this->validate($this->request_required_fields['resa_request']))
            return false;

        $data = [];

        foreach ($this->webservice_fields as $field)
        {
            $data[$field] = (!empty($this->{$field}) && $this->{$field} !== '0') ? $this->{$field} : '';
        }

        return $data;
    }

    /**
     * Cancel a reservation
     *
     * A cancel is the process of reversing a previously created, but not yet paid, reservation.<br>
     * It means that the cancel will never appear on a paid reservation.
     *
     * @param string $reservation_id can be a token_id or pos_book_id
     * @param string $as specify the type of $reservation_id
     * @return bool true if the reservation is canceled, otherwise false
     * @example PACKAGE_DIR/Examples/cancelReservation.php
     */
    public function cancelReservation($reservation_id, $as = self::_RESERVATION_ID_AS_TOKEN)
    {
        $this->mti = self::_RESA_CANCEL_MTI;
        $this->tc = self::_RESA_CANCEL_TC;

        $this->transmission_datetime = date(self::_DATE_FORMAT);
        $this->acquirer_public_key = ($this->sender_mac_index == '1') ? $this->acquirer_public_input_key_1 : $this->acquirer_public_input_key_2;

        $this->sender_mac = $this->getSenderMac(
            $this->pos_id,
            $this->transmission_datetime,
            $this->transaction_amount,
            $this->mti,
            $this->tc,
            ($as == self::_RESERVATION_ID_AS_TOKEN) ? $reservation_id : '',
            $this->acquirer_public_key
        );

        return $this->initRequest($this->request_required_fields['resa_cancel']);
    }

    /**
     * Check a reservation
     *
     * This function allows merchant to query the current status of a previous reservation
     * based on a $reservation_id.
     *
     * @param string $reservation_id can be a token_id or pos_book_id
     * @param string $as specify the type of $reservation_id
     * @return bool true if the reservation is checked, otherwise false
     * @example PACKAGE_DIR/Examples/checkReservation.php
     */
    public function checkReservation($reservation_id, $as = self::_RESERVATION_ID_AS_TOKEN)
    {
        $this->mti = self::_RESA_CHECKING_MTI;
        $this->tc = self::_RESA_CHECKING_TC;

        $this->transmission_datetime = date(self::_DATE_FORMAT);
        $this->acquirer_public_key = ($this->sender_mac_index == '1') ? $this->acquirer_public_input_key_1 : $this->acquirer_public_input_key_2;

        $this->sender_mac = $this->getSenderMac(
            $this->pos_id,
            $this->transmission_datetime,
            $this->transaction_amount,
            $this->mti,
            $this->tc,
            ($as == self::_RESERVATION_ID_AS_TOKEN) ? $reservation_id : '',
            $this->acquirer_public_key
        );

        return $this->initRequest($this->request_required_fields['resa_check']);
    }

    /**
     * Ping PAYEXPRESS online services
     *
     * This service allows the merchant site to test at any time the availability
     * of online services PAYEXPRESS.
     *
     * @return bool true if the ping succeed, otherwise false
     * @example PACKAGE_DIR/Examples/ping.php
     */
    public function ping()
    {
        $this->mti = self::_PING_MTI;
        $this->tc = self::_PING_TC;

        $this->transmission_datetime = date(self::_DATE_FORMAT);
        $this->acquirer_public_key = ($this->sender_mac_index == '1') ? $this->acquirer_public_input_key_1 : $this->acquirer_public_input_key_2;

        $this->sender_mac = $this->getSenderMac(
            $this->pos_id,
            $this->transmission_datetime,
            $this->transaction_amount,
            $this->mti,
            $this->tc,
            $this->acquirer_public_key
        );

        return $this->initRequest($this->request_required_fields['ping']);
    }

    /**
     * Sign On PAYEXPRESS online services
     *
     * This service allows the merchant site to sign on to the PAYEXPRESS online services
     *
     * @return bool true if the sign on succeed, otherwise false
     * @example PACKAGE_DIR/Examples/signOn.php
     */
    public function signOn()
    {
        $this->mti = self::_SIGN_ON_MTI;
        $this->tc = self::_SIGN_ON_TC;

        $this->transmission_datetime = date(self::_DATE_FORMAT);
        $this->acquirer_public_key = ($this->sender_mac_index == '1') ? $this->acquirer_public_input_key_1 : $this->acquirer_public_input_key_2;

        $this->sender_mac = $this->getSenderMac(
            $this->pos_id,
            $this->transmission_datetime,
            $this->transaction_amount,
            $this->mti,
            $this->tc,
            $this->acquirer_public_key
        );

        return $this->initRequest($this->request_required_fields['signOn']);
    }

    /**
     * Sign Off PAYEXPRESS online services
     *
     * This service allows the merchant site to sign off to the PAYEXPRESS online services
     *
     * @return bool true if the sign off succeed, otherwise false
     * @example PACKAGE_DIR/Examples/signOff.php
     */
    public function signOff()
    {
        $this->mti = self::_SIGN_OFF_MTI;
        $this->tc = self::_SIGN_OFF_TC;

        $this->transmission_datetime = date(self::_DATE_FORMAT);
        $this->acquirer_public_key = ($this->sender_mac_index == '1') ? $this->acquirer_public_input_key_1 : $this->acquirer_public_input_key_2;

        $this->sender_mac = $this->getSenderMac(
            $this->pos_id,
            $this->transmission_datetime,
            $this->transaction_amount,
            $this->mti,
            $this->tc,
            $this->acquirer_public_key
        );

        return $this->initRequest($this->request_required_fields['signOff']);
    }

    /**
     * Check if is a callback checking
     *
     * @param array $data POST data
     * @return bool true if is a Checking callback, otherwise false
     * @example ../Examples/callbackChecking.php
     */
    public function callbackChecking($data)
    {
        return $this->checkCallbackData($data, self::_CALLBACK_RESA_CHECKING_MTI, self::_RESA_CHECKING_TC);
    }

    /**
     * Check if is a callback payment
     *
     * @param array $data POST data
     * @return bool true if is a Checking payment, otherwise false
     * @example ../Examples/callbackPayment.php
     */
    public function callbackPayment($data)
    {
        return $this->checkCallbackData($data, self::_CALLBACK_TRX_PAYMENT_MTI, self::_CALLBACK_TRX_PAYMENT_TC);
    }

    /**
     * Check if is a callback reversal
     *
     * @param array $data POST data
     * @return bool true if is a Checking reversal, otherwise false
     * @example ../Examples/callbackReversal.php
     */
    public function callbackReversal($data)
    {
        return $this->checkCallbackData($data, self::_CALLBACK_TRX_REVERSAL_MTI, self::_CALLBACK_TRX_REVERSAL_TC);
    }

    /**
     * Check if is a callback advice
     *
     * @param array $data POST data
     * @return bool true if is a Checking advice, otherwise false
     * @example ../Examples/callbackAdvice.php
     */
    public function callbackAdvice($data)
    {
        return $this->checkCallbackData($data, self::_CALLBACK_RESA_ADVICE_MTI, self::_CALLBACK_RESA_ADVICE_TC);
    }

    /**
     * Return checking response
     *
     * @param $data
     */
    public function returnCheckingResponse($data)
    {
        $this->mti = self::_CALLBACK_RESA_CHECKING_RESPONSE_MTI;
        $this->tc = self::_CALLBACK_RESA_CHECKING_RESPONSE_TC;

        $this->transmission_datetime = date(self::_DATE_FORMAT);
        $this->acquirer_public_key = ($this->sender_mac_index == '1') ? $this->acquirer_public_input_key_1 : $this->acquirer_public_input_key_2;

        $this->sender_mac = $this->getSenderMac(
            $data['network_code'],
            $this->pos_id,
            $this->transmission_datetime,
            $data['transaction_amount'],
            $this->mti,
            $this->tc,
            $data['token'],
            $this->response_code,
            $this->acquirer_public_key
        );

        $data['mti'] = $this->mti;
        $data['tc'] = $this->tc;
        $data['transmission_datetime'] = $this->transmission_datetime;
        $data['pos_id'] = $this->pos_id;
        $data['response_code'] = $this->response_code;
        $data['response_message'] = $this->response_message;
		$data['sender_mac'] = $this->sender_mac;

        return $data;
    }

    /**
     * Return payment response
     *
     * @param $data
     */
    public function returnPaymentResponse($data)
    {
        $this->mti = self::_CALLBACK_TRX_PAYMENT_RESPONSE_MTI;
        $this->tc = self::_CALLBACK_TRX_PAYMENT_RESPONSE_TC;

        $this->transmission_datetime = date(self::_DATE_FORMAT);
        $this->acquirer_public_key = ($this->sender_mac_index == '1') ? $this->acquirer_public_input_key_1 : $this->acquirer_public_input_key_2;

        $this->sender_mac = $this->getSenderMac(
            $data['network_code'],
            $this->pos_id,
            $this->transmission_datetime,
            $data['transaction_amount'],
            $this->mti,
            $this->tc,
            $data['token'],
            $this->response_code,
            $this->acquirer_public_key
        );

        $data['mti'] = $this->mti;
        $data['tc'] = $this->tc;
        $data['transmission_datetime'] = $this->transmission_datetime;
        $data['pos_id'] = $this->pos_id;
        $data['response_code'] = $this->response_code;
        $data['response_message'] = $this->response_message;
		$data['sender_mac'] = $this->sender_mac;

        return $data;
    }

    /**
     * Return reversal response
     *
     * @param $data
     */
    public function returnReversalResponse($data)
    {
        $this->mti = self::_CALLBACK_TRX_REVERSAL_RESPONSE_MTI;
        $this->tc = self::_CALLBACK_TRX_REVERSAL_RESPONSE_TC;

        $this->transmission_datetime = date(self::_DATE_FORMAT);
        $this->acquirer_public_key = ($this->sender_mac_index == '1') ? $this->acquirer_public_input_key_1 : $this->acquirer_public_input_key_2;

        $this->sender_mac = $this->getSenderMac(
            $data['network_code'],
            $this->pos_id,
            $this->transmission_datetime,
            $data['transaction_amount'],
            $this->mti,
            $this->tc,
            $data['token'],
            $this->response_code,
            $this->acquirer_public_key
        );

        $data['mti'] = $this->mti;
        $data['tc'] = $this->tc;
        $data['transmission_datetime'] = $this->transmission_datetime;
        $data['pos_id'] = $this->pos_id;
        $data['response_code'] = $this->response_code;
        $data['response_message'] = $this->response_message;
		$data['sender_mac'] = $this->sender_mac;

		return $data;
    }

    /**
     * Return advice response
     *
     * @param $data
     */
    public function returnAdviceResponse($data)
    {
        $this->mti = self::_CALLBACK_RESA_ADVICE_RESPONSE_MTI;
        $this->tc = self::_CALLBACK_RESA_ADVICE_RESPONSE_TC;

        $this->transmission_datetime = date(self::_DATE_FORMAT);
        $this->acquirer_public_key = ($this->sender_mac_index == '1') ? $this->acquirer_public_input_key_1 : $this->acquirer_public_input_key_2;

        $this->sender_mac = $this->getSenderMac(
            $data['network_code'],
            $this->pos_id,
            $this->transmission_datetime,
            $data['transaction_amount'],
            $this->mti,
            $this->tc,
            $data['token'],
            $this->response_code,
            $this->acquirer_public_key
        );

        $data['mti'] = $this->mti;
        $data['tc'] = $this->tc;
        $data['transmission_datetime'] = $this->transmission_datetime;
        $data['pos_id'] = $this->pos_id;
        $data['response_code'] = $this->response_code;
        $data['response_message'] = $this->response_message;
		$data['sender_mac'] = $this->sender_mac;

        return $data;
    }

    /**
     * @param $data
     * @return string
     */
    private function sendJsonResponse($data)
    {
        header('Content-type: application/json');
        return json_encode($data);
    }

    /**
     * @param array $fields_required
     * @return bool
     */
    private function initRequest($fields_required)
    {
        if (!$this->validate($fields_required))
            return false;

        $result = $this->sendRequest();

        return $this->checkResponse($result);
    }

    /**
     * @param array $fields_required
     * @return bool
     */
    private function validate($fields_required)
    {
        $errors = $this->validateFields($fields_required);

        if (sizeof($errors) > 0) {
            foreach ($errors as $error) {
                $this->error_message .= $error . " * ";
            }
            return false;
        }

        return true;
    }

    /**
     * @param $fields_required
     * @return array
     */
    private function validateFields($fields_required)
    {
        $errors = array();

        //Check required fields
        foreach ($fields_required as $field)
        {
            if (EXPC2PValidate::isEmpty($this->{$field}) AND (!is_numeric($this->{$field})))
                $errors[] = 'Empty required field : '. $field;
        }

        //Check fields size
        foreach ($this->fields_size as $field => $size)
        {
            if (isset($this->{$field}) AND EXPC2PValidate::strlen($this->{$field}) > $size)
                $errors[] = 'Max length exceeded : '. $field .' > ' . $size;
        }

        //Check fields type
        foreach ($this->fields_validate as $field => $method)
        {
            if (!EXPC2PValidate::isEmpty($this->{$field}) AND !call_user_func(array('EXPC2PValidate', $method), $this->{$field}))
                $errors[] = $field . ' : must be a ' . substr($method, 2);
        }

        return $errors;
    }

    /**
     * @param array $result
     * @return bool
     */
    private function checkResponse($result)
    {
        if ($result == null && !is_array($result))
            return false;

        if ($result['response_code'] != '0000')
        {
            $this->error_message = 'response message : ' . $result['response_message'] . '. response code : ' . $result['response_code'];
            return false;
        }

        $this->result = $result;
        return true;
    }

    /**
     * @param array $data
     * @param string $mti
     * @param string $tc
     * @return bool
     */
    private function checkCallbackData($data, $mti, $tc)
    {
        if(is_null($data) && !is_array($data))
        {
            $this->error_message = 'POST DATA MUST BE AN ARRAY';
            return false;
        }

        if(!isset($data['mti']) && $data['mti'] != $mti
            && !isset($data['tc']) && $data['tc'] != $tc )
        {
            $this->error_message = 'MTI OR TC IS NOT SET OR INVALID';
            return false;
        }
        // Recalculate the sender mac sent in the request data with own acquirer public output key
        $this->sender_mac = $this->getSenderMac(
            (isset($data['network_code']) && !is_null($data['network_code'])) ? $data['network_code'] : '',
            (isset($data['pos_id']) && !is_null($data['pos_id'])) ? $data['pos_id'] : '',
            (isset($data['transmission_datetime']) && !is_null($data['transmission_datetime'])) ? $data['transmission_datetime'] : '',
            (isset($data['transaction_amount']) && !is_null($data['transaction_amount'])) ? $data['transaction_amount'] : '',
            (isset($data['mti']) && !is_null($data['mti'])) ? $data['mti'] : '',
            (isset($data['tc']) && !is_null($data['tc'])) ? $data['tc'] : '',
            (isset($data['token']) && !is_null($data['token'])) ? $data['token'] : '',
            (isset($data['response_code']) && !is_null($data['response_code'])) ? $data['response_code'] : '',
            ($data['sender_mac_index'] == '1') ? $this->acquirer_public_output_key_1 : $this->acquirer_public_output_key_2
        );
        // Check if is not the same
        if($this->sender_mac != $data['sender_mac'])
        {
            $this->error_message = 'SENDER MAC ERROR';
            return false;
        }

        $this->callback_request_data = $data;
        return true;
    }

    /**
     * @return mixed|null
     */
    private function sendRequest()
    {
        $data = [];

        foreach ($this->webservice_fields as $field)
        {
            $data[$field] = (!empty($this->{$field}) && $this->{$field} !== '0') ? $this->{$field} : '';
        }

        $result = $this->doPost($this->webservice_url, http_build_query($data));
        return $result;
    }

    /**
     * @return string
     */
    private function getSenderMac()
    {
        $params = func_get_args();
        return strtoupper(md5(implode('', $params)));
    }

    /**
     * @param $url
     * @param $data
     * @param bool $assoc
     * @return mixed|null
     */
    private function doPost($url, $data, $assoc = true) {
        return $this->doHTTPRequest("POST", $url, $data, $assoc);
    }

    /**
     * @param $type
     * @param $url
     * @param $data
     * @param bool $assoc
     * @return mixed|null
     */
    private function doHTTPRequest($type, $url, $data, $assoc = true) {
        $curl = curl_init();

        if ($type === "GET") {
            // In that case, $data is the array of params to add in the URL
            if (is_array($data) && count($data) > 0) {
                $urlParams = array();
                foreach ($data as $param => $value) {
                    $urlParams[] = urlencode($param) . "=" . urlencode($value);
                }
                if (count($urlParams) > 0) {
                    $url .= "?" . implode("&", $urlParams);
                }
            }
        } elseif ($type === "POST") {
            // In that case, $data is the body of the request
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        } else {
            $this->error_message = "Bad HTTP method specified.";
            return null;
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		//Force sslCAFile
		if ($this->sslCAFile != null) {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($curl, CURLOPT_CAINFO, $this->sslCAFile);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		} else {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		}
		
		// Extra Curl Options
		foreach ($this->extraCurlOptions as $name => $value) {
			curl_setopt($curl, $name, $value);
		}
		
		if (!isset($this->extraCurlOptions[CURLOPT_SSL_CIPHER_LIST])) {
			// Enable the default RC4 ciphers as it is disabled in recent CURL releases
			// See DEFAULT_CIPHER_SELECTION in lib/vtls/openssl.h in CURL sources
			curl_setopt($curl, CURLOPT_SSL_CIPHER_LIST, "ALL!EXPORT!EXPORT40!EXPORT56!aNULL!LOW");
		}
		
        $json = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);
        curl_close($curl);

        if ($httpCode != 200) {
            $this->error_message = "Received HTTP code " . $httpCode . " from connect2pay. CURL ERROR : ". $curl_error;
        } else {
            if ($json !== false) {

                $result = json_decode($json, $assoc);

                if ($result != null) {
                    return $result;
                } else {
                    $this->error_message = "JSON decoding error.";
                }
            }
        }

        return null;
    }



    ///////////////////////////////////////////////
    /////         GETTERS AND SETTERS         /////
    ///////////////////////////////////////////////

    /**
     * @return float
     */
    public function getWsVersion()
    {
        return $this->ws_version;
    }

    /**
     * @param float $ws_version
     */
    public function setWsVersion($ws_version)
    {
        $this->ws_version = $ws_version;
    }

    /**
     * @return string
     */
    public function getAcquirerId()
    {
        return $this->acquirer_id;
    }

    /**
     * @param string $acquirer_id
     */
    public function setAcquirerId($acquirer_id)
    {
        $this->acquirer_id = $acquirer_id;
    }

    /**
     * @return string
     */
    public function getPosId()
    {
        return $this->pos_id;
    }

    /**
     * @param string $pos_id
     */
    public function setPosId($pos_id)
    {
        $this->pos_id = $pos_id;
    }


    /**
     * @return string
     */
    public function getPosName()
    {
        return $this->pos_name;
    }

    /**
     * @param string $pos_name
     */
    public function setPosName($pos_name)
    {
        $this->pos_name = $pos_name;
    }

    /**
     * @return string
     */
    public function getProviderCode()
    {
        return $this->provider_code;
    }

    /**
     * @param string $provider_code
     */
    public function setProviderCode($provider_code)
    {
        $this->provider_code = $provider_code;
    }

    /**
     * @return string
     */
    public function getNetworkCode()
    {
        return $this->network_code;
    }

    /**
     * @param string $network_code
     */
    public function setNetworkCode($network_code)
    {
        $this->network_code = $network_code;
    }

    /**
     * @return string
     */
    public function getPosBookId()
    {
        return $this->pos_book_id;
    }

    /**
     * @param string $pos_book_id
     */
    public function setPosBookId($pos_book_id)
    {
        $this->pos_book_id = $pos_book_id;
    }

    /**
     * @return string
     */
    public function getPosBookDate()
    {
        return $this->pos_book_date;
    }

    /**
     * @param string $pos_book_date
     */
    public function setPosBookDate($pos_book_date)
    {
        $this->pos_book_date = date(self::_DATE_FORMAT, strtotime($pos_book_date));
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getCustomerTitle()
    {
        return $this->customer_title;
    }

    /**
     * @param string $customer_title
     */
    public function setCustomerTitle($customer_title)
    {
        $this->customer_title = $customer_title;
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        return $this->customer_name;
    }

    /**
     * @param string $customer_name
     */
    public function setCustomerName($customer_name)
    {
        $this->customer_name = $customer_name;
    }

    /**
     * @return string
     */
    public function getCustomerIdType()
    {
        return $this->customer_id_type;
    }

    /**
     * @param string $customer_id_type
     */
    public function setCustomerIdType($customer_id_type)
    {
        $this->customer_id_type = $customer_id_type;
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * @param mixed $customer_id
     */
    public function setCustomerId($customer_id)
    {
        $this->customer_id = $customer_id;
    }

    /**
     * @return string
     */
    public function getCustomerPhone()
    {
        return $this->customer_phone;
    }

    /**
     * @param string $customer_phone
     */
    public function setCustomerPhone($customer_phone)
    {
        $this->customer_phone = $customer_phone;
    }

    /**
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->customer_email;
    }

    /**
     * @param string $customer_email
     */
    public function setCustomerEmail($customer_email)
    {
        $this->customer_email = $customer_email;
    }

    /**
     * @return string
     */
    public function getCustomerBirthDate()
    {
        return $this->customer_birth_date;
    }

    /**
     * @param string $customer_birth_date
     */
    public function setCustomerBirthDate($customer_birth_date)
    {
        $this->customer_birth_date = date(self::_DATE_FORMAT, strtotime($customer_birth_date));
    }

    /**
     * @return string
     */
    public function getCustomerZipCode()
    {
        return $this->customer_zip_code;
    }

    /**
     * @param string $customer_zip_code
     */
    public function setCustomerZipCode($customer_zip_code)
    {
        $this->customer_zip_code = $customer_zip_code;
    }

    /**
     * @return string
     */
    public function getCustomerAddress()
    {
        return $this->customer_address;
    }

    /**
     * @param string $customer_address
     */
    public function setCustomerAddress($customer_address)
    {
        $this->customer_address = $customer_address;
    }

    /**
     * @return string
     */
    public function getCustomerCountryCode()
    {
        return $this->customer_country_code;
    }

    /**
     * @param string $customer_country_code
     */
    public function setCustomerCountryCode($customer_country_code)
    {
        $this->customer_country_code = $customer_country_code;
    }

    /**
     * @return string
     */
    public function getCustomerCityId()
    {
        return $this->customer_city_id;
    }

    /**
     * @param string $customer_city_id
     */
    public function setCustomerCityId($customer_city_id)
    {
        $this->customer_city_id = $customer_city_id;
    }

    /**
     * @return string
     */
    public function getSendCustomerMail()
    {
        return $this->send_customer_mail;
    }

    /**
     * @param string $send_customer_mail
     */
    public function setSendCustomerMail($send_customer_mail)
    {
        $this->send_customer_mail = $send_customer_mail;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getGoodServiceDesc()
    {
        return $this->good_service_desc;
    }

    /**
     * @param string $good_service_desc
     */
    public function setGoodServiceDesc($good_service_desc)
    {
        $this->good_service_desc = $good_service_desc;
    }

    /**
     * @return string
     */
    public function getTransactionDatetime()
    {
        return $this->transaction_datetime;
    }

    /**
     * @param string $transaction_datetime
     */
    public function setTransactionDatetime($transaction_datetime)
    {
        $this->transaction_datetime = date(self::_DATE_FORMAT, strtotime($transaction_datetime));
    }

    /**
     * @return string
     */
    public function getValidityReservationDatetime()
    {
        return $this->validity_reservation_datetime;
    }

    /**
     * @param string $validity_reservation_datetime
     */
    public function setValidityReservationDatetime($validity_reservation_datetime)
    {
        $this->validity_reservation_datetime = date(self::_DATE_FORMAT, strtotime($validity_reservation_datetime));
    }

    /**
     * @return string
     */
    public function getValidityReservationStatus()
    {
        return $this->validity_reservation_status;
    }

    /**
     * @param string $validity_reservation_status
     */
    public function setValidityReservationStatus($validity_reservation_status)
    {
        $this->validity_reservation_status = $validity_reservation_status;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currency_code;
    }

    /**
     * @param string $currency_code
     */
    public function setCurrencyCode($currency_code)
    {
        $this->currency_code = $currency_code;
    }

    /**
     * @return float
     */
    public function getTransactionAmount()
    {
        return $this->transaction_amount;
    }

    /**
     * @param float $transaction_amount
     */
    public function setTransactionAmount($transaction_amount)
    {
        $this->transaction_amount = number_format((float)$transaction_amount, 2, '.', '');
    }


    /**
     * @return string
     */
    public function getUrl1()
    {
        return $this->url1;
    }

    /**
     * @param string $url1
     */
    public function setUrl1($url1)
    {
        $this->url1 = $url1;
    }

    /**
     * @return string
     */
    public function getUrl2()
    {
        return $this->url2;
    }

    /**
     * @param string $url2
     */
    public function setUrl2($url2)
    {
        $this->url2 = $url2;
    }

    /**
     * @return string
     */
    public function getUrl3()
    {
        return $this->url3;
    }

    /**
     * @param string $url3
     */
    public function setUrl3($url3)
    {
        $this->url3 = $url3;
    }

    /**
     * @return string
     */
    public function getUrl4()
    {
        return $this->url4;
    }

    /**
     * @param string $url4
     */
    public function setUrl4($url4)
    {
        $this->url4 = $url4;
    }

    /**
     * @return string
     */
    public function getUrl5()
    {
        return $this->url5;
    }

    /**
     * @param string $url5
     */
    public function setUrl5($url5)
    {
        $this->url5 = $url5;
    }

    /**
     * @return string
     */
    public function getPosPrivateData()
    {
        return $this->pos_private_data;
    }

    /**
     * @param string $pos_private_data
     */
    public function setPosPrivateData($pos_private_data)
    {
        $this->pos_private_data = $pos_private_data;
    }

    /**
     * @return string
     */
    public function getResponseCode()
    {
        return $this->response_code;
    }

    /**
     * @param $response_code
     */
    public function setResponseCode($response_code)
    {
        $this->response_code = $response_code;
    }

    /**
     * @return string
     */
    public function getResponseMessage()
    {
        return $this->response_message;
    }

    /**
     * @param $response_message
     */
    public function setResponseMessage($response_message)
    {
        $this->response_message = $response_message;
    }

    /**
     * @return string
     */
    public function getSenderMacIndex()
    {
        return $this->sender_mac_index;
    }

    /**
     * @param string $sender_mac_index
     */
    public function setSenderMacIndex($sender_mac_index)
    {
        $this->sender_mac_index = $sender_mac_index;
    }

    /**
     * @return string
     */
    public function getAcquirerPublicInputKey1()
    {
        return $this->acquirer_public_input_key_1;
    }

    /**
     * @param string $chksum_public_input_key_1
     */
    public function setAcquirerPublicInputKey1($chksum_public_input_key_1)
    {
        $this->acquirer_public_input_key_1 = $chksum_public_input_key_1;
    }

    /**
     * @return string
     */
    public function getAcquirerPublicInputKey2()
    {
        return $this->acquirer_public_input_key_2;
    }

    /**
     * @param string $chksum_public_input_key_2
     */
    public function setAcquirerPublicInputKey2($chksum_public_input_key_2)
    {
        $this->acquirer_public_input_key_2 = $chksum_public_input_key_2;
    }

    /**
     * @return string
     */
    public function getAcquirerPublicOutputKey1()
    {
        return $this->acquirer_public_output_key_1;
    }

    /**
     * @param string $chksum_public_output_key_1
     */
    public function setAcquirerPublicOutputKey1($chksum_public_output_key_1)
    {
        $this->acquirer_public_output_key_1 = $chksum_public_output_key_1;
    }

    /**
     * @return string
     */
    public function getAcquirerPublicOutputKey2()
    {
        return $this->acquirer_public_output_key_2;
    }

    /**
     * @param string $chksum_public_output_key_2
     */
    public function setAcquirerPublicOutputKey2($chksum_public_output_key_2)
    {
        $this->acquirer_public_output_key_2 = $chksum_public_output_key_2;
    }


    /**
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return array
     */
    public function getCallbackRequestData()
    {
        return $this->callback_request_data;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->error_message;
    }
}


/**
 * Helper to manipulate amount in different currencies.
 * Permits to convert amount between different currencies
 * and get rates in real time from Yahoo Web service.
 *
 * @author Jérôme Schell <jsh@payzone.ma>
 * @package Connect2PayexpressPos
 * @copyright 2011 Payzone
 *
 */
 	class ExpConnect2PayCurrencyHelper {
		// The base address to fetch currency rates
		private static $YAHOO_SERVICE_URL = "http://download.finance.yahoo.com/d/quotes.csv";

		// Optional proxy to use for outgoing request
		private static $proxy_host = null;
		private static $proxy_port = null;
		private static $proxy_username = null;
		private static $proxy_password = null;

		private static $currencies = array(
			"AUD" => array("currency" => "Australian Dollar", "code" => "036", "symbol" => "$"),
			"CAD" => array("currency" => "Canadian Dollar", "code" => "124", "symbol" => "$"),
			"CHF" => array("currency" => "Swiss Franc", "code" => "756", "symbol" => "CHF"),
			"DKK" => array("currency" => "Danish Krone", "code" => "208", "symbol" => "kr"),
			"EUR" => array("currency" => "Euro", "code" => "978", "symbol" => "€"),
			"GBP" => array("currency" => "Pound Sterling", "code" => "826", "symbol" => "£"),
			"HKD" => array("currency" => "Hong Kong Dollar", "code" => "344", "symbol" => "$"),
			"JPY" => array("currency" => "Yen", "code" => "392", "symbol" => "¥"),
			"MXN" => array("currency" => "Mexican Peso", "code" => "484", "symbol" => "$"),
			"NOK" => array("currency" => "Norwegian Krone", "code" => "578", "symbol" => "kr"),
			"SEK" => array("currency" => "Swedish Krona", "code" => "752", "symbol" => "kr"),
			"USD" => array("currency" => "US Dollar", "code" => "840", "symbol" => "$"),
			"MAD" => array("currency" => "Morrocan Dirham", "code" => "504", "symbol" => "MAD"),
		);

		/**
		 * Set the parameter in the case of the use of an outgoing proxy
		 *
		 * @param string $host The proxy host.
		 * @param int $port The proxy port.
		 * @param string $username The proxy username.
		 * @param string $password The proxy password.
		 */
		public static function useProxy($host, $port, $username = null, $password = null) {
			ExpConnect2PayCurrencyHelper::$proxy_host = $host;
			ExpConnect2PayCurrencyHelper::$proxy_port = $port;
			ExpConnect2PayCurrencyHelper::$proxy_username = $username;
			ExpConnect2PayCurrencyHelper::$proxy_password = $password;
		}

		/**
		 * Return the supported currencies array.
		 *
		 * @return Array of all the currencies supported.
		 */
		public static function getCurrencies() {
			return array_keys(ExpConnect2PayCurrencyHelper::$currencies);
		}

		/**
		 * Get a currency alphabetic code according to its numeric code in ISO4217
		 *
		 * @param string $code The numeric code to look for
		 * @return The alphabetic code (like EUR or USD) or null if not found.
		 */
		public static function getISO4217CurrencyFromCode($code) {
			foreach (ExpConnect2PayCurrencyHelper::$currencies as $currency => $data) {
				if ($data["code"] == $code) {
					return $currency;
				}
			}

			return null;
		}

		/**
		 * Return the ISO4217 currency code.
		 *
		 * @param string $currency The currency to look for
		 * @return The ISO4217 code or null if not found
		 */
		public static function getISO4217CurrencyCode($currency) {
			return (array_key_exists($currency, ExpConnect2PayCurrencyHelper::$currencies)) ? ExpConnect2PayCurrencyHelper::$currencies[$currency]["code"] : null;
		}

		/**
		 * Return the currency symbol.
		 *
		 * @param string $currency The currency to look for
		 * @return The currency symbol or null if not found
		 */
		public static function getCurrencySymbol($currency) {
			return (array_key_exists($currency, ExpConnect2PayCurrencyHelper::$currencies)) ? ExpConnect2PayCurrencyHelper::$currencies[$currency]["symbol"] : null;
		}

		/**
		 * Return the currency name.
		 *
		 * @param string $currency The currency to look for
		 * @return The currency name or null if not found
		 */
		public static function getCurrencyName($currency) {
			return (array_key_exists($currency, ExpConnect2PayCurrencyHelper::$currencies)) ? ExpConnect2PayCurrencyHelper::$currencies[$currency]["currency"] : null;
		}

		/**
		 * Get a currency conversion rate from Yahoo webservice.
		 *
		 * @param string $from The source currency
		 * @param string $to The destination currency
		 */
		public static function getRate($from, $to) {
			// Check if currencies exists
			if (! ExpConnect2PayCurrencyHelper::currencyIsAvailable($from) || ! ExpConnect2PayCurrencyHelper::currencyIsAvailable($to)) {
				return null;
			}

			// Build the request URL
			$url = ExpConnect2PayCurrencyHelper::$YAHOO_SERVICE_URL . "?s=" . $from . $to . "=X&f=l1&e=.csv";

			// Do the request
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			if (ExpConnect2PayCurrencyHelper::$proxy_host != null && ExpConnect2PayCurrencyHelper::$proxy_port != null) {
				curl_setopt($curl, CURLOPT_PROXY, ExpConnect2PayCurrencyHelper::$proxy_host);
				curl_setopt($curl, CURLOPT_PROXYPORT, ExpConnect2PayCurrencyHelper::$proxy_port);

				if (ExpConnect2PayCurrencyHelper::$proxy_username != null && ExpConnect2PayCurrencyHelper::$proxy_password != null) {
					curl_setopt($curl, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
					curl_setopt($curl, CURLOPT_PROXYUSERPWD, ExpConnect2PayCurrencyHelper::$proxy_username . ":" . ExpConnect2PayCurrencyHelper::$proxy_password);
				}
			}

			$csv = trim(curl_exec($curl));
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);

			// Parse the CSV, we should only have a number, check this
			if ($httpCode == 200 && preg_match('/^[0-9.]+$/', $csv)) {
				return $csv;
			}

			return null;
		}

		/**
		 * Convert an amount from one currency to another
		 *
		 * @param int $amount The amount to convert
		 * @param string $from The source currency
		 * @param string $to The destination currency
		 * @param boolean $cent Specifies if the amount is in cent (default true)
		 * @return The converted amount or null in case of error
		 */
		public static function convert($amount, $from, $to, $cent = true) {
			// Get the conversion rate
			$rate = ExpConnect2PayCurrencyHelper::getRate($from, $to);

			if ($rate != null) {
				$convert = $amount * $rate;

				// If the amount was in cent, truncate the digit after the comma
				// else round the result to 2 digits only
				return ($cent) ? round($convert, 0) : round($convert, 2);
			}

			return null;
		}

		private static function currencyIsAvailable($currency) {
			return array_key_exists($currency, ExpConnect2PayCurrencyHelper::$currencies);
		}
	}

/**
 * Helper class to get country by different ISO 3166-1 code
 *
 * @author Mehdi Atraimche <mehdi.atraimche@gmail.com>
 * @see http://en.wikipedia.org/wiki/ISO_3166-1
 * @package Connect2PayexpressPos
 */

class CountryIsoCode{

    /**
     * All countries in array
     * Thank's to Ryan Deas for the array
     * @var array
     * @see https://gist.github.com/DrRoach/a0562291953c65c71686
     */
    private static $countries = array (
        0 => array ('country' => 'Afghanistan', 'alpha_2' => 'AF', 'alpha_3' => 'AFG', 'digit_code' => '004'),
        1 => array ('country' => 'Albania', 'alpha_2' => 'AL', 'alpha_3' => 'ALB', 'digit_code' => '008'),
        2 => array ('country' => 'Algeria', 'alpha_2' => 'DZ', 'alpha_3' => 'DZA', 'digit_code' => '012'),
        3 => array ('country' => 'American Samoa', 'alpha_2' => 'AS', 'alpha_3' => 'ASM', 'digit_code' => '016'),
        4 => array ('country' => 'Andorra', 'alpha_2' => 'AD', 'alpha_3' => 'AND', 'digit_code' => '020'),
        5 => array ('country' => 'Angola', 'alpha_2' => 'AO', 'alpha_3' => 'AGO', 'digit_code' => '024'),
        6 => array ('country' => 'Anguilla', 'alpha_2' => 'AI', 'alpha_3' => 'AIA', 'digit_code' => '660'),
        7 => array ('country' => 'Antarctica', 'alpha_2' => 'AQ', 'alpha_3' => 'ATA', 'digit_code' => '010'),
        8 => array ('country' => 'Antigua and Barbuda', 'alpha_2' => 'AG', 'alpha_3' => 'ATG', 'digit_code' => '028'),
        9 => array ('country' => 'Argentina', 'alpha_2' => 'AR', 'alpha_3' => 'ARG', 'digit_code' => '032'),
        10 => array ('country' => 'Armenia', 'alpha_2' => 'AM', 'alpha_3' => 'ARM', 'digit_code' => '051'),
        11 => array ('country' => 'Aruba', 'alpha_2' => 'AW', 'alpha_3' => 'ABW', 'digit_code' => '533'),
        12 => array ('country' => 'Australia', 'alpha_2' => 'AU', 'alpha_3' => 'AUS', 'digit_code' => '036'),
        13 => array ('country' => 'Austria', 'alpha_2' => 'AT', 'alpha_3' => 'AUT', 'digit_code' => '040'),
        14 => array ('country' => 'Azerbaijan', 'alpha_2' => 'AZ', 'alpha_3' => 'AZE', 'digit_code' => '031'),
        15 => array ('country' => 'Bahamas', 'alpha_2' => 'BS', 'alpha_3' => 'BHS', 'digit_code' => '044'),
        16 => array ('country' => 'Bahrain', 'alpha_2' => 'BH', 'alpha_3' => 'BHR', 'digit_code' => '048'),
        17 => array ('country' => 'Bangladesh', 'alpha_2' => 'BD', 'alpha_3' => 'BGD', 'digit_code' => '050'),
        18 => array ('country' => 'Barbados', 'alpha_2' => 'BB', 'alpha_3' => 'BRB', 'digit_code' => '052'),
        19 => array ('country' => 'Belarus', 'alpha_2' => 'BY', 'alpha_3' => 'BLR', 'digit_code' => '112'),
        20 => array ('country' => 'Belgium', 'alpha_2' => 'BE', 'alpha_3' => 'BEL', 'digit_code' => '056'),
        21 => array ('country' => 'Belize', 'alpha_2' => 'BZ', 'alpha_3' => 'BLZ', 'digit_code' => '084'),
        22 => array ('country' => 'Benin', 'alpha_2' => 'BJ', 'alpha_3' => 'BEN', 'digit_code' => '204'),
        23 => array ('country' => 'Bermuda', 'alpha_2' => 'BM', 'alpha_3' => 'BMU', 'digit_code' => '060'),
        24 => array ('country' => 'Bhutan', 'alpha_2' => 'BT', 'alpha_3' => 'BTN', 'digit_code' => '064'),
        25 => array ('country' => 'Bosnia and Herzegovina', 'alpha_2' => 'BA', 'alpha_3' => 'BIH', 'digit_code' => '070'),
        26 => array ('country' => 'Botswana', 'alpha_2' => 'BW', 'alpha_3' => 'BWA', 'digit_code' => '072'),
        27 => array ('country' => 'Bouvet Island', 'alpha_2' => 'BV', 'alpha_3' => 'BVT', 'digit_code' => '074'),
        28 => array ('country' => 'Brazil', 'alpha_2' => 'BR', 'alpha_3' => 'BRA', 'digit_code' => '076'),
        29 => array ('country' => 'British Indian Ocean Territory', 'alpha_2' => 'IO', 'alpha_3' => 'IOT', 'digit_code' => '086'),
        30 => array ('country' => 'Brunei Darussalam', 'alpha_2' => 'BN', 'alpha_3' => 'BRN', 'digit_code' => '096'),
        31 => array ('country' => 'Bulgaria', 'alpha_2' => 'BG', 'alpha_3' => 'BGR', 'digit_code' => '100'),
        32 => array ('country' => 'Burkina Faso', 'alpha_2' => 'BF', 'alpha_3' => 'BFA', 'digit_code' => '854'),
        33 => array ('country' => 'Burundi', 'alpha_2' => 'BI', 'alpha_3' => 'BDI', 'digit_code' => '108'),
        34 => array ('country' => 'Cambodia', 'alpha_2' => 'KH', 'alpha_3' => 'KHM', 'digit_code' => '116'),
        35 => array ('country' => 'Cameroon', 'alpha_2' => 'CM', 'alpha_3' => 'CMR', 'digit_code' => '120'),
        36 => array ('country' => 'Canada', 'alpha_2' => 'CA', 'alpha_3' => 'CAN', 'digit_code' => '124'),
        37 => array ('country' => 'Cabo Verde', 'alpha_2' => 'CV', 'alpha_3' => 'CPV', 'digit_code' => '132'),
        38 => array ('country' => 'Cayman Islands', 'alpha_2' => 'KY', 'alpha_3' => 'CYM', 'digit_code' => '136'),
        39 => array ('country' => 'Central African Republic', 'alpha_2' => 'CF', 'alpha_3' => 'CAF', 'digit_code' => '140'),
        40 => array ('country' => 'Chad', 'alpha_2' => 'TD', 'alpha_3' => 'TCD', 'digit_code' => '148'),
        41 => array ('country' => 'Chile', 'alpha_2' => 'CL', 'alpha_3' => 'CHL', 'digit_code' => '152'),
        42 => array ('country' => 'China', 'alpha_2' => 'CN', 'alpha_3' => 'CHN', 'digit_code' => '156'),
        43 => array ('country' => 'Christmas Island', 'alpha_2' => 'CX', 'alpha_3' => 'CXR', 'digit_code' => '162'),
        44 => array ('country' => 'Colombia', 'alpha_2' => 'CO', 'alpha_3' => 'COL', 'digit_code' => '170'),
        45 => array ('country' => 'Comoros', 'alpha_2' => 'KM', 'alpha_3' => 'COM', 'digit_code' => '174'),
        46 => array ('country' => 'Cook Islands', 'alpha_2' => 'CK', 'alpha_3' => 'COK', 'digit_code' => '184'),
        47 => array ('country' => 'Costa Rica', 'alpha_2' => 'CR', 'alpha_3' => 'CRI', 'digit_code' => '188'),
        48 => array ('country' => 'Croatia', 'alpha_2' => 'HR', 'alpha_3' => 'HRV', 'digit_code' => '191'),
        49 => array ('country' => 'Cuba', 'alpha_2' => 'CU', 'alpha_3' => 'CUB', 'digit_code' => '192'),
        50 => array ('country' => 'Cyprus', 'alpha_2' => 'CY', 'alpha_3' => 'CYP', 'digit_code' => '196'),
        51 => array ('country' => 'Czech Republic', 'alpha_2' => 'CZ', 'alpha_3' => 'CZE', 'digit_code' => '203'),
        52 => array ('country' => 'Denmark', 'alpha_2' => 'DK', 'alpha_3' => 'DNK', 'digit_code' => '208'),
        53 => array ('country' => 'Djibouti', 'alpha_2' => 'DJ', 'alpha_3' => 'DJI', 'digit_code' => '262'),
        54 => array ('country' => 'Dominica', 'alpha_2' => 'DM', 'alpha_3' => 'DMA', 'digit_code' => '212'),
        55 => array ('country' => 'Dominican Republic', 'alpha_2' => 'DO', 'alpha_3' => 'DOM', 'digit_code' => '214'),
        56 => array ('country' => 'Ecuador', 'alpha_2' => 'EC', 'alpha_3' => 'ECU', 'digit_code' => '218'),
        57 => array ('country' => 'Egypt', 'alpha_2' => 'EG', 'alpha_3' => 'EGY', 'digit_code' => '818'),
        58 => array ('country' => 'El Salvador', 'alpha_2' => 'SV', 'alpha_3' => 'SLV', 'digit_code' => '222'),
        59 => array ('country' => 'Equatorial Guinea', 'alpha_2' => 'GQ', 'alpha_3' => 'GNQ', 'digit_code' => '226'),
        60 => array ('country' => 'Eritrea', 'alpha_2' => 'ER', 'alpha_3' => 'ERI', 'digit_code' => '232'),
        61 => array ('country' => 'Estonia', 'alpha_2' => 'EE', 'alpha_3' => 'EST', 'digit_code' => '233'),
        62 => array ('country' => 'Ethiopia', 'alpha_2' => 'ET', 'alpha_3' => 'ETH', 'digit_code' => '231'),
        63 => array ('country' => 'Faroe Islands', 'alpha_2' => 'FO', 'alpha_3' => 'FRO', 'digit_code' => '234'),
        64 => array ('country' => 'Fiji', 'alpha_2' => 'FJ', 'alpha_3' => 'FJI', 'digit_code' => '242'),
        65 => array ('country' => 'Finland', 'alpha_2' => 'FI', 'alpha_3' => 'FIN', 'digit_code' => '246'),
        66 => array ('country' => 'France', 'alpha_2' => 'FR', 'alpha_3' => 'FRA', 'digit_code' => '250'),
        67 => array ('country' => 'French Guiana', 'alpha_2' => 'GF', 'alpha_3' => 'GUF', 'digit_code' => '254'),
        68 => array ('country' => 'French Polynesia', 'alpha_2' => 'PF', 'alpha_3' => 'PYF', 'digit_code' => '258'),
        69 => array ('country' => 'French Southern Territories', 'alpha_2' => 'TF', 'alpha_3' => 'ATF', 'digit_code' => '260'),
        70 => array ('country' => 'Gabon', 'alpha_2' => 'GA', 'alpha_3' => 'GAB', 'digit_code' => '266'),
        71 => array ('country' => 'Gambia', 'alpha_2' => 'GM', 'alpha_3' => 'GMB', 'digit_code' => '270'),
        72 => array ('country' => 'Germany', 'alpha_2' => 'DE', 'alpha_3' => 'DEU', 'digit_code' => '276'),
        73 => array ('country' => 'Ghana', 'alpha_2' => 'GH', 'alpha_3' => 'GHA', 'digit_code' => '288'),
        74 => array ('country' => 'Gibraltar', 'alpha_2' => 'GI', 'alpha_3' => 'GIB', 'digit_code' => '292'),
        75 => array ('country' => 'Greece', 'alpha_2' => 'GR', 'alpha_3' => 'GRC', 'digit_code' => '300'),
        76 => array ('country' => 'Greenland', 'alpha_2' => 'GL', 'alpha_3' => 'GRL', 'digit_code' => '304'),
        77 => array ('country' => 'Grenada', 'alpha_2' => 'GD', 'alpha_3' => 'GRD', 'digit_code' => '308'),
        78 => array ('country' => 'Guadeloupe', 'alpha_2' => 'GP', 'alpha_3' => 'GLP', 'digit_code' => '312'),
        79 => array ('country' => 'Guam', 'alpha_2' => 'GU', 'alpha_3' => 'GUM', 'digit_code' => '316'),
        80 => array ('country' => 'Guatemala', 'alpha_2' => 'GT', 'alpha_3' => 'GTM', 'digit_code' => '320'),
        81 => array ('country' => 'Guernsey', 'alpha_2' => 'GG', 'alpha_3' => 'GGY', 'digit_code' => '831'),
        82 => array ('country' => 'Guinea', 'alpha_2' => 'GN', 'alpha_3' => 'GIN', 'digit_code' => '324'),
        83 => array ('country' => 'Guyana', 'alpha_2' => 'GY', 'alpha_3' => 'GUY', 'digit_code' => '328'),
        84 => array ('country' => 'Haiti', 'alpha_2' => 'HT', 'alpha_3' => 'HTI', 'digit_code' => '332'),
        85 => array ('country' => 'Heard Island and McDonald Islands', 'alpha_2' => 'HM', 'alpha_3' => 'HMD', 'digit_code' => '334'),
        86 => array ('country' => 'Honduras', 'alpha_2' => 'HN', 'alpha_3' => 'HND', 'digit_code' => '340'),
        87 => array ('country' => 'Hong Kong', 'alpha_2' => 'HK', 'alpha_3' => 'HKG', 'digit_code' => '344'),
        88 => array ('country' => 'Hungary', 'alpha_2' => 'HU', 'alpha_3' => 'HUN', 'digit_code' => '348'),
        89 => array ('country' => 'Iceland', 'alpha_2' => 'IS', 'alpha_3' => 'ISL', 'digit_code' => '352'),
        90 => array ('country' => 'India', 'alpha_2' => 'IN', 'alpha_3' => 'IND', 'digit_code' => '356'),
        91 => array ('country' => 'Indonesia', 'alpha_2' => 'ID', 'alpha_3' => 'IDN', 'digit_code' => '360'),
        92 => array ('country' => 'Iraq', 'alpha_2' => 'IQ', 'alpha_3' => 'IRQ', 'digit_code' => '368'),
        93 => array ('country' => 'Isle of Man', 'alpha_2' => 'IM', 'alpha_3' => 'IMN', 'digit_code' => '833'),
        94 => array ('country' => 'Israel', 'alpha_2' => 'IL', 'alpha_3' => 'ISR', 'digit_code' => '376'),
        95 => array ('country' => 'Italy', 'alpha_2' => 'IT', 'alpha_3' => 'ITA', 'digit_code' => '380'),
        96 => array ('country' => 'Jamaica', 'alpha_2' => 'JM', 'alpha_3' => 'JAM', 'digit_code' => '388'),
        97 => array ('country' => 'Japan', 'alpha_2' => 'JP', 'alpha_3' => 'JPN', 'digit_code' => '392'),
        98 => array ('country' => 'Jersey', 'alpha_2' => 'JE', 'alpha_3' => 'JEY', 'digit_code' => '832'),
        99 => array ('country' => 'Jordan', 'alpha_2' => 'JO', 'alpha_3' => 'JOR', 'digit_code' => '400'),
        100 => array ('country' => 'Kazakhstan', 'alpha_2' => 'KZ', 'alpha_3' => 'KAZ', 'digit_code' => '398'),
        101 => array ('country' => 'Kenya', 'alpha_2' => 'KE', 'alpha_3' => 'KEN', 'digit_code' => '404'),
        102 => array ('country' => 'Kiribati', 'alpha_2' => 'KI', 'alpha_3' => 'KIR', 'digit_code' => '296'),
        103 => array ('country' => 'Kuwait', 'alpha_2' => 'KW', 'alpha_3' => 'KWT', 'digit_code' => '414'),
        104 => array ('country' => 'Kyrgyzstan', 'alpha_2' => 'KG', 'alpha_3' => 'KGZ', 'digit_code' => '417'),
        105 => array ('country' => 'Latvia', 'alpha_2' => 'LV', 'alpha_3' => 'LVA', 'digit_code' => '428'),
        106 => array ('country' => 'Lebanon', 'alpha_2' => 'LB', 'alpha_3' => 'LBN', 'digit_code' => '422'),
        107 => array ('country' => 'Lesotho', 'alpha_2' => 'LS', 'alpha_3' => 'LSO', 'digit_code' => '426'),
        108 => array ('country' => 'Liberia', 'alpha_2' => 'LR', 'alpha_3' => 'LBR', 'digit_code' => '430'),
        109 => array ('country' => 'Libya', 'alpha_2' => 'LY', 'alpha_3' => 'LBY', 'digit_code' => '434'),
        110 => array ('country' => 'Liechtenstein', 'alpha_2' => 'LI', 'alpha_3' => 'LIE', 'digit_code' => '438'),
        111 => array ('country' => 'Lithuania', 'alpha_2' => 'LT', 'alpha_3' => 'LTU', 'digit_code' => '440'),
        112 => array ('country' => 'Luxembourg', 'alpha_2' => 'LU', 'alpha_3' => 'LUX', 'digit_code' => '442'),
        113 => array ('country' => 'Macao', 'alpha_2' => 'MO', 'alpha_3' => 'MAC', 'digit_code' => '446'),
        114 => array ('country' => 'Madagascar', 'alpha_2' => 'MG', 'alpha_3' => 'MDG', 'digit_code' => '450'),
        115 => array ('country' => 'Malawi', 'alpha_2' => 'MW', 'alpha_3' => 'MWI', 'digit_code' => '454'),
        116 => array ('country' => 'Malaysia', 'alpha_2' => 'MY', 'alpha_3' => 'MYS', 'digit_code' => '458'),
        117 => array ('country' => 'Maldives', 'alpha_2' => 'MV', 'alpha_3' => 'MDV', 'digit_code' => '462'),
        118 => array ('country' => 'Mali', 'alpha_2' => 'ML', 'alpha_3' => 'MLI', 'digit_code' => '466'),
        119 => array ('country' => 'Malta', 'alpha_2' => 'MT', 'alpha_3' => 'MLT', 'digit_code' => '470'),
        120 => array ('country' => 'Marshall Islands', 'alpha_2' => 'MH', 'alpha_3' => 'MHL', 'digit_code' => '584'),
        121 => array ('country' => 'Martinique', 'alpha_2' => 'MQ', 'alpha_3' => 'MTQ', 'digit_code' => '474'),
        122 => array ('country' => 'Mauritania', 'alpha_2' => 'MR', 'alpha_3' => 'MRT', 'digit_code' => '478'),
        123 => array ('country' => 'Mauritius', 'alpha_2' => 'MU', 'alpha_3' => 'MUS', 'digit_code' => '480'),
        124 => array ('country' => 'Mayotte', 'alpha_2' => 'YT', 'alpha_3' => 'MYT', 'digit_code' => '175'),
        125 => array ('country' => 'Mexico', 'alpha_2' => 'MX', 'alpha_3' => 'MEX', 'digit_code' => '484'),
        126 => array ('country' => 'Monaco', 'alpha_2' => 'MC', 'alpha_3' => 'MCO', 'digit_code' => '492'),
        127 => array ('country' => 'Mongolia', 'alpha_2' => 'MN', 'alpha_3' => 'MNG', 'digit_code' => '496'),
        128 => array ('country' => 'Montenegro', 'alpha_2' => 'ME', 'alpha_3' => 'MNE', 'digit_code' => '499'),
        129 => array ('country' => 'Montserrat', 'alpha_2' => 'MS', 'alpha_3' => 'MSR', 'digit_code' => '500'),
        130 => array ('country' => 'Morocco', 'alpha_2' => 'MA', 'alpha_3' => 'MAR', 'digit_code' => '504'),
        131 => array ('country' => 'Mozambique', 'alpha_2' => 'MZ', 'alpha_3' => 'MOZ', 'digit_code' => '508'),
        132 => array ('country' => 'Myanmar', 'alpha_2' => 'MM', 'alpha_3' => 'MMR', 'digit_code' => '104'),
        133 => array ('country' => 'Namibia', 'alpha_2' => 'NA', 'alpha_3' => 'NAM', 'digit_code' => '516'),
        134 => array ('country' => 'Nauru', 'alpha_2' => 'NR', 'alpha_3' => 'NRU', 'digit_code' => '520'),
        135 => array ('country' => 'Nepal', 'alpha_2' => 'NP', 'alpha_3' => 'NPL', 'digit_code' => '524'),
        136 => array ('country' => 'Netherlands', 'alpha_2' => 'NL', 'alpha_3' => 'NLD', 'digit_code' => '528'),
        137 => array ('country' => 'New Caledonia', 'alpha_2' => 'NC', 'alpha_3' => 'NCL', 'digit_code' => '540'),
        138 => array ('country' => 'New Zealand', 'alpha_2' => 'NZ', 'alpha_3' => 'NZL', 'digit_code' => '554'),
        139 => array ('country' => 'Nicaragua', 'alpha_2' => 'NI', 'alpha_3' => 'NIC', 'digit_code' => '558'),
        140 => array ('country' => 'Niger', 'alpha_2' => 'NE', 'alpha_3' => 'NER', 'digit_code' => '562'),
        141 => array ('country' => 'Nigeria', 'alpha_2' => 'NG', 'alpha_3' => 'NGA', 'digit_code' => '566'),
        142 => array ('country' => 'Niue', 'alpha_2' => 'NU', 'alpha_3' => 'NIU', 'digit_code' => '570'),
        143 => array ('country' => 'Norfolk Island', 'alpha_2' => 'NF', 'alpha_3' => 'NFK', 'digit_code' => '574'),
        144 => array ('country' => 'Northern Mariana Islands', 'alpha_2' => 'MP', 'alpha_3' => 'MNP', 'digit_code' => '580'),
        145 => array ('country' => 'Norway', 'alpha_2' => 'NO', 'alpha_3' => 'NOR', 'digit_code' => '578'),
        146 => array ('country' => 'Oman', 'alpha_2' => 'OM', 'alpha_3' => 'OMN', 'digit_code' => '512'),
        147 => array ('country' => 'Pakistan', 'alpha_2' => 'PK', 'alpha_3' => 'PAK', 'digit_code' => '586'),
        148 => array ('country' => 'Palau', 'alpha_2' => 'PW', 'alpha_3' => 'PLW', 'digit_code' => '585'),
        149 => array ('country' => 'Panama', 'alpha_2' => 'PA', 'alpha_3' => 'PAN', 'digit_code' => '591'),
        150 => array ('country' => 'Papua New Guinea', 'alpha_2' => 'PG', 'alpha_3' => 'PNG', 'digit_code' => '598'),
        151 => array ('country' => 'Paraguay', 'alpha_2' => 'PY', 'alpha_3' => 'PRY', 'digit_code' => '600'),
        152 => array ('country' => 'Peru', 'alpha_2' => 'PE', 'alpha_3' => 'PER', 'digit_code' => '604'),
        153 => array ('country' => 'Philippines', 'alpha_2' => 'PH', 'alpha_3' => 'PHL', 'digit_code' => '608'),
        154 => array ('country' => 'Pitcairn', 'alpha_2' => 'PN', 'alpha_3' => 'PCN', 'digit_code' => '612'),
        155 => array ('country' => 'Poland', 'alpha_2' => 'PL', 'alpha_3' => 'POL', 'digit_code' => '616'),
        156 => array ('country' => 'Portugal', 'alpha_2' => 'PT', 'alpha_3' => 'PRT', 'digit_code' => '620'),
        157 => array ('country' => 'Puerto Rico', 'alpha_2' => 'PR', 'alpha_3' => 'PRI', 'digit_code' => '630'),
        158 => array ('country' => 'Qatar', 'alpha_2' => 'QA', 'alpha_3' => 'QAT', 'digit_code' => '634'),
        159 => array ('country' => 'Romania', 'alpha_2' => 'RO', 'alpha_3' => 'ROU', 'digit_code' => '642'),
        160 => array ('country' => 'Russian Federation', 'alpha_2' => 'RU', 'alpha_3' => 'RUS', 'digit_code' => '643'),
        161 => array ('country' => 'Rwanda', 'alpha_2' => 'RW', 'alpha_3' => 'RWA', 'digit_code' => '646'),
        162 => array ('country' => 'Saint Kitts and Nevis', 'alpha_2' => 'KN', 'alpha_3' => 'KNA', 'digit_code' => '659'),
        163 => array ('country' => 'Saint Lucia', 'alpha_2' => 'LC', 'alpha_3' => 'LCA', 'digit_code' => '662'),
        164 => array ('country' => 'Saint Pierre and Miquelon', 'alpha_2' => 'PM', 'alpha_3' => 'SPM', 'digit_code' => '666'),
        165 => array ('country' => 'Saint Vincent and the Grenadines', 'alpha_2' => 'VC', 'alpha_3' => 'VCT', 'digit_code' => '670'),
        166 => array ('country' => 'Samoa', 'alpha_2' => 'WS', 'alpha_3' => 'WSM', 'digit_code' => '882'),
        167 => array ('country' => 'San Marino', 'alpha_2' => 'SM', 'alpha_3' => 'SMR', 'digit_code' => '674'),
        168 => array ('country' => 'Sao Tome and Principe', 'alpha_2' => 'ST', 'alpha_3' => 'STP', 'digit_code' => '678'),
        169 => array ('country' => 'Saudi Arabia', 'alpha_2' => 'SA', 'alpha_3' => 'SAU', 'digit_code' => '682'),
        170 => array ('country' => 'Senegal', 'alpha_2' => 'SN', 'alpha_3' => 'SEN', 'digit_code' => '686'),
        171 => array ('country' => 'Serbia', 'alpha_2' => 'RS', 'alpha_3' => 'SRB', 'digit_code' => '688'),
        172 => array ('country' => 'Seychelles', 'alpha_2' => 'SC', 'alpha_3' => 'SYC', 'digit_code' => '690'),
        173 => array ('country' => 'Sierra Leone', 'alpha_2' => 'SL', 'alpha_3' => 'SLE', 'digit_code' => '694'),
        174 => array ('country' => 'Singapore', 'alpha_2' => 'SG', 'alpha_3' => 'SGP', 'digit_code' => '702'),
        175 => array ('country' => 'Slovakia', 'alpha_2' => 'SK', 'alpha_3' => 'SVK', 'digit_code' => '703'),
        176 => array ('country' => 'Slovenia', 'alpha_2' => 'SI', 'alpha_3' => 'SVN', 'digit_code' => '705'),
        177 => array ('country' => 'Solomon Islands', 'alpha_2' => 'SB', 'alpha_3' => 'SLB', 'digit_code' => '090'),
        178 => array ('country' => 'Somalia', 'alpha_2' => 'SO', 'alpha_3' => 'SOM', 'digit_code' => '706'),
        179 => array ('country' => 'South Africa', 'alpha_2' => 'ZA', 'alpha_3' => 'ZAF', 'digit_code' => '710'),
        180 => array ('country' => 'South Georgia and the South Sandwich Islands', 'alpha_2' => 'GS', 'alpha_3' => 'SGS', 'digit_code' => '239'),
        181 => array ('country' => 'South Sudan', 'alpha_2' => 'SS', 'alpha_3' => 'SSD', 'digit_code' => '728'),
        182 => array ('country' => 'Spain', 'alpha_2' => 'ES', 'alpha_3' => 'ESP', 'digit_code' => '724'),
        183 => array ('country' => 'Sri Lanka', 'alpha_2' => 'LK', 'alpha_3' => 'LKA', 'digit_code' => '144'),
        184 => array ('country' => 'Sudan', 'alpha_2' => 'SD', 'alpha_3' => 'SDN', 'digit_code' => '729'),
        185 => array ('country' => 'Suriname', 'alpha_2' => 'SR', 'alpha_3' => 'SUR', 'digit_code' => '740'),
        186 => array ('country' => 'Svalbard and Jan Mayen', 'alpha_2' => 'SJ', 'alpha_3' => 'SJM', 'digit_code' => '744'),
        187 => array ('country' => 'Swaziland', 'alpha_2' => 'SZ', 'alpha_3' => 'SWZ', 'digit_code' => '748'),
        188 => array ('country' => 'Sweden', 'alpha_2' => 'SE', 'alpha_3' => 'SWE', 'digit_code' => '752'),
        189 => array ('country' => 'Switzerland', 'alpha_2' => 'CH', 'alpha_3' => 'CHE', 'digit_code' => '756'),
        190 => array ('country' => 'Syrian Arab Republic', 'alpha_2' => 'SY', 'alpha_3' => 'SYR', 'digit_code' => '760'),
        191 => array ('country' => 'Tajikistan', 'alpha_2' => 'TJ', 'alpha_3' => 'TJK', 'digit_code' => '762'),
        192 => array ('country' => 'Thailand', 'alpha_2' => 'TH', 'alpha_3' => 'THA', 'digit_code' => '764'),
        193 => array ('country' => 'Togo', 'alpha_2' => 'TG', 'alpha_3' => 'TGO', 'digit_code' => '768'),
        194 => array ('country' => 'Tokelau', 'alpha_2' => 'TK', 'alpha_3' => 'TKL', 'digit_code' => '772'),
        195 => array ('country' => 'Tonga', 'alpha_2' => 'TO', 'alpha_3' => 'TON', 'digit_code' => '776'),
        196 => array ('country' => 'Trinidad and Tobago', 'alpha_2' => 'TT', 'alpha_3' => 'TTO', 'digit_code' => '780'),
        197 => array ('country' => 'Tunisia', 'alpha_2' => 'TN', 'alpha_3' => 'TUN', 'digit_code' => '788'),
        198 => array ('country' => 'Turkey', 'alpha_2' => 'TR', 'alpha_3' => 'TUR', 'digit_code' => '792'),
        199 => array ('country' => 'Turkmenistan', 'alpha_2' => 'TM', 'alpha_3' => 'TKM', 'digit_code' => '795'),
        200 => array ('country' => 'Turks and Caicos Islands', 'alpha_2' => 'TC', 'alpha_3' => 'TCA', 'digit_code' => '796'),
        201 => array ('country' => 'Tuvalu', 'alpha_2' => 'TV', 'alpha_3' => 'TUV', 'digit_code' => '798'),
        202 => array ('country' => 'Uganda', 'alpha_2' => 'UG', 'alpha_3' => 'UGA', 'digit_code' => '800'),
        203 => array ('country' => 'Ukraine', 'alpha_2' => 'UA', 'alpha_3' => 'UKR', 'digit_code' => '804'),
        204 => array ('country' => 'United Arab Emirates', 'alpha_2' => 'AE', 'alpha_3' => 'ARE', 'digit_code' => '784'),
        205 => array ('country' => 'United Kingdom of Great Britain and Northern Ireland', 'alpha_2' => 'GB', 'alpha_3' => 'GBR', 'digit_code' => '826'),
        206 => array ('country' => 'United States of America', 'alpha_2' => 'US', 'alpha_3' => 'USA', 'digit_code' => '840'),
        207 => array ('country' => 'United States Minor Outlying Islands', 'alpha_2' => 'UM', 'alpha_3' => 'UMI', 'digit_code' => '581'),
        208 => array ('country' => 'Uruguay', 'alpha_2' => 'UY', 'alpha_3' => 'URY', 'digit_code' => '858'),
        209 => array ('country' => 'Uzbekistan', 'alpha_2' => 'UZ', 'alpha_3' => 'UZB', 'digit_code' => '860'),
        210 => array ('country' => 'Vanuatu', 'alpha_2' => 'VU', 'alpha_3' => 'VUT', 'digit_code' => '548'),
        211 => array ('country' => 'Viet Nam', 'alpha_2' => 'VN', 'alpha_3' => 'VNM', 'digit_code' => '704'),
        212 => array ('country' => 'Wallis and Futuna', 'alpha_2' => 'WF', 'alpha_3' => 'WLF', 'digit_code' => '876'),
        213 => array ('country' => 'Yemen', 'alpha_2' => 'YE', 'alpha_3' => 'YEM', 'digit_code' => '887'),
        214 => array ('country' => 'Zambia', 'alpha_2' => 'ZM', 'alpha_3' => 'ZMB', 'digit_code' => '894'),
        215 => array ('country' => 'Zimbabwe', 'alpha_2' => 'ZW', 'alpha_3' => 'ZWE', 'digit_code' => '716'),
    );

    /**
     * Get all countries
     *
     * @return array
     */
    public static function getCountries()
    {
        return self::$countries;
    }

    /**
     * Get country by ISO 3166-1 english name
     * @param $name
     * @return array|null
     */
    public static function getCountryByName($name)
    {
        foreach(self::$countries as $key => $val)
        {
            if(strtolower($val['country']) === strtolower($name))
                return self::$countries[$key];
        }
        return null;
    }

    /**
     * Get country by ISO 3166-1 alpha-2
     *
     * @param string $alpha_2
     * @return array|null
     */
    public static function getCountryByAlpha2($alpha_2)
    {
        foreach(self::$countries as $key => $val)
        {
            if($val['alpha_2'] === strtoupper($alpha_2))
                return self::$countries[$key];
        }
        return null;
    }

    /**
     * Get country by ISO 3166-1 alpha-3
     *
     * @param string $alpha_3
     * @return array|null
     */
    public static function getCountryByAlpha3($alpha_3)
    {
        foreach(self::$countries as $key => $val)
        {
            if($val['alpha_3'] === strtoupper($alpha_3))
                return self::$countries[$key];
        }
        return null;
    }

    /**
     * Get country by ISO 3166-1 numeric
     *
     * @param string $digit_code
     * @return array|null
     */
    public static function getCountryByDigitCode($digit_code)
    {
        foreach(self::$countries as $key => $val)
        {
            if($val['digit_code'] === $digit_code)
                return self::$countries[$key];
        }
        return null;
    }
}