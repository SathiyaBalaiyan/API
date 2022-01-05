<?php

class TaskGateway
{
    private PDO $conn;    //To store database connection

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();    //Call getConnection method on the database Object
    }

//To get all records from an database.    

    public function getAllForUser(int $user_id): array 
    {
        $sql = "SELECT * FROM task WHERE user_id = :user_id ORDER BY name";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();

        //return $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $row['is_completed'] = (bool) $row['is_completed'];

            $data[] = $row;
        }

        return $data;
    }

//To show an individual record from the database.

    public function getForUser(int $user_id, string $id): array | false
    {
        $sql = "SELECT * FROM task WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data == true) {

            $data['is_completed'] = (bool) $data['is_completed'];
        }

        return $data;
    }

//To create or insert a new column in a table.     
    
    public function createForUser(int $user_id, array $data): string
    {
        $sql = "INSERT INTO task (name, priority, is_completed, user_id) VALUES (:name, :priority, :is_completed, :user_id)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);

        if (empty($data["priority"])) {

            $stmt->bindValue(":priority", null, PDO::PARAM_NULL);

        } else {

            $stmt->bindValue(":priority", $data["priority"], PDO::PARAM_INT);
        }

        $stmt->bindValue(":is_completed", $data["is_completed"] ?? false, PDO::PARAM_BOOL);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();

        //return $stmt->rowCount();

        return $this->conn->lastInsertId();             //retrieving last inserted id as an integer
    }

//To update a record in the table.

    public function updateForUser(int $user_id, string $id, array $data): int
    {
        $fields = [];

        if ( ! empty($data["name"])) {

            $fields["name"] = [
                $data["name"],
                PDO::PARAM_STR
            ];
        }

        if (array_key_exists("priority", $data)) {

            $fields["priority"] = [
                $data["priority"],
                $data["priority"] === null ? PDO::PARAM_NULL : PDO::PARAM_INT
            ];
        }

        if (array_key_exists("is_completed", $data)) {

            $fields["is_completed"] = [
                $data["is_completed"],
                PDO::PARAM_BOOL
            ];
        }
        //print_r($fields);
        //exit;
        
        if (empty($fields)) {

            return 0;

        }  else {

              $sets = array_map(function($value) {

                  return "$value = :$value";
            
            }, array_keys($fields));

            //print_r($sets);
            $sql = "UPDATE task" . " SET " . implode(", ", $sets) . " WHERE id = :id" . "AND user_id = :user_id";
        
            //echo $sql;
            //exit;

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        foreach ($fields as $name => $values) {

             $stmt->bindValue(":$name", $values[0], $values[1]);
        }

        $stmt->execute();

        return $stmt->rowCount();

    }

    }

//To delete a record in the table.

    public function deleteForUser(int $user_id, string $id): int
    {
        $sql = "DELETE FROM task WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();

    }
}