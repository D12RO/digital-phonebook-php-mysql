<?php 
if(empty(this::$_url[1])) return this::redirect(''); 

$q = get::$g_sql->prepare('SELECT * FROM `contacts` WHERE `ForAccount` = ?;');
$q->execute(array(this::$_url[1]));

if(!$q->rowCount()) 
    return this::showalert('danger', "We can't find contacts in this phonebook.", ''); 
else {
    $row = $q->fetchAll(); 
    
    $q = get::$g_sql->prepare('SELECT `Email` FROM `users` WHERE `ID` = ? LIMIT 1;');
    $q->execute(array(this::$_url[1]));  
    $row2 = $q->fetch(PDO::FETCH_OBJ); 

    echo '
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="float-start">
                    <h4><i class="fa fa-address-book"></i> <strong>'.$row2->Email.'</strong> Contacts</h4>
                </div>
            </div>
            <div class="card-body">
                <table id="contactsTable" class="display">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Number</th>
                            <th>Adress</th>
                        </tr>
                    </thead>
                    <tbody>'; 
                    foreach($row as $contact) {
                        echo '
                        <tr>
                            <td>'.$contact['ID'].'</td>
                            <td>'.$contact['Name'].'</td>
                            <td>'.$contact['Number'].'</td>
                            <td>'.$contact['Adress'].'</td>
                        </tr>
                        ';
                    }    
                    echo '
                    </tbody>
                </table>
            </div>
        </div> 
    </div> 
    ';
}
?>

<script>
$(document).ready( function () {
    $('#contactsTable').DataTable();
});
</script> 