<?php
include 'config_oracle.php';

$query = "SELECT id, name, email, subject, DBMS_LOB.SUBSTR(message, 4000, 1) as message, status, created_at 
          FROM inquiries ORDER BY created_at DESC";
$stid = oci_parse($conn, $query);
oci_execute($stid);
while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
}
?>