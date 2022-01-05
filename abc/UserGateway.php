<?php

class UserGateway 
{
    private PDO $conn;    //To store database connection

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();                     //Call getConnection method on the database Object
    }

    public function getByAPIKey(string $key): array | false           //pass a string argument to this for API key.
    {
        $sql = "SELECT * FROM user WHERE api_key = :api_key";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":api_key", $key, PDO::PARAM_STR);

        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUsername(string $username): array | false
    {

        $sql = "SELECT * FROM user WHERE username = :username";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":username", $username, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
}
