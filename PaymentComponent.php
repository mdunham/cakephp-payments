<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\InstanceConfigTrait;
use Cake\Log\LogTrait;

/**
 * CakePHP 3 component for processing payments through 360 payments
 * 
 * @author Matthew Dunham <matt@hotcoffeydesign.com>
 * @see https://www.360payments.com/
 * @todo Change the method arguments into an array instead of several method arguments.
 */
class PaymentComponent extends Component {
	
	use InstanceConfigTrait;
	use LogTrait;
	
	/**
	 * Default Config
	 * 
	 * These are merged with user-provided config when the component is used.
	 * 
	 * @var array 
	 */
	protected $_defaultConfig = [
		'endpoint' => 'https://secure.networkmerchants.com/api/transact.php',
		'username' => 'demo',
		'password' => 'password'
	];

	/**
	 * Order Info
	 * 
	 * @var array 
	 */
	private $order = [];
	
	/**
	 * Billing Info
	 * 
	 * @var array 
	 */
	private $billing = [];
	
	/**
	 * Shipping Info
	 * 
	 * @var array 
	 */
	private $shipping = [];
	
	/**
	 * Set the payment gateway login information for API access
	 * 
	 * @param string $username
	 * @param string $password
	 */
	public function setLogin($username, $password) {
		$this->setConfig([
			'username' => $username, 
			'password' => $password
		]);
	}

	/**
	 * Set the order details for a sale
	 * 
	 * @param string $orderid
	 * @param string $orderdescription
	 * @param string $tax
	 * @param string $shipping
	 * @param string $ponumber
	 * @param string $ipaddress
	 */
	public function setOrder($orderid, $orderdescription, $tax, $shipping, $ponumber, $ipaddress) {
		$this->order['orderid'] = $orderid;
		$this->order['orderdescription'] = $orderdescription;
		$this->order['tax'] = $tax;
		$this->order['shipping'] = $shipping;
		$this->order['ponumber'] = $ponumber;
		$this->order['ipaddress'] = $ipaddress;
	}

	/**
	 * Set the billing information for a sale
	 * 
	 * @param string $firstname
	 * @param string $lastname
	 * @param string $company
	 * @param string $address1
	 * @param string $address2
	 * @param string $city
	 * @param string $state
	 * @param string $zip
	 * @param string $country
	 * @param string $phone
	 * @param string $fax
	 * @param string $email
	 * @param string $website
	 */
	public function setBilling($firstname, $lastname, $company, $address1, $address2, $city, $state, $zip, $country, $phone, $fax, $email, $website) {
		$this->billing['firstname'] = $firstname;
		$this->billing['lastname'] = $lastname;
		$this->billing['company'] = $company;
		$this->billing['address1'] = $address1;
		$this->billing['address2'] = $address2;
		$this->billing['city'] = $city;
		$this->billing['state'] = $state;
		$this->billing['zip'] = $zip;
		$this->billing['country'] = $country;
		$this->billing['phone'] = $phone;
		$this->billing['fax'] = $fax;
		$this->billing['email'] = $email;
		$this->billing['website'] = $website;
	}

	/**
	 * Set the shipping information for a sale
	 * 
	 * @param string $firstname
	 * @param string $lastname
	 * @param string $company
	 * @param string $address1
	 * @param string $address2
	 * @param string $city
	 * @param string $state
	 * @param string $zip
	 * @param string $country
	 * @param string $email
	 */
	public function setShipping($firstname, $lastname, $company, $address1, $address2, $city, $state, $zip, $country, $email) {
		$this->shipping['firstname'] = $firstname;
		$this->shipping['lastname'] = $lastname;
		$this->shipping['company'] = $company;
		$this->shipping['address1'] = $address1;
		$this->shipping['address2'] = $address2;
		$this->shipping['city'] = $city;
		$this->shipping['state'] = $state;
		$this->shipping['zip'] = $zip;
		$this->shipping['country'] = $country;
		$this->shipping['email'] = $email;
	}

	/**
	 * Issue a sale request to authorize and capture a payment
	 * 
	 * @param float $amount Amount to send
	 * @param string $ccnumber
	 * @param string $ccexp
	 * @param string $cvv
	 * @return int 1 = Approved, 2 = Declined, 3 = Error
	 */
	public function doSale($amount, $ccnumber, $ccexp, $cvv = "") {

		$query = "";
		// Login Information
		$query .= "username=" . urlencode($this->config('username')) . "&";
		$query .= "password=" . urlencode($this->config('password')) . "&";
		// Sales Information
		$query .= "ccnumber=" . urlencode($ccnumber) . "&";
		$query .= "ccexp=" . urlencode($ccexp) . "&";
		$query .= "amount=" . urlencode(number_format($amount, 2, ".", "")) . "&";
		$query .= "cvv=" . urlencode($cvv) . "&";
		// Order Information
		$query .= "ipaddress=" . urlencode($this->order['ipaddress']) . "&";
		$query .= "orderid=" . urlencode($this->order['orderid']) . "&";
		$query .= "orderdescription=" . urlencode($this->order['orderdescription']) . "&";
		//$query .= "tax=" . urlencode(number_format($this->order['tax'], 2, ".", "")) . "&";
		//$query .= "shipping=" . urlencode(number_format($this->order['shipping'], 2, ".", "")) . "&";
		//$query .= "ponumber=" . urlencode($this->order['ponumber']) . "&";
		// Billing Information
		$query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
		$query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
		$query .= "company=" . urlencode($this->billing['company']) . "&";
		$query .= "address1=" . urlencode($this->billing['address1']) . "&";
		$query .= "address2=" . urlencode($this->billing['address2']) . "&";
		$query .= "city=" . urlencode($this->billing['city']) . "&";
		$query .= "state=" . urlencode($this->billing['state']) . "&";
		$query .= "zip=" . urlencode($this->billing['zip']) . "&";
		$query .= "country=" . urlencode($this->billing['country']) . "&";
		$query .= "phone=" . urlencode($this->billing['phone']) . "&";
		//$query .= "fax=" . urlencode($this->billing['fax']) . "&";
		$query .= "email=" . urlencode($this->billing['email']) . "&";
		//$query .= "website=" . urlencode($this->billing['website']) . "&";
		// Shipping Information
//		$query .= "shipping_firstname=" . urlencode($this->shipping['firstname']) . "&";
//		$query .= "shipping_lastname=" . urlencode($this->shipping['lastname']) . "&";
//		$query .= "shipping_company=" . urlencode($this->shipping['company']) . "&";
//		$query .= "shipping_address1=" . urlencode($this->shipping['address1']) . "&";
//		$query .= "shipping_address2=" . urlencode($this->shipping['address2']) . "&";
//		$query .= "shipping_city=" . urlencode($this->shipping['city']) . "&";
//		$query .= "shipping_state=" . urlencode($this->shipping['state']) . "&";
//		$query .= "shipping_zip=" . urlencode($this->shipping['zip']) . "&";
//		$query .= "shipping_country=" . urlencode($this->shipping['country']) . "&";
//		$query .= "shipping_email=" . urlencode($this->shipping['email']) . "&";
		$query .= "type=sale";
		return $this->_doPost($query);
	}

	/**
	 * Make an authorize request to verify funds
	 * 
	 * @param float $amount Amount to send
	 * @param string $ccnumber
	 * @param string $ccexp
	 * @param string $cvv
	 * @return int 1 = Approved, 2 = Declined, 3 = Error
	 */
	public function doAuth($amount, $ccnumber, $ccexp, $cvv = "") {

		$query = "";
		// Login Information
		$query .= "username=" . urlencode($this->config('username')) . "&";
		$query .= "password=" . urlencode($this->config('password')) . "&";
		// Sales Information
		$query .= "ccnumber=" . urlencode($ccnumber) . "&";
		$query .= "ccexp=" . urlencode($ccexp) . "&";
		$query .= "amount=" . urlencode(number_format($amount, 2, ".", "")) . "&";
		$query .= "cvv=" . urlencode($cvv) . "&";
		// Order Information
		$query .= "ipaddress=" . urlencode($this->order['ipaddress']) . "&";
		$query .= "orderid=" . urlencode($this->order['orderid']) . "&";
		$query .= "orderdescription=" . urlencode($this->order['orderdescription']) . "&";
		$query .= "tax=" . urlencode(number_format($this->order['tax'], 2, ".", "")) . "&";
		$query .= "shipping=" . urlencode(number_format($this->order['shipping'], 2, ".", "")) . "&";
		$query .= "ponumber=" . urlencode($this->order['ponumber']) . "&";
		// Billing Information
		$query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
		$query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
		$query .= "company=" . urlencode($this->billing['company']) . "&";
		$query .= "address1=" . urlencode($this->billing['address1']) . "&";
		$query .= "address2=" . urlencode($this->billing['address2']) . "&";
		$query .= "city=" . urlencode($this->billing['city']) . "&";
		$query .= "state=" . urlencode($this->billing['state']) . "&";
		$query .= "zip=" . urlencode($this->billing['zip']) . "&";
		$query .= "country=" . urlencode($this->billing['country']) . "&";
		$query .= "phone=" . urlencode($this->billing['phone']) . "&";
		$query .= "fax=" . urlencode($this->billing['fax']) . "&";
		$query .= "email=" . urlencode($this->billing['email']) . "&";
		$query .= "website=" . urlencode($this->billing['website']) . "&";
		// Shipping Information
		$query .= "shipping_firstname=" . urlencode($this->shipping['firstname']) . "&";
		$query .= "shipping_lastname=" . urlencode($this->shipping['lastname']) . "&";
		$query .= "shipping_company=" . urlencode($this->shipping['company']) . "&";
		$query .= "shipping_address1=" . urlencode($this->shipping['address1']) . "&";
		$query .= "shipping_address2=" . urlencode($this->shipping['address2']) . "&";
		$query .= "shipping_city=" . urlencode($this->shipping['city']) . "&";
		$query .= "shipping_state=" . urlencode($this->shipping['state']) . "&";
		$query .= "shipping_zip=" . urlencode($this->shipping['zip']) . "&";
		$query .= "shipping_country=" . urlencode($this->shipping['country']) . "&";
		$query .= "shipping_email=" . urlencode($this->shipping['email']) . "&";
		$query .= "type=auth";
		return $this->_doPost($query);
	}
	
	/**
	 * Send a credit request
	 * 
	 * @param float $amount Amount to send
	 * @param string $ccnumber
	 * @param string $ccexp
	 * @return int 1 = Approved, 2 = Declined, 3 = Error
	 */
	public function doCredit($amount, $ccnumber, $ccexp) {

		$query = "";
		// Login Information
		$query .= "username=" . urlencode($this->config('username')) . "&";
		$query .= "password=" . urlencode($this->config('password')) . "&";
		// Sales Information
		$query .= "ccnumber=" . urlencode($ccnumber) . "&";
		$query .= "ccexp=" . urlencode($ccexp) . "&";
		$query .= "amount=" . urlencode(number_format($amount, 2, ".", "")) . "&";
		// Order Information
		$query .= "ipaddress=" . urlencode($this->order['ipaddress']) . "&";
		$query .= "orderid=" . urlencode($this->order['orderid']) . "&";
		$query .= "orderdescription=" . urlencode($this->order['orderdescription']) . "&";
		$query .= "tax=" . urlencode(number_format($this->order['tax'], 2, ".", "")) . "&";
		$query .= "shipping=" . urlencode(number_format($this->order['shipping'], 2, ".", "")) . "&";
		$query .= "ponumber=" . urlencode($this->order['ponumber']) . "&";
		// Billing Information
		$query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
		$query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
		$query .= "company=" . urlencode($this->billing['company']) . "&";
		$query .= "address1=" . urlencode($this->billing['address1']) . "&";
		$query .= "address2=" . urlencode($this->billing['address2']) . "&";
		$query .= "city=" . urlencode($this->billing['city']) . "&";
		$query .= "state=" . urlencode($this->billing['state']) . "&";
		$query .= "zip=" . urlencode($this->billing['zip']) . "&";
		$query .= "country=" . urlencode($this->billing['country']) . "&";
		$query .= "phone=" . urlencode($this->billing['phone']) . "&";
		$query .= "fax=" . urlencode($this->billing['fax']) . "&";
		$query .= "email=" . urlencode($this->billing['email']) . "&";
		$query .= "website=" . urlencode($this->billing['website']) . "&";
		$query .= "type=credit";
		return $this->_doPost($query);
	}

	/**
	 * Log and offline request
	 * 
	 * @param string $authorizationcode
	 * @param float $amount Amount of the offline transaction
	 * @param string $ccnumber
	 * @param string $ccexp
	 * @return int 1 = Approved, 2 = Declined, 3 = Error
	 */
	public function doOffline($authorizationcode, $amount, $ccnumber, $ccexp) {

		$query = "";
		// Login Information
		$query .= "username=" . urlencode($this->config('username')) . "&";
		$query .= "password=" . urlencode($this->config('password')) . "&";
		// Sales Information
		$query .= "ccnumber=" . urlencode($ccnumber) . "&";
		$query .= "ccexp=" . urlencode($ccexp) . "&";
		$query .= "amount=" . urlencode(number_format($amount, 2, ".", "")) . "&";
		$query .= "authorizationcode=" . urlencode($authorizationcode) . "&";
		// Order Information
		$query .= "ipaddress=" . urlencode($this->order['ipaddress']) . "&";
		$query .= "orderid=" . urlencode($this->order['orderid']) . "&";
		$query .= "orderdescription=" . urlencode($this->order['orderdescription']) . "&";
		$query .= "tax=" . urlencode(number_format($this->order['tax'], 2, ".", "")) . "&";
		$query .= "shipping=" . urlencode(number_format($this->order['shipping'], 2, ".", "")) . "&";
		$query .= "ponumber=" . urlencode($this->order['ponumber']) . "&";
		// Billing Information
		$query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
		$query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
		$query .= "company=" . urlencode($this->billing['company']) . "&";
		$query .= "address1=" . urlencode($this->billing['address1']) . "&";
		$query .= "address2=" . urlencode($this->billing['address2']) . "&";
		$query .= "city=" . urlencode($this->billing['city']) . "&";
		$query .= "state=" . urlencode($this->billing['state']) . "&";
		$query .= "zip=" . urlencode($this->billing['zip']) . "&";
		$query .= "country=" . urlencode($this->billing['country']) . "&";
		$query .= "phone=" . urlencode($this->billing['phone']) . "&";
		$query .= "fax=" . urlencode($this->billing['fax']) . "&";
		$query .= "email=" . urlencode($this->billing['email']) . "&";
		$query .= "website=" . urlencode($this->billing['website']) . "&";
		// Shipping Information
		$query .= "shipping_firstname=" . urlencode($this->shipping['firstname']) . "&";
		$query .= "shipping_lastname=" . urlencode($this->shipping['lastname']) . "&";
		$query .= "shipping_company=" . urlencode($this->shipping['company']) . "&";
		$query .= "shipping_address1=" . urlencode($this->shipping['address1']) . "&";
		$query .= "shipping_address2=" . urlencode($this->shipping['address2']) . "&";
		$query .= "shipping_city=" . urlencode($this->shipping['city']) . "&";
		$query .= "shipping_state=" . urlencode($this->shipping['state']) . "&";
		$query .= "shipping_zip=" . urlencode($this->shipping['zip']) . "&";
		$query .= "shipping_country=" . urlencode($this->shipping['country']) . "&";
		$query .= "shipping_email=" . urlencode($this->shipping['email']) . "&";
		$query .= "type=offline";
		return $this->_doPost($query);
	}

	/**
	 * Capture a payment after an authorize request
	 * 
	 * @param string $transactionid
	 * @param float $amount
	 * @return int 1 = Approved, 2 = Declined, 3 = Error
	 */
	public function doCapture($transactionid, $amount = 0) {

		$query = "";
		// Login Information
		$query .= "username=" . urlencode($this->config('username')) . "&";
		$query .= "password=" . urlencode($this->config('password')) . "&";
		// Transaction Information
		$query .= "transactionid=" . urlencode($transactionid) . "&";
		if ($amount > 0) {
			$query .= "amount=" . urlencode(number_format($amount, 2, ".", "")) . "&";
		}
		$query .= "type=capture";
		return $this->_doPost($query);
	}

	/**
	 * Void a previous payment
	 * 
	 * @param string $transactionid
	 * @return int 1 = Approved, 2 = Declined, 3 = Error
	 */
	public function doVoid($transactionid) {

		$query = "";
		// Login Information
		$query .= "username=" . urlencode($this->config('username')) . "&";
		$query .= "password=" . urlencode($this->config('password')) . "&";
		// Transaction Information
		$query .= "transactionid=" . urlencode($transactionid) . "&";
		$query .= "type=void";
		return $this->_doPost($query);
	}

	/**
	 * Make a refund request
	 * 
	 * @param string $transactionid
	 * @param float $amount
	 * @return int 1 = Approved, 2 = Declined, 3 = Error
	 */
	public function doRefund($transactionid, $amount = 0) {

		$query = "";
		// Login Information
		$query .= "username=" . urlencode($this->config('username')) . "&";
		$query .= "password=" . urlencode($this->config('password')) . "&";
		// Transaction Information
		$query .= "transactionid=" . urlencode($transactionid) . "&";
		if ($amount > 0) {
			$query .= "amount=" . urlencode(number_format($amount, 2, ".", "")) . "&";
		}
		$query .= "type=refund";
		return $this->_doPost($query);
	}

	/**
	 * Post the request to the payment gateway
	 * 
	 * @param string $query
	 * @return int 1 = Approved, 2 = Declined, 3 = Error
	 */
	private function _doPost($query) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->config('endpoint'));
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
		curl_setopt($ch, CURLOPT_POST, 1);

		if (!($data = curl_exec($ch))) {
			return ERROR;
		}
		curl_close($ch);
		unset($ch);
		$data = explode("&", $data);
		for ($i = 0; $i < count($data); $i++) {
			$rdata = explode("=", $data[$i]);
			$this->responses[$rdata[0]] = $rdata[1];
		}
		return intval($this->responses['response']);
	}

}
