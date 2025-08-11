<?php
/**
 * Class implements named advisory lock strategy that can be used to coordinate multiple clients 
 * access to shared resources or guarantee single execution of specific routines.
 * The class implements lock using MariaDB connection server. Once lock is obtained by a client, it is maintained
 * until client releases it, or connection to MariaDB server is terminated.
 */
class ADODB_Lock_Manager {

    /**
     * Tries to acquire a named advisory lock through MariaDB server.
     *
     * @param string $strConn: ADODB Connection to MariaDB server to use for obtaining lock
     * @param string $strLockName: Lock name to acquire.
     * @param integer $intTimeoutSecond: Lock acquiring timeout in seconds. Wait time during lock acquisition before returning result.
     * @param integer $intRetryCount: Number of retries after timeout if lock acquisition fails.
     * @return boolean true if lock acquired, false if lock is already acquired by other client.
     */
    static public function GetLock($strConn, $strLockName, $intTimeoutSecond = 1, $intRetryCount = 0) {
        $DB = &\ADODB_Connection_Manager::GetConnection($strConn);
        $SQL = "SELECT GET_LOCK(?, ?) AS fldLock";
        
        for ($i = 0; $i <= $intRetryCount; ++$i) {
            $rslt = $DB->GetOne($SQL, [$strLockName, $intTimeoutSecond]);
            if ($rslt == 1) {
                break;
            }
        }
        
        return (bool)$rslt;
    }

    /**
     * Checks if a named advisory lock is free or already acquired by another client.
     *
     * @param string $strConn: ADODB Connection to MariaDB server to use for obtaining lock
     * @param string $strLockName: Lock name to check.
     * @return boolean true if lock is free, false if lock is already acquired by other client.
     */
    static public function IsFreeLock($strConn, $strLockName) {
        $DB = &\ADODB_Connection_Manager::GetConnection($strConn);
        $SQL = "SELECT IS_FREE_LOCK(?) AS fldCheck";
        $rslt = $DB->GetOne($SQL, [$strLockName]);
        
        return (bool)$rslt;
    }

    /**
     * Checks if a named advisory lock is already acquired by current client.
     *
     * @param string $strConn: ADODB Connection to MariaDB server to use for obtaining lock
     * @param string $strLockName: Lock name to acquire.
     * @return integer null if lock is free, -1 if lock is acquired by current client, otherwise it returns the ID of client that has the lock.
     */
    static public function WhoOwnsLock($strConn, $strLockName) {
        $DB = &\ADODB_Connection_Manager::GetConnection($strConn);
        $SQL = "SELECT IS_USED_LOCK(?) AS fldCheck, CONNECTION_ID() AS fldID";
        $arrRow = $DB->GetRow($SQL, [$strLockName]);
        
        if ($arrRow['fldCheck'] === null) {
            // Lock is free
            return null;
        } elseif ($arrRow['fldCheck'] == $arrRow['fldID']) {
            // Lock is already acquired by me
            return -1;
        } else {
            // Lock is acquired by another client
            return $arrRow['fldCheck'];
        }
    }

    /**
     * Releases a named advisory lock if alerady acquired.
     *
     * @param string $strConn: ADODB Connection to MariaDB server to use for obtaining lock
     * @param string $strLockName: Lock name to acquire.
     * @return bool null if lock is already free, false if lock is acquired by another client, true if lock successfully released.
     */
    static public function ReleaseLock($strConn, $strLockName) {
        $DB = &\ADODB_Connection_Manager::GetConnection($strConn);
        $SQL = "SELECT RELEASE_LOCK(?) AS fldCheck";
        $rslt = $DB->GetOne($SQL, [$strLockName]);
        
        if ($rslt === null) {
            // Lock is already free
            return null;
        } else {
            // $rslt = 0, return false: Lock already acquired by another client
            // $rslt = 1, return true: Lock successfully released
            return (bool)$rslt;
        }
    }

}