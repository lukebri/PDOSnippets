<?php

// PDO connection to MySQL
$db = new PDO('mysql:host=localhost;dbname=yourdbname', 'username', 'password', array(PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

// PDO connection to PostgreSQL
$db = new PDO('pgsql:host=localhost;dbname=yourdbname', 'username', 'password');

// select from foreach loop row[name] // Do not use for client side input, use below prep statement
foreach($db->query('SELECT * FROM table') as $row) {
    echo $row['field1'].' '.$row['field2']; //etc...
}
/////////////////////// secure prepared statement
$sql= "SELECT filmID, filmName FROM movies WHERE filmID = :filmID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':filmID', $filmID, PDO::PARAM_INT);
$stmt->execute();

//query() method returns a PDOStatement object. You can also fetch results this way:
$stmt = $db->query('SELECT * FROM table');
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['field1'].' '.$row['field2']; //etc...
}
// or create $results object from query:
$stmt = $db->query('SELECT * FROM test WHERE id < 5');
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($results as $row){
    echo "<li>{$row['id']}</li>";
}
// as an object
$sql= "SELECT * FROM movies";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$total = $stmt->rowCount(); // if you want to limit/select, knowing the max is good
while ($row = $stmt->fetchObject()) {
    echo "<li>{$row->name}</li>";
}

// _GET id/search% example

if(isset($_GET['search'])){
    $search = "%".$_GET['search']."%";
    $sql= "SELECT * FROM products WHERE name LIKE :search";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':search', $search, PDO::PARAM_STR);
    $stmt->execute();
    $total = $stmt->rowCount();
}else{
    // do else
}
// or ARRAY queryprep>execute:
 $stmt->execute(array
    ':search' = > $search
  ));

//count rows
$stmt = $db->query('SELECT * FROM table');
$row_count = $stmt->rowCount();
echo $row_count.' rows selected';

// Get last inserted ID
$insertId = $db->lastInsertId();

//show effected rows
$affected_rows = $db->exec("UPDATE table SET field='value'");
echo $affected_rows.' were affected'

// prepared statement SELECT FROM
$stmt = $db->prepare("SELECT * FROM table WHERE id=:id AND name=:name");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// execute query version ^ SELECT FROM
$stmt = $db->prepare("SELECT * FROM table WHERE id=:id AND name=:name");
$stmt->execute(array(':name' => $name,
   ':id' => $id
   ));
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// INSERT INTO /////////////////////////////////////////////////////////////////////////////////////////

$stmt = $db->prepare("INSERT INTO table(field1,field2,field3,field4,field5) VALUES(:field1,:field2,:field3,:field4,:field5)");
$stmt->execute(array(':field1' => $field1, ':field2' => $field2, ':field3' => $field3, ':field4' => $field4, ':field5' => $field5));
$affected_rows = $stmt->rowCount();

//////////////////////// trycatchblock

try {

  # Prepare the query ONCE
  $stmt = $db->prepare('INSERT INTO test (name, password) VALUES(:name, :pwd)');
  $stmt->bindParam(':name', $name);
  $stmt->bindParam(':pwd', $pwd);

  # First insertion
  $name = 'Keith';
  $pwd = "pasword";
  $stmt->execute();

} catch(PDOException $e) {
  echo $e->getMessage();
}

// DELETE FROM /////////////////////////////////////////////////////////////////////////////////////////

$stmt = $db->prepare("DELETE FROM table WHERE id=:id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$affected_rows = $stmt->rowCount();

////////////// trycatch

try {
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $stmt = $db->prepare('DELETE FROM someTable WHERE id = :id');
  $stmt->bindParam(':id', $id); // this time, we'll use the bindParam method
  $stmt->execute();

  echo $stmt->rowCount(); // echo effected row count
} catch(PDOException $e) {
  echo 'Error: ' . $e->getMessage();
}




// UPDATE //////////////////////////////////////////////////////////////////////////////////////////////

$stmt = $db->prepare("UPDATE table SET name= ? WHERE id=?");
$stmt->execute(array($name, $id));
$affected_rows = $stmt->rowCount();

///////////// trycatch

try {

  $stmt = $db->prepare('UPDATE someTable SET name = :name WHERE id = :id');
  $stmt->execute(array(
    ':id'   => $id,
    ':name' => $name
    ));

} catch(PDOException $e) {
  echo 'Error: ' . $e->getMessage();
}

}


////// COMBINE WITH FOREIGN ID (joke/author table)
/// ids have to be specified, if they are identical in each table
//  joins matching authorid table with all jokes matching that id
$sql = 'SELECT joke.id, joketext, name, email
        FROM joke
        INNER JOIN author
        ON authorid = author.id';

        // OR WHERE something = something
$sql = 'SELECT joketext
        FROM joke INNER JOIN author
        ON authorid = author.id
        WHERE name = "Joan Smith"';

////// LEFT JOIN SELECT FROM WHILE LOOP
// Eg replace countryid in user table with countries table to insert relevant data (name of country, rather than ID)
Users:     id[1] firstname [luke] country [0]
Countries: id[1] name [Australia] code    [AU]

$sql = "
SELECT users.firstname, countries.name as country
FROM users
LEFT JOIN countries ON users.country = countries.id
";
$result = $db->query($sql);
if ( $result->num_rows ) {
  while ( $row = $results->fetch_ojbect() ) {
    echo "{$row->firstname} ({$row->country})";
  } else {
    echo "No results";
  }
}

// Variable Injection
$query = $db->prepare("SELECT * FROM profile WHERE username = :username LIMIT 1");
$query->bindParam(":username", "knightofarcadia");
$query->execute();
$profile = $query>fetch( PDO::FETCH_ASSOC );
echo $profile['fullname'];

// Variable Injection with multi-row set
$query = $db->prepare("SELECT * FROM profile WHERE hometown = :hometown");
$query->bindParam(":hometown", "Wessex");
$query->execute();
foreach($query->fetch(PDO::FETCH_ASSOC) as $row) {
    echo $row["fullname"];
}

// Creation
$createsql = $db->prepare("CREATE TABLE profiles (username VARCHAR(64), fullname VARCHAR (128), hometown VARCHAR(128)"));
$db->query($createsql);

// Insertion
$query = $db->prepare($insertsql);
$query->bindParam(":username", "knightofarcadia");
$query->bindParam(":fullname", "Arthur Pendragon");
$query->bindParam(":hometown", "Wessex");
$query->execute();

// Updating
$query = $db->prepare("UPDATE profiles SET fullname = :fullname WHERE username = :username");
$query->bindParam(":fullname", "Arthur Pendragoon");
$query->bindParam(":username", "knightofarcadia");
$query->execute();

// Deletion
$query = $db->prepare("DELETE FROM profiles WHERE `username` = :username");
$query->bindParam(":username", "knightofarcadia");
$query->execute();

// MS SQL Server dbection
$db = new PDO("sqlsrv:server=localhost;database=yourdbname", "username", "password");

// IBM DB2 dbection
$db = new PDO("ibm:DRIVER={IBM DB2 ODBC DRIVER};DATABASE=yourdbname;HOSTNAME=localhost;PORT=56789;PROTOCOL=TCPIP;","username",  "password");

// Transactions
try {
    $db->beginTransaction();
    $insertsql = $db->prepare("INSERT INTO profiles (username, fullname, hometown) VALUES ('wilfred', 'Wilfred Jones', 'Scarborough')");
    $deletesql = $db->prepare("DELETE FROM profiles WHERE username = 'username'" );
    $db->exec($insertsql);
    $db->exec($deletesql);
    $db->commit();
} catch (Exception $e) {
 $db->rollBack();
 echo $ex->getMessage();
}

?>
