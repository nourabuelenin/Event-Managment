<?php

class ADODB_Connection_Manager {
    static private $_me = null;
    
    private $fetchMode = 2;
    private $assocCase = 2;
    private $quoteFieldNames = 'NATIVE';
    private $cacheDir = '';
    private $cacheSeconds = '';


    private $arrDSN = array();
    private $arrConnection = array();

    /**
    * Singletone pattern constructor
    * 
    */
    private function __construct($fetchMode = 2, $assocCase = 2, $cacheDir = '', $cacheSeconds = ''){
        $this->fetchMode = $fetchMode;
        $this->assocCase = $assocCase;
        $this->cacheDir = $cacheDir;
        $this->cacheSeconds = $cacheSeconds;

        global $ADODB_ASSOC_CASE, $ADODB_FETCH_MODE, $ADODB_QUOTE_FIELDNAMES;

        $ADODB_FETCH_MODE = $this->fetchMode;
        $ADODB_ASSOC_CASE = $this->assocCase;
        $ADODB_QUOTE_FIELDNAMES = $this->quoteFieldNames;
        if(!empty($this->cacheDir) && !empty($this->cacheSeconds)){
            if(!file_exists($this->cacheDir)){
                mkdir($this->cacheDir, 0711, true);
            }
        }
    }
    

    private function add($strKey, $strDSN){
        if(isset($this->arrDSN[$strKey])){
            if(isset($this->arrConnection[$strKey])){
                // DSN already exists and connection established
                return false;
            }
        }

        // Connection added / updated
        $this->arrDSN[$strKey] = $strDSN;
        return true;
    }

    private function remove($strKey){
        if(!isset($this->arrDSN[$strKey])){
            // Unknown key
            return true;
        }
        
        if(isset($this->arrConnection[$strKey])){
            // Connection already established
            $DB = $this->arrConnection[$strKey];
            $DB->Close();
            
            unset($this->arrConnection[$strKey]);
        }

        unset($this->arrDSN[$strKey]);
        return true;
    }

    private function &connect($strKey){
        global $ADODB_CACHE_DIR, $ADODB_ACTIVE_CACHESECS;

        $ADODB_CACHE_DIR = $this->cacheDir;
        $ADODB_ACTIVE_CACHESECS = $this->cacheSeconds;

        if(!isset($this->arrDSN[$strKey])){
            // Unknown key
            return false;
        }

        if(isset($this->arrConnection[$strKey])){
            // Connection already established
            return $this->arrConnection[$strKey];
        }

        // Establish Connection
        $strDSN = $this->arrDSN[$strKey];
        $DB = NewADOConnection($strDSN);
        if(!$DB && is_object($DB)){
            die($DB->raiseErrorFn);
        }

        $DB->SetCharSet('utf8');

        $this->arrConnection[$strKey] = &$DB;
        return $this->arrConnection[$strKey];
    }


    static public function Init($fetchMode, $assocCase, $cacheDir, $cacheSeconds){
        if(is_null(self::$_me)){
            self::$_me = new ADODB_Connection_Manager($fetchMode, $assocCase, $cacheDir, $cacheSeconds);
        }
    }

    static public function SetCacheDir($strCacheDir) {
        global $ADODB_CACHE_DIR;
        self::$_me->cacheDir = $strCacheDir;

        $ADODB_CACHE_DIR = $strCacheDir;
    }
    
    static public function SetCacheSeconds($strCacheSeconds) {
        global $ADODB_ACTIVE_CACHESECS;
        self::$_me->cacheSeconds = $strCacheSeconds;
        $ADODB_ACTIVE_CACHESECS = $strCacheSeconds;
    }

    static public function AddDSN($strKey, $strDSN){
        if(is_null(self::$_me)){
            self::$_me = new ADODB_Connection_Manager();
        }

        return self::$_me->add($strKey, $strDSN);
    }

    static public function AddConnection($strKey, $strDriver, $strHost, $intPort, $strUser, $strPass, $strCatalog, $intDebug){
        $strDSN = "$strDriver://$strUser:$strPass@$strHost/$strCatalog?persist=0&port=$intPort&debug=$intDebug";
        return self::AddDSN($strKey, $strDSN);
    }

    static public function RemoveConnection($strKey){
        if(is_null(self::$_me)){
            // Object is not initialized
            return false;
        }
        
        return self::$_me->remove($strKey);
    }

    static public function &GetConnection($strKey){
        if(is_null(self::$_me)){
            // Object is not initialized
            return false;
        }

        return self::$_me->connect($strKey);
    }    
}