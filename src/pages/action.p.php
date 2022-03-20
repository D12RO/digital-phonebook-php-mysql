<?php  
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    if(this::$_url[1] === 'show' && this::$_url[2] === 'editcontact') {
        if(!user::islogged()) return this::redirect(''); 
        
        $contactID = $_POST['contactID']; 

        $q = get::$g_sql->prepare('SELECT * FROM `contacts` WHERE `ID` = ? AND `ForAccount` = ? LIMIT 1;');
        $q->execute(array($contactID, user::get()));
        
        if($q->rowCount()) {
            $row = $q->fetch(PDO::FETCH_OBJ); 
            
            echo '
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i> </span>
                <input type="text" class="form-control" name="_name" placeholder="Contact Name" aria-label="Contact Name" aria-describedby="basic-addon1" value="'.$row->Name.'">
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1"><i class="fa fa-phone"></i> </span>
                <input type="text" class="form-control" name="_number" placeholder="Contact Number" aria-label="Contact Number" aria-describedby="basic-addon1" value="'.$row->Number.'">
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1"><i class="fa fa-address-card"></i> </span>
                <input type="text" class="form-control" name="_adress" placeholder="Contact Adress" aria-label="Contact Adress" aria-describedby="basic-addon1" value="'.$row->Adress.'">
            </div>

            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-outline-warning" name="submit_editcontact" value="'.$row->ID.'"><i class="fa fa-edit"></i> Edit Contact</button>
            </div>
            '; 
        }
    }

    else if(this::$_url[1] === 'show' && this::$_url[2] === 'search') {
        $email = $_POST['email']; 

        $q = get::$g_sql->prepare('SELECT `ID`, `Email` FROM `users` WHERE `Email` LIKE ?;');
        $q->execute(array('%'.$email.'%'));  
        
        if($q->rowCount()) {
            $row = $q->fetchAll(); 

            echo '
            <div class="card">
                <div class="card-header">
                    <h5><i class="fa fa-search"></i> Found phonebooks for users:</h5>
                </div> 

                <div class="card-body">
                    <table id="contactsTable" class="display">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Tools</th>
                            </tr>
                        </thead>
                        <tbody>'; 
                        foreach($row as $contact) {
                            echo '
                            <tr>
                                <td>'.$contact['Email'].'</td>
                                <td><a href="'.this::$_PAGE_URL.'phonebook/'.$contact['ID'].'"><button class="btn btn-warning"><i class="fa fa-eye"></i> VIEW CONTACTS</button></td>
                            </tr>
                            ';
                        }    
                        echo '
                        </tbody>
                    </table>
                </div>
            </div>

            <script>
            $(document).ready( function () {
                $("#contactsTable").DataTable();
            });
            </script>
            ';
            
        } else echo '<div class="alert alert-warning" role="alert">No phonebooks found by your search criteria!</div>';
    }

} else return this::redirect(''); 
?>