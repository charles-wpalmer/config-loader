<?php

/**
 * Class to handle database connection with Mamut SQL Server
 *
 * @category Mamut
 * @author Charles Palmer <chp@gloversure.co.uk>
 * @package Gloversure_Mamut
 * @copyright 2016 Gloversure Ltd
 */
class Gloversure_Mamut_Mamut {

    private $_database;

    protected $config;

    public function __construct()
    {
        $this->config = new Gloversure_Mamut_Config_ConfigManager('config.php');

        $this->_database = new Gloversure_Mamut_Database_DatabaseConnection(
            $this->config->get('dbuser'), 
            $this->config->get('dbpass'), 
            $this->config->get('dsn')
        );
    }

    /**
     * Gets the next custid from database
     * 
     * @access private
     * 
     * @return The next custid
     */
    private function _getNextCustId()
    {
        $currentid = $this->_database->query("SELECT MAX(CUSTID) FROM G_CONTAC");

        $currentid = $currentid->fetchAll();

        $newId = $currentid[0][0] + 1;
        
        return $newId;
    }

    /**
     * Creates a contact in Mamut
     * 
     * @param string $name  Company name
     * @param string $phone Phone number
     * @param string $fax   Fax number
     * @param string $email Email address
     * @param string $www   Website
     * 
     * @access private
     * 
     * @return ContID
     */
    private function _createContact($name, $phone, $fax, $email, $www)
    {
        $custid = $this->getNextCustId();

        return $this->_database->query("INSERT INTO G_CONTAC (CUSTID, NAME, CONT, REGDATE, PHONE1, FAX1, EMAIL, WWW, CONTRES, VENDRES, 
            MAINRES, CREATEUSERID, EDITUSERID, PURVATCODEID, REMINDER_ISTATUSID, DATA700)
            VALUES ($custid, '$name', 1, GETDATE(), $phone, $fax, '$email',  '$www', 1100, 2100, 1100, 1, 1, 2, 10, 1)");

        // return $custid returns last inserted id
    }

    /**
     * Updates a contact in Mamut
     * 
     * @param int    $contid ID of contact to update
     * @param string $name   Company name
     * @param string $phone  Phone number
     * @param string $fax    Fax number
     * @param string $email  Email address
     * @param string $www    Website
     * 
     * @access private
     * 
     * @return null
     */
    private function _updateContact($contid, $name, $phone, $fax, $email, $www)
    {
        $this->_database->query('UPDATE G_CONTAC (NAME, PHONE1, FAX1, EMAIL, WWW, EDITDATE)
            VALUES ($name, $phone, $fax, $email, $www, GETDATE())
            WHERE G_CONTACT.CONTID = $contid');
    }

    /**
     * Creates a customer in Mamut
     * 
     * @param Mage_Model $customer Customer model
     * @param int        $contid   Contact ID
     * @param string     $name     Company name
     * @param string     $phone    Phone number
     * @param string     $fax      Fax number
     * @param string     $email    Email address
     * @param string     $www      Website
     * 
     * @access private
     * 
     * @return CustID
     */
    private function _createCustomer($customer, $contid, $name, $phone, $fax, $email, $www)
    {
        $firstname = $customer->getFirstName();
        $lastame = $customer->getLastName();
        $prefix = $customer->getPrefix();

        return $this->_database->query('INSERT INTO G_CPERS (CONTID, FIRSTNAME, MIDDLENAME, LASTNAME, CONTNAME, SALUTATION, DATA16, 
            DATA17, PHONEWORK, FAX, EMAIL, REGDATE, WWW, RANGERING, OURREF, FK_USERS_CREATE, FK_USERS_EDIT)
            VALUES ($contid, $firstname, "", $lastame, $name, $prefix, 1, 1, 
            $phone, $fax, $email, GETDATE(), $www, 1, 3, 0, 1)');

        // returns last inserted id
    }

    /**
     * Updates a customer in Mamut
     * 
     * @param int        $custId   ID of customer to update
     * @param Mage_Model $customer Customer model
     * @param string     $name     Company name
     * @param string     $phone    Phone number
     * @param string     $fax      Fax number
     * @param string     $email    Email address
     * @param string     $www      Website
     * 
     * @access private
     * 
     * @return null
     */
    private function _updateCustomer($custId, $customer, $name, $phone, $fax, $email, $www)
    {
        $firstname = $customer->getFirstName();
        $lastame = $customer->getLastName();
        $prefix = $customer->getPrefix();

        $this->_database->query('UPDATE G_CPERS (FIRSTNAME, LASTNAME, CONTNAME, SALUTATION, PHONEWORK, FAX, EMAIL, WWW, EDITDATE)
            VALUES ($firstname, $lastame, $name, $prefix, $phone, $fax, $email, $www, GETDATE())
            WHERE G_CONTACT.CPERSID = $custId');
    }

    /**
     * Creates address in Mamut
     * 
     * @param int        $adrtype  Type of address to create
     * @param int        $sourceId ID of the contact or customer
     * @param Mage_Model $address  Address Model
     * 
     * @access private
     * 
     * @return null
     */
    private function _createsAddress($adrtype, $sourceId, $address)
    {
        $street = $address->getStreet();
        $city = $adress->getCity();
        $region = $address->getRegion();
        $postcode = $address->getPostcode();

        $this->_database->query('INSERT INTO G_DELI (SOURCETYPE, ADRTYPE, SOURCEID, DATA56, MEMO, ZIPCODE, REGION, CITY, STREET, 
            ZIPCONVST, DELI_INFO, FREETEXT1, FREETEXT2, FREETEXT3)
            VALUES (1, $adrtype, $sourceId, 6, "", $postcode, $region, $city, $street, 0, 0, "", "", "")');
    }

    /**
     * Updates address in Mamut
     * 
     * @param int        $addressId  ID of address to update
     * @param Mage_Model $address    Address Model
     * 
     * @access private
     * 
     * @return null
     */
    private function _updateAddress($addressId, $address)
    {
        $street = $address->getStreet();
        $city = $adress->getCity();
        $region = $address->getRegion();
        $postcode = $address->getPostcode();

        $this->_database->query('UPDATE G_DELI (ZIPCODE, REGION, CITY, STREET)
            VALUES ($postcode, $region, $city, $street)
            WHERE G_DELI.ADDRESSID = $addressId');
    }

    /**
     * Creates the addresses in Mamut
     * 
     * @param int        $sourceId         ID of the contact or customer
     * @param Mage_Model $billingAddress   Billing Address Model
     * @param Mage_Model $deliveryAddress  Delivery Address Model
     * 
     * @access private
     * 
     * @return null
     */
    private function _createAddresses($sourceId, $billingAddress, $deliveryAddress)
    {
        // invoice address
        $this->_createsAddress(1, $sourceId, $billingAddress);
        // registered address
        $this->_createsAddress(2, $sourceId, $billingAddress);
        // delivery address
        $this->_createsAddress(3, $sourceId, $deliveryAddress);
    }

    /**
     * Handles updating a customer in Mamut
     *
     * @param $customer new data
     *
     * @access public 
     *
     * @return true if successful otherwise false
     */
    public function updateCustomer($customer)
    {
        //TODO:
        // - Get the details from the $customer
        // - Run relevant update functions and 
        //   deal with the customer onject
        $this->updateContact($custid, $name, $phone, $fax, $email, $www);

        $this->updateCustomer($custid, $customer, $name, $phone, $fax, $emai, $www);

        $this->updateAddress($addressid, $address);

    }

    /**
     * Exports the customers data to Mamut
     * 
     * @param Mage_Model $customer The customer to export
     * 
     * @access public
     * 
     * @return true if exported otherwise false
     */
    public function exportCustomer($customer)
    {
        $billingAddress = $customer->getBillingAddress();
        $deliveryAddress = $customer->getDeliveryAddress();

        $name = $billingAddress->getCompany();
        $phone = $billingAddress->getTelephone();
        $fax = $customer->getBillingAddress->getFax();
        $email = $customer->getEmail();
        $www = Mage::getBaseUrl();

        $contid = $this->_createContact($name, $phone, $fax, $email, $www);
        $this->_createAddresses($contid, $billingAddress, $deliveryAddress);

        $cusId = $this->_createCustomer($customer, $contid, $name, $phone, $fax, $email, $www);
        $this->_createAddresses($cusId, $billingAddress, $deliveryAddress);
    }
}
