<?php 
if(!user::islogged()) 
    return this::showalert('danger', 'You need to be logged in to acces this page!'); 

$q = get::$g_sql->prepare('SELECT * FROM `contacts` WHERE `ForAccount` = ?;');
$q->execute(array(user::get())); 
$row = $q->fetchAll(); 

if(isset($_POST['delete_contact'])) {
    $dbID = $_POST['delete_contact']; 

    $q = get::$g_sql->prepare('DELETE FROM `contacts` WHERE `ID` = ? AND `ForAccount` = ? LIMIT 1;');
    $q->execute(array($dbID, user::get())); 

    return this::showalert('success', 'Congratulations! You deleted this contact.', 'mycontacts'); 
}

if(isset($_POST['submit_editcontact'])) {
    $name = this::protect($_POST['_name']); 
    $number = this::protect($_POST['_number']); 
    $adress = this::protect($_POST['_adress']); 

    $q = get::$g_sql->prepare('UPDATE `contacts` SET `Name` = ?, `Number` = ?, `Adress` = ? WHERE `ID` = ? AND `ForAccount` = ? LIMIT 1;');
    $q->execute(array($name, $number, $adress, $_POST['submit_editcontact'], user::get())); 

    return this::showalert('success', 'Congratulations! You have updated this contact!', 'mycontacts');
}

echo '
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<div class="modal" tabindex="-1" id="editContactModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-edit"></i> Edit this contact from your phonebook</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    
                </div>
            </form> 
        </div>
    </div>
</div>

<div class="col-lg-12">
    <div class="card">
        <div class="card-header">
            <div class="float-start">
                <h4><i class="fa fa-address-book"></i> My contacts</h4>
            </div>

            <div class="float-end">
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#contactModal"><i class="fa fa-plus"></i> ADD NEW CONTACT</button>
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
                        <th>Tools</th>
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
                        <td>

                            <form method="POST">
                                <button type="button" class="btn btn-primary editContact" contact-id="'.$contact['ID'].'"><i class="fa fa-edit"></i></button>
                                <button type="submit" class="btn btn-danger" name="delete_contact" value="'.$contact['ID'].'"><i class="fa fa-trash"></i></button>
                            </form>        
                        </td>
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
?>

<script>
$(document).ready( function () {
    $('#contactsTable').DataTable();

    $("body").on('click', '.editContact', function (e) {
        e.preventDefault(); 

        let contactID = $(this).attr('contact-id'); 

        $.ajax({
            async: true, 
            url: "<?php echo this::$_PAGE_URL ?>" + "action/show/editcontact",
            type: "post", 
            data: {
                contactID: contactID
            }, 
            success: function(data) {
                $(".modal-body").html(data); 

                var myModal = new bootstrap.Modal(document.getElementById("editContactModal"), {});
                
                myModal.show();
            }
        });
    });
});
</script>